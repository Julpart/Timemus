<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Validator;

/** @OA\Get(
 * path="/api/projects",
 * summary="Получение проектов",
 * description="Получение проектов пагинация по 5",
 * operationId="getProjects",
 * tags={"Project"},
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
 * path="/api/projects/{id}",
 * summary="Получение проекта",
 * description="Получение проекта",
 * operationId="getProject",
 * tags={"Project"},
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
 * @OA\Post(
 * path="/api/projects",
 * summary="Создание проекта",
 * description="Создание проекта",
 * operationId="postProject",
 * tags={"Project"},
 * security={ {"api_key_security_example": {} }},
 * @OA\RequestBody(
 *    required=true,
 *    @OA\JsonContent(
 *       required={"name,description"},
 *       @OA\Property(property="name", type="string", format="name"),
 *       @OA\Property(property="description", type="string", format="string"),
 *       @OA\Property(property="users", type="array",
 *          @OA\Items(
 *              type="array",
 *              @OA\Items()
 *      ),
 *      description="участники проекта"
 *     )
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
 * @OA\Put(
 * path="/api/projects/{id}",
 * summary="Обновление проекта",
 * description="Обновление данных проекта",
 * operationId="updateProject",
 * tags={"Project"},
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
 *       @OA\Property(property="name", type="string", format="name"),
 *       @OA\Property(property="description", type="string", format="string"),
 *       @OA\Property(property="users", type="array",
 *          @OA\Items(
 *              type="array",
 *              @OA\Items()
 *      ),
 *      description="участники проекта"
 *     )
 *    )
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
 * path="/api/projects/{id}",
 * summary="Удаление проекта",
 * description="Удаление проекта",
 * operationId="deleteProjects",
 * tags={"Project"},
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
class ProjectController extends Controller
{
    public $successStatus = 200;

    public function index(){
        $projects = Project::paginate(5);
        return response()->json(['success'=>$projects], $this-> successStatus);
    }

    public function store(Request $request){
        $validator = Validator::make($request->only('name','description'), [
            'name' => 'required|unique:projects',
            'description' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 400);
        }
        $input = $request->all();
        $project = new Project;
        $project->fill($input);
        $project->save();
        $user = $request->user();
        $user->projects()->attach($project->id);
        $obj = ProjectUser::where('user_id',$user->id)->where('project_id',$project->id)->first();
        $obj->role_id = 2;
        $obj->save();
        return response()->json(['success' => $project], $this-> successStatus);
    }

    /**
     * @throws AuthorizationException
     */
    public function show($id){
        $project = Project::findOrFail($id);
        $this->authorize('view', $project);
        $users = $project->users()->paginate(10);
        foreach ($users as $item) {
            $obj = ProjectUser::where('user_id',$item['id'])->where('project_id',$id)->first();
            $item->role_id = $obj->role_id;
        }
        $tasks = $project->tasks()->paginate(10);
        $success['project'] = $project;
        $success['users'] = $users;
        $success['tasks'] = $tasks;
        return response()->json(['success'=>$success], $this-> successStatus);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(Request $request,$id){
        $input = $request->all();
        $project = Project::findOrFail($id);
        $this->authorize('update', $project);
        $validator = Validator::make($input, [
                'name' => 'unique:projects,name,'.$id,
                'users' => 'array',
                'users.*.id' => 'required|integer',
                'users.*.role_id' => 'integer',
            ]);
        if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 400);
            }
        if(isset($input['users'])){
            foreach ($input['users'] as $user) {
                $obj = ProjectUser::where('user_id',$user['id'])->where('project_id',$id)->first();
                if(isset($obj)){
                    $project->users()->detach($user['id']);
                }else{
                    $project->users()->attach($user['id']);
                    if(isset($user['role_id'])){
                        $obj = ProjectUser::where('user_id',$user['id'])->where('project_id',$id)->first();
                        $obj->role_id = $user['role_id'];
                        $obj->save();
                    }
                }
            }
        }
        $project->fill($input);
        $project->save();
        $users = $project->users()->paginate(10);
        foreach ($users as $item) {
            $obj = ProjectUser::where('user_id',$item['id'])->where('project_id',$id)->first();
            $item->role_id = $obj->role_id;
        }
        $tasks = $project->tasks()->paginate(10);
        $success['project'] = $project;
        $success['users'] = $users;
        $success['tasks'] = $tasks;
        return response()->json(['success'=>$success], $this-> successStatus);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $this->authorize('delete', $project);
        if(is_null($project)){
            return response()->json(['error'=>'The requested project was not found'], 404);
        }else{
            $project->tasks()->delete();
            $project->users()->detach();
            $project->delete();
            return response()->json(['success'=>'Project deleted successfully'], $this-> successStatus);
        }
    }
}
