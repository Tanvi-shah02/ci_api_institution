<?php
/**
 * JWT Functions Helpers
 *
 * @package CodeIgniter
 */
//use App\Models\UserModel;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use CodeIgniter\HTTP\ResponseInterface;
function getJWTFromRequest($authenticationHeader)
{

    if (is_null($authenticationHeader)) { //JWT is absent
        //throw new Exception('Missing or invalid JWT in request');
        return "NOT_VALID";
    }

    $token_array = explode(' ', $authenticationHeader);

    if(count($token_array) != 2){

        return "NOT_VALID_FORMAT";
    }else {
        return $token_array[1];
    }
}

function validateJWTFromRequest($encodedToken = '')
{
    try {
        $decodedToken = JWT::decode($encodedToken, new Key(getenv('JWT_SECRET_KEY'), 'HS256'));
        return $decodedToken;
    }catch (\Exception $e) {
        return "UNAUTHORIZED";
    }

}

function getSignedJWTForUser($mobileNo = '')
{

    $issuedAtTime = time();
    $tokenTimeToLive = getenv('JWT_TIME_TO_LIVE');
    $tokenExpiration = $issuedAtTime + $tokenTimeToLive;
    $nbf = $issuedAtTime + 10;
//    $payload = [
//        'email' => $mobileNo,
//        'iat' => $issuedAtTime,
//        'exp' => $tokenExpiration,
//    ];
    $userdata['mobileNo'] = $mobileNo;
    $payload = array(
        "iat" => $issuedAtTime, // issued at
        "exp" => $tokenExpiration, // expire time in seconds
        "data" => $userdata,
    );

    $jwt = JWT::encode($payload, getenv('JWT_SECRET_KEY'), 'HS256');

    return $jwt;
}