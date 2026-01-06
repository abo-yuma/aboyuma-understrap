jQuery(document).ready(function ($) {
	const ajaxUrl = productPageData.ajax_url;
	const nonce = productPageData.nonce;

	console.log("ajaxUrl:", ajaxUrl);
	console.log("nonce:", nonce);

	// Category selection
	$("#parent_category").on("change", function () {
		var parentId = $(this).val();
		var $subSelect = $("#sub_category");

		if (parentId === "") {
			// Clear subcategory
			$subSelect.html('<option value="">-- ทั้งหมด --</option>');
			return;
		}
		$subSelect.html('<option value="">Loading...</option>');

		$.ajax({
			url: ajaxUrl,
			method: "GET",
			data: {
				action: "get_subcategories",
				parent_id: parentId,
			},
			dataType: "json",
			success: function (data) {
				var options = '<option value="">-- ทั้งหมด --</option>';
				if (data.length > 0) {
					$.each(data, function (index, subcat) {
						options +=
							'<option value="' +
							subcat.id +
							'">' +
							subcat.category_name +
							"</option>";
					});
				} else {
					options = '<option value="">-- ไม่พบรายการ --</option>';
				}
				$subSelect.html(options);
			},
			error: function () {
				$subSelect.html(
					'<option value="">Failed to load subcategories</option>'
				);
			},
		});
	});

	const isDesktop = window.matchMedia("(min-width: 769px)").matches;
	// Apply only for iPad and PC screen
	let columnDefs = [];
	if (isDesktop) {
		columnDefs = [
			{
				width: "25%",
				targets: [0],
			},
			{
				width: "30%",
				targets: [1],
			},
			{
				width: "30%",
				targets: [2],
			},
			{
				width: "15%",
				targets: [3],
			},
		];
	}
	var table = $("#aboyuma-product-table").DataTable({
		fixedColumns: {
			start: 1,
			end: 1,
		},
		paging: true,
		pageLength: 10,
		scrollCollapse: true,
		scrollX: true,
		pageLength: 10,
		searching: true,
		autoWidth: false,
		responsive: true,
		columnDefs: columnDefs,
		stateSave: true,
		ajax: {
			url: ajaxUrl,
			dataSrc: "", // Required when response is a plain array
			type: "GET",
			data: function (d) {
				d.action = "filter_products";
				d.parent_category = $("#parent_category").val();
				d.sub_category = $("#sub_category").val();
				d.price_min = $("#filter-price-min").val();
				d.price_max = $("#filter-price-max").val();
				d._ajax_nonce = nonce;
			},
			dataSrc: function (json) {
				const count = Array.isArray(json) ? json.length : 0;
				// Update the prepared div with the success message
				$("#filter-message-area").html(`
						<div class="alert alert-success alert-dismissible fade show p-2" role="alert">
						<div class="d-flex justify-content-between align-items-center w-100">
							<small class="mb-0 flex-grow-1">ค้นหาสำเร็จ ${count} รายการ</small>
							<span type="button" data-bs-dismiss="alert" aria-label="Close"
							class="text-muted" style="cursor: pointer; font-size: 0.9rem;">
							<i class="fa fa-times"></i>
							</span>
						</div>
						</div>
					`);
				return json; // Pass data to DataTables
			},
		},
	});

	$("#apply-filters").on("click", function (e) {
		e.preventDefault(); // prevents the form from submitting
		table.ajax.reload();
	});
});
