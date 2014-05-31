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
	
	public $media;
	public $contexts;
	public $enqueued_css;
	public $prepared;

	/*
	*********************************
	Setup methods
	*********************************
	*/

	function init(){
		$this->inc('acf');
		$this->inc('titan');

		$this->contexts 		= array();
		$this->media 			= array();
		$this->enqueued_css 	= '';
		$this->enqueued_js 		= '';
		$this->prepared 		= false;
		$this->block_notice 	= true;
	}

	function ready(){
		$this->setup_admin();
		add_filter('acf/load_field/name=swp_contexts', 	array($this, 'populate_context_field'));
		add_filter('acf/load_field/name=swp_media', 	array($this, 'populate_media_field'));
		add_filter('acf/load_field/name=swp_sizes', 	array($this, 'populate_size_field'));
		add_filter('gettext', 							array($this, 'set_save_button_text'), 20, 4);

		if($this->block_notice)
			add_action('wp_enqueue_scripts', array($this, 'adblock_check_js'));

		add_action('wp_footer', array($this, 'output_enqueued_css'), 999);
		add_action('wp_footer', array($this, 'output_enqueued_js'), 999);
	}

	/*
	*********************************
	Internal methods
	*********************************
	*/

	private function setup_admin(){
		require($this->dir.'/inc/spotWP_core.class.php');
		spotWP_core::init();
	}


	private function build_meta_query($size, $contexts){
		
		$mq = array();

		$mq['relation'] = 'AND';
		
		$mq[] = array(
			'key' 	=> 'swp_sizes',
			'value' => '"'.$size.'"',
			'compare' => 'LIKE'
			);

		foreach($contexts as $context){
			$mq[] = array(
				'key' => 'swp_contexts',
				'value' => '"'.$context.'"',
				'compare' => 'LIKE'
				);
		}

		return $mq;

	}


	private function prepare(){
		
		if(!$this->sizes)
			return null;

		foreach($this->sizes as $handle => $s){
			if($query = $s['query']){
				$this->enqueue_css('@media '.$query.'{.swp-'.$handle.' {display: block!important;}}');
			}
			if($size = $s['size']){
				$this->enqueue_css('.swp-'.$handle.' .swp-inner { width: '.$size[0].'px; height: '.$size[1].'px; }');
				$this->enqueue_css('.swp-'.$handle.' .swp-inner .swp-payload object, .swp-'.$handle.' .swp-payload iframe { width: '.$size[0].'px; height: '.$size[1].'px; }');
			}
			if($this->block_notice and !$this->did_block_notice){
				$this->enqueue_js("
					jQuery(document).ready(function($){
						if(!window.can_do_ads){
							$('body').prepend('<div class=\"swp-block-notice\">Appparently you are blocking ads</div>')
						}
					});
					");
				$this->did_block_notice = true;
			}
		}
	}

	private function enqueue_css($css){
		$this->enqueued_css .= $css."\n";
	}

	private function enqueue_js($js){
		$this->enqueued_js .= $js."\n";
	}

	/*
	*********************************
	Hook methods
	*********************************
	*/

	function populate_context_field($field){
		$field['choices'] = $this->contexts;
		return $field;
	}

	function populate_media_field($field){

		$choices = array();
		foreach($this->media as $handle => $med){
			$choices[$handle] = $med['title'];
		}	

		$field['choices'] = $choices;
		return $field;
	}

	function populate_size_field($field){

		$choices = array();
		foreach($this->sizes as $handle => $size){
			$choices[$handle] = $size['title'];
		}	

		$field['choices'] = $choices;
		return $field;
	}

	function set_save_button_text($trans, $text, $domain){
		global $pagenow;
		if(
			$text == 'Publish' and 
			$domain == 'default' and 
			get_post_type() == 'spotwp_ad' and 
			($pagenow == 'post.php' or $pagenow == 'post-new.php')
		){
			return 'Save';
		}
		return $trans;
	}

	function output_enqueued_css(){
		if($this->enqueued_css){
			echo "<style>".$this->enqueued_css."</style>";
		}
	}

	function output_enqueued_js(){
		if($this->enqueued_js){
			echo '<!-- spotWP enqueued js -->';
			echo '<script type="text/javascript">'.$this->enqueued_js.'</script>';
		}
	}

	function adblock_check_js(){
		wp_enqueue_script('adblock_check', $this->url.'/assets/js/ads.js');
	}

	/*
	*********************************
	Public methods
	*********************************
	*/

	function add_context($handle, $title){
		$this->contexts[$handle] = $title;
	}

	function add_size($handle, $args){

		if(is_string($args['size']))
			$args['size'] 	= array_map('intval', array_map('trim', explode('x', strtolower($args['size']))));
		
		$this->sizes[$handle] = $args;
	}

	function add_media($handle, $title, $handlers){
		$this->media[$handle] = array('title' => $title, 'handlers' => $handlers);
	}


	function ad($size, $contexts){

		if(is_string($contexts))
			$contexts = array($contexts);
		if(!is_array($contexts))
			$contexts = array();

		/* Here we prepare the media queries */
		if(!$this->prepared){
			$this->prepare();
			$this->prepared = true;
		}

		$args = array(
			'posts_per_page'	=> 1,
			'post_type' 		=> 'spotwp_ad',
			'orderby' 			=> 'rand',
			'meta_query' 		=> $this->build_meta_query($size, $contexts, $media),
			'suppress_filters'  => false
		);

		add_filter('posts_where', array($this, 'filter_ad_where'), 1, 1);
		if($ad = get_posts($args)){
			remove_filter('posts_where', array($this, 'filter_ad_where'));
			require_once($this->dir.'/inc/spotWP_ad.class.php');
			$ad = new spotWP_ad($ad[0]);
			ob_start();
			require($this->dir.'/views/ad.view.php');
			$ad = ob_get_clean();
			echo $ad;

		}
		remove_filter('posts_where', array($this, 'filter_ad_where'));

	}

	function filter_ad_where($where){
		if(strpos($where, 'spotwp') !== false){
			$parts = explode('AND', $where);
			foreach($parts as $i => $part){
				if(stripos($part, 'swp_contexts') !== false){
					$xs[] = $parts[$i].'AND'.$parts[$i+1];
					unset($parts[$i], $parts[$i+1]);
				}
			}
			if($xs){
				$parts[] = '('.implode('OR', $xs).')';
			}
			$where = ' '.implode('AND', $parts);
		}
		return $where;
	}

}


$spotwp = new spotWP();

function spotwp(){
	global $spotwp;
	return $spotwp;
}


?>