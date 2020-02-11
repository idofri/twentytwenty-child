<?php

namespace TwentyTwenty;

use TwentyTwenty\Traits\Singleton;

class Theme {
	use Singleton;

	protected function __construct() {
		add_action( 'after_switch_theme', [ $this, 'createWpTestUser' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'frontEnqueueStyles' ] );
		add_filter( 'show_admin_bar', [ $this, 'wpTestDisableAdminBar' ] );
	}

	public function createWpTestUser() {
		return wp_insert_user( [
			'role'       => 'editor',
			'user_login' => 'wp-test',
			'user_pass'  => '123456789',
			'user_email' => 'wptest@elementor.com'
		] );
	}

	public function frontEnqueueStyles(): void {
		wp_enqueue_style( 'twentytwenty-style', get_template_directory_uri() . '/style.css', [],
			wp_get_theme()->get( 'Version' ) );
		wp_style_add_data( 'twentytwenty-style', 'rtl', 'replace' );
		wp_enqueue_style( 'twentytwenty-child-style', get_stylesheet_directory_uri() . '/style.css',
			[ 'twentytwenty-style' ], wp_get_theme()->get( 'Version' ) );
	}

	public function wpTestDisableAdminBar( $showAdminBar ): bool {
		$currentUser = wp_get_current_user();
		if ( $currentUser && 'wp-test' === $currentUser->user_login ) {
			$showAdminBar = false;
		}

		return $showAdminBar;
	}
}
