// Add your JS customizations here
(function ($) {
	("use strict");

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
		$("main").attr("data-bs-theme", "dark");
		$("#aboyuma-author-box .card-body").addClass("dark-mode");
		$("#dark-mode-switch").prop("checked", true);
	}
	$("#dark-mode-switch").on("change", function () {
		if ($(this).is(":checked")) {
			$("main").attr("data-bs-theme", "dark");
			$("#aboyuma-author-box .card-body").addClass("dark-mode");
			localStorage.setItem("darkMode", "enabled");
		} else {
			$("main").attr("data-bs-theme", "light");
			$("#aboyuma-author-box .card-body").removeClass("dark-mode");
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
		if ($(this).scrollTop() > navbarPosition) {
			navbar.removeClass("container").addClass("container-fluid"); // Change to container-fluid
			if (window.innerWidth > desktopBreakpoint) {
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

	// Fix duration slow on mobile screen bug
	var duration = window.innerWidth < 768 ? 5000 : 15000;
	$("#news-ticker .marquee-content").marquee({
		duration: duration,
		gap: 50,
		delayBeforeStart: 0,
		direction: "left",
		duplicated: false,
	});

	// Add Boostrap form style to form element in widget area.
	$(".widget-area input").addClass("form-control");
	$(".widget-area select").addClass("form-select");
	// Force left sidebar widget sitcky on scroll
	// Ref: https://stackoverflow.com/questions/52062163/how-to-make-a-sidebar-sticky-when-scrolling-between-header-and-footer-with-jquer
})(jQuery);
