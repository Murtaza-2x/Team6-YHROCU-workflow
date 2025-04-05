document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.ACTION-DROPDOWN-TOGGLE').forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            // Close others
            document.querySelectorAll('.ACTION-DROPDOWN').forEach(function(drop) {
                drop.classList.remove('active');
            });
            // Toggle this one
            this.closest('.ACTION-DROPDOWN').classList.toggle('active');
        });
    });

    // Close when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.ACTION-DROPDOWN').forEach(function(drop) {
            drop.classList.remove('active');
        });
    });
});