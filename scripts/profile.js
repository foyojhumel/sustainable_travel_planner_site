// Loads and render saved itineraries
function loadSavedItineraries() {
    const container = document.getElementById('savedItinerariesGrid');
    if (!container) return;

    fetch('../php/getSavedItineraries.php')
        .then(res => res.json())
        .then(data => {
            if (data.error) throw new Error(data.error);
            if (data.length === 0) {
                container.innerHTML = '<p class="text-center col-span-full">No saved itineraries yet. Click the heart on any itinerary page to save.</p>';
                return;
            }
            container.innerHTML = data.map(item => `
                <div class="group flex flex-col">
                    <div class="relative overflow-hidden rounded-xl aspect-[5/4] md:aspect-[7/8] mb-4 shadow-sm group-hover:shadow-xl transition-all duration-500">
                        <img src="${item.image_url}" alt="${item.destination}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"/>
                        <div class="absolute top-4 left-4 bg-eco-indicator/90 backdrop-blur text-l text-on-primary px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest flex items-center gap-1">
                            <svg height="18px" viewBox="0 -960 960 960" width="18px" fill="#ffffff">
                                <path d="M216-176q-45-45-70.5-104T120-402q0-63 24-124.5T222-642q35-35 86.5-60t122-39.5Q501-756 591.5-759t202.5 7q8 106 5 195t-16.5 160.5q-13.5 71.5-38 125T684-182q-53 53-112.5 77.5T450-80q-65 0-127-25.5T216-176Zm112-16q29 17 59.5 24.5T450-160q46 0 91-18.5t86-59.5q18-18 36.5-50.5t32-85Q709-426 716-500.5t2-177.5q-49-2-110.5-1.5T485-670q-61 9-116 29t-90 55q-45 45-62 89t-17 85q0 59 22.5 103.5T262-246q42-80 111-153.5T534-520q-72 63-125.5 142.5T328-192Zm0 0Zm0 0Z"/>
                            </svg>
                            ${escapeHtml(item.eco_indicator)}
                        </div>
                        <div class="absolute top-4 right-4 text-right flex items-center gap-1 text-secondary justify-end bg-eco-indicator/90 backdrop-blur text-on-primary px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">
                            <span class="font-variation-settings: 'FILL' 1;">🌱</span>
                            <span class="text-l text-on-primary">${item.rating}</span>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 p-8 bg-gradient-to-t from-primary/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <button onclick="goToItinerary(${item.location_id})" class="w-full bg-white text-primary py-4 rounded-xl font-headline font-bold text-sm uppercase tracking-widest">
                                View Itinerary
                            </button>
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] font-label uppercase tracking-[0.2em] text-secondary font-bold">
                            ${escapeHtml(item.province)}
                        </span>
                        <h3 class="text-2xl font-headline font-bold text-primary mt-2">
                            ${escapeHtml(item.destination)}
                        </h3>
                    </div>
                </div>
            `).join('');
        })
        .catch(err => {
            console.error(err);
            container.innerHTML = '<p class="text-red-500">Failed to load saved itineraries.</p>';
        });
}

document.addEventListener('DOMContentLoaded', () => {
    // 1. Load saved itineraries
    loadSavedItineraries();

    // 2. Search suggestions
    initSearchSuggestions('searchInput', 'suggestionsDropdown');

    // 3. Motto editing (as before)
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

    // For logout menu
    const profileIcon = document.getElementById('profileIcon');
    const logoutMenu = document.getElementById('logoutMenu');
    if (profileIcon && logoutMenu) {
        profileIcon.addEventListener('click', (e) => {
            e.stopPropagation();
            logoutMenu.classList.toggle('hidden');
        });
        // Click anywhere else hides the menu
        document.addEventListener('click', () => {
            logoutMenu.classList.add('hidden');
        });
    }
});