/**
 * @jest-environment jsdom
 */
const { copyResetLink } = require("../JS/RESET-LINK-COPY");

describe("copyResetLink", () => {
  beforeEach(() => {
    document.body.innerHTML = `
      <input id="reset-link" value="https://example.com/reset" />
    `;

    // Mock execCommand and alert
    document.execCommand = jest.fn();
    window.alert = jest.fn();
  });

  it("copies reset link and shows alert", () => {
    copyResetLink();

    expect(document.execCommand).toHaveBeenCalledWith("copy");
    expect(window.alert).toHaveBeenCalledWith("Link copied to clipboard!");
  });
});
