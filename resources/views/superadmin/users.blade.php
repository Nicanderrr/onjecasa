@extends('layouts.superadmin')

@section('title', 'Superadmin - Users & Roles')
@section('page-eyebrow', 'Access Control')
@section('page-title', 'Users & Roles')
@section('page-description', 'Create accounts, assign cashier/admin/superadmin roles, reset passwords, and deactivate users.')

@section('content')
<div class="entity-page">
  <section class="row g-2 dashboard-metrics entity-metrics">
    @foreach(['total' => 'Users', 'superadmins' => 'Superadmins', 'admins' => 'Admins', 'cashiers' => 'Cashiers', 'active' => 'Active', 'bypass' => 'Bypass'] as $key => $label)
      <div class="col-6 col-xl"><article class="metric-card {{ $key === 'superadmins' ? 'metric-warning' : 'metric-primary' }}"><div class="metric-top"><span class="metric-label">{{ $label }}</span><span class="metric-icon"><i class="bi bi-people"></i></span></div><div class="metric-value">{{ number_format($metrics[$key]) }}</div><div class="metric-meta"><span>Account scope</span></div></article></div>
    @endforeach
  </section>

  <section class="row g-3 mt-1">
    <div class="col-12 col-xl-4">
      <div class="super-card h-100">
        <div class="card-header border-0 entity-toolbar"><div class="cashier-table-heading"><span><i class="bi bi-person-plus"></i></span><div><strong>Add user</strong><small>Create direct access credentials</small></div></div></div>
        <form method="POST" action="{{ route('superadmin.users.store') }}" class="card-body">
          @csrf
          <div class="mb-3"><label class="form-label">Name</label><input name="name" value="{{ old('name') }}" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email') }}" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">Role</label><select name="role" class="form-control" required>@foreach(['cashier' => 'Cashier', 'admin' => 'Admin', 'superadmin' => 'Superadmin'] as $value => $label)<option value="{{ $value }}" @selected(old('role', 'cashier') === $value)>{{ $label }}</option>@endforeach</select></div>
          <div class="mb-3"><label class="form-label">Password</label><input type="text" name="password" class="form-control" minlength="6" placeholder="Temporary password"></div>
          <label class="form-check mb-2"><input class="form-check-input" type="checkbox" name="login_bypass_enabled" value="1" @checked(old('login_bypass_enabled'))> <span class="form-check-label">Bypass login checks</span></label>
          <label class="form-check mb-4"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked> <span class="form-check-label">Active</span></label>
          <button class="btn btn-primary w-100"><i class="bi bi-save"></i> Create User</button>
        </form>
      </div>
    </div>
    <div class="col-12 col-xl-8">
      <div class="super-card">
        <div class="card-header border-0 entity-toolbar">
          <div class="cashier-table-heading"><span><i class="bi bi-person-lines-fill"></i></span><div><strong>User directory</strong><small>Role and password management</small></div></div>
          <form method="GET" action="{{ route('superadmin.users') }}" class="entity-filter-wrap"><i class="bi bi-search"></i><input type="search" name="q" value="{{ $search }}" class="form-control form-control-sm entity-filter" placeholder="Search users"></form>
        </div>
        <div class="table-responsive"><table class="table align-items-center table-flush"><thead class="thead-light"><tr><th>User</th><th>Role</th><th>Status</th><th>Password</th><th>Actions</th></tr></thead><tbody>
          @forelse($users as $row)
            <tr>
              <td><div class="cashier-identity"><span>{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($row->name, 0, 1)) }}</span><div><strong>{{ $row->name }}</strong><small>{{ $row->email }}</small></div></div></td>
              <form method="POST" action="{{ route('superadmin.users.update', $row) }}">
                @csrf @method('PATCH')
                <td><input type="hidden" name="name" value="{{ $row->name }}"><input type="hidden" name="email" value="{{ $row->email }}"><select name="role" class="form-control form-control-sm">@foreach(['cashier' => 'Cashier', 'admin' => 'Admin', 'superadmin' => 'Superadmin'] as $value => $label)<option value="{{ $value }}" @selected($row->role === $value)>{{ $label }}</option>@endforeach</select></td>
                <td>
                  <label class="form-check mb-1"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked($row->is_active)> <span class="form-check-label">Active</span></label>
                  <label class="form-check"><input class="form-check-input" type="checkbox" name="login_bypass_enabled" value="1" @checked($row->login_bypass_enabled)> <span class="form-check-label">Bypass login</span></label>
                </td>
                <td><input type="text" name="password" class="form-control form-control-sm" placeholder="New password"></td>
                <td><div class="super-actions"><button class="btn btn-sm btn-outline-primary" title="Save"><i class="bi bi-check2"></i></button>
              </form>
              @if($row->id !== auth()->id())
                <form method="POST" action="{{ route('superadmin.users.destroy', $row) }}" onsubmit="return confirm('Delete this user account?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button></form>
              @endif
                </div></td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center py-5 text-muted">No users found.</td></tr>
          @endforelse
        </tbody></table></div>
        @if($users->hasPages())<div class="card-footer border-0">{{ $users->links() }}</div>@endif
      </div>
    </div>
  </section>
</div>
@endsection
