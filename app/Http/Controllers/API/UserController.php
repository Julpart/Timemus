<?php
namespace App\Http\Controllers\API;
use App\Http\Resources\UserCollection;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
class UserController extends Controller
{
    public $successStatus = 200;
    public function index(){
        $users = User::paginate(10);
        return response()->json(['success'=>$users], $this-> successStatus);
    }

    public function show($id){
        $user = User::findOrFail($id);
        $projects = $user->projects()->paginate(10);
        $created_tasks = $user->createdTasks()->paginate(10);
        $assigned_tasks = $user->assignedTasks()->paginate(10);
        $success['user'] = $user;
        $success['projects'] = $projects;
        $success['created_tasks'] = $created_tasks;
        $success['assigned_tasks'] = $assigned_tasks;
        return response()->json(['success'=>$success], $this-> successStatus);
    }
    public function update(Request $request,$id){
        $input = $request->all();
        $user = User::findOrFail($id);
        $validator = Validator::make($input, [
            'email' => 'unique:users,email,'.$id,
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 400);
        }
        $user->fill($input);
        $user->save();
        return response()->json(['success'=>$user], $this-> successStatus);
        }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->projects()->detach();
        $user->delete();
        return response()->json(['success'=>'User deleted successfully'], $this-> successStatus);
    }

}
