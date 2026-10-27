/**
 * Cart & Customization Persistence Manager
 * Handles saving customized items, persistent local storage, quantity changes, and checkout summary.
 */

class CartManager {
    constructor() {
        this.storageKey = 'personalized_gift_cart_v1';
        this.items = this.loadFromStorage();
    }

    loadFromStorage() {
        try {
            const raw = localStorage.getItem(this.storageKey);
            return raw ? JSON.parse(raw) : [];
        } catch (e) {
            console.error('Failed to parse cart storage:', e);
            return [];
        }
    }

    saveToStorage() {
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(this.items));
            this.notifyChange();
        } catch (e) {
            console.error('Failed to write to cart storage:', e);
        }
    }

    addItem(product, customizationPayload, thumbnailDataUrl) {
        const cartItem = {
            id: 'item_' + Date.now() + '_' + Math.random().toString(36).substring(2, 7),
            productId: product.id,
            productName: product.name,
            category: product.category,
            unitPrice: product.price,
            productColor: customizationPayload.productColor,
            quantity: 1,
            thumbnail: thumbnailDataUrl,
            customization: {
                name: customizationPayload.text.name,
                message: customizationPayload.text.message,
                date: customizationPayload.text.date,
                font: customizationPayload.text.font,
                textColor: customizationPayload.text.color,
                hasPhoto: !!customizationPayload.photoDataUrl,
                photoDataUrl: customizationPayload.photoDataUrl,
                photoTransform: customizationPayload.photoTransform
            },
            fullPayload: customizationPayload,
            addedAt: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        };

        this.items.unshift(cartItem);
        this.saveToStorage();
        return cartItem;
    }

    updateQuantity(itemId, delta) {
        const item = this.items.find(i => i.id === itemId);
        if (!item) return;

        item.quantity += delta;
        if (item.quantity <= 0) {
            this.removeItem(itemId);
        } else {
            this.saveToStorage();
        }
    }

    removeItem(itemId) {
        this.items = this.items.filter(i => i.id !== itemId);
        this.saveToStorage();
    }

    clearCart() {
        this.items = [];
        this.saveToStorage();
    }

    getItem(itemId) {
        return this.items.find(i => i.id === itemId);
    }

    getItemCount() {
        return this.items.reduce((sum, item) => sum + item.quantity, 0);
    }

    getSubtotal() {
        return this.items.reduce((sum, item) => sum + (item.unitPrice * item.quantity), 0);
    }

    getShipping() {
        const subtotal = this.getSubtotal();
        if (subtotal === 0) return 0;
        return subtotal >= 999 ? 0 : 99; // Free shipping over ₹999
    }

    getTotal() {
        return this.getSubtotal() + this.getShipping();
    }

    notifyChange() {
        const event = new CustomEvent('cart:updated', {
            detail: {
                items: this.items,
                count: this.getItemCount(),
                total: this.getTotal()
            }
        });
        window.dispatchEvent(event);
    }
}

// Global cart singleton
window.cartManager = new CartManager();
