<?php
class Api extends Controller {
    public function __construct()
    {
    }

    public function index() {
        $this->view('api');
    }
}