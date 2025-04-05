/**
 * @jest-environment jsdom
 *
 * This test suite verifies the functionality of the updateStrength function
 * from the PASSWORD-STRENGTH module. It simulates a DOM with an input field for
 * the password, a div for the strength bar fill, a div for strength feedback, and
 * a button that can be enabled/disabled based on password strength.
 *
 * The tests cover various password strength scenarios:
 *  - Very weak password: Should display "Very Weak" feedback, red fill at 20%, and disable the button.
 *  - Weak password: Should display "Weak" feedback, orange fill at 40%, and disable the button.
 *  - Medium password: Should display "Medium" feedback, gold fill at 60%, and disable the button.
 *  - Good password: Should display "Good" feedback, lightgreen fill at 80%, and enable the button.
 *  - Strong password: Should display "Strong" feedback, green fill at 100%, and enable the button.
 */

const { updateStrength } = require("../JS/PASSWORD-STRENGTH");

describe("updateStrength", () => {
  let passwordInput, fill, feedback, submitButton;

  /**
   * beforeEach hook:
   * - Sets up the DOM with a password input, a strength fill div, a feedback div,
   *   and a submit button.
   * - This ensures that each test runs with a fresh environment.
   */
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

  /**
   * Helper function: testStrength
   * Simulates entering a password and then calling updateStrength,
   * and then asserts that:
   * - The feedback text matches the expected feedback.
   * - The fill's background color and width match the expected values.
   * - The submit button is enabled/disabled as expected.
   *
   * @param {string} password - The password to test.
   * @param {string} expectedFeedback - The expected feedback text.
   * @param {string} expectedColor - The expected background color of the fill element.
   * @param {string} expectedWidth - The expected width percentage of the fill element.
   * @param {boolean} isButtonEnabled - Whether the submit button should be enabled.
   */
  function testStrength(password, expectedFeedback, expectedColor, expectedWidth, isButtonEnabled) {
    // Set the password input value and call updateStrength to update UI
    passwordInput.value = password;
    updateStrength();

    // Assert that the feedback message is as expected
    expect(feedback.innerText).toBe(expectedFeedback);
    // Assert that the fill style background color is as expected
    expect(fill.style.background).toBe(expectedColor);
    // Assert that the fill style width is as expected
    expect(fill.style.width).toBe(expectedWidth);
    // Assert that the submit button is enabled/disabled as expected
    expect(submitButton.disabled).toBe(!isButtonEnabled);
  }

  /**
   * Test: Very Weak Password
   * Checks that a very short password ("a") is identified as "Very Weak",
   * with a red fill at 20% and the submit button disabled.
   */
  it("detects very weak password", () => {
    testStrength("a", "Very Weak", "red", "20%", false); // 1 point
  });

  /**
   * Test: Weak Password
   * Checks that a weak password ("abc123") is identified as "Weak",
   * with an orange fill at 40% and the submit button disabled.
   */
  it("detects weak password", () => {
    testStrength("abc123", "Weak", "orange", "40%", false); // 2 points
  });

  /**
   * Test: Medium Password
   * Checks that a medium-strength password ("Abcdef1") is identified as "Medium",
   * with a gold fill at 60% and the submit button disabled.
   */
  it("detects medium password", () => {
    testStrength("Abcdef1", "Medium", "gold", "60%", false); // 3 points
  });

  /**
   * Test: Good Password
   * Checks that a good password ("Abcd1234") is identified as "Good",
   * with a lightgreen fill at 80% and the submit button enabled.
   */
  it("detects good password", () => {
    testStrength("Abcd1234", "Good", "lightgreen", "80%", true); // 4 points
  });

  /**
   * Test: Strong Password
   * Checks that a strong password ("Abcd1234!") is identified as "Strong",
   * with a green fill at 100% and the submit button enabled.
   */
  it("detects strong password", () => {
    testStrength("Abcd1234!", "Strong", "green", "100%", true); // 5 points
  });
});