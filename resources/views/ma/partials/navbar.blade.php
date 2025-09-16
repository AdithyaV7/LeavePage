<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('ma.index') }}" class="nav-link">Applications</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('ma.dashboard') }}" class="nav-link">Dashboard</a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('ma.dashboard.vcapproved') }}" class="nav-link">VC Checked</a>
        </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user"></i> MA
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ route('ma.index') }}" class="dropdown-item">View Applications</a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('logout') }}" class="dropdown-item">Logout</a>
            </div>
        </li>
    </ul>
</nav> 

<style>
   .bg-info {
        background-color: #007bff !important;
    }

    .details-header {
        background-color: #007bff !important;
        color:rgb(255, 255, 255) !important;
    }
    .details-name {
        color:rgb(255, 255, 255) !important;
    }
    .document-header {
        background-color: #007bff !important;
    }
</style>