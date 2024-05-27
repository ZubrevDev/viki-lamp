document.addEventListener('DOMContentLoaded', function() {

    // Function to initialize a slider
    function initializeSlider(sliderContainer) {
        const slider = sliderContainer.querySelector('.slider');
        const slides = slider.querySelectorAll('.slide');
        let currentIndex = 0;
        let startX, endX;

        // Function to update the slider position
        function updateSlider() {
            const slideWidth = slides[0].getBoundingClientRect().width;
            slider.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
        }

        // Event listener for the slider container to handle all clicks
        sliderContainer.addEventListener('click', function(event) {
            if (event.target.closest('.button-prev')) {
                currentIndex = (currentIndex - 1 + slides.length) % slides.length;
                updateSlider();
            } else if (event.target.closest('.button-next')) {
                currentIndex = (currentIndex + 1) % slides.length;
                updateSlider();
            }
        });

        // Touch event listeners for swipe functionality
        sliderContainer.addEventListener('touchstart', function(event) {
            startX = event.touches[0].clientX;
        });

        sliderContainer.addEventListener('touchmove', function(event) {
            // Prevent default swipe actions like scrolling
            event.preventDefault();
        }, { passive: false });

        sliderContainer.addEventListener('touchend', function(event) {
            endX = event.changedTouches[0].clientX;

            // Determine the swipe direction
            if (startX - endX > 50) { // Swipe left
                currentIndex = (currentIndex + 1) % slides.length;
                updateSlider();
            } else if (endX - startX > 50) { // Swipe right
                currentIndex = (currentIndex - 1 + slides.length) % slides.length;
                updateSlider();
            }
        });

    }

    // Initialize all sliders on the page
    document.querySelectorAll('.slider-container').forEach(initializeSlider);

});
