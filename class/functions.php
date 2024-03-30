<?php

function validatePassword($password) {
    return preg_match('/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z]).{8,}$/', $password);
}