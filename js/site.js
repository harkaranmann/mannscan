/* Mann Scanning Centre — shared site behaviour */
(function () {
  'use strict';

  /* ---- Theme (light = sapphire, dark = midnight & gold) ---- */
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
    var current = document.documentElement.getAttribute('data-theme') || 'light';
    applyTheme(current === 'dark' ? 'light' : 'dark');
  };

  /* Initialise theme ASAP */
  var saved = 'light';
  try { saved = localStorage.getItem(STORAGE_KEY) || 'light'; } catch (e) {}
  applyTheme(saved);

  /* ---- Mobile nav ---- */
  window.toggleNav = function () {
    var header = document.querySelector('.site-header');
    if (header) header.classList.toggle('open');
  };

  document.addEventListener('DOMContentLoaded', function () {
    applyTheme(document.documentElement.getAttribute('data-theme') || 'light');

    /* Close mobile nav on link click */
    document.querySelectorAll('.nav-links a').forEach(function (a) {
      a.addEventListener('click', function () {
        var header = document.querySelector('.site-header');
        if (header) header.classList.remove('open');
      });
    });

    /* Reveal on scroll */
    var reveals = document.querySelectorAll('.reveal');
    if (reveals.length && !window.matchMedia('(prefers-reduced-motion: reduce)').matches && 'IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            io.unobserve(entry.target);
          }
        });
      }, { threshold: 0.12 });
      reveals.forEach(function (el) { io.observe(el); });
    } else {
      reveals.forEach(function (el) { el.classList.add('visible'); });
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
