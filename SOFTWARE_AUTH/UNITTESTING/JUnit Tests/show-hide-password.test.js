/**
 * @jest-environment jsdom
 */
import { togglePasswordVisibility } from "../SHOW-HIDE-PASSWORD";

describe("togglePasswordVisibility", () => {
  let passwordField, toggleIcon;

  beforeEach(() => {
    document.body.innerHTML = `
      <input id="password" type="password" />
      <img id="toggleIcon" />
    `;
    passwordField = document.getElementById("password");
    toggleIcon = document.getElementById("toggleIcon");
  });

  it("reveals the password and updates the icon", () => {
    togglePasswordVisibility();
    expect(passwordField.type).toBe("text");
    expect(toggleIcon.src).toContain("eye.svg");
  });

  it("hides the password and updates the icon", () => {
    passwordField.type = "text"; // already revealed
    togglePasswordVisibility();
    expect(passwordField.type).toBe("password");
    expect(toggleIcon.src).toContain("eye-crossed.png");
  });
});
