/**
 * @jest-environment jsdom
 * 
 * This test suite validates the functionality of the dropdown toggle feature.
 * The tests simulate user interactions to ensure that the dropdown behaves as expected,
 * such as opening, closing other dropdowns when one is opened, and closing when clicking outside.
 */

require("../JS/TOGGLE-DROPDOWN"); // Import the JavaScript file containing the dropdown functionality.

describe("Dropdown toggle functionality", () => {
  // Declare variables that will be used across multiple test cases
  let toggleBtn, dropdown;

  // Set up the test environment before each test case
  beforeEach(() => {
    // Setup the HTML structure for testing. This includes two dropdowns with toggle buttons.
    document.body.innerHTML = `
      <div class="ACTION-DROPDOWN">
        <button class="ACTION-DROPDOWN-TOGGLE">Toggle</button>
      </div>
      <div class="ACTION-DROPDOWN">
        <button class="ACTION-DROPDOWN-TOGGLE">Toggle</button>
      </div>
    `;
    // Simulate the DOMContentLoaded event to ensure that the DOM is fully loaded before tests are executed
    document.dispatchEvent(new Event("DOMContentLoaded"));

    // Select the first toggle button and its associated dropdown
    toggleBtn = document.querySelectorAll(".ACTION-DROPDOWN-TOGGLE")[0];
    dropdown = toggleBtn.closest(".ACTION-DROPDOWN");
  });

  // Test case: Verifies that the dropdown opens when the toggle button is clicked
  it("opens the dropdown when toggled", () => {
    toggleBtn.click(); // Simulate a click event on the toggle button
    expect(dropdown.classList.contains("active")).toBe(true); // Assert that the 'active' class is added to the dropdown
  });

  // Test case: Ensures that only one dropdown is open at a time.
  // When one dropdown is opened, others should be closed.
  it("closes other dropdowns when one is opened", () => {
    // Select both dropdowns
    const [first, second] = document.querySelectorAll(".ACTION-DROPDOWN");

    // Open the first dropdown by adding the 'active' class
    first.classList.add("active");

    // Select the toggle button for the second dropdown and simulate a click event
    const secondToggle = second.querySelector(".ACTION-DROPDOWN-TOGGLE");
    secondToggle.click();

    // Assert that the first dropdown is closed (doesn't have the 'active' class)
    expect(first.classList.contains("active")).toBe(false);
    // Assert that the second dropdown is open (has the 'active' class)
    expect(second.classList.contains("active")).toBe(true);
  });

  // Test case: Validates that clicking outside of a dropdown closes it
  it("closes dropdowns when clicking outside", () => {
    dropdown.classList.add("active"); // Open the dropdown
    document.body.click(); // Simulate a click event on the body, which should close the dropdown
    expect(dropdown.classList.contains("active")).toBe(false); // Assert that the 'active' class is removed, indicating the dropdown is closed
  });
});
