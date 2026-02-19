import "../css/amway-shop-add.scss";

jQuery(document).ready(function ($) {
	// Preview for the main shop image
	$("#shop_image").on("change", function () {
		const file = this.files[0];
		const preview = $("#shop_image_preview");
		if (file) {
			const reader = new FileReader();
			reader.onload = function (e) {
				preview.attr("src", e.target.result); // Set the image preview source
				preview.show(); // Show the image
			};
			reader.readAsDataURL(file); // Read the file as a data URL
		} else {
			preview.attr("src", ""); // Clear the image preview
			preview.hide(); // Hide the image
		}
	});

	$("#add-transport").on("click", function () {
		const rowHtml = `
                    <div class="transport-row">
                        <select name="transport[type][]">
                            <option value="">-- Type --</option>
                            <option value="BTS">BTS</option>
                            <option value="MRT">MRT</option>
                        </select>

                        <input type="text" name="transport[line][]" placeholder="สาย (เช่น สีน้ำเงิน)">
                        <input type="text" name="transport[station][]" placeholder="ชื่อสถานี">

                        <button type="button" class="button remove-transport">ลบ</button>
                    </div>
                `;
		$("#transport-wrapper").append(rowHtml);
	});

	$(document).on("click", ".remove-transport", function () {
		if ($(".transport-row").length > 1) {
			$(this).closest(".transport-row").remove();
		}
	});
});
