<!-- resources/views/layouts/app.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Leave Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Maroon and Gold Theme -->
    <link rel="stylesheet" href="{{ asset('css/maroon-gold-theme.css') }}">

<style>
    body {
        padding-top: 60px; /* or whatever your header height is */
    }
</style>
<!--
    <style>
        body {
            background-color: #ffffff;
        }
        .card {
            background-color: #faf560;
            border: none;
        }
        .card-header {
            background-color: #f8970e;
            border-bottom: 2px solid #ccc;
        }
        label.form-label {
            font-weight: 600;
        }
        .form-text {
            font-size: 0.9rem;
        }
    </style>
    -->

</head>
<body>
    <header class="bg-maroon-primary text-white py-3 fixed-top">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('leaves.index') }}" class="text-white text-decoration-none fw-bold fs-5 hover-gold">
                    <i class="bi bi-house me-1"></i>Home
                </a>
            </div>
            <h4 class="mb-0 text-center w-100 text-gold">Application for Conference/ Seminar/ Training and Workshop</h4>
            <div>
                <a href="{{ route('logout') }}" class="text-white text-decoration-none fw-bold hover-gold">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            </div>
        </div>
    </header>

    <main class="py-4" style="padding-top: 80px;">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- SweetAlert2 Custom Styling -->
    <style>
        .swal2-popup {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .swal2-title {
            color: #333;
            font-weight: 600;
        }
        
        .swal2-confirm {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
        }
        
        .swal2-confirm:hover {
            background-color: #c82333 !important;
            border-color: #bd2130 !important;
        }
        
        .swal2-warning .swal2-confirm {
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
            color: #212529 !important;
        }
        
        .swal2-warning .swal2-confirm:hover {
            background-color: #e0a800 !important;
            border-color: #d39e00 !important;
        }
        
        .swal2-toast {
            border-radius: 8px;
        }
    </style>
</body>
</html>
