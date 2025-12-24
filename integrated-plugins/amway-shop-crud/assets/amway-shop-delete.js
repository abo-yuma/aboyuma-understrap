jQuery(document).ready(function ($) {
  $(".amway-shop-delete-button").on("click", function (e) {
    e.preventDefault();
    if (!confirm("Are you sure you want to delete this amway shop item?")) {
      return;
    }

    var id = $(this).data("id");

    $.post(
      deleteAmwayShop.ajaxurl,
      {
        action: "delete_amway_shop",
        nonce: deleteAmwayShop.delete_amway_shop_nonce,
        id: id,
      },
      function (response) {
        if (response.success) {
          location.reload();
        } else {
          alert("An error occurred while deleting the amway shop.");
        }
      }
    );
  });
});
