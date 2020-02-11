<?php

namespace TwentyTwenty\Product;

use TwentyTwenty\Product;
use TwentyTwenty\Traits\Singleton;
use WP_Post;

class Attributes {
	use Singleton;

	protected function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'addMetaBox' ] );
		add_action( 'save_post_' . Product::POST_TYPE, [ $this, 'updateAttributes' ] );
	}

	public function addMetaBox(): void {
		add_meta_box( 'product-attributes', __( 'Product attributes', 'twentytwenty-child' ), [ $this, 'renderMetaBox' ],
			Product::POST_TYPE );
	}

	public function renderMetaBox( WP_Post $post ): void {
		$alignText = is_rtl() ? 'textright' : 'textleft';

		?>
		<table class="form-table">
		<tr valign="top">
			<td scope="row"><?php _e( 'Price', 'twentytwenty-child' ) ?></td>
			<td>
				<input type="text" class="large-text ltr <?= $alignText ?>" name="price" value="<?= esc_attr( $post->price ); ?>"/>
				<p class="description"><?php _e( 'Price in USD', 'twentytwenty-child' ) ?></p>
			</td>
		</tr>
		<tr valign="top">
			<td scope="row"><?php _e( 'Sale Price', 'twentytwenty-child' ) ?></td>
			<td>
				<input type="text" class="large-text ltr <?= $alignText ?>" name="sale_price" value="<?= esc_attr( $post->sale_price ) ?>"/>
				<p class="description"><?php _e( 'Sale price in USD', 'twentytwenty-child' ) ?></p>
			</td>
		</tr>
		<tr valign="top">
			<td colspan="2">
				<label for="on_sale">
					<input name="on_sale" id="on_sale" type="checkbox" value="1" <?php checked( $post->on_sale,
						1 ); ?> />
					<?php _e( 'Is on sale?', 'twentytwenty-child' ); ?>
				</label>
			</td>
		</tr>
		<tr valign="top">
			<td scope="row"><?php _e( 'YouTube', 'twentytwenty-child' ) ?></td>
			<td>
				<input type="url" class="large-text ltr <?= $alignText ?>" name="youtube_url" value="<?= esc_url( $post->youtube_url ); ?>"/>
				<p class="description"><?php _e( 'YouTube video link', 'twentytwenty-child' ) ?></p>
			</td>
		</tr>
		</table><?php
	}

	public function updateAttributes( $postId ): void {
		$price      = sanitize_text_field( $_POST['price'] ?? 0 );
		$onSale     = sanitize_text_field( $_POST['on_sale'] ?? false );
		$salePrice  = sanitize_text_field( $_POST['sale_price'] ?? 0 );
		$youtubeUrl = sanitize_text_field( $_POST['youtube_url'] ?? '' );

		update_post_meta( $postId, 'price', $price );
		update_post_meta( $postId, 'on_sale', $onSale );
		update_post_meta( $postId, 'sale_price', $salePrice );
		update_post_meta( $postId, 'youtube_url', $youtubeUrl );
	}
}
