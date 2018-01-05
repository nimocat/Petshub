<?php

defined( 'ABSPATH' ) or die;

$GLOBALS['processed_terms'] = array();
$GLOBALS['processed_posts'] = array();

require_once ABSPATH . 'wp-admin/includes/post.php';
require_once ABSPATH . 'wp-admin/includes/taxonomy.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

function themify_import_post( $post ) {
	global $processed_posts, $processed_terms;

	if ( ! post_type_exists( $post['post_type'] ) ) {
		return;
	}

	/* Menu items don't have reliable post_title, skip the post_exists check */
	if( $post['post_type'] !== 'nav_menu_item' ) {
		$post_exists = post_exists( $post['post_title'], '', $post['post_date'] );
		if ( $post_exists && get_post_type( $post_exists ) == $post['post_type'] ) {
			$processed_posts[ intval( $post['ID'] ) ] = intval( $post_exists );
			return;
		}
	}

	if( $post['post_type'] == 'nav_menu_item' ) {
		if( ! isset( $post['tax_input']['nav_menu'] ) || ! term_exists( $post['tax_input']['nav_menu'], 'nav_menu' ) ) {
			return;
		}
		$_menu_item_type = $post['meta_input']['_menu_item_type'];
		$_menu_item_object_id = $post['meta_input']['_menu_item_object_id'];

		if ( 'taxonomy' == $_menu_item_type && isset( $processed_terms[ intval( $_menu_item_object_id ) ] ) ) {
			$post['meta_input']['_menu_item_object_id'] = $processed_terms[ intval( $_menu_item_object_id ) ];
		} else if ( 'post_type' == $_menu_item_type && isset( $processed_posts[ intval( $_menu_item_object_id ) ] ) ) {
			$post['meta_input']['_menu_item_object_id'] = $processed_posts[ intval( $_menu_item_object_id ) ];
		} else if ( 'custom' != $_menu_item_type ) {
			// associated object is missing or not imported yet, we'll retry later
			// $missing_menu_items[] = $item;
			return;
		}
	}

	$post_parent = ( $post['post_type'] == 'nav_menu_item' ) ? $post['meta_input']['_menu_item_menu_item_parent'] : (int) $post['post_parent'];
	$post['post_parent'] = 0;
	if ( $post_parent ) {
		// if we already know the parent, map it to the new local ID
		if ( isset( $processed_posts[ $post_parent ] ) ) {
			if( $post['post_type'] == 'nav_menu_item' ) {
				$post['meta_input']['_menu_item_menu_item_parent'] = $processed_posts[ $post_parent ];
			} else {
				$post['post_parent'] = $processed_posts[ $post_parent ];
			}
		}
	}

	/**
	 * for hierarchical taxonomies, IDs must be used so wp_set_post_terms can function properly
	 * convert term slugs to IDs for hierarchical taxonomies
	 */
	if( ! empty( $post['tax_input'] ) ) {
		foreach( $post['tax_input'] as $tax => $terms ) {
			if( is_taxonomy_hierarchical( $tax ) ) {
				$terms = explode( ', ', $terms );
				$post['tax_input'][ $tax ] = array_map( 'themify_get_term_id_by_slug', $terms, array_fill( 0, count( $terms ), $tax ) );
			}
		}
	}

	$post['post_author'] = (int) get_current_user_id();
	$post['post_status'] = 'publish';

	$old_id = $post['ID'];

	unset( $post['ID'] );
	$post_id = wp_insert_post( $post, true );
	if( is_wp_error( $post_id ) ) {
		return false;
	} else {
		$processed_posts[ $old_id ] = $post_id;

		if( isset( $post['has_thumbnail'] ) && $post['has_thumbnail'] ) {
			$placeholder = themify_get_placeholder_image();
			if( ! is_wp_error( $placeholder ) ) {
				set_post_thumbnail( $post_id, $placeholder );
			}
		}

		return $post_id;
	}
}

function themify_get_placeholder_image() {
	static $placeholder_image = null;

	if( $placeholder_image == null ) {
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		WP_Filesystem();
		global $wp_filesystem;
		$upload = wp_upload_bits( $post['post_name'] . '.jpg', null, $wp_filesystem->get_contents( THEMIFY_DIR . '/img/image-placeholder.jpg' ) );

		if ( $info = wp_check_filetype( $upload['file'] ) )
			$post['post_mime_type'] = $info['type'];
		else
			return new WP_Error( 'attachment_processing_error', __( 'Invalid file type', 'themify' ) );

		$post['guid'] = $upload['url'];
		$post_id = wp_insert_attachment( $post, $upload['file'] );
		wp_update_attachment_metadata( $post_id, wp_generate_attachment_metadata( $post_id, $upload['file'] ) );

		$placeholder_image = $post_id;
	}

	return $placeholder_image;
}

function themify_import_term( $term ) {
	global $processed_terms;

	if( $term_id = term_exists( $term['slug'], $term['taxonomy'] ) ) {
		if ( is_array( $term_id ) ) $term_id = $term_id['term_id'];
		if ( isset( $term['term_id'] ) )
			$processed_terms[ intval( $term['term_id'] ) ] = (int) $term_id;
		return (int) $term_id;
	}

	if ( empty( $term['parent'] ) ) {
		$parent = 0;
	} else {
		$parent = term_exists( $term['parent'], $term['taxonomy'] );
		if ( is_array( $parent ) ) $parent = $parent['term_id'];
	}

	$id = wp_insert_term( $term['name'], $term['taxonomy'], array(
		'parent' => $parent,
		'slug' => $term['slug'],
		'description' => $term['description'],
	) );
	if ( ! is_wp_error( $id ) ) {
		if ( isset( $term['term_id'] ) ) {
			$processed_terms[ intval($term['term_id']) ] = $id['term_id'];
			return $term['term_id'];
		}
	}

	return false;
}

function themify_get_term_id_by_slug( $slug, $tax ) {
	$term = get_term_by( 'slug', $slug, $tax );
	if( $term ) {
		return $term->term_id;
	}

	return false;
}

function themify_undo_import_term( $term ) {
	$term_id = term_exists( $term['slug'], $term['term_taxonomy'] );
	if ( $term_id ) {
		if ( is_array( $term_id ) ) $term_id = $term_id['term_id'];
		if ( isset( $term_id ) ) {
			wp_delete_term( $term_id, $term['term_taxonomy'] );
		}
	}
}

/**
 * Determine if a post exists based on title, content, and date
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param array $args array of database parameters to check
 * @return int Post ID if post exists, 0 otherwise.
 */
function themify_post_exists( $args = array() ) {
	global $wpdb;

	$query = "SELECT ID FROM $wpdb->posts WHERE 1=1";
	$db_args = array();

	foreach ( $args as $key => $value ) {
		$value = wp_unslash( sanitize_post_field( $key, $value, 0, 'db' ) );
		if( ! empty( $value ) ) {
			$query .= ' AND ' . $key . ' = %s';
			$db_args[] = $value;
		}
	}

	if ( !empty ( $args ) )
		return (int) $wpdb->get_var( $wpdb->prepare($query, $args) );

	return 0;
}

function themify_undo_import_post( $post ) {
	if( $post['post_type'] == 'nav_menu_item' ) {
		$post_exists = themify_post_exists( array(
			'post_name' => $post['post_name'],
			'post_modified' => $post['post_date'],
			'post_type' => 'nav_menu_item',
		) );
	} else {
		$post_exists = post_exists( $post['post_title'], '', $post['post_date'] );
	}
	if( $post_exists && get_post_type( $post_exists ) == $post['post_type'] ) {
		/**
		 * check if the post has been modified, if so leave it be
		 *
		 * NOTE: posts are imported using wp_insert_post() which modifies post_modified field
		 * to be the same as post_date, hence to check if the post has been modified,
		 * the post_modified field is compared against post_date in the original post.
		 */
		if( $post['post_date'] == get_post_field( 'post_modified', $post_exists ) ) {
			wp_delete_post( $post_exists, true ); // true: bypass trash
		}
	}
}

function themify_do_demo_import() {
$term = array (
  'term_id' => 70,
  'name' => 'Wedding Page Menu',
  'slug' => 'wedding-page-menu',
  'term_group' => 0,
  'taxonomy' => 'nav_menu',
  'description' => '',
  'parent' => 0,
);
if( ERASEDEMO ) {
	themify_undo_import_term( $term );
} else {
	themify_import_term( $term );
}

$post = array (
  'ID' => 7,
  'post_date' => '2016-08-31 02:42:34',
  'post_date_gmt' => '2016-08-31 02:42:34',
  'post_content' => '',
  'post_title' => 'Home',
  'post_excerpt' => '',
  'post_name' => 'home',
  'post_modified' => '2017-06-22 20:12:26',
  'post_modified_gmt' => '2017-06-22 20:12:26',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-wedding/?page_id=7',
  'menu_order' => 0,
  'post_type' => 'page',
  'meta_input' => 
  array (
    'page_layout' => 'sidebar-none',
    'content_width' => 'full_width',
    'hide_page_title' => 'yes',
    'section_full_scrolling' => 'no',
    'body_background_repeat' => 'fullcover',
    'color_scheme_mode' => 'color-presets',
    'color_design' => 'default',
    'typography_mode' => 'typography-presets',
    'font_design' => 'default',
    'body_font' => 'default',
    'heading_font' => 'default',
    'header_design' => 'default',
    'fixed_header' => 'default',
    'full_height_header' => 'default',
    'header_wrap' => 'transparent',
    'background_mode' => 'fullcover',
    'background_repeat' => 'fullcover',
    'background_auto' => 'yes',
    'background_autotimeout' => '5',
    'background_speed' => '500',
    'background_wrap' => 'yes',
    'footer_design' => 'default',
    'imagefilter_options' => 'initial',
    'imagefilter_options_hover' => 'initial',
    'imagefilter_applyto' => 'initial',
    'order' => 'desc',
    'orderby' => 'date',
    'layout' => 'list-post',
    'display_content' => 'content',
    'feature_size_page' => 'blank',
    'hide_title' => 'default',
    'unlink_title' => 'default',
    'hide_date' => 'default',
    'hide_image' => 'default',
    'unlink_image' => 'default',
    'hide_navigation' => 'default',
    'builder_switch_frontend' => '0',
    '_themify_builder_settings_json' => '[{\\"row_order\\":\\"0\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first\\",\\"modules\\":[{\\"mod_name\\":\\"text\\",\\"mod_settings\\":{\\"content_text\\":\\"<h1>Amelia &amp; Steve<\\\\/h1>\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_image-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\n\\",\\"font_family\\":\\"default\\",\\"font_size\\":\\"1.3\\",\\"font_size_unit\\":\\"em\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_h1\\":\\"default\\",\\"font_size_h1_unit\\":\\"px\\",\\"line_height_h1_unit\\":\\"px\\",\\"font_family_h2\\":\\"default\\",\\"font_size_h2_unit\\":\\"px\\",\\"line_height_h2_unit\\":\\"px\\",\\"font_family_h3\\":\\"default\\",\\"font_size_h3_unit\\":\\"px\\",\\"line_height_h3_unit\\":\\"px\\",\\"font_family_h4\\":\\"default\\",\\"font_size_h4_unit\\":\\"px\\",\\"line_height_h4_unit\\":\\"px\\",\\"font_family_h5\\":\\"default\\",\\"font_size_h5_unit\\":\\"px\\",\\"line_height_h5_unit\\":\\"px\\",\\"font_family_h6\\":\\"default\\",\\"font_size_h6_unit\\":\\"px\\",\\"line_height_h6_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"mod_name\\":\\"text\\",\\"mod_settings\\":{\\"content_text\\":\\"<h4>are getting married<\\\\/h4>\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_image-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\n\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_h1\\":\\"default\\",\\"font_size_h1_unit\\":\\"px\\",\\"line_height_h1_unit\\":\\"px\\",\\"font_family_h2\\":\\"default\\",\\"font_size_h2_unit\\":\\"px\\",\\"line_height_h2_unit\\":\\"px\\",\\"font_family_h3\\":\\"default\\",\\"font_size_h3_unit\\":\\"px\\",\\"line_height_h3_unit\\":\\"px\\",\\"font_family_h4\\":\\"default\\",\\"font_size_h4_unit\\":\\"px\\",\\"line_height_h4_unit\\":\\"px\\",\\"font_family_h5\\":\\"default\\",\\"font_size_h5_unit\\":\\"px\\",\\"line_height_h5_unit\\":\\"px\\",\\"font_family_h6\\":\\"default\\",\\"font_size_h6_unit\\":\\"px\\",\\"line_height_h6_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-center\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-wedding\\\\/files\\\\/2016\\\\/08\\\\/ring.png\\",\\"appearance_image\\":\\"|\\",\\"width_image\\":\\"22\\",\\"auto_fullwidth\\":\\"|\\",\\"height_image\\":\\"15\\",\\"param_image\\":\\"|\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_image-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\n\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_title\\":\\"default\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_family_caption\\":\\"default\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"mod_name\\":\\"text\\",\\"mod_settings\\":{\\"content_text\\":\\"<h4>Friday, October 21, 2017<\\\\/h4>\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_image-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\n\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_h1\\":\\"default\\",\\"font_size_h1_unit\\":\\"px\\",\\"line_height_h1_unit\\":\\"px\\",\\"font_family_h2\\":\\"default\\",\\"font_size_h2_unit\\":\\"px\\",\\"line_height_h2_unit\\":\\"px\\",\\"font_family_h3\\":\\"default\\",\\"font_size_h3_unit\\":\\"px\\",\\"line_height_h3_unit\\":\\"px\\",\\"font_family_h4\\":\\"default\\",\\"font_size_h4_unit\\":\\"px\\",\\"line_height_h4_unit\\":\\"px\\",\\"font_family_h5\\":\\"default\\",\\"font_size_h5_unit\\":\\"px\\",\\"line_height_h5_unit\\":\\"px\\",\\"font_family_h6\\":\\"default\\",\\"font_size_h6_unit\\":\\"px\\",\\"line_height_h6_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]}],\\"styling\\":{\\"row_width\\":\\"\\",\\"row_height\\":\\"\\",\\"custom_css_row\\":\\"\\",\\"row_anchor\\":\\"\\",\\"animation_effect\\":\\"\\",\\"animation_effect_delay\\":\\"\\",\\"animation_effect_repeat\\":\\"\\",\\"custom_parallax_scroll_speed\\":\\"\\",\\"custom_parallax_scroll_zindex\\":\\"\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_mobile\\":\\"show\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"https://themify.me/demo/themes/ultra-wedding\\\\/files\\\\/2016\\\\/08\\\\/wedding-bg-top-1024x744.jpg\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"fullcover\\",\\"background_position\\":\\"center-top\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"000000_0.39\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"ffffff_1.00\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"center\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"22\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"15\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"breakpoint_mobile\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"29\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"1\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first\\",\\"modules\\":[{\\"mod_name\\":\\"text\\",\\"mod_settings\\":{\\"content_text\\":\\"<h2>Our Story<\\\\/h2><h3>One night in a hot summer day in Toronto, I randomly stumble upon a stree where I met the most important person of my life. Here is how our story goes...<\\\\/h3><p>Duis bibendum, ex ac rutrum pharetra, tortor ipsum commodo est, et vehicula metus lectus sed metus. Pellentesque. Vestibulum consectetur risus id metus lacinia suscipit. Nunc tempus sem id mi tristique, et fringilla Lorem ipsum dolor sit amet, consectetur.<\\\\/p>\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_image-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\n\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"center\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"6\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"6\\",\\"padding_left_unit\\":\\"%\\",\\"checkbox_padding_apply_all\\":\\"|\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"50\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"|\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_color\\":\\"fdf5f4\\",\\"border_top_width\\":\\"5\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"fdf5f4\\",\\"border_right_width\\":\\"5\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"fdf5f4\\",\\"border_bottom_width\\":\\"5\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"fdf5f4\\",\\"border_left_width\\":\\"5\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_h1\\":\\"default\\",\\"font_size_h1_unit\\":\\"px\\",\\"line_height_h1_unit\\":\\"px\\",\\"font_family_h2\\":\\"default\\",\\"font_size_h2_unit\\":\\"px\\",\\"line_height_h2_unit\\":\\"px\\",\\"font_family_h3\\":\\"default\\",\\"font_size_h3_unit\\":\\"px\\",\\"line_height_h3_unit\\":\\"px\\",\\"font_family_h4\\":\\"default\\",\\"font_size_h4_unit\\":\\"px\\",\\"line_height_h4_unit\\":\\"px\\",\\"font_family_h5\\":\\"default\\",\\"font_size_h5_unit\\":\\"px\\",\\"line_height_h5_unit\\":\\"px\\",\\"font_family_h6\\":\\"default\\",\\"font_size_h6_unit\\":\\"px\\",\\"line_height_h6_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]}],\\"styling\\":{\\"row_width\\":\\"\\",\\"row_height\\":\\"\\",\\"custom_css_row\\":\\"our-story-section\\",\\"row_anchor\\":\\"story\\",\\"animation_effect\\":\\"\\",\\"animation_effect_delay\\":\\"\\",\\"animation_effect_repeat\\":\\"\\",\\"custom_parallax_scroll_speed\\":\\"\\",\\"custom_parallax_scroll_zindex\\":\\"\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_mobile\\":\\"show\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"8\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"breakpoint_mobile\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"14\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"2\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first\\",\\"modules\\":[{\\"mod_name\\":\\"timeline\\",\\"mod_settings\\":{\\"template_timeline\\":\\"list\\",\\"source_timeline\\":\\"text\\",\\"category_post_timeline\\":\\"0|single\\",\\"order_post_timeline\\":\\"desc\\",\\"orderby_post_timeline\\":\\"date\\",\\"display_post_timeline\\":\\"excerpt\\",\\"hide_feat_img_post_timeline\\":\\"no\\",\\"text_source_timeline\\":[{\\"title_timeline\\":\\"How We Met\\",\\"icon_timeline\\":\\"fa-heart-o\\",\\"date_timeline\\":\\"July 2014\\",\\"content_timeline\\":\\"<p>Ego Vero Volo In Virtute Vim Esse Quam Maximam; Inde Igitur, Inquit, Ordiendum Est. Ne Amores Quidem Sanctos A Sapiente Alienos Esse Arbitrantur. Non Quam Nostram Quidem, Inquit Pomponius Iocans; De Vacuitate Doloris Eadem Sententia Erit.<\\\\/p>\\"},{\\"title_timeline\\":\\"The First Date\\",\\"icon_timeline\\":\\"fa-heart-o\\",\\"date_timeline\\":\\"Jan 2015\\",\\"image_timeline\\":\\"https://themify.me/demo/themes/ultra-wedding\\\\/files\\\\/2016\\\\/09\\\\/first-date-image.jpg\\",\\"content_timeline\\":\\"<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit.<\\\\/p>\\"},{\\"title_timeline\\":\\"We Are Enganged\\",\\"icon_timeline\\":\\"fa-heart-o\\",\\"date_timeline\\":\\"Sept 2016\\",\\"content_timeline\\":\\"<p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.<\\\\/p>\\"},{\\"title_timeline\\":\\"Getting Married\\",\\"icon_timeline\\":\\"fa-heart-o\\",\\"date_timeline\\":\\"Oct 2017\\",\\"content_timeline\\":\\"<p>Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae.<\\\\/p>\\"},{\\"icon_timeline\\":\\"fa-circle-thin\\"}],\\"font_size_unit\\":\\"px\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}],\\"styling\\":[]}],\\"styling\\":{\\"row_width\\":\\"\\",\\"row_height\\":\\"\\",\\"custom_css_row\\":\\"\\",\\"row_anchor\\":\\"\\",\\"animation_effect\\":\\"\\",\\"animation_effect_delay\\":\\"\\",\\"animation_effect_repeat\\":\\"\\",\\"custom_parallax_scroll_speed\\":\\"\\",\\"custom_parallax_scroll_zindex\\":\\"\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_mobile\\":\\"show\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"7\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"breakpoint_mobile\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"12\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"3\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col4-2 first\\",\\"modules\\":[],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"https://themify.me/demo/themes/ultra-wedding\\\\/files\\\\/2016\\\\/08\\\\/ring-1932-1024x682.jpg\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"fullcover\\",\\"background_position\\":\\"center-top\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"180\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"180\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}},{\\"column_order\\":\\"1\\",\\"grid_class\\":\\"col4-2 last\\",\\"modules\\":[{\\"mod_name\\":\\"text\\",\\"mod_settings\\":{\\"content_text\\":\\"<h2>Ceremony &amp; Reception<\\\\/h2>\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_image-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\n\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_h1\\":\\"default\\",\\"font_size_h1_unit\\":\\"px\\",\\"line_height_h1_unit\\":\\"px\\",\\"font_family_h2\\":\\"default\\",\\"font_size_h2_unit\\":\\"px\\",\\"line_height_h2_unit\\":\\"px\\",\\"font_family_h3\\":\\"default\\",\\"font_size_h3_unit\\":\\"px\\",\\"line_height_h3_unit\\":\\"px\\",\\"font_family_h4\\":\\"default\\",\\"font_size_h4_unit\\":\\"px\\",\\"line_height_h4_unit\\":\\"px\\",\\"font_family_h5\\":\\"default\\",\\"font_size_h5_unit\\":\\"px\\",\\"line_height_h5_unit\\":\\"px\\",\\"font_family_h6\\":\\"default\\",\\"font_size_h6_unit\\":\\"px\\",\\"line_height_h6_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"mod_name\\":\\"feature\\",\\"mod_settings\\":{\\"title_feature\\":\\"FRIDAY, OCTOBER 21, 2017\\",\\"content_feature\\":\\"<p>2:30 PM - 5:30 PM<\\\\/p>\\",\\"layout_feature\\":\\"icon-top\\",\\"circle_size_feature\\":\\"medium\\",\\"icon_type_feature\\":\\"icon\\",\\"icon_feature\\":\\"fa-calendar-minus-o\\",\\"icon_color_feature\\":\\"ffffff_1.00\\",\\"param_feature\\":\\"|\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_image-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\n\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_title\\":\\"default\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"mod_name\\":\\"feature\\",\\"mod_settings\\":{\\"title_feature\\":\\"Marina Village Mojo Room\\",\\"content_feature\\":\\"<p>1936 Quivira Way - San Diego, California 92109<\\\\/p>\\",\\"layout_feature\\":\\"icon-top\\",\\"circle_size_feature\\":\\"medium\\",\\"icon_type_feature\\":\\"icon\\",\\"icon_feature\\":\\"fa-map-marker\\",\\"icon_color_feature\\":\\"ffffff_1.00\\",\\"param_feature\\":\\"|\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_image-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\n\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_title\\":\\"default\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"ff887b\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"center\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"60\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"30\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\",\\"breakpoint_mobile\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"12\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"10\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}}}],\\"gutter\\":\\"gutter-none\\",\\"column_alignment\\":\\"col_align_middle\\",\\"styling\\":{\\"row_width\\":\\"fullwidth-content\\",\\"row_height\\":\\"\\",\\"custom_css_row\\":\\"ceremony-section\\",\\"row_anchor\\":\\"ceremony\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"ffffff_1.00\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_mobile\\":\\"show\\",\\"animation_effect\\":\\"\\",\\"animation_effect_delay\\":\\"\\",\\"animation_effect_repeat\\":\\"\\",\\"custom_parallax_scroll_speed\\":\\"\\",\\"custom_parallax_scroll_zindex\\":\\"\\"}},{\\"row_order\\":\\"4\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first\\",\\"modules\\":[{\\"mod_name\\":\\"text\\",\\"mod_settings\\":{\\"content_text\\":\\"<h2>Wedding Countdown<\\\\/h2>\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_image-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\n\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"center\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"50\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"|\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_h1\\":\\"default\\",\\"font_size_h1_unit\\":\\"px\\",\\"line_height_h1_unit\\":\\"px\\",\\"font_family_h2\\":\\"default\\",\\"font_size_h2_unit\\":\\"px\\",\\"line_height_h2_unit\\":\\"px\\",\\"font_family_h3\\":\\"default\\",\\"font_size_h3_unit\\":\\"px\\",\\"line_height_h3_unit\\":\\"px\\",\\"font_family_h4\\":\\"default\\",\\"font_size_h4_unit\\":\\"px\\",\\"line_height_h4_unit\\":\\"px\\",\\"font_family_h5\\":\\"default\\",\\"font_size_h5_unit\\":\\"px\\",\\"line_height_h5_unit\\":\\"px\\",\\"font_family_h6\\":\\"default\\",\\"font_size_h6_unit\\":\\"px\\",\\"line_height_h6_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"mod_name\\":\\"countdown\\",\\"mod_settings\\":{\\"mod_date_countdown\\":\\"2018-01-12 13:30:50\\",\\"color_countdown\\":\\"transparent\\",\\"done_action_countdown\\":\\"nothing\\",\\"font_family\\":\\"default\\",\\"text_align\\":\\"center\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]}],\\"styling\\":{\\"row_width\\":\\"\\",\\"row_height\\":\\"\\",\\"custom_css_row\\":\\"\\",\\"row_anchor\\":\\"\\",\\"animation_effect\\":\\"\\",\\"animation_effect_delay\\":\\"\\",\\"animation_effect_repeat\\":\\"\\",\\"custom_parallax_scroll_speed\\":\\"\\",\\"custom_parallax_scroll_zindex\\":\\"\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_mobile\\":\\"show\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"fdf5f4\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"6\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"6\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"breakpoint_mobile\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"12\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"12\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"5\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first\\",\\"modules\\":[{\\"mod_name\\":\\"text\\",\\"mod_settings\\":{\\"content_text\\":\\"<h2>Gallery<\\\\/h2>\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_image-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\n\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"center\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"50\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"|\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_h1\\":\\"default\\",\\"font_size_h1_unit\\":\\"px\\",\\"line_height_h1_unit\\":\\"px\\",\\"font_family_h2\\":\\"default\\",\\"font_size_h2_unit\\":\\"px\\",\\"line_height_h2_unit\\":\\"px\\",\\"font_family_h3\\":\\"default\\",\\"font_size_h3_unit\\":\\"px\\",\\"line_height_h3_unit\\":\\"px\\",\\"font_family_h4\\":\\"default\\",\\"font_size_h4_unit\\":\\"px\\",\\"line_height_h4_unit\\":\\"px\\",\\"font_family_h5\\":\\"default\\",\\"font_size_h5_unit\\":\\"px\\",\\"line_height_h5_unit\\":\\"px\\",\\"font_family_h6\\":\\"default\\",\\"font_size_h6_unit\\":\\"px\\",\\"line_height_h6_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"row_order\\":\\"1\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col4-1 first\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-overlay\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-wedding\\\\/files\\\\/2016\\\\/09\\\\/weddings-632734_1920-1024x516-476x353.jpg\\",\\"appearance_image\\":\\"|\\",\\"width_image\\":\\"476\\",\\"auto_fullwidth\\":\\"1\\",\\"height_image\\":\\"353\\",\\"link_image\\":\\"https://themify.me/demo/themes/ultra-wedding\\\\/files\\\\/2016\\\\/09\\\\/weddings-632734_1920.jpg\\",\\"param_image\\":\\"lightbox\\",\\"image_zoom_icon\\":\\"zoom\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"alt_image\\":\\"Wedding Dress\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_image-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\n\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_title\\":\\"default\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_family_caption\\":\\"default\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]},{\\"column_order\\":\\"1\\",\\"grid_class\\":\\"col4-1\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-overlay\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-wedding\\\\/files\\\\/2016\\\\/09\\\\/heart-529607_1920-1024x681-476x353.jpg\\",\\"appearance_image\\":\\"|\\",\\"width_image\\":\\"476\\",\\"auto_fullwidth\\":\\"1\\",\\"height_image\\":\\"353\\",\\"link_image\\":\\"https://themify.me/demo/themes/ultra-wedding\\\\/files\\\\/2016\\\\/09\\\\/heart-529607_1920.jpg\\",\\"param_image\\":\\"lightbox\\",\\"image_zoom_icon\\":\\"zoom\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"alt_image\\":\\"Love\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_image-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\n\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"none\\",\\"border_right_style\\":\\"none\\",\\"border_bottom_style\\":\\"none\\",\\"border_left_style\\":\\"none\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_title\\":\\"default\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_family_caption\\":\\"default\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]},{\\"column_order\\":\\"2\\",\\"grid_class\\":\\"col4-2 last\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-overlay\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-wedding\\\\/files\\\\/2016\\\\/08\\\\/wedding-dresses-1486004_1920.jpg\\",\\"width_image\\":\\"951\\",\\"auto_fullwidth\\":\\"1\\",\\"height_image\\":\\"353\\",\\"link_image\\":\\"https://themify.me/demo/themes/ultra-wedding\\\\/files\\\\/2016\\\\/08\\\\/wedding-dresses-1486004_1920.jpg\\",\\"param_image\\":\\"lightbox\\",\\"image_zoom_icon\\":\\"zoom\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"alt_image\\":\\"Pre Wedding \\",\\"background_image-gradient-angle\\":\\"180\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"}}],\\"styling\\":[]}],\\"gutter\\":\\"gutter-none\\",\\"column_alignment\\":\\"\\",\\"styling\\":[]},{\\"row_order\\":\\"2\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col4-2 first\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-overlay\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-wedding\\\\/files\\\\/2016\\\\/08\\\\/marriage-918864_1920-1024x682-951x353.jpg\\",\\"appearance_image\\":\\"|\\",\\"width_image\\":\\"951\\",\\"auto_fullwidth\\":\\"1\\",\\"height_image\\":\\"353\\",\\"link_image\\":\\"https://themify.me/demo/themes/ultra-wedding\\\\/files\\\\/2016\\\\/08\\\\/marriage-918864_1920.jpg\\",\\"param_image\\":\\"lightbox\\",\\"image_zoom_icon\\":\\"zoom\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"alt_image\\":\\"The Party\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_image-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\n\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_title\\":\\"default\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_family_caption\\":\\"default\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]},{\\"column_order\\":\\"1\\",\\"grid_class\\":\\"col4-1\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-overlay\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-wedding\\\\/files\\\\/2016\\\\/08\\\\/bride-997604_1920-1024x683-476x353.jpg\\",\\"appearance_image\\":\\"|\\",\\"width_image\\":\\"476\\",\\"auto_fullwidth\\":\\"1\\",\\"height_image\\":\\"353\\",\\"link_image\\":\\"https://themify.me/demo/themes/ultra-wedding\\\\/files\\\\/2016\\\\/08\\\\/bride-997604_1920.jpg\\",\\"param_image\\":\\"lightbox\\",\\"image_zoom_icon\\":\\"zoom\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"alt_image\\":\\"Hair Make Up\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_image-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\n\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_title\\":\\"default\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_family_caption\\":\\"default\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]},{\\"column_order\\":\\"2\\",\\"grid_class\\":\\"col4-1 last\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-overlay\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-wedding\\\\/files\\\\/2016\\\\/09\\\\/wedding-997605_1920-1024x683-476x353.jpg\\",\\"appearance_image\\":\\"|\\",\\"width_image\\":\\"476\\",\\"auto_fullwidth\\":\\"1\\",\\"height_image\\":\\"353\\",\\"link_image\\":\\"https://themify.me/demo/themes/ultra-wedding\\\\/files\\\\/2016\\\\/09\\\\/wedding-997605_1920.jpg\\",\\"param_image\\":\\"lightbox\\",\\"image_zoom_icon\\":\\"zoom\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"alt_image\\":\\"Together\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_image-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\n\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_title\\":\\"default\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_family_caption\\":\\"default\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]}],\\"gutter\\":\\"gutter-none\\",\\"column_alignment\\":\\"\\",\\"styling\\":[]}],\\"styling\\":[]}],\\"styling\\":{\\"row_width\\":\\"fullwidth-content\\",\\"row_height\\":\\"\\",\\"custom_css_row\\":\\"\\",\\"row_anchor\\":\\"gallery\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"6\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_mobile\\":\\"show\\",\\"animation_effect\\":\\"\\",\\"animation_effect_delay\\":\\"\\",\\"animation_effect_repeat\\":\\"\\",\\"custom_parallax_scroll_speed\\":\\"\\",\\"custom_parallax_scroll_zindex\\":\\"\\",\\"breakpoint_mobile\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"12\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"6\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first\\",\\"modules\\":[{\\"mod_name\\":\\"text\\",\\"mod_settings\\":{\\"content_text\\":\\"<h2>Are You Attending<\\\\/h2><h3>Please RSVP before September 25<\\\\/h3>\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_image-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"ffffff_1.00\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"center\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"50\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"|\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_h1\\":\\"default\\",\\"font_size_h1_unit\\":\\"px\\",\\"line_height_h1_unit\\":\\"px\\",\\"font_family_h2\\":\\"default\\",\\"font_size_h2_unit\\":\\"px\\",\\"line_height_h2_unit\\":\\"px\\",\\"font_family_h3\\":\\"default\\",\\"font_size_h3_unit\\":\\"px\\",\\"line_height_h3_unit\\":\\"px\\",\\"font_family_h4\\":\\"default\\",\\"font_size_h4_unit\\":\\"px\\",\\"line_height_h4_unit\\":\\"px\\",\\"font_family_h5\\":\\"default\\",\\"font_size_h5_unit\\":\\"px\\",\\"line_height_h5_unit\\":\\"px\\",\\"font_family_h6\\":\\"default\\",\\"font_size_h6_unit\\":\\"px\\",\\"line_height_h6_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"row_order\\":\\"1\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col4-1 first\\",\\"modules\\":[],\\"grid_width\\":\\"18.9902\\",\\"styling\\":[]},{\\"column_order\\":\\"1\\",\\"grid_class\\":\\"col4-2\\",\\"modules\\":[{\\"mod_name\\":\\"contact\\",\\"mod_settings\\":{\\"layout_contact\\":\\"style1\\",\\"mail_contact\\":\\"youremail@domain.com\\",\\"field_subject_active\\":\\"|\\",\\"field_captcha_active\\":\\"|\\",\\"field_sendcopy_active\\":\\"|\\",\\"font_family\\":\\"default\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"font_family_labels\\":\\"default\\",\\"font_size_labels_unit\\":\\"px\\",\\"font_family_inputs\\":\\"default\\",\\"font_size_inputs_unit\\":\\"px\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_send\\":\\"default\\",\\"font_size_send_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"grid_width\\":\\"55.7858\\",\\"styling\\":[]},{\\"column_order\\":\\"2\\",\\"grid_class\\":\\"col4-1 last\\",\\"modules\\":[],\\"grid_width\\":\\"18.8255\\",\\"styling\\":[]}],\\"column_alignment\\":\\"\\",\\"styling\\":[]}],\\"styling\\":[]}],\\"styling\\":{\\"row_width\\":\\"\\",\\"row_height\\":\\"\\",\\"custom_css_row\\":\\"\\",\\"row_anchor\\":\\"reservation\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"443432\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"ffffff_1.00\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"fcffcc_1.00\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"6\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"6\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"animation_effect\\":\\"\\",\\"animation_effect_delay\\":\\"\\",\\"animation_effect_repeat\\":\\"\\",\\"custom_parallax_scroll_speed\\":\\"\\",\\"custom_parallax_scroll_zindex\\":\\"\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_mobile\\":\\"show\\",\\"breakpoint_mobile\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"12\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"12\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"7\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first\\",\\"modules\\":[{\\"mod_name\\":\\"maps-pro\\",\\"mod_settings\\":{\\"map_display_type\\":\\"dynamic\\",\\"w_map\\":\\"100\\",\\"unit_w\\":\\"%\\",\\"w_map_static\\":\\"500\\",\\"h_map\\":\\"445\\",\\"unit_h\\":\\"px\\",\\"type_map\\":\\"ROADMAP\\",\\"style_map\\":\\"routexl\\",\\"scrollwheel_map\\":\\"disable\\",\\"draggable_map\\":\\"enable\\",\\"disable_map_ui\\":\\"no\\",\\"map_link\\":\\"|\\",\\"zoom_map\\":\\"17\\",\\"map_center\\":\\"Spring valley, San Diego, CA\\",\\"markers\\":[{\\"address\\":\\"Spring valley, San Diego, CA\\",\\"title\\":\\"Spring valley, San Diego, CA\\",\\"image\\":\\"https://themify.me/demo/themes/ultra-wedding\\\\/files\\\\/2016\\\\/08\\\\/wedding-icon.png\\"}],\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]}],\\"styling\\":{\\"row_width\\":\\"fullwidth-content\\",\\"row_height\\":\\"\\",\\"custom_css_row\\":\\"\\",\\"row_anchor\\":\\"\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_mobile\\":\\"show\\",\\"animation_effect\\":\\"\\",\\"animation_effect_delay\\":\\"\\",\\"animation_effect_repeat\\":\\"\\",\\"custom_parallax_scroll_speed\\":\\"\\",\\"custom_parallax_scroll_zindex\\":\\"\\"}},{\\"row_order\\":\\"8\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[],\\"styling\\":[]}],\\"styling\\":[]}]',
  ),
  'tax_input' => 
  array (
  ),
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 5884,
  'post_date' => '2016-09-20 05:06:10',
  'post_date_gmt' => '2016-09-20 05:06:10',
  'post_content' => '',
  'post_title' => 'Our Story',
  'post_excerpt' => '',
  'post_name' => 'bride-groom',
  'post_modified' => '2016-09-21 00:58:20',
  'post_modified_gmt' => '2016-09-21 00:58:20',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-wedding/2016/09/20/bride-groom/',
  'menu_order' => 1,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '5884',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#story',
  ),
  'tax_input' => 
  array (
    'nav_menu' => 'wedding-page-menu',
  ),
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 5885,
  'post_date' => '2016-09-20 05:06:10',
  'post_date_gmt' => '2016-09-20 05:06:10',
  'post_content' => '',
  'post_title' => 'When/Where',
  'post_excerpt' => '',
  'post_name' => 'wedding-party',
  'post_modified' => '2016-09-21 00:58:20',
  'post_modified_gmt' => '2016-09-21 00:58:20',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-wedding/2016/09/20/wedding-party/',
  'menu_order' => 2,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '5885',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#ceremony',
  ),
  'tax_input' => 
  array (
    'nav_menu' => 'wedding-page-menu',
  ),
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 5886,
  'post_date' => '2016-09-20 05:06:10',
  'post_date_gmt' => '2016-09-20 05:06:10',
  'post_content' => '',
  'post_title' => 'Photo Gallery',
  'post_excerpt' => '',
  'post_name' => 'when-where',
  'post_modified' => '2016-09-21 00:58:20',
  'post_modified_gmt' => '2016-09-21 00:58:20',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-wedding/2016/09/20/when-where/',
  'menu_order' => 3,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '5886',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#gallery',
  ),
  'tax_input' => 
  array (
    'nav_menu' => 'wedding-page-menu',
  ),
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 5887,
  'post_date' => '2016-09-20 05:06:10',
  'post_date_gmt' => '2016-09-20 05:06:10',
  'post_content' => '',
  'post_title' => 'RSVP',
  'post_excerpt' => '',
  'post_name' => 'rsvp-2',
  'post_modified' => '2016-09-21 00:58:20',
  'post_modified_gmt' => '2016-09-21 00:58:20',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-wedding/2016/09/20/rsvp-2/',
  'menu_order' => 4,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '5887',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => 'highlight-link',
    ),
    '_menu_item_url' => '#reservation',
  ),
  'tax_input' => 
  array (
    'nav_menu' => 'wedding-page-menu',
  ),
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 5659,
  'post_date' => '2016-09-20 05:01:24',
  'post_date_gmt' => '2016-09-20 05:01:24',
  'post_content' => '',
  'post_title' => 'Reservation',
  'post_excerpt' => '',
  'post_name' => 'reservation',
  'post_modified' => '2016-09-20 05:01:24',
  'post_modified_gmt' => '2016-09-20 05:01:24',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-wedding/2016/09/20/reservation/',
  'menu_order' => 6,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '5659',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => 'highlight-link',
    ),
    '_menu_item_url' => 'https://themify.me/demo/themes/ultra-restaurant/#reservation',
  ),
  'tax_input' => 
  array (
  ),
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 5865,
  'post_date' => '2016-09-20 05:03:28',
  'post_date_gmt' => '2016-09-20 05:03:28',
  'post_content' => '',
  'post_title' => 'Reservation',
  'post_excerpt' => '',
  'post_name' => 'reservation-2',
  'post_modified' => '2016-09-20 05:03:28',
  'post_modified_gmt' => '2016-09-20 05:03:28',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-wedding/2016/09/20/reservation-2/',
  'menu_order' => 6,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '5865',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => 'highlight-link',
    ),
    '_menu_item_url' => 'https://themify.me/demo/themes/ultra-restaurant/#reservation',
  ),
  'tax_input' => 
  array (
  ),
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 6018,
  'post_date' => '2016-09-20 05:06:50',
  'post_date_gmt' => '2016-09-20 05:06:50',
  'post_content' => '',
  'post_title' => 'Reservation',
  'post_excerpt' => '',
  'post_name' => 'reservation-3',
  'post_modified' => '2016-09-20 05:06:50',
  'post_modified_gmt' => '2016-09-20 05:06:50',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-wedding/2016/09/20/reservation-3/',
  'menu_order' => 6,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '6018',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => 'highlight-link',
    ),
    '_menu_item_url' => 'https://themify.me/demo/themes/ultra-restaurant/#reservation',
  ),
  'tax_input' => 
  array (
  ),
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}


function themify_import_get_term_id_from_slug( $slug ) {
	$menu = get_term_by( "slug", $slug, "nav_menu" );
	return is_wp_error( $menu ) ? 0 : (int) $menu->term_id;
}

	$widgets = get_option( "widget_text" );
$widgets[1002] = array (
  'title' => '',
  'text' => '',
  'filter' => false,
);
update_option( "widget_text", $widgets );

$widgets = get_option( "widget_text" );
$widgets[1003] = array (
  'title' => 'About',
  'text' => 'The Ultra theme is Themify\'s flagship theme. It\'s a WordPress designed to give you more control on the design of your theme. Built to work seamlessly with our drag & drop Builder plugin, it gives you the ability to customize the look and feel of your content. ',
  'filter' => false,
);
update_option( "widget_text", $widgets );

$widgets = get_option( "widget_text" );
$widgets[1004] = array (
  'title' => 'Address',
  'text' => '25 Ohio St. Cleveland. MA<br/>
(912) 555-8900<br/>
<a href="https://themify.me/">themify.me</a>',
  'filter' => false,
);
update_option( "widget_text", $widgets );

$widgets = get_option( "widget_themify-social-links" );
$widgets[1005] = array (
  'title' => '',
  'show_link_name' => NULL,
  'open_new_window' => NULL,
  'icon_size' => 'icon-large',
  'orientation' => 'horizontal',
);
update_option( "widget_themify-social-links", $widgets );

$widgets = get_option( "widget_text" );
$widgets[1006] = array (
  'title' => 'Hours',
  'text' => 'MON - FRI 9AM -11PM<br/>
SAT - SUN 5PM - 2AM<br/>
Bar open only on weekends',
  'filter' => false,
);
update_option( "widget_text", $widgets );

$widgets = get_option( "widget_archives" );
$widgets[1007] = array (
  'title' => '',
  'count' => 0,
  'dropdown' => 0,
);
update_option( "widget_archives", $widgets );

$widgets = get_option( "widget_meta" );
$widgets[1008] = array (
  'title' => '',
);
update_option( "widget_meta", $widgets );

$widgets = get_option( "widget_search" );
$widgets[1009] = array (
  'title' => '',
);
update_option( "widget_search", $widgets );

$widgets = get_option( "widget_text" );
$widgets[1010] = array (
  'title' => 'Widget 1',
  'text' => 'Optional widget here',
  'filter' => false,
);
update_option( "widget_text", $widgets );

$widgets = get_option( "widget_text" );
$widgets[1011] = array (
  'title' => 'Widget 2',
  'text' => 'Display any widget here',
  'filter' => false,
);
update_option( "widget_text", $widgets );

$widgets = get_option( "widget_text" );
$widgets[1012] = array (
  'title' => 'Widget 3',
  'text' => 'For example, phone #: 123-333-4567',
  'filter' => false,
);
update_option( "widget_text", $widgets );

$widgets = get_option( "widget_text" );
$widgets[1013] = array (
  'title' => 'About',
  'text' => 'The Ultra theme is Themify\'s flagship theme. It\'s a WordPress designed to give you more control on the design of your theme. Built to work seamlessly with our drag & drop Builder plugin, it gives you the ability to customize the look and feel of your content. ',
  'filter' => false,
);
update_option( "widget_text", $widgets );

$widgets = get_option( "widget_categories" );
$widgets[1014] = array (
  'title' => '',
  'count' => 0,
  'hierarchical' => 0,
  'dropdown' => 0,
);
update_option( "widget_categories", $widgets );

$widgets = get_option( "widget_recent-posts" );
$widgets[1015] = array (
  'title' => '',
  'number' => 5,
);
update_option( "widget_recent-posts", $widgets );

$widgets = get_option( "widget_recent-comments" );
$widgets[1016] = array (
  'title' => '',
  'number' => 5,
);
update_option( "widget_recent-comments", $widgets );

$widgets = get_option( "widget_themify-feature-posts" );
$widgets[1017] = array (
  'title' => 'Recent Posts',
  'category' => '0',
  'show_count' => '3',
  'show_date' => 'on',
  'show_thumb' => 'on',
  'display' => 'none',
  'hide_title' => NULL,
  'thumb_width' => '50',
  'thumb_height' => '50',
  'excerpt_length' => '55',
  'orderby' => 'date',
  'order' => 'DESC',
);
update_option( "widget_themify-feature-posts", $widgets );

$widgets = get_option( "widget_themify-social-links" );
$widgets[1018] = array (
  'title' => '',
  'show_link_name' => NULL,
  'open_new_window' => NULL,
  'icon_size' => 'icon-medium',
  'orientation' => 'horizontal',
);
update_option( "widget_themify-social-links", $widgets );

$widgets = get_option( "widget_themify-social-links" );
$widgets[1019] = array (
  'title' => '',
  'show_link_name' => NULL,
  'open_new_window' => NULL,
  'icon_size' => 'icon-medium',
  'orientation' => 'horizontal',
);
update_option( "widget_themify-social-links", $widgets );

$widgets = get_option( "widget_themify-social-links" );
$widgets[1020] = array (
  'title' => '',
  'show_link_name' => NULL,
  'open_new_window' => NULL,
  'icon_size' => 'icon-medium',
  'orientation' => 'horizontal',
);
update_option( "widget_themify-social-links", $widgets );

$widgets = get_option( "widget_themify-social-links" );
$widgets[1021] = array (
  'title' => '',
  'show_link_name' => NULL,
  'open_new_window' => NULL,
  'icon_size' => 'icon-medium',
  'orientation' => 'horizontal',
);
update_option( "widget_themify-social-links", $widgets );

$widgets = get_option( "widget_themify-twitter" );
$widgets[1022] = array (
  'title' => 'Latest Tweets',
  'username' => 'themify',
  'show_count' => '3',
  'hide_timestamp' => NULL,
  'show_follow' => 'on',
  'follow_text' => '→ Follow me',
  'include_retweets' => 'on',
  'exclude_replies' => NULL,
);
update_option( "widget_themify-twitter", $widgets );

$widgets = get_option( "widget_themify-twitter" );
$widgets[1023] = array (
  'title' => 'Latest Tweets',
  'username' => 'themify',
  'show_count' => '2',
  'hide_timestamp' => NULL,
  'show_follow' => NULL,
  'follow_text' => '→ Follow me',
  'include_retweets' => NULL,
  'exclude_replies' => NULL,
);
update_option( "widget_themify-twitter", $widgets );

$widgets = get_option( "widget_archives" );
$widgets[1024] = array (
  'title' => '',
  'count' => 0,
  'dropdown' => 0,
);
update_option( "widget_archives", $widgets );

$widgets = get_option( "widget_meta" );
$widgets[1025] = array (
  'title' => '',
);
update_option( "widget_meta", $widgets );

$widgets = get_option( "widget_search" );
$widgets[1026] = array (
  'title' => '',
);
update_option( "widget_search", $widgets );

$widgets = get_option( "widget_categories" );
$widgets[1027] = array (
  'title' => '',
  'count' => 0,
  'hierarchical' => 0,
  'dropdown' => 0,
);
update_option( "widget_categories", $widgets );

$widgets = get_option( "widget_recent-posts" );
$widgets[1028] = array (
  'title' => '',
  'number' => 5,
);
update_option( "widget_recent-posts", $widgets );

$widgets = get_option( "widget_recent-comments" );
$widgets[1029] = array (
  'title' => '',
  'number' => 5,
);
update_option( "widget_recent-comments", $widgets );

$widgets = get_option( "widget_themify-feature-posts" );
$widgets[1030] = array (
  'title' => 'Recent Posts',
  'category' => '0',
  'show_count' => '3',
  'show_date' => 'on',
  'show_thumb' => 'on',
  'display' => 'none',
  'hide_title' => NULL,
  'thumb_width' => '50',
  'thumb_height' => '50',
  'excerpt_length' => '55',
  'orderby' => 'date',
  'order' => 'DESC',
);
update_option( "widget_themify-feature-posts", $widgets );

$widgets = get_option( "widget_themify-feature-posts" );
$widgets[1031] = array (
  'title' => 'Recent Posts',
  'category' => '0',
  'show_count' => '2',
  'show_date' => 'on',
  'show_thumb' => 'on',
  'display' => 'none',
  'hide_title' => NULL,
  'thumb_width' => '50',
  'thumb_height' => '50',
  'excerpt_length' => '55',
  'orderby' => 'date',
  'order' => 'DESC',
);
update_option( "widget_themify-feature-posts", $widgets );

$widgets = get_option( "widget_themify-social-links" );
$widgets[1032] = array (
  'title' => '',
  'show_link_name' => NULL,
  'open_new_window' => NULL,
  'icon_size' => 'icon-medium',
  'orientation' => 'horizontal',
);
update_option( "widget_themify-social-links", $widgets );

$widgets = get_option( "widget_themify-social-links" );
$widgets[1033] = array (
  'title' => '',
  'show_link_name' => NULL,
  'open_new_window' => NULL,
  'icon_size' => 'icon-medium',
  'orientation' => 'horizontal',
);
update_option( "widget_themify-social-links", $widgets );

$widgets = get_option( "widget_themify-social-links" );
$widgets[1034] = array (
  'title' => '',
  'show_link_name' => NULL,
  'open_new_window' => NULL,
  'icon_size' => 'icon-large',
  'orientation' => 'horizontal',
);
update_option( "widget_themify-social-links", $widgets );

$widgets = get_option( "widget_themify-twitter" );
$widgets[1035] = array (
  'title' => 'Latest Tweets',
  'username' => 'themify',
  'show_count' => '3',
  'hide_timestamp' => NULL,
  'show_follow' => 'on',
  'follow_text' => '→ Follow me',
  'include_retweets' => 'on',
  'exclude_replies' => NULL,
);
update_option( "widget_themify-twitter", $widgets );

$widgets = get_option( "widget_themify-twitter" );
$widgets[1036] = array (
  'title' => 'Latest Tweets',
  'username' => 'themify',
  'show_count' => '2',
  'hide_timestamp' => NULL,
  'show_follow' => NULL,
  'follow_text' => '→ Follow me',
  'include_retweets' => NULL,
  'exclude_replies' => NULL,
);
update_option( "widget_themify-twitter", $widgets );

$widgets = get_option( "widget_text" );
$widgets[1037] = array (
  'title' => 'Widget 1',
  'text' => 'Optional widget here',
  'filter' => false,
);
update_option( "widget_text", $widgets );

$widgets = get_option( "widget_text" );
$widgets[1038] = array (
  'title' => 'Widget 2',
  'text' => 'Display any widget here',
  'filter' => false,
);
update_option( "widget_text", $widgets );

$widgets = get_option( "widget_text" );
$widgets[1039] = array (
  'title' => 'Widget 3',
  'text' => 'For example, phone #: 123-333-4567',
  'filter' => false,
);
update_option( "widget_text", $widgets );

$widgets = get_option( "widget_archives" );
$widgets[1040] = array (
  'title' => '',
  'count' => 0,
  'dropdown' => 0,
);
update_option( "widget_archives", $widgets );

$widgets = get_option( "widget_meta" );
$widgets[1041] = array (
  'title' => '',
);
update_option( "widget_meta", $widgets );

$widgets = get_option( "widget_search" );
$widgets[1042] = array (
  'title' => '',
);
update_option( "widget_search", $widgets );

$widgets = get_option( "widget_text" );
$widgets[1043] = array (
  'title' => 'Widget 1',
  'text' => 'Optional widget here',
  'filter' => false,
);
update_option( "widget_text", $widgets );

$widgets = get_option( "widget_text" );
$widgets[1044] = array (
  'title' => 'Widget 2',
  'text' => 'Optional widget here',
  'filter' => false,
);
update_option( "widget_text", $widgets );

$widgets = get_option( "widget_text" );
$widgets[1045] = array (
  'title' => 'Widget 3',
  'text' => 'Optional widget here',
  'filter' => false,
);
update_option( "widget_text", $widgets );

$widgets = get_option( "widget_categories" );
$widgets[1046] = array (
  'title' => '',
  'count' => 0,
  'hierarchical' => 0,
  'dropdown' => 0,
);
update_option( "widget_categories", $widgets );

$widgets = get_option( "widget_recent-posts" );
$widgets[1047] = array (
  'title' => '',
  'number' => 5,
);
update_option( "widget_recent-posts", $widgets );

$widgets = get_option( "widget_recent-comments" );
$widgets[1048] = array (
  'title' => '',
  'number' => 5,
);
update_option( "widget_recent-comments", $widgets );

$widgets = get_option( "widget_themify-social-links" );
$widgets[1049] = array (
  'title' => '',
  'show_link_name' => NULL,
  'open_new_window' => NULL,
  'icon_size' => 'icon-medium',
  'orientation' => 'horizontal',
);
update_option( "widget_themify-social-links", $widgets );



$sidebars_widgets = array (
  'orphaned_widgets_1' => 
  array (
    0 => 'text-1002',
  ),
  'orphaned_widgets_5' => 
  array (
    0 => 'text-1003',
  ),
  'footer-widget-1' => 
  array (
    0 => 'text-1004',
  ),
  'footer-widget-2' => 
  array (
    0 => 'themify-social-links-1005',
  ),
  'footer-widget-3' => 
  array (
    0 => 'text-1006',
  ),
  'wp_inactive_widgets' => 
  array (
    0 => 'archives-1007',
    1 => 'meta-1008',
    2 => 'search-1009',
    3 => 'text-1010',
    4 => 'text-1011',
    5 => 'text-1012',
    6 => 'text-1013',
    7 => 'categories-1014',
    8 => 'recent-posts-1015',
    9 => 'recent-comments-1016',
    10 => 'themify-feature-posts-1017',
    11 => 'themify-social-links-1018',
    12 => 'themify-social-links-1019',
    13 => 'themify-social-links-1020',
    14 => 'themify-social-links-1021',
    15 => 'themify-twitter-1022',
    16 => 'themify-twitter-1023',
    17 => 'archives-1024',
    18 => 'meta-1025',
    19 => 'search-1026',
    20 => 'categories-1027',
    21 => 'recent-posts-1028',
    22 => 'recent-comments-1029',
    23 => 'themify-feature-posts-1030',
    24 => 'themify-feature-posts-1031',
    25 => 'themify-social-links-1032',
    26 => 'themify-social-links-1033',
    27 => 'themify-social-links-1034',
    28 => 'themify-twitter-1035',
    29 => 'themify-twitter-1036',
    30 => 'text-1037',
    31 => 'text-1038',
    32 => 'text-1039',
    33 => 'archives-1040',
    34 => 'meta-1041',
    35 => 'search-1042',
    36 => 'text-1043',
    37 => 'text-1044',
    38 => 'text-1045',
    39 => 'categories-1046',
    40 => 'recent-posts-1047',
    41 => 'recent-comments-1048',
  ),
  'footer-social-widget' => 
  array (
    0 => 'themify-social-links-1049',
  ),
); 
update_option( "sidebars_widgets", $sidebars_widgets );

$menu_locations = array();
$menu = get_terms( "nav_menu", array( "slug" => "wedding-page-menu" ) );
if( is_array( $menu ) && ! empty( $menu ) ) $menu_locations["main-nav"] = $menu[0]->term_id;
set_theme_mod( "nav_menu_locations", $menu_locations );


$homepage = get_posts( array( 'name' => 'home', 'post_type' => 'page' ) );
			if( is_array( $homepage ) && ! empty( $homepage ) ) {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $homepage[0]->ID );
			}
			
	ob_start(); ?>a:256:{s:15:"setting-favicon";s:0:"";s:23:"setting-custom_feed_url";s:0:"";s:19:"setting-header_html";s:0:"";s:19:"setting-footer_html";s:0:"";s:23:"setting-search_settings";s:0:"";s:16:"setting-page_404";s:1:"0";s:21:"setting-feed_settings";s:0:"";s:21:"setting-webfonts_list";s:11:"recommended";s:24:"setting-webfonts_subsets";s:0:"";s:22:"setting-default_layout";s:12:"sidebar-none";s:27:"setting-default_post_layout";s:9:"list-post";s:27:"setting-post_content_layout";s:0:"";s:23:"setting-disable_masonry";s:3:"yes";s:19:"setting-post_gutter";s:6:"gutter";s:30:"setting-default_layout_display";s:7:"content";s:25:"setting-default_more_text";s:4:"More";s:21:"setting-index_orderby";s:4:"date";s:19:"setting-index_order";s:4:"DESC";s:26:"setting-default_post_title";s:0:"";s:33:"setting-default_unlink_post_title";s:0:"";s:25:"setting-default_post_meta";s:0:"";s:32:"setting-default_post_meta_author";s:3:"yes";s:34:"setting-default_post_meta_category";s:0:"";s:33:"setting-default_post_meta_comment";s:0:"";s:29:"setting-default_post_meta_tag";s:0:"";s:25:"setting-default_post_date";s:0:"";s:30:"setting-default_media_position";s:5:"above";s:26:"setting-default_post_image";s:0:"";s:33:"setting-default_unlink_post_image";s:0:"";s:31:"setting-image_post_feature_size";s:5:"blank";s:24:"setting-image_post_width";s:0:"";s:25:"setting-image_post_height";s:0:"";s:32:"setting-default_page_post_layout";s:8:"sidebar1";s:37:"setting-default_page_post_layout_type";s:7:"classic";s:31:"setting-default_page_post_title";s:0:"";s:38:"setting-default_page_unlink_post_title";s:0:"";s:30:"setting-default_page_post_meta";s:2:"no";s:37:"setting-default_page_post_meta_author";s:3:"yes";s:39:"setting-default_page_post_meta_category";s:0:"";s:38:"setting-default_page_post_meta_comment";s:0:"";s:34:"setting-default_page_post_meta_tag";s:0:"";s:30:"setting-default_page_post_date";s:0:"";s:31:"setting-default_page_post_image";s:0:"";s:38:"setting-default_page_unlink_post_image";s:0:"";s:38:"setting-image_post_single_feature_size";s:5:"blank";s:31:"setting-image_post_single_width";s:0:"";s:32:"setting-image_post_single_height";s:0:"";s:27:"setting-default_page_layout";s:8:"sidebar1";s:23:"setting-hide_page_title";s:0:"";s:23:"setting-hide_page_image";s:0:"";s:33:"setting-page_featured_image_width";s:0:"";s:34:"setting-page_featured_image_height";s:0:"";s:38:"setting-default_portfolio_index_layout";s:12:"sidebar-none";s:43:"setting-default_portfolio_index_post_layout";s:5:"grid3";s:32:"setting-portfolio_content_layout";s:0:"";s:33:"setting-portfolio_disable_masonry";s:3:"yes";s:24:"setting-portfolio_gutter";s:6:"gutter";s:39:"setting-default_portfolio_index_display";s:4:"none";s:37:"setting-default_portfolio_index_title";s:0:"";s:49:"setting-default_portfolio_index_unlink_post_title";s:0:"";s:50:"setting-default_portfolio_index_post_meta_category";s:3:"yes";s:49:"setting-default_portfolio_index_unlink_post_image";s:3:"yes";s:48:"setting-default_portfolio_index_image_post_width";s:0:"";s:49:"setting-default_portfolio_index_image_post_height";s:0:"";s:54:"setting-default_portfolio_single_portfolio_layout_type";s:9:"fullwidth";s:38:"setting-default_portfolio_single_title";s:0:"";s:50:"setting-default_portfolio_single_unlink_post_title";s:0:"";s:51:"setting-default_portfolio_single_post_meta_category";s:0:"";s:50:"setting-default_portfolio_single_unlink_post_image";s:3:"yes";s:49:"setting-default_portfolio_single_image_post_width";s:0:"";s:50:"setting-default_portfolio_single_image_post_height";s:0:"";s:22:"themify_portfolio_slug";s:7:"project";s:53:"setting-customizer_responsive_design_tablet_landscape";s:4:"1024";s:43:"setting-customizer_responsive_design_tablet";s:3:"768";s:43:"setting-customizer_responsive_design_mobile";s:3:"480";s:33:"setting-mobile_menu_trigger_point";s:4:"1200";s:24:"setting-gallery_lightbox";s:8:"lightbox";s:26:"setting-page_builder_cache";s:2:"on";s:27:"setting-script_minification";s:7:"disable";s:27:"setting-page_builder_expiry";s:1:"2";s:20:"setting-color_design";s:7:"default";s:19:"setting-font_design";s:7:"default";s:21:"setting-header_design";s:17:"header-menu-split";s:28:"setting-exclude_site_tagline";s:2:"on";s:27:"setting-exclude_search_form";s:2:"on";s:19:"setting-exclude_rss";s:2:"on";s:30:"setting-exclude_header_widgets";s:2:"on";s:29:"setting-exclude_social_widget";s:2:"on";s:22:"setting-header_widgets";s:4:"none";s:21:"setting-footer_design";s:12:"footer-block";s:38:"setting-exclude_footer_menu_navigation";s:2:"on";s:22:"setting-footer_widgets";s:4:"none";s:30:"setting-footer_widget_position";s:3:"top";s:27:"setting-imagefilter_options";s:0:"";s:33:"setting-imagefilter_options_hover";s:0:"";s:27:"setting-imagefilter_applyto";s:12:"featuredonly";s:25:"setting-page_loader_color";s:0:"";s:24:"setting-page_loader_icon";s:0:"";s:29:"setting-color_animation_speed";s:1:"5";s:20:"setting-color_stop_1";s:0:"";s:20:"setting-color_stop_2";s:0:"";s:20:"setting-color_stop_3";s:0:"";s:20:"setting-color_stop_4";s:0:"";s:20:"setting-color_stop_5";s:0:"";s:20:"setting-color_stop_6";s:0:"";s:20:"setting-color_stop_7";s:0:"";s:29:"setting-relationship_taxonomy";s:8:"category";s:37:"setting-relationship_taxonomy_entries";s:1:"3";s:45:"setting-relationship_taxonomy_display_content";s:4:"none";s:30:"setting-single_slider_autoplay";s:3:"off";s:27:"setting-single_slider_speed";s:6:"normal";s:28:"setting-single_slider_effect";s:5:"slide";s:28:"setting-single_slider_height";s:4:"auto";s:18:"setting-more_posts";s:8:"infinite";s:19:"setting-entries_nav";s:8:"numbered";s:24:"setting-footer_text_left";s:0:"";s:25:"setting-footer_text_right";s:0:"";s:27:"setting-global_feature_size";s:5:"blank";s:22:"setting-link_icon_type";s:9:"font-icon";s:32:"setting-link_type_themify-link-0";s:10:"image-icon";s:33:"setting-link_title_themify-link-0";s:7:"Twitter";s:32:"setting-link_link_themify-link-0";s:0:"";s:31:"setting-link_img_themify-link-0";s:110:"https://themify.me/demo/themes/ultra-restaurant/wp-content/themes/themify-ultra/themify/img/social/twitter.png";s:32:"setting-link_type_themify-link-1";s:10:"image-icon";s:33:"setting-link_title_themify-link-1";s:8:"Facebook";s:32:"setting-link_link_themify-link-1";s:0:"";s:31:"setting-link_img_themify-link-1";s:111:"https://themify.me/demo/themes/ultra-restaurant/wp-content/themes/themify-ultra/themify/img/social/facebook.png";s:32:"setting-link_type_themify-link-2";s:10:"image-icon";s:33:"setting-link_title_themify-link-2";s:7:"Google+";s:32:"setting-link_link_themify-link-2";s:0:"";s:31:"setting-link_img_themify-link-2";s:114:"https://themify.me/demo/themes/ultra-restaurant/wp-content/themes/themify-ultra/themify/img/social/google-plus.png";s:32:"setting-link_type_themify-link-3";s:10:"image-icon";s:33:"setting-link_title_themify-link-3";s:7:"YouTube";s:32:"setting-link_link_themify-link-3";s:0:"";s:31:"setting-link_img_themify-link-3";s:110:"https://themify.me/demo/themes/ultra-restaurant/wp-content/themes/themify-ultra/themify/img/social/youtube.png";s:32:"setting-link_type_themify-link-4";s:10:"image-icon";s:33:"setting-link_title_themify-link-4";s:9:"Pinterest";s:32:"setting-link_link_themify-link-4";s:0:"";s:31:"setting-link_img_themify-link-4";s:112:"https://themify.me/demo/themes/ultra-restaurant/wp-content/themes/themify-ultra/themify/img/social/pinterest.png";s:32:"setting-link_type_themify-link-8";s:9:"font-icon";s:33:"setting-link_title_themify-link-8";s:7:"YouTube";s:32:"setting-link_link_themify-link-8";s:19:"https://themify.me/";s:33:"setting-link_ficon_themify-link-8";s:10:"fa-youtube";s:35:"setting-link_ficolor_themify-link-8";s:0:"";s:37:"setting-link_fibgcolor_themify-link-8";s:0:"";s:33:"setting-link_type_themify-link-10";s:9:"font-icon";s:34:"setting-link_title_themify-link-10";s:9:"Instagram";s:33:"setting-link_link_themify-link-10";s:19:"https://themify.me/";s:34:"setting-link_ficon_themify-link-10";s:12:"fa-instagram";s:36:"setting-link_ficolor_themify-link-10";s:0:"";s:38:"setting-link_fibgcolor_themify-link-10";s:0:"";s:32:"setting-link_type_themify-link-5";s:9:"font-icon";s:33:"setting-link_title_themify-link-5";s:7:"Twitter";s:32:"setting-link_link_themify-link-5";s:19:"https://themify.me/";s:33:"setting-link_ficon_themify-link-5";s:10:"fa-twitter";s:35:"setting-link_ficolor_themify-link-5";s:0:"";s:37:"setting-link_fibgcolor_themify-link-5";s:0:"";s:32:"setting-link_type_themify-link-6";s:9:"font-icon";s:33:"setting-link_title_themify-link-6";s:8:"Facebook";s:32:"setting-link_link_themify-link-6";s:19:"https://themify.me/";s:33:"setting-link_ficon_themify-link-6";s:11:"fa-facebook";s:35:"setting-link_ficolor_themify-link-6";s:0:"";s:37:"setting-link_fibgcolor_themify-link-6";s:0:"";s:22:"setting-link_field_ids";s:309:"{"themify-link-0":"themify-link-0","themify-link-1":"themify-link-1","themify-link-2":"themify-link-2","themify-link-3":"themify-link-3","themify-link-4":"themify-link-4","themify-link-8":"themify-link-8","themify-link-10":"themify-link-10","themify-link-5":"themify-link-5","themify-link-6":"themify-link-6"}";s:23:"setting-link_field_hash";s:2:"11";s:30:"setting-page_builder_is_active";s:6:"enable";s:41:"setting-page_builder_animation_appearance";s:0:"";s:42:"setting-page_builder_animation_parallax_bg";s:0:"";s:46:"setting-page_builder_animation_parallax_scroll";s:6:"mobile";s:55:"setting-page_builder_responsive_design_tablet_landscape";s:4:"1024";s:45:"setting-page_builder_responsive_design_tablet";s:3:"768";s:45:"setting-page_builder_responsive_design_mobile";s:3:"680";s:23:"setting-hooks_field_ids";s:2:"[]";s:27:"setting-custom_panel-editor";s:7:"default";s:27:"setting-custom_panel-author";s:7:"default";s:32:"setting-custom_panel-contributor";s:7:"default";s:33:"setting-custom_panel-shop_manager";s:7:"default";s:25:"setting-customizer-editor";s:7:"default";s:25:"setting-customizer-author";s:7:"default";s:30:"setting-customizer-contributor";s:7:"default";s:31:"setting-customizer-shop_manager";s:7:"default";s:22:"setting-backend-editor";s:7:"default";s:22:"setting-backend-author";s:7:"default";s:27:"setting-backend-contributor";s:7:"default";s:28:"setting-backend-shop_manager";s:7:"default";s:23:"setting-frontend-editor";s:7:"default";s:23:"setting-frontend-author";s:7:"default";s:28:"setting-frontend-contributor";s:7:"default";s:29:"setting-frontend-shop_manager";s:7:"default";s:16:"setting-fontello";s:0:"";s:4:"skin";s:100:"https://themify.me/demo/themes/ultra-wedding/wp-content/themes/themify-ultra/skins/wedding/style.css";s:27:"setting-search_exclude_post";s:0:"";s:31:"setting-search_settings_exclude";s:0:"";s:23:"setting-exclude_img_rss";s:0:"";s:19:"setting-post_filter";s:3:"yes";s:20:"setting-excerpt_more";s:0:"";s:35:"setting-default_display_date_inline";s:0:"";s:27:"setting-auto_featured_image";s:0:"";s:40:"setting-default_page_display_date_inline";s:0:"";s:22:"setting-comments_posts";s:0:"";s:23:"setting-post_author_box";s:0:"";s:24:"setting-post_nav_disable";s:0:"";s:25:"setting-post_nav_same_cat";s:0:"";s:22:"setting-comments_pages";s:0:"";s:29:"setting-portfolio_post_filter";s:3:"yes";s:29:"setting-portfolio_nav_disable";s:0:"";s:30:"setting-portfolio_nav_same_cat";s:0:"";s:33:"setting-disable_responsive_design";s:0:"";s:31:"setting-lightbox_content_images";s:0:"";s:18:"setting-cache_gzip";s:0:"";s:29:"setting-fixed_header_disabled";s:0:"";s:26:"setting-full_height_header";s:0:"";s:25:"setting-exclude_site_logo";s:0:"";s:31:"setting-exclude_menu_navigation";s:0:"";s:32:"setting-exclude_footer_site_logo";s:0:"";s:30:"setting-exclude_footer_widgets";s:0:"";s:28:"setting-exclude_footer_texts";s:0:"";s:27:"setting-exclude_footer_back";s:0:"";s:38:"setting-header_color_animation_enabled";s:0:"";s:38:"setting-footer_color_animation_enabled";s:0:"";s:40:"setting-relationship_taxonomy_hide_image";s:0:"";s:20:"setting-autoinfinite";s:0:"";s:29:"setting-footer_text_left_hide";s:0:"";s:30:"setting-footer_text_right_hide";s:0:"";s:38:"setting-page_builder_disable_shortcuts";s:0:"";s:34:"setting-page_builder_exc_accordion";s:0:"";s:28:"setting-page_builder_exc_box";s:0:"";s:32:"setting-page_builder_exc_buttons";s:0:"";s:32:"setting-page_builder_exc_callout";s:0:"";s:32:"setting-page_builder_exc_contact";s:0:"";s:34:"setting-page_builder_exc_countdown";s:0:"";s:32:"setting-page_builder_exc_counter";s:0:"";s:32:"setting-page_builder_exc_divider";s:0:"";s:38:"setting-page_builder_exc_fancy-heading";s:0:"";s:32:"setting-page_builder_exc_feature";s:0:"";s:32:"setting-page_builder_exc_gallery";s:0:"";s:34:"setting-page_builder_exc_highlight";s:0:"";s:29:"setting-page_builder_exc_icon";s:0:"";s:30:"setting-page_builder_exc_image";s:0:"";s:36:"setting-page_builder_exc_layout-part";s:0:"";s:28:"setting-page_builder_exc_map";s:0:"";s:33:"setting-page_builder_exc_maps-pro";s:0:"";s:29:"setting-page_builder_exc_menu";s:0:"";s:35:"setting-page_builder_exc_plain-text";s:0:"";s:34:"setting-page_builder_exc_portfolio";s:0:"";s:29:"setting-page_builder_exc_post";s:0:"";s:37:"setting-page_builder_exc_service-menu";s:0:"";s:31:"setting-page_builder_exc_slider";s:0:"";s:28:"setting-page_builder_exc_tab";s:0:"";s:43:"setting-page_builder_exc_testimonial-slider";s:0:"";s:36:"setting-page_builder_exc_testimonial";s:0:"";s:29:"setting-page_builder_exc_text";s:0:"";s:33:"setting-page_builder_exc_timeline";s:0:"";s:30:"setting-page_builder_exc_video";s:0:"";s:31:"setting-page_builder_exc_widget";s:0:"";s:35:"setting-page_builder_exc_widgetized";s:0:"";}<?php $themify_data = unserialize( ob_get_clean() );

	// fix the weird way "skin" is saved
	if( isset( $themify_data['skin'] ) ) {
		$parsed_skin = parse_url( $themify_data['skin'], PHP_URL_PATH );
		$basedir_skin = basename( dirname( $parsed_skin ) );
		$themify_data['skin'] = trailingslashit( get_template_directory_uri() ) . 'skins/' . $basedir_skin . '/style.css';
	}

	themify_set_data( $themify_data );
	
}
themify_do_demo_import();