<?php


class ApiRequests
{
    private $token_type;
    private $access_token;

    public function __construct($token_type, $access_token)
    {
        $this->token_type = $token_type;
        $this->access_token = $access_token;
    }

    public function getSubreddits($where = "subscriber", $limit = 25, $after = null, $before = null)
    {
        $qAfter = (!empty($after)) ? "&after=" . $after : "";
        $qBefore = (!empty($before)) ? "&before=" . $before : "";

        $urlSubRel = sprintf("%s/subreddits/mine/$where?limit=%s%s%s",
            ENDPOINT_OAUTH,
            $where,
            $limit,
            $qAfter,
            $qBefore);

        return Request::runCurl($urlSubRel, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token);
    }

    public function getSubredditPosts($subreddit, $when = "top", $time = "today"){
        $urlSubRel = sprintf("%s/r/%s/%s.json?t=%s",
        ENDPOINT_OAUTH,
        $subreddit,
        $when,
        $time
        );
        return Request::runCurl($urlSubRel, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token);
    }

    public function getSubredditInfo($subreddit){
        $urlSubRel = sprintf("%s/r/%s/about.json",
        ENDPOINT_OAUTH,
        $subreddit    
        );
        return Request::runCurl($urlSubRel, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token);
    }

    public function getNumberOfUpvotesPostsComments($subreddit){
        $urlSubRel = sprintf("%s/r/%s/top.json?limit=100&t=today",
        ENDPOINT_OAUTH,
        $subreddit
        );

        $data = Request::runCurl($urlSubRel, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token);
        $countUpvotes = 0;
        $countPosts = 0;
        $countComments = 0;
        
        while(!empty($data)){
            $after = $data->data->after;
            foreach($data->data->children as $child){
                $countUpvotes += $child->data->score;
                $countComments += $child->data->num_comments;
                $countPosts += 1;
            }
            $urlSubRel = sprintf("%s/r/%s/top.json?limit=100&t=today&after=%s",
            ENDPOINT_OAUTH,
            $subreddit,
            $after
            );
            if($after == null)
                break;
            $data = Request::runCurl($urlSubRel, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token); 
        }
        
        $info = [
            "upvotes" => $countUpvotes,
            "comments" => $countComments,
            "posts" => $countPosts
        ];

        return $info;
    }

    public function getModerators($subreddit) {
        $url = sprintf("%s/r/%s/about/moderators.json",
            ENDPOINT_OAUTH,
            $subreddit
        );

        return Request::runCurl($url, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token);
    }
}