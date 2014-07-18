<?

$classes = array();
$style = '';

if($size){
	$classes[] 	= 'swp-'.$size;
	$style 		= 'style="display:none;"';
}

?>
<div class="swp <?php echo implode(' ', $classes) ?>"<?php echo $style ?>>
	<? do_action('spotWP/before_ad', $ad, $size) ?>
	<div class="swp-box">
		<? do_action('spotWP/before_ad_inner', $ad, $size) ?>
		<div class="swp-payload">
			<? do_action('spotWP/before_ad_view', $ad, $size) ?>
			<?php require $this->dir.'/views/'.$ad->type.'.view.php'; ?>
			<? do_action('spotWP/after_ad_view', $ad, $size) ?>
		</div>
		<? do_action('spotWP/after_ad_inner', $ad, $size) ?>
	</div>
	<? do_action('spotWP/after_ad', $ad, $size) ?>
</div>