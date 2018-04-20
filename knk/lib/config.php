<?php

//get ini File
$ini_array = parse_ini_file("knk_config.ini");

//set Authentification
function __authentification(){
    return $ini_array['username'].':'.$ini_array['password'];
}

?>