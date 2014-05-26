<?php

class spotwp {

	function init(){

		require_once(SPOTWP_DIR.'/inc/spotwp_admin.class.php');
		spotwp_admin::init();

	}

}

?>