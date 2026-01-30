<div id="aboyuma-author-box" class="card mb-4">

  <!-- Author image -->
  <img
    src="<?php echo esc_url($image); ?>"
    class="card-img-top img-fluid"
    alt="Profile image">

  <div class="card-body text-center">

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
            class="btn btn-sm btn-dark w-100 d-flex align-items-center justify-content-center gap-1">
            <span>🙍</span>
            <?php echo esc_html($memberRegisterLink); ?>
          </a>
        </div>

        <div class="col-2">
          <button
            type="button"
            class="btn btn-sm btn-outline-dark w-100"
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
            class="btn btn-sm btn-dark w-100 d-flex align-items-center justify-content-center gap-1">
            <span>🧑‍💻</span>
            <?php echo esc_html($aboRegisterLink); ?>
          </a>
        </div>

        <div class="col-2">
          <button
            type="button"
            class="btn btn-sm btn-outline-dark w-100"
            data-bs-toggle="modal"
            data-bs-target="#aboRegisterModal">
            <i class="fa fa-info-circle"></i>
          </button>
        </div>
      </div>

    </div>
  </div>
</div>