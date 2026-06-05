(function () {
  function parseSlides(el) {
    var raw = el.getAttribute('data-quote-slides');
    try {
      return raw ? JSON.parse(raw) : [];
    } catch (e) {
      return [];
    }
  }

  function prefersReducedMotion() {
    return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  document.querySelectorAll('.c-quote--slideshow').forEach(function (root) {
    var slides = parseSlides(root);
    if (slides.length < 2) return;

    var currentEl = root.querySelector('.c-quote__text--current');
    var nextEl = root.querySelector('.c-quote__text--next');
    if (!currentEl || !nextEl) return;

    var currentIndex = 0;
    var isTransitioning = false;
    var autoplayTimer = null;
    var AUTOPLAY_MS = 5000;

    function setText(el, html) {
      el.innerHTML = html;
    }

    function go(index) {
      if (index === currentIndex || isTransitioning || !slides[index]) return;

      isTransitioning = true;
      root.classList.add('c-quote--transitioning');
      setText(nextEl, slides[index].text || '');

      nextEl.classList.add('c-quote__text--fade-in');
      currentEl.classList.add('c-quote__text--fade-out');

      var done = function () {
        currentEl.removeEventListener('transitionend', done);
        isTransitioning = false;
        root.classList.remove('c-quote--transitioning');
        currentEl.classList.remove('c-quote__text--fade-out');
        nextEl.classList.remove('c-quote__text--fade-in');
        setText(currentEl, slides[index].text || '');
        currentIndex = index;
        nextEl.setAttribute('aria-hidden', 'true');
        currentEl.removeAttribute('aria-hidden');
        startAutoplay();
      };

      currentEl.addEventListener('transitionend', done);
    }

    function goNext() {
      go((currentIndex + 1) % slides.length);
    }

    function startAutoplay() {
      stopAutoplay();
      if (prefersReducedMotion() || root.classList.contains('c-quote--paused')) return;
      autoplayTimer = setInterval(goNext, AUTOPLAY_MS);
    }

    function stopAutoplay() {
      if (autoplayTimer) {
        clearInterval(autoplayTimer);
        autoplayTimer = null;
      }
    }

    root.addEventListener('mouseenter', function () {
      root.classList.add('c-quote--paused');
      stopAutoplay();
    });

    root.addEventListener('mouseleave', function () {
      root.classList.remove('c-quote--paused');
      startAutoplay();
    });

    startAutoplay();
  });
})();
