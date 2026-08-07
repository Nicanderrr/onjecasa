@extends('layouts.admin')

@section('title', 'Edit Employee - NewPOS')
@section('page-eyebrow', 'Access')
@section('page-title', 'Edit Employee')
@section('page-description', 'Update contact details or issue a new pincode when needed.')
@section('page-actions')
  <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary btn-sm">
    <i class="fas fa-arrow-left"></i> Back to Employees
  </a>
@endsection

@section('content')
<div class="entity-page">
  <section class="entity-hero">
    <div class="entity-hero-copy">
      <p class="eyebrow">Update employee</p>
      <h1>Edit Employee</h1>
      <p>Make a quick change without reopening the entire staff workflow.</p>
    </div>
    <div class="entity-chip">
      <i class="fas fa-user-shield"></i>
      {{ $row->number }}
    </div>
  </section>

  <div class="card shadow entity-card">
    <div class="card-header border-0">
      <h3 class="mb-0">Employee details</h3>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.staff.update', $row->id) }}" class="compact-form">
        @csrf
        @method('PUT')
        <div class="form-row">
          <div class="col-md-6 form-group">
            <label>Name</label>
            <input name="name" value="{{ $row->name }}" class="form-control" required>
          </div>
          <div class="col-md-6 form-group">
            <label>Number</label>
            <input name="number" value="{{ $row->number }}" class="form-control" required>
          </div>
        </div>
        <div class="form-row">
          <div class="col-md-6 form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ $row->email }}" class="form-control" required>
          </div>
          <div class="col-md-6 form-group">
            <label>New Pincode (optional)</label>
            <input name="pincode" class="form-control">
            <div class="form-hint">Leave blank to keep the current pincode.</div>
          </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
          <button class="btn btn-success">Update</button>
          <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
