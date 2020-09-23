<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\CommentRating;
use App\Helpers\JwtAuth;

class CommentRatingController extends Controller
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

            // Validar datos enviados por POST
            $validate = \Validator::make($params_array, [
                'value' => 'required',
                'comment_id' => 'required'
            ]);

            if ( !$validate->fails() ) {
                $rating = new CommentRating();
                $rating->value = $params->value;
                $rating->comment_id = $params->comment_id;
                $rating->user_id = $user->sub;
                $rating->save();

                $data = [
                    'status' => 'success',
                    'code' => '200',
                    'rating' => $rating
                ];
            } else {
                $data = [
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Datos enviados incorrectamente'
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
    public function destroy($id)
    {
        //
    }

    public function getIndentity($request) {
        
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $user = $jwtAuth->checkToken($token, true);

        return $user;
    }
}
