document.addEventListener('DOMContentLoaded', () => {
    // 1. Search suggestions
    initSearchSuggestions('searchInput', 'suggestionsDropdown');

    // 2. Motto editing (as before)
    const editBtn = document.getElementById('editMottoBtn');
    const mottoForm = document.getElementById('editMottoForm');
    const saveBtn = document.getElementById('saveMottoBtn');
    const cancelBtn = document.getElementById('cancelMottoBtn');
    const userMottoEl = document.getElementById('userMotto');
    const newMottoTextarea = document.getElementById('newMotto');

    if (editBtn && mottoForm) {
        editBtn.addEventListener('click', () => {
            mottoForm.style.display = 'block';
        });
    }

    if (cancelBtn && mottoForm) {
        cancelBtn.addEventListener('click', () => {
            mottoForm.style.display = 'none';
        });
    }

    if (saveBtn && newMottoTextarea && userMottoEl && mottoForm) {
        saveBtn.addEventListener('click', () => {
            const newMotto = newMottoTextarea.value.trim();
            fetch('../php/updateMotto.php', {
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
            .catch(err => console.error(err));
        });
    }

    // 3. Profile picture upload
    const editPhotoBtn = document.getElementById('editPhotoBtn');
    const photoInput = document.getElementById('profilePhotoInput');
    const profileImage = document.getElementById('profileImage');

    if (editPhotoBtn && photoInput && profileImage) {
        editPhotoBtn.addEventListener('click', () => {
            photoInput.click();
        });

        photoInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('profile_picture', file);

            fetch('../php/uploadProfilePicture.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    profileImage.src = data.new_url + '?t=' + Date.now();
                    alert('Profile picture updated!');
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(err => {
                console.error('Upload error:', err);
                alert('Network error. Please try again.');
            });

            photoInput.value = '';
        });
    }
});