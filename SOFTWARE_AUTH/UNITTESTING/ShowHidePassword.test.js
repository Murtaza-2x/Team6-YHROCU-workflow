/**
 * @jest-environment jsdom
 *
 * This test suite verifies the functionality of the togglePasswordVisibility
 * function from the SHOW-HIDE-PASSWORD module. It simulates a minimal DOM
 * with a password input field and a toggle icon. The tests ensure that the function:
 * - Reveals the password by changing the input type to "text" and updates the icon accordingly.
 * - Hides the password by changing the input type back to "password" and updates the icon accordingly.
 */

const { togglePasswordVisibility } = require("../JS/SHOW-HIDE-PASSWORD");

describe("togglePasswordVisibility", () => {
  let passwordField, toggleIcon;

  /**
   * beforeEach:
   * - Sets up the DOM with a password input and a toggle icon.
   * - Clears any previous state to ensure tests are isolated.
   */
  beforeEach(() => {
    // Set the HTML content with a password input and an image element for the toggle icon
    document.body.innerHTML = `
      <input id="password" type="password" />
      <img id="toggleIcon" />
    `;
    // Get references to the password field and the toggle icon elements
    passwordField = document.getElementById("password");
    toggleIcon = document.getElementById("toggleIcon");
  });

  /**
   * Test: Reveals the Password and Updates the Icon
   * - Simulates clicking the toggle when the password is hidden.
   * - Expects the input field's type to change to "text" and the icon source to update to show "eye.svg".
   */
  it("reveals the password and updates the icon", () => {
    // Call the function to toggle password visibility
    togglePasswordVisibility();
    // Expect the password field's type to change from "password" to "text"
    expect(passwordField.type).toBe("text");
    // Expect the toggle icon's source to include "eye.svg" indicating the password is now visible
    expect(toggleIcon.src).toContain("eye.svg");
  });

  /**
   * Test: Hides the Password and Updates the Icon
   * - Simulates clicking the toggle when the password is already revealed.
   * - Expects the input field's type to revert to "password" and the icon source to update to show "eye-crossed.png".
   */
  it("hides the password and updates the icon", () => {
    // Pre-set the password field to "text" to simulate the password being visible
    passwordField.type = "text";
    // Call the function to toggle password visibility
    togglePasswordVisibility();
    // Expect the password field's type to revert back to "password"
    expect(passwordField.type).toBe("password");
    // Expect the toggle icon's source to include "eye-crossed.png" indicating the password is now hidden
    expect(toggleIcon.src).toContain("eye-crossed.png");
  });
});