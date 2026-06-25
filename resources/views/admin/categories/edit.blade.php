@extends('layouts.admin')
@section('content')
<div class="card shadow"><div class="card-header border-0"><h3>Edit Category</h3></div><div class="card-body"><form method="POST" action="{{ route('admin.categories.update',$row->id) }}">@csrf @method('PUT')<div class="form-row"><div class="col-md-6"><label>Name</label><input name="name" value="{{ $row->name }}" class="form-control" required></div><div class="col-md-6"><label>Code</label><input name="code" value="{{ $row->code }}" class="form-control" required></div></div><br><button class="btn btn-success">Update</button></form></div></div>
@endsection
