AOS.init();

// Egyedi modális ablak megnyitása
function openCustomModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = 'block';
    modal.classList.add('modal-open'); // Animáció hozzáadása
    document.body.style.overflow = 'hidden'; // Görgetés letiltása
}

// Egyedi modális ablak bezárása
function closeCustomModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.add('modal-close'); // Animáció hozzáadása
    setTimeout(() => {
        modal.style.display = 'none';
        modal.classList.remove('modal-open', 'modal-close'); // Animációs osztályok eltávolítása
        document.body.style.overflow = 'auto'; // Görgetés engedélyezése
    }, 300); // Az animáció időtartama
}

// Kosárba rakás és modális ablak megnyitása
function addToCartAndOpenModal(modalId, itemId) {
    const button = document.querySelector(`#${modalId} .add-to-cart`) || document.querySelector(`[data-item-id="${itemId}"]`);
    const imageSrc = button.getAttribute('data-image');
    animateCartAdd(button, imageSrc); // Animáció indítása
    addToCart(itemId);
    openCustomModal(modalId); // Modális ablak megnyitása a részletek gombtól
}

// Csak kosárba rakás
function addToCartOnly(itemId) {
    const button = document.querySelector(`[data-item-id="${itemId}"]`);
    const imageSrc = button.getAttribute('data-image');
    animateCartAdd(button, imageSrc); // Animáció indítása
    addToCart(itemId);
}

// Kosárba rakás
function addToCart(itemId) {
    fetch("kosarba_rakas.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ itemId: itemId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast("Sikeresen hozzáadva a kosárhoz!");
                updateCartCount(); // Frissítjük a kosár számlálót
            } else {
                showToast("Hiba történt a kosárba rakás során.");
            }
        })
        .catch(error => {
            console.error('Hiba a kosárba rakás során:', error);
            showToast("Hiba történt a kosárba rakás során.");
        });
}

// Kosárba rakás animáció
function animateCartAdd(button, imageSrc) {
    const cartIcon = document.querySelector('.cart-btn');
    const cartAnimationItem = document.getElementById('cart-animation-item');

    // Animációs elem beállítása
    cartAnimationItem.style.backgroundImage = `url(${imageSrc})`;
    cartAnimationItem.style.display = 'block';

    // Kezdő pozíció meghatározása (a gomb pozíciója)
    const buttonRect = button.getBoundingClientRect();
    const cartRect = cartIcon.getBoundingClientRect();

    cartAnimationItem.style.left = `${buttonRect.left + buttonRect.width / 2 - 25}px`; // Középre igazítás
    cartAnimationItem.style.top = `${buttonRect.top + buttonRect.height / 2 - 25}px`;

    // Végső pozíció (kosár ikon)
    const endX = cartRect.left + cartRect.width / 2 - 25;
    const endY = cartRect.top + cartRect.height / 2 - 25;

    // Animáció
    cartAnimationItem.animate([
        { transform: 'translate(0, 0) scale(1)', opacity: 1 },
        { transform: `translate(${endX - (buttonRect.left + buttonRect.width / 2 - 25)}px, ${endY - (buttonRect.top + buttonRect.height / 2 - 25)}px) scale(0.5)`, opacity: 0.5 }
    ], {
        duration: 800,
        easing: 'ease-in-out',
        fill: 'forwards'
    });

    // Animáció végeztével eltüntetjük az elemet
    setTimeout(() => {
        cartAnimationItem.style.display = 'none';
    }, 800);
}

// Toast megjelenítése
function showToast(message) {
    const toastBody = document.querySelector("#toast-added .toast-body");
    toastBody.textContent = message;
    const toastEl = document.getElementById("toast-added");
    toastEl.style.display = 'block';
    setTimeout(() => {
        toastEl.style.display = 'none';
    }, 3000);
}

// Toast bezárása
function closeToast() {
    const toastEl = document.getElementById("toast-added");
    toastEl.style.display = 'none';
}

// Esc billentyűvel való bezárás
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        document.querySelectorAll('.custom-modal').forEach(modal => {
            if (modal.style.display === 'block') {
                closeCustomModal(modal.id);
            }
        });
    }
});