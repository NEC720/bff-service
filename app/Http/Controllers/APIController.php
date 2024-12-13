<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

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

            $response = $client->request($request->method(), $url, [
                'headers' => $request->headers->all(),
                'json' => $request->all(),
            ]);

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
        try {
            $response = $client->post('http://localhost:8001/api/validate-token', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
            ]);
            // dd($response);

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
            'auth' => 'http://localhost:8001/api',
            'api' => 'http://localhost:8002/api',
            'cyber' => 'http://localhost:8003/api',
        ];

        return $services[$service];
    }

}
