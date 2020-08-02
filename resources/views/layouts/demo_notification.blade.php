<nav class="navbar navbar-dark bg-danger text-white p-3">
    <span class="mx-auto text-bold">
        @isset($demo['demo_notification'])
            {{ $demo['demo_notification'] }}
        @endisset
        @isset($logged_user_data['demo_notification'])
            {{ $logged_user_data['demo_notification'] }}
        @endisset
    </span>
</nav>