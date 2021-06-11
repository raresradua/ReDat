<?php
class User
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function userExists($token)
    {
        $this->db->query('SELECT * FROM users WHERE token=?');
        $this->db->bind(1, $token, PDO::PARAM_STR);
        $this->db->execute();
        return $this->db->rowCount() !== 0;
    }

    public function addUser($token)
    {
        $this->db->query('INSERT INTO users (token) VALUES (?)');
        $this->db->bind(1, $token, PDO::PARAM_STR);
        $this->db->execute();
    }

    public function getSubreddits($token)
    {
        $this->db->query('SELECT * FROM subreddits WHERE token=?');
        $this->db->bind(1, $token, PDO::PARAM_STR);
        return $this->db->resultSet();
    }

    public function addSubreddit($token, $value)
    {
        $this->db->query("INSERT INTO subreddits (token, subreddit) VALUES (?, ?)");
        $this->db->bind(1, $token, PDO::PARAM_STR);
        $this->db->bind(2, $value, PDO::PARAM_STR);
        $this->db->execute();
    }


    public function topPostsExist($subreddit){
        $this->db->query('SELECT * FROM subredditTopPosts WHERE subreddit=?');
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        $this->db->execute();
        return $this->db->rowCount() !== 0;
    }

    public function getTopPosts($subreddit)
    {
        $this->db->query("SELECT * FROM subredditTopPosts WHERE subreddit=?");
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        return $this->db->resultSet();
    }

    public function addTopPost($subreddit, $score, $num_comments, $permalink, $title, $author)
    {
        $this->db->query("INSERT INTO subredditTopPosts (subreddit, score, num_comments, permalink, title, author) VALUES (?, ?, ?, ?, ?, ?)");
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        $this->db->bind(2, $score, PDO::PARAM_INT);
        $this->db->bind(3, $num_comments, PDO::PARAM_INT);
        $this->db->bind(4, $permalink, PDO::PARAM_STR);
        $this->db->bind(5, $title, PDO::PARAM_STR);
        $this->db->bind(6, $author, PDO::PARAM_STR);
        $this->db->execute();
    }

    public function subredditInfoExists($subreddit){
        $this->db->query('SELECT * FROM subredditInfo WHERE subreddit=?');
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        $this->db->execute();
        return $this->db->rowCount() !== 0;
    }

    public function getSubredditInfo($subreddit)
    {
        $this->db->query("SELECT * FROM subredditInfo WHERE subreddit=?");
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        return $this->db->resultSet();
    }

    public function addSubredditInfo($subreddit, $title, $public_description, $subscribers,
                                     $active_user_count, $today_upvotes, $today_comments, $today_posts){
        $this->db->query("INSERT INTO subredditInfo (subreddit, title, public_description, subscribers, active_user_count, today_upvotes, today_comments, today_posts) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        $this->db->bind(2, $title, PDO::PARAM_STR);
        $this->db->bind(3, $public_description, PDO::PARAM_STR);
        $this->db->bind(4, $subscribers, PDO::PARAM_INT);
        $this->db->bind(5, $active_user_count, PDO::PARAM_INT);
        $this->db->bind(6, $today_upvotes, PDO::PARAM_INT);
        $this->db->bind(7, $today_comments, PDO::PARAM_INT);
        $this->db->bind(8, $today_posts, PDO::PARAM_INT);
        $this->db->execute();
    }

    public function commentsPerDayInitialized($subreddit){
        $this->db->query('SELECT * FROM commentsPerDay WHERE subreddit=?');
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        $this->db->execute();
        return $this->db->rowCount() !== 0;
    }

    public function getCommentsPerDay($subreddit){
        $this->db->query("SELECT * FROM commentsPerDay WHERE subreddit=?");
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        return $this->db->resultSet();
    }

    public function addCommentsPerDay($subreddit, $day, $numberOfComments){
        $this->db->query("INSERT INTO commentsPerDay (subreddit, day, numberOfComments) VALUES (?, ?, ?)");
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        $this->db->bind(2, $day, PDO::PARAM_STR);
        $this->db->bind(3, $numberOfComments, PDO::PARAM_INT);
        $this->db->execute();
    }

    public function postsPerDayInitialized($subreddit){
        $this->db->query('SELECT * FROM postsPerDay WHERE subreddit=?');
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        $this->db->execute();
        return $this->db->rowCount() !== 0;
    }

    public function getPostsPerDay($subreddit){
        $this->db->query("SELECT * FROM postsPerDay WHERE subreddit=?");
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        return $this->db->resultSet();
    }

    public function addPostsPerDay($subreddit, $day, $numberOfPosts){
        $this->db->query("INSERT INTO postsPerDay (subreddit, day, numberOfPosts) VALUES (?, ?, ?)");
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        $this->db->bind(2, $day, PDO::PARAM_STR);
        $this->db->bind(3, $numberOfPosts, PDO::PARAM_INT);
        $this->db->execute();
    }

    public function subredditModsInitialized($subreddit){
        $this->db->query('SELECT * FROM subredditMods WHERE subreddit=?');
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        $this->db->execute();
        return $this->db->rowCount() !== 0;
    }

    public function getSubredditMods($subreddit){
        $this->db->query("SELECT * FROM subredditMods WHERE subreddit=?");
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        return $this->db->resultSet();
    }

    public function addSubredditMod($subreddit, $name)
    {
        $this->db->query("INSERT INTO subredditMods (subreddit, name) VALUES (?, ?)");
        $this->db->bind(1, $subreddit,PDO::PARAM_STR);
        $this->db->bind(2, $name,PDO::PARAM_STR);
        $this->db->execute();
    }

    public function subredditRecentCommentsInitialized($subreddit){
        $this->db->query('SELECT * FROM subredditRecentComments WHERE subreddit=?');
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        $this->db->execute();
        return $this->db->rowCount() !== 0;
    }

    public function getSubredditRecentComments($subreddit){
        $this->db->query("SELECT * FROM subredditRecentComments WHERE subreddit=?");
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        return $this->db->resultSet();
    }

    public function addSubredditRecentComment($subreddit, $author, $created_utc, $body=""){
        $this->db->query("INSERT INTO subredditRecentComments (subreddit, author, created_utc, body) VALUES (?, ?, ?, ?)");
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        $this->db->bind(2, $author, PDO::PARAM_STR);
        $this->db->bind(3, $created_utc, PDO::PARAM_INT);
        $this->db->bind(4, $body, PDO::PARAM_STR);

        $this->db->execute();
    }

    public function subredditRecentPostsInitialized($subreddit){
        $this->db->query('SELECT * FROM subredditRecentPosts WHERE subreddit=?');
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        $this->db->execute();
        return $this->db->rowCount() !== 0;
    }

    public function getSubredditRecentPosts($subreddit){
        $this->db->query("SELECT * FROM subredditRecentPosts WHERE subreddit=?");
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        return $this->db->resultSet();
    }

    public function addSubredditRecentPost($subreddit, $author, $title, $full_link, $created_utc, $selftext){
        $this->db->query("INSERT INTO subredditRecentPosts (subreddit, author, title, full_link, created_utc, selftext) VALUES (?, ?, ?, ?, ?, ?)");
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        $this->db->bind(2, $author, PDO::PARAM_STR);
        $this->db->bind(3, $title, PDO::PARAM_STR);
        $this->db->bind(4, $full_link, PDO::PARAM_STR);
        $this->db->bind(5, $created_utc, PDO::PARAM_INT);
        $this->db->bind(6, $selftext, PDO::PARAM_STR);

        $this->db->execute();
    }
}