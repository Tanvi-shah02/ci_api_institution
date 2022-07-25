<?php

namespace App\Filters;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
class JwtAuth implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {
        try {
            $authenticationHeader = $request->getServer('HTTP_AUTHORIZATION');

            helper('jwt');
            $encodedToken = getJWTFromRequest($authenticationHeader);
            if ($encodedToken == 'NOT_VALID') {
                return Services::response()
                    ->setJSON(
                        [
                            'status' => false,
                            'message' => "",
                            'errors' => array("No token found"),
                            'data' => new \stdClass(),
                        ]
                    )
                    ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            } else if ($encodedToken == 'NOT_VALID_FORMAT') {
                return Services::response()
                    ->setJSON(
                        [
                            'status' => false,
                            'message' => "",
                            'errors' => array("Token format is not valid"),
                            'data' => new \stdClass(),
                        ]
                    )
                    ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }
            $is_validate = validateJWTFromRequest($encodedToken);

            if ($is_validate == 'UNAUTHORIZED') {
                return Services::response()
                    ->setJSON(
                        [
                            'status' => false,
                            'message' => "",
                            'errors' => array("Access denied"),
                            'data' => new \stdClass(),
                        ]
                    )
                    ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }
            return $request;
        }catch (Exception $e) {
            return Services::response()
                ->setJSON(
                    [
                        'status' => false,
                        'message' => '',
                        'errors' => $e->getMessage(),
                        'data' => new \stdClass()
                    ]
                )
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }
    public function after(RequestInterface $request,
                          ResponseInterface $response,
                          $arguments = null)
    {
    }
}