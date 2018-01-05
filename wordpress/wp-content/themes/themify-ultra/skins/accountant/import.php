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
  'term_id' => 1,
  'name' => 'Uncategorized',
  'slug' => 'uncategorized',
  'term_group' => 0,
  'taxonomy' => 'category',
  'description' => '',
  'parent' => 0,
);
if( ERASEDEMO ) {
	themify_undo_import_term( $term );
} else {
	themify_import_term( $term );
}

$term = array (
  'term_id' => 16,
  'name' => 'Header Menu',
  'slug' => 'header-menu',
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

$term = array (
  'term_id' => 17,
  'name' => 'Footer Menu',
  'slug' => 'footer-menu',
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
  'ID' => 6490,
  'post_date' => '2017-05-17 14:56:32',
  'post_date_gmt' => '2017-05-17 14:56:32',
  'post_content' => 'Ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.

Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.

Laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit ess. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.',
  'post_title' => 'How to  Improving Teamwork',
  'post_excerpt' => '',
  'post_name' => 'improving-teamwork',
  'post_modified' => '2017-08-04 03:33:31',
  'post_modified_gmt' => '2017-08-04 03:33:31',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6490',
  'menu_order' => 0,
  'post_type' => 'post',
  'meta_input' => 
  array (
  ),
  'tax_input' => 
  array (
    'category' => 'uncategorized',
  ),
  'has_thumbnail' => true,
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 6476,
  'post_date' => '2017-05-09 13:20:33',
  'post_date_gmt' => '2017-05-09 13:20:33',
  'post_content' => 'Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus. At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates.

Magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?” repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis.',
  'post_title' => 'Tasks Management For Company Accountant',
  'post_excerpt' => '',
  'post_name' => 'tasks-management-company-accountant',
  'post_modified' => '2017-06-04 16:23:18',
  'post_modified_gmt' => '2017-06-04 16:23:18',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6476',
  'menu_order' => 0,
  'post_type' => 'post',
  'meta_input' => 
  array (
  ),
  'tax_input' => 
  array (
    'category' => 'uncategorized',
  ),
  'has_thumbnail' => true,
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 6473,
  'post_date' => '2017-05-08 12:18:20',
  'post_date_gmt' => '2017-05-08 12:18:20',
  'post_content' => 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur.

Inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur.',
  'post_title' => '5 Tips To Avoid Failure In Accounting',
  'post_excerpt' => '',
  'post_name' => '5-tips-avoid-failure-accounting',
  'post_modified' => '2017-06-04 16:20:01',
  'post_modified_gmt' => '2017-06-04 16:20:01',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6473',
  'menu_order' => 0,
  'post_type' => 'post',
  'meta_input' => 
  array (
  ),
  'tax_input' => 
  array (
    'category' => 'uncategorized',
  ),
  'has_thumbnail' => true,
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 6470,
  'post_date' => '2017-05-07 15:14:34',
  'post_date_gmt' => '2017-05-07 15:14:34',
  'post_content' => 'Ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.

Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.

Laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit ess. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.',
  'post_title' => 'Investing Small In The Big Market',
  'post_excerpt' => '',
  'post_name' => 'investing-small-big-market',
  'post_modified' => '2017-06-04 16:17:47',
  'post_modified_gmt' => '2017-06-04 16:17:47',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6470',
  'menu_order' => 0,
  'post_type' => 'post',
  'meta_input' => 
  array (
  ),
  'tax_input' => 
  array (
    'category' => 'uncategorized',
  ),
  'has_thumbnail' => true,
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 6466,
  'post_date' => '2017-05-06 17:10:54',
  'post_date_gmt' => '2017-05-06 17:10:54',
  'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.<span id="more-5716"></span>

Magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur.

Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt neque porro quisquam.',
  'post_title' => 'Accounting Process and Economic Knowledge',
  'post_excerpt' => '',
  'post_name' => 'accounting-process-economic-knowledge',
  'post_modified' => '2017-06-04 16:31:10',
  'post_modified_gmt' => '2017-06-04 16:31:10',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6466',
  'menu_order' => 0,
  'post_type' => 'post',
  'meta_input' => 
  array (
  ),
  'tax_input' => 
  array (
    'category' => 'uncategorized',
  ),
  'has_thumbnail' => true,
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 6462,
  'post_date' => '2017-05-05 11:00:18',
  'post_date_gmt' => '2017-05-05 11:00:18',
  'post_content' => 'Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit.recusandae. Vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.

At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores.',
  'post_title' => 'Simple Step To Get Maximum Target',
  'post_excerpt' => '',
  'post_name' => 'simple-step-get-maximum-target',
  'post_modified' => '2017-06-04 16:03:21',
  'post_modified_gmt' => '2017-06-04 16:03:21',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6462',
  'menu_order' => 0,
  'post_type' => 'post',
  'meta_input' => 
  array (
  ),
  'tax_input' => 
  array (
    'category' => 'uncategorized',
  ),
  'has_thumbnail' => true,
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 6459,
  'post_date' => '2017-05-03 16:54:08',
  'post_date_gmt' => '2017-05-03 16:54:08',
  'post_content' => 'Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus. Vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate.

At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.',
  'post_title' => '3 Main Elements for Audit Success',
  'post_excerpt' => '',
  'post_name' => '3-main-elements-audit-success',
  'post_modified' => '2017-06-04 16:00:01',
  'post_modified_gmt' => '2017-06-04 16:00:01',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6459',
  'menu_order' => 0,
  'post_type' => 'post',
  'meta_input' => 
  array (
  ),
  'tax_input' => 
  array (
    'category' => 'uncategorized',
  ),
  'has_thumbnail' => true,
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 6456,
  'post_date' => '2017-05-01 15:50:02',
  'post_date_gmt' => '2017-05-01 15:50:02',
  'post_content' => 'Vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit.

Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.

At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio.',
  'post_title' => '4 Ways to Improve Workforce Diversity',
  'post_excerpt' => '',
  'post_name' => '4-ways-improve-workforce-diversity',
  'post_modified' => '2017-06-04 15:53:49',
  'post_modified_gmt' => '2017-06-04 15:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6456',
  'menu_order' => 0,
  'post_type' => 'post',
  'meta_input' => 
  array (
  ),
  'tax_input' => 
  array (
    'category' => 'uncategorized',
  ),
  'has_thumbnail' => true,
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 6479,
  'post_date' => '2017-05-11 16:26:22',
  'post_date_gmt' => '2017-05-11 16:26:22',
  'post_content' => 'Totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur.

Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur. Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae.',
  'post_title' => 'Accountant Skills Through Digital Learning',
  'post_excerpt' => '',
  'post_name' => 'accountant-skills-digital-learning',
  'post_modified' => '2017-08-04 03:35:16',
  'post_modified_gmt' => '2017-08-04 03:35:16',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6479',
  'menu_order' => 0,
  'post_type' => 'post',
  'meta_input' => 
  array (
  ),
  'tax_input' => 
  array (
    'category' => 'uncategorized',
  ),
  'has_thumbnail' => true,
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 6486,
  'post_date' => '2017-05-13 18:36:34',
  'post_date_gmt' => '2017-05-13 18:36:34',
  'post_content' => 'Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus. Et quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus omnis.

At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.',
  'post_title' => '5 Tips for Effectively Managing Teams',
  'post_excerpt' => '',
  'post_name' => '5-tips-effectively-managing-teams',
  'post_modified' => '2017-06-05 15:47:09',
  'post_modified_gmt' => '2017-06-05 15:47:09',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6486',
  'menu_order' => 0,
  'post_type' => 'post',
  'meta_input' => 
  array (
  ),
  'tax_input' => 
  array (
    'category' => 'uncategorized',
  ),
  'has_thumbnail' => true,
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 6483,
  'post_date' => '2017-05-15 15:34:02',
  'post_date_gmt' => '2017-05-15 15:34:02',
  'post_content' => 'Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat epellendus.  Ton provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae.

<span id="more-133"></span>Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.

Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? Vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate.',
  'post_title' => 'Leading The Way To Financial Success',
  'post_excerpt' => '',
  'post_name' => 'leading-way-financial-success',
  'post_modified' => '2017-06-05 15:46:57',
  'post_modified_gmt' => '2017-06-05 15:46:57',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6483',
  'menu_order' => 0,
  'post_type' => 'post',
  'meta_input' => 
  array (
  ),
  'tax_input' => 
  array (
    'category' => 'uncategorized',
  ),
  'has_thumbnail' => true,
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 6493,
  'post_date' => '2017-05-18 15:06:20',
  'post_date_gmt' => '2017-05-18 15:06:20',
  'post_content' => 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur.

inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur.',
  'post_title' => 'Five Year Record Evaluation',
  'post_excerpt' => '',
  'post_name' => 'five-year-record-evaluation',
  'post_modified' => '2017-08-04 03:33:20',
  'post_modified_gmt' => '2017-08-04 03:33:20',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6493',
  'menu_order' => 0,
  'post_type' => 'post',
  'meta_input' => 
  array (
  ),
  'tax_input' => 
  array (
    'category' => 'uncategorized',
  ),
  'has_thumbnail' => true,
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 6498,
  'post_date' => '2017-05-10 15:20:10',
  'post_date_gmt' => '2017-05-10 15:20:10',
  'post_content' => 'Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus. At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates.

Magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?”  repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis.',
  'post_title' => 'Tips for Success in Accounting and Finance',
  'post_excerpt' => '',
  'post_name' => 'tips-success-accounting-finance',
  'post_modified' => '2017-08-04 03:35:01',
  'post_modified_gmt' => '2017-08-04 03:35:01',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6498',
  'menu_order' => 0,
  'post_type' => 'post',
  'meta_input' => 
  array (
  ),
  'tax_input' => 
  array (
    'category' => 'uncategorized',
  ),
  'has_thumbnail' => true,
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 6502,
  'post_date' => '2017-05-22 12:33:25',
  'post_date_gmt' => '2017-05-22 12:33:25',
  'post_content' => 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias at vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis.

quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.

Aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus.',
  'post_title' => '7 Tips to Become a Successful Accountant',
  'post_excerpt' => '',
  'post_name' => '7-tips-become-successful-accountant',
  'post_modified' => '2017-06-05 15:42:17',
  'post_modified_gmt' => '2017-06-05 15:42:17',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6502',
  'menu_order' => 0,
  'post_type' => 'post',
  'meta_input' => 
  array (
  ),
  'tax_input' => 
  array (
    'category' => 'uncategorized',
  ),
  'has_thumbnail' => true,
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 6505,
  'post_date' => '2017-05-14 14:42:37',
  'post_date_gmt' => '2017-05-14 14:42:37',
  'post_content' => 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est. Laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.

Commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur.

Perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.',
  'post_title' => 'Financial Management With Accountant',
  'post_excerpt' => '',
  'post_name' => 'financial-management-with-accountant',
  'post_modified' => '2017-08-04 03:34:20',
  'post_modified_gmt' => '2017-08-04 03:34:20',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6505',
  'menu_order' => 0,
  'post_type' => 'post',
  'meta_input' => 
  array (
  ),
  'tax_input' => 
  array (
    'category' => 'uncategorized',
  ),
  'has_thumbnail' => true,
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 6371,
  'post_date' => '2017-03-23 11:05:14',
  'post_date_gmt' => '2017-03-23 11:05:14',
  'post_content' => '',
  'post_title' => 'Home - Accountant',
  'post_excerpt' => '',
  'post_name' => 'home-accountant',
  'post_modified' => '2017-08-04 20:33:14',
  'post_modified_gmt' => '2017-08-04 20:33:14',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?page_id=6371',
  'menu_order' => 0,
  'post_type' => 'page',
  'meta_input' => 
  array (
    'page_layout' => 'sidebar-none',
    'content_width' => 'full_width',
    'hide_page_title' => 'yes',
    'builder_switch_frontend' => '0',
    '_themify_builder_settings_json' => '[{\\"row_order\\":\\"0\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[{\\"mod_name\\":\\"fancy-heading\\",\\"mod_settings\\":{\\"heading\\":\\"The First Finance Research in New York\\",\\"sub_heading\\":\\"Tax Services & Returns Rectification\\",\\"heading_tag\\":\\"h1\\",\\"text_alignment\\":\\"themify-text-center\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_image-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\n\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"main_margin_top_unit\\":\\"px\\",\\"main_margin_bottom_unit\\":\\"px\\",\\"font_family_subheading\\":\\"default\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\",\\"sub_margin_top_unit\\":\\"px\\",\\"sub_margin_bottom_unit\\":\\"px\\",\\"animation_effect\\":\\"fadeInDown\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"mod_name\\":\\"buttons\\",\\"mod_settings\\":{\\"buttons_size\\":\\"normal\\",\\"buttons_style\\":\\"outline\\",\\"content_button\\":[{\\"label\\":\\"Book Appointment\\",\\"link\\":\\"#appointment\\",\\"link_options\\":\\"newtab\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"button_color_bg\\":\\"yellow\\",\\"button_single_style\\":\\"default\\"}],\\"background_image-gradient-angle\\":\\"180\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"center\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_top\\":\\"2\\",\\"margin_top_unit\\":\\"em\\",\\"margin_right_unit\\":\\"em\\",\\"margin_bottom_unit\\":\\"em\\",\\"margin_left_unit\\":\\"em\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"text_decoration\\":\\"underline\\",\\"link_checkbox_padding_apply_all\\":\\"padding\\",\\"link_checkbox_margin_apply_all\\":\\"margin\\",\\"link_border_top_style\\":\\"solid\\",\\"link_border_right_style\\":\\"solid\\",\\"link_border_bottom_style\\":\\"solid\\",\\"link_border_left_style\\":\\"solid\\",\\"link_checkbox_border_apply_all\\":\\"border\\",\\"animation_effect\\":\\"fadeInLeft\\",\\"animation_effect_delay\\":\\".2\\"}}],\\"styling\\":[]}],\\"styling\\":{\\"custom_css_row\\":\\"vertical-divider\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/03/home-banner.jpg\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_repeat\\":\\"fullcover\\",\\"background_position\\":\\"center-bottom\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"21456b_0.78\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"ffffff_1.00\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"center\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"14.5\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom\\":\\"14\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"%\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"breakpoint_mobile\\":{\\"text_decoration\\":\\"underline\\",\\"padding_bottom\\":\\"70\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"1\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[{\\"mod_name\\":\\"fancy-heading\\",\\"mod_settings\\":{\\"heading\\":\\"Accounting Services\\",\\"sub_heading\\":\\"Learn more what we serve\\",\\"heading_tag\\":\\"h2\\",\\"text_alignment\\":\\"themify-text-center\\",\\"background_image-type\\":\\"image\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"2.75\\",\\"margin_bottom_unit\\":\\"em\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"|\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"main_margin_top_unit\\":\\"px\\",\\"main_margin_bottom_unit\\":\\"px\\",\\"font_family_subheading\\":\\"default\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\",\\"sub_margin_top_unit\\":\\"px\\",\\"sub_margin_bottom_unit\\":\\"px\\",\\"breakpoint_tablet\\":{\\"padding_bottom\\":\\"0\\",\\"margin_bottom\\":\\"0\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\"},\\"breakpoint_mobile\\":{\\"margin_bottom\\":\\"30\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\"}}},{\\"row_order\\":\\"1\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col3-1 first\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-center\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/computerized.png\\",\\"title_image\\":\\"Computerized Bookkeeping & Accounting\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"animation_effect\\":\\"zoomIn\\",\\"background_image-gradient-angle\\":\\"180\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"breakpoint_tablet\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"40\\",\\"margin_top\\":\\"0\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"},\\"breakpoint_mobile\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"0\\",\\"margin_top\\":\\"0\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"}}}],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}},{\\"column_order\\":\\"1\\",\\"grid_class\\":\\"col3-1\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-center\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/incorporation.png\\",\\"title_image\\":\\"Business Registration / Incorporation\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"animation_effect\\":\\"zoomIn\\",\\"animation_effect_delay\\":\\".2\\",\\"background_image-gradient-angle\\":\\"180\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"breakpoint_tablet\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"40\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"}}}],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}},{\\"column_order\\":\\"2\\",\\"grid_class\\":\\"col3-1 last\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-center\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/tax.png\\",\\"title_image\\":\\"Tax Services & Returns Rectification\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"animation_effect\\":\\"zoomIn\\",\\"animation_effect_delay\\":\\".4\\",\\"background_image-gradient-angle\\":\\"180\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"breakpoint_tablet\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"40\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"}}}],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}}],\\"gutter\\":\\"gutter-none\\",\\"column_alignment\\":\\"\\",\\"col_tablet\\":\\"tablet3-1\\",\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"border-first\\",\\"breakpoint_tablet\\":{\\"background_type\\":\\"image\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_top\\":\\"0\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_mobile\\":{\\"background_type\\":\\"image\\",\\"text_decoration\\":\\"underline\\",\\"padding_bottom\\":\\"0\\",\\"margin_bottom\\":\\"0\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"2\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col3-1 first\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-center\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/cra-audit.png\\",\\"title_image\\":\\"Assistance with CRA audits & matters\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"animation_effect\\":\\"zoomIn\\",\\"animation_effect_delay\\":\\".6\\",\\"background_image-gradient-angle\\":\\"180\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"breakpoint_tablet\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"40\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"}}}],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}},{\\"column_order\\":\\"1\\",\\"grid_class\\":\\"col3-1\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-center\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/business-registration.png\\",\\"title_image\\":\\"Business Registration / Incorporation\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"animation_effect\\":\\"zoomIn\\",\\"animation_effect_delay\\":\\".8\\",\\"background_image-gradient-angle\\":\\"180\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"breakpoint_tablet\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"40\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"}}}],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}},{\\"column_order\\":\\"2\\",\\"grid_class\\":\\"col3-1 last\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-center\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/pay-roll.png\\",\\"title_image\\":\\"Employee Payroll Services, GST/HST & W.S.I.B\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"animation_effect\\":\\"zoomIn\\",\\"animation_effect_delay\\":\\"1\\",\\"background_image-gradient-angle\\":\\"180\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"breakpoint_tablet\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"40\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"}}}],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}}],\\"gutter\\":\\"gutter-none\\",\\"column_alignment\\":\\"\\",\\"col_tablet\\":\\"tablet3-1\\",\\"styling\\":[]}],\\"styling\\":[]}],\\"styling\\":{\\"custom_css_row\\":\\"accounting-services accounting-services-border\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"padding_top\\":\\"7.5\\",\\"padding_top_unit\\":\\"%\\",\\"padding_bottom\\":\\"8\\",\\"padding_bottom_unit\\":\\"%\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"breakpoint_tablet_landscape\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"70\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_tablet\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"70\\",\\"padding_bottom\\":\\"30\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_mobile\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"2\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[{\\"mod_name\\":\\"fancy-heading\\",\\"mod_settings\\":{\\"heading\\":\\"Contact us Today and Receive a Free Tax Service.\\",\\"sub_heading\\":\\"First Time Free Consultation\\",\\"heading_tag\\":\\"h1\\",\\"text_alignment\\":\\"themify-text-center\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_image-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\n\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"main_margin_top_unit\\":\\"px\\",\\"main_margin_bottom_unit\\":\\"px\\",\\"font_family_subheading\\":\\"default\\",\\"font_size_subheading\\":\\"1.1\\",\\"font_size_subheading_unit\\":\\"em\\",\\"line_height_subheading_unit\\":\\"px\\",\\"sub_margin_top_unit\\":\\"px\\",\\"sub_margin_bottom_unit\\":\\"px\\",\\"animation_effect\\":\\"bounce\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"mod_name\\":\\"buttons\\",\\"mod_settings\\":{\\"buttons_size\\":\\"normal\\",\\"buttons_style\\":\\"outline\\",\\"content_button\\":[{\\"label\\":\\"Book Appointment\\",\\"link\\":\\"#appointment\\",\\"link_options\\":\\"newtab\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"button_color_bg\\":\\"yellow\\",\\"button_single_style\\":\\"default\\"}],\\"background_image-gradient-angle\\":\\"180\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"center\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_top\\":\\"1.2\\",\\"margin_top_unit\\":\\"em\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"text_decoration\\":\\"underline\\",\\"link_checkbox_padding_apply_all\\":\\"padding\\",\\"link_checkbox_margin_apply_all\\":\\"margin\\",\\"link_border_top_style\\":\\"solid\\",\\"link_border_right_style\\":\\"solid\\",\\"link_border_bottom_style\\":\\"solid\\",\\"link_border_left_style\\":\\"solid\\",\\"link_checkbox_border_apply_all\\":\\"border\\",\\"animation_effect\\":\\"bounce\\",\\"animation_effect_delay\\":\\".2\\"}}],\\"styling\\":[]}],\\"styling\\":{\\"background_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/03/servicebg.jpg\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_repeat\\":\\"fullcover\\",\\"background_position\\":\\"center-top\\",\\"background_color\\":\\"22436c_1.00\\",\\"cover_color\\":\\"20476b_0.85\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"font_color\\":\\"ffffff_1.00\\",\\"padding_top\\":\\"10\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom\\":\\"11.35\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left_unit\\":\\"%\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"breakpoint_mobile\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"50\\",\\"padding_bottom\\":\\"60\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"3\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col4-1 first\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-center\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/consultation-icon.png\\",\\"title_image\\":\\"Consultation and Registration of HST, Payroll, etc.\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"background_image-gradient-angle\\":\\"180\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"29\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"5\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom\\":\\"25\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"5\\",\\"padding_left_unit\\":\\"%\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"breakpoint_tablet_landscape\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"none\\",\\"border_right_style\\":\\"none\\",\\"border_bottom_style\\":\\"none\\",\\"border_left_style\\":\\"none\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"},\\"breakpoint_mobile\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"padding_bottom\\":\\"40\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"}}}],\\"styling\\":{\\"breakpoint_tablet_landscape\\":{\\"background_type\\":\\"image\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\"}}},{\\"column_order\\":\\"1\\",\\"grid_class\\":\\"col4-1\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-center\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/03/tex-returns.png\\",\\"title_image\\":\\"Review and rectify past year tax returns\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"background_image-type\\":\\"image\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"padding_top\\":\\"29\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"5\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom\\":\\"25\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"5\\",\\"padding_left_unit\\":\\"%\\",\\"checkbox_padding_apply_all\\":\\"|\\",\\"margin_top_unit\\":\\"%\\",\\"margin_right\\":\\"5\\",\\"margin_right_unit\\":\\"%\\",\\"margin_bottom_unit\\":\\"%\\",\\"margin_left\\":\\"5\\",\\"margin_left_unit\\":\\"%\\",\\"checkbox_margin_apply_all\\":\\"|\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_family_title\\":\\"default\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_family_caption\\":\\"default\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"breakpoint_tablet_landscape\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"none\\",\\"border_right_style\\":\\"none\\",\\"border_bottom_style\\":\\"none\\",\\"border_left_style\\":\\"none\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"},\\"breakpoint_mobile\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"padding_bottom\\":\\"40\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"}}}],\\"styling\\":{\\"breakpoint_tablet_landscape\\":{\\"background_type\\":\\"image\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"column_order\\":\\"2\\",\\"grid_class\\":\\"col4-1\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-center\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/storage-data-icon.png\\",\\"title_image\\":\\"Storage of data and files electronically\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"background_image-gradient-angle\\":\\"180\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"29\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"5\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom\\":\\"25\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"5\\",\\"padding_left_unit\\":\\"%\\",\\"margin_top_unit\\":\\"%\\",\\"margin_right\\":\\"5\\",\\"margin_right_unit\\":\\"%\\",\\"margin_bottom_unit\\":\\"%\\",\\"margin_left\\":\\"5\\",\\"margin_left_unit\\":\\"%\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"breakpoint_tablet_landscape\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"none\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"},\\"breakpoint_mobile\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"padding_bottom\\":\\"40\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"}}}],\\"styling\\":{\\"breakpoint_tablet_landscape\\":{\\"background_type\\":\\"image\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"column_order\\":\\"3\\",\\"grid_class\\":\\"col4-1 last\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-center\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/initials-tax-icon.png\\",\\"title_image\\":\\"Initial Tax Return, Address change, and more...\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"background_image-gradient-angle\\":\\"180\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"29\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"5\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom\\":\\"25\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"5\\",\\"padding_left_unit\\":\\"%\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"breakpoint_tablet_landscape\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"none\\",\\"border_right_style\\":\\"none\\",\\"border_bottom_style\\":\\"none\\",\\"border_left_style\\":\\"none\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"},\\"breakpoint_mobile\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"padding_bottom\\":\\"40\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"}}}],\\"styling\\":{\\"breakpoint_tablet_landscape\\":{\\"background_type\\":\\"image\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}}],\\"gutter\\":\\"gutter-none\\",\\"column_alignment\\":\\"\\",\\"col_tablet\\":\\"tablet4-1\\",\\"styling\\":{\\"custom_css_row\\":\\"free-consultation \\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_color\\":\\"ffffff_1.00\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"dddddd_1.00\\",\\"border_bottom_width\\":\\"1\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\"}},{\\"row_order\\":\\"4\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[{\\"mod_name\\":\\"fancy-heading\\",\\"mod_settings\\":{\\"heading\\":\\"Latest News\\",\\"sub_heading\\":\\"General Accounting News & Tips\\",\\"heading_tag\\":\\"h2\\",\\"text_alignment\\":\\"themify-text-center\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_image-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\n\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"2.35\\",\\"margin_bottom_unit\\":\\"em\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"|\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"main_margin_top_unit\\":\\"px\\",\\"main_margin_bottom_unit\\":\\"px\\",\\"font_family_subheading\\":\\"default\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\",\\"sub_margin_top_unit\\":\\"px\\",\\"sub_margin_bottom_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"mod_name\\":\\"post\\",\\"mod_settings\\":{\\"layout_post\\":\\"grid4\\",\\"post_type_post\\":\\"post\\",\\"type_query_post\\":\\"category\\",\\"category_post\\":\\"0|multiple\\",\\"post_tag_post\\":\\"0|multiple\\",\\"post_content_layout\\":\\"polaroid\\",\\"post_filter\\":\\"no\\",\\"disable_masonry\\":\\"no\\",\\"post_gutter\\":\\"no-gutter\\",\\"post_per_page_post\\":\\"4\\",\\"order_post\\":\\"desc\\",\\"orderby_post\\":\\"date\\",\\"display_post\\":\\"excerpt\\",\\"img_height_post\\":\\"358\\",\\"hide_post_meta_post\\":\\"yes\\",\\"hide_page_nav_post\\":\\"yes\\",\\"css_post\\":\\"latest-news\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"font_style_regular\\":\\"none\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_meta_unit\\":\\"px\\",\\"line_height_meta_unit\\":\\"px\\",\\"font_size_date_unit\\":\\"px\\",\\"line_height_date_unit\\":\\"px\\",\\"font_size_content_unit\\":\\"px\\",\\"line_height_content_unit\\":\\"px\\"}}],\\"styling\\":[]}],\\"styling\\":{\\"row_width\\":\\"fullwidth-content\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"f4f4f4_1.00\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"7.55\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"breakpoint_tablet\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"padding_bottom\\":\\"30\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_mobile\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"padding_bottom\\":\\"40\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"5\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[{\\"mod_name\\":\\"fancy-heading\\",\\"mod_settings\\":{\\"heading\\":\\"Put yourself in the know and discover the most exclusive upcoming events before anyone else.\\",\\"sub_heading\\":\\"Sign up to our Newsletter\\",\\"heading_tag\\":\\"h3\\",\\"text_alignment\\":\\"themify-text-center\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"main_margin_top_unit\\":\\"px\\",\\"main_margin_bottom_unit\\":\\"px\\",\\"font_family_subheading\\":\\"default\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\",\\"sub_margin_top_unit\\":\\"px\\",\\"sub_margin_bottom_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"mod_name\\":\\"plain-text\\",\\"mod_settings\\":{\\"plain_text\\":\\"<form id=\\\\\\\\\\\\\\"mc4wp-form-1\\\\\\\\\\\\\\" class=\\\\\\\\\\\\\\"mc4wp-form mc4wp-form-6369\\\\\\\\\\\\\\" method=\\\\\\\\\\\\\\"post\\\\\\\\\\\\\\" data-id=\\\\\\\\\\\\\\"6369\\\\\\\\\\\\\\" data-name=\\\\\\\\\\\\\\"Homepage Signup\\\\\\\\\\\\\\"><div class=\\\\\\\\\\\\\\"mc4wp-form-fields\\\\\\\\\\\\\\"><p>\\\\n\\\\t<input type=\\\\\\\\\\\\\\"email\\\\\\\\\\\\\\" name=\\\\\\\\\\\\\\"EMAIL\\\\\\\\\\\\\\" placeholder=\\\\\\\\\\\\\\"Email Address\\\\\\\\\\\\\\" required=\\\\\\\\\\\\\\"\\\\\\\\\\\\\\">  <input type=\\\\\\\\\\\\\\"submit\\\\\\\\\\\\\\" value=\\\\\\\\\\\\\\"Subscribe\\\\\\\\\\\\\\">\\\\n</p><div style=\\\\\\\\\\\\\\"display: none;\\\\\\\\\\\\\\"><input type=\\\\\\\\\\\\\\"text\\\\\\\\\\\\\\" name=\\\\\\\\\\\\\\"_mc4wp_honeypot\\\\\\\\\\\\\\" value=\\\\\\\\\\\\\\"\\\\\\\\\\\\\\" tabindex=\\\\\\\\\\\\\\"-1\\\\\\\\\\\\\\" autocomplete=\\\\\\\\\\\\\\"off\\\\\\\\\\\\\\"></div><input type=\\\\\\\\\\\\\\"hidden\\\\\\\\\\\\\\" name=\\\\\\\\\\\\\\"_mc4wp_timestamp\\\\\\\\\\\\\\" value=\\\\\\\\\\\\\\"1499754893\\\\\\\\\\\\\\"><input type=\\\\\\\\\\\\\\"hidden\\\\\\\\\\\\\\" name=\\\\\\\\\\\\\\"_mc4wp_form_id\\\\\\\\\\\\\\" value=\\\\\\\\\\\\\\"6369\\\\\\\\\\\\\\"><input type=\\\\\\\\\\\\\\"hidden\\\\\\\\\\\\\\" name=\\\\\\\\\\\\\\"_mc4wp_form_element_id\\\\\\\\\\\\\\" value=\\\\\\\\\\\\\\"mc4wp-form-1\\\\\\\\\\\\\\"></div><div class=\\\\\\\\\\\\\\"mc4wp-response\\\\\\\\\\\\\\"></div></form>\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"animation_effect\\":\\"shake\\"}}],\\"styling\\":[]}],\\"gutter\\":\\"gutter-none\\",\\"styling\\":{\\"custom_css_row\\":\\"row-newsletter\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"padding_top\\":\\"6.5\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom\\":\\"8\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left_unit\\":\\"%\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}},{\\"row_order\\":\\"6\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[{\\"mod_name\\":\\"maps-pro\\",\\"mod_settings\\":{\\"map_display_type\\":\\"dynamic\\",\\"w_map\\":\\"100\\",\\"unit_w\\":\\"%\\",\\"w_map_static\\":\\"500\\",\\"h_map\\":\\"815\\",\\"unit_h\\":\\"px\\",\\"type_map\\":\\"ROADMAP\\",\\"style_map\\":\\"cool-grey\\",\\"scrollwheel_map\\":\\"disable\\",\\"draggable_map\\":\\"enable\\",\\"disable_map_ui\\":\\"yes\\",\\"map_link\\":\\"|\\",\\"zoom_map\\":\\"16\\",\\"map_center\\":\\"6320 Canoga Avenue, Suite 1430 Woodland Hills, CA 91367\\",\\"markers\\":[{\\"address\\":\\"6320 Canoga Avenue, Suite 1430 Woodland Hills, CA 19367\\",\\"title\\":\\"Our Shop\\",\\"image\\":\\"https://themify.me/demo/themes/themes/wp-content/uploads/addon-samples/shop-map-marker.png\\"}],\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]}],\\"styling\\":{\\"row_width\\":\\"fullwidth-content\\",\\"row_height\\":\\"\\",\\"custom_css_row\\":\\"\\",\\"row_anchor\\":\\"\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_mobile\\":\\"show\\",\\"animation_effect\\":\\"\\",\\"animation_effect_delay\\":\\"\\",\\"animation_effect_repeat\\":\\"\\",\\"custom_parallax_scroll_speed\\":\\"\\",\\"custom_parallax_scroll_zindex\\":\\"\\"}},{\\"row_order\\":\\"7\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col3-2 first\\",\\"modules\\":[{\\"mod_name\\":\\"text\\",\\"mod_settings\\":{\\"content_text\\":\\"<h6>Send us a Message</h6>\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_h1\\":\\"default\\",\\"font_size_h1_unit\\":\\"px\\",\\"line_height_h1_unit\\":\\"px\\",\\"h1_margin_top_unit\\":\\"px\\",\\"h1_margin_bottom_unit\\":\\"px\\",\\"font_family_h2\\":\\"default\\",\\"font_size_h2_unit\\":\\"px\\",\\"line_height_h2_unit\\":\\"px\\",\\"h2_margin_top_unit\\":\\"px\\",\\"h2_margin_bottom_unit\\":\\"px\\",\\"font_family_h3\\":\\"default\\",\\"font_size_h3_unit\\":\\"px\\",\\"line_height_h3_unit\\":\\"px\\",\\"h3_margin_top_unit\\":\\"px\\",\\"h3_margin_bottom_unit\\":\\"px\\",\\"font_family_h4\\":\\"default\\",\\"font_size_h4_unit\\":\\"px\\",\\"line_height_h4_unit\\":\\"px\\",\\"h4_margin_top_unit\\":\\"px\\",\\"h4_margin_bottom_unit\\":\\"px\\",\\"font_family_h5\\":\\"default\\",\\"font_size_h5_unit\\":\\"px\\",\\"line_height_h5_unit\\":\\"px\\",\\"h5_margin_top_unit\\":\\"px\\",\\"h5_margin_bottom_unit\\":\\"px\\",\\"font_family_h6\\":\\"default\\",\\"font_size_h6_unit\\":\\"px\\",\\"line_height_h6_unit\\":\\"px\\",\\"h6_margin_top_unit\\":\\"px\\",\\"h6_margin_bottom_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"mod_name\\":\\"contact\\",\\"mod_settings\\":{\\"layout_contact\\":\\"style1\\",\\"mail_contact\\":\\"testdevelopern1@gmail.com\\",\\"field_name_label\\":\\"Your Name\\",\\"field_email_label\\":\\"Email Address\\",\\"field_subject_label\\":\\"Subject\\",\\"field_subject_active\\":\\"|\\",\\"field_captcha_active\\":\\"|\\",\\"field_message_label\\":\\"Message\\",\\"field_sendcopy_label\\":\\"Send a copy to myself\\",\\"field_sendcopy_active\\":\\"|\\",\\"field_send_label\\":\\"Send\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_labels\\":\\"default\\",\\"font_size_labels_unit\\":\\"px\\",\\"font_family_inputs\\":\\"default\\",\\"font_size_inputs_unit\\":\\"px\\",\\"font_family_send\\":\\"default\\",\\"font_size_send_unit\\":\\"px\\",\\"font_family_success_message\\":\\"default\\",\\"text_align_success_message_left\\":\\"left\\",\\"text_align_success_message_center\\":\\"center\\",\\"text_align_success_message_right\\":\\"right\\",\\"text_align_success_message_justify\\":\\"justify\\",\\"padding_top_success_message_unit\\":\\"px\\",\\"padding_right_success_message_unit\\":\\"px\\",\\"padding_bottom_success_message_unit\\":\\"px\\",\\"padding_left_success_message_unit\\":\\"px\\",\\"margin_top_success_message_unit\\":\\"px\\",\\"margin_right_success_message_unit\\":\\"px\\",\\"margin_bottom_success_message_unit\\":\\"px\\",\\"margin_left_success_message_unit\\":\\"px\\",\\"font_family_error_message\\":\\"default\\",\\"text_align_error_message_left\\":\\"left\\",\\"text_align_error_message_center\\":\\"center\\",\\"text_align_error_message_right\\":\\"right\\",\\"text_align_error_message_justify\\":\\"justify\\",\\"padding_top_error_message_unit\\":\\"px\\",\\"padding_right_error_message_unit\\":\\"px\\",\\"padding_bottom_error_message_unit\\":\\"px\\",\\"padding_left_error_message_unit\\":\\"px\\",\\"margin_top_error_message_unit\\":\\"px\\",\\"margin_right_error_message_unit\\":\\"px\\",\\"margin_bottom_error_message_unit\\":\\"px\\",\\"margin_left_error_message_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"ffffff_1.00\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"4.5\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"6\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom\\":\\"4.5\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"6\\",\\"padding_left_unit\\":\\"%\\",\\"border_top_color\\":\\"dddddd_1.00\\",\\"border_top_width\\":\\"1\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"dddddd_1.00\\",\\"border_right_width\\":\\"1\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"dddddd_1.00\\",\\"border_bottom_width\\":\\"1\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"dddddd_1.00\\",\\"border_left_width\\":\\"1\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}},{\\"column_order\\":\\"1\\",\\"grid_class\\":\\"col3-1 last\\",\\"modules\\":[{\\"mod_name\\":\\"text\\",\\"mod_settings\\":{\\"content_text\\":\\"<h6>Contact Information</h6>\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_h1\\":\\"default\\",\\"font_size_h1_unit\\":\\"px\\",\\"line_height_h1_unit\\":\\"px\\",\\"h1_margin_top_unit\\":\\"px\\",\\"h1_margin_bottom_unit\\":\\"px\\",\\"font_family_h2\\":\\"default\\",\\"font_size_h2_unit\\":\\"px\\",\\"line_height_h2_unit\\":\\"px\\",\\"h2_margin_top_unit\\":\\"px\\",\\"h2_margin_bottom_unit\\":\\"px\\",\\"font_family_h3\\":\\"default\\",\\"font_size_h3_unit\\":\\"px\\",\\"line_height_h3_unit\\":\\"px\\",\\"h3_margin_top_unit\\":\\"px\\",\\"h3_margin_bottom_unit\\":\\"px\\",\\"font_family_h4\\":\\"default\\",\\"font_size_h4_unit\\":\\"px\\",\\"line_height_h4_unit\\":\\"px\\",\\"h4_margin_top_unit\\":\\"px\\",\\"h4_margin_bottom_unit\\":\\"px\\",\\"font_family_h5\\":\\"default\\",\\"font_size_h5_unit\\":\\"px\\",\\"line_height_h5_unit\\":\\"px\\",\\"h5_margin_top_unit\\":\\"px\\",\\"h5_margin_bottom_unit\\":\\"px\\",\\"font_family_h6\\":\\"default\\",\\"font_size_h6_unit\\":\\"px\\",\\"line_height_h6_unit\\":\\"px\\",\\"h6_margin_top_unit\\":\\"px\\",\\"h6_margin_bottom_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"mod_name\\":\\"icon\\",\\"mod_settings\\":{\\"icon_size\\":\\"normal\\",\\"icon_style\\":\\"none\\",\\"icon_arrangement\\":\\"icon_horizontal\\",\\"content_icon\\":[{\\"icon\\":\\"fa-map-marker\\",\\"icon_color_bg\\":\\"blue\\",\\"label\\":\\"6320 Canoga Avenue, Suite 1430 Woodland Hills, CA 91367\\",\\"link_options\\":\\"regular\\"}],\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_color_icon\\":\\"6fb0ed_1.00\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"mod_name\\":\\"icon\\",\\"mod_settings\\":{\\"icon_size\\":\\"normal\\",\\"icon_style\\":\\"none\\",\\"icon_arrangement\\":\\"icon_horizontal\\",\\"content_icon\\":[{\\"icon\\":\\"fa-phone\\",\\"icon_color_bg\\":\\"blue\\",\\"label\\":\\"(818)657-7220\\",\\"link_options\\":\\"regular\\"}],\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_color_icon\\":\\"6fb0ed_1.00\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"mod_name\\":\\"icon\\",\\"mod_settings\\":{\\"icon_size\\":\\"normal\\",\\"icon_style\\":\\"none\\",\\"icon_arrangement\\":\\"icon_horizontal\\",\\"content_icon\\":[{\\"icon\\":\\"fa-envelope-o\\",\\"icon_color_bg\\":\\"blue\\",\\"label\\":\\"corporate@accap.com\\",\\"link_options\\":\\"regular\\"}],\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_color_icon\\":\\"6fb0ed_1.00\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"22436c_1.00\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"ffffff_1.00\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"4.5\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"3.5\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom\\":\\"4.5\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"3.5\\",\\"padding_left_unit\\":\\"%\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}}],\\"gutter\\":\\"gutter-none\\",\\"column_alignment\\":\\"\\",\\"col_tablet\\":\\"tablet-full\\",\\"styling\\":{\\"custom_css_row\\":\\"hp-contact\\",\\"row_anchor\\":\\"appointment\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_top\\":\\"-185\\",\\"margin_bottom\\":\\"5\\",\\"margin_bottom_unit\\":\\"em\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}},{\\"row_order\\":\\"8\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[],\\"styling\\":[]}],\\"styling\\":[]}]',
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
  'ID' => 6376,
  'post_date' => '2017-03-23 11:13:10',
  'post_date_gmt' => '2017-03-23 11:13:10',
  'post_content' => '',
  'post_title' => 'About Us',
  'post_excerpt' => '',
  'post_name' => 'about-us',
  'post_modified' => '2017-07-12 14:51:13',
  'post_modified_gmt' => '2017-07-12 14:51:13',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?page_id=6376',
  'menu_order' => 0,
  'post_type' => 'page',
  'meta_input' => 
  array (
    'page_layout' => 'sidebar-none',
    'content_width' => 'full_width',
    'hide_page_title' => 'yes',
    'builder_switch_frontend' => '0',
    '_themify_builder_settings_json' => '[{\\"row_order\\":\\"0\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[{\\"mod_name\\":\\"fancy-heading\\",\\"mod_settings\\":{\\"heading\\":\\"About Us\\",\\"sub_heading\\":\\"Who We Are\\",\\"heading_tag\\":\\"h1\\",\\"text_alignment\\":\\"themify-text-center\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"main_margin_top_unit\\":\\"px\\",\\"main_margin_bottom_unit\\":\\"px\\",\\"font_family_subheading\\":\\"default\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\",\\"sub_margin_top_unit\\":\\"px\\",\\"sub_margin_bottom_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"mod_name\\":\\"text\\",\\"mod_settings\\":{\\"content_text\\":\\"<p>Accounting is a premier accounting firm which provides taxation, accounting and bookkeeping services in the Greater Toronto Area.</p>\\",\\"background_image-type\\":\\"image\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_family_h1\\":\\"default\\",\\"font_size_h1_unit\\":\\"px\\",\\"line_height_h1_unit\\":\\"px\\",\\"h1_margin_top_unit\\":\\"px\\",\\"h1_margin_bottom_unit\\":\\"px\\",\\"font_family_h2\\":\\"default\\",\\"font_size_h2_unit\\":\\"px\\",\\"line_height_h2_unit\\":\\"px\\",\\"h2_margin_top_unit\\":\\"px\\",\\"h2_margin_bottom_unit\\":\\"px\\",\\"font_family_h3\\":\\"default\\",\\"font_size_h3_unit\\":\\"px\\",\\"line_height_h3_unit\\":\\"px\\",\\"h3_margin_top_unit\\":\\"px\\",\\"h3_margin_bottom_unit\\":\\"px\\",\\"font_family_h4\\":\\"default\\",\\"font_size_h4_unit\\":\\"px\\",\\"line_height_h4_unit\\":\\"px\\",\\"h4_margin_top_unit\\":\\"px\\",\\"h4_margin_bottom_unit\\":\\"px\\",\\"font_family_h5\\":\\"default\\",\\"font_size_h5_unit\\":\\"px\\",\\"line_height_h5_unit\\":\\"px\\",\\"h5_margin_top_unit\\":\\"px\\",\\"h5_margin_bottom_unit\\":\\"px\\",\\"font_family_h6\\":\\"default\\",\\"font_size_h6_unit\\":\\"px\\",\\"line_height_h6_unit\\":\\"px\\",\\"h6_margin_top_unit\\":\\"px\\",\\"h6_margin_bottom_unit\\":\\"px\\",\\"breakpoint_tablet_landscape\\":{\\"text_decoration\\":\\"underline\\",\\"column_divider_style\\":\\"solid\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_right\\":\\"20\\",\\"margin_left\\":\\"20\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_tablet\\":{\\"text_decoration\\":\\"underline\\",\\"column_divider_style\\":\\"solid\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_right\\":\\"30\\",\\"margin_left\\":\\"30\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_mobile\\":{\\"text_decoration\\":\\"underline\\",\\"column_divider_style\\":\\"solid\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_right\\":\\"40\\",\\"margin_left\\":\\"40\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}}],\\"styling\\":[]}],\\"styling\\":{\\"row_width\\":\\"fullwidth-content\\",\\"row_height\\":\\"\\",\\"custom_css_row\\":\\"vertical-divider row-newsletter input-bg-white\\",\\"row_anchor\\":\\"\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/03/about-us.jpg\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"fullcover\\",\\"background_position\\":\\"center-bottom\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"21456b_0.78\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"ffffff_1.00\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"center\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"14.5\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom\\":\\"14\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"%\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_mobile\\":\\"show\\",\\"animation_effect\\":\\"\\",\\"animation_effect_delay\\":\\"\\",\\"animation_effect_repeat\\":\\"\\",\\"custom_parallax_scroll_speed\\":\\"\\",\\"custom_parallax_scroll_zindex\\":\\"\\"}},{\\"row_order\\":\\"1\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col3-1 first\\",\\"modules\\":[{\\"mod_name\\":\\"text\\",\\"mod_settings\\":{\\"content_text\\":\\"<p>We provide our clients with professional tax planning, e-file tax returns, book-keeping, accounting and business advisory services.</p>\\",\\"add_css_text\\":\\"dropcap\\",\\"column_divider_style\\":\\"solid\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}],\\"styling\\":[]},{\\"column_order\\":\\"1\\",\\"grid_class\\":\\"col3-1\\",\\"modules\\":[{\\"mod_name\\":\\"text\\",\\"mod_settings\\":{\\"content_text\\":\\"<p>As a Registered Professional Accountant, we continue to have the honour and privilege of serving individuals and corporations since last 18 years. We pride ourselves on providing the highest level of customer service.</p><p>I place a great deal of emphasis on small businesses and sole-proprietorship and believe that by providing affordable quality accounting and tax services, it will help grow their business and prosper and in turn, help our local economy grow and prosper as well.</p>\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_h1\\":\\"default\\",\\"font_size_h1_unit\\":\\"px\\",\\"line_height_h1_unit\\":\\"px\\",\\"h1_margin_top_unit\\":\\"px\\",\\"h1_margin_bottom_unit\\":\\"px\\",\\"font_family_h2\\":\\"default\\",\\"font_size_h2_unit\\":\\"px\\",\\"line_height_h2_unit\\":\\"px\\",\\"h2_margin_top_unit\\":\\"px\\",\\"h2_margin_bottom_unit\\":\\"px\\",\\"font_family_h3\\":\\"default\\",\\"font_size_h3_unit\\":\\"px\\",\\"line_height_h3_unit\\":\\"px\\",\\"h3_margin_top_unit\\":\\"px\\",\\"h3_margin_bottom_unit\\":\\"px\\",\\"font_family_h4\\":\\"default\\",\\"font_size_h4_unit\\":\\"px\\",\\"line_height_h4_unit\\":\\"px\\",\\"h4_margin_top_unit\\":\\"px\\",\\"h4_margin_bottom_unit\\":\\"px\\",\\"font_family_h5\\":\\"default\\",\\"font_size_h5_unit\\":\\"px\\",\\"line_height_h5_unit\\":\\"px\\",\\"h5_margin_top_unit\\":\\"px\\",\\"h5_margin_bottom_unit\\":\\"px\\",\\"font_family_h6\\":\\"default\\",\\"font_size_h6_unit\\":\\"px\\",\\"line_height_h6_unit\\":\\"px\\",\\"h6_margin_top_unit\\":\\"px\\",\\"h6_margin_bottom_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]},{\\"column_order\\":\\"2\\",\\"grid_class\\":\\"col3-1 last\\",\\"modules\\":[{\\"mod_name\\":\\"text\\",\\"mod_settings\\":{\\"content_text\\":\\"<p>My personal goal and promise to you is to provide you a very high level of professional services and ensure you receive every dollar owed to you by Taxation Authorities.</p><p>My staff and I take away all the complexity and stress by employing fully advanced book-keeping and accounting system. Furthermore, by e-Filing your Tax Returns, you get the benefit of earlier refund giving you peace of mind and time to enjoy the things that you love most about your business.</p>\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_h1\\":\\"default\\",\\"font_size_h1_unit\\":\\"px\\",\\"line_height_h1_unit\\":\\"px\\",\\"h1_margin_top_unit\\":\\"px\\",\\"h1_margin_bottom_unit\\":\\"px\\",\\"font_family_h2\\":\\"default\\",\\"font_size_h2_unit\\":\\"px\\",\\"line_height_h2_unit\\":\\"px\\",\\"h2_margin_top_unit\\":\\"px\\",\\"h2_margin_bottom_unit\\":\\"px\\",\\"font_family_h3\\":\\"default\\",\\"font_size_h3_unit\\":\\"px\\",\\"line_height_h3_unit\\":\\"px\\",\\"h3_margin_top_unit\\":\\"px\\",\\"h3_margin_bottom_unit\\":\\"px\\",\\"font_family_h4\\":\\"default\\",\\"font_size_h4_unit\\":\\"px\\",\\"line_height_h4_unit\\":\\"px\\",\\"h4_margin_top_unit\\":\\"px\\",\\"h4_margin_bottom_unit\\":\\"px\\",\\"font_family_h5\\":\\"default\\",\\"font_size_h5_unit\\":\\"px\\",\\"line_height_h5_unit\\":\\"px\\",\\"h5_margin_top_unit\\":\\"px\\",\\"h5_margin_bottom_unit\\":\\"px\\",\\"font_family_h6\\":\\"default\\",\\"font_size_h6_unit\\":\\"px\\",\\"line_height_h6_unit\\":\\"px\\",\\"h6_margin_top_unit\\":\\"px\\",\\"h6_margin_bottom_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]}],\\"column_alignment\\":\\"\\",\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"11\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom\\":\\"5.5\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"%\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"breakpoint_tablet_landscape\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"10\\",\\"padding_top_unit\\":\\"%\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"2\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[{\\"mod_name\\":\\"testimonial-slider\\",\\"mod_settings\\":{\\"layout_testimonial\\":\\"image-top\\",\\"tab_content_testimonial\\":[{\\"title_testimonial\\":\\"I am glad I switched over to Electro Accounting. In my very first year, I found I could claim expenses I never thought I could. Even better, I got a refund. Something I never got dealing with so called “Professionals”.\\",\\"person_picture_testimonial\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/03/jonathan-doe.png\\",\\"person_name_testimonial\\":\\"Jonathan Doe\\",\\"person_position_testimonial\\":\\"Director of Technical Support at Shopify\\"},{\\"title_testimonial\\":\\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent dapibus sodales accumsan. Maecenas vulputate erat, sed varius ipsum velit sed odio.\\",\\"person_picture_testimonial\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/03/person-1.png\\",\\"person_name_testimonial\\":\\"Dennise King\\",\\"person_position_testimonial\\":\\"CEO\\",\\"company_testimonial\\":\\"Landmark Corporation\\"},{\\"title_testimonial\\":\\"Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Similique suscipit, id ducimus illum corporis pariatur.\\",\\"person_picture_testimonial\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/03/person-2.png\\",\\"person_name_testimonial\\":\\"Robert Gill\\",\\"person_position_testimonial\\":\\"Vice President\\",\\"company_testimonial\\":\\"Wheel Spain Ltd\\"},{\\"title_testimonial\\":\\"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae.\\",\\"person_picture_testimonial\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/03/person-3.png\\",\\"person_name_testimonial\\":\\"Juli Smith\\",\\"person_position_testimonial\\":\\"Manager\\",\\"company_testimonial\\":\\"County Road Association\\"}],\\"visible_opt_slider\\":\\"1\\",\\"auto_scroll_opt_slider\\":\\"off\\",\\"scroll_opt_slider\\":\\"1\\",\\"speed_opt_slider\\":\\"normal\\",\\"effect_slider\\":\\"cover-fade\\",\\"pause_on_hover_slider\\":\\"resume\\",\\"wrap_slider\\":\\"yes\\",\\"show_nav_slider\\":\\"no\\",\\"show_arrow_slider\\":\\"yes\\",\\"show_arrow_buttons_vertical\\":\\"vertical\\",\\"height_slider\\":\\"variable\\",\\"css_testimonial\\":\\"testimonials\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_title\\":\\"default\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_family_content\\":\\"default\\",\\"font_size_content_unit\\":\\"px\\",\\"line_height_content_unit\\":\\"px\\",\\"animation_effect\\":\\"fadeInUp\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]}],\\"styling\\":{\\"row_width\\":\\"fullwidth-content\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"22436c_1.00\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"ffffff_1.00\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"5.5\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom\\":\\"5.5\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"%\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"breakpoint_tablet_landscape\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"70\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_tablet\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_mobile\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"padding_bottom\\":\\"40\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"3\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[{\\"mod_name\\":\\"fancy-heading\\",\\"mod_settings\\":{\\"heading\\":\\"Our Team\\",\\"sub_heading\\":\\"People together to celebrate an occasio\\",\\"heading_tag\\":\\"h2\\",\\"text_alignment\\":\\"themify-text-center\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"main_margin_top_unit\\":\\"px\\",\\"main_margin_bottom_unit\\":\\"px\\",\\"font_family_subheading\\":\\"default\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\",\\"sub_margin_top_unit\\":\\"px\\",\\"sub_margin_bottom_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]}],\\"styling\\":{\\"row_width\\":\\"fullwidth-content\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"5.5\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom\\":\\"5.5\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"%\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"breakpoint_tablet_landscape\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"padding_bottom\\":\\"40\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_tablet\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"padding_bottom\\":\\"30\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_mobile\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"padding_bottom\\":\\"80\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"4\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col4-1 first\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-top\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/03/chris-evans.jpg\\",\\"appearance_image\\":\\"|\\",\\"auto_fullwidth\\":\\"1\\",\\"title_image\\":\\"Chris Evans\\",\\"param_image\\":\\"regular\\",\\"image_zoom_icon\\":\\"|\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"caption_image\\":\\"CEO\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_title\\":\\"default\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_family_caption\\":\\"default\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]},{\\"column_order\\":\\"1\\",\\"grid_class\\":\\"col4-1\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-top\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/03/jonathan-doe.jpg\\",\\"appearance_image\\":\\"|\\",\\"auto_fullwidth\\":\\"1\\",\\"title_image\\":\\"Jonathan Doe\\",\\"param_image\\":\\"regular\\",\\"image_zoom_icon\\":\\"|\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"caption_image\\":\\"Senior Accountant\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_title\\":\\"default\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_family_caption\\":\\"default\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]},{\\"column_order\\":\\"2\\",\\"grid_class\\":\\"col4-1\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-top\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/03/marlene-jane.jpg\\",\\"appearance_image\\":\\"|\\",\\"auto_fullwidth\\":\\"1\\",\\"title_image\\":\\"Marlene Jane\\",\\"param_image\\":\\"regular\\",\\"image_zoom_icon\\":\\"|\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"caption_image\\":\\"Senior Analyst\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_title\\":\\"default\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_family_caption\\":\\"default\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]},{\\"column_order\\":\\"3\\",\\"grid_class\\":\\"col4-1 last\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-top\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/03/parlone-sabastian.jpg\\",\\"appearance_image\\":\\"|\\",\\"auto_fullwidth\\":\\"1\\",\\"title_image\\":\\"Parlone Sabastian\\",\\"param_image\\":\\"regular\\",\\"image_zoom_icon\\":\\"|\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"caption_image\\":\\"Business Analyst\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_title\\":\\"default\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_family_caption\\":\\"default\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]}],\\"gutter\\":\\"gutter-none\\",\\"column_alignment\\":\\"\\",\\"col_tablet\\":\\"tablet4-2\\",\\"styling\\":{\\"row_width\\":\\"fullwidth-content\\",\\"custom_css_row\\":\\"team-member\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}},{\\"row_order\\":\\"5\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[{\\"mod_name\\":\\"fancy-heading\\",\\"mod_settings\\":{\\"heading\\":\\"Contact us Today and Receive a Free Tax Service.\\",\\"sub_heading\\":\\"Let\\\\\\\\\\\'s talk about how we can help you\\",\\"heading_tag\\":\\"h1\\",\\"text_alignment\\":\\"themify-text-center\\",\\"background_image-type\\":\\"image\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"main_margin_top_unit\\":\\"px\\",\\"main_margin_bottom_unit\\":\\"px\\",\\"font_family_subheading\\":\\"default\\",\\"font_size_subheading\\":\\"1.1\\",\\"font_size_subheading_unit\\":\\"em\\",\\"line_height_subheading_unit\\":\\"px\\",\\"sub_margin_top_unit\\":\\"px\\",\\"sub_margin_bottom_unit\\":\\"px\\",\\"breakpoint_tablet\\":{\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_right\\":\\"20\\",\\"margin_left\\":\\"20\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\"},\\"breakpoint_mobile\\":{\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_right\\":\\"20\\",\\"margin_left\\":\\"20\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\"}}},{\\"mod_name\\":\\"buttons\\",\\"mod_settings\\":{\\"buttons_size\\":\\"normal\\",\\"buttons_style\\":\\"squared\\",\\"content_button\\":[{\\"label\\":\\"Contact Us\\",\\"link\\":\\"/contant-us\\",\\"link_options\\":\\"regular\\",\\"button_color_bg\\":\\"brown\\"}],\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top\\":\\"1.3\\",\\"margin_top_unit\\":\\"em\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"|\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"padding_top_link_unit\\":\\"px\\",\\"padding_right_link_unit\\":\\"px\\",\\"padding_bottom_link_unit\\":\\"px\\",\\"padding_left_link_unit\\":\\"px\\",\\"checkbox_link_padding_apply_all\\":\\"padding\\",\\"checkbox_link_padding_apply_all_padding\\":\\"padding\\",\\"link_margin_top_unit\\":\\"px\\",\\"link_margin_right_unit\\":\\"px\\",\\"link_margin_bottom_unit\\":\\"px\\",\\"link_margin_left_unit\\":\\"px\\",\\"link_checkbox_margin_apply_all\\":\\"margin\\",\\"link_checkbox_margin_apply_all_margin\\":\\"margin\\",\\"link_border_top_style\\":\\"solid\\",\\"link_border_right_style\\":\\"solid\\",\\"link_border_bottom_style\\":\\"solid\\",\\"link_border_left_style\\":\\"solid\\",\\"link_checkbox_border_apply_all\\":\\"border\\",\\"link_checkbox_border_apply_all_border\\":\\"border\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]}],\\"gutter\\":\\"gutter-none\\",\\"styling\\":{\\"row_width\\":\\"fullwidth-content\\",\\"custom_css_row\\":\\"about-team\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"f4f4f4_1.00\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"center\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"6.15\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom\\":\\"6.85\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"%\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"breakpoint_tablet\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"padding_bottom\\":\\"60\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_mobile\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"50\\",\\"padding_bottom\\":\\"60\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"6\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[],\\"styling\\":[]}],\\"styling\\":[]}]',
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
  'ID' => 6381,
  'post_date' => '2017-03-23 11:34:48',
  'post_date_gmt' => '2017-03-23 11:34:48',
  'post_content' => '',
  'post_title' => 'Services',
  'post_excerpt' => '',
  'post_name' => 'services',
  'post_modified' => '2017-07-12 14:54:41',
  'post_modified_gmt' => '2017-07-12 14:54:41',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?page_id=6381',
  'menu_order' => 0,
  'post_type' => 'page',
  'meta_input' => 
  array (
    'page_layout' => 'sidebar-none',
    'content_width' => 'full_width',
    'hide_page_title' => 'yes',
    'builder_switch_frontend' => '0',
    '_themify_builder_settings_json' => '[{\\"row_order\\":\\"0\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[{\\"mod_name\\":\\"fancy-heading\\",\\"mod_settings\\":{\\"heading\\":\\"About Us\\",\\"sub_heading\\":\\"What We Do\\",\\"heading_tag\\":\\"h1\\",\\"text_alignment\\":\\"themify-text-center\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_image-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\n\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"main_margin_top_unit\\":\\"px\\",\\"main_margin_bottom_unit\\":\\"px\\",\\"font_family_subheading\\":\\"default\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\",\\"sub_margin_top_unit\\":\\"px\\",\\"sub_margin_bottom_unit\\":\\"px\\",\\"animation_effect\\":\\"fadeInDown\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"mod_name\\":\\"text\\",\\"mod_settings\\":{\\"content_text\\":\\"<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer bibendum aliquam consectetur Vestibulum et hendrerit quam, vel mollis diam.</p>\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_h1\\":\\"default\\",\\"font_size_h1_unit\\":\\"px\\",\\"line_height_h1_unit\\":\\"px\\",\\"h1_margin_top_unit\\":\\"px\\",\\"h1_margin_bottom_unit\\":\\"px\\",\\"font_family_h2\\":\\"default\\",\\"font_size_h2_unit\\":\\"px\\",\\"line_height_h2_unit\\":\\"px\\",\\"h2_margin_top_unit\\":\\"px\\",\\"h2_margin_bottom_unit\\":\\"px\\",\\"font_family_h3\\":\\"default\\",\\"font_size_h3_unit\\":\\"px\\",\\"line_height_h3_unit\\":\\"px\\",\\"h3_margin_top_unit\\":\\"px\\",\\"h3_margin_bottom_unit\\":\\"px\\",\\"font_family_h4\\":\\"default\\",\\"font_size_h4_unit\\":\\"px\\",\\"line_height_h4_unit\\":\\"px\\",\\"h4_margin_top_unit\\":\\"px\\",\\"h4_margin_bottom_unit\\":\\"px\\",\\"font_family_h5\\":\\"default\\",\\"font_size_h5_unit\\":\\"px\\",\\"line_height_h5_unit\\":\\"px\\",\\"h5_margin_top_unit\\":\\"px\\",\\"h5_margin_bottom_unit\\":\\"px\\",\\"font_family_h6\\":\\"default\\",\\"font_size_h6_unit\\":\\"px\\",\\"line_height_h6_unit\\":\\"px\\",\\"h6_margin_top_unit\\":\\"px\\",\\"h6_margin_bottom_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]}],\\"styling\\":{\\"custom_css_row\\":\\"vertical-divider\\",\\"background_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/03/servicebg.jpg\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_repeat\\":\\"fullcover\\",\\"background_position\\":\\"center-center\\",\\"cover_color\\":\\"21456b_0.78\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"font_color\\":\\"ffffff_1.00\\",\\"text_align\\":\\"center\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"14.5\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom\\":\\"14\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left_unit\\":\\"%\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}},{\\"row_order\\":\\"1\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[{\\"mod_name\\":\\"fancy-heading\\",\\"mod_settings\\":{\\"heading\\":\\"Tax Compliance & Planning\\",\\"heading_tag\\":\\"h2\\",\\"text_alignment\\":\\"themify-text-center\\",\\"background_image-type\\":\\"image\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"main_margin_top_unit\\":\\"px\\",\\"main_margin_bottom_unit\\":\\"px\\",\\"font_family_subheading\\":\\"default\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\",\\"sub_margin_top_unit\\":\\"px\\",\\"sub_margin_bottom_unit\\":\\"px\\",\\"breakpoint_mobile\\":{\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_top\\":\\"20\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\"}}},{\\"row_order\\":\\"1\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col3-1 first\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-center\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/computerized.png\\",\\"title_image\\":\\"Computerized Bookkeeping & Accounting\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"caption_image\\":\\"Duis a metus ac purus consequat tincidunt eu quis nulla. Nam pharetra libero nulla, sit amet maximus ligula pretium a.\\",\\"animation_effect\\":\\"zoomIn\\",\\"background_image-gradient-angle\\":\\"180\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"breakpoint_tablet\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"30\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"}}}],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}},{\\"column_order\\":\\"1\\",\\"grid_class\\":\\"col3-1\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-center\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/incorporation.png\\",\\"title_image\\":\\"Business Registration / Incorporation\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"caption_image\\":\\"Duis a metus ac purus consequat tincidunt eu quis nulla. Nam pharetra libero nulla, sit amet maximus ligula pretium a.\\",\\"animation_effect\\":\\"zoomIn\\",\\"animation_effect_delay\\":\\".2\\",\\"background_image-gradient-angle\\":\\"180\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"breakpoint_tablet\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"30\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"}}}],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}},{\\"column_order\\":\\"2\\",\\"grid_class\\":\\"col3-1 last\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-center\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/tax.png\\",\\"title_image\\":\\"Tax Services & Returns Rectification\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"caption_image\\":\\"Duis a metus ac purus consequat tincidunt eu quis nulla. Nam pharetra libero nulla, sit amet maximus ligula pretium a.\\",\\"animation_effect\\":\\"zoomIn\\",\\"animation_effect_delay\\":\\".4\\",\\"background_image-gradient-angle\\":\\"180\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"breakpoint_tablet\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"30\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"}}}],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}}],\\"gutter\\":\\"gutter-none\\",\\"column_alignment\\":\\"\\",\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"border-first\\"}},{\\"row_order\\":\\"2\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col3-1 first\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-center\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/cra-audit.png\\",\\"title_image\\":\\"Assistance with CRA audits & matters\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"caption_image\\":\\"Duis a metus ac purus consequat tincidunt eu quis nulla. Nam pharetra libero nulla, sit amet maximus ligula pretium a.\\",\\"animation_effect\\":\\"zoomIn\\",\\"animation_effect_delay\\":\\".6\\",\\"background_image-gradient-angle\\":\\"180\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"breakpoint_tablet\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"30\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"}}}],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}},{\\"column_order\\":\\"1\\",\\"grid_class\\":\\"col3-1\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-center\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/business-registration.png\\",\\"title_image\\":\\"Business Registration / Incorporation\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"caption_image\\":\\"Duis a metus ac purus consequat tincidunt eu quis nulla. Nam pharetra libero nulla, sit amet maximus ligula pretium a.\\",\\"animation_effect\\":\\"zoomIn\\",\\"animation_effect_delay\\":\\".8\\",\\"background_image-gradient-angle\\":\\"180\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"breakpoint_tablet\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"30\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"}}}],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}},{\\"column_order\\":\\"2\\",\\"grid_class\\":\\"col3-1 last\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-center\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/pay-roll.png\\",\\"title_image\\":\\"Employee Payroll Services, GST/HST & W.S.I.B\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"caption_image\\":\\"Duis a metus ac purus consequat tincidunt eu quis nulla. Nam pharetra libero nulla, sit amet maximus ligula pretium a.\\",\\"animation_effect\\":\\"zoomIn\\",\\"animation_effect_delay\\":\\"1\\",\\"background_image-gradient-angle\\":\\"180\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"breakpoint_tablet\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"30\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"}}}],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}}],\\"gutter\\":\\"gutter-none\\",\\"column_alignment\\":\\"\\",\\"styling\\":[]}],\\"styling\\":[]}],\\"styling\\":{\\"custom_css_row\\":\\"accounting-services compliance-planning\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"7.5\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"9\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"breakpoint_tablet_landscape\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"70\\",\\"padding_bottom\\":\\"30\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_tablet\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"70\\",\\"padding_bottom\\":\\"30\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_mobile\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"2\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[{\\"mod_name\\":\\"fancy-heading\\",\\"mod_settings\\":{\\"heading\\":\\"Accounting & Assurance\\",\\"heading_tag\\":\\"h2\\",\\"text_alignment\\":\\"themify-text-center\\",\\"background_image-type\\":\\"image\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"main_margin_top_unit\\":\\"px\\",\\"main_margin_bottom_unit\\":\\"px\\",\\"font_family_subheading\\":\\"default\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\",\\"sub_margin_top_unit\\":\\"px\\",\\"sub_margin_bottom_unit\\":\\"px\\",\\"breakpoint_tablet\\":{\\"padding_bottom\\":\\"0\\",\\"margin_bottom\\":\\"0\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\"}}},{\\"row_order\\":\\"1\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col3-1 first\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-center\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/compilation-engagements.png\\",\\"title_image\\":\\"Compilation Engagements\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"caption_image\\":\\"Duis a metus ac purus consequat tincidunt eu quis nulla. Nam pharetra libero nulla, sit amet maximus ligula pretium a.\\",\\"animation_effect\\":\\"zoomIn\\",\\"background_image-gradient-angle\\":\\"180\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"breakpoint_tablet\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"30\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"}}}],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}},{\\"column_order\\":\\"1\\",\\"grid_class\\":\\"col3-1\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-center\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/review-engagements.png\\",\\"title_image\\":\\"Review Engagements\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"caption_image\\":\\"Duis a metus ac purus consequat tincidunt eu quis nulla. Nam pharetra libero nulla, sit amet maximus ligula pretium a.\\",\\"animation_effect\\":\\"zoomIn\\",\\"animation_effect_delay\\":\\".2\\",\\"background_image-gradient-angle\\":\\"180\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"breakpoint_tablet\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"30\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"}}}],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}},{\\"column_order\\":\\"2\\",\\"grid_class\\":\\"col3-1 last\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-center\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/audit-engagements.png\\",\\"title_image\\":\\"Audit Engagements\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"caption_image\\":\\"Duis a metus ac purus consequat tincidunt eu quis nulla. Nam pharetra libero nulla, sit amet maximus ligula pretium a.\\",\\"animation_effect\\":\\"zoomIn\\",\\"animation_effect_delay\\":\\".4\\",\\"background_image-gradient-angle\\":\\"180\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"breakpoint_tablet\\":{\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"30\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\"}}}],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}}],\\"gutter\\":\\"gutter-none\\",\\"column_alignment\\":\\"\\",\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"border-first\\"}}],\\"styling\\":[]}],\\"styling\\":{\\"custom_css_row\\":\\"accounting-services compliance-planning\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"f4f4f4_1.00\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"7.5\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"9\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"breakpoint_tablet_landscape\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"80\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_tablet\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"70\\",\\"padding_bottom\\":\\"30\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_mobile\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"padding_bottom\\":\\"30\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"3\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[{\\"mod_name\\":\\"fancy-heading\\",\\"mod_settings\\":{\\"heading\\":\\"Other Services\\",\\"heading_tag\\":\\"h2\\",\\"text_alignment\\":\\"themify-text-center\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"180\\",\\"background_image-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_image-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\n\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"main_margin_top_unit\\":\\"px\\",\\"main_margin_bottom_unit\\":\\"px\\",\\"font_family_subheading\\":\\"default\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\",\\"sub_margin_top_unit\\":\\"px\\",\\"sub_margin_bottom_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"row_order\\":\\"1\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col3-1 first\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-left\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/bookkeeping-icon.png\\",\\"title_image\\":\\"Bookkeeping\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"animation_effect\\":\\"fadeIn\\"}}],\\"styling\\":[]},{\\"column_order\\":\\"1\\",\\"grid_class\\":\\"col3-1\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-left\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/purchase-icon.png\\",\\"title_image\\":\\"Purchase / Sale of a Business\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"animation_effect\\":\\"fadeIn\\",\\"animation_effect_delay\\":\\".1\\"}}],\\"styling\\":[]},{\\"column_order\\":\\"2\\",\\"grid_class\\":\\"col3-1 last\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-left\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/payroll-icon.png\\",\\"title_image\\":\\"Payroll Compliance\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"animation_effect\\":\\"fadeIn\\",\\"animation_effect_delay\\":\\".2\\"}}],\\"styling\\":[]}],\\"gutter\\":\\"gutter-narrow\\",\\"column_alignment\\":\\"\\",\\"styling\\":[]},{\\"row_order\\":\\"2\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col3-1 first\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-left\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/business-plan-icon.png\\",\\"title_image\\":\\"Business Plans and Forecasts\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"animation_effect\\":\\"fadeIn\\",\\"animation_effect_delay\\":\\".3\\"}}],\\"styling\\":[]},{\\"column_order\\":\\"1\\",\\"grid_class\\":\\"col3-1\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-left\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/diligence-icon.png\\",\\"title_image\\":\\"Due Diligence Procedures\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"animation_effect\\":\\"fadeIn\\",\\"animation_effect_delay\\":\\".4\\"}}],\\"styling\\":[]},{\\"column_order\\":\\"2\\",\\"grid_class\\":\\"col3-1 last\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-left\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/task-provision-icon.png\\",\\"title_image\\":\\"Tax Provision Preparation & Review\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"animation_effect\\":\\"fadeIn\\",\\"animation_effect_delay\\":\\".5\\"}}],\\"styling\\":[]}],\\"gutter\\":\\"gutter-narrow\\",\\"column_alignment\\":\\"\\",\\"styling\\":[]},{\\"row_order\\":\\"3\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col3-1 first\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-left\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/assistance-icon.png\\",\\"title_image\\":\\"Assistance with Obtaining Financing\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"animation_effect\\":\\"fadeIn\\",\\"animation_effect_delay\\":\\".6\\"}}],\\"styling\\":[]},{\\"column_order\\":\\"1\\",\\"grid_class\\":\\"col3-1\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-left\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/merger-icon.png\\",\\"title_image\\":\\"Mergers & Acquisitions\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"animation_effect\\":\\"fadeIn\\",\\"animation_effect_delay\\":\\".7\\"}}],\\"styling\\":[]},{\\"column_order\\":\\"2\\",\\"grid_class\\":\\"col3-1 last\\",\\"modules\\":[{\\"mod_name\\":\\"image\\",\\"mod_settings\\":{\\"style_image\\":\\"image-left\\",\\"url_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/07/strategic-icon.png\\",\\"title_image\\":\\"Strategic Tax Planning\\",\\"param_image\\":\\"regular\\",\\"lightbox_size_unit_width\\":\\"pixels\\",\\"lightbox_size_unit_height\\":\\"pixels\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_caption_unit\\":\\"px\\",\\"line_height_caption_unit\\":\\"px\\",\\"animation_effect\\":\\"fadeIn\\",\\"animation_effect_delay\\":\\".8\\"}}],\\"styling\\":[]}],\\"gutter\\":\\"gutter-narrow\\",\\"column_alignment\\":\\"\\",\\"styling\\":[]}],\\"styling\\":[]}],\\"styling\\":{\\"custom_css_row\\":\\"other-services\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"7.5\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"9\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"breakpoint_tablet\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"70\\",\\"padding_bottom\\":\\"70\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_mobile\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"padding_bottom\\":\\"60\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"4\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[{\\"mod_name\\":\\"testimonial-slider\\",\\"mod_settings\\":{\\"layout_testimonial\\":\\"image-top\\",\\"tab_content_testimonial\\":[{\\"title_testimonial\\":\\"I am glad I switched over to Electro Accounting. In my very first year, I found I could claim expenses I never thought I could. Even better, I got a refund. Something I never got dealing with so called “Professionals”.\\",\\"person_picture_testimonial\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/03/jonathan-doe.png\\",\\"person_name_testimonial\\":\\"Jonathan Doe\\",\\"person_position_testimonial\\":\\"Director of Technical Support at Shopify\\"},{\\"title_testimonial\\":\\"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent dapibus sodales accumsan. Maecenas vulputate erat, sed varius ipsum velit sed odio.\\",\\"person_picture_testimonial\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/03/person-1.png\\",\\"person_name_testimonial\\":\\"Dennise King\\",\\"person_position_testimonial\\":\\"CEO\\",\\"company_testimonial\\":\\"Landmark Corporation\\"},{\\"title_testimonial\\":\\"Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Similique suscipit, id ducimus illum corporis pariatur.\\",\\"person_picture_testimonial\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/03/person-2.png\\",\\"person_name_testimonial\\":\\"Robert Gill\\",\\"person_position_testimonial\\":\\"Vice President\\",\\"company_testimonial\\":\\"Wheel Spain Ltd\\"},{\\"title_testimonial\\":\\"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae.\\",\\"person_picture_testimonial\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/03/person-3.png\\",\\"person_name_testimonial\\":\\"Juli Smith\\",\\"person_position_testimonial\\":\\"Manager\\",\\"company_testimonial\\":\\"County Road Association\\"}],\\"visible_opt_slider\\":\\"1\\",\\"auto_scroll_opt_slider\\":\\"off\\",\\"scroll_opt_slider\\":\\"1\\",\\"speed_opt_slider\\":\\"normal\\",\\"effect_slider\\":\\"cover-fade\\",\\"pause_on_hover_slider\\":\\"resume\\",\\"wrap_slider\\":\\"yes\\",\\"show_nav_slider\\":\\"no\\",\\"show_arrow_slider\\":\\"yes\\",\\"show_arrow_buttons_vertical\\":\\"vertical\\",\\"height_slider\\":\\"variable\\",\\"css_testimonial\\":\\"testimonials\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_title\\":\\"default\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_family_content\\":\\"default\\",\\"font_size_content_unit\\":\\"px\\",\\"line_height_content_unit\\":\\"px\\",\\"animation_effect\\":\\"fadeInUp\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]}],\\"styling\\":{\\"row_width\\":\\"fullwidth-content\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"22436c_1.00\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"ffffff_1.00\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"5.5\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom\\":\\"5.5\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"%\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"breakpoint_tablet_landscape\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_tablet\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_mobile\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"padding_bottom\\":\\"30\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"5\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[{\\"mod_name\\":\\"fancy-heading\\",\\"mod_settings\\":{\\"heading\\":\\"Contact us Today and Receive a Free Tax Service.\\",\\"sub_heading\\":\\"Let\\\\\\\\\\\'s talk about how we can help you\\",\\"heading_tag\\":\\"h1\\",\\"text_alignment\\":\\"themify-text-center\\",\\"background_image-type\\":\\"image\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"main_margin_top_unit\\":\\"px\\",\\"main_margin_bottom_unit\\":\\"px\\",\\"font_family_subheading\\":\\"default\\",\\"font_size_subheading\\":\\"1.1\\",\\"font_size_subheading_unit\\":\\"em\\",\\"line_height_subheading_unit\\":\\"px\\",\\"sub_margin_top_unit\\":\\"px\\",\\"sub_margin_bottom_unit\\":\\"px\\",\\"breakpoint_tablet_landscape\\":{\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_right\\":\\"20\\",\\"margin_left\\":\\"20\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\"},\\"breakpoint_mobile\\":{\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_right\\":\\"20\\",\\"margin_left\\":\\"20\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\"}}},{\\"mod_name\\":\\"buttons\\",\\"mod_settings\\":{\\"buttons_size\\":\\"normal\\",\\"buttons_style\\":\\"squared\\",\\"content_button\\":[{\\"label\\":\\"Contact Us\\",\\"link\\":\\"/contant-us\\",\\"link_options\\":\\"regular\\",\\"button_color_bg\\":\\"brown\\"}],\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top\\":\\"1.3\\",\\"margin_top_unit\\":\\"em\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"|\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"padding_top_link_unit\\":\\"px\\",\\"padding_right_link_unit\\":\\"px\\",\\"padding_bottom_link_unit\\":\\"px\\",\\"padding_left_link_unit\\":\\"px\\",\\"checkbox_link_padding_apply_all\\":\\"padding\\",\\"checkbox_link_padding_apply_all_padding\\":\\"padding\\",\\"link_margin_top_unit\\":\\"px\\",\\"link_margin_right_unit\\":\\"px\\",\\"link_margin_bottom_unit\\":\\"px\\",\\"link_margin_left_unit\\":\\"px\\",\\"link_checkbox_margin_apply_all\\":\\"margin\\",\\"link_checkbox_margin_apply_all_margin\\":\\"margin\\",\\"link_border_top_style\\":\\"solid\\",\\"link_border_right_style\\":\\"solid\\",\\"link_border_bottom_style\\":\\"solid\\",\\"link_border_left_style\\":\\"solid\\",\\"link_checkbox_border_apply_all\\":\\"border\\",\\"link_checkbox_border_apply_all_border\\":\\"border\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]}],\\"gutter\\":\\"gutter-none\\",\\"styling\\":{\\"row_width\\":\\"fullwidth-content\\",\\"custom_css_row\\":\\"about-team\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"f4f4f4_1.00\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"center\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"6.15\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom\\":\\"6.85\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"%\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"breakpoint_tablet\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"padding_bottom\\":\\"60\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_mobile\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"50\\",\\"padding_bottom\\":\\"60\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"6\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[],\\"styling\\":[]}],\\"styling\\":[]}]',
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
  'ID' => 6385,
  'post_date' => '2017-03-23 11:37:39',
  'post_date_gmt' => '2017-03-23 11:37:39',
  'post_content' => '',
  'post_title' => 'Contact Us',
  'post_excerpt' => '',
  'post_name' => 'contact-us',
  'post_modified' => '2017-07-12 15:12:06',
  'post_modified_gmt' => '2017-07-12 15:12:06',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?page_id=6385',
  'menu_order' => 0,
  'post_type' => 'page',
  'meta_input' => 
  array (
    'page_layout' => 'sidebar-none',
    'content_width' => 'full_width',
    'hide_page_title' => 'yes',
    'builder_switch_frontend' => '0',
    '_themify_builder_settings_json' => '[{\\"row_order\\":\\"0\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[{\\"mod_name\\":\\"fancy-heading\\",\\"mod_settings\\":{\\"heading\\":\\"Let\\\\\\\\\\\'s get in Touch\\",\\"sub_heading\\":\\"Contact Us\\",\\"heading_tag\\":\\"h1\\",\\"text_alignment\\":\\"themify-text-center\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"main_margin_top_unit\\":\\"px\\",\\"main_margin_bottom_unit\\":\\"px\\",\\"font_family_subheading\\":\\"default\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\",\\"sub_margin_top_unit\\":\\"px\\",\\"sub_margin_bottom_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"mod_name\\":\\"text\\",\\"mod_settings\\":{\\"content_text\\":\\"<p>Email, call, or even visit our office, we\\\\\\\\\\\'d love to help you grow your business</p>\\",\\"text_decoration\\":\\"underline\\",\\"column_divider_style\\":\\"solid\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_right\\":\\"20\\",\\"margin_left\\":\\"20\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}],\\"styling\\":[]}],\\"styling\\":{\\"row_width\\":\\"fullwidth-content\\",\\"row_height\\":\\"\\",\\"custom_css_row\\":\\"vertical-divider row-newsletter input-bg-white\\",\\"row_anchor\\":\\"\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/03/contact-us.jpg\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"fullcover\\",\\"background_position\\":\\"center-bottom\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"21456b_0.78\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"ffffff_1.00\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"center\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"14.5\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom\\":\\"14\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"%\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_mobile\\":\\"show\\",\\"animation_effect\\":\\"\\",\\"animation_effect_delay\\":\\"\\",\\"animation_effect_repeat\\":\\"\\",\\"custom_parallax_scroll_speed\\":\\"\\",\\"custom_parallax_scroll_zindex\\":\\"\\"}},{\\"row_order\\":\\"1\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col3-1 first\\",\\"modules\\":[{\\"mod_name\\":\\"icon\\",\\"mod_settings\\":{\\"icon_size\\":\\"normal\\",\\"icon_style\\":\\"none\\",\\"icon_arrangement\\":\\"icon_vertical\\",\\"content_icon\\":[{\\"icon\\":\\"fa-phone\\",\\"icon_color_bg\\":\\"blue\\",\\"link_options\\":\\"regular\\"}],\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_color_icon\\":\\"6fb0ed_1.00\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"mod_name\\":\\"text\\",\\"mod_settings\\":{\\"content_text\\":\\"<p>(818)657-7220</p>\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_h1\\":\\"default\\",\\"font_size_h1_unit\\":\\"px\\",\\"line_height_h1_unit\\":\\"px\\",\\"h1_margin_top_unit\\":\\"px\\",\\"h1_margin_bottom_unit\\":\\"px\\",\\"font_family_h2\\":\\"default\\",\\"font_size_h2_unit\\":\\"px\\",\\"line_height_h2_unit\\":\\"px\\",\\"h2_margin_top_unit\\":\\"px\\",\\"h2_margin_bottom_unit\\":\\"px\\",\\"font_family_h3\\":\\"default\\",\\"font_size_h3_unit\\":\\"px\\",\\"line_height_h3_unit\\":\\"px\\",\\"h3_margin_top_unit\\":\\"px\\",\\"h3_margin_bottom_unit\\":\\"px\\",\\"font_family_h4\\":\\"default\\",\\"font_size_h4_unit\\":\\"px\\",\\"line_height_h4_unit\\":\\"px\\",\\"h4_margin_top_unit\\":\\"px\\",\\"h4_margin_bottom_unit\\":\\"px\\",\\"font_family_h5\\":\\"default\\",\\"font_size_h5_unit\\":\\"px\\",\\"line_height_h5_unit\\":\\"px\\",\\"h5_margin_top_unit\\":\\"px\\",\\"h5_margin_bottom_unit\\":\\"px\\",\\"font_family_h6\\":\\"default\\",\\"font_size_h6_unit\\":\\"px\\",\\"line_height_h6_unit\\":\\"px\\",\\"h6_margin_top_unit\\":\\"px\\",\\"h6_margin_bottom_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"center\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}},{\\"column_order\\":\\"1\\",\\"grid_class\\":\\"col3-1\\",\\"modules\\":[{\\"mod_name\\":\\"icon\\",\\"mod_settings\\":{\\"icon_size\\":\\"normal\\",\\"icon_style\\":\\"none\\",\\"icon_arrangement\\":\\"icon_vertical\\",\\"content_icon\\":[{\\"icon\\":\\"fa-map-marker\\",\\"icon_color_bg\\":\\"blue\\",\\"link_options\\":\\"regular\\"}],\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_color_icon\\":\\"6fb0ed_1.00\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"mod_name\\":\\"text\\",\\"mod_settings\\":{\\"content_text\\":\\"<p>6320 Canoga Avenue, Suite 1430 Woodland Hills, CA 91367</p>\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_h1\\":\\"default\\",\\"font_size_h1_unit\\":\\"px\\",\\"line_height_h1_unit\\":\\"px\\",\\"h1_margin_top_unit\\":\\"px\\",\\"h1_margin_bottom_unit\\":\\"px\\",\\"font_family_h2\\":\\"default\\",\\"font_size_h2_unit\\":\\"px\\",\\"line_height_h2_unit\\":\\"px\\",\\"h2_margin_top_unit\\":\\"px\\",\\"h2_margin_bottom_unit\\":\\"px\\",\\"font_family_h3\\":\\"default\\",\\"font_size_h3_unit\\":\\"px\\",\\"line_height_h3_unit\\":\\"px\\",\\"h3_margin_top_unit\\":\\"px\\",\\"h3_margin_bottom_unit\\":\\"px\\",\\"font_family_h4\\":\\"default\\",\\"font_size_h4_unit\\":\\"px\\",\\"line_height_h4_unit\\":\\"px\\",\\"h4_margin_top_unit\\":\\"px\\",\\"h4_margin_bottom_unit\\":\\"px\\",\\"font_family_h5\\":\\"default\\",\\"font_size_h5_unit\\":\\"px\\",\\"line_height_h5_unit\\":\\"px\\",\\"h5_margin_top_unit\\":\\"px\\",\\"h5_margin_bottom_unit\\":\\"px\\",\\"font_family_h6\\":\\"default\\",\\"font_size_h6_unit\\":\\"px\\",\\"line_height_h6_unit\\":\\"px\\",\\"h6_margin_top_unit\\":\\"px\\",\\"h6_margin_bottom_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"center\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}},{\\"column_order\\":\\"2\\",\\"grid_class\\":\\"col3-1 last\\",\\"modules\\":[{\\"mod_name\\":\\"icon\\",\\"mod_settings\\":{\\"icon_size\\":\\"normal\\",\\"icon_style\\":\\"none\\",\\"icon_arrangement\\":\\"icon_vertical\\",\\"content_icon\\":[{\\"icon\\":\\"fa-envelope-o\\",\\"icon_color_bg\\":\\"blue\\",\\"link_options\\":\\"regular\\"}],\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_color_icon\\":\\"6fb0ed_1.00\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"mod_name\\":\\"text\\",\\"mod_settings\\":{\\"content_text\\":\\"<p>corporate@accap.com</p>\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_h1\\":\\"default\\",\\"font_size_h1_unit\\":\\"px\\",\\"line_height_h1_unit\\":\\"px\\",\\"h1_margin_top_unit\\":\\"px\\",\\"h1_margin_bottom_unit\\":\\"px\\",\\"font_family_h2\\":\\"default\\",\\"font_size_h2_unit\\":\\"px\\",\\"line_height_h2_unit\\":\\"px\\",\\"h2_margin_top_unit\\":\\"px\\",\\"h2_margin_bottom_unit\\":\\"px\\",\\"font_family_h3\\":\\"default\\",\\"font_size_h3_unit\\":\\"px\\",\\"line_height_h3_unit\\":\\"px\\",\\"h3_margin_top_unit\\":\\"px\\",\\"h3_margin_bottom_unit\\":\\"px\\",\\"font_family_h4\\":\\"default\\",\\"font_size_h4_unit\\":\\"px\\",\\"line_height_h4_unit\\":\\"px\\",\\"h4_margin_top_unit\\":\\"px\\",\\"h4_margin_bottom_unit\\":\\"px\\",\\"font_family_h5\\":\\"default\\",\\"font_size_h5_unit\\":\\"px\\",\\"line_height_h5_unit\\":\\"px\\",\\"h5_margin_top_unit\\":\\"px\\",\\"h5_margin_bottom_unit\\":\\"px\\",\\"font_family_h6\\":\\"default\\",\\"font_size_h6_unit\\":\\"px\\",\\"line_height_h6_unit\\":\\"px\\",\\"h6_margin_top_unit\\":\\"px\\",\\"h6_margin_bottom_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"center\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"custom_css_column\\":\\"\\"}}],\\"column_alignment\\":\\"\\",\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"22436c_1.00\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"ffffff_1.00\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"11\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom\\":\\"11\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"%\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"breakpoint_tablet\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"padding_bottom\\":\\"50\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_mobile\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"50\\",\\"padding_bottom\\":\\"40\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"2\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col4-1 first\\",\\"modules\\":[],\\"styling\\":[]},{\\"column_order\\":\\"1\\",\\"grid_class\\":\\"col4-2\\",\\"modules\\":[{\\"mod_name\\":\\"fancy-heading\\",\\"mod_settings\\":{\\"heading\\":\\"Let us know how we can help you.\\",\\"sub_heading\\":\\"Use the contact form below to send us a message.\\",\\"heading_tag\\":\\"h2\\",\\"text_alignment\\":\\"themify-text-center\\",\\"background_image-type\\":\\"image\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"main_margin_top_unit\\":\\"px\\",\\"main_margin_bottom_unit\\":\\"px\\",\\"font_family_subheading\\":\\"default\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\",\\"sub_margin_top_unit\\":\\"px\\",\\"sub_margin_bottom_unit\\":\\"px\\",\\"breakpoint_tablet_landscape\\":{\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_bottom\\":\\"40\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\"},\\"breakpoint_tablet\\":{\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_bottom\\":\\"40\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\"}}},{\\"mod_name\\":\\"contact\\",\\"mod_settings\\":{\\"layout_contact\\":\\"style1\\",\\"mail_contact\\":\\"testdevelopern1@gmail.com\\",\\"field_name_label\\":\\"Your Name\\",\\"field_email_label\\":\\"Email Address\\",\\"field_subject_label\\":\\"Subject\\",\\"field_subject_active\\":\\"|\\",\\"field_captcha_active\\":\\"|\\",\\"field_message_label\\":\\"Message\\",\\"field_sendcopy_label\\":\\"Send a copy to myself\\",\\"field_sendcopy_active\\":\\"|\\",\\"field_send_label\\":\\"Send\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"text_align_left\\":\\"left\\",\\"text_align_center\\":\\"center\\",\\"text_align_right\\":\\"right\\",\\"text_align_justify\\":\\"justify\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family_labels\\":\\"default\\",\\"font_size_labels_unit\\":\\"px\\",\\"font_family_inputs\\":\\"default\\",\\"font_size_inputs_unit\\":\\"px\\",\\"font_family_send\\":\\"default\\",\\"font_size_send_unit\\":\\"px\\",\\"font_family_success_message\\":\\"default\\",\\"text_align_success_message_left\\":\\"left\\",\\"text_align_success_message_center\\":\\"center\\",\\"text_align_success_message_right\\":\\"right\\",\\"text_align_success_message_justify\\":\\"justify\\",\\"padding_top_success_message_unit\\":\\"px\\",\\"padding_right_success_message_unit\\":\\"px\\",\\"padding_bottom_success_message_unit\\":\\"px\\",\\"padding_left_success_message_unit\\":\\"px\\",\\"margin_top_success_message_unit\\":\\"px\\",\\"margin_right_success_message_unit\\":\\"px\\",\\"margin_bottom_success_message_unit\\":\\"px\\",\\"margin_left_success_message_unit\\":\\"px\\",\\"font_family_error_message\\":\\"default\\",\\"text_align_error_message_left\\":\\"left\\",\\"text_align_error_message_center\\":\\"center\\",\\"text_align_error_message_right\\":\\"right\\",\\"text_align_error_message_justify\\":\\"justify\\",\\"padding_top_error_message_unit\\":\\"px\\",\\"padding_right_error_message_unit\\":\\"px\\",\\"padding_bottom_error_message_unit\\":\\"px\\",\\"padding_left_error_message_unit\\":\\"px\\",\\"margin_top_error_message_unit\\":\\"px\\",\\"margin_right_error_message_unit\\":\\"px\\",\\"margin_bottom_error_message_unit\\":\\"px\\",\\"margin_left_error_message_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]},{\\"column_order\\":\\"2\\",\\"grid_class\\":\\"col4-1 last\\",\\"modules\\":[],\\"styling\\":[]}],\\"gutter\\":\\"gutter-none\\",\\"column_alignment\\":\\"\\",\\"col_tablet\\":\\"tablet-full\\",\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"5.5\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom\\":\\"5.5\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"%\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"breakpoint_tablet_landscape\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"70\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_tablet\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_mobile\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"40\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"3\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[{\\"mod_name\\":\\"maps-pro\\",\\"mod_settings\\":{\\"map_display_type\\":\\"dynamic\\",\\"w_map\\":\\"100\\",\\"unit_w\\":\\"%\\",\\"w_map_static\\":\\"500\\",\\"h_map\\":\\"520\\",\\"unit_h\\":\\"px\\",\\"type_map\\":\\"ROADMAP\\",\\"style_map\\":\\"cool-grey\\",\\"scrollwheel_map\\":\\"disable\\",\\"draggable_map\\":\\"enable\\",\\"disable_map_ui\\":\\"yes\\",\\"map_link\\":\\"|\\",\\"zoom_map\\":\\"16\\",\\"map_center\\":\\"6320 Canoga Avenue, Suite 1430 Woodland Hills, CA 91367\\",\\"markers\\":[{\\"address\\":\\"6320 Canoga Avenue, Suite 1430 Woodland Hills, CA 19367\\",\\"title\\":\\"Our Shop\\",\\"image\\":\\"https://themify.me/demo/themes/themes/wp-content/uploads/addon-samples/shop-map-marker.png\\"}],\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}}],\\"styling\\":[]}],\\"styling\\":{\\"row_width\\":\\"fullwidth-content\\",\\"row_height\\":\\"\\",\\"custom_css_row\\":\\"\\",\\"row_anchor\\":\\"\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom\\":\\"\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_mobile\\":\\"show\\",\\"animation_effect\\":\\"\\",\\"animation_effect_delay\\":\\"\\",\\"animation_effect_repeat\\":\\"\\",\\"custom_parallax_scroll_speed\\":\\"\\",\\"custom_parallax_scroll_zindex\\":\\"\\"}},{\\"row_order\\":\\"4\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[],\\"styling\\":[]}],\\"styling\\":[]}]',
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
  'ID' => 6391,
  'post_date' => '2017-03-23 11:42:47',
  'post_date_gmt' => '2017-03-23 11:42:47',
  'post_content' => '',
  'post_title' => 'News',
  'post_excerpt' => '',
  'post_name' => 'news',
  'post_modified' => '2017-07-12 14:55:41',
  'post_modified_gmt' => '2017-07-12 14:55:41',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?page_id=6391',
  'menu_order' => 0,
  'post_type' => 'page',
  'meta_input' => 
  array (
    'page_layout' => 'sidebar-none',
    'content_width' => 'full_width',
    'hide_page_title' => 'yes',
    'builder_switch_frontend' => '0',
    '_themify_builder_settings_json' => '[{\\"row_order\\":\\"0\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[{\\"mod_name\\":\\"fancy-heading\\",\\"mod_settings\\":{\\"heading\\":\\"Accounting\\",\\"sub_heading\\":\\"News\\",\\"heading_tag\\":\\"h1\\",\\"text_alignment\\":\\"themify-text-center\\",\\"background_image-type\\":\\"image\\",\\"background_image-type_image\\":\\"image\\",\\"background_image-type_gradient\\":\\"gradient\\",\\"background_image-gradient-type\\":\\"linear\\",\\"background_image-gradient-angle\\":\\"0\\",\\"padding_top_unit\\":\\"px\\",\\"padding_right_unit\\":\\"px\\",\\"padding_bottom_unit\\":\\"px\\",\\"padding_left_unit\\":\\"px\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_padding_apply_all_padding\\":\\"padding\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"checkbox_margin_apply_all_margin\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"checkbox_border_apply_all_border\\":\\"border\\",\\"font_family\\":\\"default\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"main_margin_top_unit\\":\\"px\\",\\"main_margin_bottom_unit\\":\\"px\\",\\"font_family_subheading\\":\\"default\\",\\"font_size_subheading_unit\\":\\"px\\",\\"line_height_subheading_unit\\":\\"px\\",\\"sub_margin_top_unit\\":\\"px\\",\\"sub_margin_bottom_unit\\":\\"px\\",\\"custom_parallax_scroll_reverse\\":\\"|\\",\\"custom_parallax_scroll_reverse_reverse\\":\\"reverse\\",\\"custom_parallax_scroll_fade\\":\\"|\\",\\"custom_parallax_scroll_fade_fade\\":\\"fade\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_desktop_show\\":\\"show\\",\\"visibility_desktop_hide\\":\\"hide\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_tablet_show\\":\\"show\\",\\"visibility_tablet_hide\\":\\"hide\\",\\"visibility_mobile\\":\\"show\\",\\"visibility_mobile_show\\":\\"show\\",\\"visibility_mobile_hide\\":\\"hide\\"}},{\\"mod_name\\":\\"plain-text\\",\\"mod_settings\\":{\\"plain_text\\":\\"<form id=\\\\\\\\\\\\\\"mc4wp-form-1\\\\\\\\\\\\\\" class=\\\\\\\\\\\\\\"mc4wp-form mc4wp-form-6369\\\\\\\\\\\\\\" method=\\\\\\\\\\\\\\"post\\\\\\\\\\\\\\" data-id=\\\\\\\\\\\\\\"6369\\\\\\\\\\\\\\" data-name=\\\\\\\\\\\\\\"Homepage Signup\\\\\\\\\\\\\\"><div class=\\\\\\\\\\\\\\"mc4wp-form-fields\\\\\\\\\\\\\\"><p>\\\\n\\\\t<input type=\\\\\\\\\\\\\\"email\\\\\\\\\\\\\\" name=\\\\\\\\\\\\\\"EMAIL\\\\\\\\\\\\\\" placeholder=\\\\\\\\\\\\\\"Email Address\\\\\\\\\\\\\\" required=\\\\\\\\\\\\\\"\\\\\\\\\\\\\\">  <input type=\\\\\\\\\\\\\\"submit\\\\\\\\\\\\\\" value=\\\\\\\\\\\\\\"Subscribe\\\\\\\\\\\\\\">\\\\n</p><div style=\\\\\\\\\\\\\\"display: none;\\\\\\\\\\\\\\"><input type=\\\\\\\\\\\\\\"text\\\\\\\\\\\\\\" name=\\\\\\\\\\\\\\"_mc4wp_honeypot\\\\\\\\\\\\\\" value=\\\\\\\\\\\\\\"\\\\\\\\\\\\\\" tabindex=\\\\\\\\\\\\\\"-1\\\\\\\\\\\\\\" autocomplete=\\\\\\\\\\\\\\"off\\\\\\\\\\\\\\"></div><input type=\\\\\\\\\\\\\\"hidden\\\\\\\\\\\\\\" name=\\\\\\\\\\\\\\"_mc4wp_timestamp\\\\\\\\\\\\\\" value=\\\\\\\\\\\\\\"1499754893\\\\\\\\\\\\\\"><input type=\\\\\\\\\\\\\\"hidden\\\\\\\\\\\\\\" name=\\\\\\\\\\\\\\"_mc4wp_form_id\\\\\\\\\\\\\\" value=\\\\\\\\\\\\\\"6369\\\\\\\\\\\\\\"><input type=\\\\\\\\\\\\\\"hidden\\\\\\\\\\\\\\" name=\\\\\\\\\\\\\\"_mc4wp_form_element_id\\\\\\\\\\\\\\" value=\\\\\\\\\\\\\\"mc4wp-form-1\\\\\\\\\\\\\\"></div><div class=\\\\\\\\\\\\\\"mc4wp-response\\\\\\\\\\\\\\"></div></form>\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}],\\"styling\\":[]}],\\"styling\\":{\\"row_width\\":\\"fullwidth-content\\",\\"row_height\\":\\"\\",\\"custom_css_row\\":\\"vertical-divider row-newsletter input-bg-white\\",\\"row_anchor\\":\\"\\",\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"https://themify.me/demo/themes/ultra-accountant/files/2017/03/news-banner-min.jpg\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"background_repeat\\":\\"fullcover\\",\\"background_position\\":\\"center-bottom\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"21456b_0.78\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_gradient_hover-css\\":\\"background-image: -moz-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -webkit-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -o-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: -ms-linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\nbackground-image: linear-gradient(180deg,rgb(0, 0, 0) 0%, rgb(255, 255, 255) 100%);\\\\r\\\\n\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"ffffff_1.00\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"center\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"14.5\\",\\"padding_top_unit\\":\\"%\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"%\\",\\"padding_bottom\\":\\"14\\",\\"padding_bottom_unit\\":\\"%\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"%\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"visibility_desktop\\":\\"show\\",\\"visibility_tablet\\":\\"show\\",\\"visibility_mobile\\":\\"show\\",\\"animation_effect\\":\\"\\",\\"animation_effect_delay\\":\\"\\",\\"animation_effect_repeat\\":\\"\\",\\"custom_parallax_scroll_speed\\":\\"\\",\\"custom_parallax_scroll_zindex\\":\\"\\"}},{\\"row_order\\":\\"1\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[{\\"mod_name\\":\\"post\\",\\"mod_settings\\":{\\"layout_post\\":\\"grid3\\",\\"post_type_post\\":\\"post\\",\\"type_query_post\\":\\"category\\",\\"category_post\\":\\"0|multiple\\",\\"post_tag_post\\":\\"0|multiple\\",\\"post_content_layout\\":\\"polaroid\\",\\"post_filter\\":\\"no\\",\\"disable_masonry\\":\\"default\\",\\"post_gutter\\":\\"no-gutter\\",\\"post_per_page_post\\":\\"12\\",\\"order_post\\":\\"desc\\",\\"orderby_post\\":\\"date\\",\\"display_post\\":\\"excerpt\\",\\"img_width_post\\":\\"390\\",\\"img_height_post\\":\\"400\\",\\"hide_post_meta_post\\":\\"yes\\",\\"hide_page_nav_post\\":\\"no\\",\\"css_post\\":\\"latest-news\\",\\"font_size_unit\\":\\"px\\",\\"line_height_unit\\":\\"px\\",\\"text_decoration\\":\\"underline\\",\\"checkbox_padding_apply_all\\":\\"padding\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"font_size_title_unit\\":\\"px\\",\\"line_height_title_unit\\":\\"px\\",\\"font_size_meta_unit\\":\\"px\\",\\"line_height_meta_unit\\":\\"px\\",\\"font_size_date_unit\\":\\"px\\",\\"line_height_date_unit\\":\\"px\\",\\"font_size_content_unit\\":\\"px\\",\\"line_height_content_unit\\":\\"px\\"}}],\\"styling\\":[]}],\\"column_alignment\\":\\"\\",\\"styling\\":{\\"background_type\\":\\"image\\",\\"background_slider\\":\\"\\",\\"background_slider_size\\":\\"\\",\\"background_slider_mode\\":\\"\\",\\"background_video\\":\\"\\",\\"background_image\\":\\"\\",\\"background_gradient-gradient-type\\":\\"linear\\",\\"background_gradient-gradient-angle\\":\\"180\\",\\"background_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"background_repeat\\":\\"\\",\\"background_position\\":\\"\\",\\"background_color\\":\\"\\",\\"cover_color-type\\":\\"color\\",\\"cover_color\\":\\"\\",\\"cover_gradient-gradient-type\\":\\"linear\\",\\"cover_gradient-gradient-angle\\":\\"180\\",\\"cover_gradient-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"cover_color_hover-type\\":\\"hover_color\\",\\"cover_color_hover\\":\\"\\",\\"cover_gradient_hover-gradient-type\\":\\"linear\\",\\"cover_gradient_hover-gradient-angle\\":\\"180\\",\\"cover_gradient_hover-gradient\\":\\"0% rgb(0, 0, 0)|100% rgb(255, 255, 255)\\",\\"font_family\\":\\"default\\",\\"font_color\\":\\"\\",\\"font_size\\":\\"\\",\\"font_size_unit\\":\\"px\\",\\"line_height\\":\\"\\",\\"line_height_unit\\":\\"px\\",\\"text_align\\":\\"\\",\\"link_color\\":\\"\\",\\"text_decoration\\":\\"\\",\\"padding_top\\":\\"7.35\\",\\"padding_top_unit\\":\\"em\\",\\"padding_right\\":\\"\\",\\"padding_right_unit\\":\\"em\\",\\"padding_bottom\\":\\"7.35\\",\\"padding_bottom_unit\\":\\"em\\",\\"padding_left\\":\\"\\",\\"padding_left_unit\\":\\"em\\",\\"margin_top\\":\\"\\",\\"margin_top_unit\\":\\"px\\",\\"margin_right\\":\\"\\",\\"margin_right_unit\\":\\"px\\",\\"margin_bottom\\":\\"\\",\\"margin_bottom_unit\\":\\"px\\",\\"margin_left\\":\\"\\",\\"margin_left_unit\\":\\"px\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_color\\":\\"\\",\\"border_top_width\\":\\"\\",\\"border_top_style\\":\\"solid\\",\\"border_right_color\\":\\"\\",\\"border_right_width\\":\\"\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_color\\":\\"\\",\\"border_bottom_width\\":\\"\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_color\\":\\"\\",\\"border_left_width\\":\\"\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\",\\"breakpoint_tablet_landscape\\":{\\"text_decoration\\":\\"underline\\",\\"padding_bottom\\":\\"30\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_tablet\\":{\\"text_decoration\\":\\"underline\\",\\"padding_bottom\\":\\"40\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"},\\"breakpoint_mobile\\":{\\"text_decoration\\":\\"underline\\",\\"padding_top\\":\\"60\\",\\"padding_bottom\\":\\"40\\",\\"checkbox_margin_apply_all\\":\\"margin\\",\\"border_top_style\\":\\"solid\\",\\"border_right_style\\":\\"solid\\",\\"border_bottom_style\\":\\"solid\\",\\"border_left_style\\":\\"solid\\",\\"checkbox_border_apply_all\\":\\"border\\"}}},{\\"row_order\\":\\"2\\",\\"cols\\":[{\\"column_order\\":\\"0\\",\\"grid_class\\":\\"col-full first last\\",\\"modules\\":[],\\"styling\\":[]}],\\"styling\\":[]}]',
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
  'ID' => 3200,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'About',
  'post_excerpt' => '',
  'post_name' => 'about',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/about/',
  'menu_order' => 1,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3200',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#about',
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
  'ID' => 3207,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Guests',
  'post_excerpt' => '',
  'post_name' => 'guests',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/guests/',
  'menu_order' => 1,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3207',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#guests',
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
  'ID' => 3215,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Bride & Groom',
  'post_excerpt' => '',
  'post_name' => 'bride-groom',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/bride-groom/',
  'menu_order' => 1,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3215',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#bg',
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
  'ID' => 3219,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Course Manager',
  'post_excerpt' => '',
  'post_name' => 'course-manager',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/course-manager/',
  'menu_order' => 1,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3219',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#course-manager',
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
  'ID' => 3226,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Layouts',
  'post_excerpt' => '',
  'post_name' => 'layouts',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/layouts/',
  'menu_order' => 1,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3226',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => 'https://themify.me/demo/themes/ultra/all-layouts/',
    '_themify_mega_menu_column' => '1',
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
  'ID' => 6444,
  'post_date' => '2017-06-03 13:34:40',
  'post_date_gmt' => '2017-06-03 13:34:40',
  'post_content' => ' ',
  'post_title' => '',
  'post_excerpt' => '',
  'post_name' => '6444',
  'post_modified' => '2017-08-03 21:48:01',
  'post_modified_gmt' => '2017-08-03 21:48:01',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6444',
  'menu_order' => 1,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'post_type',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '6376',
    '_menu_item_object' => 'page',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
  ),
  'tax_input' => 
  array (
    'nav_menu' => 'header-menu',
  ),
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 6451,
  'post_date' => '2017-06-03 13:37:19',
  'post_date_gmt' => '2017-06-03 13:37:19',
  'post_content' => ' ',
  'post_title' => '',
  'post_excerpt' => '',
  'post_name' => '6451',
  'post_modified' => '2017-07-11 00:06:26',
  'post_modified_gmt' => '2017-07-11 00:06:26',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6451',
  'menu_order' => 1,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'post_type',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '6376',
    '_menu_item_object' => 'page',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
  ),
  'tax_input' => 
  array (
    'nav_menu' => 'footer-menu',
  ),
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 3201,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Skills',
  'post_excerpt' => '',
  'post_name' => 'skills',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/skills/',
  'menu_order' => 2,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3201',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#skills',
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
  'ID' => 3208,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Schedule',
  'post_excerpt' => '',
  'post_name' => 'schedule',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/schedule/',
  'menu_order' => 2,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3208',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#schedule',
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
  'ID' => 3216,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Wedding Party',
  'post_excerpt' => '',
  'post_name' => 'wedding-party',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/wedding-party/',
  'menu_order' => 2,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3216',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#wedding-party',
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
  'ID' => 3220,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'What is a CM?',
  'post_excerpt' => '',
  'post_name' => 'what-is-a-cm',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/what-is-a-cm/',
  'menu_order' => 2,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3220',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#what',
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
  'ID' => 3227,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Archive Layouts',
  'post_excerpt' => '',
  'post_name' => 'archive-layouts',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/archive-layouts/',
  'menu_order' => 2,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '3226',
    '_menu_item_object_id' => '3227',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#',
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
  'ID' => 6445,
  'post_date' => '2017-06-03 13:34:40',
  'post_date_gmt' => '2017-06-03 13:34:40',
  'post_content' => ' ',
  'post_title' => '',
  'post_excerpt' => '',
  'post_name' => '6445',
  'post_modified' => '2017-08-03 21:48:01',
  'post_modified_gmt' => '2017-08-03 21:48:01',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6445',
  'menu_order' => 2,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'post_type',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '6381',
    '_menu_item_object' => 'page',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
  ),
  'tax_input' => 
  array (
    'nav_menu' => 'header-menu',
  ),
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 6450,
  'post_date' => '2017-06-03 13:37:19',
  'post_date_gmt' => '2017-06-03 13:37:19',
  'post_content' => ' ',
  'post_title' => '',
  'post_excerpt' => '',
  'post_name' => '6450',
  'post_modified' => '2017-07-11 00:06:26',
  'post_modified_gmt' => '2017-07-11 00:06:26',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6450',
  'menu_order' => 2,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'post_type',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '6381',
    '_menu_item_object' => 'page',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
  ),
  'tax_input' => 
  array (
    'nav_menu' => 'footer-menu',
  ),
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 3202,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Resume',
  'post_excerpt' => '',
  'post_name' => 'resume',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/resume/',
  'menu_order' => 3,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3202',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#resume',
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
  'ID' => 3209,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Location',
  'post_excerpt' => '',
  'post_name' => 'location',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/location/',
  'menu_order' => 3,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3209',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#location',
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
  'ID' => 3217,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'When & Where',
  'post_excerpt' => '',
  'post_name' => 'when-where',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/when-where/',
  'menu_order' => 3,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3217',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#whenwhere',
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
  'ID' => 3221,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Becoming a CM',
  'post_excerpt' => '',
  'post_name' => 'becoming-a-cm',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/becoming-a-cm/',
  'menu_order' => 3,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3221',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#course-exam',
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
  'ID' => 6447,
  'post_date' => '2017-06-03 13:34:40',
  'post_date_gmt' => '2017-06-03 13:34:40',
  'post_content' => ' ',
  'post_title' => '',
  'post_excerpt' => '',
  'post_name' => '6447',
  'post_modified' => '2017-08-03 21:48:01',
  'post_modified_gmt' => '2017-08-03 21:48:01',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6447',
  'menu_order' => 3,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'post_type',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '6391',
    '_menu_item_object' => 'page',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
  ),
  'tax_input' => 
  array (
    'nav_menu' => 'header-menu',
  ),
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 6453,
  'post_date' => '2017-06-03 13:37:19',
  'post_date_gmt' => '2017-06-03 13:37:19',
  'post_content' => ' ',
  'post_title' => '',
  'post_excerpt' => '',
  'post_name' => '6453',
  'post_modified' => '2017-07-11 00:06:26',
  'post_modified_gmt' => '2017-07-11 00:06:26',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6453',
  'menu_order' => 3,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'post_type',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '6391',
    '_menu_item_object' => 'page',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
  ),
  'tax_input' => 
  array (
    'nav_menu' => 'footer-menu',
  ),
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 3203,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Status',
  'post_excerpt' => '',
  'post_name' => 'status',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/status/',
  'menu_order' => 4,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3203',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#status',
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
  'ID' => 3210,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Sponsors',
  'post_excerpt' => '',
  'post_name' => 'sponsors',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/sponsors/',
  'menu_order' => 4,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3210',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#sponsors',
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
  'ID' => 3218,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'RSVP',
  'post_excerpt' => '',
  'post_name' => 'rsvp',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/rsvp/',
  'menu_order' => 4,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3218',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#rsvp',
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
  'ID' => 3222,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Learnings',
  'post_excerpt' => '',
  'post_name' => 'learnings',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/learnings/',
  'menu_order' => 4,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3222',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#interactive-interface',
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
  'ID' => 6448,
  'post_date' => '2017-06-03 13:34:40',
  'post_date_gmt' => '2017-06-03 13:34:40',
  'post_content' => ' ',
  'post_title' => '',
  'post_excerpt' => '',
  'post_name' => '6448',
  'post_modified' => '2017-08-03 21:48:01',
  'post_modified_gmt' => '2017-08-03 21:48:01',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6448',
  'menu_order' => 4,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'post_type',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '6385',
    '_menu_item_object' => 'page',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
  ),
  'tax_input' => 
  array (
    'nav_menu' => 'header-menu',
  ),
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 6454,
  'post_date' => '2017-06-03 13:37:19',
  'post_date_gmt' => '2017-06-03 13:37:19',
  'post_content' => '',
  'post_title' => 'Contact',
  'post_excerpt' => '',
  'post_name' => 'contact-2',
  'post_modified' => '2017-07-11 00:06:26',
  'post_modified_gmt' => '2017-07-11 00:06:26',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6454',
  'menu_order' => 4,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'post_type',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '6385',
    '_menu_item_object' => 'page',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
  ),
  'tax_input' => 
  array (
    'nav_menu' => 'footer-menu',
  ),
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 3204,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Portfolio',
  'post_excerpt' => '',
  'post_name' => 'portfolio',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/portfolio/',
  'menu_order' => 5,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3204',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#portfolio',
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
  'ID' => 3211,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Press',
  'post_excerpt' => '',
  'post_name' => 'press',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/press/',
  'menu_order' => 5,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3211',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#press',
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
  'ID' => 3223,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Share Your Skills',
  'post_excerpt' => '',
  'post_name' => 'share-your-skills',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/share-your-skills/',
  'menu_order' => 5,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3223',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#share',
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
  'ID' => 6449,
  'post_date' => '2017-06-03 13:34:40',
  'post_date_gmt' => '2017-06-03 13:34:40',
  'post_content' => '',
  'post_title' => 'Book Appointment',
  'post_excerpt' => '',
  'post_name' => 'book-appointment',
  'post_modified' => '2017-08-03 21:48:01',
  'post_modified_gmt' => '2017-08-03 21:48:01',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/?p=6449',
  'menu_order' => 5,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '6449',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => 'highlight-link',
    ),
    '_menu_item_url' => 'https://themify.me/demo/themes/ultra-accountant/#appointment',
  ),
  'tax_input' => 
  array (
    'nav_menu' => 'header-menu',
  ),
);
if( ERASEDEMO ) {
	themify_undo_import_post( $post );
} else {
	themify_import_post( $post );
}

$post = array (
  'ID' => 3205,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Connect',
  'post_excerpt' => '',
  'post_name' => 'connect',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/connect/',
  'menu_order' => 6,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3205',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#connect',
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
  'ID' => 3212,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Past Event Galleries',
  'post_excerpt' => '',
  'post_name' => 'past-event-galleries',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/past-event-galleries/',
  'menu_order' => 6,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3212',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#past-event-galleries',
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
  'ID' => 3224,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Testimonials',
  'post_excerpt' => '',
  'post_name' => 'testimonials',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/testimonials/',
  'menu_order' => 6,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3224',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#testimonial',
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
  'ID' => 3206,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Contact',
  'post_excerpt' => '',
  'post_name' => 'contact',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/contact/',
  'menu_order' => 7,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3206',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#contact',
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
  'ID' => 3213,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Pricing',
  'post_excerpt' => '',
  'post_name' => 'pricing',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/pricing/',
  'menu_order' => 7,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3213',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#pricing',
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
  'ID' => 3225,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Get it now!',
  'post_excerpt' => '',
  'post_name' => 'get-it-now',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/get-it-now/',
  'menu_order' => 7,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3225',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#get',
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
  'ID' => 3214,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'FAQ',
  'post_excerpt' => '',
  'post_name' => 'faq',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/faq/',
  'menu_order' => 8,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3214',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#faq',
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
  'ID' => 3228,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Post Layouts',
  'post_excerpt' => '',
  'post_name' => 'post-layouts',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/post-layouts/',
  'menu_order' => 8,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '3226',
    '_menu_item_object_id' => '3228',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#',
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
  'ID' => 3229,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Portfolio Layouts',
  'post_excerpt' => '',
  'post_name' => 'portfolio-layouts',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/portfolio-layouts/',
  'menu_order' => 13,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '3226',
    '_menu_item_object_id' => '3229',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#',
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
  'ID' => 3230,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Section Scroll',
  'post_excerpt' => '',
  'post_name' => 'section-scroll',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/section-scroll/',
  'menu_order' => 18,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '3226',
    '_menu_item_object_id' => '3230',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#',
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
  'ID' => 3231,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Text',
  'post_excerpt' => '',
  'post_name' => 'text',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/text/',
  'menu_order' => 26,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '3226',
    '_menu_item_object_id' => '3231',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#WP_Widget_Text',
    '_themify_menu_widget' => 
    array (
      'title' => 'About',
      'text' => 'Ultra comes with multiple types of layouts that gives you more flexibility when designing your blog/portfolio posts. The section scrolling features allows you to scroll through your site one row at a time.',
    ),
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
  'ID' => 3232,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Appearance',
  'post_excerpt' => '',
  'post_name' => 'appearance',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/appearance/',
  'menu_order' => 27,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '0',
    '_menu_item_object_id' => '3232',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#',
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
  'ID' => 3233,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Header Design',
  'post_excerpt' => '',
  'post_name' => 'header-design',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/header-design/',
  'menu_order' => 28,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '3232',
    '_menu_item_object_id' => '3233',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#',
    '_themify_dropdown_columns' => '3',
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
  'ID' => 3235,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Footer',
  'post_excerpt' => '',
  'post_name' => 'footer',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/footer/',
  'menu_order' => 44,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '3232',
    '_menu_item_object_id' => '3235',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#',
    '_themify_dropdown_columns' => '2',
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
  'ID' => 3234,
  'post_date' => '2017-03-23 10:53:49',
  'post_date_gmt' => '2017-03-23 10:53:49',
  'post_content' => '',
  'post_title' => 'Header BG',
  'post_excerpt' => '',
  'post_name' => 'header-bg',
  'post_modified' => '2017-03-23 10:53:49',
  'post_modified_gmt' => '2017-03-23 10:53:49',
  'post_content_filtered' => '',
  'post_parent' => 0,
  'guid' => 'https://themify.me/demo/themes/ultra-accountant/2017/03/23/header-bg/',
  'menu_order' => 50,
  'post_type' => 'nav_menu_item',
  'meta_input' => 
  array (
    '_menu_item_type' => 'custom',
    '_menu_item_menu_item_parent' => '3232',
    '_menu_item_object_id' => '3234',
    '_menu_item_object' => 'custom',
    '_menu_item_classes' => 
    array (
      0 => '',
    ),
    '_menu_item_url' => '#',
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
  'title' => 'Widget 3',
  'text' => 'For example, phone #: 123-333-4567',
  'filter' => false,
);
update_option( "widget_text", $widgets );

$widgets = get_option( "widget_themify-social-links" );
$widgets[1003] = array (
  'title' => '',
  'show_link_name' => NULL,
  'open_new_window' => NULL,
  'icon_size' => 'icon-medium',
  'orientation' => 'horizontal',
);
update_option( "widget_themify-social-links", $widgets );

$widgets = get_option( "widget_themify-feature-posts" );
$widgets[1004] = array (
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

$widgets = get_option( "widget_themify-twitter" );
$widgets[1005] = array (
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

$widgets = get_option( "widget_themify-social-links" );
$widgets[1006] = array (
  'title' => '',
  'show_link_name' => NULL,
  'open_new_window' => NULL,
  'icon_size' => 'icon-medium',
  'orientation' => 'horizontal',
);
update_option( "widget_themify-social-links", $widgets );

$widgets = get_option( "widget_themify-social-links" );
$widgets[1007] = array (
  'title' => '',
  'show_link_name' => NULL,
  'open_new_window' => NULL,
  'icon_size' => 'icon-medium',
  'orientation' => 'horizontal',
);
update_option( "widget_themify-social-links", $widgets );

$widgets = get_option( "widget_text" );
$widgets[1008] = array (
  'title' => '',
  'text' => '<span class="fa fa-phone"></span><a href="tel:(818)657-7220">(818)657-7220</a>',
  'filter' => false,
);
update_option( "widget_text", $widgets );

$widgets = get_option( "widget_themify-social-links" );
$widgets[1009] = array (
  'title' => '',
  'show_link_name' => NULL,
  'open_new_window' => NULL,
  'icon_size' => 'icon-medium',
  'orientation' => 'horizontal',
);
update_option( "widget_themify-social-links", $widgets );

$widgets = get_option( "widget_themify-feature-posts" );
$widgets[1010] = array (
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

$widgets = get_option( "widget_text" );
$widgets[1011] = array (
  'title' => 'About',
  'text' => 'The Ultra theme is Themify\'s flagship theme. It\'s a WordPress designed to give you more control on the design of your theme. Built to work seamlessly with our drag & drop Builder plugin, it gives you the ability to customize the look and feel of your content. ',
  'filter' => false,
);
update_option( "widget_text", $widgets );

$widgets = get_option( "widget_themify-twitter" );
$widgets[1012] = array (
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



$sidebars_widgets = array (
  'header-widget-3' => 
  array (
    0 => 'text-1002',
  ),
  'wp_inactive_widgets' => 
  array (
    0 => 'themify-social-links-1003',
  ),
  'sidebar-main' => 
  array (
    0 => 'themify-feature-posts-1004',
    1 => 'themify-twitter-1005',
  ),
  'social-widget' => 
  array (
    0 => 'themify-social-links-1006',
  ),
  'footer-social-widget' => 
  array (
    0 => 'themify-social-links-1007',
  ),
  'header-widget-1' => 
  array (
    0 => 'text-1008',
  ),
  'header-widget-2' => 
  array (
    0 => 'themify-social-links-1009',
  ),
  'footer-widget-1' => 
  array (
    0 => 'themify-feature-posts-1010',
  ),
  'footer-widget-2' => 
  array (
    0 => 'text-1011',
  ),
  'footer-widget-3' => 
  array (
    0 => 'themify-twitter-1012',
  ),
); 
update_option( "sidebars_widgets", $sidebars_widgets );

$menu_locations = array();
$menu = get_terms( "nav_menu", array( "slug" => "header-menu" ) );
if( is_array( $menu ) && ! empty( $menu ) ) $menu_locations["main-nav"] = $menu[0]->term_id;
$menu = get_terms( "nav_menu", array( "slug" => "footer-menu" ) );
if( is_array( $menu ) && ! empty( $menu ) ) $menu_locations["footer-nav"] = $menu[0]->term_id;
set_theme_mod( "nav_menu_locations", $menu_locations );


$homepage = get_posts( array( 'name' => 'home-accountant', 'post_type' => 'page' ) );
			if( is_array( $homepage ) && ! empty( $homepage ) ) {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $homepage[0]->ID );
			}
			
	ob_start(); ?>a:104:{s:16:"setting-page_404";s:1:"0";s:21:"setting-webfonts_list";s:11:"recommended";s:22:"setting-default_layout";s:8:"sidebar1";s:27:"setting-default_post_layout";s:9:"list-post";s:19:"setting-post_filter";s:3:"yes";s:23:"setting-disable_masonry";s:3:"yes";s:19:"setting-post_gutter";s:6:"gutter";s:30:"setting-default_layout_display";s:7:"excerpt";s:25:"setting-default_more_text";s:9:"Read More";s:20:"setting-excerpt_more";s:1:"1";s:21:"setting-index_orderby";s:4:"date";s:19:"setting-index_order";s:4:"DESC";s:30:"setting-default_media_position";s:5:"above";s:31:"setting-image_post_feature_size";s:5:"blank";s:32:"setting-default_page_post_layout";s:8:"sidebar1";s:37:"setting-default_page_post_layout_type";s:7:"classic";s:38:"setting-image_post_single_feature_size";s:5:"blank";s:27:"setting-default_page_layout";s:8:"sidebar1";s:38:"setting-default_portfolio_index_layout";s:12:"sidebar-none";s:43:"setting-default_portfolio_index_post_layout";s:5:"grid3";s:29:"setting-portfolio_post_filter";s:3:"yes";s:33:"setting-portfolio_disable_masonry";s:3:"yes";s:24:"setting-portfolio_gutter";s:6:"gutter";s:39:"setting-default_portfolio_index_display";s:4:"none";s:50:"setting-default_portfolio_index_post_meta_category";s:3:"yes";s:39:"setting-default_portfolio_single_layout";s:12:"sidebar-none";s:54:"setting-default_portfolio_single_portfolio_layout_type";s:9:"fullwidth";s:22:"themify_portfolio_slug";s:7:"project";s:53:"setting-customizer_responsive_design_tablet_landscape";s:4:"1024";s:43:"setting-customizer_responsive_design_tablet";s:3:"768";s:43:"setting-customizer_responsive_design_mobile";s:3:"480";s:33:"setting-mobile_menu_trigger_point";s:4:"1000";s:24:"setting-gallery_lightbox";s:8:"lightbox";s:26:"setting-page_builder_cache";s:2:"on";s:18:"setting-cache_gzip";s:2:"on";s:27:"setting-script_minification";s:7:"disable";s:27:"setting-page_builder_expiry";s:1:"2";s:21:"setting-header_design";s:18:"header-top-widgets";s:27:"setting-exclude_search_form";s:2:"on";s:19:"setting-exclude_rss";s:2:"on";s:29:"setting-exclude_social_widget";s:2:"on";s:22:"setting-header_widgets";s:17:"headerwidget-2col";s:21:"setting-footer_design";s:22:"footer-horizontal-left";s:22:"setting-footer_widgets";s:4:"none";s:27:"setting-imagefilter_applyto";s:9:"allimages";s:29:"setting-color_animation_speed";s:1:"5";s:29:"setting-relationship_taxonomy";s:8:"category";s:37:"setting-relationship_taxonomy_entries";s:1:"3";s:45:"setting-relationship_taxonomy_display_content";s:4:"none";s:30:"setting-single_slider_autoplay";s:3:"off";s:27:"setting-single_slider_speed";s:6:"normal";s:28:"setting-single_slider_effect";s:5:"slide";s:28:"setting-single_slider_height";s:4:"auto";s:18:"setting-more_posts";s:8:"infinite";s:20:"setting-autoinfinite";s:2:"on";s:19:"setting-entries_nav";s:8:"numbered";s:24:"setting-footer_text_left";s:99:"&copy; Copyright <a href="http://themify.me/" target="_blank">Themify</a> 2017. All right reserved.";s:30:"setting-footer_text_right_hide";s:4:"hide";s:27:"setting-global_feature_size";s:5:"blank";s:22:"setting-link_icon_type";s:9:"font-icon";s:32:"setting-link_type_themify-link-0";s:10:"image-icon";s:33:"setting-link_title_themify-link-0";s:7:"Twitter";s:31:"setting-link_img_themify-link-0";s:99:"https://themify.me/demo/themes/ultra/wp-content/themes/themify-ultra/themify/img/social/twitter.png";s:32:"setting-link_type_themify-link-1";s:10:"image-icon";s:33:"setting-link_title_themify-link-1";s:8:"Facebook";s:31:"setting-link_img_themify-link-1";s:100:"https://themify.me/demo/themes/ultra/wp-content/themes/themify-ultra/themify/img/social/facebook.png";s:32:"setting-link_type_themify-link-2";s:10:"image-icon";s:33:"setting-link_title_themify-link-2";s:6:"Google";s:31:"setting-link_img_themify-link-2";s:103:"https://themify.me/demo/themes/ultra/wp-content/themes/themify-ultra/themify/img/social/google-plus.png";s:32:"setting-link_type_themify-link-3";s:10:"image-icon";s:33:"setting-link_title_themify-link-3";s:7:"YouTube";s:31:"setting-link_img_themify-link-3";s:99:"https://themify.me/demo/themes/ultra/wp-content/themes/themify-ultra/themify/img/social/youtube.png";s:32:"setting-link_type_themify-link-4";s:10:"image-icon";s:33:"setting-link_title_themify-link-4";s:9:"Pinterest";s:31:"setting-link_img_themify-link-4";s:101:"https://themify.me/demo/themes/ultra/wp-content/themes/themify-ultra/themify/img/social/pinterest.png";s:32:"setting-link_type_themify-link-6";s:9:"font-icon";s:33:"setting-link_title_themify-link-6";s:8:"Facebook";s:32:"setting-link_link_themify-link-6";s:28:"https://facebook.com/themify";s:33:"setting-link_ficon_themify-link-6";s:11:"fa-facebook";s:32:"setting-link_type_themify-link-5";s:9:"font-icon";s:33:"setting-link_title_themify-link-5";s:7:"Twitter";s:32:"setting-link_link_themify-link-5";s:27:"https://twitter.com/themify";s:33:"setting-link_ficon_themify-link-5";s:10:"fa-twitter";s:33:"setting-link_type_themify-link-10";s:9:"font-icon";s:34:"setting-link_title_themify-link-10";s:9:"Instagram";s:33:"setting-link_link_themify-link-10";s:18:"https://themify.me";s:34:"setting-link_ficon_themify-link-10";s:12:"ti-instagram";s:33:"setting-link_type_themify-link-11";s:9:"font-icon";s:34:"setting-link_title_themify-link-11";s:9:"Pinterest";s:33:"setting-link_link_themify-link-11";s:18:"https://themify.me";s:34:"setting-link_ficon_themify-link-11";s:12:"ti-pinterest";s:32:"setting-link_type_themify-link-8";s:9:"font-icon";s:33:"setting-link_title_themify-link-8";s:7:"YouTube";s:32:"setting-link_link_themify-link-8";s:32:"https://youtube.com/user/themify";s:33:"setting-link_ficon_themify-link-8";s:10:"fa-youtube";s:33:"setting-link_type_themify-link-12";s:9:"font-icon";s:34:"setting-link_title_themify-link-12";s:11:"Google Plus";s:33:"setting-link_link_themify-link-12";s:18:"https://themify.me";s:34:"setting-link_ficon_themify-link-12";s:9:"ti-google";s:22:"setting-link_field_ids";s:381:"{"themify-link-0":"themify-link-0","themify-link-1":"themify-link-1","themify-link-2":"themify-link-2","themify-link-3":"themify-link-3","themify-link-4":"themify-link-4","themify-link-6":"themify-link-6","themify-link-5":"themify-link-5","themify-link-10":"themify-link-10","themify-link-11":"themify-link-11","themify-link-8":"themify-link-8","themify-link-12":"themify-link-12"}";s:23:"setting-link_field_hash";s:2:"13";s:30:"setting-page_builder_is_active";s:6:"enable";s:46:"setting-page_builder_animation_parallax_scroll";s:6:"mobile";s:4:"skin";s:106:"https://themify.me/demo/themes/ultra-accountant/wp-content/themes/themify-ultra/skins/accountant/style.css";}<?php $themify_data = unserialize( ob_get_clean() );

	// fix the weird way "skin" is saved
	if( isset( $themify_data['skin'] ) ) {
		$parsed_skin = parse_url( $themify_data['skin'], PHP_URL_PATH );
		$basedir_skin = basename( dirname( $parsed_skin ) );
		$themify_data['skin'] = trailingslashit( get_template_directory_uri() ) . 'skins/' . $basedir_skin . '/style.css';
	}

	themify_set_data( $themify_data );
	
}
themify_do_demo_import();