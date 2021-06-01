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

    public function getSubredditPosts($subreddit){
        $urlSubRel = sprintf("%s/r/%s",
        ENDPOINT_OAUTH,
        $subreddit
        );
        return Request::runCurl($urlSubRel, authMode: 'oauth', token_type: $this->token_type, access_token: $this->access_token);
    }

    /**
     * @return mixed
     */
    public function getTokenType()
    {
        return $this->token_type;
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }


}