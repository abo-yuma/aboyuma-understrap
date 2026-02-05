<?php
function aboyuma_author_box_edit_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'aboyuma_author_box';

    // Always has one record
    $id = 1;
    $author_box = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));

    if (!$author_box) {
        echo '<div class="notice notice-error"><p>Author record not found.</p></div>';
        return;
    }

    // Handle update
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (
            !isset($_POST['aboyuma_author_box_nonce']) ||
            !wp_verify_nonce($_POST['aboyuma_author_box_nonce'], 'aboyuma_edit_author_box_action')
        ) {
            wp_die('Security check failed.');
        }

        $errors = [];

        // Required fields
        if (empty(trim($_POST['title'] ?? ''))) {
            $errors[] = 'Author name is required.';
        }

        if (empty(trim($_POST['bio'] ?? ''))) {
            $errors[] = 'Bio is required.';
        }

        function validate_url($url, $label, &$errors) {

            // ถ้าว่าง ให้ข้าม (ถือว่า optional)
            if (empty(trim($url))) {
                return;
            }

            // ใช้ของ WordPress ตรวจ
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                $errors[] = $label . ' is not a valid URL.';
            }
        }

        validate_url($_POST['image'] ?? '', 'Profile image URL', $errors);
        validate_url($_POST['profile_link_url'] ?? '', 'Profile link URL', $errors);
        validate_url($_POST['member_register_link_url'] ?? '', 'Member register link URL', $errors);
        validate_url($_POST['abo_register_link_url'] ?? '', 'ABO register link URL', $errors);

        // ID safety
        if (!isset($id) || !is_numeric($id)) {
            $errors[] = 'Invalid author box ID.';
        }

        if (empty($errors)) {

            $data = array(
                'title' => sanitize_text_field($_POST['title'] ?? ''),
                'bio' => wp_kses_post($_POST['bio'] ?? ''),
                'image' => esc_url_raw($_POST['image'] ?? ''),
                'aboyuma_qualified_title' => sanitize_text_field($_POST['aboyuma_qualified_title'] ?? ''),

                'profile_link_url' => esc_url_raw($_POST['profile_link_url'] ?? ''),

                'member_register_link_url' => esc_url_raw($_POST['member_register_link_url'] ?? ''),
                'member_register_link' => sanitize_text_field($_POST['member_register_link'] ?? ''),
                'member_register_link_info_modal_title' => sanitize_text_field($_POST['member_register_link_info_modal_title'] ?? ''),
                'member_register_link_info_modal_content' => wp_kses_post($_POST['member_register_link_info_modal_content'] ?? ''),

                'abo_register_link_url' => esc_url_raw($_POST['abo_register_link_url'] ?? ''),
                'abo_register_link' => sanitize_text_field($_POST['abo_register_link'] ?? ''),
                'abo_register_link_info_modal_title' => sanitize_text_field($_POST['abo_register_link_info_modal_title'] ?? ''),
                'abo_register_link_info_modal_content' => wp_kses_post($_POST['abo_register_link_info_modal_content'] ?? ''),
            );

            $updated = $wpdb->update($table, $data, ['id' => (int) $id]);

            if ($updated !== false) {
                echo '<div class="notice notice-success is-dismissible"><p>Author box updated successfully.</p></div>';
                $author_box = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
            } else {
                echo '<div class="notice notice-error"><p>Update failed.</p></div>';
            }
        } else {

            echo '<div class="notice notice-error"><ul style="margin-left:20px;">';
            foreach ($errors as $error) {
                echo '<li>' . esc_html($error) . '</li>';
            }
            echo '</ul></div>';
        }
    }

?>
    <div class="wrap">
        <h1>Edit ABO YUMA Author Box</h1>

        <form method="post">
            <table class="form-table">
                <tr>
                    <th colspan="2">
                        <h2>Main Author Bio</h2>
                    </th>
                </tr>
                <tr>
                    <th><label>Title</label></th>
                    <td><input type="text" name="title" class="regular-text" value="<?php echo esc_attr($author_box->title); ?>"></td>
                </tr>

                <tr>
                    <th><label>Bio</label></th>
                    <td>
                        <?php
                        wp_editor(
                            $author_box->bio, // content
                            'bio',            // editor ID (must be unique on the page)
                            array(
                                'textarea_name' => 'bio',
                                'textarea_rows' => 6,
                                'media_buttons' => true,
                                'teeny'         => false,
                                'quicktags'     => true,
                            )
                        );
                        ?>
                    </td>
                </tr>

                <tr>
                    <th><label>Image URL</label></th>
                    <td>
                        <input type="text" name="image" class="large-text"
                            value="<?php echo esc_attr($author_box->image); ?>">

                        <?php if (! empty($author_box->image)) : ?>
                            <div style="margin-top:10px;">
                                <strong>Current image:</strong><br>
                                <img src="<?php echo esc_url($author_box->image); ?>"
                                    alt=""
                                    style="max-width:200px; height:auto; border:1px solid #ddd; padding:4px; background:#fff;">
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>

                <tr>
                    <th><label>Qualified Title</label></th>
                    <td><input type="text" name="aboyuma_qualified_title" class="regular-text" value="<?php echo esc_attr($author_box->aboyuma_qualified_title); ?>"></td>
                </tr>

                <tr>
                    <th>Profile Link URL</th>
                    <td><input type="text" name="profile_link_url" class="large-text" value="<?php echo esc_attr($author_box->profile_link_url); ?>"></td>
                </tr>

                <tr>
                    <th colspan="2">
                        <h2>Member Register</h2>
                    </th>
                </tr>

                <tr>
                    <th>Member URL</th>
                    <td><input type="text" name="member_register_link_url" class="large-text" value="<?php echo esc_attr($author_box->member_register_link_url); ?>"></td>
                </tr>

                <tr>
                    <th>Member Text</th>
                    <td><input type="text" name="member_register_link" class="regular-text" value="<?php echo esc_attr($author_box->member_register_link); ?>"></td>
                </tr>

                <tr>
                    <th>Member Modal Title</th>
                    <td><input type="text" name="member_register_link_info_modal_title" class="regular-text" value="<?php echo esc_attr($author_box->member_register_link_info_modal_title); ?>"></td>
                </tr>

                <tr>
                    <th>Member Modal Content</th>
                    <td>
                        <?php
                        wp_editor(
                            $author_box->member_register_link_info_modal_content,
                            'member_register_link_info_modal_content',
                            array(
                                'textarea_name' => 'member_register_link_info_modal_content',
                                'textarea_rows' => 8,
                                'media_buttons' => true,
                                'teeny'         => false,
                                'quicktags'     => true,
                            )
                        );
                        ?>
                    </td>
                </tr>

                <tr>
                    <th colspan="2">
                        <h2>ABO Register</h2>
                    </th>
                </tr>

                <tr>
                    <th>ABO URL</th>
                    <td><input type="text" name="abo_register_link_url" class="large-text" value="<?php echo esc_attr($author_box->abo_register_link_url); ?>"></td>
                </tr>

                <tr>
                    <th>ABO Text</th>
                    <td><input type="text" name="abo_register_link" class="regular-text" value="<?php echo esc_attr($author_box->abo_register_link); ?>"></td>
                </tr>

                <tr>
                    <th>ABO Modal Title</th>
                    <td><input type="text" name="abo_register_link_info_modal_title" class="regular-text" value="<?php echo esc_attr($author_box->abo_register_link_info_modal_title); ?>"></td>
                </tr>

                <tr>
                    <th>ABO Modal Content</th>
                    <td>
                        <?php
                        wp_editor(
                            $author_box->abo_register_link_info_modal_content,
                            'abo_register_link_info_modal_content',
                            array(
                                'textarea_name' => 'abo_register_link_info_modal_content',
                                'textarea_rows' => 8,
                                'media_buttons' => true,
                                'teeny'         => false,
                                'quicktags'     => true,
                            )
                        );
                        ?>
                    </td>
                </tr>

            </table>

            <?php
            wp_nonce_field('aboyuma_edit_author_box_action', 'aboyuma_author_box_nonce');
            submit_button('Update Author Info');
            ?>
        </form>
    </div>
<?php
}
