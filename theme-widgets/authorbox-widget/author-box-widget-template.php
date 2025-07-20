<!-- Bootstrap 5 Author Card -->
<div id="aboyuma-author-box" class="card text-center">
  <div class="card-body">
    <div id="author-image" class="mb-2">
      <img src="<?php echo esc_url($image); ?>" class="card-img-top" alt="Profile image"
        style="border-top-left-radius: 0 !important; border-top-right-radius: 0 !important;">
    </div>
    <div id="author-profile">
      <span class="card-title" style="margin-bottom: 0; font-weight: bold;"><?php echo esc_html($title); ?></span>
      <img src="https://img.icons8.com/?size=100&id=98A4yZTt9abw&format=png&color=000000" id="aboyuma-qualified"
        data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo esc_html($aboyumaQualifiedTitle); ?>" width="16px">
      <p style="margin-bottom: 0; font-size: 13px;line-height: 1;"> <?php echo esc_html($position); ?></p>
      <p style="margin-bottom: 0; font-size: 13px;line-height: 1.2;" class="card-text"><?php echo esc_html($bio); ?></p>
    </div>
    <div id="aboyuma-amway-member-register-link" class="mt-1">
      <ul style="padding: 0; margin: 0;">
        <li>
          <a href="<?php echo esc_url(url: $memberRegisterLinkUrl); ?>" target="_blank">
            <i class="<?php echo esc_html($memberRegisterLinkIcon); ?>" aria-hidden="true"></i> <?php echo esc_html($memberRegisterLink); ?>
          </a>
          <i style="cursor: pointer;" class="fa fa-info-circle ml-2"
            aria-hidden="true"
            data-bs-toggle="popover"
            data-bs-html="true"
            data-bs-title="<i class='fa fa-info-circle' aria-hidden='true'></i> <?php echo esc_html($memberRegisterLinkInfoPopoverTitle); ?>"
            data-bs-content="<?php echo esc_html($memberRegisterLinkInfoPopoverContent); ?>">
          </i>
        </li>
      </ul>
    </div>

  </div>
</div>