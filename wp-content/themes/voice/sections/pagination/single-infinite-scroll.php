<?php if( vce_get_option( 'single_infinite_scroll' ) && $next_link = get_previous_post_link('%link','%title',true) ) : ?>
	<nav id="vce-pagination" class="vce-infinite-scroll-single">
		<?php echo $next_link; ?>
	</nav>
<?php endif; ?>