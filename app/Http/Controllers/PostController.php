<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;
use App\Helpers\JwtAuth;

class PostController extends Controller
{
    public function __constructor() {
        $this->middleware('api.auth', ['except' => [
            'index',
            'show',
            'getPostsByCategory',
            'getPostsByUser'
            ]
        ]);
    }

    public function index() {
        $posts = Post::all()->load('category');
        
        return response()->json([
            'code' => 200,
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }

    public function show($id, Request $request) {
        $post = Post::find($id)->load('category')
                               ->load('user');
        if ( is_object($post) ) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'post' => $post
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La entrada no existe.' 
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function store(Request $request) {
        
        // Recoger los datos recibidos por POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if ( !empty($params_array) ) {
            $user = $this->getIndentity($request);
            // Validar los datos recibidos por POST
            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'category_id' => 'required',
                'content' => 'required',
                'image' => 'required'
            ]);

            if ( !$validate->fails() ) {
                $post = new Post();
                $post->title = $params->title;
                $post->category_id = $params->category_id;
                $post->content = $params->content;
                $post->image = $params->image;
                $post->user_id = $user->sub;
                $post->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post
                ];
            } else {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Los datos enviados no son válidos.'
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No ha enviado ningún dato.'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request) {
        // Recoger los datos por POST
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if ( !empty($params_array) ) {
            // Validar datos
            $validate = \Validator::make($params_array, [
                'title' => 'required',
                'category_id' => 'required',
                'content' => 'required',
                'image' => 'required'
            ]);

            if ( !$validate->fails() ) {
                // Eliminar lo que no queremos actualizar
                unset($params_array['user_id']);
                unset($params_array['created_at']);

                // Conseguir usuario identificado
                $user = $this->getIndentity($request);
                
                // Conseguir el registro
                $post = Post::where('id', $id)
                    ->where('user_id', $user->sub)
                    ->first();
                
                // Actualizar el registro en concreto
                /*$where = [
                    'id' => $id,
                    'user_id' => $user->sub 
                ];*/

                if ( !empty($post) && (is_object($post)) ) {
                    $post->update($params_array);

                    $data = [
                        'code' => 200,
                        'status' => 'success',
                        'post' => $post,
                        'changes' => $params_array
                    ];
                } else {
                    $data = [
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'No se encuentra el registro'
                    ];
                }
            } else {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Los datos enviados no son válidos.'
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No ha enviado ningún dato.'
            ];
        }
        // Devolver algo
        return response()->json($data, $data['code']);
    }

    public function destroy($id, Request $request) {
        // Conseguir usuario identificado
        $user = $this->getIndentity($request);
        
        // Conseguir el registro
        $post = Post::where('id', $id)
                    ->where('user_id', $user->sub)
                    ->first();

        // Borrarlo
        if ( !empty($post) && (is_object($post)) ) {
            $post->delete();

            $data = [
                'code' => 200,
                'status' => 'success',
                'post' => $post
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El post no existe'
            ];
        }
        // Devolver algo
        return response()->json($data, $data['code']);
    }

    public function getIndentity($request) {
        
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $user = $jwtAuth->checkToken($token, true);

        return $user;
    }

    public function upload(Request $request) {
        // Recoger la imagen de la petición
        $image = $request->file('file0');

        // Validar imagen
        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);

        // Guardar la imagen
        if ( !$image || $validate->fails() ) {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir la imagen'
            ];
        } else {
            $image_name = time().$image->getClientOriginalName();

            //\Storage::disk('images')->put($image_name, \File::get($image));
            $image->move(public_path().'/images/', $image_name);

            $data = [
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            ];
        }

        // Devolver datos
        return response()->json($data, $data['code']);
    }

    public function getPostsByCategory($id) {
        // Buscar posts por categoría
        $posts = Post::where('category_id', $id)->get();

        // Devolver respuesta
        return response()->json([
            'code' => 200,
            'status' => 'success',
            'posts' => $posts
        ], 200);
    }

    public function getPostsByUser($id) {
        // Buscar posts por usuario
        $posts = Post::where('user_id', $id)->get();

        // Devolver respuesta
        return response()->json([
            'code' => 200,
            'status' => 'success',
            'posts' => $posts
        ]);
    }

    public function getPostsTopRaking($id) {
        $posts = Post::all()->load('ratings');
        $postsTop = [];

        $data = [
            'status' => 'error',
            'code' => '404',
            'message' => 'No hay artículos'
        ];

        if ( !empty($posts) ) {
            foreach($posts as $post) {
                $total = 0;

                if ( count($post->ratings ) > 0 ) {
                    
                    foreach($post->ratings as $rating) {
                        $total += $rating['value'];
                    }
                    $total = round($total / count($post->ratings));
                    $post['total'] = $total;

                    if ( empty($postsTop) || ( count($postsTop) < 5 ) ) {
                        $postsTop[] = $post;
                    } else {
                        $n = count($postsTop);
                        for ($i = 0; $i < $n; $i++) { 
                            if ( $total > $postsTop[$i]->total ) {
                                unset($postsTop[$i]);
                                $postsTop[] = $post;
                                break;   
                            }
                        }
                    }

                }
            }
            $a = [];
            $a[] = $postsTop[1];
            $a[] = $postsTop[2];
            $a[] = $postsTop[3];
            $a[] = $postsTop[4];
            $a[] = $postsTop[5];

            $data = [
                'status' => 'success',
                'code' => 200,
                'posts' => $a
            ];
        }

        return response()->json($data, $data['code']);
    }
}
