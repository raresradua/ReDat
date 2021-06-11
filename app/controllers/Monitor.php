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

                if (!$this->userModel->topPostsExist($subreddit)){
                    $topPosts = $this->requests->getSubredditPosts($subreddit);
                    for ($i = 0; $i < $topPosts->data->dist; $i++){
                        $score = $topPosts->data->children[$i]->data->score;
                        $num_comments = $topPosts->data->children[$i]->data->num_comments;
                        $permalink = $topPosts->data->children[$i]->data->permalink;
                        $title = $topPosts->data->children[$i]->data->title;
                        $author = $topPosts->data->children[$i]->data->author;
                        try {
                            $this->userModel->addTopPost($subreddit, $score, $num_comments, $permalink, $title, $author);
                        } catch (Exception $e) {
                            continue;
                        }
                    }
                }

                if (!$this->userModel->subredditInfoExists($subreddit)){
                    $subredditInfo = $this->requests->getSubredditInfo($subreddit);
                    $todayStatistics = $this->requests->getSubredditStats($subreddit);
                    try {
                        $this->userModel->addSubredditInfo(
                            $subreddit,
                            $subredditInfo->data->title,
                            $subredditInfo->data->public_description,
                            $subredditInfo->data->subscribers,
                            $subredditInfo->data->active_user_count,
                            $todayStatistics['upvotes'],
                            $todayStatistics['comments'],
                            $todayStatistics['posts']
                        );
                    } catch (Exception $e) {

                    }
                }

                if (!$this->userModel->commentsPerDayInitialized($subreddit)){
                    $commentsPerDay = $this->requests->getNumberOfCommentsPerDay($subreddit);
                    for ($i = 0; $i < count($commentsPerDay['x']); $i++){
                        try {
                            $this->userModel->addCommentsPerDay($subreddit, $commentsPerDay['x'][$i], $commentsPerDay['y'][$i]);
                        } catch (Exception $e) {
                            continue;
                        }
                    }
                }

                $comments = $this->userModel->getCommentsPerDay($subreddit);
                $datasetComments = array('x'=>[], 'y'=>[]);
                foreach($comments as $comment){
                    array_push($datasetComments['x'], $comment->day);
                    array_push($datasetComments['y'], $comment->numberOfComments);
                }

                if (!$this->userModel->postsPerDayInitialized($subreddit)){
                    $postsPerDay = $this->requests->getNumberOfPostsPerDay($subreddit);
                    for ($i = 0; $i < count($postsPerDay['x']); $i++){
                        try {
                            $this->userModel->addPostsPerDay($subreddit, $postsPerDay['x'][$i], $postsPerDay['y'][$i]);
                        } catch (Exception $e) {
                            continue;
                        }
                    }
                }

                $posts = $this->userModel->getPostsPerDay($subreddit);
                $datasetPosts = array('x'=>[], 'y'=>[]);
                foreach($posts as $post){
                    array_push($datasetPosts['x'], $post->day);
                    array_push($datasetPosts['y'], $post->numberOfPosts);
                }

                if (!$this->userModel->subredditModsInitialized($subreddit)){
                    $subredditMods = $this->requests->getModerators($subreddit);
                    foreach($subredditMods->data->children as $value){
                        try {
                            $this->userModel->addSubredditMod($subreddit, $value->name);
                        } catch (Exception $e) {
                            continue;
                        }
                    }
                }

                if (!$this->userModel->subredditRecentCommentsInitialized($subreddit)){
                    $comments = $this->requests->getMostRecentComments($subreddit, 500);
                    foreach($comments as $value){
                        if (strlen($value->body) >= 255){
                            $value->body = substr($value->body, 0, 253);
                        }

                        try {
                            $this->userModel->addSubredditRecentComment($subreddit, $value->author, $value->created_utc, $value->body);
                        } catch (Exception $e) {
                            continue;
                        }
                    }
                }

                if (!$this->userModel->subredditRecentPostsInitialized($subreddit)) {
                    $posts = $this->requests->getMostRecentPosts($subreddit, 500);
                }
                foreach ($posts as $value) {
                    if (strlen($value->selftext) >= 255) {
                        $value->selftext = substr($value->selftext, 0, 253);
                    }

                    try {
                        $this->userModel->addSubredditRecentPost($subreddit, $value->author, $value->title,
                            $value->full_link, $value->created_utc, $value->selftext);
                    } catch (Exception $e) {
                        continue;
                    }
                }

                $posts = $this->userModel->getSubredditRecentPosts($subreddit);
                $comments = $this->userModel->getSubredditRecentComments($subreddit);

                $data = [
                    "current_subreddit" => $subreddit,
                    "subreddits" => $this->userModel->getSubreddits($this->userToken),
                    "topPosts" => $this->requests->getSubredditPosts($subreddit),
                    "about" => $this->userModel->getSubredditInfo($subreddit),
                    "datasetComments" => $datasetComments,
                    "datasetPosts" => $datasetPosts,
                    "moderators" => $this->userModel->getSubredditMods($subreddit),
                    "usersWithMostPosts" => $this->requests->calculateUsersWithMostPosts($posts),
                    "usersWithMostComments" => $this->requests->calculateUsersWithMostComments($comments),
                    "commonWords" => $this->requests->getCommonWords($posts, $comments)
                ];
                $this->view('monitor', $data);
            }
        }
    }

    public function logout(){
        if (isset($_COOKIE['reddit_token'])) {
            unset($_COOKIE['reddit_token']);
            var_dump($_COOKIE);
            setcookie('reddit_token', null, -1, '/');
            header("Location: " . URLROOT);
            exit();
        }
    }
}


