document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".edit-btn").forEach(button => {
    button.addEventListener("click", () => {
      const row = button.closest("tr");

      // Enable inputs
      row.querySelectorAll("input[type='text'], input[type='email'], input[type='password']").forEach(input => {
        input.removeAttribute("readonly");
      });

      row.querySelectorAll("select").forEach(select => {
        if (select.dataset.editable === "true") {
          select.removeAttribute("disabled");
        }
      });

      // Show Save / other buttons
      row.querySelectorAll(".action-btn").forEach(btn => {
        btn.style.display = "inline-block";
      });

      // Hide Edit
      button.style.display = "none";
    });
  });
});
