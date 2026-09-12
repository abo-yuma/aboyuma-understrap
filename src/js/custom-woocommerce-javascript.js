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

	function loadProducts(page = 1) {
		const categoryId = $("#product-category").val();
		const subcategoryId = $("#product-subcategory").val();

		const $results = $("#product-results");

		$results.html("<p>กำลังโหลด...</p>");

		$.ajax({
			url: aboyumaWooCommerce.ajaxUrl,
			type: "POST",
			data: {
				action: "aboyuma_load_products",
				nonce: aboyumaWooCommerce.nonce,
				category_id: categoryId,
				subcategory_id: subcategoryId,
				page: page,
			},
			success: function (response) {
				if (!response.success) {
					$results.html("<p>ไม่สามารถโหลดสินค้าได้</p>");
					return;
				}

				$results.html(response.data.html);
				const $pagination = $("#product-pagination");

				if ($pagination.length) {
					$pagination.replaceWith(response.data.pagination);
				} else if (response.data.pagination) {
					$("#product-results").after(response.data.pagination);
				}

				const perPage = response.data.per_page;

				const from = (response.data.current_page - 1) * perPage + 1;

				const to = Math.min(
					response.data.current_page * perPage,
					response.data.total,
				);

				$("#product-result-count").text(
					"Showing " +
						from +
						"–" +
						to +
						" of " +
						response.data.total +
						" results",
				);
			},
			error: function () {
				$results.html("<p>เกิดข้อผิดพลาด</p>");
			},
		});
	}

	$("#product-filter-submit").on("click", function () {
		const categoryId = $("#product-category").val();
		const subcategoryId = $("#product-subcategory").val();

		const url = new URL(window.location.href);

		url.searchParams.delete("page");

		if (categoryId) {
			url.searchParams.set("category", categoryId);
		} else {
			url.searchParams.delete("category");
		}

		if (subcategoryId) {
			url.searchParams.set("subcategory", subcategoryId);
		} else {
			url.searchParams.delete("subcategory");
		}

		history.pushState({}, "", url);

		loadProducts(1);
	});

	$(document).on("click", "#product-pagination a", function (e) {
		e.preventDefault();

		const pageUrl = new URL($(this).attr("href"));
		const page = parseInt(pageUrl.searchParams.get("page"), 10) || 1;

		const url = new URL(window.location.href);

		url.searchParams.set("page", page);

		history.pushState({}, "", url);

		loadProducts(page);
	});
	$(window).on("popstate", function () {
		const url = new URL(window.location.href);
		const page = parseInt(url.searchParams.get("page"), 10) || 1;

		loadProducts(page);
	});
});
