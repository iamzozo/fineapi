<?php

/**
 * Plugin Name: Fine API
 * Plugin URI: http://www.finehive.com
 * Version: 1.0
 * Author: Finehive
 * Description: Simple JSON API
 */
class Fine_API
{
    function __construct()
    {
        add_action('init', function () {

            add_rewrite_rule('^api/([^/]*)/?([^/]*)', 'index.php?controller=$matches[1]&id=$matches[2]', 'top');
            add_rewrite_tag('%controller%', '([^/]*)');
            add_rewrite_tag('%id%', '([^/]*)');
            require_once __DIR__ . '/finehelper.php';
        });

        add_action('template_redirect', array(&$this, 'handle_request'));
    }

    function is_ajax()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            return true;
        }
        return false;
    }

    function handle_request()
    {
        $dir = dirname(__FILE__);
        if (get_query_var('controller') != '') {

            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST' :
                    $action = 'post';
                    $input = json_decode(file_get_contents('php://input'), true);
                    break;
                case 'PUT' :
                    $action = 'put';
                    $input = json_decode(file_get_contents('php://input'), true);
                    break;
                case 'GET' :
                    $input = get_query_var('id');
                    if(isset($input) && $input != '')
                        $action = 'show';
                    else
                        $action = 'index';
                    break;
                case 'DELETE' :
                    $action = 'delete';
                    $input = get_query_var('id');
                    break;
            }

            $controller = get_query_var('controller');

            // Check for the controller
            if (!is_file($dir . '/controllers/' . strtolower($controller) . '.php')) {
                echo 'No such controller: ' . $controller;
                exit;
            }

            require_once $dir . '/controllers/' . $controller . '.php';

            // Call the method if exists
            if (!method_exists($controller, $action)) {
                echo 'No such method: ' . $action;
                exit;
            }

            // Call the controller
            call_user_func_array(array($controller, $action), array($input));
            exit;
        }
    }

    function response($array, $one = false)
    {
        header('Content-type: application/json');
        if(isset($array->posts)) {
            if($one)
                echo json_encode(array_shift($array->posts));
            else
                echo json_encode($array->posts);

        }
        else {
            if($one)
                echo json_encode(array_shift($array));
            else
                echo json_encode($array);
        }
        exit;
    }

    function upload()
    {
        $finehelper->upload();
    }
}

$fineapi = new Fine_API();
