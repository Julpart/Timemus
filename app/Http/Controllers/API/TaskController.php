<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\Tag;
use App\Models\TagTask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class TaskController extends Controller
{
    public $successStatus = 200;

    public function index(){
        $tasks = Task::paginate(10);
        return response()->json(['success'=>$tasks], $this-> successStatus);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'estimate_time' => 'required|numeric|gt:0',
            'executor_id' => 'required|numeric|gt:0',
            'project_id' => 'required|int|gt:0',
            'tags' => 'array',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 400);
        }
        $input = $request->all();
        $task = new Task;
        $creator = User::findOrFail($input['executor_id']);
        $project = Project::findOrFail($input['project_id']);
        $obj = ProjectUser::where('user_id',$creator->id)->where('project_id',$project->id)->first();
        if(is_null($obj)){
            return response()->json(['error'=>'user '.$creator->id.' does not participate in the work on this project'], 401);
        }
        $tags = $input['tags'];
        foreach ($tags as $item){
            Tag::findOrFail($item);
            $task->tags()->attach($item);
        }
        $project->estimate_time += $input['estimate_time'];
        $project->save();
        $user = $request->user();
        $task->fill($input);
        $task->creator()->associate($user);
        $task->save();
        $task->creator;
        $task->project;
        $task->executor;
        return response()->json(['success' => $task], $this-> successStatus);
    }

    public function show($id){
        $task = Task::findOrFail($id);
        $task->creator;
        $task->project;
        $task->executor;
        return response()->json(['success'=>$task], $this-> successStatus);
    }
    public function update(Request $request,$id){
        $task = Task::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'estimate_time' => 'numeric|gt:0',
            'executor_id' => 'numeric|gt:0',
            'project_id' => 'numeric|gt:0',
            'tags' => 'array',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 400);
        }
        $input = $request->all();
        $creator = $task->creator;
        $project = $task->project;
        if(isset($input['executor_id'])) $creator = User::findOrFail($input['executor_id']);
        if(isset($input['project_id'])) $project = Project::findOrFail($input['project_id']);
        $obj = ProjectUser::where('user_id',$creator->id)->where('project_id',$project->id)->first();
        if(is_null($obj)){
            return response()->json(['error'=>'user '.$creator->id.' does not participate in the work on this project'], 400);
        }
        if(isset($input['tags'])){
            foreach ($input['tags'] as $tag) {
                $obj = TagTask::where('tag_id',$tag)->where('task_id',$id)->first();
                if(isset($obj)){
                    $task->tags()->detach($tag);
                }else{
                    $task->tags()->attach($tag);
                }
            }
        }

        if(isset($input['estimate_time'])) {
            if ($task->project == $project) {
                $project->estimate_time -= $task->estimate_time;
                $project->estimate_time += $input['estimate_time'];
                $project->save();
            } else {
                $task->project->estimate_time -= $task->estimate_time;
                $task->project->save();
                $project->estimate_time += $input['estimate_time'];
                $project->save();
            }
        }
        $task->fill($input);
        $task->save();
        $task->executor;
        $task->tags;
        return response()->json(['success' => $task], $this-> successStatus);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->project->estimate_time -= $task->estimate_time;
        $task->project->save();
        $task->delete();
        return response()->json(['success'=>'Task deleted successfully'], $this-> successStatus);
    }


}
