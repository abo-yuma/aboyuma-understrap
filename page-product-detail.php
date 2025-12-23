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
$product_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
if (!$product_id) {
    /*
  wp_safe_redirect() must be called before any output (HTML, whitespace, get_header(), echo, etc.).
  Once PHP sends headers, it cannot change them — so the redirect silently fails.
  */
    wp_safe_redirect(home_url('/product/'));
    exit;
}
get_header();

$container = get_theme_mod('understrap_container_type');

?>

<main>

    <div class="<?php echo esc_attr($container); ?>" id="content" tabindex="-1">

        <div class="row py-3">
            <div class="sidebar-left-column-width order-2 order-md-1" id="sidebar-left">
                <?php
                get_template_part('sidebar-templates/sidebar', 'left');
                ?>
            </div>
            <div class="main-content-column-width order-1 order-md-2">
                <?php
                the_post();
                get_template_part('loop-templates/content', 'page');
                ?>
                <?php
                global $wpdb;

                // Get existing data for the product
                $product_table_name = $wpdb->prefix . 'aboyuma_product';
                $productBrandTable = $wpdb->prefix . 'aboyuma_product_brand';
                $productCategoryTable = $wpdb->prefix . 'aboyuma_product_category';
                $sql = $wpdb->prepare(
                    "
                    SELECT
                        p.*,
                        b.brand_name,
                        b.brand_image_url,
                        c.category_name
                    FROM
                        $product_table_name AS p
                    LEFT JOIN
                        $productBrandTable AS b ON p.product_brand_id = b.id
                    LEFT JOIN
                        $productCategoryTable AS c ON p.product_category_id = c.id
                    WHERE
                        p.id = %d
                    ",
                    $product_id
                );

                $product = $wpdb->get_row($sql);
                if (!$product) {
                    echo '<div class="notice notice-error is-dismissible">
                    <p>Product not found.</p>
                    </div>';
                    return;
                }

                $productImageTable = $wpdb->prefix . 'aboyuma_product_image';
                $sql_images = $wpdb->prepare("
                    SELECT id, product_id, product_image_path 
                    FROM $productImageTable 
                    WHERE product_id = %d
                ", $product_id);
                $productDetailImages = $wpdb->get_results($sql_images);

                $productInfographicTable = $wpdb->prefix . 'aboyuma_product_infographic';
                $sql_infographics = $wpdb->prepare("
                    SELECT id, product_id, product_infographic_path
                    FROM $productInfographicTable 
                    WHERE product_id = %d
                ", $product_id);
                $productInfographics = $wpdb->get_results($sql_infographics);

                $productRelateVideoTable = $wpdb->prefix . 'aboyuma_related_product_video';
                $sql_videos = $wpdb->prepare("
                    SELECT id, product_id, video_url, video_comment
                    FROM $productRelateVideoTable 
                    WHERE product_id = %d
                ", $product_id);
                $productRelateVideos = $wpdb->get_results($sql_videos);

                $relatedProductTable = $wpdb->prefix . 'aboyuma_related_product';
                $related_products = $wpdb->get_col($wpdb->prepare(
                    "SELECT related_product_id FROM $relatedProductTable WHERE product_id = %d",
                    $product_id
                ));
                // Display the detail
                ?>
                <div class="entry-content" id="product-detail">
                    <section id="modified-date" class="modified-date mb-2"> <?php echo "🗓️ อัพเดตข้อมูลล่าสุด " . date("d M Y", strtotime($product->modified)); ?> </section>
                    <section id="product-image-and-description" class="mb-2">
                        <div class="row">
                            <!-- Main Image Preview Section -->
                            <div class="col-md-7">
                                <div id="previewImageBox" class="previewImageBox border rounded">
                                    <?php $baseurl = wp_upload_dir()['baseurl']; ?>
                                    <a href="javascript:void(0);"
                                        id="previewFancyboxLink"
                                        onclick="openFancyboxFromCurrentIndex();">
                                        <img id="previewImage" src="<?= esc_url($baseurl . '/' . $productDetailImages[0]->product_image_path); ?>" alt="Product Preview">
                                    </a>
                                </div>
                                <!-- Hidden Fancybox links for gallery -->
                                <div style="display: none;">
                                    <?php foreach ($productDetailImages as $key => $productDetailImage) :
                                        if ($key === 0) continue; // skip the first because it's already shown
                                    ?>
                                        <a
                                            href="<?= esc_url($baseurl . '/' . $productDetailImage->product_image_path); ?>"
                                            data-fancybox="product-gallery"
                                            data-caption=""></a>
                                    <?php endforeach; ?>
                                </div>
                                <!-- Product image thumbnails -->
                                <div class="thumbnail-slider-container position-relative mt-2">
                                    <button id="prevThumbnail" class="slider-arrow left-arrow">&lt;</button>
                                    <div id="thumbnailWrapper" class="thumbnail-wrapper">
                                        <?php foreach ($productDetailImages as $key => $productDetailImage) : ?>
                                            <img src="<?= esc_url($baseurl . '/' . $productDetailImage->product_image_path); ?>"
                                                onclick="displayPreviewImage(this, <?= $key ?>)"
                                                class="thumbnail img-thumbnail mb-2 <?= $key === 0 ? 'active' : ''; ?>"
                                                alt="">
                                        <?php endforeach; ?>
                                    </div>
                                    <button id="nextThumbnail" class="slider-arrow right-arrow">&gt;</button>
                                </div>
                            </div>
                            <!-- End Main Image Preview Section -->

                            <!-- Product Description Section -->
                            <div id="product_description" class="col-md-5">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title m-0"><?php echo esc_html($product->product_name); ?></h5>
                                        <small>
                                            <?php
                                            // Map unit types to Thai
                                            $unit_types_th = [
                                                'tablet' => 'เม็ด',
                                                'capsule' => 'แคปซูล',
                                                'sachet' => 'ซอง',
                                                'g' => 'กรัม',
                                                'ml' => 'มิลลิลิตร'
                                            ];

                                            // Get the Thai label for the product unit type
                                            $unit_type_th = isset($unit_types_th[$product->product_unit_type]) ? $unit_types_th[$product->product_unit_type] : $product->product_unit_type;

                                            // Display
                                            echo '#' . esc_html($product->product_code);

                                            if (!empty($product->product_unit_count)) {
                                                echo " | บรรจุ " . esc_html($product->product_unit_count) . " " . esc_html($unit_type_th);
                                            }
                                            ?>
                                        </small>

                                        <div id="product-category-brand-label" class="col-md-12 mb-2">
                                            <span class="badge bg-success"><?php echo esc_html($product->brand_name); ?></span>
                                            <span class="badge bg-primary"><?php echo esc_html($product->category_name); ?></span>
                                        </div>
                                        <div id="product-descroption-wrapper" class="card-text" style="font-size: 14px;">
                                            <div class="product_description mb-2">
                                                <?php echo apply_filters('the_content', $product->product_description); ?>
                                            </div>
                                            <div id="product_is_no_additives">
                                                <?php
                                                // Decode the JSON from the DB
                                                $product_additive_properties = json_decode($product->product_additive_properties ?? '', true);
                                                // Safety check and output
                                                if (!empty($product_additive_properties)) {
                                                    if (!empty($product_additive_properties['no_color'])) {
                                                        echo '✅ ไม่เจือสี<br>';
                                                    }
                                                    if (!empty($product_additive_properties['no_flavoring'])) {
                                                        echo '✅ ไม่ใช้วัตถุแต่งกลิ่นรส<br>';
                                                    }
                                                    if (!empty($product_additive_properties['no_preservative'])) {
                                                        echo '✅ ไม่ใช้วัตถุกันเสีย<br>';
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <div id="product_is_plant_based" class="product_is_plant_based mt-2">
                                                <?php
                                                // Decode the JSON from the DB
                                                $product_diet_properties = json_decode($product->product_diet_properties ?? '', true);

                                                // Safety check and output
                                                if (!empty($product_diet_properties)) {
                                                    if (!empty($product_diet_properties['vegetarian'])) {
                                                        echo '🌿 Vegetarian<br>';
                                                    }
                                                    if (!empty($product_diet_properties['flexitarian'])) {
                                                        echo '🌿 Flexitarian<br>';
                                                    }
                                                    if (!empty($product_diet_properties['vegan'])) {
                                                        echo '🌿 Vegan<br>';
                                                    }
                                                    if (!empty($product_diet_properties['mostly vegan'])) {
                                                        echo '🌿 Mostly Vegan 
                                                        <i class="fa fa-info-circle ms-1 text-muted"
                                                        role="button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#mostlyVeganModal"
                                                        title="Learn more"></i><br>';
                                                    }
                                                }
                                                ?>
                                            </div>

                                            <div id="product_price_info" class="mt-2">
                                                <?php if (!empty($product->product_sop_link)) : ?>
                                                    <div class="product_descsription_sop">
                                                        <div id="product_detail_sop_link">
                                                            <span>📋 มีโปรแกรม SOP FLEX</span>
                                                            <i class="fa fa-info-circle ms-1 text-muted"
                                                                role="button"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#SOPInfoModal"
                                                                title="Learn more"></i>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="product_descsription_price">
                                                    <span>🪪 ราคา :
                                                        <span>
                                                            <?php echo "฿" . esc_html(number_format($product->product_member_price, 0)); ?>
                                                        </span>
                                                        <?php if ($product->product_price != $product->product_member_price) : ?>
                                                            <span style="font-size: 13.5px; color: #282c31ff; margin-left: 10px;">
                                                                <?php echo "<del>฿" . esc_html(number_format($product->product_price, 0)) . "</del>"; ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="product_shop_link_button mt-2">
                                            <a href="<?php echo esc_url($product->product_shop_link); ?>" target="_blank" class="btn btn-dark"
                                                style="width: 100%;">🛒 สั่งซื้อสินค้า</a>
                                        </div>
                                    </div>
                                </div>
                                <div id="purchase-description" class="mt-1">
                                    <?php
                                    $isPriceSame = $product->product_price == $product->product_member_price;
                                    if ($isPriceSame) {
                                    ?>
                                        <small style="display: inline-block; line-height: 90%;">
                                            <span style="font-size: 30px;">🪪</span>
                                            สมัครสมาชิกเพื่อรับคะแนน A-POINT! 🪙
                                        </small>
                                    <?php
                                    } else {
                                    ?>
                                        <small style="display: inline-block; line-height: 90%;">
                                            <span style="font-size: 30px;">🪪</span>
                                            สมัครสมาชิกเพื่อสั่งซื้อสินค้าในราคานี้และรับคะแนน A-POINT! 🪙
                                        </small>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </section><!-- End Product Description Section -->

                    <div class="text-end mb-2">
                        <a href="<?= esc_url(home_url('/product')) ?>"><i class="fa fa-backward" aria-hidden="true"></i>
                            ดูสินค้าอื่น</a>
                    </div>

                    <section id="product-detail-info-container" class="product-detail consistent-height mb-2">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link active" id="nav-product-detail-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-product-detail"
                                    type="button" role="tab" aria-controls="nav-product-detail" aria-selected="true">
                                    📒 บันทึก
                                </button>
                                <button class="nav-link" id="nav-product-specification-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-product-specification"
                                    type="button" role="tab" aria-controls="nav-product-specification" aria-selected="false">
                                    💊 ข้อมูลจำเพาะ
                                </button>
                                <button class="nav-link" id="nav-product-comment-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-product-comment"
                                    type="button" role="tab" aria-controls="nav-product-comment" aria-selected="false">
                                    ⚠️ หมายเหตุ
                                </button>
                                <button class="nav-link" id="nav-product-review-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-product-review"
                                    type="button" role="tab" aria-controls="nav-product-review" aria-selected="false">
                                    ⭐ รีวิว
                                </button>
                            </div>
                        </nav>
                        <div class="tab-content rounded-bottom p-3" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-product-detail" role="tabpanel" aria-labelledby="nav-product-detail-tab">
                                <?php
                                echo apply_filters('the_content', $product->product_detail);
                                ?>
                            </div>
                            <div class="tab-pane fade" id="nav-product-specification" role="tabpanel" aria-labelledby="nav-product-specification-tab">
                                <?php
                                if (!empty($product->product_specification)) {
                                    echo apply_filters('the_content', $product->product_specification);
                                }
                                ?>
                            </div>
                            <!-- SECTION: product comment -->
                            <?php if (!empty($product->product_comment)) : ?>
                                <div class="tab-pane fade" id="nav-product-comment" role="tabpanel" aria-labelledby="nav-product-comment-tab">
                                    <?php
                                    echo apply_filters('the_content', $product->product_comment);
                                    ?>
                                </div>
                            <?php endif; ?>
                            <!-- SECTION: product review -->
                            <?php if (!empty($product->product_review)) : ?>
                                <div class="tab-pane fade" id="nav-product-review" role="tabpanel" aria-labelledby="nav-product-review-tab">
                                    <?php
                                    echo apply_filters('the_content', $product->product_review);
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>

                    <?php if (!empty($productInfographics)) : ?>
                        <!-- SECTION: Product Infographic -->
                        <section id="product-infographic" class="mb-2">
                            <div class="container">
                                <div class="row g-3">
                                    <?php
                                    $upload_dir = wp_upload_dir();
                                    $base_url = trailingslashit($upload_dir['baseurl']);

                                    foreach ($productInfographics as $infographic) :
                                        $image_url = esc_url($base_url . $infographic->product_infographic_path);
                                    ?>
                                        <div class="col-md-4">
                                            <div class="border rounded p-2 text-center infographic-img-wrapper">
                                                <a href="<?php echo $image_url; ?>" data-fancybox="product-infographics">
                                                    <img src="<?php echo $image_url; ?>" alt="Infographic" class="infographic-image">
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <?php if (!empty($productRelateVideos)) : ?>
                        <section id="product-related-video" class="mb-2">
                            <span class="related-product-label">▶️ วิดีโอที่เกี่ยวข้อง</span> <!-- same size to bootstrap tab panel font size -->
                            <hr>

                            <div class="row p-3">
                                <?php foreach ($productRelateVideos as $index => $video) :
                                    // Extract YouTube video ID from the URL
                                    preg_match('/(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/watch\?v=|\/v\/|\/.+\?v=))([^&\n?#]+)/', $video->video_url, $matches);
                                    $youtube_id = $matches[1] ?? '';
                                    if (!$youtube_id) continue; // skip if no valid ID
                                ?>
                                    <div class="col-md-6 mt-2">
                                        <div class="video-thumb-wrapper position-relative overflow-hidden" style="aspect-ratio: 16 / 9;">
                                            <a data-fancybox href="https://www.youtube.com/watch?v=<?php echo esc_attr($youtube_id); ?>"
                                                data-caption="<?php echo esc_attr($video->video_comment ?: 'Related Video'); ?>">
                                                <img src="https://img.youtube.com/vi/<?php echo esc_attr($youtube_id); ?>/hqdefault.jpg"
                                                    alt="YouTube thumbnail" class="w-100 h-100 object-fit-cover">
                                                <!-- Play button overlay -->
                                                <span class="play-btn position-absolute top-50 start-50 translate-middle"></span>
                                            </a>
                                        </div>
                                        <?php if (!empty($video->video_comment)) : ?>
                                            <small class="d-block mt-1"><?php echo esc_html($video->video_comment); ?></small>
                                        <?php endif; ?>
                                    </div>

                                <?php endforeach; ?>
                            </div>

                        </section>
                    <?php endif; ?>

                    <!-- SECTION: Related Products -->
                    <section id="related-products" class="mb-2">
                        <?php
                        if (count($related_products) > 0) {
                            set_query_var('related_products', $related_products);
                            set_query_var('product_id', $product_id);
                            get_template_part('page-templates/product-detail/related-product');
                        }
                        ?>
                    </section>

                </div>

                <footer class="entry-footer">

                    <?php aboyuma_facebook_share_button(); ?>
                    <!-- Back button -->
                    <div class="text-end mt-4">
                        <a href="javascript:void(0);" onclick="history.back()">
                            <i class="fa fa-backward" aria-hidden="true"></i> ย้อนกลับ
                        </a>
                    </div>
                </footer><!-- .entry-footer -->

                <!-- Plant-based info Modal -->
                <div class="modal fade" id="mostlyVeganModal" tabindex="-1" aria-labelledby="mostlyVeganLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="mostlyVeganLabel">Mostly Vegan คืออะไร?</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>
                                    🌿 Mostly Vegan คือแนวทางการบริโภคที่ใกล้เคียงกับวีแกนมากที่สุด
                                    โดยหลีกเลี่ยงการทานเนื้อสัตว์ ผลิตภัณฑ์ที่มาจากสัตว์ 🐄🐓🐟
                                    แต่ในบางกรณีอาจมีข้อยกเว้นเล็กน้อย ⚖️ เพื่อดูแลสุขภาพและความสมดุลของร่างกายโดยรวม
                                    เมื่อหาทางเลือกอื่นที่เหมาะสมกว่าไม่ได้ เช่น การเลือกทานอาหารเสริมวิตามินรวม เกลือแร่รวม และไฟโตนิวเทรียนท์ 🥕🥦🥬🥑
                                    คุณภาพดีที่มีส่วนผสมของวิตามินดี3จากขนแกะ 🐑
                                </p>

                                <hr>

                                <h6 class="fw-bold mb-2">🐑 วิตามินดี3จากขนแกะ (Lanolin-derived Vitamin D3)</h6>
                                <p>
                                    ✅ วิตามินดี3 เป็นรูปแบบที่ร่างกายดูดซึมและใช้ได้ดีที่สุด
                                    🔬 ส่วนใหญ่สกัดจาก <strong>Lanolin</strong> ซึ่งเป็นไขมันตามธรรมชาติที่เคลือบอยู่บนขนแกะ 🐑
                                </p>

                                <hr>

                                <p><strong>เหตุผลที่ยังคงใช้ในอาหารเสริม:</strong></p>
                                <ul style="list-style: none; padding-left: 0; margin: 0;">
                                    <li>🐑 วิตามินดี3 จากขนแกะมีประสิทธิภาพสูงและข้อมูลความปลอดภัย ✅</li>
                                    <li>🌿 วิตามินดี3 จากพืช เช่น สาหร่ายไลเคน (Lichen) มีราคาสูง 💰</li>
                                </ul>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SOP FLEX info Modal -->
                <div class="modal fade" id="SOPInfoModal" tabindex="-1" aria-labelledby="SOPInfoLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-custom-size">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="SOPInfoLabel">SOP FLEX คืออะไร?</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>
                                    🌟 SOP FLEX คือโปรแกรมการสั่งซื้อแบบต่อเนื่อง เป็นโปรโมชั่นสำหรับผู้ที่สั่งซื้อผลิตภัณฑ์แอมเวย์เป็นประจำ 🛒<br>
                                    โดยมีโปรแกรมแบบ 6 เดือน และ 11 เดือน 🗓️
                                    เมื่อซื้อจบโปรแกรมจะได้รับส่วนลดพิเศษ 📉💰 หรือสินค้าฟรี 🎁 ตามเงื่อนไข 🎉
                                </p>
                                <p>
                                    <img src="https://support.amway.co.th/hc/article_attachments/28298478027156">
                                </p>
                                <p>
                                    ⭐ สามารถเลือกแบบสั่งซื้อรอบถัดไปด้วยตนเองหรือแบบอัติโนมัติ<br>
                                    การสั่งซื้อสินค้าในแต่ละเดือนเปรียบเสมือนการสะสมแสตมป์เพื่อรักษาสิทธิประโยชน์ครับ 📮🎁
                                </p>
                                <p>
                                    🔍 เช็คสินค้าที่ร่วมรายการ SOP FLEX ได้<a href="https://amway.co.th/category/sop-flex" target="_blank">ที่นี่!</a>
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div><!-- .row -->

    </div><!-- #content -->

</main><!-- #main-wrapper -->
<script>
    function openFancyboxFromCurrentIndex() {
        const thumbnails = document.querySelectorAll(".thumbnail");
        let currentIndex = 0;

        thumbnails.forEach((thumb, index) => {
            if (thumb.classList.contains("active")) {
                currentIndex = index;
            }
        });

        Fancybox.show(
            Array.from(thumbnails).map(thumb => ({
                src: thumb.getAttribute("src"),
                type: "image",
                caption: thumb.getAttribute("alt") || ""
            })), {
                startIndex: currentIndex,
                Thumbs: {
                    autoStart: true, // ✅ Automatically show thumbnails
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
            }
        );
    }


    function scrollThumbnails(direction) {
        const wrapper = document.getElementById("thumbnailWrapper"); // Get the thumbnail wrapper
        const thumbnailWidth = wrapper.querySelector(".thumbnail").offsetWidth + 500; // Get width of one thumbnail + margin

        // Scroll by one thumbnail width in the given direction
        wrapper.scrollBy({
            left: direction * thumbnailWidth,
            behavior: "smooth"
        });
    }

    // Event listeners for left and right arrow buttons
    document.getElementById("prevThumbnail").addEventListener("click", function() {
        scrollThumbnails(-1); // Scroll left
    });

    document.getElementById("nextThumbnail").addEventListener("click", function() {
        scrollThumbnails(1); // Scroll right
    });

    function displayPreviewImage(thumbnail, index) {
        const newImagePath = thumbnail.getAttribute("src");

        // Update preview image
        const previewImg = document.getElementById("previewImage");
        previewImg.src = newImagePath;

        // Update active class
        document.querySelectorAll('.thumbnail').forEach(thumb => thumb.classList.remove('active'));
        thumbnail.classList.add('active');
    }
</script>
<style>
    .fancybox__backdrop {
        background: rgba(0, 0, 0, 0.8);
        /* same as Magnific Popup */
    }

    #qrcode img {
        width: 70px;
        height: 70px;
        object-fit: contain;
    }

    .infographic-image {
        width: 100%;
        aspect-ratio: 1 / 1;
        /* force square */
        object-fit: cover;
        /* cover without distortion */
    }

    .play-btn {
        width: 60px;
        height: 60px;
        background: rgba(0, 0, 0, 0.6);
        /* semi-transparent black */
        border-radius: 12px;
        /* rounded corners instead of circle */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Triangle in the middle */
    .play-btn::before {
        content: '';
        display: block;
        margin-left: 3px;
        /* center the triangle visually */
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 12px 0 12px 20px;
        /* triangle size */
        border-color: transparent transparent transparent white;
    }
</style>
<?php
get_footer();
