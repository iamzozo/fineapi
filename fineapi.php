<?php

/**
* Plugin Name: Fine API
* Plugin URI: http://www.finehive.com
* Version: 1.0
* Author: Zoltan Varkonyi <iamzozo@gmail.com>
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
                    $action = 'store';
                    $input = json_decode(file_get_contents('php://input'), true);
                    break;
                case 'PUT' :
                    $action = 'update';
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

    function upload($file, $data = []) {
        if ( !function_exists( 'media_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );
        }
        $movefile = wp_handle_upload( $file, array( 'test_form' => FALSE ) );
        if ( !isset( $movefile['error'] ) ) {
            $attachment = array_merge(array(
                'post_mime_type' => $movefile['type'],
                'post_title' => '',
                'post_content' => '',
                'post_status' => 'inherit'
            ), $data);
            $attach_id = wp_insert_attachment( $attachment, $movefile['file'] );
            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
            $attach_data = wp_generate_attachment_metadata( $attach_id, $movefile['file'] );
            $attach_data['file_url'] = wp_get_attachment_image_src( $attach_id, 'thumbnail' );
            $image = wp_get_attachment_image_src( $attach_id, 'thumb');
            $file = wp_get_attachment_metadata( $attach_id );
            $file['id'] = $attach_id;
            $file['file_url'] = wp_get_attachment_url( $attach_id );
            $file['file_name'] = basename(wp_get_attachment_url( $attach_id ));
            $file['file_thumb'] = $image[0] ? $image[0] : get_template_directory_uri() . '/img/icons/default.png';
            return $file;
        }
        else {
            return $movefile['error'];
        }
    }
}

$fineapi = new Fine_API();
