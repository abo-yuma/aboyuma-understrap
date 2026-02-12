<?php
// Register admin menu
function aboyuma_amway_shop_menu() {
    $pageTitle = 'Amway Shops';
    $menuTitle = 'Amway Shops';
    $capability = 'manage_options';
    $menuSlug = 'aboyuma-amway-shop-list';
    $callback = 'aboyuma_amway_shop_list';
    $iconUrl = 'dashicons-store';
    $position = 26;

    add_menu_page(
        $pageTitle,
        $menuTitle,
        $capability,
        $menuSlug,
        $callback,
        $iconUrl,
        $position
    );

    add_submenu_page(
        $menuSlug,
        'All Shops',
        'All Shops',
        $capability,
        $menuSlug,
        $callback
    );

    add_submenu_page(
        null,
        'Add New Shop',
        null,
        $capability,
        'aboyuma-amway-shop-add',
        'aboyuma_amway_shop_add'
    );

    add_submenu_page(
        null,
        'Edit Shop',
        null,
        $capability,
        'aboyuma-amway-shop-edit',
        'aboyuma_amway_shop_edit'
    );

    add_submenu_page(
        null,
        'Shop Detail',
        null,
        $capability,
        'aboyuma-amway-shop-detail',
        'aboyuma_amway_shop_detail'
    );

    add_submenu_page(
        null,
        'Delete Shop',
        null,
        $capability,
        'aboyuma-amway-shop-delete',
        'aboyuma_amway_shop_delete'
    );
}
add_action('admin_menu', 'aboyuma_amway_shop_menu');
