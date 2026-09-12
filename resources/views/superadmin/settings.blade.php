@extends('layouts.superadmin')

@section('title', 'Superadmin - System Settings')
@section('page-eyebrow', 'Settings')
@section('page-title', 'System Settings')
@section('page-description', 'Review global POS settings and superadmin policy values.')
@section('page-actions')
  <a href="{{ route('admin.settings.index') }}" class="btn btn-primary btn-sm"><i class="bi bi-sliders"></i> Open POS Settings</a>
@endsection

@section('content')
<div class="entity-page">
  <section class="row g-3 mb-3">
    <div class="col-12 col-xl-4">
      <div class="super-card super-card-pad h-100">
        <h2 class="h5 mb-3 section-title"><i class="bi bi-person-bounding-box"></i><span>My Profile</span></h2>
        <div class="d-grid gap-3">
          <div class="d-flex align-items-center gap-3">
            <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}" class="rounded-circle border" style="width:72px;height:72px;object-fit:cover;background:#fff;">
            <div>
              <strong class="d-block">{{ auth()->user()->name }}</strong>
              <small class="text-muted">{{ auth()->user()->email }}</small>
            </div>
          </div>
          <form method="POST" action="{{ route('superadmin.profile.update') }}" enctype="multipart/form-data" class="d-grid gap-3">
            @csrf
            @method('PUT')
            <div>
              <label class="form-label">Profile Photo</label>
              <input type="file" name="avatar" class="form-control" accept=".jpg,.jpeg,.png,.webp">
              <div class="form-text">Square images look best. Max 2MB.</div>
            </div>
            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
              <div class="small text-muted">Used in the superadmin sidebar and profile area.</div>
              <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-check2-circle"></i> Save Photo</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="col-12 col-xl-8">
      <div class="super-card super-card-pad h-100">
        <h2 class="h5 mb-3 section-title"><i class="bi bi-shield-check"></i><span>Superadmin Policy</span></h2>
        <div class="d-grid gap-2">
          <div class="border rounded p-3"><small class="text-muted d-block">AI Assistant</small><strong>{{ ($settings['ai_enabled'] ?? true) ? 'Enabled' : 'Disabled' }}</strong></div>
          <div class="border rounded p-3"><small class="text-muted d-block">Maintenance Flag</small><strong>{{ ($settings['maintenance_enabled'] ?? false) ? 'On' : 'Off' }}</strong></div>
          <a class="btn btn-outline-secondary" href="{{ route('superadmin.security') }}">Manage Policy</a>
        </div>
      </div>
    </div>
  </section>
  <section class="row g-3">
    <div class="col-12">
      <div class="super-card h-100">
        <div class="card-header border-0 entity-toolbar"><div class="cashier-table-heading"><span><i class="bi bi-database"></i></span><div><strong>POS Settings</strong><small>Stored global settings</small></div></div></div>
        <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Key</th><th>Value</th></tr></thead><tbody>
          @forelse($posSettings as $key => $value)
            <tr><td><strong>{{ $key }}</strong></td><td>{{ \Illuminate\Support\Str::limit((string) $value, 120) }}</td></tr>
          @empty
            <tr><td colspan="2" class="text-center py-5 text-muted">No POS settings found.</td></tr>
          @endforelse
        </tbody></table></div>
      </div>
    </div>
  </section>
</div>
@endsection
