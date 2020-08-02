@php
    $profile_photo  = get_profile_photo($logged_user_data['profile_image']);  
    $stores         = $logged_user_data['user_stores'];
    $user_fullname  = $logged_user_data['fullname']; 
    $selected_store = $logged_user_data['selected_store'];
@endphp
<nav class="navbar navbar-expand-lg top-nav p-2">
    <div class="container-fluid">
        
        <a class="navbar-brand" href="/">
            <img src="{{ asset('images/logo_small.png') }}" class="d-inline-block align-top top-nav-logo  ml-lg-1 ml-sm-4" alt="appsthing"/>
        </a>

        @if (!isset($order))
        <div id="menu-toggle" class="menu-toggler mr-auto">
            <div class="bar-1"></div>
            <div class="bar-2"></div>
            <div class="bar-3"></div>
        </div>
        @endif
        
        <button class="navbar-toggler dropdown-toggle" type="button" data-toggle="collapse" data-target="#small_menu_toogler" aria-controls="small_menu_toogler" aria-expanded="false" aria-label="Toggle actions">
            <img src="{{ $profile_photo }}" class="d-inline-block rounded-circle mr-2 top-nav-profile" alt="">
        </button>

        <div class="collapse navbar-collapse" id="small_menu_toogler">
            
            <ul class="navbar-nav mt-lg-0 ml-md-5 pt-1">
                @if (session('slack') != "")
                <li class="nav-item text-right">
                    <storeselectorcomponent :stores = "{{ json_encode($stores) }}" :selected_store = "{{ json_encode($selected_store) }}"></storeselectorcomponent>
                </li>
                @endif
            </ul>

            <ul class="navbar-nav ml-auto mt-lg-0  pt-1">
                @if (session('slack') != "")
                <li class="nav-item text-right pl-md-4 pl-lg-4 pl-xl-4">
                    <a href="/search" class="nav-link text-bold"><i class="fas fa-search search-icon"></i> Search</a>
                </li>
                @if (check_access(array('A_ADD_ORDER'), true))
                <li class="nav-item text-right pl-md-4 pl-lg-4 pl-xl-4">
                    <a href="/add_order" class="nav-link text-bold">+ New Order</a>
                </li>
                @endif
                <li class="nav-item text-right pl-md-4 pl-lg-4 pl-xl-4">
                    <div class="dropdown">
                        <a href="#" class="nav-link dropdown-toggle text-bold" id="user_menu_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="{{ $profile_photo }}" class="d-inline-block rounded-circle mr-2 top-nav-profile" alt="">
                            {{ $user_fullname }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="user_menu_dropdown" >
                            <a class="dropdown-item" href="/profile/{{ session('slack') }}">Profile</a>
                            <a class="dropdown-item" href="/logout">Logout</a>
                        </div>
                    </div>
                </li>
                @else
                <li class="nav-item text-right ml-auto pl-md-4 pl-lg-4 pl-xl-4">
                    <a href="/" class="nav-link">Sign in</a>
                </li>
                @endif
            </ul>

        </div>

        <notifications 
        group="notification_bar"
        classes="n-light" 
        :duration="55000"
        :width="500"
        position="top right"/>

    </div>
</nav>
