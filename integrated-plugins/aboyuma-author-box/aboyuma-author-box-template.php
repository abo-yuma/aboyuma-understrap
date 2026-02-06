<div id="aboyuma-author-box" class="card mb-2">
  <?php
  $coverImage = 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80';
  ?>

  <!-- Cover / background image -->
  <div
    class="author-cover"
    style="background-image: url('<?php echo esc_url($coverImage); ?>');">
  </div>

  <!-- Author image (overlapping cover) -->
  <div class="author-avatar-wrapper text-center">
    <img
      src="<?php echo esc_url($image); ?>"
      class="author-avatar rounded-circle img-fluid"
      alt="Profile image">
  </div>

  <div class="card-body p-2 text-center">

    <!-- Name + badge -->
    <h5 class="card-title mb-1 fw-bold d-flex justify-content-center align-items-center gap-2">
      <?php echo esc_html($title); ?>
      <img
        src="https://img.icons8.com/?size=100&id=aGppzsxFaUXs&format=png&color=0d6efd"
        id="aboyuma-qualified"
        data-bs-toggle="tooltip"
        data-bs-placement="top"
        title="<?php echo esc_html($aboyumaQualifiedTitle); ?>"
        width="15">
    </h5>

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

    <!-- Action buttons -->
    <div class="d-grid gap-2">

      <!-- Row 1 -->
      <div class="row g-2">
        <div class="col-10">
          <a
            href="<?php echo esc_url($memberRegisterLinkUrl); ?>"
            target="_blank"
            rel="noopener noreferrer"
            class="btn btn-sm btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-1">
            <span>🙍</span>
            <?php echo esc_html($memberRegisterLink); ?>
          </a>
        </div>

        <div class="col-2">
          <button
            type="button"
            class="btn btn-sm btn-outline-secondary w-100"
            data-bs-toggle="modal"
            data-bs-target="#memberRegisterModal">
            <i class="fa fa-info-circle"></i>
          </button>
        </div>
      </div>

      <!-- Row 2 -->
      <div class="row g-2">
        <div class="col-10">
          <a
            href="<?php echo esc_url($aboRegisterLinkUrl); ?>"
            target="_blank"
            rel="noopener noreferrer"
            class="btn btn-sm btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-1">
            <span>🧑‍💻</span>
            <?php echo esc_html($aboRegisterLink); ?>
          </a>
        </div>

        <div class="col-2">
          <button
            type="button"
            class="btn btn-sm btn-outline-secondary w-100"
            data-bs-toggle="modal"
            data-bs-target="#aboRegisterModal">
            <i class="fa fa-info-circle"></i>
          </button>
        </div>
      </div>

    </div>
  </div>
</div>