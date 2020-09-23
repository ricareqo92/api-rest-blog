<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Friend;
use App\User;
use App\Helpers\JwtAuth;

class FriendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $json = $request->input('json');
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if ( !empty($params_array) ) {
            $user = $this->getIndentity($request);
            
            $validate = \Validator::make($params_array, [
                'id' => 'required'
            ]);

            if ( !$validate->fails() ) {
                $friend = new Friend();
                $friend->user_id = $params->id;
                $friend->follower_id = $user->sub;
                $friend->save();
            }

            $data = [
                'status' => 'success',
                'code' => 200,
                'friend' => $friend
            ];
        } else {
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => $json
            ];
        }

        return response()->json($data, $data['code']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {   
        $user = $this->getIndentity($request);

        $friend = Friend::where('id', $id)
                        ->where('follower_id', $user->sub)->first();
        
        if ( !empty($friend) ) {
            $friend->delete();

            $data = [
                'status' => 'success',
                'code' => 200,
                'friend' => $friend
            ];
        } else {
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'Hubo un error'
            ];
        }
        
        return response()->json($data, $data['code']);
    }

    public function getFriend($userId, Request $request) {
        $user = $this->getIndentity($request);

        $friend = Friend::where('user_id', $userId)
                        ->where('follower_id', $user->sub)->first();
        
        if ( !empty($friend) && is_object($friend) ) {
            $data = [
                'status' => 'success',
                'code' => 200,
                'friend' => $friend
            ];
        } else {
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'Hubo un error'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function getFollowersByUser($id) {

        $followers = Friend::where('user_id', $id)->get()->load('follower');

        if ( !empty($followers) ) {
            $data = [
                'status' => 'success',
                'code' => 200,
                'followers' => $followers
            ];
        } else {
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'No hay seguidores'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function getFollowingByUser($id) {

        $following = Friend::where('follower_id', $id)->get()->load('user');

        if ( !empty($following) ) {
            $data = [
                'status' => 'success',
                'code' => 200,
                'following' => $following
            ];
        } else {
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'No hay seguidos'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function getIndentity($request) {
        
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $user = $jwtAuth->checkToken($token, true);

        return $user;
    }
}
