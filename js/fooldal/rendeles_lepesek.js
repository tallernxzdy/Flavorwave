
document.addEventListener("DOMContentLoaded", function() {
    const steps = document.querySelectorAll(".step");
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("show");
            }
        });
    }, { threshold: 0.5 });
    
    steps.forEach(step => observer.observe(step));
});