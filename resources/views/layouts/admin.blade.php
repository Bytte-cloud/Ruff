<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('app.name', 'Ruff') }} - @yield('title')</title>
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta name="_token" content="{{ csrf_token() }}">

    <link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="/favicons/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="/favicons/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="/favicons/manifest.json">
    <link rel="mask-icon" href="/favicons/safari-pinned-tab.svg" color="#bc6e3c">
    <link rel="shortcut icon" href="/favicons/favicon.ico">
    <meta name="msapplication-config" content="/favicons/browserconfig.xml">
    <meta name="theme-color" content="#009d91">

    @include('layouts.scripts')
    @section('scripts')
        {!! Theme::css('vendor/select2/select2.min.css?t={cache-version}') !!}
        {!! Theme::css('vendor/sweetalert/sweetalert.min.css?t={cache-version}') !!}
        {!! Theme::css('vendor/animate/animate.min.css?t={cache-version}') !!}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="/themes/Ruff/css/admin.css">
        @if(!empty($siteConfiguration['theme']['share_accent_with_admin']) && !empty($siteConfiguration['theme']['modes']['light']['accent']))
            @php
                // Allow only valid color-token characters before emitting into a raw CSS
                // context, so a stored value can't inject extra rules (';', '{', '}', ...).
                $accent = trim((string) $siteConfiguration['theme']['modes']['light']['accent']);
                $accent = preg_match('/^(#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})|rgba?\([0-9.,%\s\/]+\)|hsla?\([0-9.,%\s\/a-z]+\)|[a-zA-Z]+)$/', $accent) ? $accent : null;
            @endphp
            @if($accent)
                {{-- Share the client theme's accent with the admin panel (all accent shades derive from --accent). --}}
                <style>:root{ --accent: {{ $accent }}; }</style>
            @endif
        @endif
    @show
</head>

<body>
    <div class="admin-wrapper">
        <header class="admin-header">
            <div class="admin-header-left" style="display:flex;align-items:center;gap:6px;">
                <a href="#" class="sidebar-toggle" id="sidebar-toggle" role="button" aria-label="Toggle navigation">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </a>
                <a href="{{ route('index') }}" class="admin-brand">{{ config('app.name', 'Ruff') }}</a>
            </div>

            <div class="admin-header-actions">
                <a href="{{ route('account') }}" class="admin-nav-btn">
                    <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(Auth::user()->email)) }}?s=160" class="avatar" alt="User Image">
                    <span class="label-name hidden-xs">{{ Auth::user()->name_first }} {{ Auth::user()->name_last }}</span>
                </a>
                <a href="{{ route('index') }}" class="admin-nav-btn admin-nav-icon" data-toggle="tooltip" data-placement="bottom" title="Exit Admin Control">
                    <i class="fa fa-server"></i>
                </a>
                <a href="{{ route('auth.logout') }}" id="logoutButton" class="admin-nav-btn admin-nav-icon" data-toggle="tooltip" data-placement="bottom" title="Logout">
                    <i class="fa fa-sign-out"></i>
                </a>
            </div>
        </header>

        <aside class="admin-sidebar" id="main-sidebar">
            <nav>
                @php
                    $navItems = [
                        ['admin.index', 'fa-home', 'Overview', false],
                        ['admin.settings', 'fa-wrench', 'Settings', false],
                        ['admin.api.index', 'fa-gamepad', 'Application API', false],
                        '__sep__',
                        ['admin.databases', 'fa-database', 'Databases', true],
                        ['admin.locations', 'fa-globe', 'Locations', true],
                        ['admin.nodes', 'fa-sitemap', 'Nodes', true],
                        ['admin.servers', 'fa-server', 'Servers', true],
                        ['admin.users', 'fa-users', 'Users', true],
                        '__sep__',
                        ['admin.mounts', 'fa-magic', 'Mounts', true],
                        ['admin.nests', 'fa-th-large', 'Nests', true],
                    ];
                @endphp

                @foreach ($navItems as $item)
                    @if ($item === '__sep__')
                        <span class="nav-sep"></span>
                    @else
                        @php
                            [$route, $icon, $label, $prefix] = $item;
                            $current = Route::currentRouteName();
                            $isActive = $prefix ? starts_with($current, $route) : $current === $route;
                        @endphp
                        <a href="{{ route($route) }}" class="{{ $isActive ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="{{ $label }}">
                            <i class="fa {{ $icon }}"></i>
                        </a>
                    @endif
                @endforeach
            </nav>
        </aside>

        <main class="admin-content" id="content-wrapper">
            <section class="content-header">
                @yield('content-header')
            </section>

            <section class="content-body">
                @if (count($errors) > 0)
                    <div class="alert alert-danger" role="alert">
                        <strong>Validation Error!</strong> There was an error validating the data provided.
                        <ul style="margin:8px 0 0;padding-left:20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @foreach (Alert::getMessages() as $type => $messages)
                    @foreach ($messages as $message)
                        @php
                            $alertClass = [
                                'success' => 'alert-success',
                                'info' => 'alert-info',
                                'warning' => 'alert-warning',
                                'danger' => 'alert-danger',
                            ][$type] ?? 'alert-info';
                        @endphp
                        <div class="alert {{ $alertClass }}" role="alert">
                            {!! $message !!}
                        </div>
                    @endforeach
                @endforeach

                @yield('content')
            </section>
        </main>

        <footer class="admin-footer">
            <div>
                Copyright &copy; 2015 - {{ date('Y') }} <a href="https://Ruff.io/">Ruff Software</a>.
            </div>
            <div class="meta">
                <span><i class="fa fa-fw {{ $appIsGit ? 'fa-git-square' : 'fa-code-fork' }}"></i> {{ $appVersion }}</span><br>
                <span><i class="fa fa-fw fa-clock-o"></i> {{ round(microtime(true) - LARAVEL_START, 3) }}s</span>
            </div>
        </footer>
    </div>

    @section('footer-scripts')
        <script src="/js/keyboard.polyfill.js" type="application/javascript"></script>
        <script>
            keyboardeventKeyPolyfill.polyfill();
        </script>

        {!! Theme::js('vendor/jquery/jquery.min.js?t={cache-version}') !!}
        {!! Theme::js('vendor/sweetalert/sweetalert.min.js?t={cache-version}') !!}
        {!! Theme::js('vendor/bootstrap/bootstrap.min.js?t={cache-version}') !!}
        {!! Theme::js('vendor/slimscroll/jquery.slimscroll.min.js?t={cache-version}') !!}
        {!! Theme::js('vendor/adminlte/app.min.js?t={cache-version}') !!}
        {!! Theme::js('vendor/bootstrap-notify/bootstrap-notify.min.js?t={cache-version}') !!}
        {!! Theme::js('vendor/select2/select2.full.min.js?t={cache-version}') !!}
        {!! Theme::js('js/admin/functions.js?t={cache-version}') !!}
        <script src="/js/autocomplete.js" type="application/javascript"></script>

        @if(Auth::user()->root_admin)
            <script>
                $('#logoutButton').on('click', function (event) {
                    event.preventDefault();
                    swal({
                        title: 'Do you want to log out?',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d9534f',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Log out'
                    }, function () {
                        $.ajax({
                            type: 'POST',
                            url: '{{ route('auth.logout') }}',
                            data: { _token: '{{ csrf_token() }}' },
                            complete: function () {
                                window.location.href = '{{ route('auth.login') }}';
                            }
                        });
                    });
                });
            </script>
        @endif

        <script>
            $(function () {
                $('[data-toggle="tooltip"]').tooltip({ container: 'body' });

                // Collapse / reveal the sidebar rail. A single body class flips the
                // default state at each breakpoint (open on desktop, closed on mobile).
                $('#sidebar-toggle').on('click', function (e) {
                    e.preventDefault();
                    $('body').toggleClass('sidebar-toggled');
                });
            });
        </script>
    @show
</body>

</html>
