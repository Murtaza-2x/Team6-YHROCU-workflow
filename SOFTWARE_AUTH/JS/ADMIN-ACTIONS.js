document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".edit-btn").forEach(button => {
      button.addEventListener("click", () => {
        const row = button.closest("tr");
  
        row.querySelectorAll("input[type='text'], input[type='email'], input[type='password'], select").forEach(input => {
          input.removeAttribute("readonly");
          input.removeAttribute("disabled");
        });
  
        row.querySelectorAll(".action-btn").forEach(btn => {
          btn.style.display = "inline-block";
        });
  
        button.style.display = "none";
      });
    });
  });