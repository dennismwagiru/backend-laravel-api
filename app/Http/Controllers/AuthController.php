<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\HasApiResponse;
use App\Transformers\UserTransformer;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use HasApiResponse;

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'email|required|unique:users,email',
            'name' => 'required',
            'password' => 'required|confirmed'
        ]);

        $payload = $request->all();
        $payload['password'] = bcrypt($request->get('password'));

        $user = User::create($payload);
        return $this->respondCreated(
            fractal($user, UserTransformer::class)->toArray()
        );
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if(Auth::attempt($request->only(['email', 'password']))) {
            $user = User::where('email', $request->get('email'))->firstOrFail();
            return $this->respondSuccess(
                [
                    'token' => $user->createToken('Bearer')->plainTextToken
                ]
            );
        }

        return $this->respondError("Incorrect Credentials", statusCode: Response::HTTP_UNAUTHORIZED);
    }
}
