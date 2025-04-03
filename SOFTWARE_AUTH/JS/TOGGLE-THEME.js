function toggleTheme() {
    const currentTheme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';

    if (currentTheme === 'light') {
        document.body.classList.remove('light-mode');
        document.body.classList.add('dark-mode');
        localStorage.setItem('theme', 'dark');  // Store theme preference in localStorage
    } else {
        document.body.classList.remove('dark-mode');
        document.body.classList.add('light-mode');
        localStorage.setItem('theme', 'light');  // Store theme preference in localStorage
    }
}

window.onload = () => {
    const savedTheme = localStorage.getItem('theme') || 'light';  // Default to light if no theme is saved
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        document.getElementById('theme-toggle').checked = true;  // Set slider position
    } else {
        document.body.classList.add('light-mode');
        document.getElementById('theme-toggle').checked = false;  // Set slider position
    }
};
