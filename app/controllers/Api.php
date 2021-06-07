<?php
class Api extends Controller {
    private $userModel;
    private $userToken;

    public function __construct()
    {
        if(isset($_COOKIE['reddit_token'])) {
            $token_info = explode(":", $_COOKIE['reddit_token']);
            $access_token = $token_info[1];
            $this->userToken = $access_token;

            $this->userModel = $this->model('User');
        }
    }

    public function index() {
        if(isset($_COOKIE['reddit_token'])){

            $data = [
                "current_subreddit" => 'Choose a subreddit',
                "subreddits" => $this->userModel->getSubreddits($this->userToken)
            ];

            $this->view('api', $data);
        }
        else{
            header("Location: " . URLROOT);
            exit();
        }
    }
}