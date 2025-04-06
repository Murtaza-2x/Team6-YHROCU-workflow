/**
 * @jest-environment jsdom
 */
require("../JS/TOGGLE-DROPDOWN"); 

describe("Dropdown toggle functionality", () => {
  let toggleBtn, dropdown;

  beforeEach(() => {
    document.body.innerHTML = `
      <div class="ACTION-DROPDOWN">
        <button class="ACTION-DROPDOWN-TOGGLE">Toggle</button>
      </div>
      <div class="ACTION-DROPDOWN">
        <button class="ACTION-DROPDOWN-TOGGLE">Toggle</button>
      </div>
    `;
    document.dispatchEvent(new Event("DOMContentLoaded"));

    toggleBtn = document.querySelectorAll(".ACTION-DROPDOWN-TOGGLE")[0];
    dropdown = toggleBtn.closest(".ACTION-DROPDOWN");
  });

  it("opens the dropdown when toggled", () => {
    toggleBtn.click();
    expect(dropdown.classList.contains("active")).toBe(true);
  });

  it("closes other dropdowns when one is opened", () => {
    const [first, second] = document.querySelectorAll(".ACTION-DROPDOWN");
    first.classList.add("active");
    const secondToggle = second.querySelector(".ACTION-DROPDOWN-TOGGLE");

    secondToggle.click();

    expect(first.classList.contains("active")).toBe(false);
    expect(second.classList.contains("active")).toBe(true);
  });

  it("closes dropdowns when clicking outside", () => {
    dropdown.classList.add("active");
    document.body.click();
    expect(dropdown.classList.contains("active")).toBe(false);
  });
});
