<?php if( vce_can_display_ads() && $ad = vce_get_option('ad_above_footer') ): ?>
	<div class="vce-ad-above-footer vce-ad-container"><?php echo do_shortcode( $ad ); ?></div>
<?php endif; ?>