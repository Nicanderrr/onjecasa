<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StaffCrudController extends Controller
{
    public function index(): View
    {
        $performance = DB::table('pos_orders')
            ->selectRaw('cashier_user_id, COUNT(*) as order_count, SUM(grand_total) as sales_total, MAX(created_at) as last_sale_at')
            ->groupBy('cashier_user_id');

        $rows = DB::table('pos_staff as staff')
            ->leftJoin('users as account', 'account.id', '=', 'staff.user_id')
            ->leftJoinSub($performance, 'performance', fn ($join) => $join->on('performance.cashier_user_id', '=', 'account.id'))
            ->select([
                'staff.*',
                'account.role as account_role',
                'account.is_active',
                'performance.order_count',
                'performance.sales_total',
                'performance.last_sale_at',
            ])
            ->orderByDesc('staff.id')
            ->get();

        return view('admin.staff.index', compact('rows'));
    }

    public function create(): View
    {
        return view('admin.staff.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:100', 'unique:pos_staff,number'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email', 'unique:pos_staff,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        [$staffId, $userId] = DB::transaction(function () use ($data, $request) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'cashier',
                'is_active' => $request->boolean('is_active'),
            ]);

            $staffId = DB::table('pos_staff')->insertGetId([
                'user_id' => $user->id,
                'name' => $data['name'],
                'number' => $data['number'],
                'email' => $data['email'],
                'pincode' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return [$staffId, $user->id];
        });

        AuditTrail::record('cashier_created', 'Created cashier account ' . $data['name'], [
            'auditable_type' => 'user',
            'auditable_id' => $userId,
            'properties' => [
                'staff_id' => $staffId,
                'name' => $data['name'],
                'email' => $data['email'],
                'number' => $data['number'],
                'is_active' => $request->boolean('is_active'),
            ],
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Cashier account created');
    }

    public function edit(int $id): View
    {
        $row = DB::table('pos_staff as staff')
            ->leftJoin('users as account', 'account.id', '=', 'staff.user_id')
            ->select('staff.*', 'account.is_active')
            ->where('staff.id', $id)
            ->first();

        abort_unless($row, 404);

        return view('admin.staff.edit', compact('row'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $staff = DB::table('pos_staff')->where('id', $id)->first();
        abort_unless($staff, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:100', Rule::unique('pos_staff', 'number')->ignore($id)],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('pos_staff', 'email')->ignore($id),
                Rule::unique('users', 'email')->ignore($staff->user_id),
            ],
            'password' => [Rule::requiredIf(empty($staff->user_id)), 'nullable', 'string', 'min:6', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $userId = DB::transaction(function () use ($staff, $data, $request, $id) {
            $user = $staff->user_id ? User::find($staff->user_id) : null;

            if (! $user) {
                $user = new User();
                $user->role = 'cashier';
            }

            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->is_active = $request->boolean('is_active');
            if (! empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            $user->save();

            DB::table('pos_staff')->where('id', $id)->update([
                'user_id' => $user->id,
                'name' => $data['name'],
                'number' => $data['number'],
                'email' => $data['email'],
                'updated_at' => now(),
            ]);

            return $user->id;
        });

        AuditTrail::record('cashier_updated', 'Updated cashier account ' . $data['name'], [
            'auditable_type' => 'user',
            'auditable_id' => $userId,
            'properties' => [
                'staff_id' => $id,
                'name' => $data['name'],
                'email' => $data['email'],
                'number' => $data['number'],
                'password_changed' => ! empty($data['password']),
                'is_active' => $request->boolean('is_active'),
            ],
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Cashier account updated');
    }

    public function destroy(int $id): RedirectResponse
    {
        $staff = DB::table('pos_staff')->where('id', $id)->first();
        abort_unless($staff, 404);

        if ($staff->user_id) {
            User::whereKey($staff->user_id)->update(['is_active' => false]);
            $message = 'Cashier account deactivated';
        } else {
            DB::table('pos_staff')->where('id', $id)->delete();
            $message = 'Legacy staff record deleted';
        }

        AuditTrail::record('cashier_deactivated', 'Deactivated cashier ' . $staff->name, [
            'auditable_type' => 'user',
            'auditable_id' => $staff->user_id,
            'properties' => ['staff_id' => $id],
        ]);

        return redirect()->route('admin.staff.index')->with('success', $message);
    }
}
