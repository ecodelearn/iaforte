// Theme management - Apply immediately to prevent flash
(function() {
    const currentTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', currentTheme);
})();

// Theme management
let currentTheme = localStorage.getItem('theme') || 'light';
console.log('Theme initialized to:', currentTheme);

// Performance: Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// CSRF Token generator
function generateCSRFToken() {
    return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
}

// Show message function
function showMessage(message, type = 'success') {
    const existingMessage = document.querySelector('.form-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `form-message form-message-${type}`;
    messageDiv.textContent = message;
    
    const form = document.getElementById('contact-form');
    if (form) {
        form.insertBefore(messageDiv, form.firstChild);
        
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 5000);
    }
}

// Form validation
function validateForm(form) {
    const errors = [];
    
    const nome = form.querySelector('#nome').value.trim();
    if (!nome || nome.length < 2) {
        errors.push('Nome deve ter pelo menos 2 caracteres.');
    }
    
    const empresa = form.querySelector('#empresa').value.trim();
    if (!empresa || empresa.length < 2) {
        errors.push('Empresa deve ter pelo menos 2 caracteres.');
    }
    
    const email = form.querySelector('#email').value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email || !emailRegex.test(email)) {
        errors.push('Email inválido.');
    }
    
    const telefone = form.querySelector('#telefone').value.trim();
    if (!telefone) {
        errors.push('Telefone é obrigatório.');
    }
    
    const mensagem = form.querySelector('#mensagem').value.trim();
    if (!mensagem || mensagem.length < 10) {
        errors.push('Mensagem deve ter pelo menos 10 caracteres.');
    }
    
    return errors;
}

// SINGLE DOMContentLoaded - ALL functionality here
document.addEventListener('DOMContentLoaded', function() {
    console.log('IA FORTE - Site carregado com sucesso!');
    
    // Theme toggle functionality
    const themeToggle = document.querySelector('.theme-toggle');
    if (themeToggle) {
        // Set initial theme
        document.documentElement.setAttribute('data-theme', currentTheme);
        
        // Update theme icon
        const themeIcon = themeToggle.querySelector('.theme-icon');
        if (themeIcon) {
            themeIcon.textContent = currentTheme === 'light' ? '🌙' : '☀️';
        }
        
        themeToggle.addEventListener('click', function() {
            currentTheme = currentTheme === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', currentTheme);
            localStorage.setItem('theme', currentTheme);
            
            const icon = this.querySelector('.theme-icon');
            if (icon) {
                icon.textContent = currentTheme === 'light' ? '🌙' : '☀️';
            }
        });
    }
    
    // Mobile menu functionality
    const navToggle = document.getElementById('nav-toggle');
    const navMenu = document.getElementById('nav-menu');
    const hamburger = navToggle; // Use nav-toggle as hamburger

    function checkScreenSize() {
        if (window.innerWidth > 768) {
            if (navMenu) {
                navMenu.classList.remove('active');
                navMenu.style.display = 'flex';
            }
            if (hamburger) {
                hamburger.classList.remove('active');
            }
        } else {
            if (navMenu) {
                navMenu.style.display = '';
            }
        }
    }

    if (navToggle && navMenu && hamburger) {
        checkScreenSize();
        window.addEventListener('resize', checkScreenSize);

        navToggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (window.innerWidth <= 768) {
                const isActive = navMenu.classList.contains('active');
                
                if (isActive) {
                    navMenu.classList.remove('active');
                    hamburger.classList.remove('active');
                } else {
                    navMenu.classList.add('active');
                    hamburger.classList.add('active');
                }
            }
        });

        const navLinks = navMenu.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    navMenu.classList.remove('active');
                    hamburger.classList.remove('active');
                }
            });
        });

        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                const isClickInsideNav = navMenu.contains(e.target) || navToggle.contains(e.target);
                
                if (!isClickInsideNav && navMenu.classList.contains('active')) {
                    navMenu.classList.remove('active');
                    hamburger.classList.remove('active');
                }
            }
        });
        
        // Accessibility
        navToggle.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
                
                setTimeout(() => {
                    const firstLink = document.querySelector('.nav-menu .nav-link');
                    if (firstLink && navMenu.classList.contains('active')) {
                        firstLink.focus();
                    }
                }, 100);
            }
        });
    }
    
    // Smooth scroll for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, observerOptions);

    document.querySelectorAll('section').forEach(section => {
        section.classList.add('section');
        observer.observe(section);
    });

    // Animate service features bullets in sequence
    const serviceCards = document.querySelectorAll('.service-card');
    serviceCards.forEach(card => {
        const featureItems = card.querySelectorAll('.service-features li');
        featureItems.forEach((item, index) => {
            item.style.setProperty('--delay', index);
        });
    });
    
    // Header scroll effect
    const debouncedHeaderScroll = debounce(function() {
        const header = document.querySelector('.header');
        if (header) {
            if (window.scrollY > 100) {
                header.style.background = 'var(--bg-primary)';
                header.style.backdropFilter = 'blur(10px)';
                header.style.boxShadow = '0 2px 20px var(--shadow-medium)';
            } else {
                header.style.background = 'var(--bg-primary)';
                header.style.backdropFilter = 'none';
                header.style.boxShadow = '0 2px 10px var(--shadow-light)';
            }
        }
    }, 10);

    window.addEventListener('scroll', debouncedHeaderScroll);
    
    // Contact form functionality
    const form = document.getElementById('contact-form');
    if (form) {
        console.log('Formulário de contato inicializado');
        
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            
            try {
                submitButton.disabled = true;
                submitButton.textContent = 'Enviando...';
                
                const validationErrors = validateForm(form);
                if (validationErrors.length > 0) {
                    showMessage(validationErrors.join(' '), 'error');
                    return;
                }
                
                // Obter token CSRF do servidor (sem JSON)
                const csrfResponse = await fetch('no-json-csrf.php');
                const csrfToken = await csrfResponse.text();
                
                if (csrfToken === 'ERROR') {
                    throw new Error('Erro ao obter token de segurança');
                }
                
                const formData = new FormData(form);
                formData.append('csrf_token', csrfToken);
                
                const response = await fetch('no-json-email.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const responseText = await response.text();
                
                if (responseText === 'SUCCESS') {
                    showMessage('Mensagem enviada com sucesso!', 'success');
                    form.reset();
                } else if (responseText === 'RATE_LIMIT') {
                    showMessage('Muitas tentativas. Tente novamente em 1 hora.', 'error');
                } else if (responseText === 'INVALID_TOKEN') {
                    showMessage('Token de segurança inválido.', 'error');
                } else if (responseText === 'VALIDATION_ERROR') {
                    showMessage('Dados do formulário inválidos.', 'error');
                } else if (responseText === 'MAIL_ERROR') {
                    showMessage('Erro no envio. Tente novamente mais tarde.', 'error');
                } else {
                    showMessage('Erro desconhecido: ' + responseText, 'error');
                }
                
            } catch (error) {
                console.error('Erro ao enviar formulário:', error);
                showMessage(`Erro: ${error.message}`, 'error');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        });
    }
});