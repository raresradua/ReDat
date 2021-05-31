<?php

require_once 'Response.php';

class Request {

    private $httpMethod;
    private $url;
    private $headers = array();
    private $postVariables = array();
    private $cookies = array();
    private $body;

    public function setHttpMethod($httpMethod)
    {
        $this->httpMethod = $httpMethod;
    }
    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    public function setCookie($name, $value)
    {
        $this->cookies[$name] = $value;

        $cookieAscii = '';

        foreach ($this->cookies as $name => $value) {
            $cookieAscii .= " {$name}={$value};";
        }

        $this->setHeader('Cookie', $cookieAscii);
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function setPostVariable($name, $value)
    {
        $this->sethttpMethod('POST');
        $this->postVariables[$name] = $value;
        $this->body = http_build_query($this->postVariables);
    }

    public function getResponse()
    {
        $parameters = array(
            'http' => array(
                'method'  => $this->httpMethod,
                'content' => $this->body,
                'header'  => $this->getHeadersAsAscii(),
            ),
        );

        $stream = stream_context_create($parameters);
        $handle = @fopen($this->url, 'rb', false, $stream);

        if (!is_resource($handle)) {
            return null;
        }

        $streamMetaData = stream_get_meta_data($handle);
        $streamContents = stream_get_contents($handle);

        $headers = $streamMetaData['wrapper_data'];
        $body    = $streamContents;

        return new Response($headers, $body);
    }

    private function getHeadersAsAscii()
    {
        $headerAscii = '';

        foreach ($this->headers as $name => $value) {
            $headerAscii .= "{$name}: {$value}\r\n";
        }

        $headerAscii .= "\r\n";

        return $headerAscii;
    }

}