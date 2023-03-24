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
        $user = User::find($id);
        if(is_null($user)){
            return response()->json(['error'=>'The requested user was not found'], 404);
        }else{
            return response()->json(['success'=>$user], $this-> successStatus);
        }
    }
    public function update(Request $request,$id){
        $input = $request->all();
        $user = User::find($id);
        if(is_null($user)){
            return response()->json(['error'=>'The requested user was not found'], 404);
        }else{
            foreach ($input as $key => $value){
                if($key == 'email'){
                    $validator = Validator::make([$key => $value], [
                        'email' => 'required|email|unique:users',
                    ]);
                    if ($validator->fails()) {
                        return response()->json(['error'=>$validator->errors()], 401);
                    }
                    $user->email = $value;
                }
                if($key == 'login'){
                    $user->login = $value;
                }
                if($key == 'password'){
                    $user->password = bcrypt($value);
                }

            }
            $user->save();
            return response()->json(['success'=>$user], $this-> successStatus);
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if(is_null($user)){
            return response()->json(['error'=>'The requested user was not found'], 404);
        }else{
            $user->projects()->detach();
            $user->delete();
            return response()->json(['success'=>'User deleted successfully'], $this-> successStatus);
        }
    }

    public function projects($id){
        $user = User::find($id);
        if(is_null($user)){
            return response()->json(['error'=>'The requested user was not found'], 404);
        }else {
            $projects = $user->projects()->paginate(10);
            return response()->json(['success' => $projects], $this->successStatus);
        }
    }

}
