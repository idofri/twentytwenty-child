<?php

namespace TwentyTwenty\Product;

use TwentyTwenty\Product;
use TwentyTwenty\Traits\Singleton;
use WP_Post;
use WP_REST_Request;
use WP_Term;

class Rest {
	use Singleton;

	protected function __construct() {
		add_action( 'rest_api_init', [ $this, 'init' ] );
	}

	public function init() {
		register_rest_route( 'products/v1', '/category/(?P<category>.+)', [
			'methods'  => 'GET',
			'callback' => [ $this, 'searchByTerm' ],
		] );
	}

	public function searchByTerm( WP_REST_Request $request ): array {
		$term = $this->getTermByNameOrId( $request->get_param( 'category' ) );
		if ( ! $term ) {
			return [];
		}

		$items = $this->getTermProducts( $term );
		if ( ! $items ) {
			return [];
		}

		$result = [];
		foreach ( $items as $item ) {
			$result[] = $this->prepareResponseItem( $item );
		}

		return $result;
	}

	private function getTermByNameOrId( string $value ) {
		$field = ( (int) $value == $value && (int) $value > 0 ) ? 'id' : 'name';

		return get_term_by( $field, $value, Product::TAXONOMY );
	}

	private function getTermProducts( WP_Term $term ): array {
		return get_posts( [
			'post_type'      => Product::POST_TYPE,
			'posts_per_page' => - 1,
			'tax_query'      => [
				[
					'taxonomy' => $term->taxonomy,
					'terms'    => $term->term_id,
					'field'    => 'term_id',
				]
			]
		] );
	}

	private function prepareResponseItem( WP_Post $post ): array {
		return [
			'title'       => $post->post_title,
			'description' => apply_filters( 'the_content', $post->post_content ),
			'image'       => get_the_post_thumbnail_url( $post ),
			'price'       => (float) get_post_meta( $post->ID, 'price', true ),
			'on_sale'     => (bool) get_post_meta( $post->ID, 'on_sale', true ),
			'sale_price'  => (float) get_post_meta( $post->ID, 'sale_price', true ),
		];
	}
}
