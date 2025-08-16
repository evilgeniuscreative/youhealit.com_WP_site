// File: /js/carousel.js
// Purpose: Controls the horizontal scrolling of the custom homepage carousel

document.addEventListener("DOMContentLoaded", function () {
  const carousel = document.querySelector(".carousel");
  const prevBtn = document.querySelector(".carousel-prev");
  const nextBtn = document.querySelector(".carousel-next");

  if (!carousel || !prevBtn || !nextBtn) return;

  prevBtn.addEventListener("click", () => {
    carousel.scrollBy({ left: -carousel.offsetWidth, behavior: "smooth" });
  });

  nextBtn.addEventListener("click", () => {
    carousel.scrollBy({ left: carousel.offsetWidth, behavior: "smooth" });
  });
});
