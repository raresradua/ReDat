<?php


class SignIn extends Controller {

    private $loggedIn = false;
    private $token;
    private $state;

    public function __construct() {
    }

    public function index() {
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

        if (!isset($this->state))
            $this->state = generateRandomString();

        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            header("Location: " . $redditAuthUrl);
            exit();
        }

        // Verify the user accepted logging in with reddit and no errors occured
        if (isset($_GET['error'])){
            exit();
        }

        $code = $_GET['code'];
        $this->token = substr($code, 0, -2);

        // Verify the code returned is the same with the one generated for security reasons
        $this->loggedIn = true;
        $request = new Request;
        var_dump($this->state);
        $request->setUrl(ACCESSCODE_URL);
        $request->setHttpMethod('POST');
        $request->setHeader('Authorization', BASE64_APP_CREDENTIALS);
        $request->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        $request->setBody("grant_type=authorization_code&code=" . $this->token . "&redirect_uri=" . URLROOT . '/monitor');

        $response = $request->getResponse();
        var_dump($response);
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
