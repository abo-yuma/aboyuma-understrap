<article <?php post_class('archive-loop-item mb-4'); ?> id="post-<?php the_ID(); ?>">

	<div class="row g-3">

		<div class="col-3">

			<?php if (has_post_thumbnail()) : ?>

				<a href="<?php the_permalink(); ?>">
					<?php
					the_post_thumbnail(
						'thumbnail',
						[
							'class' => 'img-fluid img-thumbnail rounded',
						]
					);
					?>
				</a>

			<?php else : ?>

				<a href="<?php the_permalink(); ?>">
					<img
						src="https://placehold.co/150x150"
						alt=""
						width="150"
						height="150"
						class="img-fluid img-thumbnail rounded">
				</a>

			<?php endif; ?>

		</div>

		<div class="col-9">

			<header class="entry-header">

				<a
					href="<?php the_permalink(); ?>"
					class="text-decoration-none">

					<?php
					the_title(
						'<h2 class="entry-title mb-1">',
						'</h2>'
					);
					?>

				</a>

				<?php if ('post' === get_post_type()) : ?>

					<div class="entry-meta">
						<?php understrap_posted_on(); ?>
					</div>

				<?php endif; ?>

			</header>

			<div class="entry-content text-muted small">
				<?php the_excerpt(); ?>
				<div class="text-end mt-2">
					<a
						href="<?php the_permalink(); ?>"
						class="btn btn-outline-primary btn-sm">
						อ่านบทความ →
					</a>
				</div>
			</div>

			<footer class="entry-footer">

				<?php understrap_entry_footer(); ?>

			</footer>

		</div>

	</div>

</article>