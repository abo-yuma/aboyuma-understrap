jQuery(function ($) {
	/**
	 * Load product subcategories via AJAX.
	 *
	 * This function is responsible only for loading the subcategory
	 * options after a product category has been selected.
	 *
	 * `selectedSubcategoryId` is used when restoring the filter state
	 * from the URL, for example:
	 *
	 * /shop/?category=36&subcategory=37
	 *
	 * In that case, category 36 is loaded first, then subcategory 37
	 * is selected after the AJAX response has been inserted.
	 *
	 * `callback` is optional and is used when another operation needs
	 * to wait until the subcategory AJAX request has finished.
	 *
	 * This is important for browser Back/Forward:
	 *
	 * 1. Read category/subcategory/page from the URL.
	 * 2. Load subcategories.
	 * 3. Restore the selected subcategory.
	 * 4. Only then load products.
	 */
	function loadSubcategories(
		categoryId,
		selectedSubcategoryId = "",
		callback = null,
	) {
		const $subcategory = $("#product-subcategory");

		/**
		 * Disable the subcategory select while its options are loading.
		 *
		 * This prevents the user from selecting an incomplete list
		 * of subcategories while the AJAX request is in progress.
		 */
		$subcategory
			.prop("disabled", true)
			.html('<option value="">กำลังโหลด...</option>');

		/**
		 * If no category is selected, there is no reason to load
		 * subcategories.
		 *
		 * Reset the select and stop here.
		 */
		if (!categoryId) {
			$subcategory
				.html('<option value="">เลือกหมวดย่อย</option>')
				.prop("disabled", true);

			/**
			 * If a callback was provided, continue with the next step.
			 *
			 * This is mainly used by the URL/history restore flow.
			 */
			if (callback) {
				callback();
			}

			return;
		}

		/**
		 * Request subcategories belonging to the selected category.
		 */
		$.ajax({
			url: aboyumaWooCommerce.ajaxUrl,
			type: "POST",
			data: {
				action: "aboyuma_load_subcategories",
				nonce: aboyumaWooCommerce.nonce,
				category_id: categoryId,
			},

			/**
			 * Handle a successful AJAX request.
			 */
			success: function (response) {
				/**
				 * The request itself succeeded, but the server reported
				 * that the operation was not successful.
				 *
				 * Show an error state and stop.
				 */
				if (!response.success) {
					$subcategory
						.html('<option value="">ไม่พบหมวดย่อย</option>')
						.prop("disabled", true);

					/**
					 * Continue the callback chain if one was provided.
					 */
					if (callback) {
						callback();
					}

					return;
				}

				/**
				 * Replace the current subcategory options with the
				 * options returned by PHP.
				 *
				 * The first option is kept as the default "เลือกหมวดย่อย".
				 */
				$subcategory.html(
					'<option value="">เลือกหมวดย่อย</option>' + response.data.html,
				);

				/**
				 * Restore the subcategory from the URL.
				 *
				 * Example:
				 *
				 * URL:
				 * ?category=36&subcategory=37
				 *
				 * At this point the options for category 36 have already
				 * been inserted, so `.val(37)` can select the correct option.
				 */
				if (selectedSubcategoryId) {
					$subcategory.val(selectedSubcategoryId);
				}

				/**
				 * The subcategory options are now ready for the user.
				 */
				$subcategory.prop("disabled", false);

				/**
				 * IMPORTANT:
				 *
				 * If another operation is waiting for this AJAX request
				 * to finish, run it now.
				 *
				 * For example, browser history restoration can wait here
				 * before calling loadProducts().
				 */
				if (callback) {
					callback();
				}
			},

			/**
			 * Handle a network/server error.
			 */
			error: function () {
				$subcategory
					.html('<option value="">เกิดข้อผิดพลาด</option>')
					.prop("disabled", true);

				/**
				 * Continue the callback chain if one was provided.
				 */
				if (callback) {
					callback();
				}
			},
		});
	}

	/**
	 * Handle product category change.
	 *
	 * This event is triggered when the user manually changes
	 * the category select.
	 *
	 * We only load the corresponding subcategories here.
	 *
	 * We do NOT load products here because the user must first
	 * press the "ค้นหา" button to apply the filter.
	 */
	$("#product-category").on("change", function () {
		const categoryId = $(this).val();

		loadSubcategories(categoryId);
	});

	/**
	 * Load products via AJAX.
	 *
	 * This function is used after the page has already loaded,
	 * for example when:
	 *
	 * - The user clicks "ค้นหา".
	 * - The user clicks a pagination link.
	 * - The browser Back/Forward button changes the URL.
	 *
	 * IMPORTANT:
	 *
	 * This function is intentionally NOT called during the
	 * normal initial page load.
	 *
	 * The initial product query is handled by PHP / WooCommerce
	 * so that the correct products are already present in the
	 * first HTML response.
	 *
	 * This prevents:
	 *
	 * Wrong products → AJAX request → correct products
	 *
	 * which would cause a visible flash.
	 */
	function loadProducts(page = 1) {
		const categoryId = $("#product-category").val();
		const subcategoryId = $("#product-subcategory").val();

		const $results = $("#product-results");

		/**
		 * Give the user immediate feedback while the AJAX request
		 * is loading.
		 */
		$results.html("<p>กำลังโหลด...</p>");

		/**
		 * Request the filtered/paginated products from PHP.
		 */
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

			/**
			 * Handle a successful product request.
			 */
			success: function (response) {
				if (!response.success) {
					$results.html("<p>ไม่สามารถโหลดสินค้าได้</p>");
					return;
				}

				/**
				 * Replace the current product grid with the new results.
				 */
				$results.html(response.data.html);

				/**
				 * Replace the existing pagination with the pagination
				 * returned by PHP.
				 *
				 * If pagination does not currently exist but the response
				 * contains pagination, insert it after the product results.
				 */
				const $pagination = $("#product-pagination");

				if ($pagination.length) {
					$pagination.replaceWith(response.data.pagination);
				} else if (response.data.pagination) {
					$("#product-results").after(response.data.pagination);
				}

				/**
				 * Calculate the "Showing X–Y of Z results" text.
				 *
				 * `perPage` tells us how many products are displayed
				 * on each page.
				 *
				 * `total` is the total number of matching products.
				 */
				const perPage = response.data.per_page;
				const total = response.data.total;

				let from = 0;
				let to = 0;

				/**
				 * Only calculate the range when products exist.
				 *
				 * This prevents an empty result from displaying something
				 * like "Showing 1–0 of 0 results".
				 */
				if (total > 0) {
					from = (response.data.current_page - 1) * perPage + 1;

					to = Math.min(response.data.current_page * perPage, total);
				}

				/**
				 * Update the result count displayed above the product grid.
				 */
				$("#product-result-count").text(
					"Showing " + from + "–" + to + " of " + total + " results",
				);
			},

			/**
			 * Handle a network/server error.
			 */
			error: function () {
				$results.html("<p>เกิดข้อผิดพลาด</p>");
			},
		});
	}

	/**
	 * Restore product filters from the current URL.
	 *
	 * This function has TWO different modes:
	 *
	 * 1. Initial page load:
	 *
	 *    applyFilterFromURL()
	 *
	 *    The default `loadProduct = false` means:
	 *
	 *    - Restore the dropdowns.
	 *    - Do NOT load products via AJAX.
	 *
	 *    The products were already loaded correctly by PHP.
	 *
	 *
	 * 2. Browser Back/Forward:
	 *
	 *    applyFilterFromURL(true)
	 *
	 *    In this case:
	 *
	 *    - Restore the dropdowns.
	 *    - Load products according to the URL.
	 *
	 * The distinction is important because calling loadProducts()
	 * during initial page load would cause the same products to
	 * be loaded twice.
	 */
	function applyFilterFromURL(loadProduct = false) {
		const url = new URL(window.location.href);

		/**
		 * Read the current filter state from the URL.
		 *
		 * Example:
		 *
		 * /shop/?category=36&subcategory=37&page=2
		 *
		 * categoryId     = 36
		 * subcategoryId  = 37
		 * page           = 2
		 */
		const categoryId = url.searchParams.get("category") || "";
		const subcategoryId = url.searchParams.get("subcategory") || "";
		const page = parseInt(url.searchParams.get("page"), 10) || 1;

		/**
		 * Restore the selected category.
		 */
		$("#product-category").val(categoryId);

		if (categoryId) {
			/**
			 * Load the subcategories belonging to this category.
			 *
			 * `subcategoryId` tells loadSubcategories() which option
			 * should be selected after the AJAX response arrives.
			 *
			 * If `loadProduct` is true, wait until the subcategories
			 * are finished loading before loading products.
			 *
			 * This ordering is important because loadProducts()
			 * reads:
			 *
			 * $("#product-subcategory").val()
			 */
			loadSubcategories(categoryId, subcategoryId, function () {
				if (loadProduct) {
					loadProducts(page);
				}
			});
		} else {
			/**
			 * There is no category in the URL.
			 *
			 * Reset the subcategory select because it should not contain
			 * options belonging to a previously selected category.
			 */
			$("#product-subcategory")
				.html('<option value="">เลือกหมวดย่อย</option>')
				.prop("disabled", true);

			/**
			 * If this function was triggered by Back/Forward,
			 * reload the products using the page from the URL.
			 *
			 * Example:
			 *
			 * /shop/?page=4
			 *
			 * This will call:
			 *
			 * loadProducts(4)
			 *
			 * If there is no `page` parameter, `page` defaults to 1.
			 */
			if (loadProduct) {
				loadProducts(page);
			}
		}
	}

	/**
	 * Apply product filters.
	 *
	 * This runs when the user explicitly clicks the "ค้นหา" button.
	 *
	 * The URL is updated first so that the URL becomes the source
	 * of truth for the current filter state.
	 */
	$("#product-filter-submit").on("click", function () {
		const categoryId = $("#product-category").val();
		const subcategoryId = $("#product-subcategory").val();

		const url = new URL(window.location.href);

		/**
		 * A new filter always starts from page 1.
		 *
		 * Example:
		 *
		 * Current:
		 * /shop/?category=36&page=4
		 *
		 * After applying a new filter:
		 * /shop/?category=37
		 */
		url.searchParams.delete("page");

		/**
		 * Add or remove the category parameter.
		 */
		if (categoryId) {
			url.searchParams.set("category", categoryId);
		} else {
			url.searchParams.delete("category");
		}

		/**
		 * Add or remove the subcategory parameter.
		 */
		if (subcategoryId) {
			url.searchParams.set("subcategory", subcategoryId);
		} else {
			url.searchParams.delete("subcategory");
		}

		/**
		 * Change the URL without causing a full page reload.
		 *
		 * This creates a browser history entry, which allows
		 * Back/Forward to restore the previous filter state.
		 */
		history.pushState({}, "", url);

		/**
		 * Load the first page of the newly filtered products.
		 *
		 * This is AJAX because the page is already loaded.
		 */
		loadProducts(1);
	});

	/**
	 * Handle product pagination.
	 *
	 * Pagination is also handled through AJAX instead of a full
	 * page reload.
	 */
	$(document).on("click", "#product-pagination a", function (e) {
		e.preventDefault();

		/**
		 * Read the target page from the pagination link.
		 *
		 * Example:
		 *
		 * ?page=4
		 *
		 * becomes:
		 *
		 * page = 4
		 */
		const pageUrl = new URL($(this).attr("href"));
		const page = parseInt(pageUrl.searchParams.get("page"), 10) || 1;

		/**
		 * Start with the current URL so that existing filters
		 * are preserved.
		 *
		 * For example:
		 *
		 * /shop/?category=36&subcategory=37
		 *
		 * becomes:
		 *
		 * /shop/?category=36&subcategory=37&page=2
		 */
		const url = new URL(window.location.href);

		url.searchParams.set("page", page);

		/**
		 * Add this page change to browser history.
		 */
		history.pushState({}, "", url);

		/**
		 * Load the selected page via AJAX.
		 */
		loadProducts(page);
	});

	/**
	 * Restore filters and products when browser history changes.
	 *
	 * This event fires when the user uses:
	 *
	 * - Back
	 * - Forward
	 *
	 * We pass `true` because, unlike the initial page load,
	 * the product grid must also be updated to match the new URL.
	 *
	 * If the URL contains a subcategory, applyFilterFromURL()
	 * waits for loadSubcategories() to finish before calling
	 * loadProducts().
	 */
	$(window).on("popstate", function () {
		applyFilterFromURL(true);
	});

	/**
	 * Restore filters on initial page load.
	 *
	 * IMPORTANT:
	 *
	 * Do NOT change this to:
	 *
	 * applyFilterFromURL(true);
	 *
	 * because that would cause loadProducts() to run again.
	 *
	 * The initial product query is already handled by PHP.
	 *
	 * JavaScript only needs to restore the dropdown state here.
	 */
	applyFilterFromURL();
});
