jQuery(document).ready(function ($) {
  $(".news-delete-button").on("click", function (e) {
    e.preventDefault();
    if (!confirm("Are you sure you want to delete this news item?")) {
      return;
    }

    var id = $(this).data("id");

    $.post(
      deleteNews.ajaxurl,
      {
        action: "delete_news",
        nonce: deleteNews.delete_news_nonce,
        id: id,
      },
      function (response) {
        if (response.success) {
          location.reload();
        } else {
          alert("An error occurred while deleting the news.");
        }
      }
    );
  });
});
