<?php
namespace App\Http\Controllers\API;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Validator;

/**
 *
 *
 *
 * @OA\Post(
 * path="/api/login",
 * summary="Sign in",
 * description="Login by email, password",
 * operationId="authLogin",
 * tags={"Auth"},
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
 *       @OA\Property(property="token", type="JWT", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNjg4ZGY0ZTAyMjAxN2FlYjMzMDcwOTM3ODJlODQ4NjZhNjgzYjY5NzZmNzEzYjhiNjc5Y2NkODcxYzU1YzBiODk3ZjNiYzc4ZTc5MGNiMTQiLCJpYXQiOjE2Nzg4NjE2MDAuODA3MjExLCJuYmYiOjE2Nzg4NjE2MDAuODA3MjEzLCJleHAiOjE3MTA0ODQwMDAuODAxMzczLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.VRsx8fG95YPt5NlPGe0WI9vqwPH1evoQNP5w5edKR6hNn8BLamwQvv3yQJ3dAfSlbf6yJUztH7uD4S3olWAhTBaYTtI3b7KcsfuhtKv6-3br9KqWkaFrfDFICVLCv8s5bU9nIt4ILOoHvwN9hgtUq0OfzKSHHswcG7-QQkU5kjVy1x6W7hAtl42xmcC4ziOOf3vxot76DnwMNm1TWw8b8rw6yIA3Q1kOJnIQCR9ZW5Nq3vd0uK5upH0L1rMJQiWZvNhXMpX9Q1DSFNHv5JGBHOFRJkMTN7lio6HBLOuCYMKMZvQjuZS4VhzpiZWUaBxaRPtx2FXCXJg2-LXUUcMVfagLYd_DMwa4tvKVMUWlcFO2F8wqwG-oTt8v78ltYHdZlMwXdoQrYtT4Y2DXhqcnYxUcj91tDpLfKEvqkQy3iGZCyWn9GH1fHBcMyIpWmLqJ5RYiVH-gtnKoKNaPWCH-tMa7KibmstcaCDRI8w9Vv2_j2NUvSPOl_OpdJH5d8Gw7GnX_9FQ4cRIdGwHxe8moaxJ2e3ScfZUbNB4FTmBTyu_hkaOABF-hIw419JwzBnAAKhjcRM34jjjbSTKXbz6Jv00vsega-cq1F3PWaQtKI8yuqVdUF0Ja1_7jaw8Hv9rJXdpO9GJ3xyHOv1v62l0MKmqxkH7LMmLMlmDg5m5aJLA")
 *        )
 *     )
 * )
 * @OA\Post(
 * path="/api/register",
 * summary="Регистрация",
 * description="Регистрация по login, email, password",
 * operationId="authRegister",
 * tags={"Auth"},
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
 *    response=400,
 *    description="Ошибка валидации",
 *    @OA\JsonContent(
 *       @OA\Property(property="error", type="string", example="Unauthorised"),
 *        )
 *     ),
 * @OA\Response(
 * response=200,
 *    description="Success",
 *    @OA\JsonContent(
 *      @OA\Property(property="login", type="string", example="userName"),
 *      @OA\Property(property="token", type="JWT", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNjg4ZGY0ZTAyMjAxN2FlYjMzMDcwOTM3ODJlODQ4NjZhNjgzYjY5NzZmNzEzYjhiNjc5Y2NkODcxYzU1YzBiODk3ZjNiYzc4ZTc5MGNiMTQiLCJpYXQiOjE2Nzg4NjE2MDAuODA3MjExLCJuYmYiOjE2Nzg4NjE2MDAuODA3MjEzLCJleHAiOjE3MTA0ODQwMDAuODAxMzczLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.VRsx8fG95YPt5NlPGe0WI9vqwPH1evoQNP5w5edKR6hNn8BLamwQvv3yQJ3dAfSlbf6yJUztH7uD4S3olWAhTBaYTtI3b7KcsfuhtKv6-3br9KqWkaFrfDFICVLCv8s5bU9nIt4ILOoHvwN9hgtUq0OfzKSHHswcG7-QQkU5kjVy1x6W7hAtl42xmcC4ziOOf3vxot76DnwMNm1TWw8b8rw6yIA3Q1kOJnIQCR9ZW5Nq3vd0uK5upH0L1rMJQiWZvNhXMpX9Q1DSFNHv5JGBHOFRJkMTN7lio6HBLOuCYMKMZvQjuZS4VhzpiZWUaBxaRPtx2FXCXJg2-LXUUcMVfagLYd_DMwa4tvKVMUWlcFO2F8wqwG-oTt8v78ltYHdZlMwXdoQrYtT4Y2DXhqcnYxUcj91tDpLfKEvqkQy3iGZCyWn9GH1fHBcMyIpWmLqJ5RYiVH-gtnKoKNaPWCH-tMa7KibmstcaCDRI8w9Vv2_j2NUvSPOl_OpdJH5d8Gw7GnX_9FQ4cRIdGwHxe8moaxJ2e3ScfZUbNB4FTmBTyu_hkaOABF-hIw419JwzBnAAKhjcRM34jjjbSTKXbz6Jv00vsega-cq1F3PWaQtKI8yuqVdUF0Ja1_7jaw8Hv9rJXdpO9GJ3xyHOv1v62l0MKmqxkH7LMmLMlmDg5m5aJLA"),
 *     )
 * )
 * )
 * @OA\Post(
 * path="/api/forgot-password",
 * summary="Запрос на сброс пароля",
 * description="Запрос на отправку письма на email с ссылкой на сброс пароля",
 * operationId="authForgotPassword",
 * tags={"Auth"},
 * @OA\RequestBody(
 *    required=true,
 *    @OA\JsonContent(
 *       required={"email"},
 *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com")
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
 *    description="Ошибка валидации",
 *    @OA\JsonContent(
 *       @OA\Property(property="error", type="string", example="This password reset token is invalid.")
 *        )
 *     )
 * )
 *
 * @OA\Post(
 * path="/api/reset-password",
 * summary="Cброс пароля",
 * description="Cброс пароля после перехода по ссылке",
 * operationId="authResetPassword",
 * tags={"Auth"},
 * @OA\RequestBody(
 *    required=true,
 *    @OA\JsonContent(
 *       required={"email,token,password"},
 *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
 *       @OA\Property(property="token", type="string", format="string", example="a0e08df1c83f106ac2b6d01b0fb8fa6ee70009037624c7f397c771c30c652bda"),
 *       @OA\Property(property="password", type="string", format="password", example="PassWord12345")
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
 *    description="Ошибка валидации",
 *    @OA\JsonContent(
 *       @OA\Property(property="error", type="string", example="Unauthorised")
 *        )
 *     )
 * )
 *
 * @OA\Get(
 * path="/api/email/verify",
 * summary="Запрос на подтверждение почты",
 * description="Запрос на отправку письма на email с ссылкой на подтверждение почты",
 * operationId="authEmail",
 * tags={"Auth"},
 * security={ {"api_key_security_example": {} }},
 * @OA\Response(
 * response=200,
 *    description="Success",
 *    @OA\JsonContent(
 *      @OA\Property(property="success", type="string", example="Verification link sent")
 *     )
 * ),
 * @OA\Response(
 *    response=401,
 *    description="Unauthenticated",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string", example="Unauthenticated")
 *        )
 *     )
 * )
 * @OA\Get(
 * path="/api/email/verify/{id}/{hash}",
 * summary="Подтверждение почты",
 * description="Подтверждение почты после перехода по ссылке",
 * operationId="authEmailVerify",
 * tags={"Auth"},
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
 * @OA\Parameter(
 *          name="hash",
 *          description="token из ссылки подтверждения ",
 *          required=true,
 *          in="path",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 * @OA\Response(
 * response=200,
 *    description="Success",
 *    @OA\JsonContent(
 *      @OA\Property(property="success", type="string", example="Verified successfully")
 *     )
 * ),
 * @OA\Response(
 *    response=401,
 *    description="Unauthenticated",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string", example="Unauthenticated")
 *        )
 *     ),
 * @OA\Response(
 *    response=403,
 *    description="Email verified required",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string", example="Your email address is not verified.")
 *        )
 *     )
 * )
 *
 * @OA\Post(
 * path="/email/verify/resend",
 * summary="Повторный запрос на подтверждение почты",
 * description="Повторный запрос на отправку письма на email с ссылкой на подтверждение почты",
 * operationId="authEmailResend",
 * tags={"Auth"},
 * security={ {"api_key_security_example": {} }},
 * @OA\Response(
 * response=200,
 *    description="Success",
 *    @OA\JsonContent(
 *      @OA\Property(property="success", type="string", example="Verification link sent")
 *     )
 * ),
 * @OA\Response(
 *    response=401,
 *    description="Unauthenticated",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string", example="Unauthenticated")
 *        )
 *     )
 * )
 * @OA\Get(
 * path="/api/logout",
 * summary="Выход",
 * description="Выход",
 * operationId="authLogout",
 * tags={"Auth"},
 * security={ {"api_key_security_example": {} }},
 * @OA\Response(
 * response=200,
 *    description="Success",
 *    @OA\JsonContent(
 *      @OA\Property(property="success", type="string", example="User successfully signed out")
 *     )
 * ),
 * @OA\Response(
 *    response=401,
 *    description="Unauthenticated",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string", example="Unauthenticated")
 *        )
 *     )
 * )
 */
class AuthController extends Controller
{
    public $successStatus = 200;
    /**
     * login api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login() {
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')-> accessToken;
            return response()->json(['success' => $success], $this-> successStatus);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    public function logout(Request $request) {
        $request->user()->token()->delete();
        return response()->json(['success' => 'User successfully signed out'], $this-> successStatus);
    }
    /**
     * Register api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 400);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')-> accessToken;
        $success['login'] =  $user->login;
        event(new Registered($user));
        return response()->json(['success'=>$success], $this-> successStatus);
    }
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 400);
        }
        $email = $request->only('email');
        try {
            $status = Password::sendResetLink($email);
            switch ($status) {
                case Password::RESET_LINK_SENT:
                    return response()->json(['success'=>trans($status)], 200);
                case Password::INVALID_USER:
                    return response()->json(['error'=>trans($status)], 400);
            }
        } catch (\Swift_TransportException $ex) {
            $arr = array("status" => 400, "message" => $ex->getMessage(), "data" => []);
        } catch (Exception $ex) {
            $arr = array("status" => 400, "message" => $ex->getMessage(), "data" => []);
        }
        return response()->json($arr);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 400);
        }
        $input = $request->all();
        $status = Password::reset(
            $request->only('email', 'password', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ]);
                $user->save();
                event(new PasswordReset($user));
            }
        );
        return match ($status) {
            Password::PASSWORD_RESET => response()->json(['success'=>trans($status)], 200),
            default => response()->json(['error'=>trans($status)], 400),
        };
        }

}
