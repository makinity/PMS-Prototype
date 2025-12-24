import './bootstrap';

// Landing Page
document.addEventListener('DOMContentLoaded', function() {
if (!document.getElementById('mainNav')) {
    return;
}
// Mobile menu functionality
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const mobileMenu = document.getElementById('mobileMenu');
const loginModalBtnMobile = document.getElementById('loginModalBtnMobile');
const accountActivationModalBtnMobile = document.getElementById('accountActivationModalBtnMobile');
const body = document.body;

function toggleMobileMenu() {
    const isActive = mobileMenu.classList.contains('active');
    
    if (isActive) {
        // Close menu
        mobileMenu.classList.remove('active');
        body.classList.remove('menu-open');
        mobileMenuBtn.querySelector('i').classList.remove('fa-times');
        mobileMenuBtn.querySelector('i').classList.add('fa-bars');
    } else {
        // Open menu
        mobileMenu.classList.add('active');
        body.classList.add('menu-open');
        mobileMenuBtn.querySelector('i').classList.remove('fa-bars');
        mobileMenuBtn.querySelector('i').classList.add('fa-times');
    }
}

if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleMobileMenu();
    });
    
    // Close mobile menu when clicking links
    mobileMenu.querySelectorAll('a, button').forEach(element => {
        element.addEventListener('click', function() {
            mobileMenu.classList.remove('active');
            body.classList.remove('menu-open');
            if (mobileMenuBtn) {
                mobileMenuBtn.querySelector('i').classList.remove('fa-times');
                mobileMenuBtn.querySelector('i').classList.add('fa-bars');
            }
        });
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!mobileMenu.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
            mobileMenu.classList.remove('active');
            body.classList.remove('menu-open');
            if (mobileMenuBtn) {
                mobileMenuBtn.querySelector('i').classList.remove('fa-times');
                mobileMenuBtn.querySelector('i').classList.add('fa-bars');
            }
        }
    });
    
    // Close mobile menu on scroll
    window.addEventListener('scroll', function() {
        if (mobileMenu && mobileMenu.classList.contains('active')) {
            mobileMenu.classList.remove('active');
            body.classList.remove('menu-open');
            if (mobileMenuBtn) {
                mobileMenuBtn.querySelector('i').classList.remove('fa-times');
                mobileMenuBtn.querySelector('i').classList.add('fa-bars');
            }
        }
    });
    
    // Close menu on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
            toggleMobileMenu();
        }
    });
}

// Sticky nav scroll effect
const nav = document.getElementById('mainNav');

window.addEventListener('scroll', function() {
    if (window.scrollY > 50) {
        nav.classList.add('scrolled');
    } else {
        nav.classList.remove('scrolled');
    }
});

// Scroll animations
const fadeElements = document.querySelectorAll('.fade-in-up');

const fadeInOnScroll = function() {
    fadeElements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const elementVisible = 100;
        
        if (elementTop < window.innerHeight - elementVisible) {
            element.classList.add('visible');
        }
    });
};

// Check on load and scroll
fadeInOnScroll();
window.addEventListener('scroll', fadeInOnScroll);

// Get modal elements
const loginModal = document.getElementById('loginModal');
const accountActivationModal = document.getElementById('accountActivationModal');
const passwordResetModal = document.getElementById('passwordResetModal');

// Get button elements
const loginModalBtns = document.querySelectorAll('#loginModalBtn, #loginModalBtn2');
const accountActivationModalBtns = document.querySelectorAll('#accountActivationModalBtn, #accountActivationModalBtn2, #accountActivationModalBtn3');
const openPasswordResetBtn = document.getElementById('openPasswordReset');
const openAccountActivationFromLogin = document.getElementById('openAccountActivationFromLogin');
const openLoginFromActivation = document.getElementById('openLoginFromActivation');
const openLoginFromReset = document.getElementById('openLoginFromReset');

// Modal instances (Flowbite)
const loginModalInstance = new Modal(loginModal);
const accountActivationModalInstance = new Modal(accountActivationModal);
const passwordResetModalInstance = new Modal(passwordResetModal);

const modalOverlays = document.querySelectorAll('.modal-overlay');
const updateModalBackdrop = () => {
    const anyOpen = Array.from(modalOverlays).some(modal => !modal.classList.contains('hidden'));
    body.classList.toggle('modal-open', anyOpen);
};

if (modalOverlays.length) {
    const modalObserver = new MutationObserver(updateModalBackdrop);
    modalOverlays.forEach(modal => {
        modalObserver.observe(modal, { attributes: true, attributeFilter: ['class'] });
    });
    updateModalBackdrop();
}

// Close mobile menu when opening modals
function closeMobileMenuForModal() {
    if (mobileMenu && mobileMenu.classList.contains('active')) {
        toggleMobileMenu();
    }
}

// Mobile modal buttons
if (loginModalBtnMobile) {
    loginModalBtnMobile.addEventListener('click', function() {
        closeMobileMenuForModal();
        loginModalInstance.show();
    });
}

if (accountActivationModalBtnMobile) {
    accountActivationModalBtnMobile.addEventListener('click', function() {
        closeMobileMenuForModal();
        accountActivationModalInstance.show();
    });
}

// Open login modal
loginModalBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        closeMobileMenuForModal();
        loginModalInstance.show();
    });
});

// Open account activation modal
accountActivationModalBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        closeMobileMenuForModal();
        accountActivationModalInstance.show();
    });
});

// Open password reset modal
if (openPasswordResetBtn) {
    openPasswordResetBtn.addEventListener('click', function(e) {
        e.preventDefault();
        closeMobileMenuForModal();
        loginModalInstance.hide();
        setTimeout(() => {
            passwordResetModalInstance.show();
        }, 300);
    });
}

// Switch from login to activation
if (openAccountActivationFromLogin) {
    openAccountActivationFromLogin.addEventListener('click', function(e) {
        e.preventDefault();
        closeMobileMenuForModal();
        loginModalInstance.hide();
        setTimeout(() => {
            accountActivationModalInstance.show();
        }, 300);
    });
}

// Switch from activation to login
if (openLoginFromActivation) {
    openLoginFromActivation.addEventListener('click', function(e) {
        e.preventDefault();
        closeMobileMenuForModal();
        accountActivationModalInstance.hide();
        setTimeout(() => {
            loginModalInstance.show();
        }, 300);
    });
}

// Switch from reset to login
if (openLoginFromReset) {
    openLoginFromReset.addEventListener('click', function(e) {
        e.preventDefault();
        closeMobileMenuForModal();
        passwordResetModalInstance.hide();
        setTimeout(() => {
            loginModalInstance.show();
        }, 300);
    });
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href !== '#') {
            e.preventDefault();
            closeMobileMenuForModal();
            const target = document.querySelector(href);
            if (target) {
                window.scrollTo({
                    top: target.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        }
    });
});
});


document.addEventListener('DOMContentLoaded', function() {
const activationForm = document.getElementById('activationForm');
if (!activationForm) {
    return;
}
// Get DOM elements
const verifyPhase = document.getElementById('activationPhaseVerify');
const passwordPhase = document.getElementById('activationPhasePassword');
const verifyBtn = document.getElementById('verifyAccountBtn');
const backBtn = document.getElementById('backToVerifyBtn');
const activateBtn = document.getElementById('activateAccountBtn');
const stepIndicators = document.querySelectorAll('.step-indicator');
const currentStepSpan = document.getElementById('currentStep');
const modalTitle = document.getElementById('modalTitle');
const employeeIdInput = document.getElementById('employee_id');
const emailInput = document.getElementById('act_email');
const passwordInput = document.getElementById('act_password');
const confirmPasswordInput = document.getElementById('password_confirmation');
const passwordStrengthBar = document.getElementById('passwordStrengthBar');
const passwordStrengthText = document.getElementById('passwordStrengthText');
const fullNameInput = document.getElementById('full_name');
const profilePhotoInput = document.getElementById('profilePhoto');
const profilePreviewImage = document.getElementById('profilePreviewImage');
const profilePreviewIcon = document.getElementById('profilePreviewIcon');
const removeProfilePhotoBtn = document.getElementById('removeProfilePhoto');

// Track current step
let currentStep = 1;
let verifiedData = null;

// Step 1: Verify Account
verifyBtn.addEventListener('click', function() {
// Get form values
const employeeId = employeeIdInput.value.trim();
const email = emailInput.value.trim();

// Validation
if (!employeeId) {
    showError(employeeIdInput, 'Employee ID is required');
    return;
}

if (!email) {
    showError(emailInput, 'Email is required');
    return;
}

// Validate Employee ID format
const employeeIdPattern = /^EMP-\d{4}-\d{5}$/;
if (!employeeIdPattern.test(employeeId)) {
    showError(employeeIdInput, 'Invalid format. Use EMP-YYYY-XXXXX');
    return;
}

// Validate email format
const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
if (!emailPattern.test(email)) {
    showError(emailInput, 'Please enter a valid email address');
    return;
}

// Show loading state
const originalText = verifyBtn.innerHTML;
verifyBtn.innerHTML = '<span class="loading-spinner"></span> Verifying...';
verifyBtn.disabled = true;

// Simulate API call to verify account
setTimeout(() => {
    // For demo purposes, we'll simulate a successful verification
    // In real application, this would be an API call
    const isVerified = Math.random() > 0.2; // 80% success rate for demo

    if (isVerified) {
        // Save verified data
        verifiedData = {
            employeeId: employeeId,
            email: email
        };

        // Move to next step
        goToStep(2);
    } else {
        // Show error
        alert('Account verification failed. Please check your Employee ID and email, or contact HR.');
    }

    // Reset button
    verifyBtn.innerHTML = originalText;
    verifyBtn.disabled = false;
}, 1500);
});

// Step 2: Back to Verify
backBtn.addEventListener('click', function() {
goToStep(1);
});

// Password strength checker
passwordInput.addEventListener('input', function() {
checkPasswordStrength(this.value);
});

// Profile photo preview
function resetProfilePhoto() {
if (profilePhotoInput) {
    profilePhotoInput.value = '';
}
if (profilePreviewImage) {
    profilePreviewImage.src = '';
    profilePreviewImage.classList.add('hidden');
}
if (profilePreviewIcon) {
    profilePreviewIcon.classList.remove('hidden');
}
if (removeProfilePhotoBtn) {
    removeProfilePhotoBtn.classList.add('hidden');
}
}

if (profilePhotoInput) {
profilePhotoInput.addEventListener('change', function() {
    const file = this.files && this.files[0];
    if (!file) {
        resetProfilePhoto();
        return;
    }

    if (!file.type.startsWith('image/')) {
        alert('Please select an image file.');
        resetProfilePhoto();
        return;
    }

    const reader = new FileReader();
    reader.onload = function(event) {
        if (profilePreviewImage) {
            profilePreviewImage.src = event.target.result;
            profilePreviewImage.classList.remove('hidden');
        }
        if (profilePreviewIcon) {
            profilePreviewIcon.classList.add('hidden');
        }
        if (removeProfilePhotoBtn) {
            removeProfilePhotoBtn.classList.remove('hidden');
        }
    };
    reader.readAsDataURL(file);
});
}

if (removeProfilePhotoBtn) {
removeProfilePhotoBtn.addEventListener('click', function() {
    resetProfilePhoto();
});
}

// Password visibility toggle function
window.togglePassword = function(inputId, button) {
const input = document.getElementById(inputId);
const icon = button.querySelector('i');

if (input.type === 'password') {
    input.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
} else {
    input.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
}
};

// Form submission
document.getElementById('activationForm').addEventListener('submit', function(e) {
e.preventDefault();

// Validate passwords
const password = passwordInput.value;
const confirmPassword = confirmPasswordInput.value;

if (!password) {
    showError(passwordInput, 'Password is required');
    return;
}

if (!confirmPassword) {
    showError(confirmPasswordInput, 'Please confirm your password');
    return;
}

if (password !== confirmPassword) {
    showError(confirmPasswordInput, 'Passwords do not match');
    return;
}

// Password strength validation
const strength = calculatePasswordStrength(password);
if (strength < 2) { // Less than "fair"
    alert('Please use a stronger password (at least 8 characters with letters and numbers)');
    return;
}

// Show loading state
const originalText = activateBtn.innerHTML;
activateBtn.innerHTML = '<span class="loading-spinner"></span> Activating...';
activateBtn.disabled = true;

// Simulate account activation
setTimeout(() => {
    // Success!
    alert(`Account activated successfully!\n\nEmployee ID: ${verifiedData.employeeId}\nEmail: ${verifiedData.email}\n\nYou can now login to the Performance Management System.`);

    // Reset form and close modal
    resetForm();
    
    // Close modal (using Flowbite)
    const modal = document.getElementById('accountActivationModal');
    const modalInstance = Modal.getInstance(modal);
    if (modalInstance) {
        modalInstance.hide();
    }

    // Reset button
    activateBtn.innerHTML = originalText;
    activateBtn.disabled = false;
}, 2000);
});

// Helper functions
function goToStep(step) {
currentStep = step;

// Update UI
if (step === 1) {
    verifyPhase.classList.remove('hidden');
    passwordPhase.classList.add('hidden');
    modalTitle.textContent = 'Activate PMS Account';
} else if (step === 2) {
    verifyPhase.classList.add('hidden');
    passwordPhase.classList.remove('hidden');
    modalTitle.textContent = 'Set Your Password';
    
    // Pre-fill email from verification
    if (verifiedData) {
        document.getElementById('act_password').focus();
    }
}

// Update step indicators
stepIndicators.forEach(indicator => {
    const stepNum = parseInt(indicator.getAttribute('data-step'));
    if (stepNum <= step) {
        indicator.classList.add('active');
    } else {
        indicator.classList.remove('active');
    }
});

// Update step text
currentStepSpan.textContent = step;
}

function checkPasswordStrength(password) {
const strength = calculatePasswordStrength(password);

// Update progress bar and text
passwordStrengthBar.className = 'h-1.5 rounded-full';
passwordStrengthBar.style.width = '0%';

switch(strength) {
    case 0:
        passwordStrengthText.textContent = 'None';
        break;
    case 1:
        passwordStrengthText.textContent = 'Weak';
        passwordStrengthBar.classList.add('weak');
        break;
    case 2:
        passwordStrengthText.textContent = 'Fair';
        passwordStrengthBar.classList.add('fair');
        break;
    case 3:
        passwordStrengthText.textContent = 'Good';
        passwordStrengthBar.classList.add('good');
        break;
    case 4:
        passwordStrengthText.textContent = 'Strong';
        passwordStrengthBar.classList.add('strong');
        break;
}
}

function calculatePasswordStrength(password) {
let strength = 0;

if (password.length >= 8) strength++;
if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
if (/\d/.test(password)) strength++;
if (/[^A-Za-z0-9]/.test(password)) strength++;

return strength;
}

function showError(inputElement, message) {
// Add error styling
inputElement.classList.add('border-red-500');

// Show error message
let errorDiv = inputElement.nextElementSibling;
if (!errorDiv || !errorDiv.classList.contains('error-message')) {
    errorDiv = document.createElement('div');
    errorDiv.className = 'error-message text-xs text-red-400 mt-1';
    inputElement.parentNode.insertBefore(errorDiv, inputElement.nextSibling);
}
errorDiv.textContent = message;

// Focus the input
inputElement.focus();

// Remove error after 3 seconds
setTimeout(() => {
    inputElement.classList.remove('border-red-500');
    if (errorDiv && errorDiv.parentNode) {
        errorDiv.remove();
    }
}, 3000);
}

function resetForm() {
// Reset form inputs
employeeIdInput.value = '';
emailInput.value = '';
if (fullNameInput) {
    fullNameInput.value = '';
}
passwordInput.value = '';
confirmPasswordInput.value = '';
resetProfilePhoto();

// Reset steps
goToStep(1);
verifiedData = null;

// Reset password strength meter
passwordStrengthBar.style.width = '0%';
passwordStrengthText.textContent = 'None';
passwordStrengthBar.className = 'h-1.5 rounded-full';
}

// Initialize
goToStep(1);
});
// End Landing Page



