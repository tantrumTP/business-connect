<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;


class AuthController extends BaseController
{

    /**
     * Handle user register and token generation.
     *
     * This function validates the user's data, creates de user and generate an authentication token
     * It returns a JSON response indicating whether the register was successful and, if successful, 
     * includes the generated token.
     *
     * @param Request $request The HTTP request object containing the user's register data.
     * 
     * The request must include the following POST parameters:
     *      - name (string)
     *      - email (string)
     *      - password (string)
     *      - password_confirmation (string)
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing:
     *     - 'success' (bool): Indicates whether the register was successful.
     *     - 'token' (string, optional): The authentication token if register is successful.
     *     - 'error' (string, optional): An error message if login fails.
     *     - 'status_response' (int): Http code response
     */
    public function register(Request $request)
    {
        //Data validation
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);
        } catch (ValidationException $e) {
            return $this->sendError('Registration error', ['errors' => $e->errors()], 422);
        }

        //try to create user
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        } catch (Exception $e) {
            return $this->sendError('Registration error', ['error' => $e->getMessage()], 422);
        }

        if ($user) {
            try {
                $user->sendEmailVerificationNotification();
                $token = $user->createToken('auth-token')->plainTextToken;
                $response = $this->sendResponse(['token' => $token], 'User registered sucessfully');
            } catch (Exception $e) {
                $response = $this->sendError('Something has gone wrong in the registry.', ['exceptionError' => $e->getMessage()], 422);
            }
        } else {
            $response['error'] = 'Something has gone wrong in the registry.';
            $response = $this->sendError('Something has gone wrong in the registry.', [], 422);
        }

        return $response;
    }


    /**
     * Handle user login and token generation.
     *
     * This function validates the user's email and password, checks the credentials against the database,
     * and generates an authentication token if the credentials are correct. It returns a JSON response
     * indicating whether the login was successful and, if successful, includes the generated token.
     *
     * @param Request $request The HTTP request object containing the user's login credentials.
     * 
     * The request must include the following POST parameters:
     *      - email (string)
     *      - password (string)
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing:
     *     - 'success' (bool): Indicates whether the login was successful.
     *     - 'token' (string, optional): The authentication token if login is successful.
     *     - 'error' (string, optional): An error message if login fails.
     *     - 'status_response' (int): Http code response
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return $this->sendError('Login error', $e->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            $response = $this->sendError('The provided credentials are incorrect.', [], 401);
        } else {
            $token = $user->createToken('auth-token')->plainTextToken;
            $response = $this->sendResponse(['token' => $token], 'Login sucessfull');
        }

        return $response;
    }

    public function logout()
    {
        try {
            $user = $this->getUser();
            $user->tokens()->delete();
            $response = $this->sendResponse(['user' => $user->name, 'id' => $user->id], 'Session closed successfully');
        } catch (Exception $e) {
            $response = $this->sendError('Logout error', [$e->getMessage()], 422);
        }

        return $response;
    }

    public function verify(Request $request)
    {
        try {
            $user = User::findOrFail($request->route('id'));

            if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
                return $this->sendError('Verification error', ['message' => 'Invalid verification link'], 400);
            }

            if ($user->hasVerifiedEmail()) {
                return $this->sendResponse(['email' => $user->email], 'Email already verified');
            }

            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
                $response = $this->sendResponse(['email' => $user->email], 'Email successfully verified');
            }
        } catch (Exception $e) {
            $response = $this->sendError('Verification error', ['exceptionMessage' => $e->getMessage()], 404);
        }

        return $response;
    }

    public function resendVerification()
    {
        try {
            $user = $this->getUser();

            if ($user->hasVerifiedEmail()) {
                return $this->sendResponse(['email' => $user->email], 'Email already verified');
            }

            $user->sendEmailVerificationNotification();
            $response = $this->sendResponse(['email' => $user->email], 'Email successfully resend');
        } catch (Exception $e) {
            $response = $this->sendError('Resend email error', ['exceptionMessage' => $e->getMessage()], 402);
        }

        return $response;
    }

    public function sendResetLinkEmail(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

            $status = Password::sendResetLink(
                $request->only('email'),
                function ($user, $token) {
                    $frontEndUrl = env('FRONTEND_URL');
                    $resetUrl = "{$frontEndUrl}/reset-password?token={$token}&email={$user->email}";
                    // Envía un correo con $resetUrl
                    Mail::to($user->email)->send(new ResetPasswordMail($resetUrl));
                }
            );

            $response = ($status === Password::RESET_LINK_SENT)
                ? $this->sendResponse([], 'Reset link sent to your email')
                : $this->sendError('Error generating link', ['message' => 'Unable to send reset link'], 400);
        } catch (Exception $e) {
            $response = $this->sendError('Error generating link', ['exceptionMessage' => $e->getMessage()], 400);
        }

        return $response;
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|confirmed|min:8',
            ]);

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));
                    $user->save();
                    event(new PasswordReset($user));
                }
            );

            $response = ($status === Password::PASSWORD_RESET)
                ? $this->sendResponse([], 'Password reset successfully')
                : $this->sendError('Error resetting password', ['message' => 'Unable to reset password'], 400);
        } catch (Exception $e) {
            $response = $this->sendError('Error resetting password', ['exceptionMessage' => $e->getMessage()], 400);
        }

        return $response;
    }
}
