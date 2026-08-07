@extends('layouts.admin')

@section('title', 'Add Employee - NewPOS')
@section('page-eyebrow', 'Access')
@section('page-title', 'Add Employee')
@section('page-description', 'Create a staff record with contact details and login pincode.')
@section('page-actions')
  <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary btn-sm">
    <i class="fas fa-arrow-left"></i> Back to Employees
  </a>
@endsection

@section('content')
<div class="entity-page">
  <section class="entity-hero">
    <div class="entity-hero-copy">
      <p class="eyebrow">New employee</p>
      <h1>Add Employee</h1>
      <p>Keep the record small, clear, and ready for access control.</p>
    </div>
    <div class="entity-chip">
      <i class="fas fa-user-shield"></i>
      Pincode is required for new staff
    </div>
  </section>

  <div class="card shadow entity-card">
    <div class="card-header border-0">
      <h3 class="mb-0">Employee details</h3>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.staff.store') }}" class="compact-form">
        @csrf
        <div class="form-row">
          <div class="col-md-6 form-group">
            <label>Name</label>
            <input name="name" class="form-control" required>
          </div>
          <div class="col-md-6 form-group">
            <label>Number</label>
            <input name="number" class="form-control" required>
          </div>
        </div>
        <div class="form-row">
          <div class="col-md-6 form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="col-md-6 form-group">
            <label>Pincode</label>
            <input name="pincode" class="form-control" required>
          </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
          <button class="btn btn-success">Save</button>
          <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
