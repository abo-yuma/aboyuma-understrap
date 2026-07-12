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
$container_classes = $container . ' px-4 py-0';
?>

<main>

    <div class="<?php echo esc_attr($container_classes); ?>" id="content" tabindex="-1">

        <div class="row">
            <div class="sidebar-left-column order-2" id="sidebar-left">
                <?php
                get_template_part('sidebar-templates/sidebar', 'left');
                ?>
            </div>
            <div class="main-content-column order-1">
                <div class="main-content-wrapper mb-2">
                    <?php
                    while (have_posts()) {
                        the_post();
                        get_template_part('loop-templates/content', 'page');
                    }
                    ?>
                    <div id="aboyuma-amway-shop-wrapper" class="aboyuma-amway-shop-wrapper">

                        <?php
                        global $wpdb;

                        $shop_table  = $wpdb->prefix . 'aboyuma_amway_shop';

                        $shops = $wpdb->get_results("
                        SELECT 
                            id,
                            shop_image,
                            shop_name,
                            shop_address,
                            shop_nearby_transport,
                            shop_phone,
                            shop_description,
                            shop_map_url
                        FROM $shop_table
                    ");
                        ?>



                        <?php if (!empty($shops)) { ?>
                            <?php foreach ($shops as $shop) { ?>
                                <div class="card mb-4" style="border-radius: 6px;">
                                    <div class="row g-0">
                                        <!-- LEFT: IMAGE -->
                                        <div class="col-md-5">
                                            <img
                                                src="data:image/jpeg;base64,<?php echo base64_encode($shop->shop_image) ?>"
                                                class="img-fluid h-100 w-100 rounded-start"
                                                alt="<?php echo esc_attr($shop->shop_name); ?>"
                                                style="object-fit:cover;">
                                        </div>

                                        <!-- RIGHT: CONTENT -->
                                        <div class="col-md-7">
                                            <div class="card-body">

                                                <h5 class="card-title">
                                                    🏪 <?php echo esc_html($shop->shop_name); ?>
                                                </h5>

                                                <?php if (!empty($shop->shop_address)) { ?>
                                                    <h6 class="card-subtitle text-muted mb-2">ที่อยู่ร้านค้า :</h6>
                                                    <p class="card-text">
                                                        📍 <?php echo esc_html($shop->shop_address); ?>
                                                    </p>
                                                <?php } ?>

                                                <?php
                                                $transports = json_decode($shop->shop_nearby_transport, true);

                                                /**
                                                 * Color map by TYPE + LINE
                                                 */
                                                // $line_colors = [

                                                //     'MRT' => [
                                                //         'purple' => '#9c27b0',
                                                //         'pink'   => '#e91e63',
                                                //         'blue'   => '#2196f3',
                                                //         'yellow' => '#ffeb3b',
                                                //     ],

                                                //     'BTS' => [
                                                //         'dark green' => '#006400',
                                                //         'green'      => '#4caf50',
                                                //         'light green' => '#8bc34a',
                                                //         'gold'       => '#cfa92d',
                                                //     ],
                                                // ];

                                                if (!empty($transports) && is_array($transports)) :
                                                ?>
                                                    <p class="card-text">
                                                    <ul class="shop-transport-list" style="list-style:none; padding:0; margin:0;">
                                                        <?php foreach ($transports as $t) :

                                                            $type    = strtoupper(trim($t['type']));
                                                            $station = esc_html($t['station']);
                                                            $line    = strtolower(trim($t['line']));

                                                            // $color;

                                                            // // detect color by type + line
                                                            // if (isset($line_colors[$type])) {
                                                            //     foreach ($line_colors[$type] as $key => $value) {
                                                            //         if (strpos($line, $key) !== false) {
                                                            //             $color = $value;
                                                            //             break;
                                                            //         }
                                                            //     }
                                                            // }
                                                        ?>
                                                            <li style="margin-bottom:4px;">
                                                                <span>
                                                                    🚄 <?php echo esc_html($type); ?>
                                                                </span>
                                                                <span>
                                                                    <?php echo $station; ?>
                                                                </span>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                    </p>
                                                <?php endif; ?>


                                                <?php if (!empty($shop->shop_phone)) { ?>
                                                    <h6 class="card-subtitle text-muted mb-2">เบอร์โทร :</h6>
                                                    <p class="card-text">
                                                        📞 <?php echo esc_html($shop->shop_phone); ?>
                                                    </p>
                                                <?php } ?>

                                                <div class="mt-3">
                                                    <?php if (!empty($shop->shop_map_url)) { ?>
                                                        <a
                                                            href="<?php echo esc_url($shop->shop_map_url); ?>"
                                                            target="_blank"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="fa fa-globe" aria-hidden="true"></i> View Google Map
                                                        </a>
                                                    <?php } ?>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <p>No Amway shops found.</p>
                        <?php } ?>



                    </div><!-- /#post-<?php the_ID(); ?> -->
                </div>
            </div>

        </div><!-- .row -->

    </div><!-- #content -->

</main><!-- #main-wrapper -->
<style>

</style>
<?php
get_footer();
