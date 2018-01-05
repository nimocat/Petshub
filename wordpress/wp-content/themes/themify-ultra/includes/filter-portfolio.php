<?php
/**
 * Partial template that displays an entry filter.
 *
 * Created by themify
 * @since 1.0.0
 */

global $themify;

if ( isset( $themify->is_shortcode ) && $themify->is_shortcode ) {
	$cats = $themify->shortcode_query_category;
	$taxo = $themify->shortcode_query_taxonomy;
} else {
	if ( is_array( $themify->query_category ) ) {
		$cats = join(',', $themify->query_category);
	} else {
		$cats = $themify->query_category;
	}
	$taxo = $themify->query_taxonomy;
}

?>

<ul class="post-filter">
	<?php wp_list_categories( "hierarchical=0&show_count=0&title_li=&include=$cats&taxonomy=$taxo");?>
</ul>