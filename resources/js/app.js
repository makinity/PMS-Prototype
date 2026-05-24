import './bootstrap';

const showAuthSnackbar = (type, message) => {
    if (window.PMSnackbar) {
        window.PMSnackbar.clear();
        window.PMSnackbar.show({
            type,
            message,
            duration: type === 'error' ? 4500 : 3200,
        });
        return;
    }

    console[type === 'error' ? 'error' : 'log'](message);
};

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

function setMobileMenuState(isOpen) {
    if (!mobileMenuBtn || !mobileMenu) {
        return;
    }

    mobileMenu.classList.toggle('active', isOpen);
    mobileMenu.setAttribute('aria-hidden', String(!isOpen));
    body.classList.toggle('menu-open', isOpen);
    mobileMenuBtn.setAttribute('aria-expanded', String(isOpen));

    const icon = mobileMenuBtn.querySelector('i');
    if (!icon) {
        return;
    }

    icon.classList.toggle('fa-bars', !isOpen);
    icon.classList.toggle('fa-times', isOpen);
}

function toggleMobileMenu() {
    setMobileMenuState(!mobileMenu.classList.contains('active'));
}

function closeMobileMenu() {
    setMobileMenuState(false);
}

if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleMobileMenu();
    });

    // Close mobile menu when clicking links
    mobileMenu.querySelectorAll('a, button').forEach(element => {
        element.addEventListener('click', function() {
            closeMobileMenu();
        });
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!mobileMenu.contains(event.target) && !mobileMenuBtn.contains(event.target)) {
            closeMobileMenu();
        }
    });

    // Close mobile menu on scroll
    window.addEventListener('scroll', function() {
        if (mobileMenu && mobileMenu.classList.contains('active')) {
            closeMobileMenu();
        }
    });

    // Close menu on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
            closeMobileMenu();
        }
    });

    const desktopNavMediaQuery = window.matchMedia('(min-width: 1024px)');
    const handleDesktopNav = event => {
        if (event.matches) {
            closeMobileMenu();
        }
    };

    if (desktopNavMediaQuery.addEventListener) {
        desktopNavMediaQuery.addEventListener('change', handleDesktopNav);
    } else {
        desktopNavMediaQuery.addListener(handleDesktopNav);
    }
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
        closeMobileMenu();
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

const loginForm = document.getElementById('loginForm');
const loginNameInput = document.getElementById('login_name');
const loginPasswordInput = document.getElementById('password');
const loginText = document.getElementById('loginText');
const loginSpinner = document.getElementById('loginSpinner');
const loginSubmitBtn = document.getElementById('loginToDashboardBtn');

const getLoginCsrfToken = () => {
    const metaToken = document.querySelector('meta[name="csrf-token"]');
    return metaToken ? metaToken.getAttribute('content') ?? '' : '';
};

const parseLoginJsonSafe = async response => {
    try {
        return await response.json();
    } catch {
        return null;
    }
};

if (loginForm && loginNameInput && loginPasswordInput && loginText && loginSpinner && loginSubmitBtn) {
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const email = loginNameInput.value.trim();
        const password = loginPasswordInput.value.trim();

        if (!email) {
            showError(loginNameInput, 'Email or employee ID is required');
            return;
        }

        if (!password) {
            showError(loginPasswordInput, 'Password is required');
            return;
        }

        loginText.classList.add('hidden');
        loginSpinner.classList.remove('hidden');
        loginSubmitBtn.disabled = true;
        loginSubmitBtn.style.pointerEvents = 'none';

        try {
            let csrf = getLoginCsrfToken();
            const rememberInput = document.getElementById('remember');
            const remember = rememberInput && rememberInput.checked ? 'on' : '';
            const body = new URLSearchParams({
                email,
                password,
                remember,
            });

            const switchResponse = await fetch('/auth/switch-session', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            const switchPayload = await parseLoginJsonSafe(switchResponse);
            if (switchResponse.ok && switchPayload?.csrf_token) {
                csrf = switchPayload.csrf_token;
                document.querySelector('meta[name="csrf-token"]')?.setAttribute('content', csrf);
            }

            const response = await fetch('/login', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body,
            });

            const payload = await parseLoginJsonSafe(response);

            if (response.ok) {
                if (loginModalInstance) {
                    loginModalInstance.hide();
                }
                try {
                    const whoamiResponse = await fetch('/whoami', {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    const whoamiPayload = await parseLoginJsonSafe(whoamiResponse);
                    if (whoamiResponse.ok && whoamiPayload) {
                        console.log('whoami', whoamiPayload);
                    }
                } catch (error) {
                    console.warn('whoami check failed', error);
                }
                window.location.href = '/dashboard';
                return;
            }

            if (response.status === 422) {
                const errors = payload?.errors ?? {};
                const message = payload?.message ?? '';
                if (errors.email) {
                    showError(loginNameInput, errors.email[0]);
                }
                if (errors.password) {
                    showError(loginPasswordInput, errors.password[0]);
                }
                if (message === 'Activate account first.') {
                    showError(loginNameInput, message);
                }
                return;
            }

            if (response.status === 401) {
                showError(loginNameInput, 'Invalid credentials.');
                return;
            }

            alert(payload?.message ?? 'Login failed. Please try again.');
        } catch (error) {
            alert('Login failed. Please try again.');
        } finally {
            loginText.classList.remove('hidden');
            loginSpinner.classList.add('hidden');
            loginSubmitBtn.disabled = false;
            loginSubmitBtn.style.pointerEvents = '';
        }
    });
}
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

const requiredElements = [
    verifyPhase,
    passwordPhase,
    verifyBtn,
    backBtn,
    activateBtn,
    currentStepSpan,
    modalTitle,
    employeeIdInput,
    emailInput,
    passwordInput,
    confirmPasswordInput,
    passwordStrengthBar,
    passwordStrengthText,
];

if (requiredElements.some(element => !element)) {
    return;
}

const getCsrfToken = () => {
    const metaToken = document.querySelector('meta[name="csrf-token"]');
    if (metaToken) {
        return metaToken.getAttribute('content') ?? '';
    }

    const hiddenToken = document.querySelector('input[name="_token"]');
    return hiddenToken ? hiddenToken.value : '';
};

const parseJsonSafe = async response => {
    try {
        return await response.json();
    } catch {
        return null;
    }
};

const employeeIdPattern = /^EMP-[A-Z]{3}-\d{4}$/i;
const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

const activateLoadingState = (button, text) => {
    if (!button) return null;
    const original = button.innerHTML;
    button.innerHTML = `<span class="loading-spinner"></span> ${text}`;
    button.disabled = true;
    return original;
};

const restoreButton = (button, original) => {
    if (!button || original === null) return;
    button.innerHTML = original;
    button.disabled = false;
};

// Track current step
let currentStep = 1;
let verifiedData = null;

// Step 1: Verify Account
verifyBtn.addEventListener('click', async function() {
    const employeeId = employeeIdInput.value.trim().toUpperCase();
    const email = emailInput.value.trim();

    if (!employeeId) {
        showError(employeeIdInput, 'Employee ID is required');
        return;
    }

    if (!email) {
        showError(emailInput, 'Email is required');
        return;
    }

    if (!employeeIdPattern.test(employeeId)) {
        showError(employeeIdInput, 'Invalid format. Use EMP-ABC-0000');
        return;
    }

    if (!emailPattern.test(email)) {
        showError(emailInput, 'Please enter a valid email address');
        return;
    }

    const originalText = activateLoadingState(verifyBtn, 'Verifying...');

    try {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        };
        const csrf = getCsrfToken();
        if (csrf) {
            headers['X-CSRF-TOKEN'] = csrf;
        }

        const response = await fetch('/activate/verify', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                ...headers,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ employee_id: employeeId, email }),
        });


        const payload = await parseJsonSafe(response);

        if (response.ok) {
            verifiedData = {
                employeeId,
                email,
                token: payload?.token ?? '',
                user: payload?.user ?? null,
            };

            goToStep(2);
        } else {
            const message = payload?.message ?? 'Account verification failed.';
            if (response.status === 409) {
                alert(message);
            } else if (/email/i.test(message)) {
                showError(emailInput, message);
            } else {
                showError(employeeIdInput, message);
            }
        }
    } catch (error) {
        alert('Unable to verify your account. Please try again.');
    } finally {
        restoreButton(verifyBtn, originalText);
    }
});

if (backBtn) {
    backBtn.addEventListener('click', function() {
        goToStep(1);
    });
}

if (passwordInput) {
    passwordInput.addEventListener('input', function() {
        checkPasswordStrength(this.value);
    });
}

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

window.togglePassword = function(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');

    if (!input || !icon) {
        return;
    }

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

activationForm.addEventListener('submit', async function(e) {
    e.preventDefault();

    if (!passwordInput || !confirmPasswordInput || !activateBtn) {
        return;
    }

    const password = passwordInput.value.trim();
    const confirmPassword = confirmPasswordInput.value.trim();

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

    const strength = calculatePasswordStrength(password);
    if (strength < 2) {
        alert('Please use a stronger password (at least 8 characters with letters and numbers)');
        return;
    }

    if (!verifiedData?.token) {
        goToStep(1);
        alert('Please verify your account first.');
        return;
    }

    const originalText = activateLoadingState(activateBtn, 'Activating...');

    try {
        const formData = new FormData();
        formData.append('token', verifiedData.token);
        formData.append('password', password);
        formData.append('password_confirmation', confirmPassword);
        if (profilePhotoInput && profilePhotoInput.files.length) {
            formData.append('profile_photo', profilePhotoInput.files[0]);
        }

        const headers = {
            'Accept': 'application/json',
        };
        const csrf = getCsrfToken();
        if (csrf) {
            headers['X-CSRF-TOKEN'] = csrf;
        }

        const response = await fetch('/activate/complete', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                ...headers,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
        });

        const payload = await parseJsonSafe(response);

        if (response.ok) {
            const message = payload?.message ?? 'Account activated successfully.';
            alert(message);
            resetForm();

            const modal = document.getElementById('accountActivationModal');
            const modalInstance = Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
            }

            setTimeout(() => {
                if (typeof loginModalInstance !== 'undefined') {
                    loginModalInstance.show();
                }
            }, 300);
        } else {
            const message = payload?.message ?? 'Unable to activate your account.';
            if (payload?.errors?.password) {
                showError(passwordInput, payload.errors.password[0]);
            }
            if (payload?.errors?.token) {
                alert(payload.errors.token[0]);
                goToStep(1);
                return;
            }
            alert(message);
        }
    } catch (error) {
        alert('Unable to activate your account. Please try again.');
    } finally {
        restoreButton(activateBtn, originalText);
    }
});

function goToStep(step) {
    currentStep = step;

    if (step === 1) {
        verifyPhase.classList.remove('hidden');
        passwordPhase.classList.add('hidden');
        modalTitle.textContent = 'Activate PMS Account';
    } else if (step === 2) {
        verifyPhase.classList.add('hidden');
        passwordPhase.classList.remove('hidden');
        modalTitle.textContent = 'Set Your Password';

        if (verifiedData) {
            document.getElementById('act_password').focus();
        }
    }

    stepIndicators.forEach(indicator => {
        const stepNum = parseInt(indicator.getAttribute('data-step'), 10);
        if (stepNum <= step) {
            indicator.classList.add('active');
        } else {
            indicator.classList.remove('active');
        }
    });

    currentStepSpan.textContent = step;
}

function checkPasswordStrength(password) {
    if (!passwordStrengthBar || !passwordStrengthText) {
        return;
    }

    const strength = calculatePasswordStrength(password);

    passwordStrengthBar.className = 'h-1.5 rounded-full';
    passwordStrengthBar.style.width = '0%';

    switch (strength) {
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

    if (password.length >= 8) {
        strength++;
    }
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) {
        strength++;
    }
    if (/\d/.test(password)) {
        strength++;
    }
    if (/[^A-Za-z0-9]/.test(password)) {
        strength++;
    }

    return strength;
}

function showError(inputElement, message) {
    if (!inputElement) {
        return;
    }

    inputElement.classList.add('border-red-500');

    let errorDiv = inputElement.nextElementSibling;
    if (!errorDiv || !errorDiv.classList.contains('error-message')) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message text-xs text-red-400 mt-1';
        inputElement.parentNode.insertBefore(errorDiv, inputElement.nextSibling);
    }
    errorDiv.textContent = message;

    inputElement.focus();

    setTimeout(() => {
        inputElement.classList.remove('border-red-500');
        if (errorDiv && errorDiv.parentNode) {
            errorDiv.remove();
        }
    }, 3000);
}

function resetForm() {
    if (employeeIdInput) {
        employeeIdInput.value = '';
    }
    if (emailInput) {
        emailInput.value = '';
    }
    if (fullNameInput) {
        fullNameInput.value = '';
    }
    if (passwordInput) {
        passwordInput.value = '';
    }
    if (confirmPasswordInput) {
        confirmPasswordInput.value = '';
    }

    resetProfilePhoto();

    goToStep(1);
    verifiedData = null;

    if (passwordStrengthBar) {
        passwordStrengthBar.style.width = '0%';
        passwordStrengthBar.className = 'h-1.5 rounded-full';
    }
    if (passwordStrengthText) {
        passwordStrengthText.textContent = 'None';
    }
}

goToStep(1);
});
// End Landing Page

// Employee-Dashboard
document.addEventListener('DOMContentLoaded', function() {
if (!document.body.classList.contains('employee-layout')) {
    return;
}

const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const sidebar = document.getElementById('sidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');
const logoutBtn = document.getElementById('logoutBtn');

if (mobileMenuBtn && sidebar && sidebarOverlay) {
    // Toggle mobile menu
    mobileMenuBtn.addEventListener('click', function() {
        sidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
    });

    // Close menu when clicking overlay
    sidebarOverlay.addEventListener('click', function() {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
    });

    // Close menu when clicking a link (for mobile)
    sidebar.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 1024) {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });
}

// Logout functionality
if (logoutBtn) {
    const logoutUrl = logoutBtn.getAttribute('data-logout-url') || '/';
    logoutBtn.addEventListener('click', function() {
        window.location.href = logoutUrl;
    });
}

if (sidebar) {
    function setActiveMenuItem() {
        const currentPath = window.location.hash || '#';
        const menuItems = sidebar.querySelectorAll('a');

        menuItems.forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('href') === currentPath) {
                item.classList.add('active');
            }
        });

        // Default to dashboard if no match
        if (!sidebar.querySelector('a.active')) {
            sidebar.querySelector('.dashboard-item a').classList.add('active');
        }
    }

    setActiveMenuItem();

    // Close menu on window resize (if resized to desktop)
    window.addEventListener('resize', function() {
        if (window.innerWidth > 1024) {
            sidebar.classList.remove('active');
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('active');
            }
            document.body.style.overflow = '';
        }
    });
}
});


//End Employee-Dashboard

// Admin Loading Buttons
document.addEventListener('DOMContentLoaded', function () {
const adminLoadingButtons = document.querySelectorAll('[data-admin-loading="true"]');
if (!adminLoadingButtons.length) {
    return;
}

function setButtonLoading(button, isLoading, loadingText) {
    if (!button) {
        return;
    }
    const label = button.querySelector('[data-button-label]');
    const spinner = button.querySelector('[data-button-spinner]');
    if (label && !button.dataset.originalLabel) {
        button.dataset.originalLabel = label.textContent.trim();
    }

    if (isLoading) {
        button.disabled = true;
        button.classList.add('opacity-70', 'cursor-wait');
        if (spinner) {
            spinner.classList.remove('hidden');
        }
        if (label && loadingText) {
            label.textContent = loadingText;
        }
    } else {
        button.disabled = false;
        button.classList.remove('opacity-70', 'cursor-wait');
        if (spinner) {
            spinner.classList.add('hidden');
        }
        if (label && button.dataset.originalLabel) {
            label.textContent = button.dataset.originalLabel;
        }
    }
}

adminLoadingButtons.forEach((button) => {
    button.addEventListener('click', function () {
        if (button.dataset.loadingActive === 'true') {
            return;
        }
        button.dataset.loadingActive = 'true';
        setButtonLoading(button, true, button.dataset.loadingText || 'Loading...');

        const duration = Number.parseInt(button.dataset.loadingDuration || '1200', 10);
        if (!Number.isNaN(duration)) {
            setTimeout(() => {
                setButtonLoading(button, false);
                button.dataset.loadingActive = 'false';
            }, duration);
        }
    });
});
});
