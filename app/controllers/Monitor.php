<?php
class Monitor extends Controller {
    public function __construct()
    {
    }

    public function index() {
        if(isset($_COOKIE['reddit_token']))
            $this->view('monitor');
        else{
            header("Location: http://localhost/ReDat");
            exit();
        }
    }


    public function logout(){
        if (isset($_COOKIE['reddit_token'])) {
            unset($_COOKIE['reddit_token']);
            var_dump($_COOKIE);
            setcookie('reddit_token', null, -1, '/ReDat');
            header("Location: http://localhost/ReDat/");
            exit();
        }
    }
}
