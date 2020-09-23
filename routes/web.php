<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Cargando clases
use App\Http\Middleware\ApiAuthMiddleware;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-orm', 'PruebasController@testOrm');

// RUTAS DEL API

    /*Métodos de HTTP comunes
    
    *   GET: Conseguir datos o recursos
    *   POST: Guardar datos o recursos o hacer desde un formulario
    *   PUT: Actualizar datos o recursos
    *   DELETE: Eliminar datos o recursos
    
    */

    // Rutas de prueba
    //Route::get('/usuario/pruebas', 'UserController@pruebas');
    //Route::get('/categoria/pruebas', 'CategoryController@pruebas');
    //Route::get('/entrada/pruebas', 'PostContorller@pruebas');

    // Rutas del controlador de usuarios
    Route::post('api/register', 'UserController@register');
    Route::post('api/login', 'UserController@login');
    Route::put('api/user/update', 'UserController@update');
    Route::post('api/user/upload', 'UserController@upload')->middleware(ApiAuthMiddleware::class);
    Route::get('/api/user/avatar/{filename}', 'UserController@getImage');    
    Route::get('/api/user/detail/{id}', 'UserController@detail');

    // Rutas del controlador de categorías
    Route::resource('api/category', 'CategoryController');

    // Rutas del controlador de entradas
    Route::resource('api/post', 'PostController');
    Route::post('api/post/upload', 'PostController@upload');
    Route::get('api/post/category/{id}', 'PostController@getPostsByCategory');
    Route::get('api/post/user/{id}', 'PostController@getPostsByUser');
    Route::get('api/post/postTopRaking/{id}', 'PostController@getPostsTopRaking');

    // Rutas del controlador de valoración
    Route::resource('api/rating', 'RatingController');
    Route::get('api/post/rating/{id}', 'RatingController@getRatingTotalByPost');
    Route::get('api/rating/post/{id}', 'RatingController@getRatingByPost');

    // Rutas del controlador de comentario
    Route::resource('api/comment', 'CommentController');
    Route::get('api/comments/post/{id}', 'CommentController@getCommentsByPost');

    // Rutas del controlador de valoración de comentario

    Route::resource('api/commentRating', 'CommentRatingController');

    // Rutas del controlador de amigos
    Route::resource('api/friend', 'FriendController');
    Route::get('api/friend/getFriend/{id}', 'FriendController@getFriend');
    Route::get('api/friend/getFollowers/{id}', 'FriendController@getFollowersByUser');
    Route::get('api/friend/getFollowing/{id}', 'FriendController@getFollowingByUser');

    // Rutas del controlador de favoritos
    Route::resource('api/favorite', 'FavoriteController');
    Route::get('api/favorite/posts/{id}', 'FavoriteController@getPostFavoriteByUser');
