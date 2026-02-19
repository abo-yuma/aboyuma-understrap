jQuery(document).on("click", ".amway-shop-delete-button", function (e) {
	e.preventDefault();

	if (!confirm("Are you sure you want to delete this amway shop item?")) {
		return;
	}

	var button = jQuery(this);
	var row = button.closest("tr");
	var id = button.data("id");

	jQuery.post(
		deleteShop.ajaxurl,
		{
			action: "delete_amway_shop",
			nonce: deleteShop.nonce,
			id: id,
		},
		function (response) {
			if (response.success) {
				row.fadeOut(200, function () {
					jQuery(this).remove();
				});
			} else {
				alert(response.data || "Delete failed");
			}
		},
	);
});
