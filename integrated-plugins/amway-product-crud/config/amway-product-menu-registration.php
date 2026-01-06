<?php
/*
https://stackoverflow.com/a/59558080/8527193

You do not need to install any additional plug-in. Just follow the following procedure and you are good to go.
Create a page under Administration>Pages>Add new.
Enter the name you want for your page, the custom URL and press publish.
This would generate a record in your database under the wp_posts table. 
(wp_ is the default prefix for your wordpress tables. If you changed it during the installation procedure, 
replace it with your custom prefix). Find the ID that has the name of your page in the table.
Go to wp-content\themes\YOUR_ACTIVE_THEME in your wordpress installation directory 
or in the sub-folders(it depends how the theme has been structured). There would be page.php file. 
Create a page-{ID}.php file in the same directory.
It's ready. Add any static html file to page-{ID}.php file.
Now, if you go the custom URL, you can find the static content you put in page-{ID}.php file.

https://stackoverflow.com/a/44495723/8527193

Add a page with format page-{slug}.php for example page-products.php to your theme. 
You also need to add an empty page on wordpress under pages with that name i.e. 
if your for example your file is page-products.php, the id for the page you create on wordpress should be products . 
This should work. For more information, you can check this link 
https://developer.wordpress.org/themes/template-files-section/page-template-files/#creating-a-custom-page-template-for-one-specific-page.

*/
function add_static_page_to_menu()
{
    // Specify the menu name where you want to add the page
    $menu_name = 'primary'; // Replace with your menu's name or slug
    $menu = wp_get_nav_menu_object($menu_name);
    // Check if the menu exists
    if ($menu) {
        $menu_id = $menu->term_id;
        // Get the page object by its slug (file name)
        $page = get_page_by_path('aboyuma-product-menu-registration'); // Replace 'your-page-slug' with the actual slug
        if ($page) {
            // Add the page to the menu
            wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-object-id'   => $page->ID, // Use the page ID from the object
                'menu-item-object'      => 'page',
                'menu-item-type'        => 'post_type',
                'menu-item-status'      => 'publish',
            ]);
        } else {
            error_log('Page not found for the given slug.');
        }
    } else {
        error_log('Menu not found.');
    }
}
add_action('after_switch_theme', 'add_static_page_to_menu');
