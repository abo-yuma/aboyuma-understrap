<?php

/**
 * Plugin Name: Amway Product Management System
 * Description: Theme integrated plugin for managing Amway products with full CRUD functionality.
 * Version:     1.0.0
 *
 * Bootstrap file for Amway Product Management System.
 * Acts as the entry point for this theme-integrated plugin.
 * Responsible only for defining base paths and loading all required modules.
 */
defined('ABSPATH') || exit;

$amway_product_crud_plugin_path = get_template_directory() . '/integrated-plugins/amway-product-crud/';

$amway_product_crud_files = [

	// CORE
	'/inc/core/amway-product-database.php',

	// ADMIN 
	'/inc/admin/amway-product-admin-menu.php',
	'/inc/admin/amway-product-admin-enqueue.php',

	// PRODUCT INPUT HANDLING (Sanitize & Validate)
	'/inc/product/product-sanitize.php',
	'/inc/product/product-validation.php',

	// OPERATION: PRODUCT
	'/operation/product/amway-product-list.php',
	'/operation/product/amway-product-add.php',
	'/operation/product/amway-product-detail.php',
	'/operation/product/amway-product-edit.php',
	'/operation/product/amway-product-delete.php',

	// OPERATION: PRODUCT PROPERTY
	'/operation/product/property/amway-product-property-list.php',
	'/operation/product/property/amway-product-property-add.php',
	'/operation/product/property/amway-product-property-edit.php',
	'/operation/product/property/amway-product-property-delete.php',

	// OPERATION: BRAND
	'/operation/brand/amway-product-brand-list.php',
	'/operation/brand/amway-product-brand-add.php',
	'/operation/brand/amway-product-brand-edit.php',
	'/operation/brand/amway-product-brand-delete.php',

	// OPERATION: CATEGORY
	'/operation/category/amway-product-category-list.php',
	'/operation/category/amway-product-category-add.php',
	'/operation/category/amway-product-category-edit.php',
	'/operation/category/amway-product-category-delete.php',

	// FRONTEND
	'/frontend/amway-product-frontend-enqueue.php',
	'/frontend/amway-product-frontend-handler.php',
	'/frontend/amway-product-frontend-meta.php',
	'/frontend/amway-product-frontend-page-registration.php',
];

foreach ($amway_product_crud_files as $file) {
	require_once $amway_product_crud_plugin_path . $file;
}
