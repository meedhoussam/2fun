<?php if( vce_can_display_ads() && $ad = vce_get_option('ad_below_header') ): ?>
	<div class="vce-ad-below-header vce-ad-container"><?php echo do_shortcode( $ad ); ?></div>
<?php endif; ?>