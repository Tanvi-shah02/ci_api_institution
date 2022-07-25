<?php

/**
 * CodeIgniter Common Functions Helpers
 *
 * @package CodeIgniter
 */
use Config\Services;
use CodeIgniter\I18n\Time;
use CodeIgniter\HTTP\IncomingRequest;
if(!function_exists('getRequestInput')) {

    function getRequestInput(IncomingRequest $request)
    {

        $input = $request->getPost();

        if (empty($input)) {
            $input = $request->getGet();
            if (empty($input)) {
                $input = $request->getRawInput();
                if (empty($input)) {
                    //convert request body to associative array
                    $input = json_decode($request->getBody(), true);
                }
            }
        }
        return $input;
    }
}
?>