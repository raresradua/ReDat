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

    public function getMostRecentPosts($subreddit, $size){
        $before = time();
        $oneMonthTime = 2678400;
        $posts = array();
        while (count($posts) < 1000) {
            $url = sprintf("%s/submission/?subreddit=%s&after=%d&before=%d&sort=desc&sort_type=created_utc&size=%d&fields=author,title,full_link,created_utc,full_text",
                PUSHSHIFT_API,
                $subreddit,
                $before - $oneMonthTime,
                $before,
                $size
            );
            $tmp = Request::runCurl($url);
            if (empty($tmp->data)){
                break;
            }
            $posts = array_merge($posts, $tmp->data);
            $before = end($posts)->created_utc;
        }

        return $posts;
    }

    public function getMostRecentComments($subreddit, $size){
        $before = time();
        $oneMonthTime = 2678400;
        $comments = array();
        while (count($comments) < 10000) {
            $url = sprintf("%s/comment/?subreddit=%s&after=%d&before=%d&sort=desc&sort_type=created_utc&size=%d&fields=author,title,full_link,created_utc,full_text",
                PUSHSHIFT_API,
                $subreddit,
                $before - $oneMonthTime,
                $before,
                $size
            );
            $tmp = Request::runCurl($url);
            if (empty($tmp->data)){
                break;
            }
            $comments = array_merge($comments, $tmp->data);
            $before = end($comments)->created_utc;
        }

        return $comments;
    }

    public function calculateUsersWithMostPosts($posts){
        $users = array();
        foreach($posts as $post){
            array_push($users, $post->author);
        }
        $values = array_count_values($users);
        arsort($values);
        return array_slice($values, 0, 10, true);
    }

    public function calculateUsersWithMostComments($comments){
        $users = array();
        foreach($comments as $comment){
            array_push($users, $comment->author);
        }
        $values = array_count_values($users);
        arsort($values);
        return array_slice($values, 0, 10, true);
    }

    public function processDataset($posts){
        $data = array();
        foreach($posts as $post){
            $epoch = $post->created_utc;
            $dt = new DateTime("@$epoch");
            $dt = $dt->format('Y-m-d');
            if (array_key_exists($dt, $data)){
                $data[$dt] += 1;
            } else {
                $data[$dt] = 0;
            }
        }
        return $data;
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

    public function getUserComments($subreddit, $user){
        $url = sprintf("%s/comment/?subreddit=%s&author=%s&size=500",
            PUSHSHIFT_API,
            $subreddit,
            $user
        );

        return Request::runCurl($url);
    }

    public function getUserPosts($subreddit, $user){
        $url = sprintf("%s/submission/?subreddit=%s&author=%s&size=500",
            PUSHSHIFT_API,
            $subreddit,
            $user
        );

        return Request::runCurl($url);
    }
}