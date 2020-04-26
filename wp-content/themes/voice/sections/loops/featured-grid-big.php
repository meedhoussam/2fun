<div class="vce-grid-item">

    <div class="vce-grid-text">
    <div class="vce-featured-info">
        <?php if ( vce_get_option( 'lay_fa_grid_big_cat' ) ) : ?>
            <div class="vce-featured-section">
                <?php echo vce_get_category(); ?>
            </div>
        <?php endif; ?>

        <h2 class="vce-featured-title">
            <a class="vce-featured-link-article" href="<?php echo esc_url( get_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"><?php echo vce_get_title( 'lay-fa-grid-big' ); ?></a>
        </h2>

        <?php if ( $meta = vce_get_meta_data( 'lay-fa-grid-big' ) ) : ?>
            <div class="entry-meta"><?php echo $meta; ?></div>
        <?php endif; ?>
    </div>

    <a href="<?php echo esc_url(get_permalink()); ?>" class="vce-featured-header-background"></a>

    </div>

    <?php if ( $fimage = vce_featured_image( 'vce-fa-big-grid' ) ): ?>
            <a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>">
                <?php echo $fimage; ?>
            </a>
    <?php endif; ?>



</div>
