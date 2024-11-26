const currentPage = window.location.pathname.split('/').pop();
const links = document.querySelectorAll('.navbar_link a');

links.forEach(link => {
    if (link.getAttribute('href') === currentPage) {
        link.classList.add('active');
    }
});
