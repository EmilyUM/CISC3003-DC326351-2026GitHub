(() => {
  const form = document.querySelector('form[data-form="p2a"]');
  if (!form) return;

  form.addEventListener('submit', (e) => {
    const email = form.querySelector('input[name="email"]');
    const bio = form.querySelector('textarea[name="bio"]');
    const agree = form.querySelector('input[name="agree_terms"]');

    if (!email || !bio || !agree) return;

    if (!email.value.trim() || !email.value.includes('@')) {
      e.preventDefault();
      alert('Please enter a valid email.');
      email.focus();
      return;
    }

    if (bio.value.trim().length < 10) {
      e.preventDefault();
      alert('Bio must be at least 10 characters.');
      bio.focus();
      return;
    }

    if (!agree.checked) {
      e.preventDefault();
      alert('You must agree to the terms.');
      agree.focus();
      return;
    }

    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;
  });
})();

