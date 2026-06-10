<?php
/**
 * Duplicate news posts (post type: post).
 *
 * @package GeneratePress_Child
 */

defined( 'ABSPATH' ) || exit;

define( 'RPT_DUPLICATE_POST_ACTION', 'rpt_duplicate_post' );

/**
 * Post types that support duplication.
 *
 * @return array<int, string>
 */
function rpt_get_duplicatable_post_types() {
	/**
	 * Filter post types that can be duplicated from admin.
	 *
	 * @param array<int, string> $post_types Post type slugs.
	 */
	return apply_filters( 'rpt_duplicatable_post_types', array( 'post' ) );
}

/**
 * Add duplicate link to posts list row actions.
 *
 * @param array    $actions Row actions.
 * @param WP_Post  $post    Post object.
 * @return array
 */
function rpt_duplicate_post_row_action( $actions, $post ) {
	if ( ! $post instanceof WP_Post || ! in_array( $post->post_type, rpt_get_duplicatable_post_types(), true ) ) {
		return $actions;
	}

	if ( ! rpt_current_user_can_duplicate_post( $post ) ) {
		return $actions;
	}

	$url = wp_nonce_url(
		add_query_arg(
			array(
				'action' => RPT_DUPLICATE_POST_ACTION,
				'post'   => $post->ID,
			),
			admin_url( 'admin.php' )
		),
		RPT_DUPLICATE_POST_ACTION . '_' . $post->ID
	);

	$actions['rpt_duplicate'] = sprintf(
		'<a href="%1$s" aria-label="%2$s">%3$s</a>',
		esc_url( $url ),
		esc_attr(
			sprintf(
				/* translators: %s: post title */
				__( 'Nhân bản "%s"', 'generatepress_child' ),
				get_the_title( $post )
			)
		),
		esc_html__( 'Nhân bản', 'generatepress_child' )
	);

	return $actions;
}
add_filter( 'post_row_actions', 'rpt_duplicate_post_row_action', 10, 2 );

/**
 * Whether current user can duplicate a post.
 *
 * @param WP_Post $post Post object.
 * @return bool
 */
function rpt_current_user_can_duplicate_post( $post ) {
	$post_type_object = get_post_type_object( $post->post_type );

	if ( ! $post_type_object || ! current_user_can( $post_type_object->cap->edit_posts ) ) {
		return false;
	}

	return current_user_can( 'edit_post', $post->ID );
}

/**
 * Handle duplicate post admin action.
 */
function rpt_handle_duplicate_post_action() {
	if ( empty( $_GET['post'] ) ) {
		wp_die( esc_html__( 'Không tìm thấy bài viết cần nhân bản.', 'generatepress_child' ) );
	}

	$post_id = absint( $_GET['post'] );
	$post    = get_post( $post_id );

	if ( ! $post instanceof WP_Post || ! in_array( $post->post_type, rpt_get_duplicatable_post_types(), true ) ) {
		wp_die( esc_html__( 'Loại nội dung này không hỗ trợ nhân bản.', 'generatepress_child' ) );
	}

	check_admin_referer( RPT_DUPLICATE_POST_ACTION . '_' . $post_id );

	if ( ! rpt_current_user_can_duplicate_post( $post ) ) {
		wp_die( esc_html__( 'Bạn không có quyền nhân bản bài viết này.', 'generatepress_child' ) );
	}

	$new_post_id = rpt_duplicate_post( $post );

	if ( is_wp_error( $new_post_id ) ) {
		wp_die( esc_html( $new_post_id->get_error_message() ) );
	}

	wp_safe_redirect(
		add_query_arg(
			array(
				'action'  => 'edit',
				'post'    => $new_post_id,
				'message' => 'rpt_duplicated',
			),
			admin_url( 'post.php' )
		)
	);
	exit;
}
add_action( 'admin_action_' . RPT_DUPLICATE_POST_ACTION, 'rpt_handle_duplicate_post_action' );

/**
 * Clone a post and related data.
 *
 * @param WP_Post $post Source post.
 * @return int|WP_Error New post ID.
 */
function rpt_duplicate_post( $post ) {
	$new_post_data = array(
		'post_type'      => $post->post_type,
		'post_title'     => rpt_get_duplicate_post_title( $post->post_title ),
		'post_content'   => $post->post_content,
		'post_excerpt'   => $post->post_excerpt,
		'post_status'    => 'draft',
		'post_author'    => get_current_user_id(),
		'post_password'  => $post->post_password,
		'comment_status' => $post->comment_status,
		'ping_status'    => $post->ping_status,
		'menu_order'     => (int) $post->menu_order,
	);

	$new_post_id = wp_insert_post( $new_post_data, true );

	if ( is_wp_error( $new_post_id ) ) {
		return $new_post_id;
	}

	rpt_duplicate_post_taxonomies( $post->ID, $new_post_id, $post->post_type );
	rpt_duplicate_post_meta( $post->ID, $new_post_id );
	rpt_duplicate_post_thumbnail( $post->ID, $new_post_id );

	/**
	 * Fires after a post has been duplicated.
	 *
	 * @param int $new_post_id New post ID.
	 * @param int $post_id     Source post ID.
	 */
	do_action( 'rpt_duplicate_post', $new_post_id, $post->ID );

	return $new_post_id;
}

/**
 * Build duplicated post title.
 *
 * @param string $title Original title.
 * @return string
 */
function rpt_get_duplicate_post_title( $title ) {
	$title = trim( (string) $title );

	if ( '' === $title ) {
		$title = __( '(Không có tiêu đề)', 'generatepress_child' );
	}

	return sprintf(
		/* translators: %s: original post title */
		__( '%s (Bản sao)', 'generatepress_child' ),
		$title
	);
}

/**
 * Copy taxonomies to duplicated post.
 *
 * @param int    $source_id Source post ID.
 * @param int    $target_id Target post ID.
 * @param string $post_type Post type slug.
 */
function rpt_duplicate_post_taxonomies( $source_id, $target_id, $post_type ) {
	$taxonomies = get_object_taxonomies( $post_type );

	if ( empty( $taxonomies ) ) {
		return;
	}

	foreach ( $taxonomies as $taxonomy ) {
		$terms = wp_get_object_terms(
			$source_id,
			$taxonomy,
			array(
				'fields' => 'ids',
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			continue;
		}

		wp_set_object_terms( $target_id, array_map( 'intval', $terms ), $taxonomy, false );
	}
}

/**
 * Meta keys that should not be copied.
 *
 * @return array<int, string>
 */
function rpt_get_duplicate_post_excluded_meta_keys() {
	/**
	 * Filter meta keys excluded when duplicating a post.
	 *
	 * @param array<int, string> $keys Meta keys.
	 */
	return apply_filters(
		'rpt_duplicate_post_excluded_meta_keys',
		array(
			'_edit_lock',
			'_edit_last',
			'_wp_old_slug',
		)
	);
}

/**
 * Copy post meta to duplicated post.
 *
 * @param int $source_id Source post ID.
 * @param int $target_id Target post ID.
 */
function rpt_duplicate_post_meta( $source_id, $target_id ) {
	$excluded = array_flip( rpt_get_duplicate_post_excluded_meta_keys() );
	$meta     = get_post_meta( $source_id );

	if ( empty( $meta ) || ! is_array( $meta ) ) {
		return;
	}

	foreach ( $meta as $meta_key => $values ) {
		if ( isset( $excluded[ $meta_key ] ) ) {
			continue;
		}

		foreach ( $values as $value ) {
			add_post_meta( $target_id, $meta_key, maybe_unserialize( $value ) );
		}
	}
}

/**
 * Copy featured image to duplicated post.
 *
 * @param int $source_id Source post ID.
 * @param int $target_id Target post ID.
 */
function rpt_duplicate_post_thumbnail( $source_id, $target_id ) {
	$thumbnail_id = get_post_thumbnail_id( $source_id );

	if ( $thumbnail_id ) {
		set_post_thumbnail( $target_id, $thumbnail_id );
	}
}

/**
 * Admin notice after successful duplication.
 */
function rpt_duplicate_post_admin_notice() {
	if ( empty( $_GET['message'] ) || 'rpt_duplicated' !== $_GET['message'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Bài viết đã được nhân bản thành bản nháp.', 'generatepress_child' ) . '</p></div>';
}
add_action( 'admin_notices', 'rpt_duplicate_post_admin_notice' );
