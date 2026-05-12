(() => {
  const form = document.querySelector('form[data-form="contact"]');
  if (!form) return;

  form.addEventListener('submit', (e) => {
    const name = form.querySelector('input[name="name"]');
    const email = form.querySelector('input[name="email"]');
    const subject = form.querySelector('input[name="subject"]');
    const message = form.querySelector('textarea[name="message"]');
    if (!name || !email || !subject || !message) return;

    if (!name.value.trim()) {
      e.preventDefault();
      alert('Name is required.');
      name.focus();
      return;
    }
    if (!email.value.trim() || !email.value.includes('@')) {
      e.preventDefault();
      alert('Valid email is required.');
      email.focus();
      return;
    }
    if (!subject.value.trim()) {
      e.preventDefault();
      alert('Subject is required.');
      subject.focus();
      return;
    }
    if (message.value.trim().length < 10) {
      e.preventDefault();
      alert('Message must be at least 10 characters.');
      message.focus();
      return;
    }

    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;
  });
})();

