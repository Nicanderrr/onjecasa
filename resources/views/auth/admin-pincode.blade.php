<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Admin Pincode</title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
  <link href="{{ asset('assets/admin/vendor/nucleo/css/nucleo.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/admin/vendor/@fortawesome/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
  <link type="text/css" href="{{ asset('assets/admin/css/argon.css?v=1.0.0') }}" rel="stylesheet">
  <style>
    html, body { background-color: #000; color: #636b6f; font-family: 'Nunito', sans-serif; font-weight: 200; height: 100vh; margin: 0; overflow-y: hidden; }
    .full-height { height: 100vh; }
    .flex-center { align-items: center; display: flex; justify-content: center; }
    .content { text-align: center; margin-bottom: 80px; }
    .card { padding: 20px 30px; padding-top: 29px; }
    .wel h2 { position: relative; top: 100px; }
  </style>
</head>
<body class="bg-dark">
  <h1 class="alert alert-secondary text-center text-dark">Hello, <span style="text-transform:uppercase;">{{ $adminName }}</span></h1>
  <div class="wel">
    <h2 class="text-center text-light">Enter Code to Access <span class="text-teal">Admin Dashboard</span></h2>
  </div>
  <div class="flex-center full-height">
    <div class="content card">
      <form method="POST" action="{{ route('admin.pincode.verify') }}">
        @csrf
        <span class="input-group-text bg-light"><i class="ni ni-lock-circle-open"></i>
          <input style="border:none; font-size:large;" type="password" name="admin_pincode" maxlength="6" class="form-control" id="pincode" placeholder="Enter code" required />
        </span>
        @if($errors->any())
          <div class="text-danger mt-2">{{ $errors->first() }}</div>
        @endif
        <div><button class="btn btn-primary btn-block my-3" type="submit">Enter</button></div>
      </form>
    </div>
  </div>

  <script src="{{ asset('assets/admin/vendor/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/admin/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script>document.addEventListener('DOMContentLoaded', ()=> document.getElementById('pincode').focus());</script>
</body>
</html>
