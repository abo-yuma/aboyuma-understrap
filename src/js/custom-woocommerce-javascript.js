jQuery(function ($) {
	/**
	 * Load product subcategories via AJAX.
	 */
	function loadSubcategories(
		categoryId,
		selectedSubcategoryId = "",
		callback = null,
	) {
		const $subcategory = $("#product-subcategory");

		$subcategory
			.prop("disabled", true)
			.html('<option value="">กำลังโหลด...</option>');

		if (!categoryId) {
			$subcategory
				.html('<option value="">เลือกหมวดย่อย</option>')
				.prop("disabled", true);

			if (callback) {
				callback();
			}

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
					$subcategory
						.html('<option value="">ไม่พบหมวดย่อย</option>')
						.prop("disabled", true);

					if (callback) {
						callback();
					}

					return;
				}

				$subcategory.html(
					'<option value="">เลือกหมวดย่อย</option>' + response.data.html,
				);

				// Restore selected subcategory.
				if (selectedSubcategoryId) {
					$subcategory.val(selectedSubcategoryId);
				}

				$subcategory.prop("disabled", false);

				if (callback) {
					callback();
				}
			},
			error: function () {
				$subcategory
					.html('<option value="">เกิดข้อผิดพลาด</option>')
					.prop("disabled", true);

				if (callback) {
					callback();
				}
			},
		});
	}

	/**
	 * Handle product category change.
	 */
	$("#product-category").on("change", function () {
		const categoryId = $(this).val();

		loadSubcategories(categoryId);
	});

	/**
	 * Load products via AJAX.
	 */
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
				const total = response.data.total;

				let from = 0;
				let to = 0;

				if (total > 0) {
					from = (response.data.current_page - 1) * perPage + 1;

					to = Math.min(response.data.current_page * perPage, total);
				}

				$("#product-result-count").text(
					"Showing " + from + "–" + to + " of " + total + " results",
				);
			},
			error: function () {
				$results.html("<p>เกิดข้อผิดพลาด</p>");
			},
		});
	}

	/**
	 * Restore product filters and pagination from URL.
	 */
	function applyFilterFromURL() {
		const url = new URL(window.location.href);

		const categoryId = url.searchParams.get("category") || "";
		const subcategoryId = url.searchParams.get("subcategory") || "";

		// Restore category.
		$("#product-category").val(categoryId);

		if (categoryId) {
			// Load subcategories and restore the selected subcategory.
			loadSubcategories(categoryId, subcategoryId);
		} else {
			// Reset subcategory when no category is selected.
			$("#product-subcategory")
				.html('<option value="">เลือกหมวดย่อย</option>')
				.prop("disabled", true);

		}
	}

	/**
	 * Apply product filters.
	 */
	$("#product-filter-submit").on("click", function () {
		const categoryId = $("#product-category").val();
		const subcategoryId = $("#product-subcategory").val();

		const url = new URL(window.location.href);

		// Reset pagination when applying a new filter.
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

	/**
	 * Handle product pagination.
	 */
	$(document).on("click", "#product-pagination a", function (e) {
		e.preventDefault();

		const pageUrl = new URL($(this).attr("href"));
		const page = parseInt(pageUrl.searchParams.get("page"), 10) || 1;

		const url = new URL(window.location.href);

		url.searchParams.set("page", page);

		history.pushState({}, "", url);

		loadProducts(page);
	});

	/**
	 * Restore filters when browser history changes.
	 */
	$(window).on("popstate", function () {
		applyFilterFromURL();
	});

	/**
	 * Restore filters on initial page load.
	 */
	applyFilterFromURL();
});
