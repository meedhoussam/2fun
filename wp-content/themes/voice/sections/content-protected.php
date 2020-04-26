<div class="main-wrapper">
    <div id="content" class="container site-content">
        <?php global $vce_sidebar_opts; ?>
	    <?php if ( $vce_sidebar_opts['use_sidebar'] == 'left' ) { get_sidebar(); } ?>
        <div id="primary" class="vce-main-content">
            <main id="main" class="main-box main-box-single">
                <article id="post-<?php the_ID(); ?>" <?php post_class('vce-page'); ?>>
                    <header class="entry-header">
                        <?php the_title( '<h1 class="entry-title entry-title-page">', '</h1>' ); ?>
                    </header>
                    <div class="entry-content page-content">
                        <?php echo get_the_password_form(); ?>
                    </div>
                </article>
            </main>
        </div>
        <?php if ( $vce_sidebar_opts['use_sidebar'] == 'right' ) { get_sidebar(); } ?>
    </div>
</div>