// ===============================
// POS APPLICATION (Refactored)
// ===============================

function posApp() {
    return {
        // =========================
        // STATE
        // =========================
        cart: {
            items: [],
            subtotal: 0,
            discount_total: 0,
            tax_total: 0,
            grand_total: 0,
            item_count: 0,
            coupon: null,
        },

        products: [],
        categories: [],

        selectedCategory: null,
        selectedCustomer: null,

        searchQuery: '',
        couponCode: '',

        loadingProducts: false,
        loadingCart: false,

        // =========================
        // PAYMENT STATE
        // =========================
        paymentModal: false,
        paymentMethod: 'cash',
        amountTendered: 0,
        changeAmount: 0,
        cardReference: '',
        processingPayment: false,

        quickCashAmounts: [5, 10, 20, 50],

        // =========================
        // INIT
        // =========================
        async init() {
            await Promise.all([
                this.fetchCart(),
                this.loadProducts()
            ]);

            this.initBarcodeFocus();
            this.initKeyboardShortcuts();
        },

        // =========================
        // API LAYER (CLEAN)
        // =========================
        async request(method, url, payload = null) {
            const res = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.POS_CONFIG.csrfToken,
                },
                body: payload ? JSON.stringify(payload) : null,
            });

            const data = await res.json();

            if (!res.ok) {
                throw new Error(data.message || 'Request failed');
            }

            return data;
        },

        // =========================
        // CART
        // =========================
        async fetchCart() {
            try {
                const res = await this.request('GET', window.POS_CONFIG.routes.cart);
                this.cart = res.data;
            } catch (e) {
                this.toast(e.message, 'error');
            }
        },

        async addToCart(variantId) {
            if (!variantId) return;

            try {
                const res = await this.request(
                    'POST',
                    window.POS_CONFIG.routes.addItem,
                    { variant_id: variantId, quantity: 1 }
                );

                this.cart = res.data;
                this.searchQuery = '';
                this.beep();
            } catch (e) {
                this.toast(e.message, 'error');
            }
        },

        async updateQuantity(variantId, qty) {
            try {
                const url = `${window.POS_CONFIG.routes.cart}/items/${variantId}`;

                const res = await this.request('PATCH', url, { quantity: qty });
                this.cart = res.data;
            } catch (e) {
                this.toast(e.message, 'error');
            }
        },

        async removeItem(variantId) {
            try {
                const url = `${window.POS_CONFIG.routes.cart}/items/${variantId}`;

                const res = await this.request('DELETE', url);
                this.cart = res.data;
            } catch (e) {
                this.toast(e.message, 'error');
            }
        },

        async clearCart() {
            if (!confirm('Clear cart?')) return;

            try {
                const res = await this.request('DELETE', window.POS_CONFIG.routes.cart);
                this.cart = res.data;
                this.selectedCustomer = null;
                this.couponCode = '';
            } catch (e) {
                this.toast(e.message, 'error');
            }
        },

        async applyCoupon() {
            if (!this.couponCode) return;

            try {
                const res = await this.request(
                    'POST',
                    `${window.POS_CONFIG.routes.cart}/coupon`,
                    { code: this.couponCode }
                );

                this.cart = res.data;
                this.couponCode = '';
                this.toast('Coupon applied', 'success');
            } catch (e) {
                this.toast(e.message, 'error');
            }
        },

        async removeCoupon() {
            try {
                const res = await this.request(
                    'DELETE',
                    `${window.POS_CONFIG.routes.cart}/coupon`
                );

                this.cart = res.data;
            } catch (e) {
                this.toast(e.message, 'error');
            }
        },

        // =========================
        // PRODUCTS
        // =========================
        async loadProducts() {
            this.loadingProducts = true;

            try {
                const params = new URLSearchParams();

                params.append('store_id', window.POS_CONFIG.storeId);

                if (this.selectedCategory) {
                    params.append('category_id', this.selectedCategory);
                }

                const res = await this.request(
                    'GET',
                    `/api/v1/products?${params.toString()}`
                );

                this.products = res.data || [];
            } catch (e) {
                this.toast(e.message, 'error');
            } finally {
                this.loadingProducts = false;
            }
        },

        async searchProducts() {
            if (this.searchQuery.length < 2) {
                return this.loadProducts();
            }

            this.loadingProducts = true;

            try {
                const res = await this.request(
                    'GET',
                    `${window.POS_CONFIG.routes.search}?q=${encodeURIComponent(this.searchQuery)}&store_id=${window.POS_CONFIG.storeId}`
                );

                this.products = res.data || [];
            } catch (e) {
                this.products = [];
            } finally {
                this.loadingProducts = false;
            }
        },

        async handleEnterKey() {
            if (!this.searchQuery) return;

            try {
                const res = await this.request(
                    'GET',
                    `${window.POS_CONFIG.routes.barcode}${encodeURIComponent(this.searchQuery)}`
                );

                if (res.data) {
                    const id = res.data?.matched_variant?.id || res.data?.product?.default_variant_id;
                    return this.addToCart(id);
                }
            } catch (e) {
                await this.searchProducts();
            }
        },

        // =========================
        // PAYMENT
        // =========================
        openPayment(method) {
            this.paymentMethod = method;
            this.amountTendered = this.cart.grand_total;
            this.changeAmount = 0;
            this.cardReference = '';
            this.paymentModal = true;
        },

        calculateChange() {
            this.changeAmount =
                Math.max(0, this.amountTendered - this.cart.grand_total);
        },

        async processPayment() {
            this.processingPayment = true;

            try {
                const payload = {
                    payment_method: this.paymentMethod,
                    amount_tendered: Number(this.amountTendered),
                    customer_id: this.selectedCustomer?.id || null,
                    register_id: window.POS_REGISTER_ID || null,
                    pos_session_id: window.POS_SESSION_ID || null,
                    payments: [{
                        method: this.paymentMethod,
                        amount: this.cart.grand_total,
                        tendered: Number(this.amountTendered),
                        change: this.changeAmount,
                        reference: this.cardReference,
                    }],
                };

                const res = await this.request(
                    'POST',
                    window.POS_CONFIG.routes.checkout,
                    payload
                );

                this.resetCart();

                this.toast('Sale completed', 'success');

                window.open(
                    `/pos/receipts/${res.data.id}/print`,
                    '_blank',
                    'width=420,height=600'
                );

            } catch (e) {
                this.toast(e.message, 'error');
            } finally {
                this.processingPayment = false;
            }
        },

        resetCart() {
            this.paymentModal = false;

            this.cart = {
                items: [],
                subtotal: 0,
                discount_total: 0,
                tax_total: 0,
                grand_total: 0,
                item_count: 0,
            };

            this.selectedCustomer = null;
        },

        // =========================
        // BARCODE + KEYBOARD
        // =========================
        initBarcodeFocus() {
            const input = document.getElementById('barcode-input');
            if (!input) return;

            input.focus();

            document.addEventListener('click', () => input.focus());
        },

        initKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                if (e.key === 'F2') {
                    document.getElementById('barcode-input')?.focus();
                }

                if (e.key === 'F1' && this.cart.item_count > 0) {
                    this.openPayment('cash');
                }

                if (e.key === 'Escape') {
                    this.paymentModal = false;
                }
            });
        },

        // =========================
        // UI HELPERS
        // =========================
        formatMoney(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: window.POS_CONFIG.storeCurrency || 'USD',
            }).format(amount || 0);
        },

        toast(message, type = 'info') {
            document.dispatchEvent(
                new CustomEvent('pos-toast', {
                    detail: { message, type }
                })
            );
        },

        beep() {
            try {
                const ctx = new AudioContext();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.frequency.value = 900;
                gain.gain.value = 0.2;

                osc.start();
                osc.stop(ctx.currentTime + 0.1);
            } catch (_) {}
        },

        openCustomerModal() {
            this.$dispatch('open-customer-modal');
        },
    };
}
