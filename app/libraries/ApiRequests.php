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

    public function getSubredditPosts($subreddit, $when = "top", $time = "today", $more = false){
        $urlSubRel = sprintf("%s/r/%s/%s.json?t=%s",
        ENDPOINT_OAUTH,
        $subreddit,
        $when,
        $time
        );
        $req = Request::runCurl($urlSubRel, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token);
        $subreddit_arr = array();
        $title = array();
        $author = array();
        $number_comments = array();
        $score = array();
        $permalink = array();
        $created_utc = array();

        foreach($req->data->children as $child){
            array_push($subreddit_arr, $subreddit);
            array_push($title, $child->data->title);
            array_push($author, $child->data->author);
            array_push($number_comments, $child->data->num_comments);
            array_push($score, $child->data->score);
            array_push($permalink, $child->data->permalink);
            array_push($created_utc, $child->data->created_utc);
        }
        $query_param = [
            "subreddit" => $subreddit_arr,
            "title" => $title,
            "author" => $author,
            "number_comments" => $number_comments,
            "score" => $score,
            "permalink" => $permalink,
            "created_utc" => $created_utc
        ];
        
        if($more){
            while(!empty($req)){
                $after = $req->data->after;
                $urlSubRel = sprintf("%s/r/%s/%s.json?t=%s&after=%s",
                ENDPOINT_OAUTH,
                $subreddit,
                $when,
                $time,
                $after
                );

                $req = Request::runCurl($urlSubRel, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token);
                foreach($req->data->children as $child){
                    array_push($subreddit_arr, $subreddit);
                    array_push($title, $child->data->title);
                    array_push($author, $child->data->author);
                    array_push($number_comments, $child->data->num_comments);
                    array_push($score, $child->data->score);
                    array_push($permalink, $child->data->permalink);
                    array_push($created_utc, $child->data->created_utc);
                }

                if($after == null)
                    break;
            }
            $query_param = [
                "subreddit" => $subreddit_arr,
                "title" => $title,
                "author" => $author,
                "number_comments" => $number_comments,
                "score" => $score,
                "permalink" => $permalink,
                "created_utc" => $created_utc
            ];
            return $query_param;
        }
        else
            return $query_param;
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

    public function getNumberOfCommentsAndDays($subreddit){
        $numberOfComments = array();
        $days = array();

        $urlSubRel = sprintf("%s/r/%s/top.json?limit=100&t=month",
        ENDPOINT_OAUTH,
        $subreddit,
        );

        $data = Request::runCurl($urlSubRel, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token);

        while(!empty($data)){
            $after = $data->data->after;
            foreach($data->data->children as $child){
                array_push($numberOfComments, $child->data->num_comments);
                $epoch = $child->data->created_utc;
                $dt = new DateTime("@$epoch");
                array_push($days, $dt->format('Y-m-d'));
            }
            if($after == null)
                break;

            $urlSubRel = sprintf("%s/r/%s/top.json?limit=100&t=month&after=%s",
            ENDPOINT_OAUTH,
            $subreddit,
            $after
            );

            $data = Request::runCurl($urlSubRel, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token);
        }

        $info = [
            "x" => $days,
            "y" => $numberOfComments
        ];

        return $info;
    }

    public function getPostPerDayInAMonth($subreddit){
        $numberOfPosts = array();
        $days = array();

        $urlSubRel = sprintf("%s/r/%s/new.json?limit=100",
        ENDPOINT_OAUTH,
        $subreddit
        );

        $data = Request::runCurl($urlSubRel, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token);
        $timeOneMonthAgo = time() - 2678400; //2678400 one month in unix epoch time between two months, current date and one month ago
        while(!empty($data)){
            $after = $data->data->after;
            $ok = 0;
            foreach($data->data->children as $child){
                if($child->data->created_utc < $timeOneMonthAgo){
                    $ok = 1;
                    break;
                }
                $epoch = $child->data->created_utc;
                $dt = new DateTime("@$epoch");
                if(in_array($dt->format('Y-m-d'), $days)){
                    $index = array_search($dt->format('Y-m-d'), $days);
                    $numberOfPosts[$index]+=1;
                }
                else{
                    array_push($days, $dt->format('Y-m-d'));
                    array_push($numberOfPosts, 1);
                }
            }
            if($after == null || $ok == 1)
                break;

            $urlSubRel = sprintf("%s/r/%s/new.json?limit=100&after=%s",
            ENDPOINT_OAUTH,
            $subreddit,
            $after
            );

            $data = Request::runCurl($urlSubRel, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token);
        }

        $info = [
            "x" => $days,
            "y" => $numberOfPosts
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