<!-- resources/views/layouts/app.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    body {
        padding-top: 60px; /* or whatever your header height is */
    }
</style>

</head>
<body>
    <header class="bg-secondary text-white py-3 fixed-top">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('leaves.index') }}" class="text-white text-decoration-none fw-bold fs-5">
                    <i class="bi bi-house"></i>    Home
                </a>
            </div>
            <h4 class="mb-0 text-center w-100">Leave Requests</h4>
            <div>
                <a href="{{ route('logout') }}" class="text-white text-decoration-none fw-bold">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <main class="py-4" style="padding-top: 80px;">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
