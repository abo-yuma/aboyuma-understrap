<?php
// Debug wp_mail
add_action('wp_mail_failed', 'aboyuma_log_wp_mail_error', 10, 1);
function aboyuma_log_wp_mail_error($wp_error) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('wp_mail failed: ' . $wp_error->get_error_message());
        error_log('wp_mail error data: ' . print_r($wp_error->get_error_data(), true));
    }
}

// Email sending
add_action('wp_ajax_aboyuma_contact_form', 'aboyuma_handle_contact_ajax');
add_action('wp_ajax_nopriv_aboyuma_contact_form', 'aboyuma_handle_contact_ajax');

function aboyuma_handle_contact_ajax() {

    // Nonce
    if (
        !isset($_POST['aboyuma_contact_nonce']) ||
        !wp_verify_nonce($_POST['aboyuma_contact_nonce'], 'aboyuma_contact_form')
    ) {
        wp_send_json_error(['message' => 'Security check failed']);
    }

    // Honeypot
    if (!empty($_POST['aboyuma_hp'])) {
        wp_send_json_error(['message' => 'Spam detected']);
    }

    // Rate limit
    $ip = get_real_ip();
    $rate_key = 'aboyuma_contact_' . md5($ip);
    if (get_transient($rate_key)) {
        wp_send_json_error(['message' => 'โปรดรอ 1 นาทีก่อนทำการส่งข้อมูลใหม่']);
    }
    set_transient($rate_key, true, 60);

    // Sanitize user input
    // About wp_unslash: https://github.com/WordPress/WordPress-Coding-Standards/issues/510
    $sender_email = sanitize_email(wp_unslash($_POST['sender_email'] ?? ''));
    $title        = sanitize_text_field(wp_unslash($_POST['title'] ?? ''));
    $message      = sanitize_textarea_field(wp_unslash($_POST['message'] ?? ''));

    if (empty($sender_email)) {
        wp_send_json_error(['message' => 'กรุณากรอกอีเมล']);
    } elseif (!is_email($sender_email)) {
        wp_send_json_error(['message' => 'รูปแบบอีเมลไม่ถูกต้อง']);
    }

    if (empty($title)) {
        wp_send_json_error(['message' => 'กรุณากรอกหัวข้อเรื่อง']);
    } elseif (mb_strlen($title) > 100) {
        wp_send_json_error(['message' => 'หัวข้อยาวเกิน 100 ตัวอักษร']);
    }

    if (empty($message)) {
        wp_send_json_error(['message' => 'กรุณากรอกข้อความ']);
    } elseif (mb_strlen($message) > 2000) {
        wp_send_json_error(['message' => 'ข้อความยาวเกิน 2000 ตัวอักษร']);
    }

    // Email
    $to      = get_option('admin_email');
    $subject = 'New Contact: ' . $title;

    $body  = "New contact form submission\n\n";
    $body .= "Sender Email: {$sender_email}\n";
    $body .= "Title: {$title}\n\n";
    $body .= "Message:\n{$message}\n";

    // Detect environment
    $is_local = wp_get_environment_type() === 'local';

    if ($is_local) {
        // Always use a fake but valid domain
        $from_email = 'no-reply@example.test';
        $from_name  = 'Local Dev';
    } else {

        $host = wp_parse_url(home_url(), PHP_URL_HOST);

        // Fallback safety
        if (!$host || $host === 'localhost') {
            $host = 'aboyuma.com';
        }

        $host = preg_replace('/^www\./', '', $host);

        $from_email = 'no-reply@' . $host;
        $from_name  = get_bloginfo('name');
    }


    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . $from_name . ' <' . $from_email . '>',
        'Reply-To: ' . $sender_email
    ];

    // ---------- Upload ----------
    require_once ABSPATH . 'wp-admin/includes/file.php';

    $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
    $max_size      = 2 * 1024 * 1024;
    $attachments   = [];
    $uploaded_files = [];
    $file_fields = ['attachment1', 'attachment2'];

    foreach ($file_fields as $field) {

        if (empty($_FILES[$field]['name'])) continue;

        $file = $_FILES[$field];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error(['message' => 'เกิดข้อผิดพลาดในการอัปโหลดไฟล์']);
        }

        if ($file['size'] > $max_size) {
            wp_send_json_error(['message' => 'ไฟล์ต้องมีขนาดไม่เกิน 2MB']);
        }

        // MIME type check
        $filetype = wp_check_filetype_and_ext(
            $file['tmp_name'],
            $file['name']
        );

        if (empty($filetype['ext']) || !in_array($filetype['type'], $allowed_types, true)) {
            wp_send_json_error(['message' => 'อนุญาตเฉพาะไฟล์ jpg, png, pdf เท่านั้น']);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $file['name'] = 'aboyuma_' . time() . '_' . wp_generate_password(6, false) . '.' . $ext;

        $upload = wp_handle_upload($file, ['test_form' => false]);

        if (!isset($upload['error']) && isset($upload['file'])) {
            $attachments[]    = $upload['file'];
            $uploaded_files[] = $upload['file'];
        }
    }

    // Send mail
    $sent = wp_mail($to, $subject, $body, $headers, $attachments);

    // remove files
    foreach ($uploaded_files as $file_path) {
        if (file_exists($file_path)) unlink($file_path);
    }

    if ($sent) {
        wp_send_json_success(['message' => 'ส่งข้อมูลสำเร็จ!']);
    } else {
        wp_send_json_error(['message' => 'ขออภัย ระบบไม่สามารถส่งข้อมูลได้ในขณะนี้ กรุณาติดต่อผ่านช่องทางอื่น']);
    }
}
