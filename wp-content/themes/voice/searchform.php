<form class="vce-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
	<input name="s" class="vce-search-input" size="20" type="text" value="<?php echo __vce('search_form'); ?>" onfocus="(this.value == '<?php echo __vce('search_form'); ?>') && (this.value = '')" onblur="(this.value == '') && (this.value = '<?php echo __vce('search_form'); ?>')" placeholder="<?php echo __vce('search_form'); ?>" />
	<?php if(defined('ICL_LANGUAGE_CODE')): ?>
		<input type="hidden" name="lang" value="<?php echo esc_attr(ICL_LANGUAGE_CODE); ?>">
	<?php endif; ?>
	<button type="submit" class="vce-search-submit"><i class="fa fa-search"></i></button> 
</form>