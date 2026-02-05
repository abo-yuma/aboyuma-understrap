<?php
function aboyuma_get_author_box_template() {
    global $wpdb;

    $table = $wpdb->prefix . 'aboyuma_author_box';

    // Get latest record (or change to WHERE id = 1 if you want fixed row)
    $author = $wpdb->get_row("SELECT * FROM {$table} ORDER BY id DESC LIMIT 1");

    if (!$author) {
        return; // nothing to show
    }

    // Map DB → template variables
    $title  = $author->title;
    $bio    = $author->bio;
    $image  = $author->image;

    $aboyumaQualifiedTitle = $author->aboyuma_qualified_title;

    $profileLinkUrl = $author->profile_link_url;

    $memberRegisterLinkUrl             = $author->member_register_link_url;
    $memberRegisterLink                = $author->member_register_link;
    $memberRegisterLinkInfoModalTitle  = $author->member_register_link_info_modal_title;
    $memberRegisterLinkInfoModalContent = $author->member_register_link_info_modal_content;

    $aboRegisterLinkUrl            = $author->abo_register_link_url;
    $aboRegisterLink               = $author->abo_register_link;
    $aboRegisterLinkInfoModalTitle = $author->abo_register_link_info_modal_title;
    $aboRegisterLinkInfoModalContent = $author->abo_register_link_info_modal_content;

    // Make modal data globally available
    global $aboyuma_member_modal_data, $aboyuma_abo_modal_data;

    $aboyuma_member_modal_data = array(
        'title'   => $memberRegisterLinkInfoModalTitle,
        'content' => $memberRegisterLinkInfoModalContent,
    );

    $aboyuma_abo_modal_data = array(
        'title'   => $aboRegisterLinkInfoModalTitle,
        'content' => $aboRegisterLinkInfoModalContent,
    );

    /**
     * Add the modal HTML at the end of the page (footer)
     * We use wp_footer hook so the modal is outside any sticky or positioned containers
     * Reason: Modals use position: fixed and rely on being at top-level of the DOM
     * If the modal is nested inside a sticky, relative, or overflow:hidden container (like the author box widget),
     * it can get clipped or appear behind other elements due to stacking context / z-index issues
     */
    if (!has_action('wp_footer', 'aboyuma_member_modal')) {
        add_action('wp_footer', 'aboyuma_member_modal');
    }
    if (!has_action('wp_footer', 'aboyuma_abo_modal')) {
        add_action('wp_footer', 'aboyuma_abo_modal');
    }

    /**
     * DESIGN NOTE:
     * Direct output would work here, but this follows common template rendering practice.
     * 
     * Capture template output and return it as a string instead of printing it.
     *
     * ob_start() begins output buffering, so any HTML or echo inside the template
     * is stored in memory instead of being sent to the browser.
     *
     * require locate_template(...) executes the template file normally,
     * but all of its output goes into the buffer.
     *
     * ob_get_clean() returns the buffered output as a string and then
     * clears the buffer.
     *
     * Result:
     * This allows the template HTML to be returned and reused in templates,
     * shortcodes, AJAX handlers, REST API responses, and filters.
     */
    ob_start();
    require locate_template('/integrated-plugins/aboyuma-author-box/aboyuma-author-box-template.php');
    return ob_get_clean();
}

function aboyuma_member_modal() {
    global $aboyuma_member_modal_data;

    // Stop if there is no data
    if (empty($aboyuma_member_modal_data)) {
        return;
    }

    $title   = $aboyuma_member_modal_data['title'];
    $content = $aboyuma_member_modal_data['content'];
?>
    <div class="modal fade" id="memberRegisterModal" tabindex="-1" aria-labelledby="memberRegisterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="memberRegisterModalLabel"><?php echo esc_html($title); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php echo wp_kses_post($content); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>
<?php
}


function aboyuma_abo_modal() {
    global $aboyuma_abo_modal_data;

    // Stop if there is no data
    if (empty($aboyuma_abo_modal_data)) {
        return;
    }

    $title   = $aboyuma_abo_modal_data['title'];
    $content = $aboyuma_abo_modal_data['content'];
?>
    <div class="modal fade" id="aboRegisterModal" tabindex="-1" aria-labelledby="aboRegisterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aboRegisterModalLabel"><?php echo esc_html($title); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php echo apply_filters('the_content', $content); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>
<?php
}

/**
 * Display helper function.
 * prints the HTML returned by aboyuma_get_author_box_template().
 * Use this in theme templates where you want to show the author box.
 */
function aboyuma_author_box_render() {
    echo aboyuma_get_author_box_template();
}
