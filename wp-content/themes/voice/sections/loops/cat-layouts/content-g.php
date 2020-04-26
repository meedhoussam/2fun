<article id="post-<?php the_ID(); ?>" <?php post_class('vce-post vce-lay-g'); ?>>

    <div class="vce-featured-header">
        <div class="vce-featured-info">
            <h2 class="entry-title"><a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"><?php echo esc_html($cat->name); ?></a></h2>
           	<?php if($mod['display_count']): ?>
		       	<div class="entry-meta-count">
	       			<span class="meta-item"><?php echo esc_html( $cat->count ); ?> <?php echo esc_html($mod['count_label']); ?></span>
	       		</div>
			<?php endif; ?>
        </div>
        <div class="vce-featured-header-background"></div>
    </div>

	<?php global $vce_sidebar_opts; $img_size = $vce_sidebar_opts['use_sidebar'] == 'none' ? 'vce-lay-a-nosid' : 'vce-lay-a'; ?>
	<?php if($fimage = vce_get_category_featured_image($img_size, $cat->term_id)): ?>
	 	<div class="meta-image">			
			<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" title="<?php echo esc_html($cat->name); ?>">
				<?php echo $fimage; ?>
			</a>
		</div>
	<?php endif; ?>	

</article>