<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SocialMediaLinks as ModelsSocialMediaLinks;
use Illuminate\Http\Request;

class SocialMediaLinks extends Controller
{
    public function index(){
        $data=ModelsSocialMediaLinks::all();
        $data=$data->map(function($link){
            return [
                'id'=>$link->id,
                'title'=>$link->title,
                'link'=>$link->link,
                'icon'=>asset($link->icon)
            ];
        });
        return response()->json(['status' => 'success', 'data'=>$data], 200);
    }
}
