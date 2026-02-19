<?php
defined('ABSPATH') || exit;

function aboyuma_amway_product_property_list() {

    global $wpdb;
    $table = $wpdb->prefix . 'aboyuma_amway_product_property';
    $rows = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");

    // Enqueue DataTables
    wp_enqueue_style(
        'datatables-css',
        'https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css'
    );

    wp_enqueue_script(
        'datatables-js',
        'https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js',
        ['jquery'],
        null,
        true
    );

    // Init DataTable
    wp_add_inline_script('datatables-js', "
        jQuery(document).ready(function($) {
            $('.widefat').DataTable({
                order: [[0, 'desc']],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50]
            });
        });
    ");
?>

    <div class="wrap">

        <h1>Property List</h1>

        <?php if (isset($_GET['message']) && $_GET['message'] === 'added') : ?>
            <div class="notice notice-success is-dismissible">
                <p>Property added successfully.</p>
            </div>
        <?php endif; ?>

        <a href="admin.php?page=aboyuma-product-property-add" class="button button-primary">
            Add New
        </a>

        <br><br>

        <table class="widefat striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Key</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($rows)) : ?>
                    <?php foreach ($rows as $row) : ?>
                        <tr>
                            <td><?= esc_html($row->id) ?></td>
                            <td><?= esc_html($row->property_key) ?></td>
                            <td><?= esc_html($row->property_name) ?></td>
                            <td><?= esc_html($row->property_type) ?></td>
                            <td><?= esc_html($row->created) ?></td>
                            <td>
                                <a href="admin.php?page=aboyuma-product-property-edit&id=<?= $row->id ?>" class="button">
                                    Edit
                                </a>

                                <a href="#"
                                    class="button product-property-delete-button"
                                    data-id="<?= $row->id ?>">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

    </div>

<?php
}
