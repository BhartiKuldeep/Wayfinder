document.addEventListener('DOMContentLoaded', () => {
  const alerts = document.querySelectorAll('[data-auto-dismiss]');
  alerts.forEach((alert) => {
    setTimeout(() => alert.remove(), 4000);
  });
});
