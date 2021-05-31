<?php
/*
 * Base Controller
 * Loads models and views
 */

class Controller {
    public function view($view, $data = []) {
        // Verific daca exista view-ul
        if (file_exists('../app/views/' . $view . '.php')){
            require_once '../app/views/' . $view . '.php';
        } else {
            die('View does not exist');
        }
    }
}