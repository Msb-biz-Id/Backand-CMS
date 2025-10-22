/**
 * Advanced CMS - Default Theme JavaScript
 */

(function() {
    'use strict';
    
    // Update cart count
    function updateCartCount() {
        fetch('/api/cart/count')
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.cart-badge');
                if (badge && data.count) {
                    badge.textContent = data.count;
                    badge.style.display = 'flex';
                } else if (badge) {
                    badge.style.display = 'none';
                }
            })
            .catch(err => console.log('Cart count error:', err));
    }
    
    // Add to cart
    window.addToCart = function(productId, quantity = 1) {
        fetch('/api/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Product added to cart!');
                updateCartCount();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => {
            console.error('Add to cart error:', err);
            alert('Failed to add to cart');
        });
    };
    
    // Remove from cart
    window.removeFromCart = function(cartId) {
        if (!confirm('Remove this item from cart?')) return;
        
        fetch('/api/cart/remove/' + cartId, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    };
    
    // Update cart quantity
    window.updateCartQuantity = function(cartId, quantity) {
        fetch('/api/cart/update/' + cartId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    };
    
    // Mobile menu toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
        });
    }
    
    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Lazy loading images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img.lazy').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // Initialize on load
    document.addEventListener('DOMContentLoaded', function() {
        updateCartCount();
    });
    
})();
