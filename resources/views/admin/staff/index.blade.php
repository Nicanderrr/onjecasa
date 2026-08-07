@extends('layouts.admin')

@section('title', 'Employees - NewPOS')
@section('page-eyebrow', 'Access')
@section('page-title', 'Employees')
@section('page-description', 'Manage staff records with a tighter table and quicker actions.')
@section('page-actions')
  <a href="{{ route('admin.staff.create') }}" class="btn btn-primary btn-sm">
    <i class="fas fa-user-plus"></i> Add Employee
  </a>
@endsection

@section('content')
@php
  $uniqueNumbers = $rows->pluck('number')->filter()->unique()->count();
  $emailCount = $rows->whereNotNull('email')->count();
@endphp

<div class="entity-page">
  <section class="entity-hero entity-hero-slim">
    <div class="entity-stats">
      <div class="entity-stat">
        <span>Total Employees</span>
        <strong>{{ $rows->count() }}</strong>
      </div>
      <div class="entity-stat">
        <span>Unique Numbers</span>
        <strong>{{ $uniqueNumbers }}</strong>
      </div>
      <div class="entity-stat">
        <span>Email Records</span>
        <strong>{{ $emailCount }}</strong>
      </div>
    </div>
  </section>

  <div class="card shadow entity-card">
    <div class="card-header border-0 entity-toolbar">
      <div class="entity-chip">
        <i class="fas fa-id-badge"></i>
        Staff records stay newest first
      </div>
      <input
        type="search"
        class="form-control form-control-sm entity-filter"
        placeholder="Filter employees"
        aria-label="Filter employees"
        data-table-filter="#staff-table"
      >
    </div>

    <div class="table-responsive">
      <table class="table align-items-center table-flush" id="staff-table">
        <thead class="thead-light">
          <tr>
            <th>Name</th>
            <th>Number</th>
            <th>Email</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $r)
            <tr data-filter-row>
              <td class="font-weight-bold">{{ $r->name }}</td>
              <td>{{ $r->number }}</td>
              <td>{{ $r->email }}</td>
              <td class="text-right">
                <div class="action-group justify-content-end">
                  <form method="POST" action="{{ route('admin.staff.destroy', $r->id) }}">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" title="Delete employee">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                  <a href="{{ route('admin.staff.edit', $r->id) }}" class="btn btn-sm btn-outline-primary" title="Edit employee">
                    <i class="fas fa-edit"></i>
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr data-filter-empty>
              <td colspan="4" class="text-center py-5 text-muted">No employees yet. Add the first staff account to get started.</td>
            </tr>
          @endforelse
          @if($rows->count())
            <tr data-filter-empty style="display:none;">
              <td colspan="4" class="text-center py-5 text-muted">No matching employees found.</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
