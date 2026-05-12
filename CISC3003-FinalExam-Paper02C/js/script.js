(() => {
  const byId = (id) => document.getElementById(id);

  const btnSignup = byId('btnSignup');
  const btnSignin = byId('btnSignin');
  const panelSignup = byId('panelSignup');
  const panelSignin = byId('panelSignin');

  const show = (panel) => {
    if (panelSignup) panelSignup.hidden = panel !== panelSignup;
    if (panelSignin) panelSignin.hidden = panel !== panelSignin;
  };

  if (btnSignup && btnSignin && panelSignup && panelSignin) {
    btnSignup.addEventListener('click', () => show(panelSignup));
    btnSignin.addEventListener('click', () => show(panelSignin));
  }

  const registerForm = document.querySelector('form[data-form="register"]');
  const loginForm = document.querySelector('form[data-form="login"]');

  const validateEmail = (v) => typeof v === 'string' && v.includes('@') && v.includes('.');
  const minLen = (v, n) => typeof v === 'string' && v.trim().length >= n;

  const emailStatus = byId('email_status');
  const setEmailStatus = (text, ok) => {
    if (!emailStatus) return;
    emailStatus.textContent = text;
    emailStatus.className = `status ${ok ? 'ok' : 'bad'}`;
  };

  const checkEmailAvailability = async (email) => {
    if (!validateEmail(email)) {
      setEmailStatus('Please enter a valid email.', false);
      return false;
    }
    try {
      const res = await fetch(`check_email.php?email=${encodeURIComponent(email)}`, {
        headers: { 'Accept': 'application/json' },
      });
      const data = await res.json();
      if (data && data.available === true) {
        setEmailStatus('Email is available.', true);
        return true;
      }
      setEmailStatus('Email is already registered.', false);
      return false;
    } catch {
      setEmailStatus('Email check failed.', false);
      return false;
    }
  };

  if (registerForm) {
    const emailInput = registerForm.querySelector('input[name="email"]');
    if (emailInput) {
      emailInput.addEventListener('blur', () => {
        checkEmailAvailability(emailInput.value.trim());
      });
    }

    registerForm.addEventListener('submit', async (e) => {
      const name = registerForm.querySelector('input[name="name"]');
      const email = registerForm.querySelector('input[name="email"]');
      const password = registerForm.querySelector('input[name="password"]');
      const confirm = registerForm.querySelector('input[name="confirm_password"]');

      if (!name || !email || !password || !confirm) return;

      if (!minLen(name.value, 2)) {
        e.preventDefault();
        alert('Name is required (min 2 chars).');
        name.focus();
        return;
      }
      if (!validateEmail(email.value.trim())) {
        e.preventDefault();
        alert('Valid email is required.');
        email.focus();
        return;
      }
      if (!minLen(password.value, 8)) {
        e.preventDefault();
        alert('Password must be at least 8 characters.');
        password.focus();
        return;
      }
      if (password.value !== confirm.value) {
        e.preventDefault();
        alert('Passwords do not match.');
        confirm.focus();
        return;
      }

      const ok = await checkEmailAvailability(email.value.trim());
      if (!ok) {
        e.preventDefault();
        email.focus();
        return;
      }
    });
  }

  if (loginForm) {
    loginForm.addEventListener('submit', (e) => {
      const email = loginForm.querySelector('input[name="email"]');
      const password = loginForm.querySelector('input[name="password"]');
      if (!email || !password) return;

      if (!validateEmail(email.value.trim())) {
        e.preventDefault();
        alert('Valid email is required.');
        email.focus();
        return;
      }
      if (!minLen(password.value, 8)) {
        e.preventDefault();
        alert('Password must be at least 8 characters.');
        password.focus();
        return;
      }
    });
  }
})();

