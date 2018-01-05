<?php
/**
 * Partial template that displays an entry filter.
 *
 * @var $cats Category IDs to include in the list
 * @var $taxo Taxonomy to draw the list from
 * @since 1.0.0
 */

?>

<ul class="themify-portfolio-posts-filter">
	<?php wp_list_categories( "hierarchical=0&show_count=0&title_li=&include=$cats&taxonomy=$taxo" ); ?>
</ul>