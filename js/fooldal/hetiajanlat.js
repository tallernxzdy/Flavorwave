    // Visszaszámláló logika
    const countdown = document.getElementById('countdown');
    const endDate = new Date();
    endDate.setDate(endDate.getDate() + 7); // Heti ajánlat 7 nap múlva véget ér

    function updateCountdown() {
        const now = new Date();
        const timeLeft = endDate - now;

        if (timeLeft <= 0) {
            countdown.innerHTML = '<strong>Az ajánlat véget ért!</strong>';
            return;
        }

        const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

        countdown.querySelector('.days').textContent = String(days).padStart(2, '0');
        countdown.querySelector('.hours').textContent = String(hours).padStart(2, '0');
        countdown.querySelector('.minutes').textContent = String(minutes).padStart(2, '0');
        countdown.querySelector('.seconds').textContent = String(seconds).padStart(2, '0');
    }

    setInterval(updateCountdown, 1000);

    // Váltakozó képek logika
    function changeImage(sliderId) {
        const images = document.querySelectorAll(`#${sliderId} img`);
        let currentIndex = 0;

        function switchImage() {
            images[currentIndex].classList.remove('active');
            currentIndex = (currentIndex + 1) % images.length;
            images[currentIndex].classList.add('active');
        }

        images[currentIndex].classList.add('active'); // Induló kép
        setInterval(switchImage, 3000); // 3 másodpercenként vált
    }

    // Kezdés a két sliderrel
    changeImage('image-slider-1');
    changeImage('image-slider-2');