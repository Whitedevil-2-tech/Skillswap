document.addEventListener('DOMContentLoaded', () => {
  const $ = s => document.querySelector(s);

  // Auto-dismiss flash messages
  const flash = $('.flash.ok');
  if (flash) setTimeout(() => { flash.style.opacity = 0; setTimeout(() => flash.remove(), 500); }, 3500);

  // Show / hide password(s)
  const toggle = $('#showPw');
  if (toggle) toggle.addEventListener('change', () =>
    document.querySelectorAll('input[type=password],input[data-pw]').forEach(i => {
      i.type = toggle.checked ? 'text' : 'password'; i.dataset.pw = 1;
    }));

  // Password strength meter
  const pw = $('#pw'), bar = $('#meterBar');
  if (pw && bar) pw.addEventListener('input', () => {
    const v = pw.value;
    const score = [v.length >= 8, /[A-Z]/.test(v), /[0-9]/.test(v), /[^A-Za-z0-9]/.test(v)].filter(Boolean).length;
    bar.style.width = score * 25 + '%';
    bar.style.background = ['#e5484d', '#e5484d', '#ffb800', '#00c2a8', '#00a085'][score];
  });

  // Client-side form validation (empty fields, email format, password match)
  document.querySelectorAll('form.form').forEach(form => {
    form.addEventListener('submit', ev => {
      let ok = true;
      form.querySelectorAll('[required]').forEach(f => {
        const bad = !f.value.trim(); f.classList.toggle('bad', bad); if (bad) ok = false;
      });
      const email = form.querySelector('[type=email]');
      if (email && email.value && !/^\S+@\S+\.\S+$/.test(email.value)) { email.classList.add('bad'); ok = false; }
      const p1 = $('#pw'), p2 = $('#pw2');
      if (p1 && p2 && p1.value !== p2.value) { p2.classList.add('bad'); alert('Passwords do not match.'); ok = false; }
      if (p1 && p2 && p1.value.length < 8) { p1.classList.add('bad'); ok = false; }
      if (!ok) ev.preventDefault();
    });
  });

  // Instant live filter on browse board (server-side search still works on submit)
  const live = $('#liveSearch');
  if (live) live.addEventListener('input', () => {
    const k = live.value.toLowerCase();
    document.querySelectorAll('.card').forEach(c => c.style.display = c.dataset.text.includes(k) ? '' : 'none');
  });
});
