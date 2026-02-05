jQuery(document).ready(function ($) {
  $(".delete-link").on("click", function (e) {
    e.preventDefault();
    if (!confirm("Are you sure you want to delete this product?")) {
      return;
    }
    var id = $(this).data("id");
    $.post(
      deleteProduct.ajaxurl,
      {
        action: "delete_product",
        nonce: deleteProduct.delete_product_nonce,
        id: id,
      },
      function (response) {
        if (response.success) {
          location.reload();
        } else {
          alert("An error occurred while deleting the product.");
        }
      }
    );
  });
});
