<?php

require_once(dirname(__FILE__) . '/../../../config.php');

function check_created_permission() {
    global $USER;

    $admins = get_admins();
    foreach ($admins as $admin) {
        if ($USER->id == $admin->id) {
            return true;
        }
    }

    if (!user_has_role_assignment($USER->id,5)) {
        return true;
    }

    return false;
}

