<?php
/**
 * Template Name: Modules
 */
?>
<?php get_header(); ?>

<?php if ( post_password_required() ) : ?>
    <?php get_template_part( 'sections/content-protected' ); ?>
<?php else: ?>

	<?php get_template_part( 'sections/featured-area' ); ?>

	<?php get_template_part( 'sections/ads/below-header' ); ?>

	<?php $display_content = vce_get_page_meta( get_the_ID(), 'display_content' ); ?>

	<?php if( $display_content['position'] == 'up' ): ?>
		<?php get_template_part( 'sections/content-modules-page' ); ?>
	<?php endif; ?>

	<div id="content" class="container site-content">

		<?php global $vce_sidebar_opts; ?>

		<?php if ( $vce_sidebar_opts['use_sidebar'] == 'left' ) { get_sidebar(); } ?>

		<div id="primary" class="vce-main-content">

			<?php $modules = vce_get_modules(); ?>

			<?php if( !empty( $modules ) ): ?>

				<?php foreach( $modules as $k => $mod ) : ?>

						<?php $module_template = isset( $mod['cpt']) ? 'cpt' : $mod['type']; ?>
						
						<?php include( locate_template('sections/modules/'.$module_template.'.php') ); ?>

				<?php endforeach; ?>

			<?php else: ?>
				
				<?php include( locate_template('sections/modules/empty.php') ); ?>

			<?php endif; ?>

		</div>

		<?php if ( $vce_sidebar_opts['use_sidebar'] == 'right' ) { get_sidebar(); } ?>

	</div>

	<?php if($display_content['position'] == 'down'): ?>
		<?php get_template_part( 'sections/content-modules-page' ); ?>
	<?php endif; ?>

<?php endif; ?>

<?php get_footer(); ?>