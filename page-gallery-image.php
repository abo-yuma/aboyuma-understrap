<?php

/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();

$container = get_theme_mod('understrap_container_type');

?>

<main>

    <div class="<?php echo esc_attr($container); ?>" id="content" tabindex="-1">

        <div class="row py-3">
            <div class="sidebar-left-column-width" id="sidebar-left">
                <?php
                get_template_part('sidebar-templates/sidebar', 'left');
                ?>
            </div>
            <div class="main-content-column-width">
                <div id="post-<?php the_ID(); ?>">
                    <?php
                    /**
                     * Template Name: Single Gallery Page
                     */
                    get_header();
                    global $wpdb;

                    $gallery_table       = $wpdb->prefix . 'aboyuma_gallery';
                    $gallery_image_table = $wpdb->prefix . 'aboyuma_gallery_image';

                    // Get gallery ID from URL (?id=)
                    $gallery_id = intval($_GET['id'] ?? 0);

                    if (!$gallery_id) {
                        echo '<p>No gallery specified.</p>';
                        get_footer();
                        exit;
                    }

                    // Fetch gallery info
                    $gallery = $wpdb->get_row($wpdb->prepare("
                        SELECT * 
                        FROM {$gallery_table} 
                        WHERE id = %d
                    ", $gallery_id));

                    if (!$gallery) {
                        echo '<p>Gallery not found.</p>';
                        get_footer();
                        exit;
                    }

                    // Fetch images for this gallery ordered by image_order
                    $images = $wpdb->get_results($wpdb->prepare("
                        SELECT * 
                        FROM {$gallery_image_table} 
                        WHERE gallery_id = %d 
                        ORDER BY image_order ASC, id ASC
                    ", $gallery_id));
                    ?>

                    <div class="container">

                        <h2 class="gallery-title">🖼️ <?= esc_html($gallery->title) ?></h2>
                        <?php if ($gallery->gallery_date) : ?>
                            <p class="gallery-date text-muted">🗓️ <?= esc_html(date('Y-m-d', strtotime($gallery->gallery_date))) ?></p>
                        <?php endif; ?>
                        <?php if ($gallery->description) : ?>
                            <p class="gallery-description">📝 <?= esc_html($gallery->description) ?></p>
                        <?php endif; ?>

                        <?php if ($images) : ?>
                            <div class="card bg-body bg-secondary text-body p-3 popup-gallery"> <!-- Wrap all images in one card -->
                                <div class="row">
                                    <?php foreach ($images as $image) :
                                        $uploads   = wp_get_upload_dir();
                                        $image_url = esc_url($uploads['baseurl'] . '/' . $image->image_path);
                                    ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card">
                                                <a href="<?= $image_url ?>" title="<?= esc_attr($image->caption) ?>">
                                                    <img src="<?= $image_url ?>" class="gallery-image" alt="<?= esc_attr($image->caption) ?>">
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <!-- Back button -->
                        <div class="text-end mt-4">
                            <a href="<?= esc_url(home_url('/gallery')) ?>">ย้อนกลับ</a>
                        </div>

                    </div>

                </div><!-- /#post-<?php the_ID(); ?> -->

            </div>

        </div><!-- .row -->

    </div><!-- #content -->

</main><!-- #main-wrapper -->
<script>
    jQuery(document).ready(function($) {
        $('.popup-gallery').magnificPopup({
            delegate: 'a',
            type: 'image',
            gallery: {
                enabled: true,
                navigateByImgClick: true,
                preload: [0, 1]
            },
            image: {
                titleSrc: 'title'
            }
        });
    });
</script>
<style>
    .gallery-title {
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .gallery-description {
        margin-bottom: 0.5rem;
    }

    .gallery-date {
        margin-bottom: 1rem;
    }

    .gallery-image {
        width: 100%;
        aspect-ratio: 1 / 1;
        /* force square */
        object-fit: cover;
        /* cover without distortion */
    }
</style>
<?php
get_footer();
