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

// Check if product ID is provided
$gallery_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
if (!$gallery_id) {
    /*
  wp_safe_redirect() must be called before any output (HTML, whitespace, get_header(), echo, etc.).
  Once PHP sends headers, it cannot change them — so the redirect silently fails.
  */
    wp_safe_redirect(home_url('/gallery/'));
    exit;
}

get_header();

$container = get_theme_mod('understrap_container_type');
$container_classes = $container . ' px-4 py-0';
?>

<main>

    <div class="<?php echo esc_attr($container_classes); ?>" id="content" tabindex="-1">

        <div class="row">
            <div class="sidebar-left-column order-2 order-md-1" id="sidebar-left">
                <?php
                get_template_part('sidebar-templates/sidebar', 'left');
                ?>
            </div>
            <div class="main-content-column order-1 order-md-2">
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
                            <div class="card bg-body bg-secondary text-body p-3 popup-gallery">
                                <div class="gallery-grid">
                                    <?php foreach ($images as $image) :
                                        $uploads   = wp_get_upload_dir();
                                        $image_url = esc_url($uploads['baseurl'] . '/' . $image->image_path);
                                    ?>
                                        <div class="gallery-item">
                                            <a href="<?= $image_url ?>" data-fancybox="gallery" data-caption="<?= esc_attr($image->caption) ?>">
                                                <img src="<?= $image_url ?>" class="gallery-image" alt="<?= esc_attr($image->caption) ?>">
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Back button -->
                        <div class="text-end mt-4">
                            <a href="<?= esc_url(home_url('/gallery')) ?>"><i class="fa fa-backward" aria-hidden="true"></i>
                                ย้อนกลับ</a>
                        </div>

                    </div>

                </div><!-- /#post-<?php the_ID(); ?> -->

            </div>

        </div><!-- .row -->

    </div><!-- #content -->

</main><!-- #main-wrapper -->
<script>
    jQuery(document).ready(function($) {
        Fancybox.bind("[data-fancybox='gallery']", {
            Thumbs: {
                autoStart: false
            },
            Toolbar: {
                display: [
                    "zoom",
                    "slideshow",
                    "thumbs",
                    "close"
                    // omit "fullscreen" to disable it
                ]
            }
        });
    });
</script>
<style>
    /* Main gallery grid */
    .gallery-grid {
        display: grid;
        /* Use CSS Grid layout */
        gap: 10px;
        /* Space between rows and columns */
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        /* Create responsive columns: minimum 120px, maximum 1 fraction of available space */
        grid-auto-rows: 180px;
        /* Set default row height for all items */
        grid-auto-flow: dense;
        /* Fill in gaps with remaining items for a compact layout */
    }

    /* Individual gallery item */
    .gallery-item {
        position: relative;
        /* Allow absolute positioning of child elements if needed */
        overflow: hidden;
        /* Crop overflowing image content */
    }

    /* Images inside gallery items */
    .gallery-image {
        width: 100%;
        /* Fill width of the cell */
        height: 100%;
        /* Fill height of the cell */
        object-fit: cover;
        /* Cover the cell without distortion */
        display: block;
        /* Remove inline spacing */
        transition: transform 0.3s ease, opacity 0.3s ease;
        /* Smooth hover animation */
    }

    /* Hover effect for gallery images */
    .gallery-item:hover .gallery-image {
        transform: scale(1.05);
        /* Slight zoom in */
        opacity: 0.85;
        /* Dim the image slightly */
    }

    /* Dynamic Facebook-style layout for desktop / tablet */
    @media (min-width: 480px) {
        .gallery-item:first-child {
            grid-row: span 2;
            /* Make the first item span 2 rows */
            grid-column: span 2;
            /* Make the first item span 2 columns */
        }

        .gallery-item:nth-child(5n) {
            grid-column: span 2;
            /* Every 5th item spans 2 columns for variation */
        }
    }

    /* Responsive layout for small screens / mobile */
    @media (max-width: 480px) {
        .gallery-grid {
            display: grid;
            grid-template-columns: 1fr;
            /* One item per row */
            grid-auto-rows: auto;
            /* Row height adjusts to image aspect ratio */
            gap: 10px;
            /* Space between items */
        }

        .gallery-item {
            width: 100%;
            /* Fill full width of the row */
            height: 100%;
            /* Fill full height of the row */
        }

        .gallery-image {
            width: 100%;
            /* Fill width of the item */
            height: 100%;
            /* Fill height of the item */
            object-fit: cover;
            /* Cover the cell without distortion */
        }
    }
</style>
<?php
get_footer();
