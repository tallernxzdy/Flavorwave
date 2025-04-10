
        // Weekly Deals Slider
        const sliders = document.querySelectorAll('.image-slider');
        sliders.forEach(slider => {
            const images = slider.querySelectorAll('img');
            let currentIndex = 0;

            images[currentIndex].classList.add('active');
            setInterval(() => {
                images[currentIndex].classList.remove('active');
                currentIndex = (currentIndex + 1) % images.length;
                images[currentIndex].classList.add('active');
            }, 5000);
        });




        // Coupon Slider
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');
        const prevBtn = document.querySelector('.prev');
        const nextBtn = document.querySelector('.next');
        let currentSlide = 0;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.remove('active');
                dots[i].classList.remove('active');
                if (i === index) {
                    slide.classList.add('active');
                    dots[i].classList.add('active');
                }
            });
        }

        showSlide(currentSlide);
        prevBtn.addEventListener('click', () => {
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(currentSlide);
        });

        nextBtn.addEventListener('click', () => {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        });

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentSlide = index;
                showSlide(currentSlide);
            });
        });

        setInterval(() => {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }, 5000);




        // Quiz
        // const questions = document.querySelectorAll('.quiz-question');
        // const resultCard = document.querySelector('.quiz-result-card');
        // const recommendedFood = document.getElementById('recommended-food');
        // const foodImage = document.getElementById('food-image');
        // const orderLink = document.getElementById('order-link');
        // let currentQuestion = 0;
        // let answers = {};

        // questions[currentQuestion].classList.add('active');

        // document.querySelectorAll('.quiz-options button').forEach(button => {
        //     button.addEventListener('click', () => {
        //         const key = button.getAttribute('data-type') || button.getAttribute('data-spice') || button.getAttribute('data-cheese');
        //         answers[key] = button.textContent;

        //         questions[currentQuestion].classList.remove('active');
        //         currentQuestion++;

        //         if (currentQuestion < questions.length) {
        //             questions[currentQuestion].classList.add('active');
        //         } else {
        //             resultCard.classList.add('active');
        //             showResult();
        //         }
        //     });
        // });

        // function showResult() {
        //     let result = '';
        //     let imageSrc = '';

        //     if (answers['type'] === 'Húsos') {
        //         if (answers['spice'] === 'Csípős') result = 'Spicy Meat Lover';
        //         else if (answers['cheese'] === 'Extra') result = 'Meat & Cheese Deluxe';
        //         else result = 'Classic Pepperoni';
        //         imageSrc = '../kepek/pizza4.jpg';
        //     } else if (answers['type'] === 'Zöldséges') {
        //         if (answers['spice'] === 'Csípős') result = 'Spicy Veggie';
        //         else result = 'Garden Fresh';
        //         imageSrc = '../kepek/pizza2.jpg';
        //     } else {
        //         result = answers['cheese'] === 'Extra' ? 'Four Cheese Bliss' : 'Margherita';
        //         imageSrc = '../kepek/pizza2.jpg';
        //     }

        //     recommendedFood.textContent = result;
        //     foodImage.src = imageSrc;
        //     orderLink.href = '#order';
        // }




        // Shaker Slider
        const shakerSlides = document.querySelectorAll('.shaker-slide');
        const shakerDots = document.querySelectorAll('.shaker-dot');
        const shakerPrev = document.querySelector('.shaker-prev');
        const shakerNext = document.querySelector('.shaker-next');
        let currentShaker = 0;

        function showShaker(index) {
            shakerSlides.forEach((slide, i) => {
                slide.classList.remove('active');
                shakerDots[i].classList.remove('active');
                if (i === index) {
                    slide.classList.add('active');
                    shakerDots[i].classList.add('active');
                }
            });
        }

        showShaker(currentShaker);
        shakerPrev.addEventListener('click', () => {
            currentShaker = (currentShaker - 1 + shakerSlides.length) % shakerSlides.length;
            showShaker(currentShaker);
        });

        shakerNext.addEventListener('click', () => {
            currentShaker = (currentShaker + 1) % shakerSlides.length;
            showShaker(currentShaker);
        });

        shakerDots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentShaker = index;
                showShaker(currentShaker);
            });
        });

        setInterval(() => {
            currentShaker = (currentShaker + 1) % shakerSlides.length;
            showShaker(currentShaker);
        }, 5000);



        function updateCountdown() {
            const now = new Date();
            
            // Az aktuális hét vasárnapjának éjfél kiszámítása
            const currentDay = now.getDay(); // 0 = vasárnap, 1 = hétfő, stb.
            const daysUntilSunday = (7 - currentDay) % 7; // Hány nap van vasárnapig
            
            // Következő vasárnap éjfél beállítása
            const endDate = new Date(now);
            endDate.setDate(now.getDate() + daysUntilSunday);
            endDate.setHours(23, 59, 59, 999); // Vasárnap éjfél előtti utolsó pillanat
        
            // Ha éppen vasárnap van és éjfél elmúlt, a következő vasárnapra állítjuk
            if (currentDay === 0 && now > endDate) {
                endDate.setDate(endDate.getDate() + 7);
            }
        
            const diff = endDate - now;
        
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);
        
            // Ellenőrzés, hogy a DOM elemek léteznek-e
            const daysElement = document.querySelector('.days');
            const hoursElement = document.querySelector('.hours');
            const minutesElement = document.querySelector('.minutes');
            const secondsElement = document.querySelector('.seconds');
        
            if (daysElement) daysElement.textContent = days.toString().padStart(2, '0');
            if (hoursElement) hoursElement.textContent = hours.toString().padStart(2, '0');
            if (minutesElement) minutesElement.textContent = minutes.toString().padStart(2, '0');
            if (secondsElement) secondsElement.textContent = seconds.toString().padStart(2, '0');
        }
        
        // Azonnali indítás és másodpercenkénti frissítés
        setInterval(updateCountdown, 1000);
        updateCountdown();

// Using AOS (Animate on Scroll) library for scroll animations
document.addEventListener('DOMContentLoaded', () => {
    AOS.init({
        duration: 800, /* Animation duration */
        easing: 'ease-out-back', /* Smooth bounce effect */
        delay: 200, /* Staggered delay */
        offset: 100, /* Trigger earlier */
        once: false /* Repeat on scroll */
    });
});