<?php
class User {
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function userExists($token){
        $this->db->query('SELECT * FROM users WHERE token=?');
        $this->db->bind(1, $token, PDO::PARAM_STR);
        $this->db->execute();
        return $this->db->rowCount() !== 0;
    }

    public function addUser($token){
        $this->db->query('INSERT INTO users (token) VALUES (?)');
        $this->db->bind(1, $token, PDO::PARAM_STR);
        $this->db->execute();
    }

    public function getSubreddits($token){
        $this->db->query('SELECT * FROM subreddits WHERE token=?');
        $this->db->bind(1, $token, PDO::PARAM_STR);
        return $this->db->resultSet();
    }

    public function addSubreddit($token, $value){
        $this->db->query("INSERT INTO subreddits (token, subreddit) VALUES (?, ?)");
        $this->db->bind(1, $token, PDO::PARAM_STR);
        $this->db->bind(2, $value, PDO::PARAM_STR);
        $this->db->execute();
    }

    public function subredditInfoExists($subreddit, $getinfo = false){
        $this->db->query('SELECT * FROM information WHERE subreddit=?');
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        $this->db->execute();
        if($getinfo){
            return $this->db->resultSet();
        }
        else{
            return $this->db->rowCount() !== 0;
        }
    }

    public function subredditInfoInsert($subreddit, $title_post, $author, $num_comments, $score, $permalink, $created_utc){
        $this->db->query('INSERT INTO information (subreddit, title, author, number_comments, score, permalink, created_utc) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $this->db->bind(1, $subreddit, PDO::PARAM_STR);
        $this->db->bind(2, $title_post, PDO::PARAM_STR);
        $this->db->bind(3, $author, PDO::PARAM_STR);
        $this->db->bind(4, $num_comments, PDO::PARAM_INT);
        $this->db->bind(5, $score, PDO::PARAM_INT);
        $this->db->bind(6, $permalink, PDO::PARAM_STR);
        $this->db->bind(7, $created_utc, PDO::PARAM_INT);
        $this->db->execute();
    }
}