<nav id="navbar-main" class="navbar container navbar-expand-md bg-dark px-4 py-0" data-bs-theme="dark">

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar"
        aria-controls="navbar" aria-expanded="false"
        aria-label="<?php esc_attr_e('Toggle navigation', 'aboyuma'); ?>">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div id="navbar" class="collapse navbar-collapse">
        <?php
        // Loading WordPress Custom Menu (theme_location).
        wp_nav_menu(
            array(
                'theme_location'  => 'primary',
                'container'       => false,
                'menu_class'      => '',
                'fallback_cb'     => '__return_false',
                'items_wrap'      => '<ul id="%1$s" class="navbar-nav me-auto mb-2 mb-md-0 %2$s">%3$s</ul>',
                'depth'           => 3,
                'walker'          => new bootstrap_5_wp_nav_menu_walker()
            )
        );
        ?>
        <!-- Dark Mode Switch -->
        <div class="d-flex align-items-center ms-auto">
            <div class="form-check form-switch">
                <label class="form-check-label" for="flexSwitchCheckDefault" style="color: #949494;">Dark mode</label>
                <input class="form-check-input" type="checkbox" id="dark-mode-switch">
            </div>
        </div>
    </div><!-- /.navbar-collapse -->

</nav><!-- /#header -->