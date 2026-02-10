/**
 * Rollup plugins
 * We use CommonJS `require` so Node does NOT need "type": "module"
 */
const resolve = require("@rollup/plugin-node-resolve").default;
const commonjs = require("@rollup/plugin-commonjs");
const terser = require("@rollup/plugin-terser").default;
const postcss = require("rollup-plugin-postcss");
const cssnano = require("cssnano");

/**
 * build()
 *
 * A small helper function so we don’t repeat the same config
 * for every JS file.
 *
 * @param {string} entry     - JS entry file (source)
 * @param {string} outputDir - Output folder (admin / frontend)
 * @param {string} name      - Base filename (without .min)
 */
function build(entry, outputDir, name) {
	return {
		/**
		 * Entry JS file
		 * Each entry is built independently (NO code splitting)
		 */
		input: entry,

		/**
		 * Output configuration
		 */
		output: {
			/**
			 * Final JS output file
			 * Example:
			 * assets/admin/amway-product-add.min.js
			 */
			file: `${outputDir}/${name}.min.js`,

			/**
			 * IIFE = Immediately Invoked Function Expression
			 * Outputs a classic script that runs immediately
			 * (ideal for WordPress wp_enqueue_script)
			 */
			format: "iife",
		},

		/**
		 * Plugins run from top → bottom
		 */
		plugins: [
			/**
			 * Allows Rollup to resolve node_modules
			 * (needed if you import libraries)
			 */
			resolve(),

			/**
			 * Converts CommonJS dependencies to ES modules
			 * (many npm packages still use CommonJS)
			 */
			commonjs(),

			/**
			 * Handles CSS imports inside JS
			 */
			postcss({
				/**
				 * Extract CSS into a real file instead of JS
				 * Example:
				 * assets/admin/amway-product-add.min.css
				 */
				extract: `${name}.min.css`,

				/**
				 * Minify CSS
				 */
				minimize: true,

				/**
				 * cssnano does the actual CSS minification
				 */
				plugins: [cssnano()],
			}),

			/**
			 * Minify JavaScript
			 * Outputs *.min.js
			 */
			terser(),
		],
	};
}

/**
 * Export an ARRAY of build configs
 * Rollup will run each one independently
 */
module.exports = [
	// ======================
	// ADMIN
	// ======================
	build(
		"src/admin/js/amway-product-add.js",
		"assets/admin",
		"amway-product-add",
	),

	build(
		"src/admin/js/amway-product-edit.js",
		"assets/admin",
		"amway-product-edit",
	),

	build("src/admin/js/delete-product.js", "assets/admin", "delete-product"),

	build(
		"src/admin/js/delete-product-brand.js",
		"assets/admin",
		"delete-product-brand",
	),

	build(
		"src/admin/js/delete-product-category.js",
		"assets/admin",
		"delete-product-category",
	),

	// ======================
	// FRONTEND
	// ======================
	build(
		"src/frontend/js/amway-product-page.js",
		"assets/frontend",
		"amway-product-page",
	),

	build(
		"src/frontend/js/amway-product-detail-page.js",
		"assets/frontend",
		"amway-product-detail-page",
	),
];
