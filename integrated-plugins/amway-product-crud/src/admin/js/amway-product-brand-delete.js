jQuery(document).ready(function ($) {
	$("#aboyuma-product-brand-table").on(
		"click",
		".delete-brand-link",
		function (e) {
			e.preventDefault();

			if (!confirm("Are you sure you want to delete this brand?")) {
				return;
			}

			var button = $(this);
			var row = button.closest("tr");
			var id = button.data("id");

			$.post(
				deleteProductBrand.ajaxurl,
				{
					action: "delete_product_brand",
					nonce: deleteProductBrand.delete_product_brand_nonce,
					id: id,
				},
				function (response) {
					if (response.success) {
						row.fadeOut(200, function () {
							$(this).remove();
						});
					} else {
						alert(
							response.data || "An error occurred while deleting the brand.",
						);
					}
				},
			);
		},
	);
});
