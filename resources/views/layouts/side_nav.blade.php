<div class="side-nav">
    <ul class="list-unstyled modules">
        @foreach ($menus as $menu_key_id => $menu)
            
            @php $sub_menu_exists = (isset($menu['sub_menu']))?"dropdown-toggle":""; @endphp
            @php $data_toggle_check = (isset($menu['sub_menu']))?'collapse':""; @endphp
            @php $main_route = (isset($menu['route']) && $menu['route']!='')?route($menu['route']):'#'; @endphp
            @php $menu_selected = ($menu['menu_key'] == $menu_key)?"menu-selected":""; @endphp
            
            <li class="">
                
                <a href="{{ ($sub_menu_exists == '')? $main_route : '#menu'.$menu_key_id }}"  data-toggle="{{ $data_toggle_check }}" aria-expanded="false" class="{{ $sub_menu_exists }} {{ $menu_selected }} module-parent"> {{ $menu['label'] }}</a>
                
                @isset($menu['sub_menu'])
                <ul class="collapse list-unstyled module" id="menu{{ $menu_key_id }}" aria-expanded="false">
                    
                    @foreach ($menu['sub_menu'] as $sub_menu)
                    
                    @php $submenu_selected = (isset($sub_menu["menu_key"]) && isset($sub_menu_key))?(($sub_menu["menu_key"] == $sub_menu_key)?"submenu-selected":""):""; @endphp
                    @php $route = (isset($sub_menu['route']) && $sub_menu['route']!='')?route($sub_menu['route']):'#'; @endphp
                    
                    <li>
                        <a href="{{ $route }}" class="{{ $submenu_selected }}">{{ $sub_menu['label'] }}</a>
                    </li>
                    
                    @endforeach
                
                </ul>
                @endisset
            
            </li>
        @endforeach
    </ul>
</div>