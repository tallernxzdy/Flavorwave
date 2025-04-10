const hamburger = document.querySelector('.hamburger');
const menubar = document.querySelector('.menubar');

// Hamburger menü kezelése
hamburger.addEventListener('click', () => {
    menubar.classList.toggle('active');
    hamburger.classList.toggle('active');
});

function closeMenuOnResize() {
    if (window.innerWidth > 1200) {
        menubar.classList.remove('active');
        hamburger.classList.remove('active');
    }
}

window.addEventListener('resize', closeMenuOnResize);
closeMenuOnResize();

// Kosár számláló frissítése
function updateCartCount() {
    fetch("get_cart_count.php")
        .then(response => response.json())
        .then(data => {
            const cartCountElements = document.querySelectorAll('.cart-count');
            cartCountElements.forEach(element => {
                element.textContent = data.count;
            });
        })
        .catch(error => {
            console.error('Hiba a kosár számláló frissítése során:', error);
        });
}

// Oldal betöltésekor frissítjük a számlálót
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
});