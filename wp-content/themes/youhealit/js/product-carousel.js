// File: /js/product-carousel.js
// Purpose: Optional JS for product carousel behavior (basic scroll buttons or swipe)

document.addEventListener("DOMContentLoaded", function () {
  const carousel = document.querySelector(".product-carousel");
  if (!carousel) return;

  // Optional: Add left/right scroll buttons if needed
  // Example placeholder functions:
  window.scrollCarouselLeft = function () {
    carousel.scrollBy({ left: -300, behavior: "smooth" });
  };

  window.scrollCarouselRight = function () {
    carousel.scrollBy({ left: 300, behavior: "smooth" });
  };
});
