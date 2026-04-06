// File: resources/js/products/product-crud.js

function productManager() {
    return {
        showModal: false,
        editingId: null,
        saving: false,
        deleting: null,
        errors: {},

        form: {
            name: '',
            category_id: '',
            brand_id: '',
            tax_rate_id: '',
            sku: '',
            barcode: '',
            description: '',
            cost_price: '',
            selling_price: '',
            unit: 'pcs',
            track_stock: true,
            allow_negative_stock: false,
            is_active: true,
            is_pos_visible: true,
            product_type: 'standard',
        },

        // ── Modal Management ───────────────────────────────────────────────

        openCreateModal() {
            this.editingId = null;
            this.errors = {};
            this.form = {
                name: '', category_id: '', brand_id: '',
                tax_rate_id: '', sku: '', barcode: '',
                description: '', cost_price: '', selling_price: '',
                unit: 'pcs', track_stock: true,
                allow_negative_stock: false, is_active: true,
                is_pos_visible: true, product_type: 'standard',
            };
            this.showModal = true;
        },

        async editProduct(id) {
            this.editingId = id;
            this.errors = {};
            this.showModal = true;

            try {
                const res = await this.ajax('GET', `/products/${id}/edit`);
                const p = res.data;

                this.form = {
                    name: p.name,
                    category_id: p.category?.id ?? '',
                    brand_id: p.brand?.id ?? '',
                    tax_rate_id: p.tax_rate?.id ?? '',
                    sku: p.sku ?? '',
                    barcode: p.barcode ?? '',
                    description: p.description ?? '',
                    cost_price: p.cost_price,
                    selling_price: p.selling_price,
                    unit: p.unit ?? 'pcs',
                    track_stock: p.track_stock,
                    allow_negative_stock: p.allow_negative_stock,
                    is_active: p.is_active,
                    is_pos_visible: p.is_pos_visible,
                    product_type: p.product_type,
                };
            } catch (e) {
                this.showToast('Failed to load product data.', 'error');
                this.showModal = false;
            }
        },

        async saveProduct() {
            this.saving = true;
            this.errors = {};

            try {
                const url = this.editingId ? `/products/${this.editingId}` : '/products';
                const method = this.editingId ? 'PUT' : 'POST';
                const res = await this.ajax(method, url, this.form);

                this.showToast(res.message, 'success');
                this.showModal = false;

                // Refresh table without full page reload
                setTimeout(() => window.location.reload(), 500);
            } catch (e) {
                if (e.errors) {
                    this.errors = e.errors;
                } else {
                    this.showToast(e.message || 'Save failed.', 'error');
                }
            } finally {
                this.saving = false;
            }
        },

        async deleteProduct(id, name) {
            if (!confirm(`Delete "${name}"? This action cannot be undone.`)) return;

            this.deleting = id;
            try {
                await this.ajax('DELETE', `/products/${id}`);
                this.showToast(`"${name}" deleted.`, 'success');
                setTimeout(() => window.location.reload(), 500);
            } catch (e) {
                this.showToast(e.message || 'Delete failed.', 'error');
            } finally {
                this.deleting = null;
            }
        },

        // ── Utilities ──────────────────────────────────────────────────────

        async ajax(method, url, data = null) {
            const options = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                },
            };

            if (data && method !== 'GET') {
                options.body = JSON.stringify(data);
            }

            const response = await fetch(url, options);
            const json = await response.json();

            if (!response.ok) {
                const err = new Error(json.message || 'Request failed');
                err.errors = json.errors;
                throw err;
            }

            return json;
        },

        showToast(message, type = 'info') {
            const el = document.createElement('div');
            el.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-xl text-white shadow-lg
                             ${type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-blue-600'}`;
            el.textContent = message;
            document.body.appendChild(el);
            setTimeout(() => el.remove(), 3500);
        },
    };
}