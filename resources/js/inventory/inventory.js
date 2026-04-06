// File: resources/js/inventory/inventory.js

function inventoryManager() {
    return {
        showAdjustModal: false,
        showTransferModal: false,
        adjusting: false,
        transferring: false,
        errors: {},

        adjustForm: {
            product_id: '',
            store_id: '',
            new_quantity: '',
            reason: '',
        },

        transferForm: {
            from_store_id: '',
            to_store_id: '',
            items: [{ product_id: '', quantity: '' }],
            notes: '',
        },

        // ── Stock Adjustment ───────────────────────────────────────────────

        openAdjustModal(productId, storeId, currentQty) {
            this.errors = {};
            this.adjustForm = {
                product_id: productId,
                store_id: storeId,
                new_quantity: currentQty,
                reason: '',
            };
            this.showAdjustModal = true;
        },

        async saveAdjustment() {
            this.adjusting = true;
            this.errors = {};

            try {
                const res = await this.ajax('POST', '/inventory/adjust', this.adjustForm);
                this.showToast(res.message || 'Stock adjusted.', 'success');
                this.showAdjustModal = false;
                setTimeout(() => window.location.reload(), 500);
            } catch (e) {
                this.errors = e.errors ?? {};
                this.showToast(e.message || 'Adjustment failed.', 'error');
            } finally {
                this.adjusting = false;
            }
        },

        // ── Stock Transfer ─────────────────────────────────────────────────

        openTransferModal() {
            this.errors = {};
            this.transferForm = {
                from_store_id: '',
                to_store_id: '',
                items: [{ product_id: '', quantity: '' }],
                notes: '',
            };
            this.showTransferModal = true;
        },

        addTransferItem() {
            this.transferForm.items.push({ product_id: '', quantity: '' });
        },

        removeTransferItem(index) {
            this.transferForm.items.splice(index, 1);
        },

        async saveTransfer() {
            this.transferring = true;
            this.errors = {};

            try {
                const res = await this.ajax('POST', '/inventory/transfer', this.transferForm);
                this.showToast(res.message || 'Transfer created.', 'success');
                this.showTransferModal = false;
                setTimeout(() => window.location.reload(), 500);
            } catch (e) {
                this.errors = e.errors ?? {};
                this.showToast(e.message || 'Transfer failed.', 'error');
            } finally {
                this.transferring = false;
            }
        },

        // ── Shared Utilities ───────────────────────────────────────────────

        async ajax(method, url, data = null) {
            const res = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                },
                body: data ? JSON.stringify(data) : null,
            });

            const json = await res.json();
            if (!res.ok) {
                const err = new Error(json.message || 'Error');
                err.errors = json.errors;
                throw err;
            }
            return json;
        },

        showToast(message, type = 'info') {
            const classes = {
                success: 'bg-green-600',
                error: 'bg-red-600',
                info: 'bg-blue-600',
            };
            const el = document.createElement('div');
            el.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-xl text-white shadow-lg ${classes[type] ?? classes.info}`;
            el.textContent = message;
            document.body.appendChild(el);
            setTimeout(() => el.remove(), 3500);
        },
    };
}