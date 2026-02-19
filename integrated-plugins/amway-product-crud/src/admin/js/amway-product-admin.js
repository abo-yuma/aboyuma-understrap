import "../css/amway-product-admin.scss";

jQuery(document).ready(function ($) {
	// ==============================
	// MULTI SELECT HACK
	// ==============================
	$(".related-product-select-multiple option").on("mousedown", function (e) {
		this.selected = !this.selected;
		e.preventDefault();
	});

	// ==============================
	// CATEGORY AJAX LOAD
	// ==============================
	$("#parent_category").on("change", function () {
		var parentId = $(this).val();
		var $subSelect = $("#product_subcategory");
		$subSelect.html('<option value="">Loading...</option>');

		$.ajax({
			url: ajaxurl,
			method: "GET",
			data: {
				action: "get_subcategories",
				parent_id: parentId,
			},
			dataType: "json",
			success: function (data) {
				var options = '<option value="">-- Select Subcategory --</option>';

				if (data.length > 0) {
					$.each(data, function (index, subcat) {
						options += `<option value="${subcat.id}">${subcat.category_name}</option>`;
					});
				} else {
					options = '<option value="">-- No Subcategories Found --</option>';
				}

				$subSelect.html(options);
			},
			error: function () {
				$subSelect.html(
					'<option value="">Failed to load subcategories</option>',
				);
			},
		});
	});

	// ==============================
	// VIDEO ROW HANDLING
	// ==============================
	let videoIndex = $("#video-form-rows tr").length;

	$("#add-video-row").on("click", function () {
		const videoForm = `
		<tr>
			<td><input type="url" name="video_datas[${videoIndex}][video_url]" style="width: 300px;"></td>
			<td><input type="text" name="video_datas[${videoIndex}][video_comment]" maxlength="255" style="width: 200px;" placeholder="Video comment.."></td>
			<td><button type="button" class="button remove-row">✕</button></td>
		</tr>
		`;

		$("#video-form-rows").append(videoForm);
		videoIndex++;
	});

	$("#video-form-table").on("click", ".remove-row", function () {
		$(this).closest("tr").remove();

		$("#video-form-rows tr").each(function (index) {
			$(this)
				.find('input[name*="[video_url]"]')
				.attr("name", `video_datas[${index}][video_url]`);
			$(this)
				.find('input[name*="[video_comment]"]')
				.attr("name", `video_datas[${index}][video_comment]`);
		});

		videoIndex = $("#video-form-rows tr").length;
	});
});
