<?php

/**
 * Contact page template
 *
 * @package Understrap
 */

defined('ABSPATH') || exit;

get_header();
$container = get_theme_mod('understrap_container_type');
$container_classes = $container . ' px-3 px-md-4 py-0';
?>

<main>
    <div class="<?php echo esc_attr($container_classes); ?>" id="content" tabindex="-1">
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
                    <h5>📩 ส่งข้อความถึงเรา</h5>
                    <form id="contact-form" class="p-2" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="aboyuma_contact_form">
                        <div class="mb-3">
                            <label for="senderEmail" class="form-label">📧 อีเมล</label>
                            <input type="email" class="form-control form-control-sm" id="senderEmail" name="sender_email" placeholder="name@example.com" required>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">📝 เรื่อง</label>
                            <input type="text" class="form-control form-control-sm" id="title" name="title"
                                placeholder="เช่น ต้องการให้สมัครสมาชิกแอมเวย์ให้" required>
                        </div>

                        <div class="mb-3">
                            <label for="attachment1" class="form-label">🖼️ ไฟล์แนบ</label>
                            <small class="form-text text-muted d-block mb-2">
                                กรณีแจ้งสมัครสมาชิก ให้แนบสำเนาบัตรประชาชนและสลิปโอนเงินค่าสมัครครับ
                            </small>
                            <input class="form-control form-control-sm mb-2"
                                type="file"
                                id="attachment1"
                                name="attachment1"
                                accept=".jpg,.jpeg,.png,.pdf">

                            <input class="form-control form-control-sm"
                                type="file"
                                id="attachment2"
                                name="attachment2"
                                accept=".jpg,.jpeg,.png">
                            <small class="form-text text-muted">
                                **รองรับไฟล์ .jpg, .png, .pdf ขนาดไม่เกิน 2MB
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">🗒️ ข้อความ</label>
                            <textarea class="form-control form-control-sm" id="message" name="message" rows="4"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">📩 ส่งข้อมูล</button>
                        <div id="contact-loading" style="display:none;margin-top:10px;">
                            ⏳ กำลังส่งข้อมูล กรุณารอสักครู่...
                        </div>
                        <div id="aboyuma-message-wrapper"></div>
                        <?php wp_nonce_field('aboyuma_contact_form', 'aboyuma_contact_nonce'); ?>
                    </form>

                </div>
            </div>

        </div>
    </div>
</main>
<script>
    jQuery(function($) {

        const $form = $('#contact-form');
        const $msgWrap = $('#aboyuma-message-wrapper');

        $form.on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            $msgWrap.html(''); // clear old message

            $('#contact-loading').show();
            $form.find('button').prop('disabled', true).css('opacity', '0.6');

            $.ajax({
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,

                success: function(res) {

                    let alertClass = res.success ? 'alert-success' : 'alert-danger';

                    let html = `
                        <div class="alert ${alertClass} alert-dismissible fade show p-2 mt-2" role="alert">
                            <div class="d-flex justify-content-between align-items-center w-100 gap-2">

                                <small class="mb-0 flex-grow-1">
                                    ${res.data.message}
                                </small>

                                <button type="button"
                                    class="btn p-0 border-0 bg-transparent text-muted"
                                    data-bs-dismiss="alert"
                                    aria-label="Close">
                                    <i class="fa fa-times"></i>
                                </button>

                            </div>
                        </div>`;

                    $msgWrap.html(html);

                    if (res.success) {
                        $form[0].reset();
                    }
                },

                error: function() {
                    $msgWrap.html(`
                    <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                        <small>ระบบขัดข้อง กรุณาลองใหม่</small>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `);
                },

                complete: function() {
                    $('#contact-loading').hide();
                    $form.find('button').prop('disabled', false).css('opacity', '1');
                }
            });
        });

    });
</script>


<?php get_footer(); ?>