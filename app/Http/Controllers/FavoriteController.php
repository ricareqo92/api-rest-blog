<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Favorite;
use App\User;
use App\Helpers\JwtAuth;

class FavoriteController extends Controller
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
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if ( !empty($params_array) ) {
            $validate = \Validator::make($params_array, [
                'post_id' => 'required'
            ]);

            if ( !$validate->fails() ) {
                $user = $this->getIdentity($request);

                $favorite = new Favorite();
                $favorite->user_id = $user->sub;
                $favorite->post_id = $params->post_id;
                $favorite->save();

                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'favorite' => $favorite
                ];
            } else {
                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Hubo un error en el envió de los datos'
                ];
            }
        } else {
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'No se envió ningún dato'
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
        $user = $this->getIdentity($request);

        $favorite = Favorite::find($id);
        
        if ( !empty($favorite) ) {
            $favorite->delete();
            
            $data = [
                'status' => 'success',
                'code' => 200,
                'favorite' => $favorite
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

    public function getPostFavoriteByUser($id) {
        $favorites = Favorite::where('user_id', $id)->get()->load('post');

        foreach($favorites as $favorite) {
            $user = User::where('id', $favorite->post->user_id)->first();
            
            if ( !empty($user) ) {
                $favorite->post->user = $user;
            }
        }

        if ( !empty($favorites) ) {
            $data = [
                'status' => 'success',
                'code' => 200,
                'posts' => $favorites
            ];
        } else {
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'No existe post favorite'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function getIdentity($request) {
        
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $user = $jwtAuth->checkToken($token, true);

        return $user;
    }
}
