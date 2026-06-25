@extends('layouts.admin')
@section('content')
<div class="card shadow"><div class="card-header border-0"><h3>Add Staff</h3></div><div class="card-body"><form method="POST" action="{{ route('admin.staff.store') }}">@csrf<div class="form-row"><div class="col-md-6"><label>Name</label><input name="name" class="form-control" required></div><div class="col-md-6"><label>Number</label><input name="number" class="form-control" required></div></div><hr><div class="form-row"><div class="col-md-6"><label>Email</label><input type="email" name="email" class="form-control" required></div><div class="col-md-6"><label>Pincode</label><input name="pincode" class="form-control" required></div></div><br><button class="btn btn-success">Save</button></form></div></div>
@endsection
