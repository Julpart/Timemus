<?php
namespace App\Http\Controllers\API;
use App\Http\Resources\UserCollection;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Validator;

/** @OA\Get(
 * path="/api/users",
 * summary="Получение пользователей",
 * description="Получение пользователей пагинация по 10",
 * operationId="getUsers",
 * tags={"User"},
 * security={ {"api_key_security_example": {} }},
 * @OA\Response(
 * response=200,
 *    description="Success",
 *    @OA\JsonContent(
 *      @OA\Property(property="success", type="string")
*     )
 * ),
 * @OA\Response(
 *    response=401,
 *    description="Unauthenticated",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string")
*        )
 *     ),
 * @OA\Response(
 *    response=403,
 *    description="Email verified required",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string")
 *        )
 *     )
 * )
 * @OA\Get(
 * path="/api/users/{id}",
 * summary="Получение пользователя",
 * description="Получение пользователя",
 * operationId="getUser",
 * tags={"User"},
 * security={ {"api_key_security_example": {} }},
 * @OA\Parameter(
 *          name="id",
 *          description="user id",
 *          required=true,
 *          in="path",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 * @OA\Response(
 * response=200,
 *    description="Success",
 *    @OA\JsonContent(
 *      @OA\Property(property="success", type="string")
 *     )
 * ),
 * @OA\Response(
 *    response=400,
 *    description="Bad request",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string")
 *        )
 *     ),
 * @OA\Response(
 *    response=401,
 *    description="Unauthenticated",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string")
 *        )
 *     ),
 * @OA\Response(
 *    response=403,
 *    description="Email verified required",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string")
 *        )
 *     )
 * )
 * @OA\Put(
 * path="/api/users/{id}",
 * summary="Обновление пользователя",
 * description="Обновление данных пользователя",
 * operationId="updateUsers",
 * tags={"User"},
 * security={ {"api_key_security_example": {} }},
 * @OA\Parameter(
 *          name="id",
 *          description="user id",
 *          required=true,
 *          in="path",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 * @OA\RequestBody(
 *    required=true,
 *    @OA\JsonContent(
 *       required={"email,login"},
 *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
 *       @OA\Property(property="login", type="string", format="string", example="userName")
 *    ),
 * ),
 * @OA\Response(
 * response=200,
 *    description="Success",
 *    @OA\JsonContent(
 *      @OA\Property(property="success", type="string", example="userName")
 *     )
 * ),
 * @OA\Response(
 *    response=400,
 *    description="Bad request",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string")
 *        )
 *     ),
 * @OA\Response(
 *    response=401,
 *    description="Unauthenticated",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string")
 *        )
 *     ),
 * @OA\Response(
 *    response=403,
 *    description="Email verified required",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string")
 *        )
 *     )
 * )
 * @OA\Delete(
 * path="/api/users/{id}",
 * summary="Удаление пользователя",
 * description="Удаление пользователя",
 * operationId="deleteUsers",
 * tags={"User"},
 * security={ {"api_key_security_example": {} }},
 * @OA\Parameter(
 *          name="id",
 *          description="user id",
 *          required=true,
 *          in="path",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 * @OA\Response(
 * response=200,
 *    description="Success",
 *    @OA\JsonContent(
 *      @OA\Property(property="success", type="string", example="userName")
 *     )
 * ),
 * @OA\Response(
 *    response=400,
 *    description="Bad request",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string")
 *        )
 *     ),
 * @OA\Response(
 *    response=401,
 *    description="Unauthenticated",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string")
 *        )
 *     ),
 * @OA\Response(
 *    response=403,
 *    description="Email verified required",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string")
 *        )
 *     )
 * )
 */
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

    /**
     * @throws AuthorizationException
     */
    public function update(Request $request,$id){
        $input = $request->except('password');
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        $validator = Validator::make($input, [
            'email' => 'unique:users,email,'.$id,
            'login' => 'string',
            'avatar' => 'image:jpg, jpeg, png',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 400);
        }

        if(isset($input['avatar'])){
            Storage::disk('s3')->put('avatars/1', $input['avatar']);
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

    public function loadAvatar(Request $request,$id)
    {
        $input = $request->only('avatar');
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        $validator = Validator::make($input, [
            'avatar' => 'image:jpg, jpeg, png',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 400);
        }
        $path = null;
        if(isset($input['avatar'])){
            $path = Storage::disk('minio')->putFile('avatars', $input['avatar']);
        }
        $user->fill($input);
        $user->save();
        $user->path = $path;
        return response()->json(['success'=>$user], $this-> successStatus);
    }

}
