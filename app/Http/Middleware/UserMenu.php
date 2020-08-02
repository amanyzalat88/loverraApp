<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Menu as MenuModel;
use App\Models\User as UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class UserMenu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $menu_array = array();
        $logged_in_user = $request->logged_user_id;

        if($request->logged_user_role_id == 1){
            $menus = MenuModel::select('*')
            ->active()
            ->orderByRaw('FIELD(type , "MAIN_MENU", "SUB_MENU") ASC')
            ->orderBy('sort_order', 'ASC')
            ->get();
        }else{
            $menus = DB::table('user_menus')
            ->select('menus.*')
            ->join('menus', 'menus.id', '=', 'user_menus.menu_id')
            ->where('user_menus.user_id', $logged_in_user)
            ->orderByRaw('FIELD(type , "MAIN_MENU", "SUB_MENU") ASC')
            ->orderBy('sort_order', 'ASC')
            ->get();
        }
        
        foreach($menus as $menu){
            if($menu->type == "MAIN_MENU"){
                $menu_array[$menu->id] = [
                    "menu_key" => $menu->menu_key,
                    "label" => $menu->label,
                    "route" => $menu->route
                ];
            }else if($menu->type == "SUB_MENU"){
                if(isset($menu_array[$menu->parent])){
                    unset($menu_array[$menu->parent]["route"]);
                    $menu_array[$menu->parent]['sub_menu'][] = [
                        "sub_menu_id" => $menu->id,
                        "menu_key" => $menu->menu_key,
                        "label" => $menu->label,
                        "route" => $menu->route
                    ];
                }
            }
        }
        
        View::share('menus', $menu_array);
        
        return $next($request);
    }
}
