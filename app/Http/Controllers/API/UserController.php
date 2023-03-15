<?php
namespace App\Http\Controllers\API;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;

/**
 * @OA\Post(
 * path="/api/login",
 * summary="Sign in",
 * description="Login by email, password",
 * operationId="authLogin",
 * tags={"auth"},
 * @OA\RequestBody(
 *    required=true,
 *    description="Pass user credentials",
 *    @OA\JsonContent(
 *       required={"email","password"},
 *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
 *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
 *    ),
 * ),
 * @OA\Response(
 *    response=401,
 *    description="Unauthorised",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string", example="Unauthorised")
 *        )
 *     ),
 * @OA\Response(
 *     response=200,
 *    description="Success",
 *    @OA\JsonContent(
 *       @OA\Property(property="token", type="baerer", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNjg4ZGY0ZTAyMjAxN2FlYjMzMDcwOTM3ODJlODQ4NjZhNjgzYjY5NzZmNzEzYjhiNjc5Y2NkODcxYzU1YzBiODk3ZjNiYzc4ZTc5MGNiMTQiLCJpYXQiOjE2Nzg4NjE2MDAuODA3MjExLCJuYmYiOjE2Nzg4NjE2MDAuODA3MjEzLCJleHAiOjE3MTA0ODQwMDAuODAxMzczLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.VRsx8fG95YPt5NlPGe0WI9vqwPH1evoQNP5w5edKR6hNn8BLamwQvv3yQJ3dAfSlbf6yJUztH7uD4S3olWAhTBaYTtI3b7KcsfuhtKv6-3br9KqWkaFrfDFICVLCv8s5bU9nIt4ILOoHvwN9hgtUq0OfzKSHHswcG7-QQkU5kjVy1x6W7hAtl42xmcC4ziOOf3vxot76DnwMNm1TWw8b8rw6yIA3Q1kOJnIQCR9ZW5Nq3vd0uK5upH0L1rMJQiWZvNhXMpX9Q1DSFNHv5JGBHOFRJkMTN7lio6HBLOuCYMKMZvQjuZS4VhzpiZWUaBxaRPtx2FXCXJg2-LXUUcMVfagLYd_DMwa4tvKVMUWlcFO2F8wqwG-oTt8v78ltYHdZlMwXdoQrYtT4Y2DXhqcnYxUcj91tDpLfKEvqkQy3iGZCyWn9GH1fHBcMyIpWmLqJ5RYiVH-gtnKoKNaPWCH-tMa7KibmstcaCDRI8w9Vv2_j2NUvSPOl_OpdJH5d8Gw7GnX_9FQ4cRIdGwHxe8moaxJ2e3ScfZUbNB4FTmBTyu_hkaOABF-hIw419JwzBnAAKhjcRM34jjjbSTKXbz6Jv00vsega-cq1F3PWaQtKI8yuqVdUF0Ja1_7jaw8Hv9rJXdpO9GJ3xyHOv1v62l0MKmqxkH7LMmLMlmDg5m5aJLA")
 *        )
 *     )
 * )
 * @OA\Post(
 * path="/api/register",
 * summary="Sign up",
 * description="Register by login, email, password",
 * operationId="authRegister",
 * tags={"auth"},
 * @OA\RequestBody(
 *    required=true,
 *    @OA\JsonContent(
 *       required={"login","email","password"},
 *       @OA\Property(property="login", type="string", format="string", example="userName"),
 *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
 *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
 *    ),
 * ),
 * @OA\Response(
 *    response=401,
 *    description="Validator error",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string", example="Unauthorised"),
 *        )
 *     ),
 * @OA\Response(
 * response=200,
 *    description="Success",
 *    @OA\JsonContent(
 *      @OA\Property(property="login", type="string", example="userName"),
 *      @OA\Property(property="token", type="baerer", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNjg4ZGY0ZTAyMjAxN2FlYjMzMDcwOTM3ODJlODQ4NjZhNjgzYjY5NzZmNzEzYjhiNjc5Y2NkODcxYzU1YzBiODk3ZjNiYzc4ZTc5MGNiMTQiLCJpYXQiOjE2Nzg4NjE2MDAuODA3MjExLCJuYmYiOjE2Nzg4NjE2MDAuODA3MjEzLCJleHAiOjE3MTA0ODQwMDAuODAxMzczLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.VRsx8fG95YPt5NlPGe0WI9vqwPH1evoQNP5w5edKR6hNn8BLamwQvv3yQJ3dAfSlbf6yJUztH7uD4S3olWAhTBaYTtI3b7KcsfuhtKv6-3br9KqWkaFrfDFICVLCv8s5bU9nIt4ILOoHvwN9hgtUq0OfzKSHHswcG7-QQkU5kjVy1x6W7hAtl42xmcC4ziOOf3vxot76DnwMNm1TWw8b8rw6yIA3Q1kOJnIQCR9ZW5Nq3vd0uK5upH0L1rMJQiWZvNhXMpX9Q1DSFNHv5JGBHOFRJkMTN7lio6HBLOuCYMKMZvQjuZS4VhzpiZWUaBxaRPtx2FXCXJg2-LXUUcMVfagLYd_DMwa4tvKVMUWlcFO2F8wqwG-oTt8v78ltYHdZlMwXdoQrYtT4Y2DXhqcnYxUcj91tDpLfKEvqkQy3iGZCyWn9GH1fHBcMyIpWmLqJ5RYiVH-gtnKoKNaPWCH-tMa7KibmstcaCDRI8w9Vv2_j2NUvSPOl_OpdJH5d8Gw7GnX_9FQ4cRIdGwHxe8moaxJ2e3ScfZUbNB4FTmBTyu_hkaOABF-hIw419JwzBnAAKhjcRM34jjjbSTKXbz6Jv00vsega-cq1F3PWaQtKI8yuqVdUF0Ja1_7jaw8Hv9rJXdpO9GJ3xyHOv1v62l0MKmqxkH7LMmLMlmDg5m5aJLA"),
 *     )
 * )
 * )
 * @OA\Post(
 * path="/api/details",
 * summary="Check user",
 * description="Check user auth",
 * operationId="authCheck",
 * tags={"auth"},
 * security={ {"bearer": {} }},
 * @OA\Response(
 * response=200,
 *    description="Success",
 * )
 * )
 */
class UserController extends Controller
{
    public $successStatus = 200;
    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')-> accessToken;
            return response()->json(['success' => $success], $this-> successStatus);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')-> accessToken;
        $success['login'] =  $user->login;
        return response()->json(['success'=>$success], $this-> successStatus);
    }
    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this-> successStatus);
    }
}
