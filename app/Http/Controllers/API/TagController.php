<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tag;

class TagController extends Controller
{
    public $successStatus = 200;
    public function index(){
        $tags = Tag::paginate(10);
        return response()->json(['success'=>$tags], $this->successStatus);
    }
    public function show($id){
        $tag = Tag::findOrFail($id);
        $tasks = $tag->tasks()->paginate(10);
        $success['tag'] = $tag;
        $success['tasks'] = $tasks;
        return response()->json(['success'=>$success], $this-> successStatus);
    }
}
