jQuery(document).ready(function ($) {
	$("#aboyuma-product-category-table").on(
		"click",
		".delete-category-link",
		function (e) {
			e.preventDefault();

			if (!confirm("Are you sure you want to delete this category?")) {
				return;
			}

			var button = $(this);
			var row = button.closest("tr");
			var id = button.data("id");

			$.post(
				deleteProductCategory.ajaxurl,
				{
					action: "delete_product_category",
					nonce: deleteProductCategory.delete_product_category_nonce,
					id: id,
				},
				function (response) {
					if (response.success) {
						row.fadeOut(200, function () {
							$(this).remove();
						});
					} else {
						alert(
							response.data || "An error occurred while deleting the category.",
						);
					}
				},
			);
		},
	);
});
