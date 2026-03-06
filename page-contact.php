<?php

/**
 * Contact page template
 *
 * @package Understrap
 */

defined('ABSPATH') || exit;

get_header();
$container = get_theme_mod('understrap_container_type');
$container_classes = $container . ' px-4 py-0';
?>

<main>
    <div class="<?php echo esc_attr($container_classes); ?>" id="content" tabindex="-1">
        <div class="row">

            <div class="sidebar-left-column border-start order-2" id="sidebar-left">
                <?php get_template_part('sidebar-templates/sidebar', 'left'); ?>
            </div>

            <div class="main-content-column order-1">
                <div class="main-content-wrapper">
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
                        <h3 class="contact-form-title">📩 ส่งข้อความถึงเรา</h3>

                        <form id="contact-form" class="contact-form p-3 mb-3" enctype="multipart/form-data">

                            <div style="display:none !important">
                                <label>Leave this field empty</label>
                                <input type="text" name="aboyuma_hp" tabindex="-1" autocomplete="off">
                            </div>

                            <input type="hidden" name="action" value="aboyuma_contact_form">

                            <!-- Email -->
                            <div class="row mb-3">
                                <label for="senderEmail" class="col-sm-3 col-form-label">
                                    📧 อีเมล
                                </label>
                                <div class="col-sm-9">
                                    <input
                                        type="email"
                                        class="form-control form-control-sm"
                                        id="senderEmail"
                                        name="sender_email"
                                        required>
                                </div>
                            </div>

                            <!-- Title -->
                            <div class="row mb-3">
                                <label for="title" class="col-sm-3 col-form-label">
                                    📝 เรื่อง
                                </label>
                                <div class="col-sm-9">
                                    <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        id="title"
                                        name="title"
                                        maxlength="100"
                                        required>
                                </div>
                            </div>

                            <!-- Attachment -->
                            <div class="row mb-3">
                                <label for="attachment1" class="col-sm-3 col-form-label">
                                    🖼️ ไฟล์แนบ
                                </label>
                                <div class="col-sm-9">
                                    <input
                                        class="form-control form-control-sm mb-1"
                                        type="file"
                                        id="attachment1"
                                        name="attachment1"
                                        accept=".jpg,.jpeg,.png,.pdf">
                                    <small class="text-muted">
                                        รองรับไฟล์ .jpg, .png, .pdf ขนาดไม่เกิน 2MB
                                    </small>
                                </div>
                            </div>

                            <!-- Message -->
                            <div class="row mb-3">
                                <label for="message" class="col-sm-3 col-form-label">
                                    🗒️ ข้อความ
                                </label>
                                <div class="col-sm-9">
                                    <textarea
                                        class="form-control form-control-sm"
                                        id="message"
                                        name="message"
                                        rows="4"
                                        maxlength="2000"></textarea>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="row">
                                <div class="col-sm-9 offset-sm-3">
                                    <button type="submit" class="btn btn-light w-100">
                                        📩 ส่งข้อมูล
                                    </button>

                                    <div id="contact-loading" style="display:none;margin-top:10px;">
                                        ⏳ กำลังส่งข้อมูล กรุณารอสักครู่...
                                    </div>

                                    <div id="aboyuma-message-wrapper"></div>
                                </div>
                            </div>

                            <?php wp_nonce_field('aboyuma_contact_form', 'aboyuma_contact_nonce'); ?>
                        </form>


                    </div>
                </div>
            </div>

        </div>
    </div>
</main>
<script>
    jQuery(function($) {

        const $form = $('#contact-form');
        const $msgWrap = $('#aboyuma-message-wrapper');
        const $loading = $('#contact-loading');
        const $submitBtn = $form.find('button[type="submit"]');

        // ✅ Allow only these domains
        const allowedDomains = [
            'gmail.com',
            'hotmail.com',
            'outlook.com',
            'live.com'
        ];

        function isAllowedEmail(email) {
            if (!email.includes('@')) return false;

            const [local, domain] = email.toLowerCase().split('@');
            return allowedDomains.includes(domain);
        }

        function showMessage(type, message) {
            const alertClass = type === 'success' ?
                'alert-success' :
                type === 'warning' ?
                'alert-warning' :
                'alert-danger';

            $msgWrap.html(`
            <div class="alert ${alertClass} alert-dismissible fade show p-2 mt-2" role="alert">
                <div class="d-flex justify-content-between align-items-center w-100 gap-2">
                    <small class="mb-0 flex-grow-1">${message}</small>
                    <button type="button"
                        class="btn p-0 border-0 bg-transparent text-muted"
                        data-bs-dismiss="alert"
                        aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
        `);
        }

        $form.on('submit', function(e) {
            e.preventDefault();

            $msgWrap.html('');

            const email = $('#senderEmail').val().trim();

            // Email validation
            if (!isAllowedEmail(email)) {
                showMessage(
                    'warning',
                    'กรุณาใช้อีเมลจาก Gmail, Hotmail หรือ Outlook เท่านั้น'
                );
                return;
            }

            const formData = new FormData(this);

            $loading.show();
            $submitBtn.prop('disabled', true).css('opacity', '0.6');

            $.ajax({
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,

                success: function(res) {
                    if (res && res.data && res.data.message) {
                        showMessage(res.success ? 'success' : 'error', res.data.message);
                    } else {
                        showMessage('error', 'เกิดข้อผิดพลาด');
                    }

                    if (res.success) {
                        $form[0].reset();
                    }
                },

                error: function() {
                    showMessage('error', 'ระบบขัดข้อง กรุณาลองใหม่');
                },

                complete: function() {
                    $loading.hide();
                    $submitBtn.prop('disabled', false).css('opacity', '1');
                }
            });
        });

    });
</script>

<?php get_footer(); ?>