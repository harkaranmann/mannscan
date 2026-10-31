/* Mann Scanning Centre — shared site behaviour + motion engine */
(function () {
  'use strict';

  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---- Theme (dark = obsidian & chrome, light = porcelain) ---- */
  var STORAGE_KEY = 'mann-theme';
  function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    try { localStorage.setItem(STORAGE_KEY, theme); } catch (e) {}
    document.querySelectorAll('[data-theme-label]').forEach(function (el) {
      el.textContent = theme === 'dark' ? 'Light' : 'Dark';
    });
    document.querySelectorAll('.theme-icon-sun').forEach(function (el) {
      el.style.display = theme === 'dark' ? 'block' : 'none';
    });
    document.querySelectorAll('.theme-icon-moon').forEach(function (el) {
      el.style.display = theme === 'dark' ? 'none' : 'block';
    });
  }
  window.toggleTheme = function () {
    var current = document.documentElement.getAttribute('data-theme') || 'dark';
    applyTheme(current === 'dark' ? 'light' : 'dark');
  };

  /* Initialise theme ASAP */
  var saved = 'dark';
  try { saved = localStorage.getItem(STORAGE_KEY) || 'dark'; } catch (e) {}
  applyTheme(saved);

  /* ---- Soundtrack (on by default, visitor can mute; browsers block audible autoplay, so it starts on the visitor's first gesture) ---- */
  var SOUND_KEY = 'mann-sound', SOUND_POS = 'mann-sound-pos', VOL = 0.5;
  var music, soundBtn, fadeTimer;
  function fadeTo(target, done) {
    clearInterval(fadeTimer);
    fadeTimer = setInterval(function () {
      var v = music.volume + (target > music.volume ? 0.02 : -0.02);
      if (Math.abs(target - v) < 0.02) { music.volume = target; clearInterval(fadeTimer); if (done) done(); }
      else music.volume = v;
    }, 40);
  }
  function setSound(on) {
    try { localStorage.setItem(SOUND_KEY, on ? 'on' : 'off'); } catch (e) {}
    soundBtn.setAttribute('aria-pressed', on ? 'true' : 'false');
    soundBtn.classList.toggle('is-on', on);
    if (on) {
      music.volume = 0;
      var p = music.play();
      if (p && p.catch) p.catch(function () {});
      fadeTo(VOL);
    } else {
      fadeTo(0, function () { music.pause(); });
    }
  }
  function buildSound() {
    var themeBtn = document.querySelector('.icon-btn[onclick="toggleTheme()"]');
    if (!themeBtn) return;
    music = new Audio('audio/soundtrack.mp3');
    music.loop = true;
    music.preload = 'none';
    soundBtn = document.createElement('button');
    soundBtn.type = 'button';
    soundBtn.className = 'icon-btn sound-btn';
    soundBtn.setAttribute('aria-label', 'Soundtrack');
    soundBtn.setAttribute('aria-pressed', 'false');
    soundBtn.title = 'Soundtrack';
    soundBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5 6 9H2v6h4l5 4V5z"/><path class="sound-wave" d="M15.5 8.5a5 5 0 0 1 0 7M19 5a10 10 0 0 1 0 14"/><path class="sound-mute" d="m22 9-6 6M16 9l6 6"/></svg>';
    soundBtn.addEventListener('click', function () { setSound(soundBtn.getAttribute('aria-pressed') !== 'true'); });
    themeBtn.parentNode.insertBefore(soundBtn, themeBtn);

    // Carry playback position across page navigations.
    music.addEventListener('loadedmetadata', function () {
      try { var pos = parseFloat(sessionStorage.getItem(SOUND_POS)); if (pos < music.duration) music.currentTime = pos; } catch (e) {}
    }, { once: true });
    window.addEventListener('pagehide', function () {
      try { sessionStorage.setItem(SOUND_POS, String(music.currentTime || 0)); } catch (e) {}
    });

    var pref = null;
    try { pref = localStorage.getItem(SOUND_KEY); } catch (e) {}
    if (pref !== 'off') {
      // On by default; browsers block audible autoplay, so start on the first interaction with this page.
      soundBtn.setAttribute('aria-pressed', 'true');
      soundBtn.classList.add('is-on');
      var resume = function (e) {
        ['pointerdown', 'keydown'].forEach(function (t) { document.removeEventListener(t, resume, true); });
        if (soundBtn.contains(e.target)) return; // the button's own click handles it
        setSound(true);
      };
      ['pointerdown', 'keydown'].forEach(function (t) { document.addEventListener(t, resume, true); });
    }
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', buildSound);
  else buildSound();

  /* ---- Mobile nav ---- */
  window.toggleNav = function () {
    var header = document.querySelector('.site-header');
    if (header) header.classList.toggle('open');
  };

  /* ---- Liquid page transition ---- */
  var PT_KEY = 'mann-pt';
  var pt, ptPath;
  function ease(t) { return t < .5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2; }
  // Edge with a viscous bulge that peaks mid-flight, flattening as it settles.
  function edge(y, t) { return 'Q50 ' + (y - Math.sin(Math.PI * t) * 28).toFixed(2) + ' 100 ' + y.toFixed(2); }
  function animate(dur, frame, done) {
    var start = null;
    function step(ts) {
      if (start === null) start = ts;
      var t = Math.min(1, (ts - start) / dur);
      frame(ease(t), t);
      if (t < 1) requestAnimationFrame(step); else if (done) done();
    }
    requestAnimationFrame(step);
  }
  function cover(done) {
    pt.classList.add('busy');
    animate(720, function (e, t) {
      var y = 100 - 100 * e;
      ptPath.setAttribute('d', 'M0 100 L0 ' + y.toFixed(2) + ' ' + edge(y, t) + ' L100 100 Z');
    }, done);
  }
  function uncover() {
    animate(820, function (e, t) {
      var y = 100 - 100 * e;
      ptPath.setAttribute('d', 'M0 0 L0 ' + y.toFixed(2) + ' ' + edge(y, t) + ' L100 0 Z');
    }, function () { pt.classList.remove('busy'); ptPath.setAttribute('d', ''); });
  }
  function buildOverlay() {
    pt = document.createElement('div');
    pt.className = 'pt';
    pt.setAttribute('aria-hidden', 'true');
    pt.innerHTML = '<svg viewBox="0 0 100 100" preserveAspectRatio="none"><path d=""/></svg>' +
      // goo filter used by .goo blobs
      '<svg width="0" height="0" style="position:absolute"><defs><filter id="goo"><feGaussianBlur in="SourceGraphic" stdDeviation="22" result="b"/><feColorMatrix in="b" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 28 -12"/></filter></defs></svg>';
    document.body.appendChild(pt);
    ptPath = pt.querySelector('path');
  }
  function isInternal(a) {
    if (!a || a.target || a.hasAttribute('download')) return false;
    var href = a.getAttribute('href') || '';
    if (!href || href.charAt(0) === '#' || /^(mailto|tel|javascript):/i.test(href)) return false;
    var url = new URL(a.href, location.href);
    if (url.origin !== location.origin) return false;
    if (url.pathname === location.pathname && url.hash) return false;
    return /\.html?$|\/$/.test(url.pathname);
  }

  /* ---- Helpers ---- */
  function splitWords(el) {
    var i = 0;
    (function walk(node) {
      Array.prototype.slice.call(node.childNodes).forEach(function (child) {
        if (child.nodeType === 3) {
          var frag = document.createDocumentFragment();
          child.textContent.split(/(\s+)/).forEach(function (part) {
            if (!part) return;
            if (/^\s+$/.test(part)) { frag.appendChild(document.createTextNode(part)); return; }
            var w = document.createElement('span'); w.className = 'w';
            var inner = document.createElement('span'); inner.textContent = part; inner.style.setProperty('--i', i++);
            w.appendChild(inner); frag.appendChild(w);
          });
          child.parentNode.replaceChild(frag, child);
        } else if (child.nodeType === 1 && child.tagName !== 'BR') {
          walk(child);
        }
      });
    })(el);
  }

  function loadVideo(v) {
    if (v.dataset.src && !v.src) { v.src = v.dataset.src; v.load(); }
  }
  function playVideo(v) {
    var p = v.play(); if (p && p.catch) p.catch(function () {});
  }

  document.addEventListener('DOMContentLoaded', function () {
    applyTheme(document.documentElement.getAttribute('data-theme') || 'dark');
    buildOverlay();

    if (!reduced) {
      var arriving = false;
      try { arriving = sessionStorage.getItem(PT_KEY) === '1'; sessionStorage.removeItem(PT_KEY); } catch (e) {}
      if (arriving) { pt.classList.add('busy'); ptPath.setAttribute('d', 'M0 0 H100 V100 H0 Z'); uncover(); }

      document.addEventListener('click', function (e) {
        if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        var a = e.target.closest('a');
        if (!isInternal(a)) return;
        e.preventDefault();
        try { sessionStorage.setItem(PT_KEY, '1'); } catch (err) {}
        cover(function () { location.href = a.href; });
      });
    }
    window.addEventListener('pageshow', function (e) {
      if (e.persisted && pt) { pt.classList.remove('busy'); ptPath.setAttribute('d', ''); }
    });

    var header = document.querySelector('.site-header');

    /* Close mobile nav on link click */
    document.querySelectorAll('.nav-links a').forEach(function (a) {
      a.addEventListener('click', function () { if (header) header.classList.remove('open'); });
    });

    /* Interior heroes: liquid blobs + chrome film loop */
    document.querySelectorAll('.page-hero .hero__bg').forEach(function (bg) {
      var v = document.createElement('video');
      v.muted = true; v.loop = true; v.playsInline = true; v.autoplay = !reduced;
      v.setAttribute('aria-hidden', 'true');
      v.poster = 'videos/liquid.jpg';
      v.dataset.src = 'videos/liquid.mp4';
      v.className = 'lazy-video';
      bg.appendChild(v);
      var goo = document.createElement('div');
      goo.className = 'goo';
      goo.innerHTML = '<span></span><span></span><span></span>';
      bg.appendChild(goo);
    });

    /* Split headline words */
    document.querySelectorAll('.split, .page-hero h1').forEach(function (el) {
      el.classList.add('split');
      splitWords(el);
      requestAnimationFrame(function () { requestAnimationFrame(function () { el.classList.add('in'); }); });
    });

    /* Lazy / in-view videos (the showcase manages its own films) */
    var vids = Array.prototype.filter.call(
      document.querySelectorAll('video.lazy-video, video[data-src]'),
      function (v) { return !v.closest('.reel__stage'); });
    if ('IntersectionObserver' in window) {
      var vio = new IntersectionObserver(function (entries) {
        entries.forEach(function (en) {
          var v = en.target;
          if (en.isIntersecting) {
            loadVideo(v);
            if (!reduced) playVideo(v);
          } else if (!v.paused) { v.pause(); }
        });
      }, { rootMargin: '200px 0px' });
      vids.forEach(function (v) { vio.observe(v); });
    } else {
      vids.forEach(function (v) { loadVideo(v); if (!reduced) playVideo(v); });
    }

    /* Scroll-driven vars: film parallax, expanding frame, header state */
    var film = document.querySelector('.film');
    var expands = document.querySelectorAll('.expand');
    // Hero film plays only when motion is welcome; otherwise the poster stays.
    var filmVideo = film && film.querySelector('.film__video');
    if (filmVideo && !reduced) {
      filmVideo.preload = 'auto';
      var fp = filmVideo.play(); if (fp && fp.catch) fp.catch(function () {});
    }
    if (film && header) header.classList.add('is-over-film');
    var ticking = false;
    function onScroll() {
      ticking = false;
      var vh = window.innerHeight;
      if (header) header.classList.toggle('scrolled', window.scrollY > (film ? vh * .7 : 10));
      if (reduced) return;
      if (film) {
        var p = Math.min(1, Math.max(0, window.scrollY / vh));
        film.style.setProperty('--p', p.toFixed(3));
      }
      expands.forEach(function (ex) {
        var r = ex.getBoundingClientRect();
        var e = Math.min(1, Math.max(0, -r.top / (r.height - vh) * 1.4));
        ex.style.setProperty('--e', e.toFixed(3));
      });
    }
    window.addEventListener('scroll', function () {
      if (!ticking) { ticking = true; requestAnimationFrame(onScroll); }
    }, { passive: true });
    onScroll();

    /* Cursor spotlight on cards + liquid fill origin on buttons */
    document.addEventListener('pointermove', function (e) {
      var c = e.target.closest && e.target.closest('.card, .stat-tile');
      if (c) {
        var r = c.getBoundingClientRect();
        c.style.setProperty('--mx', (e.clientX - r.left) + 'px');
        c.style.setProperty('--my', (e.clientY - r.top) + 'px');
      }
    }, { passive: true });
    document.querySelectorAll('.btn').forEach(function (b) {
      b.addEventListener('pointerenter', function (e) {
        var r = b.getBoundingClientRect();
        b.style.setProperty('--bx', (e.clientX - r.left) + 'px');
        b.style.setProperty('--by', (e.clientY - r.top) + 'px');
      });
    });

    /* Modality showcase: liquid circle-morph between machine films */
    document.querySelectorAll('.reel').forEach(function (reel) {
      var items = Array.prototype.slice.call(reel.querySelectorAll('.reel__item'));
      var videos = Array.prototype.slice.call(reel.querySelectorAll('.reel__stage video'));
      var stage = reel.querySelector('.reel__stage');
      var tag = reel.querySelector('.reel__tag');
      var current = -1, DUR = 7000;
      // One clock drives both rotation and the CSS progress bar, so they never drift apart.
      var timer = null, remaining = DUR, startedAt = 0, running = false;
      var visible = !('IntersectionObserver' in window), hovering = false, offTimer = null;
      reel.style.setProperty('--dur', DUR + 'ms');

      function next() { show((current + 1) % items.length); }
      function sync() {
        var should = !reduced && visible && !hovering && current >= 0;
        reel.classList.toggle('paused', !should);
        if (should && !running) {
          running = true; startedAt = Date.now();
          timer = setTimeout(next, remaining);
          if (videos[current]) playVideo(videos[current]);
        } else if (!should && running) {
          running = false; clearTimeout(timer);
          remaining = Math.max(0, remaining - (Date.now() - startedAt));
          if (!visible && videos[current]) videos[current].pause();
        }
      }

      function show(i, x) {
        if (i === current) return;
        stage.style.setProperty('--cx', x || '50%');
        clearTimeout(offTimer);
        var prev = videos[current];
        videos.forEach(function (v, k) {
          v.classList.remove('off');
          if (v === prev) { v.classList.remove('on'); v.classList.add('off'); }
          else if (k !== i) { v.classList.remove('on'); v.pause(); }
        });
        // The outgoing film stays visible under the circle wipe; pause it once the wipe ends.
        if (prev) offTimer = setTimeout(function () { if (prev.classList.contains('off')) prev.pause(); }, 1400);
        var v = videos[i];
        loadVideo(v);
        v.currentTime = 0;
        v.classList.add('on');
        items.forEach(function (it, k) {
          var on = k === i;
          it.classList.toggle('active', on);
          it.setAttribute('aria-selected', on ? 'true' : 'false');
          it.tabIndex = on ? 0 : -1;
        });
        stage.setAttribute('aria-labelledby', items[i].id);
        if (tag) tag.textContent = items[i].dataset.tag || '';
        current = i;
        running = false; clearTimeout(timer); remaining = DUR;
        if (!reduced && visible && !hovering) playVideo(v);
        sync();
      }

      items.forEach(function (it, k) {
        it.addEventListener('click', function (e) {
          var r = it.getBoundingClientRect();
          show(k, (e.clientX ? Math.round((e.clientX - r.left) / r.width * 100) : 50) + '%');
        });
        it.addEventListener('keydown', function (e) {
          var to = null;
          if (e.key === 'ArrowRight' || e.key === 'ArrowDown') to = (k + 1) % items.length;
          else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') to = (k - 1 + items.length) % items.length;
          else if (e.key === 'Home') to = 0;
          else if (e.key === 'End') to = items.length - 1;
          else if (e.key === 'Enter' || e.key === ' ') to = k;
          if (to === null) return;
          e.preventDefault();
          show(to);
          items[to].focus();
        });
      });
      // Mouse only: touch devices emulate mouseenter and would leave the reel frozen.
      reel.addEventListener('pointerenter', function (e) { if (e.pointerType === 'mouse') { hovering = true; sync(); } });
      reel.addEventListener('pointerleave', function (e) { if (e.pointerType === 'mouse') { hovering = false; sync(); } });

      // Nothing loads or rotates until the showcase is on screen.
      items.forEach(function (it, k) { it.tabIndex = k === 0 ? 0 : -1; });
      if (!visible) {
        new IntersectionObserver(function (entries) {
          visible = entries[0].isIntersecting;
          if (visible && current < 0) show(0); else sync();
        }, { threshold: 0.25 }).observe(reel);
      } else {
        show(0);
      }
    });

    /* Count-up numbers */
    var nums = document.querySelectorAll('[data-count]');
    function count(el) {
      var to = parseFloat(el.dataset.count), suffix = el.dataset.suffix || '';
      if (reduced) { el.textContent = to + suffix; return; }
      animate(1600, function (e) { el.textContent = Math.round(to * e) + suffix; });
    }

    /* Reveal on scroll */
    var reveals = document.querySelectorAll('.reveal');
    if (!reduced && 'IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            entry.target.querySelectorAll('[data-count]').forEach(count);
            if (entry.target.hasAttribute('data-count')) count(entry.target);
            io.unobserve(entry.target);
          }
        });
      }, { threshold: 0.12 });
      reveals.forEach(function (el) { io.observe(el); });
      nums.forEach(function (n) { if (!n.closest('.reveal')) io.observe(n); });
    } else {
      reveals.forEach(function (el) { el.classList.add('visible'); });
      nums.forEach(count);
    }
  });

  /* ---- LMP modal (home) ---- */
  window.openLMPModal = function () { var m = document.getElementById('lmpModal'); if (m) m.classList.add('show'); };
  window.closeLMPModal = function () { var m = document.getElementById('lmpModal'); if (m) m.classList.remove('show'); };
  document.addEventListener('click', function (e) {
    var m = document.getElementById('lmpModal');
    if (m && e.target === m) m.classList.remove('show');
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      var m = document.getElementById('lmpModal');
      if (m) m.classList.remove('show');
      var header = document.querySelector('.site-header');
      if (header) header.classList.remove('open');
    }
  });
})();
