const slides = document.querySelector('.slides');
const slide = document.querySelectorAll('.slide');
const prevButton = document.querySelector('.prev');
const nextButton = document.querySelector('.next');
const dots = document.querySelectorAll('.dot');
let currentIndex = 0;
let autoSlideInterval;

const updateSlider = () => {
    slides.style.transform = `translateX(-${currentIndex * 100}%)`;
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

// Start automatic sliding on page load
startAutoSlide();

// Optional: Pause auto-slide on hover
const slider = document.querySelector('.coupon-slider');
slider.addEventListener('mouseenter', stopAutoSlide);
slider.addEventListener('mouseleave', startAutoSlide);