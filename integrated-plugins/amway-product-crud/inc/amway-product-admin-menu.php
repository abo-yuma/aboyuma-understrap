<?php
function aboyuma_amway_product_menu() {
    $productPageTitle = 'Amway Products';
    $productPageMenuTitle = 'Amway Products';
    $productPageCapability = 'manage_options';
    $productPageMenuSlug = 'aboyuma-product-list';
    $productPageFunction = 'aboyuma_amway_product_list';
    $productPageIconUrl = 'dashicons-products';
    $productPagePosition = 25;

    add_menu_page(
        $productPageTitle,
        $productPageMenuTitle,
        $productPageCapability,
        $productPageMenuSlug,
        $productPageFunction,
        $productPageIconUrl,
        $productPagePosition
    );
    /*
    if you do not add the add_submenu_page for "All Products," WordPress will automatically 
    create a submenu item for the main menu. This default submenu item will have the same title 
    as the main menu ("aboyuma Product" in your case) and will point to the same slug and callback function.
    Behavior Without the Submenu Code:
    WordPress automatically mirrors the main menu as the first submenu item.
    The submenu item will have:
    Menu title: "aboyuma Product" (same as the main menu title).
    Slug: The same slug as the main menu (aboyuma-product).
    Callback function: The same callback function as the main menu.
    */
    add_submenu_page(
        $productPageMenuSlug,
        'Products', // Page title
        'Products', // Menu title
        $productPageCapability,
        $productPageMenuSlug, // Use the same slug as the main menu
        $productPageFunction
    );
    add_submenu_page(
        null,
        'Add New Product',
        null,
        $productPageCapability,
        'aboyuma-product-add',
        'aboyuma_amway_product_add'
    );
    // Add submenu page without adding menu 
    // Ref:https://stackoverflow.com/a/12758400/8527193
    add_submenu_page(
        null,
        'Product Detail',
        null,
        $productPageCapability,
        'aboyuma-product-detail',
        'aboyuma_amway_product_detail'
    );
    add_submenu_page(
        null,
        'Edit Product',
        null,
        $productPageCapability,
        'aboyuma-product-edit',
        'aboyuma_amway_product_edit'
    );
    // ==============================
    // PRODUCT PROPERTIES
    // ==============================
    add_submenu_page(
        $productPageMenuSlug,
        'Product Properties',
        'Product Properties',
        $productPageCapability,
        'aboyuma-product-property-list',
        'aboyuma_amway_product_property_list'
    );

    add_submenu_page(
        null,
        'Add New Property',
        null,
        $productPageCapability,
        'aboyuma-product-property-add',
        'aboyuma_amway_product_property_add'
    );

    add_submenu_page(
        null,
        'Edit Property',
        null,
        $productPageCapability,
        'aboyuma-product-property-edit',
        'aboyuma_amway_product_property_edit'
    );
    // ==============================
    // PRODUCT BRANDS
    // ==============================
    add_submenu_page(
        $productPageMenuSlug,
        'Brands',
        'Brands',
        $productPageCapability,
        'aboyuma-product-brand-list',
        'aboyuma_amway_product_brand_list'
    );
    add_submenu_page(
        null,
        'Add New Brand',
        null,
        $productPageCapability,
        'aboyuma-product-brand-add',
        'aboyuma_amway_product_brand_add'
    );
    add_submenu_page(
        null,
        'Edit Brand',
        null,
        $productPageCapability,
        'aboyuma-product-brand-edit',
        'aboyuma_amway_product_brand_edit'
    );
    // ==============================
    // PRODUCT CATEGORIES
    // ==============================
    add_submenu_page(
        $productPageMenuSlug,
        'Categories',
        'Categories',
        $productPageCapability,
        'aboyuma-product-category-list',
        'aboyuma_amway_product_category_list'
    );
    add_submenu_page(
        null,
        'Add New Category',
        null,
        $productPageCapability,
        'aboyuma-product-category-add',
        'aboyuma_amway_product_category_add'
    );
    add_submenu_page(
        null,
        'Edit Category',
        null,
        $productPageCapability,
        'aboyuma-product-category-edit',
        'aboyuma_amway_product_category_edit'
    );
}
add_action('admin_menu', 'aboyuma_amway_product_menu');
