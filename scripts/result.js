// Function to load province data from the server and display it on the page
async function loadProvinceData() {
    const urlParams = new URLSearchParams(window.location.search);
    const provinceId = urlParams.get('province_id');
    if (!provinceId) {
        document.getElementById('destinationsGrid').innerHTML = '<p>No province selected.</p>';
        return;
    }

    try {
        const response = await fetch(`../php/getDestinationsByProvince.php?province_id=${provinceId}`);
        const data = await response.json();
        if (data.error) throw new Error(data.error);

        // Populate header
        document.getElementById('provinceName').innerText = data.province.name;
        document.getElementById('provinceDescription').innerText = data.province.description;

        // Render top destinations
        const topContainer = document.getElementById('topDestinationsGrid');
        if (topContainer) {
            topContainer.innerHTML = data.top_destinations.map(dest => createDestinationCard(dest, true)).join('');
        }

        // Render off the beaten path destinations
        const track = document.getElementById('offBeatenCarouselTrack');
        if (track) {
            const offDests = data.off_beaten_path || [];
            if (offDests.length) {
                track.innerHTML = offDests.map(dest => createOffBeatCard(dest)).join('');
                // Initialize carousel after content is loaded
                initCarousel(track, 'prevOffArrow', 'nextOffArrow');
            } else {
                track.innerHTML = '<div class="w-full text-center">No off the beaten path destinations found for this province.</div>';
            }
        }
    } catch (error) {
        console.error(error);
        document.getElementById('destinationsGrid').innerHTML = '<p class="text-red-500">Failed to load province data. Please try again later.</p>';
    }
}

// Top destinations card creation function
function createDestinationCard(dest, showButton = true) {
    return `
        <div class="md:col-span-6 flex flex-row gap-8">
            <article class="group relative bg-on-primary editorial-shadow rounded-xl overflow-hidden flex flex-col md:flex-row h-auto md:h-80 transition-all duration-500 hover:-translate-y-1">
                <div class="w-full md:w-2/5 relative overflow-hidden">
                    <!--h-96 sets the images, either portrait or landscape, to same height on all screen sizes-->
                    <img src="${dest.path}" alt="${dest.destination}" class="w-full h-96 object-cover transition-transform duration-700 group-hover:scale-110"/>
                    <div class="absolute top-4 left-4 bg-eco-indicator/90 backdrop-blur text-on-primary px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest flex items-center gap-1">
                        <!--Eco Icon-->
                        <svg height="18px" viewBox="0 -960 960 960" width="18px" fill="#ffffff">
                            <path d="M216-176q-45-45-70.5-104T120-402q0-63 24-124.5T222-642q35-35 86.5-60t122-39.5Q501-756 591.5-759t202.5 7q8 106 5 195t-16.5 160.5q-13.5 71.5-38 125T684-182q-53 53-112.5 77.5T450-80q-65 0-127-25.5T216-176Zm112-16q29 17 59.5 24.5T450-160q46 0 91-18.5t86-59.5q18-18 36.5-50.5t32-85Q709-426 716-500.5t2-177.5q-49-2-110.5-1.5T485-670q-61 9-116 29t-90 55q-45 45-62 89t-17 85q0 59 22.5 103.5T262-246q42-80 111-153.5T534-520q-72 63-125.5 142.5T328-192Zm0 0Zm0 0Z"/>
                        </svg>
                        ${escapeHtml(dest.eco_indicator)}
                    </div>
                </div>
                <div class="p-8 flex flex-col justify-between flex-1">
                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <h3 class="text-2xl font-headline font-bold text-primary">
                                ${escapeHtml(dest.destination)}
                            </h3>
                            <div class="flex items-center gap-1 text-secondary">
                                <span style="font-variation-settings: 'FILL' 1;">🌱</span>
                                <span class="text-l font-bold">${escapeHtml(dest.rating)}</span>
                            </div>
                        </div>
                        <p class="text-on-surface-variant text-sm mb-4 leading-relaxed line-clamp-5">
                            ${escapeHtml(dest.description)}
                        </p>
                    </div>
                    <div class="flex items-center justify-between mt-6">
                        <div class="flex items-center gap-2 text-outline text-xs uppercase tracking-wider font-bold">
                            <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5
                                    c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5
                                    2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                                ${escapeHtml(dest.location)}
                        </div>
                        ${showButton ? `<button onclick="goToItinerary(${dest.location_id}, ${dest.destination_id})" class="text-primary text-sm font-bold flex items-center gap-2 group/btn">
                            View Details
                            <svg class="w-6 h-6 transition-transform group-hover/btn:translate-x-1 text-gray-700" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M3 10a1 1 0 011-1h9.586l-3.293-3.293a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 11-1.414-1.414L13.586 11H4a1 1 0 01-1-1z"/>
                            </svg>
                        </button>` : ''}
                    </div>
                </div>
            </article>
        </div>
    `;
}

// Off the beaten path card creation function
function createOffBeatCard(dest) {
    return `
        <div onclick="goToItinerary(${dest.location_id}, ${dest.destination_id})" class="w-full md:w-1/3 flex-shrink-0 px-4">
            <article class="group flex flex-col">
                <div class="aspect-[5/4] md:aspect-[7/8] rounded-xl overflow-hidden mb-4 relative">
                    <img src="${dest.path}" alt="${dest.description}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"/>
                    <div class="absolute top-4 left-4 bg-eco-indicator/90 backdrop-blur text-on-primary px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest flex items-center gap-1">
                        <svg height="18px" viewBox="0 -960 960 960" width="18px" fill="#ffffff">
                            <path d="M216-176q-45-45-70.5-104T120-402q0-63 24-124.5T222-642q35-35 86.5-60t122-39.5Q501-756 591.5-759t202.5 7q8 106 5 195t-16.5 160.5q-13.5 71.5-38 125T684-182q-53 53-112.5 77.5T450-80q-65 0-127-25.5T216-176Zm112-16q29 17 59.5 24.5T450-160q46 0 91-18.5t86-59.5q18-18 36.5-50.5t32-85Q709-426 716-500.5t2-177.5q-49-2-110.5-1.5T485-670q-61 9-116 29t-90 55q-45 45-62 89t-17 85q0 59 22.5 103.5T262-246q42-80 111-153.5T534-520q-72 63-125.5 142.5T328-192Zm0 0Zm0 0Z"/>
                        </svg>
                        ${escapeHtml(dest.eco_indicator)}
                    </div>
                </div>
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="text-xl font-headline font-bold text-primary mb-1">
                            ${escapeHtml(dest.destination)}
                        </h4>
                        <div class="flex items-center gap-2 text-outline text-xs uppercase tracking-wider font-bold">
                            <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5
                                c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5
                                2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                            ${escapeHtml(dest.location)}
                        </div>
                    </div>
                    <div class="text-right flex items-center gap-1 text-secondary justify-end">
                        <span class="font-variation-settings: 'FILL' 1;">🌱</span>
                        <span class="text-l font-bold">${escapeHtml(dest.rating)}</span>
                    </div>
                </div>
            </article>
        </div>
    `;
}

// Carousel initialization function
function initCarousel(trackElement, prevBtnId, nextBtnId) {
    const slides = Array.from(trackElement.children);
    if (slides.length <= 3) return; // No need for carousel if 3 or fewer items

    let currentIndex = 0;
    const slidesPerView = window.innerWidth >= 768 ? 3 : 1;
    const maxIndex = Math.max(0, slides.length - slidesPerView);

    function updateCarousel() {
        const slideWidth = slides[0].offsetWidth;
        const shift = -currentIndex * slideWidth;
        trackElement.style.transform = `translateX(${shift}px)`;
        // Disable buttons at boundaries
        const prevBtn = document.getElementById(prevBtnId);
        const nextBtn = document.getElementById(nextBtnId);
        if (prevBtn) prevBtn.style.opacity = currentIndex === 0 ? '0.5' : '1';
        if (nextBtn) nextBtn.style.opacity = currentIndex === maxIndex ? '0.5' : '1';
    }

    document.getElementById(prevBtnId)?.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateCarousel();
        }
    });
    document.getElementById(nextBtnId)?.addEventListener('click', () => {
        if (currentIndex < maxIndex) {
            currentIndex++;
            updateCarousel();
        }
    });

    // Recalculate on window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            // Recalculate slidesPerView and maxIndex on resize
            const newSlidesPerView = window.innerWidth >= 768 ? 3 : 1;
            const newMaxIndex = Math.max(0, slides.length - newSlidesPerView);
            if (currentIndex > newMaxIndex) currentIndex = newMaxIndex;
            updateCarousel();
        }, 150);
    });

    updateCarousel(); // Initial setup
}

function goToItinerary(locationId, destinationId) {
    window.location.href = `itinerary.php?location_id=${locationId}&destination_id=${destinationId}`;
}

document.addEventListener('DOMContentLoaded', () => {
    // Initialize search suggestions
    initSearchSuggestions('searchInput', 'suggestionsDropdown');

    // Load province data and render the page
    loadProvinceData();
});