
jQuery(document).ready(function ($) {
  $("#delete-brand-link").on("click", function (e) {
    e.preventDefault();
    if (!confirm("Are you sure you want to delete this brand?")) {
      return;
    }
    var id = $(this).data("id");
    $.post(
      deleteProduct.ajaxurl,
      {
        action: "delete_product_brand",
        nonce: deleteProductBrand.delete_product_brand_nonce,
        id: id,
      },
      function (response) {
        if (response.success) {
          location.reload();
        } else {
          alert("An error occurred while deleting the brand.");
        }
      }
    );
  });
});
