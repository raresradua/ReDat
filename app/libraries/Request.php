<?php

class Request {

    public static function runCurl($url, $postVals = null, $headers = null,
                             $auth = false, $authMode = 'basic', $token_type = null, $access_token = null){
        $ch = curl_init($url);

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10
        );

        if (!empty($_SERVER['HTTP_USER_AGENT'])){
            $options[CURLOPT_USERAGENT] = $_SERVER['HTTP_USER_AGENT'];
        }

        if ($postVals != null){
            $options[CURLOPT_POSTFIELDS] = $postVals;
            $options[CURLOPT_CUSTOMREQUEST] = "POST";
        }

        if ($authMode == 'oauth') {
            $headers = array("Authorization: {$token_type} {$access_token}");
            $options[CURLOPT_HEADER] = false;
            $options[CURLINFO_HEADER_OUT] = false;
            $options[CURLOPT_HTTPHEADER] = $headers;
        }

        if ($auth){
            $options[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
            $options[CURLOPT_USERPWD] = CLIENT_ID . ":" . CLIENT_SECRET;
            $options[CURLOPT_SSLVERSION] = 4;
            $options[CURLOPT_SSL_VERIFYPEER] = false;
            $options[CURLOPT_SSL_VERIFYHOST] = 2;
        }

        curl_setopt_array($ch, $options);
        $apiResponse = curl_exec($ch);
        $response = json_decode($apiResponse);

        //check if non-valid JSON is returned
        if ($error = json_last_error()){
            $response = $apiResponse;
        }
        curl_close($ch);

        return $response;
    }

}