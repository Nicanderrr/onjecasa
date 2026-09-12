@extends('layouts.superadmin')

@section('title', 'Superadmin - Security Center')
@section('page-eyebrow', 'Security')
@section('page-title', 'Security Center')
@section('page-description', 'Manage high-level policy switches and review privileged sessions.')

@section('content')
<div class="entity-page">
  <section class="row g-3">
    <div class="col-12 col-xl-4">
      <div class="super-card super-card-pad h-100">
        <h2 class="h5 mb-3 section-title"><i class="bi bi-shield-lock"></i><span>Policy Switches</span></h2>
        <form method="POST" action="{{ route('superadmin.security.update') }}">
          @csrf
          @method('PUT')
          <label class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" name="ai_enabled" value="1" @checked($settings['ai_enabled'] ?? true)>
            <span class="form-check-label">Enable POS AI Assistant</span>
          </label>
          <label class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" name="maintenance_enabled" value="1" @checked($settings['maintenance_enabled'] ?? false)>
            <span class="form-check-label">Maintenance Flag</span>
          </label>
          <div class="mb-3">
            <label class="form-label">Maintenance Note</label>
            <textarea name="maintenance_note" rows="4" class="form-control" placeholder="Optional internal note">{{ $settings['maintenance_note'] ?? '' }}</textarea>
          </div>
          <button class="btn btn-primary w-100"><i class="bi bi-save"></i> Save Security Settings</button>
        </form>
      </div>
    </div>

    <div class="col-12 col-xl-8">
      <div class="super-card h-100">
        <div class="card-header border-0 entity-toolbar"><div class="cashier-table-heading"><span><i class="bi bi-person-lock"></i></span><div><strong>Privileged Accounts</strong><small>Admins and superadmins</small></div></div></div>
        <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>User</th><th>Role</th><th>Status</th><th>Created</th></tr></thead><tbody>
          @forelse($adminUsers as $user)
            <tr><td><strong>{{ $user->name }}</strong><small class="d-block text-muted">{{ $user->email }}</small></td><td>{{ ucfirst($user->role) }}</td><td><span class="badge {{ $user->is_active ? 'text-bg-success' : 'text-bg-danger' }}">{{ $user->is_active ? 'Active' : 'Disabled' }}</span></td><td>{{ $user->created_at?->format('d M Y') }}</td></tr>
          @empty
            <tr><td colspan="4" class="text-center py-5 text-muted">No privileged accounts found.</td></tr>
          @endforelse
        </tbody></table></div>
      </div>
    </div>
  </section>

  <section class="super-card mt-3">
    <div class="card-header border-0 entity-toolbar"><div class="cashier-table-heading"><span><i class="bi bi-hdd-network"></i></span><div><strong>Active Sessions</strong><small>Latest authenticated browser sessions</small></div></div></div>
    <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>User ID</th><th>IP Address</th><th>Browser</th><th>Last Activity</th></tr></thead><tbody>
      @forelse($sessions as $session)
        <tr><td>#{{ $session->user_id }}</td><td>{{ $session->ip_address ?: 'local' }}</td><td>{{ \Illuminate\Support\Str::limit($session->user_agent ?: 'Unknown', 80) }}</td><td>{{ \Illuminate\Support\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}</td></tr>
      @empty
        <tr><td colspan="4" class="text-center py-5 text-muted">No active sessions found.</td></tr>
      @endforelse
    </tbody></table></div>
  </section>
</div>
@endsection
