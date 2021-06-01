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
}