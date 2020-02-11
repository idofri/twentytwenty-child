<?php

namespace TwentyTwenty\Product;

use TwentyTwenty\Product;
use TwentyTwenty\Traits\Singleton;
use WP_Post;

class Gallery {
	use Singleton;

	protected function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'addMetaBox' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'adminEnqueueScripts' ] );
		add_action( 'save_post_' . Product::POST_TYPE, [ $this, 'updateGallery' ] );
	}

	public function addMetaBox(): void {
		add_meta_box( 'product-gallery', __( 'Product gallery', 'twentytwenty-child' ), [ $this, 'renderMetaBox' ],
			Product::POST_TYPE, 'side' );
	}

	public function renderMetaBox( WP_Post $post ): void {
		$attachmentIds = $this->getGalleryAttachmentIds( $post );
		?>
		<div id="product_images_container">
		<ul id="product-images">
			<?php foreach ( $attachmentIds as $attachmentId ) : ?>
				<li class="image" data-attachment_id="<?= esc_attr( $attachmentId ); ?>">
					<?= wp_get_attachment_image( $attachmentId, 'thumbnail' ); ?>
					<a href="#" class="delete"><?php esc_html_e( 'Delete', 'twentytwenty-child' ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
		<input type="hidden" id="product_image_gallery" name="product_image_gallery" value="<?= esc_attr( implode( ',', $attachmentIds ) ); ?>">
		</div>
		<div class="add_product_images editor-post-featured-image">
		<button type="button" id="add-to-gallery" class="components-button editor-post-featured-image__toggle">
			<?php _e( 'Add product gallery images', 'twentytwenty-child' ); ?>
		</button>
		</div><?php
	}

	public function getGalleryAttachmentIds( WP_Post $post ) {
		$attachmentIds = explode( ',', get_post_meta( $post->ID, '_product_image_gallery', true ) );

		return array_filter( $attachmentIds, function ( $attachmentId ) {
			return wp_get_attachment_url( $attachmentId );
		} );
	}

	public function adminEnqueueScripts(): void {
		global $post_type;

		if ( Product::POST_TYPE != $post_type ) {
			return;
		}

		wp_enqueue_style( 'product-gallery', get_stylesheet_directory_uri() . '/assets/css/gallery.css', false,
			wp_get_theme()->get( 'Version' ) );
		wp_enqueue_script( 'product-gallery', get_stylesheet_directory_uri() . '/assets/js/gallery.js', [ 'jquery' ],
			wp_get_theme()->get( 'Version' ) );
	}

	public function updateGallery( $postId ): void {
		$attachmentIds = array_map( 'intval', explode( ',', $_POST['product_image_gallery'] ?? '' ) );
		update_post_meta( $postId, '_product_image_gallery', implode( ',', $attachmentIds ) );
	}
}
