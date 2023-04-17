<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\Tag;
use App\Models\TagTask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Enums\TaskStatus;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Validator;

/** @OA\Get(
 * path="/api/tasks",
 * summary="Получение тасак",
 * description="Получение тасак пагинация по 10",
 * operationId="getTasks",
 * tags={"Task"},
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
 * path="/api/tasks/{id}",
 * summary="Получение таски",
 * description="Получение таски",
 * operationId="getTask",
 * tags={"Task"},
 * security={ {"api_key_security_example": {} }},
 * @OA\Parameter(
 *          name="id",
 *          description="task id",
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
 * path="/api/tasks/{id}",
 * summary="Обновление таски",
 * description="Обновление таски",
 * operationId="updateTasks",
 * tags={"Task"},
 * security={ {"api_key_security_example": {} }},
 * @OA\Parameter(
 *          name="id",
 *          description="task id",
 *          required=true,
 *          in="path",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 * @OA\RequestBody(
 *    required=true,
 *    @OA\JsonContent(
 *       @OA\Property(property="name", type="string"),
 *       @OA\Property(property="description", type="string"),
 *       @OA\Property(property="estimate_time", type="double"),
 *       @OA\Property(property="executor_id", type="integer"),
 *       @OA\Property(property="project_id", type="integer"),
 *       @OA\Property(property="tags", type="array",
 *          @OA\Items(
 *              type="array",
 *              @OA\Items()
 *      ),
 *      description="тэги проекта"
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
 * @OA\Delete(
 * path="/api/tasks/{id}",
 * summary="Удаление таски",
 * description="Удаление таски",
 * operationId="deleteTasks",
 * tags={"Task"},
 * security={ {"api_key_security_example": {} }},
 * @OA\Parameter(
 *          name="id",
 *          description="task id",
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
 * @OA\Post(
 * path="/api/tasks",
 * summary="Создание таски",
 * description="Создание таски",
 * operationId="postTask",
 * tags={"Task"},
 * security={ {"api_key_security_example": {} }},
 * @OA\RequestBody(
 *    required=true,
 *    @OA\JsonContent(
 *       required={"name,description,estimate_time,executor_id,project_id"},
 *       @OA\Property(property="name", type="string"),
 *       @OA\Property(property="description", type="string"),
 *       @OA\Property(property="estimate_time", type="double"),
 *       @OA\Property(property="executor_id", type="integer"),
 *       @OA\Property(property="project_id", type="integer"),
 *       @OA\Property(property="tags", type="array",
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
 */
class TaskController extends Controller
{
    public $successStatus = 200;

    public function index(){
        $tasks = Task::paginate(10);
        return response()->json(['success'=>$tasks], $this-> successStatus);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'estimate_time' => 'required|numeric|gt:0',
            'executor_id' => 'required|int|gt:0',
            'project_id' => 'required|int|gt:0',
            'tags' => 'array',
            'status' => [new Enum(TaskStatus::class)],
            'real_time' => 'numeric|gt:0',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $input = $request->all();
        $user = $request->user();
        $task = new Task;
        $executor = User::findOrFail($input['executor_id']);
        $project = Project::findOrFail($input['project_id']);
        $obj = ProjectUser::where('user_id', $executor->id)->where('project_id', $project->id)->first();
        if (is_null($obj)) {
            return response()->json(['error' => 'user ' . $executor->id . ' does not participate in the work on this project'],
                400);
        }
        $creatorObj = ProjectUser::where('user_id', $user->id)->where('project_id', $project->id)->first();
        if (is_null($creatorObj) or $creatorObj->role_id !== 2) {
            return response()->json(['error' => 'user ' . $user->id . ' has insufficient rights in this project'],
                400);
        }
        if (isset($input['tags'])) {
            $tags = $input['tags'];
        foreach ($tags as $item) {
            Tag::findOrFail($item);
            $task->tags()->attach($item);
        }
    }
        $project->estimate_time += $input['estimate_time'];
        $project->save();
        $task->fill($input);
        $task->creator()->associate($user);
        $task->save();
        $task->creator;
        $task->project;
        $task->executor;
        return response()->json(['success' => $task], $this-> successStatus);
    }

    /**
     * @throws AuthorizationException
     */
    public function show($id){
        $task = Task::findOrFail($id);
        $this->authorize('view', $task);
        $task->creator;
        $task->project;
        $task->executor;
        return response()->json(['success'=>$task], $this-> successStatus);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(Request $request,$id){
        $task = Task::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'estimate_time' => 'numeric|gt:0',
            'executor_id' => 'numeric|gt:0',
            'project_id' => 'numeric|gt:0',
            'tags' => 'array',
            'status' => [new Enum(TaskStatus::class)],
            'real_time' => 'numeric|gt:0',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 400);
        }
        $this->authorize('update', $task);
        $user = $request->user();
        if($user->id === $task->creator->id){
            $input = $request->all();
        }else{
            $input = $request->only('status','real_time');
        }
        $executor = $task->executor;
        $project = $task->project;
        if(isset($input['executor_id'])) $executor = User::findOrFail($input['executor_id']);
        if(isset($input['project_id'])) $project = Project::findOrFail($input['project_id']);
        $obj = ProjectUser::where('user_id',$executor->id)->where('project_id',$project->id)->first();
        if(is_null($obj)){
            return response()->json(['error'=>'user '.$executor->id.' does not participate in the work on this project'], 400);
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

    /**
     * @throws AuthorizationException
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $this->authorize('delete', $task);
        $task->project->estimate_time -= $task->estimate_time;
        $task->project->save();
        $task->delete();
        return response()->json(['success'=>'Task deleted successfully'], $this-> successStatus);
    }


}
