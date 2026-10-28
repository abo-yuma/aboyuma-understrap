<?php

/**
 * News single post partial template
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;
?>

<article <?php post_class('content-single'); ?> id="post-<?php the_ID(); ?>">

    <header class="entry-header">

        <nav class="breadcrumb" aria-label="breadcrumb">
            <?php
            get_breadcrumb();
            ?>
        </nav>

        <?php the_title('<h1 class="post-entry-title">', '</h1>'); ?>

        <div class="post-entry-meta d-flex flex-wrap gap-2">

            <span>
                <?php
                understrap_posted_on();
                ?>
            </span>

        </div><!-- .entry-meta -->

    </header><!-- .entry-header -->

    <div class="entry-content mt-3">

        <?php
        the_content();
        understrap_link_pages();
        ?>

    </div><!-- .entry-content -->

    <footer class="entry-footer">

        <?php understrap_entry_footer(); ?>

    </footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->