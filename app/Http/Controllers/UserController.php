<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\User;

class UserController extends Controller
{
    public function pruebas(Request $request) {
        return "Acción de pruebas de USER-CONTROLLER";
    }

    public function register(Request $request) {

        // Recoger los datos del usuario por post
        
        $json = $request->input('json', null);
        $params = json_decode($json); // objecto

        $params_array = json_decode($json, true); // array

        // Validar datos
        
        if ( !empty($params) && !(empty($params_array)) ) {

            // Limpiar datos
            $params_array = array_map('trim', $params_array);

            // Validar datos

            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ( $validate->fails() ) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha creado',
                    'errors' => $validate->errors()
                );
            } else {
                // Validación pasada correctamente

                // Cifrar la contraseña
                $pwd = hash('sha256', $params->password);

                // Crear el usuario

                $user = new User();
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->role = 'ROLE_USER';

                // Guardar el usuario
                $user->save();

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha creado correctamente',
                    'user' => $user
                );
            }
            
        } else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Los datos enviados no son correctos',
                'errors' => $validate->errors()
            );
        }
        // Cifrar la contraseña
        
        // Comprobar si el usuario existe ya (duplicado)

        // Crear el usuario

        return response()->json($data, $data['code']);
    }

    public function login(Request $request)   {

        $jwtAuth = new \JwtAuth();

        // Recibir datos por POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        // Validar esos datos
        $validate = \Validator::make($params_array, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ( $validate->fails() ) {
            $signup = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no se ha podido identificar',
                'errors' => $validate->errors()
            );
        } else {
            // Cifrar la password
            $pwd = hash('sha256', $params->password);

            // Devolver token o datos
            $signup = $jwtAuth->signup($params->email, $pwd);
            
            if ( !empty($params->gettoken) ) {
                $signup = $jwtAuth->signup($params->email, $pwd, true);
            }
        }

        return response()->json($signup, 200);
    }

    public function update(Request $request) {

        // Comprobar si el usuario esta identificado
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

         // Recoger los datos por post
         $json = $request->input('json', null);
         $params_array = json_decode($json, true);
         
         //var_dump($checkToken);
         //die

        if ( $checkToken && !empty($params_array) ) {
            
           

            // Sacar usuario identificado
            $user = $jwtAuth->checkToken($token, true);

            // Validar datos
            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email|unique:users'.$user->sub
            ]);

            // Quitar los campos que no quiero actualizar
            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);

            // Actualizar usuario en bbdd
            $user_update = User::where('id', $user->sub)->update($params_array);

            //Devolver array con resultado
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user,
                'changes' => $params_array
            );
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no está identificado.'
            );
            
        }

        return response()->json($data, $data['code']);
    }

    public function upload(Request $request) {

        // Recoger datos de la petición
        $image = $request->file('file0');

        //Validación de imagen
        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);

        if ( !$image || $validate->fails() ) {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir imagen'
            );
        } else {
            $image_name = time().$image->getClientOriginalName();
            //Storage::disk('images')->put($image_name, \File::get($image));

            //$path = Storage::disk('public')->put('images', $request->file('file0'));
            $image->move(public_path().'/images/', $image_name);
            $data = array(
                'code' => 200,
                'status' => 'success',
                'image' => 'fsef'
            );
        }

        return response()->json($data, $data['code']);
    }

    public function getImage($filename) {
        $isset = \Storage::disk('users')->exists($filename);
        if ( $isset ) {
            $file = \Storage::disk('users')->get($filename);
            return new Response($file, 200);
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'La imagen no existe.'
            );

            return response()->json($data, $data['code']);
        }

    }

    public function detail($id) {
        $user = User::find($id);

        if ( is_object($user) ) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'user' => 'El usuario no existe.'
            );
        }

        return response()->json($data, $data['code']);
    }
}
