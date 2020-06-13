<!doctype html>
<html lang="en">
    @include('layouts.header')
    <div id="app">
        <body>
            @includeWhen((isset($demo['demo_notification']) && $demo['demo_notification'] != ''), 'layouts.demo_notification')
            <div class="wrapper">
                <div class="content">
                    @yield('content')
                </div>
            </div>     
        </body>
    </div>
    @include('layouts.footer')
</html>