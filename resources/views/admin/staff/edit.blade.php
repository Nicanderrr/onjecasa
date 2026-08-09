@extends('layouts.admin')

@section('title', 'Edit Cashier - NewPOS')
@section('page-eyebrow', 'Access')
@section('page-title', 'Edit Cashier')
@section('page-description', 'Update login access, contact details, or issue a new password.')
@section('page-actions')
  <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left"></i> Back to Cashiers
  </a>
@endsection

@section('content')
<div class="card shadow entity-card cashier-form-card">
  <div class="card-header border-0">
    <div class="cashier-form-heading">
      <span><i class="bi bi-person-gear"></i></span>
      <div>
        <h3 class="mb-1">{{ $row->name }}</h3>
        <p class="mb-0">{{ $row->user_id ? 'Linked cashier login account' : 'Legacy record: set a password to create login access' }}</p>
      </div>
    </div>
  </div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.staff.update', $row->id) }}" class="compact-form">
      @csrf
      @method('PUT')
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Full Name</label>
          <input name="name" value="{{ old('name', $row->name) }}" class="form-control" required>
          @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Phone Number</label>
          <input name="number" value="{{ old('number', $row->number) }}" class="form-control" required>
          @error('number')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
          <label class="form-label">Login Email</label>
          <input type="email" name="email" value="{{ old('email', $row->email) }}" class="form-control" required autocomplete="username">
          @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">{{ $row->user_id ? 'New Password (optional)' : 'Password' }}</label>
          <input type="password" name="password" class="form-control" {{ $row->user_id ? '' : 'required' }} minlength="6" autocomplete="new-password">
          <div class="form-hint">{{ $row->user_id ? 'Leave blank to keep the current password.' : 'Required to convert this record into a cashier login.' }}</div>
          @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Confirm Password</label>
          <input type="password" name="password_confirmation" class="form-control" {{ $row->user_id ? '' : 'required' }} minlength="6" autocomplete="new-password">
        </div>
        <div class="col-12">
          <input type="hidden" name="is_active" value="0">
          <label class="cashier-active-toggle">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', (string) ($row->is_active ?? 1)) === '1' ? 'checked' : '' }}>
            <span><strong>Active account</strong><small>Inactive cashiers cannot log in, but their sales history remains available.</small></span>
          </label>
        </div>
      </div>
      <div class="d-flex flex-wrap gap-2 mt-2">
        <button class="btn btn-primary"><i class="bi bi-check2-circle"></i> Update Cashier</button>
        <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
