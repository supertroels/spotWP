<?php

/*
Plugin Name: spotWP
Description: Manage advertising in WordPress
Author: Troels Wilde
Version: 1.0
Text Domain: spotwp
*/

define('SPOTWP_DIR', dirname(__FILE__));

require('inc/spotwp.class.php');

$spotwp = new spotWP();
$spotwp->init();
?>