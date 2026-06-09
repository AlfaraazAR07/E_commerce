document.addEventListener('DOMContentLoaded', function () {
    var hamburger = document.querySelector('.hamburger');
    var nav = document.querySelector('nav');
    var cartCountEl = document.querySelector('.cart-count');
    var addToCartBtns = document.querySelectorAll('.add-to-cart');

    if (hamburger) {
        hamburger.addEventListener('click', function () {
            nav.classList.toggle('active');
            hamburger.classList.toggle('active');
        });

        document.addEventListener('click', function (e) {
            if (!nav.contains(e.target) && !hamburger.contains(e.target)) {
                nav.classList.remove('active');
                hamburger.classList.remove('active');
            }
        });
    }

    if (addToCartBtns.length) {
        addToCartBtns.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var productId = parseInt(this.dataset.productId);
                var productName = this.textContent;

                if (typeof Cart !== 'undefined') {
                    var count = Cart.addToCart(productId);
                    updateCartCount(count);
                    showToast(productName + ' added to cart!');
                } else {
                    var formData = new FormData();
                    formData.append('product_id', productId);
                    formData.append('action', 'add');

                    fetch('includes/cart_handler.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(function (response) { return response.json(); })
                        .then(function (data) {
                            if (data.success) {
                                updateCartCount(data.count);
                                showToast(productName + ' added to cart!');
                            }
                        })
                        .catch(function (error) {
                            console.error('Error:', error);
                        });
                }
            });
        });
    }

    function updateCartCount(count) {
        if (cartCountEl) {
            cartCountEl.textContent = count;
            cartCountEl.style.display = count > 0 ? 'flex' : 'none';
        }
    }

    function showToast(message) {
        var toastEl = document.getElementById('toast');
        if (!toastEl) {
            toastEl = document.createElement('div');
            toastEl.id = 'toast';
            toastEl.className = 'toast';
            document.body.appendChild(toastEl);
        }
        toastEl.textContent = message;
        toastEl.classList.add('show');
        setTimeout(function () {
            toastEl.classList.remove('show');
        }, 2500);
    }

    var searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            var query = document.getElementById('searchInput').value.trim();
            if (!query) {
                e.preventDefault();
            }
        });
    }

    if (typeof Cart !== 'undefined') {
        var count = Cart.getCartCount();
        updateCartCount(count);
    }

    var cartTable = document.querySelector('.cart-table');
    if (cartTable) {
        var cartTotalEl = document.getElementById('cartTotal');

        function formatMoney(value) {
            return new Intl.NumberFormat('en-IN', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            }).format(value);
        }

        function updateCartTotals(total) {
            if (cartTotalEl) {
                cartTotalEl.textContent = formatMoney(total);
            }
        }

        cartTable.addEventListener('change', function (e) {
            if (!e.target.classList.contains('quantity-input')) {
                return;
            }

            var input = e.target;
            var cartId = parseInt(input.dataset.cartId);
            var price = parseFloat(input.dataset.price || '0');
            var quantity = parseInt(input.value, 10);

            if (!quantity || quantity < 1) {
                quantity = 1;
                input.value = 1;
            }

            var formData = new FormData();
            formData.append('action', 'update');
            formData.append('cart_id', cartId);
            formData.append('quantity', quantity);

            fetch('includes/cart_handler.php', {
                method: 'POST',
                body: formData
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (data.success) {
                        var row = input.closest('.cart-row');
                        if (row) {
                            var lineTotalEl = row.querySelector('.line-total');
                            if (lineTotalEl) {
                                lineTotalEl.textContent = formatMoney(price * quantity);
                            }
                        }
                        updateCartTotals(data.total || 0);
                        updateCartCount(data.count || 0);
                    }
                })
                .catch(function (error) {
                    console.error('Error:', error);
                });
        });

        cartTable.addEventListener('click', function (e) {
            if (!e.target.classList.contains('remove-btn')) {
                return;
            }

            var cartId = parseInt(e.target.dataset.cartId);
            var formData = new FormData();
            formData.append('action', 'remove');
            formData.append('cart_id', cartId);

            fetch('includes/cart_handler.php', {
                method: 'POST',
                body: formData
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (data.success) {
                        var row = e.target.closest('.cart-row');
                        if (row) {
                            row.remove();
                        }
                        updateCartTotals(data.total || 0);
                        updateCartCount(data.count || 0);

                        if (document.querySelectorAll('.cart-row').length === 0) {
                            window.location.reload();
                        }
                    }
                })
                .catch(function (error) {
                    console.error('Error:', error);
                });
        });
    }
});
