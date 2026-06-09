/* Static version of cart functions using localStorage */

var Cart = {
    getCart: function () {
        var cart = localStorage.getItem('lapphub_cart');
        return cart ? JSON.parse(cart) : [];
    },

    saveCart: function (cart) {
        localStorage.setItem('lapphub_cart', JSON.stringify(cart));
    },

    getCartCount: function () {
        var cart = this.getCart();
        var count = 0;
        cart.forEach(function (item) { count += item.quantity; });
        return count;
    },

    addToCart: function (productId) {
        var cart = this.getCart();
        var found = false;

        for (var i = 0; i < cart.length; i++) {
            if (cart[i].id === productId) {
                cart[i].quantity += 1;
                found = true;
                break;
            }
        }

        if (!found) {
            cart.push({ id: productId, quantity: 1 });
        }

        this.saveCart(cart);
        return this.getCartCount();
    },

    removeFromCart: function (productId) {
        var cart = this.getCart();
        cart = cart.filter(function (item) { return item.id !== productId; });
        this.saveCart(cart);
        return this.getCartCount();
    },

    clearCart: function () {
        localStorage.removeItem('lapphub_cart');
    }
};
