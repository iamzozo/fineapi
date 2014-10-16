<?php

class Posts
{
    function get($id = null)
    {
        global $fineapi;

        $args = array(
            'post_type' => 'post'
        );

        if ($id) $args['p'] = $id;

        $posts = new WP_Query($args);

        foreach ($posts->posts as $post) {
            $out[] = array(
                'id' => $post->ID,
                'title' => $post->post_title
            );
        }

        if ($id)
            $out = array_shift($out);

        $fineapi->response($out);

    }
}