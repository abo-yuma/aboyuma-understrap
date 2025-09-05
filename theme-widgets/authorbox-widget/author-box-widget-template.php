<!-- Bootstrap 5 Author Card -->
<div id="aboyuma-author-box" class="text-center p-2">
  <div class="author-box-body">
    <div id="author-image" class="mb-2">
      <img src="<?php echo esc_url($image); ?>" class="card-img-top" alt="Profile image"
        style="border-top-left-radius: 0 !important; border-top-right-radius: 0 !important;">
    </div>
    <div id="author-profile">
      <span class="card-title" style="margin-bottom: 0; font-weight: bold;">🧑‍💻 <?php echo esc_html($title); ?></span>
      <img src="https://img.icons8.com/?size=100&id=98A4yZTt9abw&format=png&color=000000" id="aboyuma-qualified"
        data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo esc_html($aboyumaQualifiedTitle); ?>" width="16px">
      <p style="margin-bottom: 0; font-size: 13px;line-height: 1.2;" class="card-text"><?php echo esc_html($bio); ?></p>
    </div>
    <div id="aboyuma-member-register-link" class="mt-1">
      <ul style="padding: 0; margin: 0;">
        <li>
          <a href="<?php echo esc_url(url: $memberRegisterLinkUrl); ?>" target="_blank">
            <span class="member-register-icon">🙍</span> <?php echo esc_html($memberRegisterLink); ?>
          </a>
          <!-- Info Icon triggers modal -->
          <i style="cursor: pointer;" class="fa fa-info-circle ml-2" data-bs-toggle="modal" data-bs-target="#memberRegisterModal"></i>
        </li>
        <li>
          <a href="<?php echo esc_url(url: $aboRegisterLinkUrl); ?>" target="_blank">
            <span class="abo-register-icon">🧑‍💻</span> สมัครนักธุรกิจแอมเวย์
          </a>
          <!-- Info Icon triggers modal -->
          <i style="cursor: pointer;" class="fa fa-info-circle ml-2" data-bs-toggle="modal" data-bs-target="#aboRegisterModal"></i>
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