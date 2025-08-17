// DOM Content Loaded
console.log('DOM loaded, initializing theme...');

// Theme management
let currentTheme = localStorage.getItem('theme') || 'light';
console.log('Theme initialized to:', currentTheme);

// Apply initial theme
document.documentElement.setAttribute('data-theme', currentTheme);

// Theme toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    // Set initial theme
    document.documentElement.setAttribute('data-theme', currentTheme);
    
    // Update theme icon
    const themeIcon = document.querySelector('.theme-icon');
    if (themeIcon) {
        themeIcon.textContent = currentTheme === 'light' ? '🌙' : '☀️';
    }
    
    const themeToggle = document.querySelector('.theme-toggle');
    console.log('Theme toggle button found:', themeToggle);
    
    if (themeToggle) {
        console.log('Adding click event listener to theme toggle');
        themeToggle.addEventListener('click', function() {
            console.log('Theme toggle clicked, current theme:', currentTheme);
            currentTheme = currentTheme === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', currentTheme);
            localStorage.setItem('theme', currentTheme);
            
            // Update icon
            const icon = this.querySelector('.theme-icon');
            if (icon) {
                icon.textContent = currentTheme === 'light' ? '🌙' : '☀️';
            }
            
            console.log('Theme switched to:', currentTheme);
        });
    }
});

// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.getElementById('nav-toggle');
    const navMenu = document.getElementById('nav-menu');
    const hamburger = document.querySelector('.hamburger');

    // Force desktop behavior on wider screens
    function checkScreenSize() {
        if (window.innerWidth > 768) {
            console.log('Forced desktop menu behavior - width:', window.innerWidth);
            navMenu.classList.remove('active');
            hamburger.classList.remove('active');
            navMenu.style.display = 'flex';
            console.log('Menu reset to closed state on page load');
        } else {
            navMenu.style.display = '';
        }
    }

    // Initial check and reset on page load
    checkScreenSize();
    
    // Reset hamburger animation on page load
    if (hamburger) {
        hamburger.classList.remove('active');
        console.log('Hamburger animation reset on page load');
    }

    // Handle window resize
    window.addEventListener('resize', checkScreenSize);

    // Mobile menu toggle with explicit state management
    if (navToggle && navMenu && hamburger) {
        navToggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Only allow toggle on mobile screens
            if (window.innerWidth <= 768) {
                console.log('Mobile menu toggle clicked');
                const isActive = navMenu.classList.contains('active');
                
                if (isActive) {
                    navMenu.classList.remove('active');
                    hamburger.classList.remove('active');
                    console.log('Menu closed');
                } else {
                    navMenu.classList.add('active');
                    hamburger.classList.add('active');
                    console.log('Menu opened');
                }
            }
        });

        // Close menu when clicking nav links (mobile only)
        const navLinks = navMenu.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    navMenu.classList.remove('active');
                    hamburger.classList.remove('active');
                    console.log('Menu closed after nav link click');
                }
            });
        });

        // Close menu when clicking outside (mobile only)
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                const isClickInsideNav = navMenu.contains(e.target) || navToggle.contains(e.target);
                
                if (!isClickInsideNav && navMenu.classList.contains('active')) {
                    navMenu.classList.remove('active');
                    hamburger.classList.remove('active');
                    console.log('Menu closed by outside click');
                }
            }
        });
    }
});

// Initialize scroll features when DOM loads
document.addEventListener('DOMContentLoaded', function() {
    // Smooth Scroll for Navigation Links
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

    // Observe all sections
    document.querySelectorAll('section').forEach(section => {
        section.classList.add('section');
        observer.observe(section);
    });
});

// Performance: Debounce function for better scroll handling
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

// Apply debounced scroll to header effect
const debouncedHeaderScroll = debounce(function() {
    const header = document.querySelector('.header');
    
    if (window.scrollY > 100) {
        header.style.background = 'var(--bg-primary)';
        header.style.backdropFilter = 'blur(10px)';
        header.style.boxShadow = '0 2px 20px var(--shadow-medium)';
    } else {
        header.style.background = 'var(--bg-primary)';
        header.style.backdropFilter = 'none';
        header.style.boxShadow = '0 2px 10px var(--shadow-light)';
    }
}, 10);

window.addEventListener('scroll', debouncedHeaderScroll);

// Accessibility: Focus management for mobile menu
document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.getElementById('nav-toggle');
    if (navToggle) {
        navToggle.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
                
                // Focus first menu item when opened
                setTimeout(() => {
                    const firstLink = document.querySelector('.nav-menu .nav-link');
                    if (firstLink && document.getElementById('nav-menu').classList.contains('active')) {
                        firstLink.focus();
                    }
                }, 100);
            }
        });
    }
});

// Função para gerar token CSRF
function generateCSRFToken() {
    return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
}

// Função para exibir mensagens
function showMessage(message, type = 'success') {
    // Remove mensagem anterior se existir
    const existingMessage = document.querySelector('.form-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `form-message form-message-${type}`;
    messageDiv.textContent = message;
    
    const form = document.getElementById('contact-form');
    form.insertBefore(messageDiv, form.firstChild);
    
    // Remove a mensagem após 5 segundos
    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 5000);
}

// Função para validar formulário no frontend
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

// Initialize all functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('IA FORTE - Site carregado com sucesso!');
    
    // Inicializar formulário de contato
    const form = document.getElementById('contact-form');
    if (form) {
        console.log('Formulário de contato inicializado');
        
        // Gerar e armazenar token CSRF
        const csrfToken = generateCSRFToken();
        sessionStorage.setItem('csrf_token', csrfToken);
        
        // Adicionar evento de submit
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            
            try {
                // Desabilitar botão e mostrar loading
                submitButton.disabled = true;
                submitButton.textContent = 'Enviando...';
                
                // Validar no frontend primeiro
                const validationErrors = validateForm(form);
                if (validationErrors.length > 0) {
                    showMessage(validationErrors.join(' '), 'error');
                    return;
                }
                
                // Coletar dados do formulário
                const formData = new FormData(form);
                formData.append('csrf_token', sessionStorage.getItem('csrf_token'));
                
                // Enviar formulário
                const response = await fetch('send-email.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                // Verificar se resposta é válida
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const responseText = await response.text();
                let result;
                
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('Resposta PHP inválida:', responseText);
                    throw new Error('Resposta do servidor inválida');
                }
                
                if (result.success) {
                    showMessage(result.message, 'success');
                    form.reset();
                    // Gerar novo token CSRF
                    const newToken = generateCSRFToken();
                    sessionStorage.setItem('csrf_token', newToken);
                } else {
                    showMessage(result.message, 'error');
                }
                
            } catch (error) {
                console.error('Erro ao enviar formulário:', error);
                showMessage(`Erro: ${error.message}`, 'error');
            } finally {
                // Reabilitar botão
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        });
    }
});