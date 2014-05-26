<?php

/*
Plugin Name: spotWP
Description: Manage advertising in WordPress
Author: Troels Wilde
Version: 1.0
Text Domain: spotwp
*/

require('plugWP/init.php');

/**
* 
*/
class spotWP extends plugWP {
	
	function init(){
		$this->inc('acf');
	}

}


$spotwp = new spotWP();


?>