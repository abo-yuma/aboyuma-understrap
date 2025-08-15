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

<main class="bg-body text-body" data-bs-theme="">

    <div class="<?php echo esc_attr($container); ?>" id="content" tabindex="-1">

        <div class="row">
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
                        ORDER BY g.id DESC
                    ");
                    ?>
                    <table id="aboyuma-gallery-list" class="widefat striped">
                        <thead>
                            <tr>
                                <th>รูปหน้าปก</th>
                                <th>ชื่อแกลอรี่</th>
                                <th>คำอธิบาย</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td>
                                        <?php
                                        if (!empty($row->image_path)) {
                                            $uploads = wp_get_upload_dir();
                                            // Prepend the uploads URL to your relative path
                                            $image_url = esc_url($uploads['baseurl'] . '/' . $row->image_path);
                                            echo '<img src="' . $image_url . '" alt="" style="max-width:150px; max-height:150px;">';
                                        } else {
                                            echo '<span style="color:#999;">No image</span>';
                                        }
                                        ?>
                                    </td>
                                    <td><?= esc_html($row->title) ?></td>
                                    <td><?= esc_html($row->description) ?></td>
                                    <td><?= esc_html($row->created) ?></td>
                                    <td>
                                        ฟฟฟห
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
                [0, 'desc']
            ],
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            columnDefs: [{
                    orderable: false,
                    targets: 1
                } // disable sorting on Cover column
            ]
        });
    });
</script>
<?php
get_footer();
