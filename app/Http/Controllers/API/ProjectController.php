<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;
class ProjectController extends Controller
{
    public $successStatus = 200;

    public function index(){
        $projects = Project::paginate(5);
        return response()->json(['success'=>$projects], $this-> successStatus);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:projects',
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        $input = $request->all();
        $project = new Project;
        $project->fill($input);
        $project->save();
        $user = $request->user();
        $user->projects()->attach($project->id);
        return response()->json(['message' => "Project created successfully"], $this-> successStatus);
    }

    public function show($id){
        $project = Project::find($id);
        if(is_null($project)){
            return response()->json(['error'=>'The requested project was not found'], 404);
        }else{
            return response()->json(['success'=>$project], $this-> successStatus);
        }
    }
    public function update(Request $request,$id){
        $input = $request->all();
        $project = Project::find($id);
        if(is_null($project)){
            return response()->json(['error'=>'The requested project was not found'], 404);
        }else{
            $validator = Validator::make($input, [
                'name' => 'unique:projects',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);
            }
            $project->fill($input);
            $project->save();
            return response()->json(['success'=>$project], $this-> successStatus);
        }
    }

    public function destroy($id)
    {
        $project = Project::find($id);
        if(is_null($project)){
            return response()->json(['error'=>'The requested project was not found'], 404);
        }else{
            $project->users()->detach();
            $project->delete();
            return response()->json(['success'=>'Project deleted successfully'], $this-> successStatus);
        }
    }

    public function users($id){
        $project = Project::find($id);
        if(is_null($project)){
            return response()->json(['error'=>'The requested project was not found'], 404);
        }else {
            $users = $project->users()->paginate(10);
            return response()->json(['success' => $users], $this->successStatus);
        }
    }

    public function addUsers(Request $request,$id){
        $project = Project::find($id);
        if(is_null($project)){
            return response()->json(['error'=>'The requested project was not found'], 404);
        }else {
            $input = $request->all();
            $validator = Validator::make($input, [
                'users' => 'array',
            ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 401);
            }
            $users = $input['users'];
            foreach ($users as $item){
                $user = User::findOrFail($item);
                $obj = ProjectUser::where('user_id',$item)->where('project_id',$id)->first();
                if(is_null($obj)){
                    $user->projects()->attach($id);
                }else{
                    return response()->json(['error'=>'user '.$item.' is already in the project'], 401);
                }
            }
            return response()->json(['message' => 'Users add in the project'], $this->successStatus);
        }
    }
}
