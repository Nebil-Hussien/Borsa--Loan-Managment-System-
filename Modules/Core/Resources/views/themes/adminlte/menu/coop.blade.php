<style type="text/css">
    .login-logo img {
        height: 10%;
        width: 65%;
    }

    .login-logo {
        margin-bottom: 0px;
        margin-top: 15%
    }

</style>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="login-logo">
        <a href="{{ url('/') }}" class="logo-link text-center">
            <img class="logo-light logo-img logo-img-lg" src="{{ asset('storage/uploads/borsa_logo.jpg') }}"
                srcset="{{ asset('storage/uploads/borsa_logo.jpg') }} 2x" alt="logo">
        </a>
    </div>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                @if (!empty(Auth::user()->photo))
                    <img class="img-circle elevation-2" src="{{ asset('storage/uploads/' . Auth::user()->photo) }}"
                        alt="User Image">
                @else
                    <img class="img-circle elevation-2" src="{{ asset('themes/adminlte/img/user.png') }}"
                        alt="User profile picture">
                @endif
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</a>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">
                <li class="nav-item">
                    <a href="{{ url('portal/dashboard') }}"
                        class="nav-link @if (Request::is('portal/dashboard')) active @endif">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                            {{ trans_choice('dashboard::general.dashboard', 1) }}
                        </p>
                    </a>
                </li>
                <li class="nav-item has-treeview @if (Request::is('portal/loan*')) menu-open @endif">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-money-bill"></i>
                        <p>
                            {{ trans_choice('loan::general.loan', 2) }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('portal/loan') }}"
                                class="nav-link @if (Request::is('portal/loan')) active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ trans_choice('core::general.view', 1) . ' ' . trans_choice('loan::general.loan', 2) }}
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('portal/loan/application') }}"
                                class="nav-link @if (Request::is('portal/loan/application')) active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ trans_choice('core::general.view', 1) . ' ' . trans_choice('loan::general.application', 2) }}
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('portal/loan/application/create') }}"
                                class="nav-link @if (Request::is('portal/loan/application/create')) active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ trans_choice('core::general.create', 1) . ' ' . trans_choice('loan::general.application', 1) }}
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview @if (Request::is('portal/savings*')) menu-open @endif">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-building"></i>
                        <p>
                            {{ trans_choice('savings::general.savings', 2) }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('portal/savings') }}"
                                class="nav-link @if (Request::is('portal/savings')) active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ trans_choice('core::general.view', 1) . ' ' . trans_choice('savings::general.savings', 2) }}
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ url('portal/client') }}"
                        class="nav-link @if (Request::is('portal/client')) active @endif">
                        <i class="nav-icon fas fa-user"></i>
                        <p>
                            {{ trans_choice('client::general.client', 1) . ' ' . trans_choice('core::general.profile', 1) }}
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <!-- /.sidebar -->
</aside>
