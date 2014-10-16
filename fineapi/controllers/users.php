<?php

class Users
{
    function get($id) {
        var_dump(get_user_by('id', $id));
    }
}