<?php

class Monitor extends Controller {
    private $requests;
    private $userModel;
    private $userToken;

    public function __construct()
    {
        if(isset($_COOKIE['reddit_token'])){
            $token_info = explode(":", $_COOKIE['reddit_token']);
            $token_type = $token_info[0];
            $access_token = $token_info[1];
            $this->userToken = $access_token;

            $this->requests = new ApiRequests($token_type, $access_token);
            $this->userModel = $this->model('User');
        }
    }

    public function index() {
        if(isset($_COOKIE['reddit_token'])) {
            if (!$this->userModel->userExists($this->userToken)) {
                $this->userModel->addUser($this->userToken);
                // TODO: de repetat asta daca user-ul are mai mult de 100 de subreddit-uri
                $batch = $this->requests->getSubreddits(limit: 100);
                $childrenCount = $batch->data->dist;
                $children = $batch->data->children;

                for ($i = 0; $i < $childrenCount; $i++){
                    $display_name_prefixed = $children[$i]->data->display_name_prefixed;
                    $this->userModel->addSubreddit($this->userToken, $display_name_prefixed);
                }
            }

            $data = [
                "current_subreddit" => 'Choose a subreddit',
                "subreddits" => $this->userModel->getSubreddits($this->userToken)
            ];

            $this->view('monitor', $data);

        } else{
            header("Location: " . URLROOT);
            exit();
        }
    }

    public function r ($subreddit) {
        if (isset($_COOKIE['reddit_token'])) {
            if (!$this->userModel->userExists($this->userToken)) {
                header("Location: " . URLROOT . "/monitor");
                exit();
            } else {
                $posts = $this->requests->getMostRecentPosts($subreddit, 500);
                $comments = $this->requests->getMostRecentComments($subreddit, 500);

                $data = [
                    "current_subreddit" => $subreddit,
                    "subreddits" => $this->userModel->getSubreddits($this->userToken),
                    "posts" => $this->requests->getSubredditPosts($subreddit),
                    "about" => $this->requests->getSubredditInfo($subreddit),
                    "todayStatistics" => $this->requests->getNumberOfUpvotesPostsComments($subreddit),
                    "datasetPostsDay" => $this->requests->processDataset($posts),
                    "datasetCommentsDay" => $this->requests->processDataset($comments),
                    "moderators" => $this->requests->getModerators($subreddit),
                    "usersWithMostPosts" => $this->requests->calculateUsersWithMostPosts($posts),
                    "usersWithMostComments" => $this->requests->calculateUsersWithMostComments($comments)
                ];
                $this->view('monitor', $data);
            }
        }
    }

    public function logout(){
        if (isset($_COOKIE['reddit_token'])) {
            unset($_COOKIE['reddit_token']);
            var_dump($_COOKIE);
            setcookie('reddit_token', null, -1, '/ReDat');
            header("Location: " . URLROOT);
            exit();
        }
    }
}


