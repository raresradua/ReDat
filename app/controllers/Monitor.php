<?php

class Monitor extends Controller {
    private $subreddits;
    private $requests;

    public function __construct()
    {
        if(isset($_COOKIE['reddit_token'])){
            $token_info = explode(":", $_COOKIE['reddit_token']);
            $token_type = $token_info[0];
            $access_token = $token_info[1];

            $this->requests = new ApiRequests($token_type, $access_token);
            $this->subreddits = array();
        }
    }

    public function index() {
        if(isset($_COOKIE['reddit_token'])) {

            if (count($this->subreddits) == 0) {
                $batch = $this->requests->getSubRel(limit:100);

                $childrenCount = $batch->data->dist;
                $children = $batch->data->children;

                for ($i = 0; $i < $childrenCount; $i++){
                    $title = $children[$i]->data->title;
                    $display_name_prefixed = $children[$i]->data->display_name_prefixed;
                    $subscribers = $children[$i]->data->subscribers;
                    $subreddit = new Subreddit($title, $display_name_prefixed, $subscribers);
                    array_push($this->subreddits, $subreddit);
                }

                $data = [
                    "current_subreddit" => 'Choose a subreddit',
                    "subreddits" => $this->subreddits
                ];
                $this->view('monitor', $data);
                var_dump($this->subreddits);
            }

        } else{
            header("Location: http://localhost/ReDat");
            exit();
        }
    }

    public function r ($subreddit) {
        $data = [
            "current_subreddit" => $subreddit,
            "subreddits" => $this->subreddits
        ];
        $this->view('monitor', $data);
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


