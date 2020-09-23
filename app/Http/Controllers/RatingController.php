<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Rating;
use App\Helpers\JwtAuth;

class RatingController extends Controller
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
        // Obtener datos recibibos por POST
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if ( !empty($params_array) ) {
            // Obtener usuario identificado
            $user = $this->getIndentity($request);

            // Validar datos recibidos
            $validate = \Validator::make($params_array, [
                'value' => 'required',
                'post_id' => 'required'
            ]);

            if ( !$validate->fails() ) {
                $rating = new Rating();
                $rating->value = $params_array['value'];
                $rating->post_id = $params_array['post_id'];
                $rating->user_id = $user->sub;
                $rating->save();

                $data = [
                    'status' => 'success',
                    'code' => '200',
                    'message' => 'La valoración se ha guardado satifactoriamente'
                ];
            } else {
                $data = [
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Los datos enviados no son válidos'
                ];
            }
        } else {
            $data = [
                'status' => 'error',
                'code' => '400',
                'message' => 'No ha enviado ningún dato'
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

    public function getRatingTotalByPost($id) {

        $ratings = Rating::where('post_id', $id)->get();
        $total = 0;

        if ( !empty($ratings) ) {

            foreach($ratings as $rating) {
                $total += $rating['value'];
            }

            $total = round($total / count($ratings));

            $data = [
                'status' => 'success',
                'code' => '200',
                'total' => $total
            ];
        } else {
            $data = [
                'status' => 'error',
                'code' => '404',
                'message' => 'No hay ninguna valoración'
            ];
        }

        return Response()->json($data, $data['code']);
    }

    public function getRatingByPost(Request $request, $id) {
        // Obtener datos de usuario logueado
        $user = $this->getIndentity($request);

        $rating = Rating::where('post_id', $id)
                        ->where('user_id', $user->sub)->first();
        
        if ( empty($rating) ) {
            $data = [
                'status' => 'error',
                'code' => '404',
                'message' => 'No hay valoración del usuario'
            ];
        } else {
            $data = [
                'status' => 'success',
                'code' => '200',
                'rating' => $rating
            ];
        }

        return Response()->json($data, $data['code']);
    }
}
