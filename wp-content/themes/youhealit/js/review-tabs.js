// File: /js/review-tabs.js
// Purpose: Handle tab switching and optional filtering for review tabs

document.addEventListener("DOMContentLoaded", function () {
  const tabLinks = document.querySelectorAll(".tab-nav a");

  tabLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const url = new URL(this.href);
      const tab = url.searchParams.get("review_tab");

      // Update URL without full reload
      const newUrl = `${window.location.pathname}?review_tab=${tab}`;
      if (window.location.search.includes("five_star=1")) {
        newUrl += "&five_star=1";
      }
      window.location.href = newUrl;
    });
  });
});
