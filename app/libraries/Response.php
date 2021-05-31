<?php

class Response {

    private $headers = array();

    private $body;

    public function __construct(array $headers, $body)
    {
        $this->headers = $headers;
        $this->body    = $body;
    }

    public function getHeader($name)
    {
        foreach ($this->headers as $header) {
            if (strpos($header, "{$name}:") === 0) {

                $prefixLength = strlen("{$name}:") + 1;

                $suffix = substr($header, $prefixLength);
                $suffix = rtrim($suffix);

                return $suffix;
            }
        }

    }


    public function getBody()
    {
        return $this->body;
    }

}
