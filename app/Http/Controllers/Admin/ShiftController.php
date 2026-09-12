<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ShiftController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', 'all');
        $cashierId = $request->query('cashier_id');

        $baseQuery = DB::table('pos_cashier_shifts as shifts')
            ->join('users as cashier', 'cashier.id', '=', 'shifts.cashier_user_id')
            ->when($status === 'active', fn ($query) => $query->whereNull('shifts.ended_at'))
            ->when($status === 'closed', fn ($query) => $query->whereNotNull('shifts.ended_at'))
            ->when($cashierId, fn ($query) => $query->where('shifts.cashier_user_id', $cashierId));

        $summary = [
            'total' => DB::table('pos_cashier_shifts')->count(),
            'active' => DB::table('pos_cashier_shifts')->whereNull('ended_at')->count(),
            'closed' => DB::table('pos_cashier_shifts')->whereNotNull('ended_at')->count(),
        ];

        $shifts = $baseQuery
            ->select('shifts.*', 'cashier.name as cashier_name', 'cashier.email as cashier_email')
            ->orderByDesc('shifts.started_at')
            ->paginate(20)
            ->through(function ($shift) {
                $orders = DB::table('pos_orders')
                    ->where('cashier_user_id', $shift->cashier_user_id)
                    ->where('created_at', '>=', $shift->started_at)
                    ->when($shift->ended_at, fn ($query) => $query->where('created_at', '<=', $shift->ended_at));

                $shift->order_count = (clone $orders)->count();
                $shift->sales_total = (float) (clone $orders)->sum('grand_total');

                return $shift;
            })
            ->withQueryString();

        $cashiers = DB::table('users')
            ->where('role', 'cashier')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $filters = [
            'status' => $status,
            'cashier_id' => $cashierId,
        ];

        return view('admin.shifts.index', compact('shifts', 'cashiers', 'filters', 'summary'));
    }
}
