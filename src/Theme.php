<?php

namespace TwentyTwenty;

use TwentyTwenty\Traits\Singleton;

class Theme {
	use Singleton;

	protected function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'frontEnqueueStyles' ] );
	}

	public function frontEnqueueStyles(): void {
		wp_enqueue_style( 'twentytwenty-style', get_template_directory_uri() . '/style.css', [],
			wp_get_theme()->get( 'Version' ) );
		wp_style_add_data( 'twentytwenty-style', 'rtl', 'replace' );
		wp_enqueue_style( 'twentytwenty-child-style', get_stylesheet_directory_uri() . '/style.css',
			[ 'twentytwenty-style' ], wp_get_theme()->get( 'Version' ) );
	}
}
