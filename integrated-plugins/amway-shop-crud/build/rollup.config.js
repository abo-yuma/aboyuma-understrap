/**
 * Rollup plugins
 * CommonJS style
 */
const resolve = require("@rollup/plugin-node-resolve").default;
const commonjs = require("@rollup/plugin-commonjs");
const terser = require("@rollup/plugin-terser").default;
const postcss = require("rollup-plugin-postcss");
const cssnano = require("cssnano");

/**
 * build helper
 */
function build(entry, outputDir, name) {
	return {
		input: entry,

		output: {
			file: `${outputDir}/${name}.min.js`,
			format: "iife",
		},

		plugins: [
			resolve(),
			commonjs(),

			postcss({
				extensions: [".css", ".scss"],

				extract: `${name}.min.css`,

				// minify CSS
				minimize: true,

				plugins: [cssnano()],
			}),

			// minify JS
			terser(),
		],
	};
}

module.exports = [
	// ======================
	// ADMIN
	// ======================
	build("src/admin/js/amway-shop-add.js", "assets/admin", "amway-shop-add"),

	build("src/admin/js/amway-shop-edit.js", "assets/admin", "amway-shop-edit"),

	build(
		"src/admin/js/amway-shop-delete.js",
		"assets/admin",
		"amway-shop-delete",
	),

	// Shop Media (WP Media Uploader)
	build("src/admin/js/amway-shop-media.js", "assets/admin", "amway-shop-media"),
];
