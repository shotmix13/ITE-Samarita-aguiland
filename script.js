document.addEventListener('DOMContentLoaded', function() {
// Signup and Login js
const formOpenBtn = document.querySelector("#form-open"),
  home = document.querySelector(".home"),
  formContainer = document.querySelector(".form_container"),
  formCloseBtn = document.querySelector(".xicon"),
  signupBtn = document.querySelector("#signup"),
  loginBtn = document.querySelector("#login");

formOpenBtn.addEventListener("click", () => {
  home.classList.add("show");
  document.body.classList.add("no-scroll");
});

document.querySelectorAll(".xicon").forEach(btn => {
  btn.addEventListener("click", () => {
    home.classList.remove("show");
    document.body.classList.remove("no-scroll");
  });
});

// Close form when clicking outside for the Signup and Login
home.addEventListener("click", (e) => {
  if (e.target === home) {
    home.classList.remove("show");
    document.body.classList.remove("no-scroll");
  }
});

signupBtn.addEventListener("click", (e) => {
  e.preventDefault();
  formContainer.classList.add("active");
});
loginBtn.addEventListener("click", (e) => {
  e.preventDefault();
  formContainer.classList.remove("active");
});

// Login form submission via AJAX
const loginForm = document.getElementById('loginForm');
if (loginForm) {
  loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(loginForm);
    const submitBtn = loginForm.querySelector('.button');
    submitBtn.textContent = 'Logging in...';
    submitBtn.disabled = true;

    try {
      const response = await fetch('user.php', {
        method: 'POST',
        body: formData
      });
      const data = await response.json();
      
      if (data.status === 'success') {
        alert(data.message);
        loginForm.reset();
        home.classList.remove("show");
        document.body.classList.remove("no-scroll");
        // Redirect to dashboard if specified
        if (data.redirect) {
          window.location.href = data.redirect;
        }
      } else {
        alert(data.message);
      }
    } catch (error) {
      alert('Login failed. Please try again.');
    } finally {
      submitBtn.textContent = 'Login Now';
      submitBtn.disabled = false;
    }
  });
}

// Signup form submission via AJAX
const signupForm = document.getElementById('signupForm');
if (signupForm) {
  signupForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(signupForm);
    const submitBtn = signupForm.querySelector('.button');
    submitBtn.textContent = 'Signing up...';
    submitBtn.disabled = true;

    try {
      const response = await fetch('user.php', {
        method: 'POST',
        body: formData
      });
      const data = await response.json();
      
      if (data.status === 'success') {
        alert(data.message);
        signupForm.reset();
        // Switch to login form
        formContainer.classList.remove("active");
      } else {
        alert(data.message);
      }
    } catch (error) {
      alert('Registration failed. Please try again.');
    } finally {
      submitBtn.textContent = 'Signup Now';
      submitBtn.disabled = false;
    }
  });
}


// Card flip functionality
const cards = document.querySelectorAll('.card');
cards.forEach(card => {
  // Flip on hover for desktop
  card.addEventListener('mouseenter', () => {
    card.classList.add('is-flipped');
  });
  card.addEventListener('mouseleave', () => {
    card.classList.remove('is-flipped');
  });
  // Flip on click for mobile/touch devices (but not when clicking buttons)
  card.addEventListener('click', (e) => {
    if (!e.target.closest('.btn-white')) {
      card.classList.toggle('is-flipped');
    }
  });
});

// Booking Modal functionality
const bookingModal = document.getElementById("bookingModal");
const bookingTourName = document.getElementById("bookingTourName");
const bookingCloseBtn = document.querySelector(".booking-close-btn");
const bookingForm = document.getElementById("bookingForm");

// Get all btn-white buttons
const bookNowButtons = document.querySelectorAll(".btn-white");

console.log("Found book now buttons:", bookNowButtons.length);

// Add click event to each btn-white button
bookNowButtons.forEach((btn, index) => {
  console.log("Adding event to button", index);
  btn.addEventListener("click", (e) => {
    e.preventDefault();
    e.stopPropagation();
    console.log("Button clicked!", btn);
    // Get the tour name from the card
    const tourCard = btn.closest(".card");
    if (tourCard) {
      const tourName = tourCard.querySelector(".card-heading-color");
      if (tourName) {
        bookingTourName.textContent = `Tour: ${tourName.textContent}`;
      }
    }
    // Open the booking modal
    bookingModal.classList.add("show");
    document.body.classList.add("no-scroll");
    console.log("Modal should be open now");
  });
});

// Close booking modal
bookingCloseBtn.addEventListener("click", () => {
  bookingModal.classList.remove("show");
  document.body.classList.remove("no-scroll");
});

// Close modal when clicking outside
bookingModal.addEventListener("click", (e) => {
  if (e.target === bookingModal) {
    bookingModal.classList.remove("show");
    document.body.classList.remove("no-scroll");
  }
});

// Handle form submission
bookingForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  
  const formData = new FormData(bookingForm);
  const submitBtn = bookingForm.querySelector('.booking-submit-btn');
  submitBtn.textContent = 'Sending...';
  submitBtn.disabled = true;

  try {
    const response = await fetch('booking.php', {
      method: 'POST',
      body: formData
    });
    const data = await response.json();
    
    if (data.status === 'success') {
      alert(data.message);
      bookingModal.classList.remove("show");
      document.body.classList.remove("no-scroll");
      bookingForm.reset();
    } else {
      alert(data.message);
    }
  } catch (error) {
    alert('Booking failed. Please try again.');
  } finally {
    submitBtn.textContent = 'Confirm Booking';
    submitBtn.disabled = false;
  }
});


// Smooth scroll to section with centering for Navigation
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function(e) {
    e.preventDefault();
    const targetId = this.getAttribute('href');
    if (targetId === '#') return;
    
    const targetElement = document.querySelector(targetId);
    if (targetElement) {
// Get the nav height for the offset of it
      const navHeight = document.querySelector('.navigation').offsetHeight;
// Calculate the center position of the screen,
      const viewportHeight = window.innerHeight;
      const elementHeight = targetElement.offsetHeight;
// Calculate scroll position to center the section of the form
      const scrollPosition = targetElement.offsetTop - (viewportHeight / 2) + (elementHeight / 2);
      
      window.scrollTo({
        top: scrollPosition,
        behavior: 'smooth'
      });
    }
  });
});
});

// Password visibility toggle - global function
function togglePassword(inputId, toggleIcon) {
  const passwordInput = document.getElementById(inputId);
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    toggleIcon.classList.add('active');
  } else {
    passwordInput.type = 'password';
    toggleIcon.classList.remove('active');
  }
}
