jQuery(document).ready(function ($) {
	/**
	 * MAIN IMAGE
	 */
	let mainFrame;

	$("#select-main-image").on("click", function (e) {
		e.preventDefault();

		if (mainFrame) {
			mainFrame.open();
			return;
		}

		mainFrame = wp.media({
			title: "Select Main Image",
			button: { text: "Use this image" },
			multiple: false,
		});

		mainFrame.on("select", function () {
			const attachment = mainFrame.state().get("selection").first().toJSON();

			const imageUrl =
				attachment.sizes && attachment.sizes.medium
					? attachment.sizes.medium.url
					: attachment.url;

			$("#main_image_id").val(attachment.id);
			$("#main-image-preview").attr("src", imageUrl);
			$("#main-image-wrapper").show();
		});

		mainFrame.open();
	});

	/**
	 * GALLERY
	 */
	let galleryFrame;

	$("#add-gallery-images").on("click", function (e) {
		e.preventDefault();

		if (galleryFrame) {
			galleryFrame.open();
			return;
		}

		galleryFrame = wp.media({
			title: "Select Gallery Images",
			button: { text: "Add images" },
			multiple: true,
		});

		galleryFrame.on("select", function () {
			const attachments = galleryFrame.state().get("selection").toJSON();

			attachments.forEach(function (attachment) {
				if (
					$('#gallery-container input[value="' + attachment.id + '"]').length
				) {
					return;
				}

				const imageUrl =
					attachment.sizes && attachment.sizes.thumbnail
						? attachment.sizes.thumbnail.url
						: attachment.url;

				const html = `
				<div class="gallery-item">
					<span class="remove-image">&times;</span>
					<img src="${imageUrl}">
					<input type="hidden" name="gallery_image_ids[]" value="${attachment.id}">
				</div>
			`;

				$("#gallery-container").append(html);
			});
		});

		galleryFrame.open();
	});

	/**
	 * REMOVE
	 */
	$(document).on("click", ".remove-image", function () {
		$(this).closest(".gallery-item").remove();
	});

	/**
	 * SORTABLE
	 */
	$("#gallery-container").sortable({
		items: ".gallery-item",
		cursor: "move",
		opacity: 0.8,
	});
});
