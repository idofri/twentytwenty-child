<?php

namespace TwentyTwenty;

use TwentyTwenty\Product\Attributes;
use TwentyTwenty\Product\Gallery;
use TwentyTwenty\Product\Rest;
use TwentyTwenty\Traits\Singleton;
use WP_Post;

class Product {
	use Singleton;

	const POST_TYPE = 'product';

	const TAXONOMY = 'product-category';

	protected function __construct() {
		add_action( 'init', [ $this, 'registerPostType' ] );
		add_action( 'init', [ $this, 'registerTaxonomy' ] );
		add_filter( 'the_content', [ $this, 'appendVideoToContent' ] );
		add_filter( 'twentytwenty_disallowed_post_types_for_meta_output', [ $this, 'disallowMetaOutput' ] );

		add_shortcode( self::POST_TYPE, [ $this, 'renderShortcode' ] );

		Gallery::instance();
		Attributes::instance();
	}

	public function registerPostType(): void {
		register_post_type( self::POST_TYPE, [
			'labels'              => [
				'name'               => __( 'Products', 'twentytwenty-child' ),
				'all_items'          => __( 'All Products', 'twentytwenty-child' ),
				'singular_name'      => __( 'Product', 'twentytwenty-child' ),
				'menu_name'          => __( 'Products', 'twentytwenty-child' ),
				'name_admin_bar'     => __( 'Products', 'twentytwenty-child' ),
				'new_item'           => __( 'New Product', 'twentytwenty-child' ),
				'add_new'            => __( 'Add New', 'twentytwenty-child' ),
				'view_item'          => __( 'Show Product', 'twentytwenty-child' ),
				'add_new_item'       => __( 'New Product', 'twentytwenty-child' ),
				'search_items'       => __( 'Search Products', 'twentytwenty-child' ),
				'not_found'          => __( 'No products found', 'twentytwenty-child' ),
				'not_found_in_trash' => __( 'No products found in trash', 'twentytwenty-child' ),
			],
			'public'              => true,
			'hierarchical'        => false,
			'supports'            => [ 'title', 'editor', 'thumbnail' ],
			'menu_icon'           => 'dashicons-products',
			'can_export'          => true,
			'show_in_nav_menus'   => true,
			'show_in_rest'        => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'rewrite'             => true,
			'has_archive'         => true,
			'query_var'           => true,
			'show_ui'             => true,
			'taxonomies'          => [ self::TAXONOMY ]
		] );
	}

	public function registerTaxonomy(): void {
		register_taxonomy(
			self::TAXONOMY,
			[ self::POST_TYPE ],
			[
				'hierarchical'      => true,
				'label'             => __( 'Categories', 'twentytwenty-child' ),
				'labels'            => [
					'name'              => __( 'Product categories', 'twentytwenty-child' ),
					'singular_name'     => __( 'Category', 'twentytwenty-child' ),
					'menu_name'         => _x( 'Categories', 'Admin menu name', 'twentytwenty-child' ),
					'search_items'      => __( 'Search categories', 'twentytwenty-child' ),
					'all_items'         => __( 'All categories', 'twentytwenty-child' ),
					'parent_item'       => __( 'Parent category', 'twentytwenty-child' ),
					'parent_item_colon' => __( 'Parent category:', 'twentytwenty-child' ),
					'edit_item'         => __( 'Edit category', 'twentytwenty-child' ),
					'update_item'       => __( 'Update category', 'twentytwenty-child' ),
					'add_new_item'      => __( 'Add new category', 'twentytwenty-child' ),
					'new_item_name'     => __( 'New category name', 'twentytwenty-child' ),
					'not_found'         => __( 'No categories found', 'twentytwenty-child' ),
				],
				'show_ui'           => true,
				'show_in_rest'      => true,
				'query_var'         => true,
				'show_admin_column' => true,
				'rewrite'           => [
					'slug'         => self::TAXONOMY,
					'with_front'   => false,
					'hierarchical' => true,
				],
			]
		);
	}

	public function getYouTubeId( $url ): string {
		$components = parse_url( $url );
		parse_str( $components['query'] ?? '', $params );

		return $params['v'] ?? '';
	}

	public function appendVideoToContent( string $content ): string {
		global $post;

		if ( is_admin() || ! $post ) {
			return $content;
		}

		$youtubeId = $this->getYouTubeId( $post->youtube_url );
		if ( $youtubeId ) {
			$content .= sprintf(
				'<iframe frameborder="0" class="intrinsic-ignore" width="560" height="315" src="https://www.youtube.com/embed/%s"></iframe>',
				$youtubeId
			);
		}

		return $content;
	}

	public function getRelatedProducts( WP_Post $post ): array {
		return get_posts( [
			'post_type'      => Product::POST_TYPE,
			'exclude'        => [ $post->ID ],
			'posts_per_page' => 3,
			'tax_query'      => [
				[
					'taxonomy' => self::TAXONOMY,
					'terms'    => wp_get_object_terms( $post->ID, self::TAXONOMY, [ 'fields' => 'ids' ] ),
					'field'    => 'term_id',
					'operator' => 'IN'
				]
			]
		] );
	}

	public function disallowMetaOutput( $postTypes ): array {
		$postTypes[] = self::POST_TYPE;

		return $postTypes;
	}

	public function renderShortcode( $atts ) {
		global $post;

		$atts = shortcode_atts( [
			'id'         => 0,
			'bg_color'   => 'none',
			'show_price' => 'false'
		], $atts, self::POST_TYPE );

		$atts['show_price'] = $atts['show_price'] !== 'false';

		$post = get_post( $atts['id'] );
		if ( ! $post || get_post_type( $post ) !== self::POST_TYPE ) {
			return;
		}

		ob_start();
		setup_postdata( $post );
		require locate_template( 'template-parts/loop-product.php' );
		wp_reset_postdata();

		return apply_filters( 'twentytwenty_product_loop_html', ob_get_clean(), $post, $atts );
	}
}
