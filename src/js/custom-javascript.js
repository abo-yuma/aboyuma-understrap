// Add your JS customizations here

// Top-level import: brings in only the Tooltip and Popover modules from your bootstrap.js file
// This allows you to use them in this script without relying on any global variables.
import { Tooltip, Popover } from "./bootstrap"; // ✅ must be at the very top of the file

// Wrap everything in an IIFE (Immediately Invoked Function Expression) and pass in jQuery ($)
// This keeps your code scoped and avoids polluting the global namespace
(function ($) {
	"use strict"; // Enable strict mode for safer JavaScript

	// Initialize all Bootstrap tooltips on the page
	// Select all elements with the attribute data-bs-toggle="tooltip"
	document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => {
		// Create a new Tooltip instance for each element
		new Tooltip(el);
	});

	// Initialize all Bootstrap popovers on the page
	// Select all elements with the attribute data-bs-toggle="popover"
	document.querySelectorAll('[data-bs-toggle="popover"]').forEach((el) => {
		// Create a new Popover instance for each element
		new Popover(el);
	});

	// Scroll To Top Button
	var offset = 200;
	var speed = 50;
	$(window).on("scroll", function () {
		if ($(this).scrollTop() > offset) {
			$(".scroll-to-top-btn").addClass("show");
		} else {
			$(".scroll-to-top-btn").removeClass("show");
		}
	});

	$(".scroll-to-top-btn").click(function (e) {
		$("html, body").animate({ scrollTop: 0 }, speed);
	});

	// Bootstrap 5 Dark Mode Toggle
	// Check local storage for dark mode preference
	if (localStorage.getItem("darkMode") === "enabled") {
		$("html").attr("data-bs-theme", "dark");
		$("#dark-mode-switch").prop("checked", true);
	}

	$("#dark-mode-switch").on("change", function () {
		if ($(this).is(":checked")) {
			$("html").attr("data-bs-theme", "dark");
			localStorage.setItem("darkMode", "enabled");
		} else {
			$("html").attr("data-bs-theme", "light");
			localStorage.setItem("darkMode", "disabled");
		}
	});

	/**
	 * Navbar Fixed Top On Scroll
	 */
	var navbarPosition = $("header").height();
	var navbar = $("#navbar-main");

	// Check if admin bar exists and get its height if present
	var hasAdminBar = $("#wpadminbar").length > 0;
	var adminBarHeight = hasAdminBar ? $("#wpadminbar").outerHeight() : 0;
	var desktopBreakpoint = 768; // Define desktop breakpoint

	// TODO: https://stackoverflow.com/questions/31162606/how-to-detect-screen-size-for-responsive-web-design
	$(window).on("scroll", function () {
		console.log("window).on(scroll");
		if ($(this).scrollTop() > navbarPosition) {
			console.log("scrollTop() > navbarPosition");
			navbar.removeClass("container").addClass("container-fluid"); // Change to container-fluid
			if (window.innerWidth > desktopBreakpoint) {
				console.log("window.innerWidth > desktopBreakpoint");
				// Apply offset only on desktop
				navbar.css({
					position: "fixed",
					top: hasAdminBar ? adminBarHeight + "px" : "0", // Apply top offset only if admin bar exists
				});
			} else {
				navbar.css({
					position: "fixed",
					top: "0",
				});
			}
		} else {
			navbar.removeClass("container-fluid").addClass("container"); // Change back to container
			navbar.css({
				position: "static", // Revert to static
				top: "", // Reset top when navbar is not fixed
			});
		}
	});

	// TODO: Fix lightbox to not apply to product detail thumbnail
	// Implement jQuery lightbox sciprt
	// Wrap each image in .entry-content with an anchor tag linking to itself
	// $(".entry-content img").each(function () {
	//   var imgSrc = $(this).attr("src");
	//   $(this).wrap(
	//     '<a href="' + imgSrc + '" data-lightbox="gallery" data-title=""></a>'
	//   );
	// });

	// Add Boostrap form style to form element in widget area.
	$(".widget-area input").addClass("form-control");
	$(".widget-area select").addClass("form-select");
	// Force left sidebar widget sitcky on scroll
	// Ref: https://stackoverflow.com/questions/52062163/how-to-make-a-sidebar-sticky-when-scrolling-between-header-and-footer-with-jquer

	// =======================
	// Home marquee
	// =======================
	const $marquee = $(".home-marquee-content");
	if (!$marquee.length) return;

	// Duration is handled via data-attribute in the HTML markup.

	$marquee.marquee({
		gap: 60,
		delayBeforeStart: 0,
		direction: "left",
		duplicated: true,
		pauseOnHover: true,
		startVisible: true,
	});
})(jQuery); // Pass in jQuery to the IIFE so $ can be used safely inside
