/**
 * @jest-environment jsdom
 */
const { updateStrength } = require("../JS/PASSWORD-STRENGTH");

describe("updateStrength", () => {
  let passwordInput, fill, feedback, submitButton;

  beforeEach(() => {
    document.body.innerHTML = `
      <input type="password" id="password" />
      <div id="STRENGTH-FILL"></div>
      <div id="STRENGTH-FEEDBACK"></div>
      <button id="create-user-button"></button>
    `;
    passwordInput = document.getElementById("password");
    fill = document.getElementById("STRENGTH-FILL");
    feedback = document.getElementById("STRENGTH-FEEDBACK");
    submitButton = document.getElementById("create-user-button");
  });

  function testStrength(password, expectedFeedback, expectedColor, expectedWidth, isButtonEnabled) {
    passwordInput.value = password;
    updateStrength();

    expect(feedback.innerText).toBe(expectedFeedback);
    expect(fill.style.background).toBe(expectedColor);
    expect(fill.style.width).toBe(expectedWidth);
    expect(submitButton.disabled).toBe(!isButtonEnabled);
  }

  it("detects very weak password", () => {
    testStrength("a", "Very Weak", "red", "20%", false); // 1 point
  });

  it("detects weak password", () => {
    testStrength("abc123", "Weak", "orange", "40%", false); // 2 points
  });

  it("detects medium password", () => {
    testStrength("Abcdef1", "Medium", "gold", "60%", false); // 3 points
  });

  it("detects good password", () => {
    testStrength("Abcd1234", "Good", "lightgreen", "80%", true); // 4 points
  });

  it("detects strong password", () => {
    testStrength("Abcd1234!", "Strong", "green", "100%", true); // 5 points
  });
});
