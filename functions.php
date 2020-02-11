<?php

namespace TwentyTwenty {

	require __DIR__ . '/vendor/autoload.php';

	Theme::instance();
	Product::instance();
	Customizer::instance();
};

namespace {

	function on_sale( $post = 0 ): bool {
		$post = get_post( $post );

		return (bool) get_post_meta( $post->ID, 'on_sale', true );
	}

	function related_products( $post = 0 ): array {
		$post = get_post( $post );

		return \TwentyTwenty\Product::instance()->getRelatedProducts( $post );
	}

	function gallery_items( $post = 0 ): array {
		$post = get_post( $post );

		return \TwentyTwenty\Product\Gallery::instance()->getGalleryAttachmentIds( $post );
	}
};
