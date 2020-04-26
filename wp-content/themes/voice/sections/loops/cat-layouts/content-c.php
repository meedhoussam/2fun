<article <?php post_class( 'vce-post vce-lay-c' ); ?>>

	<?php if ( $fimage = vce_get_category_featured_image( 'vce-lay-b', $cat->term_id ) ): ?>
	 	<div class="meta-image">
			<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" title="<?php echo esc_html($cat->name); ?>">
				<?php echo $fimage; ?>
			</a>
		</div>
	<?php endif; ?>

	<header class="entry-header">
		<h2 class="entry-title h2"><a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"><?php echo esc_html($cat->name); ?></a></h2>
	</header>

	<?php if($mod['display_count']): ?>
	       <div class="entry-meta-count">
	       		<span class="meta-item"><?php echo esc_html( $cat->count ); ?> <?php echo esc_html($mod['count_label']); ?></span>
	       </div>
	<?php endif; ?>

</article>