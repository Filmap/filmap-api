<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all()->push(['response' => true])->toJson();

        return $users;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $this->validate($request, [
        //     'name' => 'required|max:255',
        //     'email' => 'required|email|max:255|unique:users',
        //     'password' => 'required|confirmed|min:6',
        // ]);

        $validator = Validator::make($request->all(), [
            'name'     => 'required|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['response' => false, 'errors' => $validator->getMessageBag()->toArray()], 400);
        }

        $user = new User([
            'name'     => $request['name'],
            'email'    => $request['email'],
            'password' => bcrypt($request['password']),
        ]);

        $user->save();

        return response()->json(['response' => true], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        $user->response = true;

        return json_encode($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = JWTAuth::authenticate();

        if ($user->id != $id) {
            return response()->json(['response' => false, 'error' => 'Permission denied'], 403);
        }

        $this->validate($request, [
            'name'     => 'max:255',
            'email'    => 'email|max:255|unique:users',
            'password' => 'confirmed|min:6',
        ]);

        $validator = Validator::make($request->all(), [
            'name'     => 'max:255',
            'email'    => 'email|max:255|unique:users',
            'password' => 'confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['response' => false, 'errors' => $validator->getMessageBag()->toArray()], 400);
        }

        if ($request->has('name')) {
            $user->name = $request['name'];
        }
        if ($request->has('email')) {
            $user->email = $request['email'];
        }
        if ($request->has('password')) {
            $user->password = bcrypt($request['password']);
        }

        $user->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = JWTAuth::authenticate();

        $user->delete();
    }
}
