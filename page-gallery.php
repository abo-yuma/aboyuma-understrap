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
                <?php
                the_post();
                get_template_part('loop-templates/content', 'page');
                ?>
                <div id="post-<?php the_ID(); ?>" <?php post_class('content'); ?>>
                    <?php
                    global $wpdb;
                    $gallery_table  = $wpdb->prefix . 'aboyuma_gallery';
                    $image_table   = $wpdb->prefix . 'aboyuma_gallery_image';

                    // Fetch rows with cover image path
                    $rows = $wpdb->get_results("
                        SELECT g.*, gi.image_path
                        FROM {$gallery_table} g
                        LEFT JOIN {$image_table} gi 
                            ON g.cover_image_id = gi.id
                    ");
                    ?>
                    <table id="aboyuma-gallery-list" class="table table-striped table-bordered border-top border-bottom">
                        <thead>
                            <tr>
                                <th>แกลอรี่</th>
                                <th>คำอธิบาย</th>
                                <th>วันที่</th>
                                <th>ดูแกลอรี่</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td>
                                        <div class="gallery-card p-2">
                                            <div class="gallery-cover-wrapper">
                                                <?php
                                                if (!empty($row->image_path)) {
                                                    $uploads   = wp_get_upload_dir();
                                                    $image_url = esc_url($uploads['baseurl'] . '/' . $row->image_path);
                                                    echo '<img src="' . $image_url . '" alt="" class="gallery-cover">';
                                                } else {
                                                    echo '<span style="color:#999;">No image</span>';
                                                }
                                                ?>
                                                <div class="gallery-title">
                                                    <?= esc_html($row->title) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= esc_html($row->description) ?></td>
                                    <td>
                                        <?= esc_html(date('Y-m-d', strtotime($row->gallery_date))) ?>
                                    </td>
                                    <td>
                                        <?php
                                        $gallery_url = add_query_arg(
                                            array('id' => intval($row->id)),
                                            home_url('/gallery-image')
                                        );
                                        echo '<a href="' . esc_url($gallery_url) . '">ดูแกลอรี่</a>';
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>

                </div><!-- /#post-<?php the_ID(); ?> -->

            </div>

        </div><!-- .row -->

    </div><!-- #content -->

</main><!-- #main-wrapper -->
<script>
    jQuery(document).ready(function($) {
        $('#aboyuma-gallery-list').DataTable({
            order: [
                [2, 'desc'] // sort by Date column
            ],
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            columnDefs: [{
                    orderable: false,
                    targets: [0, 1, 2, 3]
                }, // Cover + Action not sortable
                {
                    width: "30%",
                    targets: 0
                }, // Cover
                {
                    width: "40%",
                    targets: 1
                }, // Description
                {
                    width: "15%",
                    targets: 2
                }, // Date
                {
                    width: "15%",
                    targets: 3
                } // Action
            ]
        });
    });
</script>
<style>

    #aboyuma-gallery-list td {
        align-content: center;
    }

    .gallery-cover-wrapper {
        position: relative;
        /* important for overlay */
        width: 100%;
        height: 150px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f0f0f0;
    }

    .gallery-cover {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }

    .gallery-title {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        /* center text */
        color: #fff;
        font-weight: bold;
        text-align: center;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
        /* for readability */
        padding: 5px 10px;
    }
</style>
<?php
get_footer();
