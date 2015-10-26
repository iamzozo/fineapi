<?php

class Posts
{
    function index()
    {
        global $fineapi;
        $posts = new WP_Query(array(
            'post_type' => 'post'
        ));
        $fineapi->response($posts);
    }

    function show($id)
    {
        $posts = new WP_Query(array(
            'post_type' => 'post',
            'p'  => $id
        ));

        $fineapi->response($posts);
    }
}
