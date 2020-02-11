<?php defined( 'ABSPATH' ) || exit; ?>

<div style="background-color: <?= $atts['bg_color']; ?>">
	<div class="box-row">
		<a href="<?= esc_url( get_permalink() ); ?>">
			<?php if ( on_sale() ) : ?>
				<div class="on-sale"><?php _e( 'Sale!', 'twentytwenty-child' ); ?></div>
			<?php endif; ?>

			<?php the_post_thumbnail(); ?>

			<div class="has-text-align-center">
				<?php the_title( '<h3 class="has-text-align-center">', '</h3>' ); ?>

				<?php
				if ( $atts['show_price'] ) {
					get_template_part( 'template-parts/price' );
				}
				?>
			</div>
		</a>
	</div>
</div>