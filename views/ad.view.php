<?

$classes = array();
$style = '';
if($ad->sizes){
	foreach($ad->sizes as $size){
		$classes[] = 'swp-'.$size;
		if(isset(spotwp()->sizes[$size]['query']))
			$style = ' style="display:none;"';
	}
}

if($ad->contexts){
	foreach($ad->contexts as $context){
		$classes[] = 'swp-context-'.$context;
	}
}

?>
<div class="swp <?php echo implode(' ', $classes) ?>"<?php echo $style ?>>
	<small>Annonce</small>
	<div class="swp-inner">
		<?php require $this->dir.'/views/'.$ad->type.'.view.php'; ?>
	</div>
</div>
