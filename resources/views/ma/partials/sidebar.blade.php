<div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item">
                            <a href="{{ route('ma.dashboard') }}" class="nav-link{{ (isset($pageName) && $pageName == 'Dashboard') ? ' active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('ma.index') }}" class="nav-link{{ (isset($pageName) && $pageName == 'Applications') ? ' active' : '' }}">
                                <i class="nav-icon fas fa-list"></i>
                                <p>Applications</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('ma.dashboard.vcapproved') }}" class="nav-link{{ (isset($pageName) && $pageName == 'VCChecked') ? ' active' : '' }}">
                                <i class="nav-icon fas fa-check-double"></i>
                                <p>VC Checked</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('ma.status') }}" class="nav-link{{ (isset($pageName) && $pageName == 'Status') ? ' active' : '' }}">
                                <i class="nav-icon fas fa-stream"></i>
                                <p>Status</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>