// File: /js/scroll-animations.js
// Purpose: Scroll-triggered reveal animations for sections with class 'animated'

document.addEventListener("DOMContentLoaded", function () {
  const animatedElems = document.querySelectorAll(".animated");

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible");
          observer.unobserve(entry.target); // Animate once
        }
      });
    },
    {
      threshold: 0.15,
    }
  );

  animatedElems.forEach((elem) => observer.observe(elem));
});
