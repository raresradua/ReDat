<?php

class SignIn extends Controller {

    public function __construct() {
    }

    public function index() {
        if (isset($_COOKIE['reddit_token'])) {
            header("Location: " . URLROOT . "/monitor");
            exit();
        } else {
            if (isset($_GET['code'])) {
                $code = $_GET["code"];
                $postvals = sprintf("grant_type=authorization_code&code=%s&redirect_uri=%s",
                    $code,
                    URLROOT
                );
                $token = Request::runCurl(OAUTH_TOKEN, $postvals, null, true);

                if (isset($token->access_token)) {
                    $access_token = $token->access_token;
                    $token_type = $token->token_type;

                    $cookie_time = 60 * 59 + time();
                    setcookie('reddit_token', "{$token_type}:{$access_token}", $cookie_time);

                    header("Location: " . URLROOT . "/monitor");
                    exit();
                }
            } else {
                $redditAuthUrl = sprintf("%s?client_id=%s&response_type=%s&state=%s&redirect_uri=%s&duration=%s&scope=%s",
                    OAUTH_AUTHORIZE,
                    CLIENT_ID,
                    'code',
                    rand(),
                    URLROOT,
                    'permanent',
                    SCOPES
                );

                $data = [
                    'authUrl' => $redditAuthUrl
                ];

                $this->view('sign_in', $data);
            }
        }
    }
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
