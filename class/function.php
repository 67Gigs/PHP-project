<?php

function verifyPassword($password) {
    if (strlen($password) > 7) {
        return true;
    } else {
        return false;
    }
}

function validateEmail($email) {
    return (filter_var($email, FILTER_VALIDATE_EMAIL));
}

?>