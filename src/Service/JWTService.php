<?php 

namespace App\Service;

use DateTimeImmutable;

class JWTService
{
    // on génère le token 
    /**
     * Générer JWT
     * @param array $header
     * @param array $payload
     * @param string $secret
     * @param int $validity
     * @param string
     */

    public function generate(array $header, array $payload, string $secret, int $validity = 10800): string  //10800 sec égal 3 heures de validité
    {
        if($validity > 0)
        {
            $now = new DateTimeImmutable();
            $exp = $now->getTimestamp() + $validity;  //date d'expiration
    
            $payload['iat'] = $now->getTimestamp(); //iat = issued at
            $payload['exp'] = $exp; // = expiration du token
        }


        // encodage les tokens en base64 
        $encodageHeader = base64_encode(json_encode($header));
        $encodagePayload = base64_encode(json_encode($payload));

        // on nettoie valeurs encodées (retrait des +, / et =) 
        $encodageHeader = str_replace(['+', '/', '='], ['-', '_', '' ], $encodageHeader);
        $encodagePayload = str_replace(['+', '/', '='], ['-', '_', '' ], $encodagePayload);

        //générer la signature avec un "secret"
        $secret = base64_encode($secret);

        $signature = hash_hmac('sha256', $encodageHeader . '.' . $encodagePayload, $secret, true);

        $encodageSignature = base64_encode($signature);
        $encodageSignature = str_replace(['+', '/', '='], ['-', '_', '' ], $encodageSignature);

        // création token
        $jwt = $encodageHeader . '.' . $encodagePayload . '.' . $encodageSignature;
        
        
        return $jwt;
    }

    // on vérifie que le token est valide (correctement formé) 
    public function isValid(string $token):bool
    {
        return preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/', $token
        ) === 1;
    }

    // on récupère le Header
    public function getHeader(string $token): array
    {
        // on démonte le token
        $array = explode('.', $token);

        // on décode le payload 
        $header = json_decode(base64_decode($array[0]), true);

        return $header;
    }

     // on récupère le Payload
     public function getPayload(string $token): array
     {
         // on démonte le token
         $array = explode('.', $token);
 
         // on décode le payload 
         $payload = json_decode(base64_decode($array[1]), true);
 
         return $payload;
     }

    // on vérifie si le token a expiré 
    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);

        $now = new DateTimeImmutable();

        return $payload['exp'] < $now->getTimestamp();
    }

    // on vérifie signature du token 
    public function check(string $token, string $secret)
    {
        // on récupère le header et playload 
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        // on régénère un token
        $verifToken = $this->generate($header, $payload, $secret, 0);  // 0 par rapport à la validité condition de début

        return $token === $verifToken;  // si oui, il ya meme contenu et meme signature, donc token non corrompu
    }
}