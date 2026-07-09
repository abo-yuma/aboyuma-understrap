<?php

/**
 * Sidebar Template.
 */
?>

<div id="sidebar-left-widget-wrapper" class="sidebar-left-widget-wrapper">

	<div class="authorbox">

		<?php
		$author_id = 1;

		$first_name = get_the_author_meta('first_name', $author_id);
		$last_name  = get_the_author_meta('last_name', $author_id);
		$bio        = get_the_author_meta('description', $author_id);

		$name = trim($first_name . ' ' . $last_name);
		if (empty($name)) {
			$name = get_the_author_meta('display_name', $author_id);
		}

		$facebook = 'https://www.facebook.com/profile.php?id=100093053005799';
		?>

		<div class="card bg-body-secondary mb-4 rounded-3">

			<div class="text-center pt-4">
				<?php echo get_avatar($author_id, 180, '', '', [
					'class' => 'rounded-circle img-fluid',
				]); ?>
			</div>

			<div class="card-body text-center p-3">

				<h5 class="fw-medium mb-2 fs-6">
					<?php echo esc_html($name); ?>
				</h5>

				<?php if (!empty($bio)) : ?>
					<p class="card-text small mb-3">
						<?php echo esc_html($bio); ?>
					</p>
				<?php endif; ?>

				<a href="<?php echo esc_url(home_url('/about-me/')); ?>"
					class="btn btn-outline-primary btn-sm w-100">


					🙋 เกี่ยวกับเรา

				</a>

			</div>

		</div>

	</div>

	<?php
$products = [
    ['name'=>'Wireless Mouse','image'=>'https://picsum.photos/seed/mouse/600/400'],
    ['name'=>'Keyboard','image'=>'https://picsum.photos/seed/keyboard/600/400'],
    ['name'=>'Headset','image'=>'https://picsum.photos/seed/headset/600/400'],
    ['name'=>'Watch','image'=>'https://picsum.photos/seed/watch/600/400'],
    ['name'=>'USB Hub','image'=>'https://picsum.photos/seed/hub/600/400'],
];
?>

<div class="card" style="max-width:420px;margin:auto;">
    <img id="productImage" src="<?= $products[0]['image']; ?>" class="card-img-top">

    <div class="card-body text-center">
        <h3 id="productName"><?= $products[0]['name']; ?></h3>

        <button id="spin" class="btn btn-primary">
            🎲 สุ่มสินค้า
        </button>
    </div>
</div>

<script>

const products = <?= json_encode($products); ?>;

const img = document.getElementById('productImage');
const name = document.getElementById('productName');
const btn = document.getElementById('spin');

btn.onclick = () => {

    btn.disabled = true;

    let count = 0;

    const interval = setInterval(() => {

        const p = products[Math.floor(Math.random()*products.length)];

        img.src = p.image;
        name.textContent = p.name;

        count++;

        if(count >= 25){

            clearInterval(interval);

            btn.disabled = false;

        }

    },100);

};

</script>

	<?php
	if (is_active_sidebar('left-sidebar') || is_archive() || is_single()):
	?>
		<div class="sticky-widget-wrapper">

			<div id="widget-area" class="widget-area" role="complementary">
				<?php
				dynamic_sidebar('left-sidebar');

				if (current_user_can('manage_options')):
				?>
					<a href="<?php echo esc_url(admin_url('widgets.php')); ?>">
						<?php esc_html_e('Edit widget'); ?></a>
				<?php
				endif;
				?>
			</div><!-- /.widget-area -->
		</div>
	<?php
	endif;
	?>

</div><!-- /#widget-wrapper -->