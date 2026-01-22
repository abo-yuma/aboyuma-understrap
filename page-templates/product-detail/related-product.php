<div id="related-product" class="mt-2">
    <?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'aboyuma_amway_product';
    $product_id = get_query_var('product_id');
    $related_products = get_query_var('related_products');
    if (!empty($related_products) && $product_id) {
        $related_products = array_map('intval', $related_products);
        $placeholders = implode(',', array_fill(0, count($related_products), '%d'));

        $product_table = $wpdb->prefix . 'aboyuma_amway_product';
        $related_product_table = $wpdb->prefix . 'aboyuma_related_amway_product';
        // Query product info
        $sql = "
            SELECT 
            p.id,
            p.product_image,
            p.product_name,
            r.related_product_comment
            FROM $product_table p
            INNER JOIN $related_product_table r
            ON p.id = r.related_product_id
            WHERE r.product_id = %d
            AND r.related_product_id IN ($placeholders)
            ORDER BY p.product_name ASC
        ";
        $prepared_sql = $wpdb->prepare($sql, $product_id, ...$related_products);
        $products = $wpdb->get_results($prepared_sql);
    }
    if ($wpdb->last_error) {
        echo '<div style="color:red;">MySQL Error: ' . $wpdb->last_error . '</div>';
    }
    ?>
    <span class="related-product-label">🛒 สินค้าที่แนะนำคู่กัน</span> <!-- same size to bootstrap tab panel font size -->
    <hr>
    <table id="latest-product-table" class="table table-striped table-bordered" width="100%">
        <thead>
            <tr>
                <th>สินค้า</th>
                <th>คำอธิบาย</th>
                <th>เหตุผล</th>
                <th>รายละเอียด</th>
            </tr>
        </thead>
    </table>

    <!-- please init datatable using inline js -->
    <script>
        jQuery(document).ready(function($) {
            const productId = <?php echo json_encode(get_query_var('product_id')); ?>;
            const relatedProducts = <?php echo json_encode(get_query_var('related_products')); ?>;

            if (!productId || !relatedProducts.length) return;

            const isDesktop = window.matchMedia("(min-width: 769px)").matches;
            let columnDefs = [];

            if (isDesktop) {
                columnDefs = [{
                        width: "25%",
                        targets: [0],
                    },
                    {
                        width: "30%",
                        targets: [1],
                    },
                    {
                        width: "30%",
                        targets: [2],
                    },
                    {
                        width: "15%",
                        targets: [3],
                    }
                ];
            }

            $("#latest-product-table").DataTable({
                ajax: {
                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                    type: "GET",
                    data: function(d) {
                        d.action = "get_related_products";
                        d.product_id = productId;
                        d.related_products = relatedProducts;
                        d._ajax_nonce = "<?php echo wp_create_nonce('aboyuma_related_nonce'); ?>";
                    }
                },
                columns: [{
                        title: "สินค้า"
                    },
                    {
                        title: "คำอธิบาย"
                    },
                    {
                        title: "เหตุผล"
                    },
                    {
                        title: "รายละเอียด"
                    }
                ],
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
</div><!-- Reccommend Product Section -->