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
$knkLibrary->create_db();
//init plugin
add_action("admin_menu","addmenuPage");
add_shortcode('knk_project_grid','__getProjectGrid');
add_action('admin_enqueue_scripts', 'callback_for_setting_up_scripts');

function callback_for_setting_up_scripts() {
    wp_register_style( 'Bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' );
   wp_enqueue_style( 'Bootstrap' );
    wp_enqueue_script( 'Bootstrap', 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js', array( 'jquery' ) );
    wp_enqueue_script( 'Bootstrap min', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.j', array( 'jquery' ) );
}




/***Plugin Handle Functions***/

//Activation
register_activation_hook(__FILE__,'__activation');

//Deactivation
register_deactivation_hook(__FILE__,'');


//uninstall
register_uninstall_hook(__FILE__,'');

function __activation(){
    
}

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

function __customer_list(){
    include('knk/grid.php');
}

function __job_list(){
    include('knk/debitor.php');
}

?>