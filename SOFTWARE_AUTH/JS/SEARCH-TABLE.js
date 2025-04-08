document.addEventListener("DOMContentLoaded", function () {
  function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll(`#${tableId} tbody tr`);

    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(filter) ? "" : "none";
    });
  }

  const projectSearch = document.getElementById("searchProject");
  const taskSearch = document.getElementById("searchTask");

  if (projectSearch) {
    projectSearch.addEventListener("input", function () {
      searchTable("searchProject", "PROJECT-TABLE");
    });
  }

  if (taskSearch) {
    taskSearch.addEventListener("input", function () {
      searchTable("searchTask", "TASK-TABLE");
    });
  }
});