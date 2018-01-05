<?php 

if ( 'true' == $more_link ) {
	$more_link = get_post_type_archive_link( $this->post_type );
}
?>
<p class="more-link-wrap">
	<a href="<?php echo esc_url( $more_link ) ?>" class="more-link"><?php echo esc_html( $more_text ) ?></a>
</p>
