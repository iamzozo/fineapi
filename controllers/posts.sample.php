<?php

class Posts
{
    function index()
    {
        $posts = new WP_Query(array(
            'post_type' => 'post'
        ));
        $fineapi->response($posts);
    }

    function show($id)
    {
        global $fineapi;
        $posts = new WP_Query(array(
            'post_type' => 'post',
            'p'  => $id
        ));

        $fineapi->response($posts, true);
    }

    function store() {
        global $fineapi;
        $id = wp_insert_post([
            'post_title' => 'Tesztelek',
            'post_content' => 'Tesztelek',
        ]);
        foreach($_FILES as $file) {
            $fineapi->upload($file, [
                'post_parent' => $id
            ]);
        }
    }
}
