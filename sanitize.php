<?php

function sanitizeString($var){
    $var = strip_tags($var);
    $var = htmlentities($var);
    return $var;
}

function sanitizeMySQL($connection,$var){
    $var=$connection->real_escape_string($var);
    $var = sanitizeString($var);
    return $var;
}

// to call:  $var = sanitizeString($_POST['user input']);
// or $var= sanitizeMySQL($connection,($_POST['user input']);