<!-- Reccommend Product Section -->
<div id="home-latest-product" class="mb-2">
    <?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'aboyuma_amway_product';
    $products = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT id, product_image, product_name, 
                product_short_description, product_benefit
                FROM {$table_name} 
                ORDER BY created DESC LIMIT %d",
            4
        )
    );

    if ($wpdb->last_error) {
        echo '<div style="color:red;">MySQL Error: ' . $wpdb->last_error . '</div>';
    }
    ?>

    <h1 class="section-header">
        🛍️ สินค้าแอมเวย์ที่เพิ่มล่าสุด
    </h1>
    <?php if (empty($products)) : ?>
        <div class="alert alert-secondary text-center">
            📦 ยังไม่มีข้อมูลสินค้า
        </div>
    <?php else : ?>
        <table id="latest-product-table" class="table">
            <thead>
                <tr>
                    <th>🛍️ สินค้า</th>
                    <th>📝 คำอธิบาย</th>
                    <th>📋 รายละเอียด</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($products as $product) :
                    $image_url = $product->product_image ? '<img class="img-fluid rounded-3 mb-2" src="data:image/jpeg;base64,'
                        . base64_encode($product->product_image) . '" width="100%" height="auto">' : '';
                    $detail_url = esc_url(add_query_arg('id', $product->id, get_permalink(get_page_by_path('amway-product-detail')->ID)));
                ?>
                    <tr>
                        <!-- product -->
                        <td class="product-name">
                            <div class="text-center">
                                <?php echo $image_url; ?>
                                <div class="fw-semibold mt-1">
                                    <?php echo esc_html($product->product_name); ?>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars_decode($product->product_short_description); ?></td>
                        <td>
                            <a href="<?php echo $detail_url; ?>"
                                class="btn btn-sm btn-outline-primary">
                                ดูรายละเอียด
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- please init datatable using inline js -->
        <script>
            jQuery(document).ready(function($) {
                const isDesktop = window.matchMedia("(min-width: 769px)").matches;
                // Apply only for iPad and PC screen
                let columnDefs = [];
                if (isDesktop) {
                    columnDefs = [{
                            width: "25%",
                            targets: [0],
                        },
                        {
                            width: "55%",
                            targets: [1],
                        },
                        {
                            width: "20%",
                            targets: [2],
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
                    ordering: false
                });
            });
        </script>
    <?php endif; ?>
</div>