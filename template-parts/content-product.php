<?php defined( 'ABSPATH' ) || exit; ?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<?php get_template_part( 'template-parts/entry-header' ); ?>

	<div class="post-inner">

		<div class="entry-content">

			<?php the_content( __( 'Continue reading', 'twentytwenty-child' ) ); ?>

			<?php get_template_part( 'template-parts/price' ); ?>

			<?php if ( is_singular() && $attachmentIds = gallery_items() ) : ?>
				<h4><u><?php _e( 'Product Gallery', 'twentytwenty-child' ); ?></u></h4>
				<figure class="wp-block-gallery columns-3 is-cropped">
					<ul class="blocks-gallery-grid">
						<?php foreach ( $attachmentIds as $attachmentId ) : ?>
							<li class="blocks-gallery-item">
								<figure>
									<?= wp_get_attachment_image( $attachmentId, '' ); ?>
								</figure>
							</li>
						<?php endforeach; ?>
					</ul>
				</figure>
			<?php endif; ?>

			<?php if ( is_singular() && $relatedProducts = related_products() ) : ?>
				<h4><u><?php _e( 'Related Products', 'twentytwenty-child' ); ?></u></h4>
				<div class="wp-block-columns">
					<?php
					foreach ( $relatedProducts as $relatedProduct ) {
						echo '<div class="wp-block-column">';
						echo do_shortcode( sprintf( '[product id="%d"]', $relatedProduct->ID ) );
						echo '</div>';
					}
					?>
				</div>
			<?php endif; ?>
		</div>

	</div>

</article>