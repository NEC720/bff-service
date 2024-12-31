<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class APIController extends Controller
{
    //
    public function proxy(Request $request, $service, $path)
    {
        $routeException = ['login', 'register', 'supervisors', 'redirect/google', 'callback/google', 'redirect/github', 'callback/github', 'redirect/linkedin', 'callback/linkedin', 'email/verify/{id}/{hash}']; // email/verify/{id}/{hash} NE MARCHE PAS !
        if (!in_array($path, $routeException)) {
            $authHeader = $request->header('Authorization');
            if ($authHeader) {
                $token = str_replace('Bearer ', '', $authHeader);
                $user = $this->validateTokenWithAuthService($token);
                // dd($user);
                // dd($user->valid, $request->path(), $service, $path);
                if (!$user->valid) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
            } else {
                return response()->json(['error' => 'Token not provided'], 401);
            }

            $client = new Client();
            $url = $this->resolveServiceUrl($service) . '/' . $path;

            $pathsForRequestWithFiles = ['print-requests'];

            if (in_array($path, $pathsForRequestWithFiles)) {
                Log::info('Request files:', $request->allFiles());
                Log::info('Request all:', $request->all());
                // Supprimer content-type header s'il existe
                $headers = collect($request->headers->all())
                    ->filter(function ($value, $key) {
                        return strtolower($key) !== 'content-type';
                    })->all();

                $response = $client->request($request->method(), $url, [
                    'headers' => $headers,
                    'multipart' => collect($request->allFiles())->map(function ($file, $key) {
                        Log::info("Processing file: " . $key, ['filename' => $file->getClientOriginalName()]);
                        return [
                            'name' => $key,
                            'contents' => fopen($file->getPathname(), 'r'),
                            'filename' => $file->getClientOriginalName()
                        ];
                    })->merge(
                        collect($request->except(array_keys($request->allFiles())))->map(function ($value, $key) {
                            Log::info("Processing field: " . $key, ['value' => $value]);
                            return [
                                'name' => $key,
                                'contents' => (string) $value  // Conversion explicite en string
                            ];
                        })
                    )->values()->all()
                ]);
            } else {
                $response = $client->request($request->method(), $url, [
                        'headers' => $request->headers->all(),
                        'json' => $request->all()
                    ]);
            }

            // $response = $client->request($request->method(), $url, [
            //     'headers' => $request->headers->all(),
            //     'json' => $request->all(), //17-12
            // ]);

            return response($response->getBody()->getContents(), $response->getStatusCode())
            ->withHeaders($response->getHeaders());

        } else {
            // Handle route exceptions here
            $client = new Client();
            $url = $this->resolveServiceUrl($service) . '/' . $path;

            $response = $client->request($request->method(), $url, [
                'headers' => $request->headers->all(),
                'json' => $request->all(),
            ]);

            return response($response->getBody()->getContents(), $response->getStatusCode())
                ->withHeaders($response->getHeaders());
            // return response($response->getBody(), $response->getStatusCode;
        }
        
        
    }

    private function validateTokenWithAuthService($token)
    {
        $client = new Client();
        $authServiceUrl = env('AUTH_SERVICE_URL') . '/validate-token';

        try {
            $response = $client->post($authServiceUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents());
            }
        } catch (\Exception $e) {
            return $e;
        }

        return null;
    }

    private function resolveServiceUrl($service)
    {
        $services = [
            'auth' => env('AUTH_SERVICE_URL'),
            'api' => env('API_SERVICE_URL'),
            'cyber' => env('CYBER_SERVICE_URL'),
            'user-service' => env('USER_SERVICE_URL')
        ];

        return $services[$service];
    }

}
