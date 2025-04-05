/**
 * @jest-environment jsdom
 */
const { toggleTheme } = require("../JS/TOGGLE-THEME");

describe("toggleTheme", () => {
  beforeEach(() => {
    document.body.className = ""; // Reset body classes
    localStorage.clear();         // Clear localStorage before each test

    // Add a mock theme toggle checkbox
    document.body.innerHTML = `<input type="checkbox" id="theme-toggle" />`;
  });

  it("switches from light to dark mode", () => {
    document.body.classList.add("light-mode");

    toggleTheme();

    expect(document.body.classList.contains("dark-mode")).toBe(true);
    expect(document.body.classList.contains("light-mode")).toBe(false);
    expect(localStorage.getItem("theme")).toBe("dark");
  });

  it("switches from dark to light mode", () => {
    document.body.classList.add("dark-mode");

    toggleTheme();

    expect(document.body.classList.contains("light-mode")).toBe(true);
    expect(document.body.classList.contains("dark-mode")).toBe(false);
    expect(localStorage.getItem("theme")).toBe("light");
  });
});
