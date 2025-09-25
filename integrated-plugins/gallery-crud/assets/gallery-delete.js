jQuery(document).ready(function ($) {
	$(".gallery-delete-button").on("click", function (e) {
		e.preventDefault();
		if (!confirm("Are you sure you want to delete this gallery item?")) {
			return;
		}

		var id = $(this).data("id");

		$.post(
			deleteGallery.ajaxurl,
			{
				action: "delete_gallery",
				nonce: deleteGallery.delete_gallery_nonce,
				id: id,
			},
			function (response) {
				if (response.success) {
					location.reload();
				} else {
					alert("An error occurred while deleting the gallery.");
				}
			}
		);
	});
});
