<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductImportController extends Controller
{
    public function create(): View
    {
        return view('admin.products.import');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'import_file' => ['required', 'file', 'max:10240'],
            'mode' => ['required', 'in:upsert,insert_only'],
        ]);

        $file = $request->file('import_file');
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['xlsx', 'xls', 'csv', 'txt', 'sql'], true)) {
            throw ValidationException::withMessages(['import_file' => 'Upload an Excel, CSV, or SQL product import file.']);
        }

        $rows = in_array($extension, ['sql', 'txt'], true)
            ? $this->rowsFromSqlFile($file->getRealPath())
            : $this->rowsFromSpreadsheet($file->getRealPath());

        if (count($rows) === 0) {
            throw ValidationException::withMessages(['import_file' => 'No product rows were found in the uploaded file.']);
        }

        $result = $this->importRows($rows, $data['mode']);

        AuditTrail::record('products_imported', 'Imported products from ' . $file->getClientOriginalName(), [
            'auditable_type' => 'product',
            'properties' => [
                'file' => $file->getClientOriginalName(),
                'mode' => $data['mode'],
                'created' => $result['created'],
                'updated' => $result['updated'],
                'skipped' => $result['skipped'],
            ],
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Import complete: {$result['created']} created, {$result['updated']} updated, {$result['skipped']} skipped.");
    }

    private function rowsFromSpreadsheet(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rawRows = $sheet->toArray(null, true, true, true);

        if (count($rawRows) < 2) {
            return [];
        }

        $headers = array_map(fn ($value) => $this->normalizeHeader((string) $value), array_shift($rawRows));
        $rows = [];

        foreach ($rawRows as $rawRow) {
            $row = [];
            $hasValue = false;

            foreach ($headers as $column => $header) {
                if ($header === '') {
                    continue;
                }

                $value = is_string($rawRow[$column] ?? null) ? trim($rawRow[$column]) : $rawRow[$column] ?? null;
                $row[$header] = $value;
                $hasValue = $hasValue || filled($value);
            }

            if ($hasValue) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    private function rowsFromSqlFile(string $path): array
    {
        $sql = file_get_contents($path);
        if ($sql === false) {
            return [];
        }

        preg_match_all('/insert\s+into\s+`?pos_products`?\s*\((.*?)\)\s*values\s*(.*?);/is', $sql, $matches, PREG_SET_ORDER);
        $rows = [];

        foreach ($matches as $statement) {
            $columns = array_map(
                fn ($column) => $this->normalizeHeader(trim($column, " \t\n\r\0\x0B`\"'")),
                explode(',', $statement[1])
            );

            foreach ($this->splitSqlValueTuples($statement[2]) as $tuple) {
                $values = str_getcsv(trim($tuple, '() '), ',', "'", '\\');
                $row = [];

                foreach ($columns as $index => $column) {
                    if ($column === 'id' || $column === '') {
                        continue;
                    }

                    $row[$column] = $this->cleanSqlValue($values[$index] ?? null);
                }

                if (!empty(array_filter($row, fn ($value) => filled($value)))) {
                    $rows[] = $row;
                }
            }
        }

        return $rows;
    }

    private function importRows(array $rows, string $mode): array
    {
        $result = ['created' => 0, 'updated' => 0, 'skipped' => 0];

        DB::transaction(function () use ($rows, $mode, &$result) {
            foreach ($rows as $row) {
                $product = $this->normalizeProductRow($row);

                if ($product === null) {
                    $result['skipped']++;
                    continue;
                }

                $existing = DB::table('pos_products')
                    ->where('code', $product['code'])
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    if ($mode === 'insert_only') {
                        $result['skipped']++;
                        continue;
                    }

                    DB::table('pos_products')->where('id', $existing->id)->update([
                        'name' => $product['name'],
                        'description' => $product['description'],
                        'price' => $product['price'],
                        'stock' => $product['stock'],
                        'low_stock_threshold' => $product['low_stock_threshold'],
                        'image' => $product['image'] ?: $existing->image,
                        'updated_at' => now(),
                    ]);

                    $result['updated']++;
                    continue;
                }

                DB::table('pos_products')->insert([
                    ...$product,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $result['created']++;
            }
        });

        return $result;
    }

    private function normalizeProductRow(array $row): ?array
    {
        $name = trim((string) ($row['name'] ?? $row['product_name'] ?? $row['product'] ?? ''));
        $price = $this->decimalValue($row['price'] ?? $row['unit_price'] ?? $row['selling_price'] ?? null);
        $stock = $this->integerValue($row['stock'] ?? $row['quantity'] ?? $row['qty'] ?? 0);
        $lowStockThreshold = $this->integerValue($row['low_stock_threshold'] ?? $row['alert_threshold'] ?? $row['reorder_level'] ?? 5);

        if ($name === '' || $price === null || $price < 0 || $stock === null || $stock < 0 || $lowStockThreshold === null || $lowStockThreshold < 0) {
            return null;
        }

        $code = trim((string) ($row['code'] ?? $row['sku'] ?? $row['barcode'] ?? ''));
        $image = trim((string) ($row['image'] ?? $row['image_name'] ?? ''));

        return [
            'code' => $code !== '' ? Str::limit($code, 100, '') : $this->generateProductCode(),
            'name' => Str::limit($name, 255, ''),
            'description' => (string) ($row['description'] ?? ''),
            'price' => $price,
            'stock' => $stock,
            'low_stock_threshold' => $lowStockThreshold,
            'image' => $image !== '' ? basename($image) : null,
        ];
    }

    private function normalizeHeader(string $header): string
    {
        return Str::of($header)->lower()->trim()->replace([' ', '-', '.'], '_')->toString();
    }

    private function decimalValue(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        $number = str_replace(',', '', (string) $value);
        return is_numeric($number) ? round((float) $number, 2) : null;
    }

    private function integerValue(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        $number = str_replace(',', '', (string) $value);
        return is_numeric($number) ? max(0, (int) floor((float) $number)) : null;
    }

    private function splitSqlValueTuples(string $values): array
    {
        $tuples = [];
        $buffer = '';
        $depth = 0;
        $quote = null;
        $length = strlen($values);

        for ($i = 0; $i < $length; $i++) {
            $char = $values[$i];
            $previous = $i > 0 ? $values[$i - 1] : '';

            if ($depth === 0 && $quote === null && $char !== '(') {
                continue;
            }

            if (($char === "'" || $char === '"') && $previous !== '\\') {
                $quote = $quote === $char ? null : ($quote ?? $char);
            }

            if ($quote === null) {
                if ($char === '(') {
                    $depth++;
                } elseif ($char === ')') {
                    $depth--;
                }
            }

            $buffer .= $char;

            if ($depth === 0 && trim($buffer) !== '' && $char === ')') {
                $tuples[] = trim($buffer);
                $buffer = '';
            }
        }

        return $tuples;
    }

    private function cleanSqlValue(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        if (strcasecmp($value, 'null') === 0) {
            return null;
        }

        return str_replace(["\\'", '\\"'], ["'", '"'], $value);
    }

    private function generateProductCode(): string
    {
        do {
            $code = 'PRD-' . random_int(1000, 9999);
        } while (DB::table('pos_products')->where('code', $code)->exists());

        return $code;
    }
}
