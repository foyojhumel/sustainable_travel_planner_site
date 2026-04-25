document.addEventListener('DOMContentLoaded', () => {
    // Initialize search suggestions
    initSearchSuggestions('searchInput', 'suggestionsDropdown');

    // Motto editing functionality
    const editBtn = document.getElementById('editMottoBtn');
    const mottoForm = document.getElementById('editMottoForm');
    const saveBtn = document.getElementById('saveMottoBtn');
    const cancelBtn = document.getElementById('cancelMottoBtn');
    const userMottoEl = document.getElementById('userMotto');
    const newMottoTextarea = document.getElementById('newMotto');

    // Show edit form
    if (editBtn && mottoForm) {
        editBtn.addEventListener('click', () => {
            mottoForm.style.display = 'block';
        });
    } else {
        console.warn('Edit motto button or form not found');
    }

    // Cancel button – hide form
    if (cancelBtn && mottoForm) {
        cancelBtn.addEventListener('click', () => {
            mottoForm.style.display = 'none';
        });
    }

    // Save button – AJAX update
    if (saveBtn && newMottoTextarea && userMottoEl && mottoForm) {
        saveBtn.addEventListener('click', () => {
            const newMotto = newMottoTextarea.value.trim();
            fetch('update_motto.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'motto=' + encodeURIComponent(newMotto)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    userMottoEl.innerText = newMotto || 'Click edit to add your motto...';
                    mottoForm.style.display = 'none';
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(err => {
                console.error('Fetch error:', err);
                alert('Network error. Please try again.');
            });
        });
    } else {
        console.warn('Save button, textarea, or motto element missing');
    }
});