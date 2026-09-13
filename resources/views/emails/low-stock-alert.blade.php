Low stock alert

Product: {{ $product->name }}
Code: {{ $product->code }}
Current stock: {{ (int) $product->stock }}
Alert threshold: {{ (int) ($product->low_stock_threshold ?? 5) }}

Please restock this product soon.
