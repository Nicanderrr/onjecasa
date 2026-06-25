@extends('layouts.admin')
@section('content')
<div class="card shadow">
  <div class="card-header border-0"><h3>Settings</h3></div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="form-row">
        <div class="col-md-6">
          <label>Name</label>
          <input name="name" value="{{ auth()->user()->name }}" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label>Email</label>
          <input type="email" name="email" value="{{ auth()->user()->email }}" class="form-control" required>
        </div>
      </div>
      <hr>
      <div class="form-row">
        <div class="col-md-6">
          <label>New Password</label>
          <input type="password" name="password" class="form-control">
        </div>
        <div class="col-md-6">
          <label>New Pincode</label>
          <input type="password" name="pincode" class="form-control">
        </div>
      </div>
      <hr>
      <div class="form-row">
        <div class="col-md-8">
          <label>Dashboard Hero Background Image</label>
          <input type="file" name="hero_image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
          <small class="text-muted">Recommended wide image. Max 4MB.</small>
        </div>
        <div class="col-md-4">
          @if(!empty($heroImage))
            <label>Current Hero Image</label>
            <div><img src="{{ asset('assets/admin/img/settings/'.$heroImage) }}" style="width:100%; max-height:110px; object-fit:cover; border-radius:10px; border:1px solid #d9e8df;"></div>
          @endif
        </div>
      </div>
      <div class="form-row mt-3">
        <div class="col-md-8">
          <label>Hero Dark Overlay (40 - 85)</label>
          <input type="range" min="40" max="85" value="{{ $heroOverlay ?? 72 }}" class="custom-range" id="hero_overlay_range">
          <input type="number" min="40" max="85" name="hero_overlay" id="hero_overlay_input" value="{{ $heroOverlay ?? 72 }}" class="form-control" style="max-width:120px;">
          <small class="text-muted">Higher value = darker overlay.</small>
        </div>
      </div>
      <div class="form-row mt-3">
        <div class="col-md-8">
          <label>Dark Mode</label>
          <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="dark_mode" name="dark_mode" value="1" {{ !empty($darkMode) ? 'checked' : '' }}>
            <label class="custom-control-label" for="dark_mode">Enable admin dark theme</label>
          </div>
        </div>
      </div>
      <hr>
      <div class="form-row">
        <div class="col-md-3">
          <label>Theme Preset</label>
          <select name="theme_preset" class="form-control">
            <option value="emerald" {{ ($themePreset ?? '') === 'emerald' ? 'selected' : '' }}>Emerald</option>
            <option value="amber" {{ ($themePreset ?? '') === 'amber' ? 'selected' : '' }}>Amber</option>
            <option value="rose" {{ ($themePreset ?? '') === 'rose' ? 'selected' : '' }}>Rose</option>
            <option value="ocean" {{ ($themePreset ?? '') === 'ocean' ? 'selected' : '' }}>Ocean</option>
            <option value="slate" {{ ($themePreset ?? '') === 'slate' ? 'selected' : '' }}>Slate</option>
          </select>
        </div>
        <div class="col-md-3">
          <label>Font Family</label>
          <select name="font_family" class="form-control">
            <option value="open_sans" {{ ($fontFamily ?? '') === 'open_sans' ? 'selected' : '' }}>Open Sans</option>
            <option value="poppins" {{ ($fontFamily ?? '') === 'poppins' ? 'selected' : '' }}>Poppins</option>
            <option value="source_sans" {{ ($fontFamily ?? '') === 'source_sans' ? 'selected' : '' }}>Source Sans</option>
            <option value="nunito" {{ ($fontFamily ?? '') === 'nunito' ? 'selected' : '' }}>Nunito</option>
            <option value="system" {{ ($fontFamily ?? '') === 'system' ? 'selected' : '' }}>System</option>
          </select>
        </div>
        <div class="col-md-3">
          <label>Base Font Size</label>
          <input type="number" min="13" max="19" name="font_size" class="form-control" value="{{ $fontSize ?? 15 }}">
        </div>
        <div class="col-md-3">
          <label>Sidebar Color</label>
          <select name="sidebar_color" class="form-control">
            <option value="default" {{ ($sidebarColor ?? '') === 'default' ? 'selected' : '' }}>Default</option>
            <option value="midnight" {{ ($sidebarColor ?? '') === 'midnight' ? 'selected' : '' }}>Midnight</option>
            <option value="forest" {{ ($sidebarColor ?? '') === 'forest' ? 'selected' : '' }}>Forest</option>
            <option value="wine" {{ ($sidebarColor ?? '') === 'wine' ? 'selected' : '' }}>Wine</option>
            <option value="indigo" {{ ($sidebarColor ?? '') === 'indigo' ? 'selected' : '' }}>Indigo</option>
          </select>
        </div>
      </div>
      <div class="form-row mt-3">
        <div class="col-md-6">
          <label>System Name (Sidebar Top)</label>
          <input type="text" name="system_name" class="form-control" maxlength="30" value="{{ $systemName ?? 'POS' }}" placeholder="e.g. NewPOS">
        </div>
        <div class="col-md-6">
          <label>Sidebar Logo</label>
          <input type="file" name="sidebar_logo" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg">
          <small class="text-muted">Square logo works best. Max 2MB.</small>
          @if(!empty($sidebarLogo))
            <div class="mt-2"><img src="{{ asset('assets/admin/img/settings/'.$sidebarLogo) }}" style="height:44px; width:44px; object-fit:cover; border-radius:8px; border:1px solid #d9e8df;"></div>
          @endif
        </div>
      </div>
      <br>
      <button class="btn btn-success">Update Settings</button>
    </form>
  </div>
</div>
<script>
  (function () {
    const range = document.getElementById('hero_overlay_range');
    const input = document.getElementById('hero_overlay_input');
    if (!range || !input) return;
    range.addEventListener('input', () => { input.value = range.value; });
    input.addEventListener('input', () => {
      let v = parseInt(input.value || '72', 10);
      if (Number.isNaN(v)) v = 72;
      v = Math.max(40, Math.min(85, v));
      input.value = v;
      range.value = v;
    });
  })();
</script>
@endsection
