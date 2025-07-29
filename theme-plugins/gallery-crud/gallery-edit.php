<?php
function aboyuma_gallery_edit_page() {
    global $wpdb;
    $gallery_id = intval($_GET['gallery_id']);
    $gallery_table = $wpdb->prefix . 'aboyuma_gallery';
    $gallery_image_table   = $wpdb->prefix . 'aboyuma_gallery_image';

    // Fetch images
    $galleryImages = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $gallery_image_table WHERE gallery_id = %d ORDER BY image_order ASC",
        $gallery_id
    ));
?>
    <div class="wrap">
        <h1>Edit Gallery Images</h1>
        <form method="post" enctype="multipart/form-data">
            <table class="form-table">
                <!-- Gallery Images Row -->
                <tr>
                    <th scope="row"><label for="gallery_benefit">Gallery Images</label></th>
                    <td>
                        <input type="file" id="gallery-multi-upload" name="gallery_images[]" class="gallery-multi-input" accept="image/*" multiple>
                        <ul id="sortable-gallery" class="sortable-gallery-list">
                            <?php
                            $upload_dir = wp_upload_dir();
                            $base_url = trailingslashit($upload_dir['baseurl']);
                            foreach ($galleryImages as $galleryImage) :
                                $image_url = $base_url . $galleryImage->gallery_image_path;
                            ?>
                                <li class="image-preview-container existing-image" data-id="<?= $galleryImage->id; ?>">
                                    <img src="<?= esc_url($image_url); ?>" class="image-preview" width="150">
                                    <input type="hidden" name="existing_image_ids[]" value="<?= $galleryImage->id; ?>">
                                    <label><input type="checkbox" name="deleted_image_ids[]" value="<?= $galleryImage->id; ?>"> Delete</label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <input type="hidden" name="image_order_json" id="image_order_json">
                        <small>**Drag to reorder images. Top = first image.</small>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        jQuery(function($) {
            let imageIndex = 1000; // Unique temporary index for new uploads

            // Initialize Sortable
            $("#sortable-gallery").sortable({
                update: updateImageOrder
            });

            function updateImageOrder() {
                const order = [];
                $('#sortable-gallery .image-preview-container').each(function() {
                    const existingId = $(this).data('id');
                    const tempIndex = $(this).data('temp-index');
                    if (existingId) {
                        order.push('existing-' + existingId);
                    } else if (tempIndex) {
                        order.push('new-' + tempIndex);
                    }
                });
                $('#image_order_json').val(JSON.stringify(order));
            }

            // Handle multiple file upload and preview
            $('#gallery-multi-upload').on('change', function(e) {
                const files = e.target.files;
                if (files.length === 0) return;

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const reader = new FileReader();

                    reader.onload = function(event) {
                        const $li = $(`
                    <li class="image-preview-container" data-temp-index="${imageIndex}">
                        <img src="${event.target.result}" class="image-preview" width="150">
                        <input type="hidden" name="gallery_image_indexes[]" value="${imageIndex}">
                        <input type="hidden" name="gallery_image_filenames[]" value="${file.name}">
                        <span class="remove-image-button" style="cursor:pointer;color:red;">Remove</span>
                    </li>
                    `);
                        $('#sortable-gallery').append($li);
                        updateImageOrder();
                        imageIndex++;
                    };

                    reader.readAsDataURL(file);
                }

                $(this).val('');
            });

            // Remove image block
            $(document).on('click', '.remove-image-button', function() {
                $(this).closest('.image-preview-container').remove();
                updateImageOrder();
            });
        });
    </script>
    <style>
        .sortable-gallery-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-wrap: wrap;
            /* wrap if too many */
            gap: 10px;
        }

        .image-preview-container {
            width: 120px;
            text-align: center;
            border: 1px solid #ccc;
            padding: 5px;
            background: #fafafa;
            position: relative;
            cursor: grab;
        }

        .image-preview-container img {
            width: 100%;
            height: auto;
            display: block;
        }

        .remove-image-button {
            display: inline-block;
            color: red;
            cursor: pointer;
            margin-top: 5px;
            font-size: 13px;
        }
    </style>

<?php
}
