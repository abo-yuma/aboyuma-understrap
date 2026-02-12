<?php
/**
 * Plugin Name: Amway Shop Management System
 * Description: Theme integrated plugin for managing Amway shops with full CRUD functionality.
 * Version:     1.0.0
 * 
 * Bootstrap file for Amway Shop Management System.
 * Acts as the entry point for this theme-integrated plugin.
 * Responsible only for defining base paths and loading all required modules.
 */

defined('ABSPATH') || exit;

$amway_shop_crud_plugin_path = get_template_directory() . '/integrated-plugins/amway-shop-crud/';

$amway_shop_crud_files = [

	// DATABASE
	'/inc/amway-shop-database.php',

	// ADMIN MENU
	'/inc/amway-shop-admin-menu.php',

	// ADMIN SCRIPTS
	'/inc/amway-shop-admin-enqueue.php',

	// OPERATION: PRODUCT
	'/operation/amway-shop-list.php',
	'/operation/amway-shop-add.php',
	'/operation/amway-shop-edit.php',
	'/operation/amway-shop-delete.php',

];

foreach ($amway_shop_crud_files as $file) {
	require_once $amway_shop_crud_plugin_path . $file;
}
