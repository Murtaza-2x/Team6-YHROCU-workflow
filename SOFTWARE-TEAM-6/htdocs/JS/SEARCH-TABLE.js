// Wait until the DOM is fully loaded
$(function () {
  // Cache selectors to improve performance and readability
  const $searchInput = $("#searchInput");
  const $filterButton = $("#filterButton");
  const $taskRows = $("#TASK-TABLE tbody tr");

  /**
   * This function handles the filtering logic.
   * It takes the user’s input from the search bar,
   * converts it to lowercase (for case-insensitive search),
   * and compares it to the text content of each table row.
   * Rows that include the search term will be shown, others hidden.
   */
  function runSearch() {
    const searchTerm = $searchInput.val().toLowerCase().trim();

    $taskRows.each(function () {
      const rowText = $(this).text().toLowerCase();
      $(this).toggle(rowText.includes(searchTerm));
    });
  }

  /**
   * When the user clicks the "Filter" button,
   * prevent the default button behavior and run the search.
   */
  $filterButton.on("click", function (e) {
    e.preventDefault();
    runSearch();
  });

  /**
   * When the user presses Enter while typing in the search input,
   * prevent the form from submitting or the page from reloading,
   * and trigger the search instead.
   */
  $searchInput.on("keydown", function (e) {
    if (e.key === "Enter") {
      e.preventDefault();
      runSearch();
    }
  });
});
