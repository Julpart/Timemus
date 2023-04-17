<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tag;
/** @OA\Get(
 * path="/api/tags",
 * summary="Получение тэгов",
 * description="Получение тэгов пагинация по 10",
 * operationId="getTags",
 * tags={"Tag"},
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
 * path="/api/tags/{id}",
 * summary="Получение тэга",
 * description="Получение тэга",
 * operationId="getTag",
 * tags={"Tag"},
 * security={ {"api_key_security_example": {} }},
 * @OA\Parameter(
 *          name="id",
 *          description="tag id",
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
 */
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
