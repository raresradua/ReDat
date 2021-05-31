<?php


class Subreddit
{
    private $title;
    private $display_name_prefixed;
    private $subscribers;

    /**
     * Subreddit constructor.
     * @param $title
     * @param $display_name_prefixed
     * @param $subscribers
     */
    public function __construct($title, $display_name_prefixed, $subscribers)
    {
        $this->title = $title;
        $this->display_name_prefixed = $display_name_prefixed;
        $this->subscribers = $subscribers;
    }

    /**
     * @return mixed
     */
    public function getTitle(): mixed
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDisplayNamePrefixed(): mixed
    {
        return $this->display_name_prefixed;
    }

    /**
     * @param mixed $display_name_prefixed
     */
    public function setDisplayNamePrefixed($display_name_prefixed): void
    {
        $this->display_name_prefixed = $display_name_prefixed;
    }

    /**
     * @return mixed
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }

    /**
     * @param mixed $subscribers
     */
    public function setSubscribers($subscribers): void
    {
        $this->subscribers = $subscribers;
    }




}