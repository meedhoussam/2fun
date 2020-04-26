<?php $mod =  wp_parse_args( (array) $mod, vce_get_module_defaults_text() ); ?>
<?php echo vce_open_column_wrap($mod); ?>
	<div id="main-box-<?php echo ($k+1); ?>" class="main-box vce-border-top vce-blank-module <?php echo vce_get_column_class($mod); ?> <?php echo esc_attr(vce_get_module_css_class($mod)); ?>">
		<?php if(!empty($mod['title']) && empty($mod['hide_title'])): ?>
			<h3 class="main-box-title <?php echo vce_get_cat_class( $mod ); ?>"><?php echo vce_get_module_title($mod); ?></h3>
		<?php endif; ?>
			
			<div class="main-box-inside <?php echo vce_get_mainbox_class( $mod ); ?>">
				<?php echo do_shortcode( wpautop($mod['content'])); ?>
			</div>
	</div>
<?php echo vce_close_column_wrap($modules, $k ); ?>