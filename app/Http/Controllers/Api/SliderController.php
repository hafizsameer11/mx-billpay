<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function index(){
        $slides=Slide::all();
        $slides=$slides->map(function($slide){
            return [
                'id'=>$slide->id,
                'image'=>asset('storage/'.$slide->image),
            ];
        });
        return response()->json(['status'=>'success','data'=>$slides],200);
    }
}
