<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

//TODO: modify responses to use BaseController
//TODO: password reset and mail confirmation
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
            $token = $user->createToken('auth-token')->plainTextToken;
            $response = $this->sendResponse(['token' => $token], 'User registered sucessfully');
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
}
