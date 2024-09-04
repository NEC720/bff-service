<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class WebProxyController extends Controller
{
    //
    public function proxy(Request $request, $service, $path)
    {
        // Vous pouvez ajouter ici des exceptions spécifiques ou d'autres traitements nécessaires.

        // Créez le client Guzzle pour envoyer la requête à un autre service.
        $client = new Client();
        $url = $this->resolveServiceUrl($service) . '/' . $path;

        // Envoyer la requête au service web et récupérer la réponse
        $response = $client->request($request->method(), $url, [
            'headers' => $request->headers->all(),
            'query' => $request->query(),
            'form_params' => $request->all(), // Pour les requêtes POST/PUT, etc.
        ]);

        return response($response->getBody()->getContents(), $response->getStatusCode())
            ->withHeaders($response->getHeaders());
    }

    private function resolveServiceUrl($service)
    {
        // Mapper les services vers les URL des services web
        $services = [
            'auth' => 'http://localhost:8001',
            'web' => 'http://localhost:8002',
        ];

        return $services[$service];
    }
}
