<?php
/**
 * Created by PhpStorm.
 * User: GMG-Developer
 * Date: 23/01/2018
 * Time: 10:45
 */

namespace App\Transformer\MasterData;


use App\Models\Menu;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class MenuTransformer extends TransformerAbstract
{
    public function transform(Menu $menu){

        $action =
            "<a class='btn btn-xs btn-info' href='menus/".$menu->id."/ubah' data-toggle='tooltip' data-placement='top'><i class='fa fa-pencil'></i></a>";
        $action .= "<a class='delete-modal btn btn-xs btn-danger' data-id='". $menu->id ."' ><i class='fa fa-trash'></i></a>";

        return[
            'name'           => $menu->name,
            'route'          => $menu->route,
            'menu_header'    => $menu->menu_header->name,
            'action'         => $action
        ];
    }
}