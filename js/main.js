// Nderi Law & Associates — Main JS

document.addEventListener('DOMContentLoaded', function () {

  // ── AOS Init ──
  AOS.init({ duration: 700, once: true, offset: 60 });

  // ── Mobile hamburger ──
  const hamburger = document.getElementById('hamburger');
  const mobileMenu = document.getElementById('mobile-menu');

  hamburger.addEventListener('click', function () {
    mobileMenu.classList.toggle('open');
  });

  document.querySelectorAll('.mobile-menu a').forEach(function (link) {
    link.addEventListener('click', function () {
      mobileMenu.classList.remove('open');
    });
  });

  // ── Navbar shadow on scroll ──
  window.addEventListener('scroll', function () {
    document.getElementById('navbar').style.boxShadow =
      window.scrollY > 10
        ? '0 4px 20px rgba(44,26,14,0.14)'
        : '0 2px 12px rgba(44,26,14,0.07)';
  }, { passive: true });

  // ── Practice card tilt ──
  document.querySelectorAll('.p-card').forEach(function (card) {
    card.addEventListener('mousemove', function (e) {
      var r = card.getBoundingClientRect();
      var x = (e.clientX - r.left) / r.width - 0.5;
      var y = (e.clientY - r.top) / r.height - 0.5;
      card.style.transform = 'translateY(-5px) rotateX(' + (-y * 4) + 'deg) rotateY(' + (x * 4) + 'deg)';
    });
    card.addEventListener('mouseleave', function () {
      card.style.transform = '';
    });
  });

  // ── Contact form ──
  const form = document.getElementById('contact-form');
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      var btn = document.getElementById('submit-btn');
      var msg = document.getElementById('form-msg');
      var originalText = btn.textContent;

      btn.textContent = 'Sending…';
      btn.disabled = true;
      msg.style.display = 'none';
      msg.className = '';

      var data = new FormData(form);

      fetch('contact.php', { method: 'POST', body: data })
        .then(function (res) { return res.json(); })
        .then(function (json) {
          if (json.success) {
            btn.textContent = '✓ Enquiry Sent';
            msg.textContent = '✓ Thank you! Ann\'s team will be in touch within 1–2 business days.';
            msg.className = 'success';
            msg.style.display = 'block';
            form.reset();
            setTimeout(function () {
              btn.textContent = originalText;
              btn.disabled = false;
              msg.style.display = 'none';
            }, 6000);
          } else {
            throw new Error(json.error || 'Something went wrong.');
          }
        })
        .catch(function (err) {
          btn.textContent = originalText;
          btn.disabled = false;
          msg.textContent = '✗ ' + err.message + ' Please try emailing us directly.';
          msg.className = 'error';
          msg.style.display = 'block';
        });
    });
  }

});
