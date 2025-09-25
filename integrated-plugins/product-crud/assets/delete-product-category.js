jQuery(document).ready(function ($) {
  $("#aboyuma-product-category-table").on(
    "click",
    ".delete-category-link",
    function (e) {
      e.preventDefault();
      if (!confirm("Are you sure you want to delete this category?")) {
        return;
      }

      var id = $(this).data("id");

      $.post(
        deleteProductCategory.ajaxurl,
        {
          action: "delete_product_category",
          nonce: deleteProductCategory.delete_product_category_nonce,
          id: id,
        },
        function (response) {
          if (response.success) {
            location.reload();
          } else {
            alert(
              response.data || "An error occurred while deleting the category."
            );
          }
        }
      );
    }
  );
});
