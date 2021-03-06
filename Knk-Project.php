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

//KNK Library Initialisieren
$knkLibrary = new KnkLibrary();

//Admin Page Erzeugen
add_action("admin_menu","addmenuPage");

//Shortcode Erzeugen
add_shortcode('knk_project_grid','__getProjectGrid');

//Komponenten Einbinden
add_action('admin_enqueue_scripts', 'callback_for_setting_up_scripts');
add_action('wp_enqueue_scripts', 'callback_for_setting_up');
function callback_for_setting_up_scripts() {
    wp_register_style( 'Bootstrap', '/includes/css/bootstrap.min.css' );
    wp_enqueue_style( 'Bootstrap' );
    wp_enqueue_script( 'Bootstrap min', '/includesjs/bootstrap.min.js', array( 'jquery' ) );
}
function callback_for_setting_up() {
    wp_register_style( 'Bootstrap', '/includes/css/bootstrap.min.css' );
    wp_enqueue_style( 'Bootstrap' );
  
    wp_enqueue_script( 'Bootstrap min', '/includesjs/bootstrap.min.js', array( 'jquery' ) );
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

if(isset($_POST['Save'])){
    $knkLibrary->UpdateConfig($_POST['username'],
                            $_POST['password'],
                            $_POST['project_link'],
                            $_POST['participant_link']);
    add_action('admin_notices', '__notice_save');                        
}

function __notice_save(){
    echo '<br><br><div class="alert alert-success">
            <strong>Info!: </strong> Erfolgreich gespeichert.
        </div>';
}
?>