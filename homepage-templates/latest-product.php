<!-- Reccommend Product Section -->
<section id="home-latest-product" class="mb-2">

    <?php
    global $wpdb;

    $table_name = $wpdb->prefix . 'aboyuma_product';
    $products = [];

    $has_product_table = (
        $wpdb->get_var(
            $wpdb->prepare("SHOW TABLES LIKE %s", $table_name)
        ) === $table_name
    );

    if ($has_product_table) {
        $products = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, main_image_id, product_name,
                    product_description, product_slug
             FROM {$table_name}
             ORDER BY created DESC
             LIMIT %d",
                4
            )
        );
    }

    $has_products = !empty($products);
    ?>

    <h2 class="section-header">
        🛍️ ข้อมูลสินค้าแอมเวย์ที่เพิ่มล่าสุด
    </h2>

    <?php if ($has_products) : ?>

        <table id="latest-product-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>🛍️ สินค้า</th>
                    <th>📝 คำอธิบาย</th>
                    <th>📋 รายละเอียด</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product) : ?>
                    <tr>
                        <td class="text-center align-middle">

                            <?php
                            $image_url = '';

                            if (!empty($product->main_image_id)) {
                                $image_url = wp_get_attachment_image_url(
                                    $product->main_image_id,
                                    'medium'
                                );
                            }
                            ?>

                            <?php if ($image_url): ?>
                                <img
                                    src="<?php echo esc_url($image_url); ?>"
                                    alt="<?php echo esc_attr($product->product_name); ?>"
                                    class="img-fluid mb-2">
                            <?php else: ?>
                                <div class="mb-2 text-muted small">
                                    No image
                                </div>
                            <?php endif; ?>

                            <div class="fw-semibold">
                                <?php echo esc_html($product->product_name); ?>
                            </div>

                        </td>
                        <td>
                            <?php echo wp_kses_post($product->product_description); ?>
                        </td>
                        <td>
                            <a href="<?php echo esc_url(
                                            add_query_arg(
                                                'product_slug',
                                                $product->product_slug,
                                                get_permalink(get_option('aboyuma_product_page_id'))
                                            )
                                        ); ?>"
                                class="btn btn-sm btn-light">
                                ดูรายละเอียด
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else : ?>

        <div class="alert alert-secondary text-center">
            📦 ยังไม่มีข้อมูลสินค้า
        </div>

    <?php endif; ?>

</section>

<!-- please init datatable using inline js -->
<script>
    jQuery(document).ready(function($) {
        const isDesktop = window.matchMedia("(min-width: 769px)").matches;
        // Apply only for iPad and PC screen
        let columnDefs = [];
        if (isDesktop) {
            columnDefs = [{
                    width: "30%",
                    targets: [0],
                },
                {
                    width: "55%",
                    targets: [1],
                },
                {
                    width: "25%",
                    targets: [2],
                    orderable: false
                }
            ];
        }
        var table = $("#latest-product-table").DataTable({
            fixedColumns: {
                start: 1,
                end: 1
            },
            paging: false,
            scrollCollapse: true,
            scrollX: true,
            searching: false,
            autoWidth: false,
            responsive: true,
            columnDefs: columnDefs,
            info: false,
            ordering: true
        });
    });
</script>