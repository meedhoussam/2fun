<?php if ( current_user_can( 'manage_options' ) ): ?>
	<div class="main-box">
		<h3 class="main-box-title"><?php _e( 'Oooops!', THEME_SLUG ); ?></h3>
		<div class="main-box-inside">
			<p class="no-modules-msg"><?php printf( __( 'You don\'t have any modules yet. Hurry up and create your <a href="%s">first module</a>.', THEME_SLUG ), admin_url( 'post.php?post='.get_the_ID().'&action=edit#vce_hp_modules' ) ); ?></p>
		</div>
	</div>
<?php endif; ?>