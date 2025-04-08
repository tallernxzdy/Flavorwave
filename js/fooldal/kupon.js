const slidesContainer = document.querySelector('.slides');
const slide = document.querySelectorAll('.slide');
const prevButton = document.querySelector('.prev');
const nextButton = document.querySelector('.next');
const dots = document.querySelectorAll('.dot');
let currentIndex = 0;
let autoSlideInterval;
let maxHeight = 0;

// Calculate maximum height of all slides
const calculateMaxHeight = () => {
    maxHeight = 0;
    slide.forEach(s => {
        s.style.opacity = '1'; // Temporarily make visible to measure
        s.style.position = 'relative';
        if (s.offsetHeight > maxHeight) {
            maxHeight = s.offsetHeight;
        }
        s.style.opacity = '';
        s.style.position = '';
    });
    
    // Apply max height to all slides and container
    document.querySelectorAll('.slide').forEach(s => {
        s.style.height = `${maxHeight}px`;
    });
    document.querySelector('.slides').style.minHeight = `${maxHeight}px`;
};

const updateSlider = () => {
    slide.forEach((s, index) => {
        s.classList.toggle('active', index === currentIndex);
    });
    dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === currentIndex);
    });
};

const nextSlide = () => {
    currentIndex = (currentIndex < slide.length - 1) ? currentIndex + 1 : 0;
    updateSlider();
};

const prevSlide = () => {
    currentIndex = (currentIndex > 0) ? currentIndex - 1 : slide.length - 1;
    updateSlider();
};

const startAutoSlide = () => {
    autoSlideInterval = setInterval(nextSlide, 7000);
};

const stopAutoSlide = () => {
    clearInterval(autoSlideInterval);
};

// Initialize slider
window.addEventListener('load', () => {
    calculateMaxHeight();
    updateSlider();
    startAutoSlide();
});

window.addEventListener('resize', () => {
    calculateMaxHeight();
});

// Event listeners
prevButton.addEventListener('click', () => {
    prevSlide();
    stopAutoSlide();
    startAutoSlide();
});

nextButton.addEventListener('click', () => {
    nextSlide();
    stopAutoSlide();
    startAutoSlide();
});

dots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
        currentIndex = index;
        updateSlider();
        stopAutoSlide();
        startAutoSlide();
    });
});

// Pause on hover
const slider = document.querySelector('.coupon-slider');
slider.addEventListener('mouseenter', stopAutoSlide);
slider.addEventListener('mouseleave', startAutoSlide);