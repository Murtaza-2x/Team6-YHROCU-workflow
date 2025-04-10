function updateStrength() {
    const password = document.getElementById("password").value;
    const fill = document.getElementById("STRENGTH-FILL");
    const feedback = document.getElementById("STRENGTH-FEEDBACK");
    const submitButton = document.getElementById("create-user-button");

    let score = 0;

    if (password.length >= 8) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[!@#$%^&*(),.?\":{}|<>]/.test(password)) score++;

    switch (score) {
        case 0:
        case 1:
            fill.style.width = "20%";
            fill.style.background = "red";
            feedback.innerText = "Very Weak";
            submitButton.disabled = true;
            break;
        case 2:
            fill.style.width = "40%";
            fill.style.background = "orange";
            feedback.innerText = "Weak";
            submitButton.disabled = true;
            break;
        case 3:
            fill.style.width = "60%";
            fill.style.background = "gold";
            feedback.innerText = "Medium";
            submitButton.disabled = true;
            break;
        case 4:
            fill.style.width = "80%";
            fill.style.background = "lightgreen";
            feedback.innerText = "Good";
            submitButton.disabled = false;
            break;
        case 5:
            fill.style.width = "100%";
            fill.style.background = "green";
            feedback.innerText = "Strong";
            submitButton.disabled = false;
            break;
    }
}