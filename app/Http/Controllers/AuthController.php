<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
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
        $response = [
            'success' => false,
        ];

        //Data validation
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);
        } catch (ValidationException $e){
            $response['error'] = $e->errors();
            $response['status_response'] = 422;
            return response()->json($response, $response['status_response']);
        }

        //try to create user
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        } catch (Exception $e){
            $response['error'] = $e->getMessage();
            $response['status_response'] = 422;
            return response()->json($response, $response['status_response']);
        }

        if ($user) {
            $response['success'] = true;
            $response['token'] = $user->createToken('auth-token')->plainTextToken;
        } else {
            $response['error'] = 'Something has gone wrong in the registry.';
        }

        $response['status_response'] = 200;

        return response()->json($response, $response['status_response']);
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
        $response = [
            'success' => false,
        ];

        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            $response['error'] = $e->errors();
            $response['status_response'] = 422;
            return response()->json($response, $response['status_response']);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            $response['error'] = 'The provided credentials are incorrect.';
        } else {
            $response['success'] = true;
            $response['token'] = $user->createToken('auth-token')->plainTextToken;
        }

        $response['status_response'] = 200;


        return response()->json($response, $response['status_response']);
    }

    public function logout(Request $request)
    {
        $response = [
            'success' => false
        ];

        try {
            $request->user()->tokens()->delete();
            $response['success'] = true;
        } catch (Exception $e) {
            $response['error'] = $e->getMessage();
        }
    

        return response()->json($response, 200);
    }
}
