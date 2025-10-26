require('./bootstrap');

// resources/js/app.js
document.addEventListener('DOMContentLoaded', function () {
  function updateClock() {
    const now = new Date();
    const clockEl = document.getElementById('clock');
    const dayEl = document.getElementById('day');
    if (clockEl) {
      clockEl.innerText = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute:'2-digit', second:'2-digit' });
    }
    if (dayEl) {
      dayEl.innerText = now.toLocaleDateString('id-ID', { weekday: 'long', year:'numeric', month:'long', day:'numeric' });
    }
  }
  updateClock();
  setInterval(updateClock, 1000);
});