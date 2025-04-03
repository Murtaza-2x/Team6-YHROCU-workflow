function copyResetLink() {
    var copyText = document.getElementById('reset-link');
    copyText.select();
    document.execCommand("copy");
    alert("Link copied to clipboard!");
}