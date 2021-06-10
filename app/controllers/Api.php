<?php
class Api extends Controller {
    private $userModel;
    private $userToken;
    private $requests;

    public function __construct()
    {
        if(isset($_COOKIE['reddit_token'])) {
            $token_info = explode(":", $_COOKIE['reddit_token']);
            $token_type = $token_info[0];
            $access_token = $token_info[1];
            $this->userToken = $access_token;

            $this->requests = new ApiRequests($token_type, $access_token);
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

    public function r ($subreddit, $user=null) {
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        switch ($requestMethod) {
            case 'GET':
                if ($user) {
                    $response = $this->getUserStats($subreddit, $user);
                } else {
                    $response = $this->getSubredditStats($subreddit);
                }
                break;
            default:
                $response = $this->notFoundResponse();
        }
        header($response['status_code_header']);
        if ($response['body']){
            echo $response['body'];
        }
    }

    private function getUserStats($subreddit, $user){
        $commentsNo = count($this->requests->getUserComments($subreddit, $user)->data);
        $postsNo = count($this->requests->getUserPosts($subreddit, $user)->data);

        $json = array("commentsNo"=>$commentsNo, "postsNo"=>$postsNo);

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($json);
        return $response;
    }

    private function getSubredditStats($subreddit){
        $info = $this->requests->getSubredditInfo($subreddit);
        $stats = $this->requests->getNumberOfUpvotesPostsComments($subreddit);
        $json = array(
            "title"=>$info->data->title,
            "description"=>$info->data->public_description,
            "subscribers"=>$info->data->subscribers,
            "active users"=>$info->data->active_users_count,
            "upvotesToday"=>$stats['upvotes'],
            "commentsToday"=>$stats['comments'],
            "postsToday"=>$stats['posts']
        );

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($json);

        return $response;
    }

    private function notFoundResponse() {
        $response["status_code_header"] = "HTTP/1.1 404 Not Found";
        $response["body"] = null;
        return $response;
    }
}