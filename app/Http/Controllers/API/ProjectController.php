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
        return response()->json(['success' => $project], $this-> successStatus);
    }

    public function show($id){
        $project = Project::findOrFail($id);
        $users = $project->users()->paginate(10);
        $tasks = $project->tasks()->paginate(10);
        $success['project'] = $project;
        $success['users'] = $users;
        $success['tasks'] = $tasks;
        return response()->json(['success'=>$success], $this-> successStatus);
    }
    public function update(Request $request,$id){
        $input = $request->all();
        $project = Project::findOrFail($id);
        $validator = Validator::make($input, [
                'name' => 'unique:projects,name,'.$id,
                'users' => 'array',
            ]);
        if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 400);
            }
        if(isset($input['users'])){
            foreach ($input['users'] as $user) {
                $obj = ProjectUser::where('user_id',$user)->where('project_id',$id)->first();
                if(isset($obj)){
                    $project->users()->detach($user);
                }else{
                    $project->users()->attach($user);
                }
            }
        }
        $project->fill($input);
        $project->save();
        $users = $project->users()->paginate(10);
        $tasks = $project->tasks()->paginate(10);
        $success['project'] = $project;
        $success['users'] = $users;
        $success['tasks'] = $tasks;
        return response()->json(['success'=>$success], $this-> successStatus);
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
}
