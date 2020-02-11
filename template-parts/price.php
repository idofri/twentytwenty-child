<?php defined( 'ABSPATH' ) || exit; ?>

<div class="">
	<u><?php _e( 'Price:', 'twentytwenty-child' ); ?></u>
	<?php if ( on_sale() ) : ?>
	<s>$<?= $post->price; ?></s>
	<strong>$<?= $post->sale_price; ?></strong>
	<?php else : ?>
	<strong>$<?= $post->price; ?></strong>
	<?php endif; ?>
</div>