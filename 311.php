<?php

require('QueryClass.php');

$params = [
    'lat' => array_key_exists(1, $argv) ? $argv[1] : "",
    'long' => array_key_exists(2, $argv) ? $argv[2] : "",
];

$args = [
    array_key_exists(3, $argv) ? explode(',', $argv[3][0]) : "",
];

print_r($args);
exit;

if (!empty($params)) {
    $query = new QueryClass();
    $query->queryApi($params, $args);
}