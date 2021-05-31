<?php
class Api extends Controller {
    public function __construct()
    {
    }

    public function index() {
        if(isset($_COOKIE['reddit_token']))
            $this->view('api');
        else{
            header("Location: http://localhost/ReDat");
            exit();
        }
    }

}