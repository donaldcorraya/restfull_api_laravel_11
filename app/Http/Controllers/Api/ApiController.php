<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        
        return response()->json([
            'status' => true,
            'message' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        try{
            
            $validate = Validator::make($request->all(),[
                'name'  => 'required|string',
                'email'  => 'required|string|email|unique:users',
                'password'  => 'required|confirmed',
            ]);
            
            if($validate->fails()){
                return response()->json([
                    'status' => false,
                    'message' => $validate->errors()->all(),
                ]);
            }

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            return response()->json([
                'status' => true,
                'message' => "User register successfully",
            ]);

        }catch(Exception $e){
            return redirect()->back()->with('error', $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if($request->email){
            $user->update([
                'email' => $request->email,
            ]);
        }

        if($request->password){
            if(Hash::check($request->new_password, $user->password)){
                $user->update([
                    'password' => bcrypt($request->password),
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Incorrect password',
                ]);
            }
        }
        

        return response()->json([
            'status' => true,
            'message' => 'Data update successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::where('id',$id)->delete();

        if(!$user){
            return response()->json([
                'status' => false,
                'message' => 'Unable to delete',
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'User deleted',
        ]);

    }

    public function login(Request $request)
    {
        try{
            
            $validate = Validator::make($request->all(),[
                'email'  => 'required|string|email',
                'password'  => 'required',
            ]);

            if($validate->fails()){
                return response()->json([
                    'status' => false,
                    'message' => $validate->errors()->all(),
                ]);
            }
            
            $user = User::where('email', $request->email)->first();
            
            if(!empty($user)){

                if(Hash::check($request->password, $user->password)){
                    $token = $user->createToken('mytoken')->accessToken;

                    return response()->json([
                        'success' => true,
                        'message' => "Login sucessfully",
                        'token' => $token,
                    ]);
                }else{
                    return response()->json([
                        'success' => false,
                        'message' => "Password did not match",
                    ]);
                }

            }else{
                return response()->json([
                    'success' => false,
                    'message' => "Invalid email address",
                ]);
            }

        }catch(Exception $e){
            return redirect()->back()->with('error', $e);
        }
    }

    public function logout()
    {
        
        $token = auth()->user()->token();

        $token->revoke();

        return response()->json([
            'success' => true,
            'message' => "Logout successfully",
        ]);

    }
}
