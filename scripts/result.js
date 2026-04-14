(function() {
    const track = document.getElementById('carouselTrack');
    const prevBtn = document.getElementById('prevArrow');
    const nextBtn = document.getElementById('nextArrow');
    if (!track) return;

    let currentIndex = 0;
    let slides = Array.from(track.children);
    let slidesPerView = getSlidesPerView();
    let totalSlides = slides.length;
    let maxIndex = Math.max(0, totalSlides - slidesPerView);

    // Helper: get number of visible slides based on screen width
    function getSlidesPerView() {
      return window.innerWidth >= 768 ? 3 : 1;
    }

    // Update carousel position
    function updateCarousel() {
      slidesPerView = getSlidesPerView();
      maxIndex = Math.max(0, totalSlides - slidesPerView);
      if (currentIndex > maxIndex) currentIndex = maxIndex;
      const slideWidth = slides[0]?.offsetWidth || 0;
      const shift = -currentIndex * slideWidth;
      track.style.transform = `translateX(${shift}px)`;
      // Disable arrows at boundaries
      if (prevBtn) prevBtn.style.opacity = currentIndex === 0 ? '0.5' : '1';
      if (nextBtn) nextBtn.style.opacity = currentIndex === maxIndex ? '0.5' : '1';
    }

    // Event listeners
    if (prevBtn) {
      prevBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
          currentIndex--;
          updateCarousel();
        }
      });
    }
    if (nextBtn) {
      nextBtn.addEventListener('click', () => {
        if (currentIndex < maxIndex) {
          currentIndex++;
          updateCarousel();
        }
      });
    }

    // Recalculate on window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        updateCarousel();
      }, 150);
    });

    // Initial setup
    updateCarousel();
  })();