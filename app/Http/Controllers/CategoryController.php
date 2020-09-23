<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illunminate\Http\Response;
use App\Category;

class CategoryController extends Controller
{   

    public function __constructor() {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function index() {
        $categories = Category::all();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'categories' => $categories
        ]);
    }

    public function show($id) {
        $caterory = Category::find($id);

        if ( is_object($caterory) ) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'category' => $caterory
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'La Categoría no existe.' 
            );
        }

        return response()->json($data, $data['code']);
    }

    public function store(Request $request) {

        // Recoger los datos recibidos por POST
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if ( !empty($params_array) ) {
            // Validar los datos recibidos por POST
            
            $validate = \Validator::make($params_array, [
                'name' => 'required'
            ]);

            if ( !$validate->fails() ) {
                // Guardar los datos recibidos por POST
                
                $category = new Category();
                $category->name = $params_array['name'];
                $category->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'category' => $category
                ];
            } else {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha guardado la categoría.'
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No has enviado ninguna categoría.'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request) {

        // Recoger datos recibidos por POST
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if ( !empty($params_array) ) {
            // Validar datos recibidos por POST
            $validate = \Validator::make($params_array, [
                'name' => 'required'
            ]);

            // Quitar los datos que no quiero actualizar
            unset($params_array['id']);
            unset($params_array['created_at']);

            if ( !$validate->fails() ) {
                // Actualizar el registro(categoría)
                $category = Category::where('id', $id)->update($params_array);

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'category' => $params_array
                ];
            } else {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'category' => 'La información enviada es incorrecta.'
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No has enviado ninguna categoría.'
            ];
        }

        return response()->json($data, $data['code']);
    }
}
