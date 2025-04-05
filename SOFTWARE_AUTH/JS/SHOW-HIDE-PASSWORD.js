function togglePasswordVisibility() {
  const passwordField = document.getElementById("password");
  const toggleIcon = document.getElementById("toggleIcon");

  if (passwordField.type === "password") {
    passwordField.type = "text";
    // Change the icon to 'eye'
    toggleIcon.src = "ICONS/eye.svg";
  } else {
    // Switch to hidden password
    passwordField.type = "password";
    // Change the icon to 'eye-crossed'
    toggleIcon.src = "ICONS/eye-crossed.png";
  }
}
module.exports = { togglePasswordVisibility };