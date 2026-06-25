(function () {
  document.addEventListener('DOMContentLoaded', function () {
    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      return;
    }

    if (document.querySelector('[data-global-cursor-aura]')) {
      return;
    }

    var style = document.createElement('style');
    style.textContent = [
      '.global-cursor-trail-aura,.global-cursor-trail-dot{position:fixed;top:0;left:0;pointer-events:none;border-radius:9999px;transform:translate3d(-999px,-999px,0);opacity:0;transition:opacity 180ms ease;z-index:10000;}',
      '.global-cursor-trail-aura{width:30px;height:30px;background:radial-gradient(circle,rgba(16,128,255,.46) 0%,rgba(0,192,255,.16) 40%,transparent 72%);filter:blur(2px);mix-blend-mode:screen;}',
      '.global-cursor-trail-dot{width:10px;height:10px;background:rgba(255,255,255,.95);box-shadow:0 0 18px rgba(0,192,255,.58);mix-blend-mode:screen;}',
      '.global-cursor-trail-aura.is-active,.global-cursor-trail-dot.is-active{opacity:1;}',
      '.global-cursor-star{position:fixed;left:0;top:0;width:var(--star-size,7px);height:var(--star-size,7px);pointer-events:none;z-index:9999;color:var(--star-color,#fff);filter:drop-shadow(0 0 8px rgba(0,192,255,.85));transform:translate3d(var(--star-x),var(--star-y),0) rotate(var(--star-rotate,0deg)) scale(.5);animation:global-cursor-star-drift 760ms ease-out forwards;}',
      '.global-cursor-star::before,.global-cursor-star::after{content:"";position:absolute;inset:50% auto auto 50%;background:currentColor;border-radius:999px;transform:translate(-50%,-50%);}',
      '.global-cursor-star::before{width:100%;height:2px;}',
      '.global-cursor-star::after{width:2px;height:100%;}',
      '@keyframes global-cursor-star-drift{0%{opacity:0;transform:translate3d(var(--star-x),var(--star-y),0) rotate(var(--star-rotate,0deg)) scale(.35);}20%{opacity:1;}100%{opacity:0;transform:translate3d(calc(var(--star-x) + var(--star-dx)),calc(var(--star-y) + var(--star-dy)),0) rotate(calc(var(--star-rotate,0deg) + 95deg)) scale(1);}}'
    ].join('');
    document.head.appendChild(style);

    var aura = document.createElement('div');
    aura.className = 'global-cursor-trail-aura';
    aura.setAttribute('data-global-cursor-aura', '');
    aura.setAttribute('aria-hidden', 'true');

    var dot = document.createElement('div');
    dot.className = 'global-cursor-trail-dot';
    dot.setAttribute('data-global-cursor-dot', '');
    dot.setAttribute('aria-hidden', 'true');

    document.body.appendChild(aura);
    document.body.appendChild(dot);

    var targetX = window.innerWidth / 2;
    var targetY = window.innerHeight / 2;
    var currentX = targetX;
    var currentY = targetY;
    var active = false;
    var lastStarAt = 0;
    var starColors = ['#ffffff', '#00c0ff', '#1080ff', '#dff7ff'];

    function spawnStar(x, y) {
      var now = performance.now();
      if (now - lastStarAt < 38) {
        return;
      }
      lastStarAt = now;

      var star = document.createElement('span');
      var size = 4 + Math.random() * 8;
      var offsetX = (Math.random() - 0.5) * 34;
      var offsetY = (Math.random() - 0.5) * 34;
      var driftX = (Math.random() - 0.5) * 72;
      var driftY = -18 - Math.random() * 48;
      var color = starColors[Math.floor(Math.random() * starColors.length)];

      star.className = 'global-cursor-star';
      star.style.setProperty('--star-x', (x + offsetX) + 'px');
      star.style.setProperty('--star-y', (y + offsetY) + 'px');
      star.style.setProperty('--star-dx', driftX + 'px');
      star.style.setProperty('--star-dy', driftY + 'px');
      star.style.setProperty('--star-size', size + 'px');
      star.style.setProperty('--star-color', color);
      star.style.setProperty('--star-rotate', (Math.random() * 180) + 'deg');
      document.body.appendChild(star);
      star.addEventListener('animationend', function () {
        star.remove();
      }, { once: true });
    }

    function tick() {
      currentX += (targetX - currentX) * 0.18;
      currentY += (targetY - currentY) * 0.18;
      aura.style.transform = 'translate3d(' + (currentX - 15) + 'px,' + (currentY - 15) + 'px,0)';
      dot.style.transform = 'translate3d(' + (targetX - 5) + 'px,' + (targetY - 5) + 'px,0)';
      requestAnimationFrame(tick);
    }

    tick();

    window.addEventListener('pointermove', function (event) {
      targetX = event.clientX;
      targetY = event.clientY;
      if (!active) {
        active = true;
        aura.classList.add('is-active');
        dot.classList.add('is-active');
      }
      spawnStar(event.clientX, event.clientY);
    }, { passive: true });

    window.addEventListener('pointerleave', function () {
      aura.classList.remove('is-active');
      dot.classList.remove('is-active');
    });

    Array.prototype.forEach.call(document.querySelectorAll('.navbar-vertical a[href]'), function (link) {
      var linkPath = link.getAttribute('href').split('?')[0];
      var pagePath = window.location.pathname.split('/').pop();
      if (linkPath === pagePath) {
        link.classList.add('rto-nav-active');
      }
    });
  });
}());
