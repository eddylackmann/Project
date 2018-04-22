<?php

/** 
* @package KnkProjectPlugin
*/

/*
plugin name: KnkProject 
plugin URI: http://knkverlagssoftware.com/
Description: Connect your website with Knkverlag and Read out Publishing Projects
Author: Eddy Lackmann
Author URI:http://knkverlagssoftware.com/ 
Version:0.1
*/

if(! defined('ABSPATH')){
    die;
}

//import Knk Libraries
require_once('knk/index.php');

//Init KNK Library
$knkLibrary = new KnkLibrary();

//init plugin
add_action("admin_menu","addmenuPage");

add_shortcode('knk_project_grid','__getProjectGrid');
add_action('admin_enqueue_scripts', 'callback_for_setting_up_scripts');
add_action('wp_enqueue_scripts', 'callback_for_setting_up');
function callback_for_setting_up_scripts() {
    wp_register_style( 'Bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' );
    wp_enqueue_style( 'Bootstrap' );
  
    wp_enqueue_script( 'Bootstrap min', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array( 'jquery' ) );
}
function callback_for_setting_up() {
    wp_register_style( 'Bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' );
    wp_enqueue_style( 'Bootstrap' );
  
    wp_enqueue_script( 'Bootstrap min', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js', array( 'jquery' ) );
}




/***Plugin Handle Functions***/

//Uninstall


//Custom Functions
function addmenuPage(){
    add_menu_page("Knk Project Admin",
                   "Knk Project Admin",
                   4,
                   "Knk Project Admin",
                   "__Knkadmin");
    
    
}
 
 

function __getProjectGrid(){
    include('knk/grid.php');
}
function __knkadmin(){
   include('admin.php');
}


?>