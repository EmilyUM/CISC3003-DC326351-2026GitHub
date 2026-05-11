const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const container = document.getElementById('container');

// 点击 "Sign Up" 按钮时，添加激活类
signUpButton.addEventListener('click', () => {
    container.classList.add("right-panel-active");
});

// 点击 "Sign In" 按钮时，移除激活类
signInButton.addEventListener('click', () => {
    container.classList.remove("right-panel-active");
});