// File: resources/js/pos/cart.js

function posApp() {
    return {
        // State
        cart: { items: [], subtotal: 0, discount_total: 0, tax_total: 0, grand_total: 0, item_count: 0, coupon: null },
        products: [],
        categories: [],
        selectedCategory: null,
        searchQuery: '',
        selectedCustomer: null,
        couponCode: '',
        loadingProducts: false,
        loadingCart: false,

        // Payment modal state
        paymentModal: false,
        paymentMethod: 'cash',
        amountTendered: 0,
        changeAmount: 0,
        cardReference: '',
        processingPayment: false,

        // Quick cash buttons
        quickCashAmounts: [5, 10, 20, 50],

        // ── Lifecycle ──────────────────────────────────────────────────────

        async init() {
            await Promise.all([this.fetchCart(), this.loadProducts()]);
            this.setupBarcodeCapture();
            this.setupKeyboardShortcuts();
        },

        // ── Cart Operations ────────────────────────────────────────────────

        async fetchCart() {
            try {
                const res = await this.ajax('GET', window.POS_CONFIG.routes.cart);
                this.cart = res.data;
            } catch (e) {
                this.toast('Failed to load cart', 'error');
            }
        },

        async addToCart(variantId) {
            if (!variantId) return;

            try {
                const res = await this.ajax('POST', window.POS_CONFIG.routes.addItem, {
                    variant_id: variantId,
                    quantity: 1,
                });
                this.cart = res.data;
                this.playBeep();
                this.searchQuery = '';
                document.getElementById('barcode-input').focus();
            } catch (e) {
                this.toast(e.message || 'Failed to add item', 'error');
            }
        },

        async updateQuantity(variantId, qty) {
            if (qty < 0) return;
            try {
                const url = window.POS_CONFIG.routes.addItem.replace('items', `items/${variantId}`);
                const res = await this.ajax('PATCH', url, { quantity: qty });
                this.cart = res.data;
            } catch (e) {
                this.toast('Failed to update quantity', 'error');
            }
        },

        async removeItem(variantId) {
            try {
                const url = window.POS_CONFIG.routes.addItem.replace('items', `items/${variantId}`);
                const res = await this.ajax('DELETE', url);
                this.cart = res.data;
            } catch (e) {
                this.toast('Failed to remove item', 'error');
            }
        },

        async clearCart() {
            if (!confirm('Clear all items from cart?')) return;
            try {
                const res = await this.ajax('DELETE', window.POS_CONFIG.routes.cart);
                this.cart = res.data;
                this.selectedCustomer = null;
                this.couponCode = '';
            } catch (e) {
                this.toast('Failed to clear cart', 'error');
            }
        },

        async applyCoupon() {
            if (!this.couponCode.trim()) return;
            try {
                const res = await this.ajax('POST', window.POS_CONFIG.routes.cart + '/coupon', {
                    code: this.couponCode,
                });
                this.cart = res.data;
                this.couponCode = '';
                this.toast('Coupon applied!', 'success');
            } catch (e) {
                this.toast(e.message || 'Invalid coupon', 'error');
            }
        },

        async removeCoupon() {
            try {
                const res = await this.ajax('DELETE', window.POS_CONFIG.routes.cart + '/coupon');
                this.cart = res.data;
            } catch (e) {
                this.toast('Failed to remove coupon', 'error');
            }
        },

        // ── Product Operations ─────────────────────────────────────────────

        async loadProducts() {
            this.loadingProducts = true;
            try {
                const params = new URLSearchParams({
                    store_id: window.POS_CONFIG.storeId,
                });
                if (this.selectedCategory) params.set('category_id', this.selectedCategory);

                const res = await this.ajax('GET', `/api/v1/products?${params}`);
                this.products = res.data ?? [];
            } catch (e) {
                this.toast('Failed to load products', 'error');
            } finally {
                this.loadingProducts = false;
            }
        },

        async searchProducts() {
            if (this.searchQuery.length < 2) {
                await this.loadProducts();
                return;
            }
            this.loadingProducts = true;
            try {
                const res = await this.ajax('GET', `${window.POS_CONFIG.routes.search}?q=${encodeURIComponent(this.searchQuery)}&store_id=${window.POS_CONFIG.storeId}`);
                this.products = res.data ?? [];
            } catch (e) {
                this.products = [];
            } finally {
                this.loadingProducts = false;
            }
        },

        async handleEnterKey() {
            if (!this.searchQuery.trim()) return;

            // Try barcode lookup first
            try {
                const res = await this.ajax('GET', `${window.POS_CONFIG.routes.barcode}${encodeURIComponent(this.searchQuery)}`);
                if (res.data) {
                    await this.addToCart(res.data.matched_variant?.id ?? res.data.product?.default_variant_id);
                    return;
                }
            } catch (e) {
                // Not found by barcode — fall through to search
            }

            await this.searchProducts();
        },

        // ── Payment ────────────────────────────────────────────────────────

        openPayment(method) {
            this.paymentMethod = method;
            this.amountTendered = this.cart.grand_total;
            this.changeAmount = 0;
            this.cardReference = '';
            this.paymentModal = true;
        },

        calculateChange() {
            this.changeAmount = Math.max(0, this.amountTendered - this.cart.grand_total);
        },

        async processPayment() {
            this.processingPayment = true;
            try {
                const payload = {
                    payment_method: this.paymentMethod,
                    amount_tendered: parseFloat(this.amountTendered),
                    customer_id: this.selectedCustomer?.id ?? null,
                    register_id: window.POS_REGISTER_ID ?? null,
                    pos_session_id: window.POS_SESSION_ID ?? null,
                    payments: [{
                        method: this.paymentMethod,
                        amount: this.cart.grand_total,
                        tendered: parseFloat(this.amountTendered),
                        change: this.changeAmount,
                        reference: this.cardReference ?? null,
                    }],
                };

                const res = await this.ajax('POST', window.POS_CONFIG.routes.checkout, payload);

                this.paymentModal = false;
                this.cart = { items: [], subtotal: 0, discount_total: 0, tax_total: 0, grand_total: 0, item_count: 0 };
                this.selectedCustomer = null;

                this.toast(`Sale complete! Change: ${this.formatMoney(this.changeAmount)}`, 'success');

                // Open receipt in popup
                window.open(`/pos/receipts/${res.data.id}/print`, '_blank', 'width=420,height=600');

            } catch (e) {
                this.toast(e.message || 'Payment failed', 'error');
            } finally {
                this.processingPayment = false;
            }
        },

        // ── Barcode Scanner ────────────────────────────────────────────────

        setupBarcodeCapture() {
            // USB HID scanners emit keystrokes ending with Enter
            // Keep the barcode input focused at all times in POS mode
            const input = document.getElementById('barcode-input');
            if (!input) return;

            input.focus();

            document.addEventListener('keydown', (e) => {
                if (document.activeElement.tagName !== 'INPUT' &&
                    document.activeElement.tagName !== 'TEXTAREA') {
                    input.focus();
                }
            });
        },

        setupKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                if (e.key === 'F2') {
                    e.preventDefault();
                    document.getElementById('barcode-input').focus();
                }
                if (e.key === 'F1' && this.cart.item_count > 0) {
                    e.preventDefault();
                    this.openPayment('cash');
                }
                if (e.key === 'Escape') {
                    this.paymentModal = false;
                }
            });
        },

        // ── Utility Methods ────────────────────────────────────────────────

        formatMoney(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: window.POS_CONFIG.storeCurrency || 'USD',
            }).format(parseFloat(amount) || 0);
        },

        async ajax(method, url, data = null) {
            const options = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.POS_CONFIG.csrfToken,
                },
            };
            if (data && method !== 'GET') {
                options.body = JSON.stringify(data);
            }

            const response = await fetch(url, options);
            const json = await response.json();

            if (!response.ok) {
                throw new Error(json.message || 'Request failed');
            }
            return json;
        },

        toast(message, type = 'info') {
            const event = new CustomEvent('pos-toast', { detail: { message, type } });
            document.dispatchEvent(event);
        },

        playBeep() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.frequency.value = 880;
                gain.gain.value = 0.3;
                osc.start();
                osc.stop(ctx.currentTime + 0.08);
            } catch (e) {
                // Audio not available — silent fail
            }
        },

        openCustomerModal() {
            // Dispatches to a separate Alpine component
            this.$dispatch('open-customer-modal');
        },
    };
}

// Toast manager Alpine component
function toastManager() {
    return {
        toasts: [],
        init() {
            document.addEventListener('pos-toast', (e) => {
                const { message, type } = e.detail;
                const id = Date.now();
                this.toasts.push({ id, message, type, visible: true });
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 3500);
            });
        },
    };
}