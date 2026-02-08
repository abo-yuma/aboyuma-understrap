<div id="aboyuma-author-box" class="card mb-2">
  <?php
  $coverImage = 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80';
  ?>

  <!-- Author image (overlapping cover) -->
  <div class="author-avatar-wrapper text-center mt-3">
    <img
      src="<?php echo esc_url($image); ?>"
      class="author-avatar img-fluid rounded-2 border"
      alt="Profile image">
  </div>

  <div class="card-body p-2 text-center">

    <!-- Name + badge -->
    <p class="author-name card-title mb-1 d-flex justify-content-center align-items-center gap-2">
      <?php echo esc_html($title); ?>
      <img
        src="https://img.icons8.com/?size=100&id=aGppzsxFaUXs&format=png&color=0d6efd"
        id="aboyuma-qualified"
        data-bs-toggle="tooltip"
        data-bs-placement="top"
        title="<?php echo esc_html($aboyumaQualifiedTitle); ?>"
        width="15">
    </p>

    <!-- Bio -->
    <div class="card-text mt-2">
      <?php echo apply_filters('the_content', $bio); ?>
    </div>

    <!-- View profile -->
    <div class="mt-3">
      <a
        href="<?php echo esc_url($profileLinkUrl); ?>"
        target="_blank"
        class="btn btn-sm btn-outline-primary w-100">
        🪪 ดูโปรไฟล์
      </a>
    </div>

    <hr class="my-3">

    <div class="d-flex gap-2 mb-2">

      <a
        href="<?php echo esc_url($memberRegisterLinkUrl); ?>"
        target="_blank"
        rel="noopener noreferrer"
        class="btn btn-sm btn-outline-secondary flex-grow-1 d-flex align-items-center justify-content-center gap-1">
        <span>🙍</span>
        <?php echo esc_html($memberRegisterLink); ?>
      </a>

      <button
        type="button"
        class="btn btn-sm btn-outline-secondary"
        style="width: 38px;"
        data-bs-toggle="modal"
        data-bs-target="#memberRegisterModal">
        <i class="fa fa-info-circle"></i>
      </button>

    </div>
    <div class="d-flex gap-2">

      <a
        href="<?php echo esc_url($aboRegisterLinkUrl); ?>"
        target="_blank"
        rel="noopener noreferrer"
        class="btn btn-sm btn-outline-secondary flex-grow-1 d-flex align-items-center justify-content-center gap-1">
        <span>🧑‍💻</span>
        <?php echo esc_html($aboRegisterLink); ?>
      </a>

      <button
        type="button"
        class="btn btn-sm btn-outline-secondary"
        style="width: 38px;"
        data-bs-toggle="modal"
        data-bs-target="#aboRegisterModal">
        <i class="fa fa-info-circle"></i>
      </button>

    </div>


  </div>
</div>