<?php if( voice_is_co_authors_active() && $coauthors_meta = get_coauthors() ) : ?>
	<?php foreach ($coauthors_meta as $key ) : ?>
	
		<section class="main-box mbt-border-top author-box">

			<h3 class="main-box-title"><?php echo __vce('about_author'); ?></h3>

			<div class="main-box-inside">

			<div class="data-image">
				<?php echo get_avatar( $key->user_email, 112 ); ?>
			</div>
			
			<div class="data-content">
				<h4 class="author-title"><?php echo $key->display_name; ?></h4>
				<div class="data-entry-content">
					<?php echo wpautop( $key->description ); ?>
				</div>
			</div>

			</div>

			<div class="vce-content-outside">
				<div class="data-links">
						<a href="<?php echo esc_url( get_author_posts_url($key->ID, $key->user_nicename) ); ?>" class="vce-author-link vce-button"><?php echo __vce('view_all_posts'); ?></a>
				</div>
				<div class="vce-author-links">
					<?php if (get_the_author_meta('url', $key->ID)) {?> <a href="<?php esc_url( the_author_meta('url', $key->ID) ); ?>" target="_blank" class="fa fa-link vce-author-website"></a><?php } ?>
					<?php $user_social = vce_get_social(); ?>
					<?php foreach($user_social as $soc_id => $soc_name): ?>
						<?php if($social_meta = get_the_author_meta($soc_id, $key->ID)) : ?>
						  	<?php if ($soc_id == 'twitter') {$social_meta = (strpos($social_meta, 'http') === false) ? 'https://twitter.com/' . $social_meta : $social_meta; } ?>
							<a href="<?php echo esc_url( $social_meta ); ?>" target="_blank" class="fa fa-<?php echo $soc_id; ?>"></a>
						<?php endif; ?>			
					<?php endforeach; ?>					
				</div>
			</div>

		</section>

	<?php endforeach; ?>

<?php else: ?>

	<section class="main-box mbt-border-top author-box">

		<h3 class="main-box-title"><?php echo __vce('about_author'); ?></h3>

		<div class="main-box-inside">

		<div class="data-image">
			<?php echo get_avatar( get_the_author_meta('ID'), 112 ); ?>
		</div>
		
		<div class="data-content">
			<h4 class="author-title"><?php the_author_meta('display_name'); ?></h4>
			<div class="data-entry-content">
				<?php echo wpautop(get_the_author_meta('description')); ?>
			</div>
		</div>

		</div>

		<div class="vce-content-outside">
			<div class="data-links">
					<a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" class="vce-author-link vce-button"><?php echo __vce('view_all_posts'); ?></a>
			</div>
			<div class="vce-author-links">
				<?php if (get_the_author_meta('url')) {?> <a href="<?php the_author_meta('url'); ?>" target="_blank" class="fa fa-link vce-author-website"></a><?php } ?>
				<?php $user_social = vce_get_social(); ?>			
				<?php foreach($user_social as $soc_id => $soc_name): ?>
					<?php if($social_meta = get_the_author_meta($soc_id)) : ?>
						<a href="<?php echo esc_url($social_meta ); ?>" target="_blank" class="fa fa-<?php echo $soc_id; ?>"></a>
					<?php endif; ?>			
				<?php endforeach; ?>					
			</div>
		</div>

	</section>

<?php endif; ?>