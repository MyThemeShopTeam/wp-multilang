<?php

namespace WPM\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class WPM_Posts
 * @package  WPM\Core
 * @author   VaLeXaR
 * @version  1.1.2
 */
class WPM_Posts extends \WPM_Object {

	/**
	 * Object name
	 * @var string
	 */
	public $object_type = 'post';

	/**
	 * Table name for meta
	 * @var string
	 */
	public $object_table = 'postmeta';


	/**
	 * WPM_Posts constructor.
	 */
	public function __construct() {
		add_filter( 'get_pages', array( $this, 'translate_posts' ), 0 );
		add_filter( 'posts_results', array( $this, 'translate_posts' ), 0 );
		add_action( 'parse_query', array( $this, 'filter_posts_by_language' ) );
		add_filter( 'the_post', 'wpm_translate_object', 0 );
		add_filter( 'the_title', 'wpm_translate_string', 0 );
		add_filter( 'the_content', 'wpm_translate_string', 0 );
		add_filter( 'the_excerpt', 'wpm_translate_string', 0 );
		add_filter( "get_{$this->object_type}_metadata", array( $this, 'get_meta_field' ), 0, 3 );
		add_filter( "update_{$this->object_type}_metadata", array( $this, 'update_meta_field' ), 99, 5 );
		add_filter( "add_{$this->object_type}_metadata", array( $this, 'add_meta_field' ), 99, 5 );
		add_action( 'wp', array( $this, 'translate_queried_object' ), 0 );
	}


	/**
	 * Translate all posts
	 *
	 * @param $posts
	 *
	 * @return array
	 */
	public function translate_posts( $posts ) {
		return array_map( 'wpm_translate_object', $posts );
	}

	/**
	 * Separate posts py languages
	 *
	 * @param $query object WP_Query
	 *
	 * @return object WP_Query
	 */
	public function filter_posts_by_language( $query ) {
		if ( ( ! is_admin() || wp_doing_ajax() ) && ! defined( 'DOING_CRON' ) ) {

			if ( isset( $query->query_vars['post_type'] ) && ! empty( $query->query_vars['post_type'] ) ) {

				$post_type = $query->query_vars['post_type'];

				if ( is_string( $post_type ) ) {

					$config                     = wpm_get_config();
					$posts_config               = $config['post_types'];
					$posts_config               = apply_filters( 'wpm_posts_config', $posts_config );
					$posts_config[ $post_type ] = apply_filters( "wpm_post_{$post_type}_config", isset( $posts_config[ $post_type ] ) ? $posts_config[ $post_type ] : null );

					if ( ! isset( $config['post_types'][ $post_type ] ) || is_null( $config['post_types'][ $post_type ] ) ) {
						return $query;
					}
				}
			}

			$lang = get_query_var( 'lang' );

			if ( ! $lang && ! $query->is_main_query() ) {
				$lang = wpm_get_user_language();
			}

			if ( $lang ) {
				$lang_meta_query = array(
					array(
						'relation' => 'OR',
						array(
							'key'     => '_languages',
							'compare' => 'NOT EXISTS',
						),
						array(
							'key'     => '_languages',
							'value'   => 's:' . strlen( $lang ) . ':"' . $lang . '";',
							'compare' => 'LIKE',
						),
					),
				);

				if ( isset( $query->query_vars['meta_query'] ) ) {
					$lang_meta_query = wp_parse_args( $query->query_vars['meta_query'], $lang_meta_query );
				}

				$query->set( 'meta_query', $lang_meta_query );
			}
		} // End if().

		return $query;
	}


	/**
	 * Translate queried object in global $wp_query
	 */
	public function translate_queried_object() {
		global $wp_query;
		if ( is_singular() ) {
			$wp_query->queried_object = wpm_translate_object( $wp_query->queried_object );
		}
	}
}
