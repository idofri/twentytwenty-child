<?php

namespace TwentyTwenty;

use TwentyTwenty\Traits\Singleton;
use WP_Customize_Color_Control;

class Customizer {
	use Singleton;

	protected function __construct() {
		add_action( 'customize_register', [ $this, 'register' ], 20 );
	}

	public function register( $wp_customize ): void {
		$wp_customize->add_setting(
			'address_bar_color',
			[
				'default'           => '',
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			]
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'address_bar_color',
				[
					'label'   => __( 'Address Bar Color', 'twentytwenty-child' ),
					'section' => 'colors',
				]
			)
		);
	}
}
