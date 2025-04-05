/**
 * @jest-environment jsdom
 *
 * This test suite verifies the functionality of the toggleTheme function
 * which is responsible for switching the website theme between light and dark modes.
 * It uses a simulated DOM (via jsdom) to manipulate the document body and localStorage.
 */

const { toggleTheme } = require("../JS/TOGGLE-THEME");

describe("toggleTheme", () => {
  /**
   * beforeEach hook:
   * - Resets the document body class list.
   * - Clears localStorage to ensure a clean environment for each test.
   * - Sets up a mock theme toggle checkbox element on the page.
   */
  beforeEach(() => {
    document.body.className = ""; // Reset body classes
    localStorage.clear();         // Clear localStorage before each test

    // Add a mock theme toggle checkbox to the document body
    document.body.innerHTML = `<input type="checkbox" id="theme-toggle" />`;
  });

  /**
   * Test: Switching from light to dark mode
   * - Initially, the document body is set to "light-mode".
   * - After calling toggleTheme(), the body should switch to "dark-mode",
   *   the "light-mode" class should be removed, and localStorage should store "dark".
   */
  it("switches from light to dark mode", () => {
    // Set initial theme to light-mode
    document.body.classList.add("light-mode");

    // Call the function to toggle theme
    toggleTheme();

    // Check that the dark-mode class is present and light-mode is removed
    expect(document.body.classList.contains("dark-mode")).toBe(true);
    expect(document.body.classList.contains("light-mode")).toBe(false);
    // Verify that localStorage records the "dark" theme
    expect(localStorage.getItem("theme")).toBe("dark");
  });

  /**
   * Test: Switching from dark to light mode
   * - Initially, the document body is set to "dark-mode".
   * - After calling toggleTheme(), the body should switch to "light-mode",
   *   the "dark-mode" class should be removed, and localStorage should store "light".
   */
  it("switches from dark to light mode", () => {
    // Set initial theme to dark-mode
    document.body.classList.add("dark-mode");

    // Call the function to toggle theme
    toggleTheme();

    // Check that the light-mode class is present and dark-mode is removed
    expect(document.body.classList.contains("light-mode")).toBe(true);
    expect(document.body.classList.contains("dark-mode")).toBe(false);
    // Verify that localStorage records the "light" theme
    expect(localStorage.getItem("theme")).toBe("light");
  });
});