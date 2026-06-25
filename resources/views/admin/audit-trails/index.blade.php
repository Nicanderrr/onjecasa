@extends('layouts.admin')

@section('content')
<div class="card shadow">
  <div class="card-header border-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between">
      <div>
        <h3 class="mb-0">Audit Trail</h3>
        <p class="text-muted mb-0">Recent user and system actions across the POS.</p>
      </div>
      <form class="form-inline mt-3 mt-md-0" method="GET" action="{{ route('admin.audit-trails.index') }}">
        <select class="form-control mr-2 mb-2 mb-md-0" name="event">
          <option value="">All Events</option>
          @foreach($events as $event)
            <option value="{{ $event }}" @selected(($filters['event'] ?? '') === $event)>{{ ucwords(str_replace('_', ' ', $event)) }}</option>
          @endforeach
        </select>
        <input class="form-control mr-2 mb-2 mb-md-0" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search audit trail">
        <button class="btn btn-primary mb-2 mb-md-0" type="submit"><i class="fas fa-search"></i> Filter</button>
      </form>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table align-items-center table-flush">
      <thead class="thead-light">
        <tr>
          <th>Time</th>
          <th>User</th>
          <th>Event</th>
          <th>Description</th>
          <th>Target</th>
          <th>IP</th>
          <th>Details</th>
        </tr>
      </thead>
      <tbody>
        @forelse($audits as $audit)
          <tr>
            <td>{{ \Illuminate\Support\Carbon::parse($audit->created_at)->format('M d, Y H:i') }}</td>
            <td>
              <strong>{{ $audit->user_name ?: 'System' }}</strong>
              @if($audit->user_role)<br><small class="text-muted">{{ ucfirst($audit->user_role) }}</small>@endif
            </td>
            <td><span class="badge badge-info">{{ ucwords(str_replace('_', ' ', $audit->event)) }}</span></td>
            <td>{{ $audit->description }}</td>
            <td>
              @if($audit->auditable_type)
                {{ $audit->auditable_type }} #{{ $audit->auditable_id }}
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
            <td>{{ $audit->ip_address ?: '-' }}</td>
            <td style="max-width: 280px;">
              @if($audit->properties)
                <details>
                  <summary class="text-primary" style="cursor:pointer;">View</summary>
                  <pre class="mt-2 mb-0 small" style="white-space:pre-wrap;">{{ json_encode(json_decode($audit->properties, true), JSON_PRETTY_PRINT) }}</pre>
                </details>
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center text-muted py-5">No audit entries found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="card-footer py-4">
    {{ $audits->links() }}
  </div>
</div>
@endsection
