import "../css/amway-product-detail-page.scss";

jQuery(function ($) {
	// Open fancybox from current active thumbnail
	function openFancyboxFromCurrentIndex() {
		const $thumbnails = $(".thumbnail");
		let currentIndex = 0;

		$thumbnails.each(function (index) {
			if ($(this).hasClass("active")) {
				currentIndex = index;
			}
		});

		Fancybox.show(
			$thumbnails
				.map(function () {
					return {
						src: $(this).attr("src"),
						type: "image",
						caption: $(this).attr("alt") || "",
					};
				})
				.get(),
			{
				startIndex: currentIndex,
				Thumbs: { autoStart: true },
				Toolbar: {
					display: ["zoom", "slideshow", "thumbs", "close"],
				},
			},
		);
	}

	// Thumbnail click → update preview
	function displayPreviewImage($thumb) {
		$("#previewImage").attr("src", $thumb.attr("src"));

		$(".thumbnail").removeClass("active");
		$thumb.addClass("active");
	}

	// ===== Event bindings =====

	// Main preview click
	$("#previewFancyboxLink").on("click", function (e) {
		e.preventDefault();
		openFancyboxFromCurrentIndex();
	});

	// Thumbnail click
	$(".thumbnail").on("click", function () {
		displayPreviewImage($(this));
	});
});
