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