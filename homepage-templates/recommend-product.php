<!-- Reccommend Product Section -->
<div id="home-recommend-product" class="mb-2">
    <?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'aboyuma_product';
    $products = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$table_name} 
                WHERE product_is_pinned = %d 
                ORDER BY created DESC LIMIT %d",
            1,
            4
        )
    );
    if ($wpdb->last_error) {
        echo '<div style="color:red;">MySQL Error: ' . $wpdb->last_error . '</div>';
    }
    ?>
    <span style="font-weight: bold;">🏆 สินค้าแอมเวย์แนะนำ:</span>
    <hr>
    <table id="recommend-product-table" class="table table-striped table-border">
        <thead>
            <tr>
                <th>สินค้า</th>
                <th>คำอธิบาย</th>
                <th>คุณสมบัติ</th>
                <th>รายละเอียด</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($products as $product) :
                $image_url = $product->product_image ? '<img src="data:image/jpeg;base64,' . base64_encode($product->product_image) . '" width="100%" height="auto">' : '';
                $detail_url = esc_url(add_query_arg('id', $product->id, get_permalink(get_page_by_path('product-detail')->ID)));
                // Combine image and name in one column
                $image_and_name = '
                <div style="text-align:center;">
                    ' . $image_url . '
                    <span>' . esc_html($product->product_name) . '</span>
                </div>';
            ?>
                <tr>
                    <td><?php echo $image_and_name; ?></td>
                    <td><?php echo htmlspecialchars_decode($product->product_short_description); ?></td>
                    <td><?php echo htmlspecialchars_decode($product->product_benefit); ?></td>
                    <td><a href="<?php echo $detail_url; ?>">[รายละเอียด]</a></td>
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
                        width: "15%",
                        targets: [0],
                    },
                    {
                        width: "35%",
                        targets: [1],
                    },
                    {
                        width: "35%",
                        targets: [2],
                    },
                    {
                        width: "15%",
                        targets: [3],
                    }
                ];
            }
            var table = $("#recommend-product-table").DataTable({
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

</div>