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
$container_classes = $container . ' px-3 py-0';
?>

<main>

    <div class="<?php echo esc_attr($container_classes); ?>" id="content" tabindex="-1">

        <div class="row">
            <div class="sidebar-left-column order-2" id="sidebar-left">
                <?php
                get_template_part('sidebar-templates/sidebar', 'left');
                ?>
            </div>
            <div class="main-content-column order-1 px-3">
                <div class="main-content-wrapper mb-2">
                    <?php
                    while (have_posts()) {
                        the_post();
                        get_template_part('loop-templates/content', 'page');
                    }
                    ?>
                    <div id="aboyuma-amway-product-wrapper" class="aboyuma-amway-product-wrapper">

                        <form id="filter-form">
                            <!-- Filters -->
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <!-- Category Filter -->
                                <!-- CSS Note:
                                d-flex align-items-center works 
                                when elements are similar height
                                But when you have label + input/select, 
                                it's best to wrap them in flex-column 
                                for better cross-browser consistency -->
                                <?php
                                // Fetch categories
                                global $wpdb;
                                $categoryTable = $wpdb->prefix . 'aboyuma_amway_product_category';
                                $categories = $wpdb->get_results("SELECT id, category_name, parent_id FROM $categoryTable");
                                ?>
                                <div class="d-flex flex-column mb-3">
                                    <label for="filter-category" class="form-label mb-1">หมวดหมู่:</label>
                                    <select name="parent_category" id="parent_category" class="form-select form-select-sm"
                                        style="min-width: 180px;" required>
                                        <option value="">-- ทั้งหมด --</option>
                                        <?php foreach ($categories as $category):
                                            if ($category->parent_id == 0): ?>
                                                <option value="<?php echo esc_attr($category->id); ?>">
                                                    <?php echo esc_html($category->category_name); ?>
                                                </option>
                                        <?php endif;
                                        endforeach; ?>
                                    </select>
                                </div>
                                <div class="d-flex flex-column mb-3">
                                    <label for="filter-subcategory" class="form-label mb-1">หมวดหมู่ย่อย:</label>
                                    <select name="sub_category" id="sub_category" class="form-select form-select-sm"
                                        style="min-width: 180px;" required>
                                        <option value="">-- ทั้งหมด --</option>
                                        <!-- AJAX-loaded options will go here -->
                                    </select>
                                </div>
                                <div class="d-flex flex-column">
                                    <label class="invisible mb-1">ค้นหา</label> <!-- 🔧 invisible label -->
                                    <button id="apply-filters" class="btn btn-sm btn-light">ค้นหา</button>
                                </div>
                                <div class="d-flex flex-column">
                                    <label class="invisible mb-1">รีเซ็ต</label>
                                    <button type="reset" class="btn btn-sm btn-light">รีเซ็ต</button>
                                </div>
                            </div>
                        </form>
                        <div id="filter-message-area"></div>
                        <table id="aboyuma-product-table" class="table">
                            <thead>
                                <tr>
                                    <th>สินค้า</th>
                                    <th>คำอธิบาย</th>
                                    <th>รายละเอียด</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                    </div><!-- /#post-<?php the_ID(); ?> -->
                </div>
            </div>

        </div><!-- .row -->

    </div><!-- #content -->

</main><!-- #main-wrapper -->
<?php
get_footer();
