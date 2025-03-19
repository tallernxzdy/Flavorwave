// Navbar Toggle
const hamburger = document.querySelector('.hamburger');
const menubar = document.querySelector('.menubar');

hamburger.addEventListener('click', () => {
    menubar.classList.toggle('active');
    hamburger.classList.toggle('active');
});

// Bezárja a menüt, ha az oldal szélessége nagyobb, mint a töréspont
function closeMenuOnResize() {
    if (window.innerWidth > 1200) { // Ugyanaz a töréspont, mint a CSS-ben
        menubar.classList.remove('active');
        hamburger.classList.remove('active');
    }
}

// Hozzáad egy eseményfigyelőt a window resize eseményre
window.addEventListener('resize', closeMenuOnResize);

// Hívjuk meg a függvényt az oldal betöltésekor, hogy ellenőrizzük az állapotot
closeMenuOnResize();