<!doctype html>
<html lang="en">
    @include('layouts.header')
    <div id="app">
        <body class="conatiner-fluid">
            @includeWhen((isset($logged_user_data['demo_notification']) && $logged_user_data['demo_notification'] != ''), 'layouts.demo_notification')
            @include('layouts.top_nav')
            <div class="wrapper">
                @include('layouts.side_nav')
                <div class="content">
                    @yield('content')
                </div>
            </div>     
        </body>
    </div>
    @include('layouts.footer')
    @stack('scripts')
</html>