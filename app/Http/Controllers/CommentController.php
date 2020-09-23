<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Comment;
use App\Helpers\JwtAuth;
use App\User;

class CommentController extends Controller
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
        // Recoger datos enviados por POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if ( !empty($json) ) {
            $user = $this->getIndentity($request);

            // Validar datos recibidos por POST
            $validate = \Validator::make($params_array, [
                'description' => 'required',
                'post_id' => 'required'
            ]);

            if ( !$validate->fails() ) {
                $comment = new Comment();
                $comment->description = $params->description;
                $comment->post_id = $params->post_id;
                $comment->user_id = $user->sub;
                $comment->save();
                
                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'comment' => $comment
                ];
            } else {
                $data = [
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'No se ha enviado ningún dato'
                ];
            }
        } else {
            $data = [
                'status' => 'error',
                'code' => '400',
                'message' => 'No se ha enviado ningún dato'
            ];
        }

        return Response()->json($data, $data['code']);
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
    public function destroy($id, Request $request) {

        $user = $this->getIndentity($request);

        $comment = Comment::where('id', $id)
                            ->where('user_id', $user->sub)
                            ->first();
        
        if ( !empty($comment) && is_object($comment) ) {
            
            $data = array(
                'status' => 'success',
                'code' => '200',
                'comment' => $comment
            );

            $comment->delete();
        } else {
            $data = array(
                'status' => 'error',
                'code' => '400',
                'message' => 'El comentario no existe'
            );
        }
        
        return Response()->json($data, $data['code']);
    }
    
    public function getIndentity($request) {
        
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $user = $jwtAuth->checkToken($token, true);

        return $user;
    }

    public function getCommentsByPost($id) {
        $comments = Comment::where('post_id', $id)->with('commentRatings')->get();

        if ( !empty($comments) ) {
            foreach($comments as $comment) {
                $user = User::where('id', $comment->user_id)->first();
                $comment->user = $user;
            }
            
            $data = [
                'status' => 'success',
                'code' => '200',
                'comments' => $comments
            ];
        } else {
            $data = [
                'status' => 'success',
                'code' => '400',
                'message' => 'No hay ningún comentario'
            ];
        }

        return Response()->json($data, $data['code']);
    }
}
