$(document).ready(function () {
  const $searchInput = $("#searchInput");
  const $filterButton = $("#filterButton");
  const $taskRows = $("#TASK-TABLE tbody tr");

  /**
   * runSearch:
   * Reads the current search input, converts it to lowercase,
   * and toggles the visibility of each row based on whether its text contains the search term.
   */
  function runSearch() {
    const searchTerm = $searchInput.val().toLowerCase().trim();
    $taskRows.each(function () {
      const rowText = $(this).text().toLowerCase();
      $(this).toggle(rowText.includes(searchTerm));
    });
  }

  $searchInput.on("keyup", runSearch);

  $filterButton.on("click", function (e) {
    e.preventDefault();
    runSearch();
  });
});
