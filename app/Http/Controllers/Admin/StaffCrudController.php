<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StaffCrudController extends Controller
{
    public function index(): View { $rows = DB::table('pos_staff')->orderByDesc('id')->get(); return view('admin.staff.index', compact('rows')); }
    public function create(): View { return view('admin.staff.create'); }
    public function store(Request $request): RedirectResponse {
        $d = $request->validate(['name'=>'required|string|max:255','number'=>'required|string|max:100','email'=>'required|email|max:255','pincode'=>'required|string|min:4|max:10']);
        $staffId = DB::table('pos_staff')->insertGetId(['name'=>$d['name'],'number'=>$d['number'],'email'=>$d['email'],'pincode'=>Hash::make($d['pincode']),'created_at'=>now(),'updated_at'=>now()]);
        AuditTrail::record('staff_created', 'Created staff member '.$d['name'], ['auditable_type'=>'staff','auditable_id'=>$staffId,'properties'=>['staff'=>$d]]);
        return redirect()->route('admin.staff.index')->with('success','Staff Added');
    }
    public function edit(int $id): View { $row = DB::table('pos_staff')->where('id',$id)->first(); abort_unless($row,404); return view('admin.staff.edit', compact('row')); }
    public function update(Request $request, int $id): RedirectResponse {
        $d = $request->validate(['name'=>'required|string|max:255','number'=>'required|string|max:100','email'=>'required|email|max:255','pincode'=>'nullable|string|min:4|max:10']);
        $before = DB::table('pos_staff')->where('id',$id)->first();
        $u = ['name'=>$d['name'],'number'=>$d['number'],'email'=>$d['email'],'updated_at'=>now()];
        if (!empty($d['pincode'])) { $u['pincode'] = Hash::make($d['pincode']); }
        DB::table('pos_staff')->where('id',$id)->update($u);
        AuditTrail::record('staff_updated', 'Updated staff member '.$d['name'], ['auditable_type'=>'staff','auditable_id'=>$id,'properties'=>['before'=>$before ? (array) $before : null,'after'=>$d]]);
        return redirect()->route('admin.staff.index')->with('success','Staff Updated');
    }
    public function destroy(int $id): RedirectResponse { $before = DB::table('pos_staff')->where('id',$id)->first(); DB::table('pos_staff')->where('id',$id)->delete(); AuditTrail::record('staff_deleted', 'Deleted staff member '.($before->name ?? '#'.$id), ['auditable_type'=>'staff','auditable_id'=>$id,'properties'=>['staff'=>$before ? (array) $before : null]]); return redirect()->route('admin.staff.index')->with('success','Deleted'); }
}
