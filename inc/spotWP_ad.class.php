<?php

class spotWP_ad {

	protected $cached_fields;

	/**
	 *
	 * @return void
	**/
	function __construct($ad){

		/* Cast a numeric string as integer */
		if(is_numeric($ad)){
			$ad = (int)$ad;
		}

		/* Initiate based on $ad variable type */
		if(is_int($ad)){
			$this->ID 		= $ad;
			$this->object 	= spotwp()->get($ad);
		}
		elseif(is_string($ad)){
			if($this->object 	= spotwp()->get($ad)){
				$this->ID = $this->object->ID;
			}

		}
		elseif(is_object($ad) and $ad->post_type == 'spotwp_ad'){
			$this->ID 		= $ad->ID;
			$this->object 	= $ad;
		}

	}


	/**
	 * getter
	 *
	 * @return void
	 **/
	function __get($var){
		if(isset($this->object->$var)){
			return $this->object->$var;
		}
		elseif($value = $this->cached_fields['swp_'.$var]){
			return $value;
		}
		elseif($value = get_field('swp_'.$var, $this->ID)){
			$this->cached_fields['swp_'.$var] = $value;
			return $value;
		}
		return false;
	}

}


?>