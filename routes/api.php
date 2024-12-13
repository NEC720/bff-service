<?php

use App\Http\Controllers\APIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return [
        'success' => true,
        'message' => "Bienvenue sur l'API du 'BFF Service DossCopy' !",
        'data' => [
            'name' => 'BFF Service',
            'description' => "API DossCopy orchestrer les requête du frontEnd",
            'version' => '1.0.0',
            'language' => app()->getLocale(),
            'supports' => [
                "contact@ittraininghub.io",
                "https://www.ittraininghub.io",
            ],
            'authors' => [
                'It Training Hub Team - CG',
            ],
            'links' => [
                [
                    'rel' => 'documentation',
                    'href' => 'https://localhost:8000/api/documentation',
                ],
                // More links...
                // For example, authentication link...
            ],
            'endpoints' => [
                [
                    'path' => '/cybers',
                    'description' => 'List all cybers',
                    'method' => 'GET',
                    'parameters' => [],
                    'response' => [
                        'code' => 200,
                        'type' => 'array',
                        'content' => [
                            'cyber_id' => 'int',
                            'name' => 'string',
                            'description' => 'string',
                        ],
                        'examples' => [
                            [
                                'cyber_id' => 1,
                                'name' => 'cyber 1',
                                'description' => 'Description du cyber 1',
                            ],
                            // More products...
                        ],
                        'error_responses' => [
                            [
                                'code' => 401,
                                'type' => 'object',
                                'content' => [
                                    'error' => 'string',
                                    'message' => 'string',
                                    'trace' => 'array',
                                ],
                            ],
                            // More error responses...
                            // Forbidden response...
                        ],
                    ],
                    'error_responses' => [
                        [
                            'code' => 404,
                            'type' => 'object',
                            'content' => [
                                'error' => 'string',
                                'message' => 'string',
                                'trace' => 'array',
                            ],
                        ],
                    ],
                ],
                // More endpoints...
                // For example, authentication middleware...
            ],
        ]


    ];
});


// Proxy routes to the other services (APIs)
Route::any('/{service}/{path}', [APIController::class, 'proxy'])->where('path', '.*');

// Proxy routes pour l'authantification
Route::post('auth/login');
Route::post('auth/register');

// // Google
// Route::get('auth/redirect/google');
// Route::get('auth/callback/google');

// // GitHub
// Route::get('auth/redirect/github');
// Route::get('auth/callback/github');

// // LinkedIn
// Route::get('auth/redirect/linkedin');
// Route::get('auth/callback/linkedin');



Route::post('auth/logout');
Route::post('auth/refresh');
Route::get('auth/me');
Route::post('auth/verifytoken');

Route::get('auth/email/verify/{id}/{hash}');

// Proxy routes pour le servive api
Route::get('api/dashboard');

Route::get('api/locations');
Route::get('cyber/cybers');

Route::get('api/users/{id}');
Route::get('api/supervisors');

// Route::post('/cyber/sendCyberAvis');
// Route::post('/cyber/allcybersfavorites');
// Route::post('/cyber/addfavorite');
// Route::post('/cyber/removefavorite');
// Route::middleware('throttle:100,1')->post('/cyber/checkfavorites');
// Route::post('/cyber/print-requests');

Route::put('auth/users/{id}');
Route::put('auth/user/update');

