<?php
/*
require_once '../preload.php';

var_dump([
   $_COOKIE,
   $_SESSION,
   $_SERVER,
    serialize([])
]);

die();
*/

extract($_GET);
var_dump($_GET);