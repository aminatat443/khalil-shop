const loginBtn = document.querySelector('.btn-login');
const userMenu = document.querySelector('.user-menu');
const logoutBtn = document.querySelector('.logout');
const dropdown = document.querySelector('.dropdown');

let timeout;

function updateUI() {
    const isLogged = localStorage.getItem('isLogged') === 'true';

    if (isLogged) {
        loginBtn.classList.add('hidden');
        userMenu.classList.remove('hidden');
    } else {
        loginBtn.classList.remove('hidden');
        userMenu.classList.add('hidden');
    }
}

loginBtn.addEventListener('click', () => {
    localStorage.setItem('isLogged', 'true');
    updateUI();
});

logoutBtn.addEventListener('click', () => {
    localStorage.removeItem('isLogged');
    updateUI();
});


const slides = document.querySelectorAll('.slide');
const nextBtn = document.querySelector('.next');
const prevBtn = document.querySelector('.prev');

let index = 0;

// afficher slide
function showSlide(i) {
    slides.forEach(slide => slide.classList.remove('active'));
    slides[i].classList.add('active');
}

// next
nextBtn.addEventListener('click', () => {
    index = (index + 1) % slides.length;
    showSlide(index);
});

// prev
prevBtn.addEventListener('click', () => {
    index = (index - 1 + slides.length) % slides.length;
    showSlide(index);
});

// auto slide
setInterval(() => {
    index = (index + 1) % slides.length;
    showSlide(index);
}, 4000);

// DROPDOWN USER MENU
userMenu.addEventListener('mouseenter', () => {
    dropdown.classList.add('show');
});

userMenu.addEventListener('mouseleave', () => {
    dropdown.classList.remove('show');
});

document.addEventListener('DOMContentLoaded', updateUI);