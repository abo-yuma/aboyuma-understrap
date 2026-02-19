jQuery(document).ready(function ($) {
	var mediaUploader;

	$("#upload_shop_image").click(function (e) {
		e.preventDefault();

		if (mediaUploader) {
			mediaUploader.open();
			return;
		}

		mediaUploader = wp.media({
			title: "Select Shop Image",
			button: {
				text: "Use this image",
			},
			multiple: false,
		});

		mediaUploader.on("select", function () {
			var attachment = mediaUploader.state().get("selection").first().toJSON();

			$("#shop_image_id").val(attachment.id);

			$("#shop_image_preview").html(
				'<img src="' +
					attachment.sizes.medium.url +
					'" style="max-width:200px;" />',
			);
		});

		mediaUploader.open();
	});
});
