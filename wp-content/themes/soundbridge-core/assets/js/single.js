document.addEventListener("DOMContentLoaded", () => {
  document
    .querySelectorAll("[data-program-testimonials]")
    .forEach((carousel) => {
      const track = carousel.querySelector(".sb-program-testimonials");
      const cards = Array.from(track?.children || []);
      const previousButton = carousel.querySelector("[data-carousel-previous]");
      const nextButton = carousel.querySelector("[data-carousel-next]");

      if (!track || cards.length < 2 || !previousButton || !nextButton) return;

      let currentIndex = 0;

      const cardsPerView = () =>
        window.matchMedia("(max-width: 800px)").matches ? 1 : 2;

      const updateCarousel = () => {
        const maximumIndex = Math.max(0, cards.length - cardsPerView());
        currentIndex = Math.min(currentIndex, maximumIndex);
        const offset = cards[currentIndex].offsetLeft - cards[0].offsetLeft;
        track.style.transform = `translateX(-${offset}px)`;
        previousButton.disabled = currentIndex === 0;
        nextButton.disabled = currentIndex === maximumIndex;

        cards.forEach((card, index) => {
          const isVisible =
            index >= currentIndex && index < currentIndex + cardsPerView();
          card.setAttribute("aria-hidden", String(!isVisible));
        });

        carousel.classList.toggle("is-static", maximumIndex === 0);
      };

      previousButton.addEventListener("click", () => {
        currentIndex = Math.max(0, currentIndex - 1);
        updateCarousel();
      });

      nextButton.addEventListener("click", () => {
        currentIndex = Math.min(
          cards.length - cardsPerView(),
          currentIndex + 1,
        );
        updateCarousel();
      });

      window.addEventListener("resize", updateCarousel);
      updateCarousel();
    });
});
