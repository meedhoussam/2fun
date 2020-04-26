<?php
$share = array();

$title = vce_esc_text( wp_strip_all_tags( get_the_title() ));
$url = vce_esc_url(get_the_permalink());

$share['facebook'] = '<a class="fa fa-facebook" href="javascript:void(0);" data-url="http://www.facebook.com/sharer/sharer.php?u='.$url.'&amp;t='.$title.'"></a>';
$share['twitter'] = '<a class="fa fa-twitter" href="javascript:void(0);" data-url="http://twitter.com/intent/tweet?url='.$url.'&amp;text='.$title.'"></a>';
$share['gplus'] = '<a class="fa fa-google-plus" href="javascript:void(0);" data-url="https://plus.google.com/share?url='.$url.'"></a>';
$pin_img = has_post_thumbnail() ? wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' ) : '';
$pin_img = isset( $pin_img[0] ) ? $pin_img[0] : '';
$share['pinterest'] = '<a class="fa fa-pinterest" href="javascript:void(0);" data-url="http://pinterest.com/pin/create/button/?url='.$url.'&amp;media='.urlencode( $pin_img ).'&amp;description='.$title.'"></a>';
$share['linkedin'] = '<a class="fa fa-linkedin" href="javascript:void(0);" data-url="http://www.linkedin.com/shareArticle?mini=true&amp;url='.$url.'&amp;title='.$title.'"></a>';
$share['reddit'] = '<a href="javascript:void(0);"  class="fa fa-reddit-alien" data-url="http://www.reddit.com/submit?url='.$url.'&amp;title='.$title.'"></a>';
$share['email'] = '<a href="mailto:?subject='.$title.'&amp;body='.$url.'" class="fa fa-envelope-o no-popup"></a>';
$share['stumbleupon'] = '<a href="javascript:void(0);"  class="fa fa-stumbleupon" data-url="http://www.stumbleupon.com/badge?url='.$url.'&amp;title='.$title.'"></a>';
$share['vk'] = '<a href="javascript:void(0);"  class="fa fa-vk" data-url="http://vk.com/share.php?url='.$url.'&amp;title='.$title.'"></a>';
$share['whatsapp'] = '<a class="fa fa-whatsapp no-popup" href="https://api.whatsapp.com/send?text='.$title.' '.$url.'"></a>';

if ( is_page() ) {
	$share_options = vce_get_option( 'page_social_share' );
} else {
	$share_options = vce_get_option( 'social_share' );
}

$vce_share_html = '';

foreach ( $share_options as $social => $value ) {
	if ( $value ) {
		$vce_share_html .= '<li>'.$share[$social].'</li>';
	}
}
?>

<?php if ( $vce_share_html ): ?>
	<div class="vce-share-bar">
		<ul class="vce-share-items">
			<?php echo $vce_share_html; ?>
		</ul>
	</div>
<?php endif; ?>