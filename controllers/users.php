<?php

class Users
{
    function show($id) {
        global $fineapi;
        $fineapi->response(get_user_by('id', $id));
    }
}
