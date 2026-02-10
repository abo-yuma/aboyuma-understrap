<?php

/**
 * Auto register Amway Product pages + menu
 * Location: integrated/amway-product/register-menu.php
 */

function aboyuma_register_amway_product_page() {

    // 1️⃣ Register menu location (ถ้ายังไม่มี)
    register_nav_menu('primary', __('Primary', 'aboyuma'));

    // 2️⃣ Pages definition (เฉพาะ product module)
    $pages = [
        'amway-product'        => 'สินค้าแนะนำ',        // ✅ Add to menu
        'amway-product-detail' => 'รายละเอียดสินค้า',   // ❌ Not in menu
    ];

    $exclude_from_menu = ['amway-product-detail'];
    $created_page_ids  = [];

    // 3️⃣ Create pages if not exist
    foreach ($pages as $slug => $title) {

        $existing_page = get_page_by_path($slug);

        if (!$existing_page) {
            $page_id = wp_insert_post([
                'post_title'  => $title,
                'post_name'   => $slug,
                'post_status' => 'publish',
                'post_type'   => 'page',
            ]);

            if ($page_id && !is_wp_error($page_id)) {
                $created_page_ids[$slug] = $page_id;
            }
        } else {
            $created_page_ids[$slug] = $existing_page->ID;
        }
    }

    // 4️⃣ Create / get menu
    $menu_name  = 'Primary';
    $menu       = wp_get_nav_menu_object($menu_name);
    $menu_id    = $menu ? $menu->term_id : wp_create_nav_menu($menu_name);

    // 5️⃣ Add menu item (เฉพาะ amway-product)
    foreach ($created_page_ids as $slug => $page_id) {

        if (in_array($slug, $exclude_from_menu)) {
            continue;
        }

        $menu_items   = wp_get_nav_menu_items($menu_id);
        $page_exists  = false;

        if ($menu_items) {
            foreach ($menu_items as $item) {
                if ((int) $item->object_id === (int) $page_id) {
                    $page_exists = true;
                    break;
                }
            }
        }

        if (!$page_exists) {
            wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-title'     => get_the_title($page_id),
                'menu-item-object'    => 'page',
                'menu-item-object-id' => $page_id,
                'menu-item-type'      => 'post_type',
                'menu-item-status'    => 'publish',
            ]);
        }
    }

    // 6️⃣ Assign menu to location
    set_theme_mod('nav_menu_locations', [
        'primary' => $menu_id,
    ]);
}

add_action('after_switch_theme', 'aboyuma_register_amway_product_page');
