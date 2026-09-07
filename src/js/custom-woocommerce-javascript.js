jQuery(function ($) {
	$("#product-category").on("change", function () {
		const categoryId = $(this).val();
		const $subcategory = $("#product-subcategory");

		$subcategory
			.prop("disabled", true)
			.html('<option value="">กำลังโหลด...</option>');

		if (!categoryId) {
			$subcategory
				.html('<option value="">เลือกหมวดย่อย</option>')
				.prop("disabled", true);

			return;
		}

		$.ajax({
			url: aboyumaWooCommerce.ajaxUrl,
			type: "POST",
			data: {
				action: "aboyuma_load_subcategories",
				nonce: aboyumaWooCommerce.nonce,
				category_id: categoryId,
			},
			success: function (response) {
				if (!response.success) {
					$subcategory.html('<option value="">ไม่พบหมวดย่อย</option>');

					return;
				}

				$subcategory.html(
					'<option value="">เลือกหมวดย่อย</option>' + response.data.html,
				);

				$subcategory.prop("disabled", false);
			},
			error: function () {
				$subcategory.html('<option value="">เกิดข้อผิดพลาด</option>');
			},
		});
	});
});
