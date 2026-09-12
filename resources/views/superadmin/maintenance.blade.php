@extends('layouts.superadmin')

@section('title', 'Superadmin - Maintenance')
@section('page-eyebrow', 'Maintenance')
@section('page-title', 'Maintenance')
@section('page-description', 'Clear caches and control the internal maintenance flag.')

@section('content')
<div class="entity-page">
  <section class="row g-3">
    <div class="col-12 col-xl-6">
      <div class="super-card super-card-pad h-100">
        <h2 class="h5 mb-3 section-title"><i class="bi bi-arrow-repeat"></i><span>Application Cache</span></h2>
        <p class="text-muted">Clear route, config, event, and view caches after deployments or environment changes.</p>
        <form method="POST" action="{{ route('superadmin.maintenance.clear-cache') }}">
          @csrf
          <button class="btn btn-primary"><i class="bi bi-lightning-charge"></i> Clear Application Cache</button>
        </form>
      </div>
    </div>
    <div class="col-12 col-xl-6">
      <div class="super-card super-card-pad h-100">
        <h2 class="h5 mb-3 section-title"><i class="bi bi-cone-striped"></i><span>Maintenance Flag</span></h2>
        <p class="text-muted">This stores an internal flag that the portal can display. It does not run Laravel down mode.</p>
        <form method="POST" action="{{ route('superadmin.maintenance.toggle') }}">
          @csrf
          <label class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" name="maintenance_enabled" value="1" @checked($settings['maintenance_enabled'] ?? false)>
            <span class="form-check-label">Maintenance enabled</span>
          </label>
          <button class="btn btn-outline-primary"><i class="bi bi-save"></i> Save Flag</button>
        </form>
      </div>
    </div>
  </section>
</div>
@endsection
