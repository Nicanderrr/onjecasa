@extends('layouts.admin')
@section('content')
<div class="card shadow"><div class="card-header border-0"><h3>Add Category</h3></div><div class="card-body"><form method="POST" action="{{ route('admin.categories.store') }}">@csrf<div class="form-row"><div class="col-md-6"><label>Name</label><input name="name" class="form-control" required></div><div class="col-md-6"><label>Code</label><input name="code" class="form-control"></div></div><br><button class="btn btn-success">Save</button></form></div></div>
@endsection
