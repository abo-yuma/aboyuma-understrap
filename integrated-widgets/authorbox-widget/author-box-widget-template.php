<!-- Bootstrap 5 Author Card -->
<div id="aboyuma-author-box" class="text-center">
  <div class="author-box-body">
    <div id="author-image" class="mb-2" style="padding-left: 15px; padding-right: 15px;">
      <img src="<?php echo esc_url($image); ?>" class="card-img-top" alt="Profile image"
        style="border-top-left-radius: 0 !important; border-top-right-radius: 0 !important;">
    </div>
    <div id="author-profile">
      <span class="card-title" style="margin-bottom: 0; font-weight: bold;"><?php echo esc_html($title); ?></span>
      <img src="https://img.icons8.com/?size=100&id=98A4yZTt9abw&format=png&color=000000" id="aboyuma-qualified"
        data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo esc_html($aboyumaQualifiedTitle); ?>" width="16px">
      <p style="margin-bottom: 0; font-size: 14px; line-height: 1.2;" class="card-text mt-1"><?php echo esc_html($bio); ?></p>
    </div>
    <div id="aboyuma-member-register-link" class="mt-2">
      <ul class="list-unstyled m-0 text-center">
        <li class="mb-2 d-flex justify-content-center align-items-center">
          <!-- Main button link -->
          <a href="<?php echo esc_url($memberRegisterLinkUrl); ?>" target="_blank" rel="noopener noreferrer"
            class="btn btn-sm btn-secondary d-inline-flex align-items-center">
            <span class="member-register-icon me-1">🙍</span>
            <?php echo esc_html($memberRegisterLink); ?>
          </a>

          <!-- Inline info button -->
          <button type="button" class="btn btn-sm btn-outline-secondary ms-2" data-bs-toggle="modal" data-bs-target="#memberRegisterModal">
            <i class="fa fa-info-circle"></i>
          </button>
        </li>

        <li class="d-flex justify-content-center align-items-center">
          <!-- Main button link -->
          <a href="<?php echo esc_url($aboRegisterLinkUrl); ?>" target="_blank" rel="noopener noreferrer"
            class="btn btn-sm btn-secondary d-inline-flex align-items-center">
            <span class="abo-register-icon me-1">🧑‍💻</span>
            <?php echo esc_html($aboRegisterLink); ?>
          </a>

          <!-- Inline info button -->
          <button type="button" class="btn btn-sm btn-outline-secondary ms-2" data-bs-toggle="modal" data-bs-target="#aboRegisterModal">
            <i class="fa fa-info-circle"></i>
          </button>
        </li>
      </ul>

    </div>
  </div><!-- /.author-box-body -->
</div><!-- /#aboyuma-author-box -->
<style>
  .member-register-icon,
  .abo-register-icon {
    /*font-size: 1.25rem;*/
    vertical-align: 0px !important;
  }

  #aboyuma-member-register-link ul li {

    /* default is ~1.5, so this makes lines tighter */
    margin-bottom: 0;
    /* remove extra space between items */
  }
</style>