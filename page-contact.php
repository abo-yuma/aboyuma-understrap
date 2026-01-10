<?php

/**
 * Contact page template
 *
 * @package Understrap
 */

defined('ABSPATH') || exit;


// Create a per-user flash message key (hashed by IP + User Agent)
// to prevent flash messages from colliding between different visitors
// and to support Post-Redirect-Get pattern without using sessions.
$flash_key = 'aboyuma_form_flash_' . wp_hash($_SERVER['REMOTE_ADDR'] . ($_SERVER['HTTP_USER_AGENT'] ?? ''));

$flash = get_transient($flash_key);
if ($flash) {
    delete_transient($flash_key);
}

// --------------------
// Process POST
// --------------------
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['aboyuma_contact_nonce'])
    && wp_verify_nonce($_POST['aboyuma_contact_nonce'], 'aboyuma_contact_form')
) {

    // secutiry: Honeypot (anti-spam)
    if (!empty($_POST['aboyuma_hp'])) exit;

    // secutiry: Rate limit (1 request / minute per IP)
    $rate_key = 'aboyuma_contact_' . md5($_SERVER['REMOTE_ADDR']);
    if (get_transient($rate_key)) {
        set_transient($flash_key, [
            'status'  => 'warning',
            'message' => 'ส่งข้อมูลไม่สำเร็จ โปรดลองอีกครั้งหลัง 1 นาที'
        ], 10);
        wp_safe_redirect(esc_url_raw($_SERVER['REQUEST_URI']));
        exit;
    }
    set_transient($rate_key, true, 60);

    // Sanitize inputs
    $sender_email = sanitize_email($_POST['sender_email'] ?? '');
    $title        = sanitize_text_field($_POST['title'] ?? '');
    $notes        = sanitize_textarea_field($_POST['notes'] ?? '');

    // Validate required fields
    if (empty($sender_email) || empty($title) || !is_email($sender_email)) {
        set_transient($flash_key, [
            'status'  => 'warning',
            'message' => 'กรุณาระบุหัวข้อเรื่อง'
        ], 10);
        wp_safe_redirect(esc_url_raw($_SERVER['REQUEST_URI']));
        exit;
    }

    // Prepare email
    $to      = get_option('admin_email');
    $subject = 'New Contact: ' . $title;
    $body    = "Sender Email: {$sender_email}\n\nTitle: {$title}\n\nNotes:\n{$notes}\n";
    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $sender_email
    ];
    $attachments = [];

    // Handle file upload
    if (!empty($_FILES['attachment']['name'])) {

        require_once ABSPATH . 'wp-admin/includes/file.php';
        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];

        if ($_FILES['attachment']['size'] > 2 * 1024 * 1024) {
            set_transient($flash_key, [
                'status'  => 'warning',
                'message' => 'ไฟล์มีขนาดใหญ่เกินไป (ขนาดสูงสุด 2MB).'
            ], 10);
            wp_safe_redirect(esc_url_raw($_SERVER['REQUEST_URI']));
            exit;
        }

        if (!in_array($_FILES['attachment']['type'], $allowed_types)) {
            set_transient($flash_key, [
                'status'  => 'warning',
                'message' => 'ไฟล์ไม่ถูกต้อง'
            ], 10);
            wp_safe_redirect(esc_url_raw($_SERVER['REQUEST_URI']));
            exit;
        }

        $upload = wp_handle_upload($_FILES['attachment'], ['test_form' => false]);
        if (!isset($upload['error']) && isset($upload['file'])) {
            $attachments[] = $upload['file'];
        }
    }

    // Send email
    if (wp_mail($to, $subject, $body, $headers, $attachments)) {
        set_transient($flash_key, [
            'status'  => 'success',
            'message' => 'ส่งข้อมูลสำเร็จ!'
        ], 10);
    } else {
        set_transient($flash_key, [
            'status'  => 'error',
            'message' => 'ระบบไม่สามารถส่งข้อมูลได้ในขณะนี้ โปรดลองอีกครั้งภายหลัง'
        ], 10);
    }

    // PRG: redirect to avoid resubmission
    wp_safe_redirect(esc_url_raw($_SERVER['REQUEST_URI']));
    exit;
}

get_header();
$container = get_theme_mod('understrap_container_type');
?>

<main>
    <div class="<?php echo esc_attr($container); ?>" id="content" tabindex="-1">
        <div class="row py-3">

            <div class="sidebar-left-column-width order-2 order-md-1" id="sidebar-left">
                <?php get_template_part('sidebar-templates/sidebar', 'left'); ?>
            </div>

            <div class="main-content-column-width order-1 order-md-2">
                <div id="aboyuma-contact-wrapper" class="aboyuma-contact-wrapper">

                    <?php
                    while (have_posts()) {
                        the_post();
                        get_template_part('loop-templates/content', 'page');
                    }
                    ?>

                    <!-- Flash Message -->
                    <?php if (!empty($flash)) :
                        $status_class = 'alert-info';
                        switch ($flash['status'] ?? '') {
                            case 'success':
                                $status_class = 'alert-success';
                                break;
                            case 'error':
                                $status_class = 'alert-danger';
                                break;
                            case 'warning':
                                $status_class = 'alert-warning';
                                break;
                        }
                    ?>
                        <div class="alert <?php echo esc_attr($status_class); ?> alert-dismissible fade show p-2" role="alert">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <small class="mb-0 flex-grow-1"><?php echo esc_html($flash['message']); ?></small>
                                <span type="button" data-bs-dismiss="alert" aria-label="Close"
                                    class="text-muted" style="cursor: pointer; font-size: 0.9rem;">
                                    <i class="fa fa-times"></i>
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Contact Form -->
                    <form class="p-2" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="senderEmail" class="form-label">📧 อีเมล</label>
                            <input type="email" class="form-control form-control-sm" id="senderEmail" name="sender_email" placeholder="name@example.com" required>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">📝 เรื่อง</label>
                            <input type="text" class="form-control form-control-sm" id="title" name="title" placeholder="เช่น ต้องการสมัครสมาชิกแอมเวย์ หรือ สอบถาม" required>
                        </div>

                        <div class="mb-3">
                            <label for="fileUpload" class="form-label">🖼️ ไฟล์แนบ</label>
                            <input class="form-control form-control-sm" type="file" id="fileUpload" name="attachment">
                            <small class="form-text text-muted">
                                รองรับไฟล์ .jpg, .png, .pdf ขนาดไม่เกิน 2MB
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">🗒️ หมายเหตุ</label>
                            <textarea class="form-control form-control-sm" id="notes" name="notes" rows="4"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">📩 ส่งข้อมูล</button>
                        <?php wp_nonce_field('aboyuma_contact_form', 'aboyuma_contact_nonce'); ?>
                    </form>

                </div>
            </div>

        </div>
    </div>
</main>

<?php get_footer(); ?>