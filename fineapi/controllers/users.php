<?php

class Users
{
    function show($id) {
        var_dump(get_user_by('id', $id));
    }
}
