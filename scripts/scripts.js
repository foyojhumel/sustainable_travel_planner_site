// Function to generate slides dynamically
function initHeroSlideshow(images) {
    const container = document.getElementById('heroSlideshow');
    if (!container) return;

    // Clear any existing content
    container.innerHTML = '';

    // Create a slide div for each image
    images.forEach((imgSrc, index) => {
        const slideDiv = document.createElement('div');
        slideDiv.className = `slide ${index === 0 ? 'active' : ''}`;
        
        const img = document.createElement('img');
        img.src = imgSrc;
        img.alt = `Hero background ${index + 1}`;
        img.loading = 'eager';
        
        slideDiv.appendChild(img);
        container.appendChild(slideDiv);
    });

    // Start the rotation
    let currentIndex = 0;
    const slides = document.querySelectorAll('#heroSlideshow .slide');
    if (slides.length <= 1) return; // No need to rotate if only one image

    setInterval(() => {
        // Remove active class from current slide
        slides[currentIndex].classList.remove('active');
        // Move to next slide (loop around)
        currentIndex = (currentIndex + 1) % slides.length;
        // Add active class to new slide
        slides[currentIndex].classList.add('active');
    }, 5000); // Change image every 5 seconds
}

// Function to load top destinations from the server and display them to
//  bento grid layout
function loadTopDestinations() {
    const container = document.getElementById('topDestinationsGrid');
    if (!container) {
        console.error('Container #topDestinationsGrid not found');
        return;
    }

    // Show loading indicator
    container.innerHTML = '<div class="text-center col-span-full">Loading destinations...</div>';

    fetch('php/getTopDestinations.php')
        .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        })
        .then(destinations => {
            if (destinations && destinations.length) {
                container.innerHTML = destinations.map(dest => createDestinationCard(dest)).join('');
            } else {
                container.innerHTML = '<p class="text-center col-span-full">No destinations found.</p>';
            }
        })
        .catch(error => {
            console.error('Failed to load destinations:', error);
            container.innerHTML = '<p class="text-center col-span-full text-red-500">Unable to load destinations.</p>';
        });
}

// Function to create HTML for a single destination card
function createDestinationCard(dest) {
    const ecoIcon = `<svg height="18px" viewBox="0 -960 960 960" width="18px" fill="#ffffff">
                        <path d="M216-176q-45-45-70.5-104T120-402q0-63 24-124.5T222-642q35-35 86.5-60t122-39.5Q501-756 591.5-759t202.5 7q8 106 5 195t-16.5 160.5q-13.5 71.5-38 125T684-182q-53 53-112.5 77.5T450-80q-65 0-127-25.5T216-176Zm112-16q29 17 59.5 24.5T450-160q46 0 91-18.5t86-59.5q18-18 36.5-50.5t32-85Q709-426 716-500.5t2-177.5q-49-2-110.5-1.5T485-670q-61 9-116 29t-90 55q-45 45-62 89t-17 85q0 59 22.5 103.5T262-246q42-80 111-153.5T534-520q-72 63-125.5 142.5T328-192Zm0 0Zm0 0Z"/>
                    </svg>`;

    return `
        <div onclick="goToItinerary(${dest.location_id}, ${dest.destination_id})" class="md:col-span-4 gap-6">
            <div class="group cursor-pointer">
                <div class="relative aspect-[16/10] overflow-hidden rounded-xl bg-surface-container-high mb-2">
                    <img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" 
                        src="${escapeHtml(dest.path)}" 
                        alt="${escapeHtml(dest.destination)}">
                    <div class="absolute top-4 left-4 bg-eco-indicator/90 backdrop-blur text-on-primary px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest flex items-center gap-1">
                        ${ecoIcon}
                        ${escapeHtml(dest.eco_indicator)}
                    </div>
                </div>
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-headline font-bold text-xl text-on-surface mb-1">
                            ${escapeHtml(dest.destination)}
                        </h3>
                        <p class="text-sm text-on-surface-variant font-medium">
                            ${escapeHtml(dest.description)}
                        </p>
                    </div>
                    <div class="text-right flex items-center gap-1 text-secondary justify-end">
                        <span class="text-sm">🌱</span>
                        <span class="text-l font-bold font-headline">
                            ${parseFloat(dest.rating).toFixed(1)}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function goToItinerary(locationId, destinationId) {
    window.location.href = `pages/itinerary.html?location_id=${locationId}&destination_id=${destinationId}`;
}

// Startup code to initialize search suggestions and hero slideshow
document.addEventListener('DOMContentLoaded', () => {
    // Initialize search suggestions
    initSearchSuggestions('searchInput', 'suggestionsDropdown', 'searchButton');
    
    // Fetch hero images from the server and initialize the slideshow
    fetch('php/getHeroImages.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(heroImages => {
            if (heroImages && heroImages.length > 0) {
                initHeroSlideshow(heroImages);
            } else {
                console.warn('No images returned from PHP, using fallbacks');
                const fallbackImages = [
                    '/images/palawan/el_nido/seven_commandos_beach.jpg',
                    '/images/cebu/bantayan/bantayan_island.jpg'
                ];
                initHeroSlideshow(fallbackImages);
            }
        })
        .catch(error => {
            console.error('Failed to load hero images:', error);
            const fallbackImages = [
                '/images/palawan/el_nido/seven_commandos_beach.jpg',
                '/images/cebu/bantayan/bantayan_island.jpg'
            ];
            initHeroSlideshow(fallbackImages);
        });

    // Load top destinations for the bento grid
    loadTopDestinations();
});