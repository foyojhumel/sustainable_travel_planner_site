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

// Start the slideshow when the page loads
document.addEventListener('DOMContentLoaded', () => {
//initHeroSlideshow(heroImages);
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
});