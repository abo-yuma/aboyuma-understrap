<?php

/**
 * Extra User Profile Fields
 */
function aboyuma_extra_profile_fields($user) {
?>

    <h2>ABO YUMA Extra Profile Fields</h2>
    <table class="form-table">
        <tr>
            <th>
                <label for="about_me_page_url">About Me Page</label>
            </th>
            <td>
                <input
                    type="url"
                    name="about_me_page_url"
                    id="about_me_page_url"
                    value="<?php echo esc_attr(get_user_meta($user->ID, 'about_me_page_url', true)); ?>"
                    class="regular-text">

                <p class="description">
                    URL About Me: https://example.com/about-me/
                </p>
            </td>
        </tr>

        <tr>
            <th>
                <label for="support_me_page_url">Support Me Page</label>
            </th>
            <td>
                <input
                    type="url"
                    name="support_me_page_url"
                    id="support_me_page_url"
                    value="<?php echo esc_attr(get_user_meta($user->ID, 'support_me_page_url', true)); ?>"
                    class="regular-text">

                <p class="description">
                    URL Support Me: https://example.com/support-me/
                </p>
            </td>
        </tr>
    </table>

    <!-- Career -->
    <h3>Career</h3>

    <table class="form-table">

        <tr>
            <th>
                <label for="career">Career 1</label>
            </th>
            <td>
                <input
                    type="text"
                    name="career"
                    id="career"
                    value="<?php echo esc_attr(get_user_meta($user->ID, 'career', true)); ?>"
                    class="regular-text">
            </td>
        </tr>

        <tr>
            <th>
                <label for="career_2">Career 2</label>
            </th>
            <td>
                <input
                    type="text"
                    name="career_2"
                    id="career_2"
                    value="<?php echo esc_attr(get_user_meta($user->ID, 'career_2', true)); ?>"
                    class="regular-text">
            </td>
        </tr>

    </table>

    <!-- Social Links -->
    <h3>Social Links</h3>

    <table class="form-table">

        <tr>
            <th>
                <label for="facebook_url">Facebook</label>
            </th>
            <td>
                <input
                    type="url"
                    name="facebook_url"
                    id="facebook_url"
                    value="<?php echo esc_attr(get_user_meta($user->ID, 'facebook_url', true)); ?>"
                    class="regular-text">
            </td>
        </tr>

        <tr>
            <th>
                <label for="instagram_url">Instagram</label>
            </th>
            <td>
                <input
                    type="url"
                    name="instagram_url"
                    id="instagram_url"
                    value="<?php echo esc_attr(get_user_meta($user->ID, 'instagram_url', true)); ?>"
                    class="regular-text">
            </td>
        </tr>

        <tr>
            <th>
                <label for="youtube_url">YouTube</label>
            </th>
            <td>
                <input
                    type="url"
                    name="youtube_url"
                    id="youtube_url"
                    value="<?php echo esc_attr(get_user_meta($user->ID, 'youtube_url', true)); ?>"
                    class="regular-text">
            </td>
        </tr>

    </table>

<?php
}

add_action('show_user_profile', 'aboyuma_extra_profile_fields');
add_action('edit_user_profile', 'aboyuma_extra_profile_fields');


/**
 * Save Extra User Profile Fields
 */
function aboyuma_save_extra_profile_fields($user_id) {
    if (! current_user_can('edit_user', $user_id)) {
        return;
    }

    $fields = array(
        'career',
        'career_2',
        'abo_id',
        'about_me_page_url',
        'support_me_page_url',
        'facebook_url',
        'instagram_url',
        'youtube_url',
    );

    foreach ($fields as $field) {

        if (isset($_POST[$field])) {

            if (str_ends_with($field, '_url')) {

                update_user_meta(
                    $user_id,
                    $field,
                    esc_url_raw(wp_unslash($_POST[$field]))
                );
            } else {

                update_user_meta(
                    $user_id,
                    $field,
                    sanitize_text_field(wp_unslash($_POST[$field]))
                );
            }
        }
    }
}

add_action('personal_options_update', 'aboyuma_save_extra_profile_fields');
add_action('edit_user_profile_update', 'aboyuma_save_extra_profile_fields');
