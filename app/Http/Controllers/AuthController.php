<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\PasswordResetSuccess;
use App\Traits\HasApiResponse;
use App\Transformers\UserTransformer;
use Auth;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use HasApiResponse;

    /**
     * Handle New User Registration
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'email|required|unique:users,email',
            'name' => 'required',
            'password' => 'required|confirmed'
        ]);

        $payload = $request->all();
        $payload['password'] = Hash::make($request->get('password'));

        $user = User::create($payload);
        return $this->respondCreated(
            fractal($user, UserTransformer::class)->toArray()
        );
    }

    /**
     * Handle User Login
     *
     * @param Request $request
     * @return JsonResponse
     */
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

        return $this->respondError(__('auth.failed'), statusCode: Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Handle Forgot Password
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|exists:users,email'
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT)
            return $this->respondSuccess(['message' => __('passwords.sent')]);

        return $this->respondError(
            error: __('errors.validation'),
            errors: ['email' => [__($status)]]
        );
    }

    /**
     * Handle Reset Password
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed',
            'token' => 'required'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                $user->notify(new PasswordResetSuccess());
            }
        );

        if ($status === Password::PASSWORD_RESET)
            return $this->respondSuccess(['message' => __('passwords.reset')]);

        return $this->respondError(
            error: __('errors.validation'),
            errors: ['email' => [__($status)]]
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePreferences(Request $request): JsonResponse {
        $request->validate([
            'source_ids' => 'nullable|array',
            'source_ids.*' => 'exists|sources,id',
            'author_ids' => 'nullable|array',
            'author_ids.*' => 'exists|sources,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists|sources,id',
        ]);

        $user = auth('sanctum')->user();

        $user->update([
            'preferences' => json_encode($request->only(['source_ids', 'author_ids', 'category_ids']))
        ]);

        return $this->respondCreated(
            fractal($user, UserTransformer::class)->toArray()
        );
    }
}
