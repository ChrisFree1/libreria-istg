<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;

class FirebaseAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $authorizationHeader = $request->header('Authorization');
        
        if ($authorizationHeader && str_starts_with($authorizationHeader, 'Bearer ')) {
            $idToken = substr($authorizationHeader, 7); 
        } else {
            return response()->json(['error' => 'Token de autorización no proporcionado'], 401);
        }

        try {
            $projectId = env("FIREBASE_PROJECT_ID");
            $factory = (new Factory)
                ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
                ->withDatabaseUri(env("FIREBASE_DATABASE_URL"))
                ->withProjectId($projectId);
            $firebaseAuth = $factory->createAuth();
            
            $verifiedIdToken = $firebaseAuth->verifyIdToken($idToken);

            // Agregar el token verificado al objeto de solicitud
            $request->attributes->add(['verified_id_token' => $verifiedIdToken]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Token inválido o expirado'], 401);
        }

        return $next($request);
    }
}
