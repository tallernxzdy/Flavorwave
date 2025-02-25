const shakerSlides = document.querySelector('.shaker-slides');
const shakerSlide = document.querySelectorAll('.shaker-slide');
const shakerPrevButton = document.querySelector('.shaker-prev');
const shakerNextButton = document.querySelector('.shaker-next');
const shakerDots = document.querySelectorAll('.shaker-dot');
let currentShakerIndex = 0;
let shakerAutoSlideInterval;

const updateShakerSlider = () => {
    shakerSlides.style.transform = `translateX(-${currentShakerIndex * 100}%)`;
    shakerDots.forEach((dot, index) => {
        dot.classList.toggle('active', index === currentShakerIndex);
    });
};

const nextShakerSlide = () => {
    currentShakerIndex = (currentShakerIndex < shakerSlide.length - 1) ? currentShakerIndex + 1 : 0;
    updateShakerSlider();
};

const prevShakerSlide = () => {
    currentShakerIndex = (currentShakerIndex > 0) ? currentShakerIndex - 1 : shakerSlide.length - 1;
    updateShakerSlider();
};

const startShakerAutoSlide = () => {
    shakerAutoSlideInterval = setInterval(nextShakerSlide, 9000); // 5 másodpercenként vált
};

const stopShakerAutoSlide = () => {
    clearInterval(shakerAutoSlideInterval);
};

shakerPrevButton.addEventListener('click', () => {
    prevShakerSlide();
    stopShakerAutoSlide();
    startShakerAutoSlide();
});

shakerNextButton.addEventListener('click', () => {
    nextShakerSlide();
    stopShakerAutoSlide();
    startShakerAutoSlide();
});

shakerDots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
        currentShakerIndex = index;
        updateShakerSlider();
        stopShakerAutoSlide();
        startShakerAutoSlide();
    });
});

// Start automatic sliding on page load
startShakerAutoSlide();

// Optional: Pause auto-slide on hover
const shakerSlider = document.querySelector('.shaker-master');
shakerSlider.addEventListener('mouseenter', stopShakerAutoSlide);
shakerSlider.addEventListener('mouseleave', startShakerAutoSlide);