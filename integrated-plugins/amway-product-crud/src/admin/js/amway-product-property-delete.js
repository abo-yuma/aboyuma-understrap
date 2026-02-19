jQuery(document).on("click", ".product-property-delete-button", function (e) {
	e.preventDefault();

	if (!confirm("Are you sure you want to delete this property?")) {
		return;
	}

	var button = jQuery(this);
	var row = button.closest("tr");
	var id = button.data("id");

	jQuery.post(
		deleteProperty.ajaxurl,
		{
			action: "delete_amway_product_property",
			nonce: deleteProperty.nonce,
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
