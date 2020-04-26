<?php $mod =  wp_parse_args( (array) $mod, vce_get_module_defaults_category() ); ?>

<?php echo vce_open_column_wrap($mod); ?>

<div id="main-box-<?php echo ($k+1); ?>" class="main-box vce-border-top <?php echo vce_get_column_class($mod); ?> <?php echo esc_attr(vce_get_module_css_class($mod)); ?>">
	<?php if(!empty($mod['title']) && empty($mod['hide_title'])): ?>
		<h3 class="main-box-title <?php echo vce_get_cat_class( $mod ); ?>"><?php echo vce_get_module_title($mod); ?></h3>
	<?php endif; ?>
	
	<div class="main-box-inside <?php echo vce_get_mainbox_class( $mod ); ?>">

			<?php $mod_cats = get_categories( array( 'include' => implode(",", $mod['cat']) ) ); ?>
	
		    <?php 
		        $new_mod_cats = array();       
		        foreach( $mod_cats as $cat){
		            if(!empty($mod['cat'])){
		                $new_mod_cats[array_search( $cat->term_id, $mod['cat'])] = $cat;
		             } else {
		                $new_mod_cats[$cat->term_id] = $cat;
		             }
		        }
		        ksort($new_mod_cats);
		    ?>
		   
	        <?php if( !empty( $new_mod_cats ) ): ?> 
	            <?php $i = 0; foreach( $new_mod_cats as $cat ): $i++; ?>
	            	
					<?php echo vce_loop_wrap_div($mod, $i, count( $new_mod_cats ) ); ?>
	                <?php include( locate_template('sections/loops/cat-layouts/content-' . $mod['layout'] . '.php') ); ?>
					
	            <?php endforeach; ?>

	            <?php if ( $i == ( count( $new_mod_cats ) ) ) : ?>
					</div>
				<?php endif; ?>

				<?php  echo vce_check_module_action($mod); ?>
	        
	        <?php endif; ?>
    </div>
</div>
<?php echo vce_close_column_wrap($modules, $k ); ?>