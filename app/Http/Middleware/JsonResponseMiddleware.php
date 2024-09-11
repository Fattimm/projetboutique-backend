<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JsonResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!($response instanceof JsonResponse)) {
            $originalContent = $response->getOriginalContent();
            
            $data = $originalContent['data'] ?? null;
            $errors = $originalContent['errors'] ?? null;
            $message = $originalContent['message'] ?? '';
            $statusCode = $originalContent['status_code'] ?? $this->determineStatusCode($data, $errors, $message);

            
            // $statusCode = $this->determineStatusCode($data, $message);

            // Construire une réponse JSON cohérente
            $jsonResponse = [
                'status' => $statusCode,
                'data' => $data,
                'errors' => $errors,
                'message' => $message,
            ];

            // Retourner la réponse formatée en JSON
            return response()->json($jsonResponse, $statusCode);       
        }

        // Si la réponse est déjà une JsonResponse, on la renvoie telle quelle
        return $response;
    }

   // Méthode pour déterminer le statut HTTP selon le contenu de la réponse
   private function determineStatusCode($data, $errors, $message)
   {
       // Cas des erreurs (ex: erreurs de validation)
       if ($errors !== null) {
           return 422; // Erreur de validation
       }

       // Cas où il n'y a pas de données et la ressource est non trouvée
       if ($data === null && strpos($message, 'non trouvé') !== false) {
           return 404; // Ressource non trouvée
       }

       // Cas de création réussie
       if (strpos($message, 'créée') !== false || strpos($message, 'enregistrée') !== false) {
           return 201; // Ressource créée
       }

       // Cas d'une suppression réussie
       if (strpos($message, 'supprimée') !== false) {
           return 204; // Suppression réussie (No Content)
       }

       // Autres cas de succès par défaut
       return 200; // OK
   }
   
}
