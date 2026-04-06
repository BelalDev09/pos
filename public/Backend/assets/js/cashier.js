/* ═══════════════════════════════════════════════════════
   CASHIER DASHBOARD — JavaScript
   Dynamic interactions & AJAX calls to Laravel Controller
═══════════════════════════════════════════════════════ */

// ─── State ────────────────────────────────────────────────────────────────────
let currentOrder    = null;
let currentTab      = 'all';
let selectedRefType = 'full';
let selectedReason  = '';
let appliedDiscount = 0;
let appliedTip      = 0;

// Item emojis for display
const ITEM_EMOJIS = {
    'Beef Burger':    '🍔',
    'Caesar Salad':   '🥗',
    'Lager':          '🍺',
    'Quinoa Salad':   '🥗',
    'Grilled Salmon': '🐟',
    'Chips':          '🍟',
};

function emoji(name) {
    return ITEM_EMOJIS[name] || '🍽';
}

// ─── CSRF Helper ──────────────────────────────────────────────────────────────
function csrf() {
    return window.ROUTES?.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || '';
}

async function post(url, data) {
    const res = await fetch(url, {
        method:  'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf(),
            'Accept':       'application/json',
        },
        body: JSON.stringify(data),
    });
    return res.json();
}

// ─── TABS ─────────────────────────────────────────────────────────────────────
function switchTab(tab, el) {
    currentTab = tab;

    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');

    document.querySelectorAll('.order-card').forEach(card => {
        const status = card.dataset.status;
        const show   = tab === 'all' || status === tab;
        card.style.display = show ? '' : 'none';
    });
}

// ─── SEARCH ───────────────────────────────────────────────────────────────────
function filterOrders(query) {
    const q = query.toLowerCase();
    document.querySelectorAll('.order-card').forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(q) ? '' : 'none';
    });
}

// ─── SELECT ORDER ─────────────────────────────────────────────────────────────
async function selectOrder(orderId, el) {
    // Highlight selected
    document.querySelectorAll('.order-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');

    try {
        const order = await fetch(`${window.ROUTES.orderDetails}/${orderId}`, {
            headers: { 'Accept': 'application/json' }
        }).then(r => r.json());

        currentOrder    = order;
        appliedDiscount = order.payment.discount || 0;
        appliedTip      = order.payment.tip      || 0;

        renderOrderDetails(order);
    } catch (e) {
        showToast('Failed to load order', 'error');
    }
}

function renderOrderDetails(order) {
    document.getElementById('detailsEmpty').classList.add('hidden');
    const content = document.getElementById('detailsContent');
    content.classList.remove('hidden');

    // Header
    document.getElementById('detailTable').textContent = order.table;
    document.getElementById('detailMeta').textContent  =
        `${order.id}  ·  ${order.date}  ·  ${order.time}  ·  ${order.guests} guests`;

    const badge = document.getElementById('detailBadge');
    badge.textContent  = order.status.charAt(0).toUpperCase() + order.status.slice(1);
    badge.className    = `badge badge-${order.status}`;

    // Items
    const itemsContainer = document.getElementById('detailItems');
    itemsContainer.innerHTML = order.items.map(item => `
        <div class="detail-item">
            <div class="item-img">${emoji(item.name)}</div>
            <div class="item-info">
                <div class="item-name">${item.name}</div>
                <div class="item-qty">Qty: ${item.qty}</div>
            </div>
            <div class="item-price">$${(item.price * item.qty).toFixed(2)}</div>
        </div>
    `).join('');

    // Payment summary
    renderPaymentSummary(order);
}

function renderPaymentSummary(order) {
    const p   = order.payment;
    const dis = appliedDiscount;
    const tip = appliedTip;
    const tot = (p.subtotal + p.tax - dis + tip).toFixed(2);

    document.getElementById('paymentSummary').innerHTML = `
        <div class="section-label">Payment Summary</div>
        <div class="pay-row"><span>Subtotal</span><span>$${p.subtotal.toFixed(2)}</span></div>
        <div class="pay-row"><span>Tax (10%)</span><span>$${p.tax.toFixed(2)}</span></div>
        ${dis > 0 ? `<div class="pay-row"><span>Discount</span><span class="green">-$${dis.toFixed(2)}</span></div>` : ''}
        ${tip > 0 ? `<div class="pay-row"><span>Tips</span><span>$${tip.toFixed(2)}</span></div>` : ''}
        <div class="pay-row total"><span>Total</span><span>$${tot}</span></div>
    `;
}

// ─── PAYMENT MODAL ────────────────────────────────────────────────────────────
function openPaymentModal() {
    if (!currentOrder) return;

    const p   = currentOrder.payment;
    const dis = appliedDiscount;
    const tip = appliedTip;
    const tot = (p.subtotal + p.tax - dis + tip).toFixed(2);

    document.getElementById('payModalOrderId').textContent = currentOrder.id + ' · ' + currentOrder.table;
    document.getElementById('paySubtotal').textContent     = '$' + p.subtotal.toFixed(2);
    document.getElementById('payTax').textContent          = '$' + p.tax.toFixed(2);
    document.getElementById('payDiscount').textContent     = '-$' + dis.toFixed(2);
    document.getElementById('payTips').textContent         = '$' + tip.toFixed(2);
    document.getElementById('payTotal').textContent        = '$' + tot;
    document.getElementById('cashAmount').value            = '';
    document.getElementById('changeRow').style.display     = 'none';

    openModal('paymentModal');
}

function switchPayTab(tab, el) {
    document.querySelectorAll('.pay-tab').forEach(b => b.classList.remove('active'));
    el.classList.add('active');

    document.getElementById('cashSection').classList.toggle('hidden',  tab !== 'cash' && tab !== 'transfer');
    document.getElementById('splitSection').classList.toggle('hidden', tab !== 'split');
}

function calcChange() {
    if (!currentOrder) return;
    const p        = currentOrder.payment;
    const total    = parseFloat(p.subtotal) + parseFloat(p.tax) - appliedDiscount + appliedTip;
    const given    = parseFloat(document.getElementById('cashAmount').value) || 0;
    const change   = given - total;
    const row      = document.getElementById('changeRow');

    if (given > 0) {
        row.style.display = '';
        document.getElementById('changeAmount').textContent = (change >= 0 ? '$' : '-$') + Math.abs(change).toFixed(2);
        document.getElementById('changeAmount').className   = change >= 0 ? 'green' : 'red';
    } else {
        row.style.display = 'none';
    }
}

async function confirmPayment() {
    if (!currentOrder) return;

    const activeTab = document.querySelector('.pay-tab.active')?.textContent.trim().toLowerCase();

    if (activeTab === 'split') {
        const cashAmt  = document.getElementById('splitCash').value;
        const otherAmt = document.getElementById('splitOther').value;
        try {
            const res = await post(window.ROUTES.paymentSplit, {
                order_id:     currentOrder.id,
                cash_amount:  cashAmt,
                other_amount: otherAmt,
            });
            if (res.success) {
                closeModal('paymentModal');
                showToast('✅ Split payment processed! Change: $' + res.change, 'success');
            } else {
                showToast(res.error || 'Payment failed', 'error');
            }
        } catch (e) { showToast('Request failed', 'error'); }
    } else {
        const amount = document.getElementById('cashAmount').value;
        try {
            const res = await post(window.ROUTES.paymentCash, {
                order_id:     currentOrder.id,
                amount_given: amount,
            });
            if (res.success) {
                closeModal('paymentModal');
                showToast('✅ Payment confirmed! Change: $' + res.change, 'success');
            } else {
                showToast(res.error || 'Payment failed', 'error');
            }
        } catch (e) { showToast('Request failed', 'error'); }
    }
}

// ─── DISCOUNT MODAL ───────────────────────────────────────────────────────────
function openDiscountModal() {
    if (!currentOrder) return;
    openModal('discountModal');
}

function switchDiscountTab(type, el) {
    document.querySelectorAll('.pay-tab').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    document.querySelectorAll('.discount-section').forEach(s => s.classList.add('hidden'));
    document.getElementById(type + 'Section').classList.remove('hidden');
}

async function confirmDiscount() {
    if (!currentOrder) return;

    const activeTab = document.querySelector('#discountModal .pay-tab.active')?.textContent.trim();
    let discountType, value, promoCode;

    if (activeTab?.includes('Percentage')) {
        discountType = 'percentage';
        value        = document.getElementById('discountPct').value;
    } else if (activeTab === 'Fixed') {
        discountType = 'fixed';
        value        = document.getElementById('discountFixed').value;
    } else {
        discountType = 'promo';
        promoCode    = document.getElementById('discountPromo').value;
        value        = '0';
    }

    try {
        const res = await post(window.ROUTES.discount, {
            order_id:      currentOrder.id,
            discount_type: discountType,
            value:         value,
            promo_code:    promoCode,
        });

        if (res.success) {
            appliedDiscount = parseFloat(res.discount);
            currentOrder.payment.discount = appliedDiscount;
            renderPaymentSummary(currentOrder);
            closeModal('discountModal');
            showToast('✅ ' + res.message + ' (-$' + res.discount + ')', 'success');
        } else {
            showToast(res.error || 'Discount failed', 'error');
        }
    } catch (e) { showToast('Request failed', 'error'); }
}

// ─── TIPS MODAL ───────────────────────────────────────────────────────────────
function openTipModal() {
    if (!currentOrder) return;
    openModal('tipModal');
}

function setTip(amount) {
    document.getElementById('tipAmount').value = amount;
    document.querySelectorAll('.tip-preset').forEach(b => {
        b.classList.toggle('active', parseFloat(b.textContent.replace('$', '')) === amount);
    });
}

async function confirmTip() {
    if (!currentOrder) return;
    const tip = document.getElementById('tipAmount').value;
    try {
        const res = await post(window.ROUTES.tip, {
            order_id:   currentOrder.id,
            tip_amount: tip,
        });
        if (res.success) {
            appliedTip = parseFloat(res.tip);
            currentOrder.payment.tip = appliedTip;
            renderPaymentSummary(currentOrder);
            closeModal('tipModal');
            showToast('✅ Tip of $' + res.tip + ' added!', 'success');
        } else {
            showToast(res.error || 'Failed', 'error');
        }
    } catch (e) { showToast('Request failed', 'error'); }
}

// ─── REFUND MODALS ────────────────────────────────────────────────────────────
function openRefundModal() {
    if (!currentOrder) return;
    const total = currentOrder.payment.total;
    document.getElementById('fullRefundAmount').textContent = '$' + total.toFixed(2);
    document.getElementById('voidAmount').textContent       = '$' + total.toFixed(2);
    openModal('refundActionModal');
}

function setRefundType(type) {
    selectedRefType = type;
    document.querySelectorAll('.refund-option').forEach(opt => {
        opt.style.borderColor = opt.querySelector('input').value === type ? 'var(--green)' : '';
        opt.style.background  = opt.querySelector('input').value === type ? 'var(--green-light)' : '';
    });
    document.querySelector(`.refund-option input[value="${type}"]`).checked = true;
}

function proceedRefund() {
    closeModal('refundActionModal');

    if (selectedRefType === 'partial') {
        openPartialItemsModal();
    } else {
        openReasonModal();
    }
}

function openPartialItemsModal() {
    if (!currentOrder) return;

    const list = document.getElementById('refundItemsList');
    list.innerHTML = currentOrder.items.map(item => `
        <div class="refund-item">
            <input type="checkbox" name="refundItems" value="${item.id}" id="ri_${item.id}">
            <label for="ri_${item.id}" class="refund-item-info" style="cursor:pointer;flex:1">
                <div class="refund-item-name">${item.name}</div>
                <div class="refund-item-qty">x${item.qty}</div>
            </label>
            <div class="refund-item-price">$${(item.price * item.qty).toFixed(2)}</div>
        </div>
    `).join('');

    openModal('partialItemsModal');
}

function openReasonModal() {
    closeModal('partialItemsModal');
    selectedReason = '';
    document.querySelectorAll('.reason-chip').forEach(c => c.classList.remove('selected'));
    document.getElementById('reasonDescGroup').style.display = 'none';
    openModal('reasonModal');
}

function selectReason(el) {
    document.querySelectorAll('.reason-chip').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    selectedReason = el.textContent.trim();

    const descGroup = document.getElementById('reasonDescGroup');
    descGroup.style.display = selectedReason === 'Other' ? '' : 'none';
}

async function confirmRefund() {
    if (!currentOrder) return;

    let reason = selectedReason;
    if (reason === 'Other') {
        reason = document.getElementById('reasonDesc').value.trim() || 'Other';
    }

    if (!reason) { showToast('Please select a reason', 'error'); return; }

    const selectedItems = [...document.querySelectorAll('input[name="refundItems"]:checked')].map(c => c.value);

    try {
        const res = await post(window.ROUTES.refund, {
            order_id:    currentOrder.id,
            refund_type: selectedRefType,
            reason:      reason,
            items:       selectedItems,
        });

        if (res.success) {
            closeModal('reasonModal');
            const msg = selectedRefType === 'void'
                ? '🚫 Order voided'
                : `✅ ${res.message} — Refund: $${res.refund_amount}`;
            showToast(msg, 'success');
        } else {
            showToast(res.error || 'Refund failed', 'error');
        }
    } catch (e) { showToast('Request failed', 'error'); }
}

// ─── MODAL HELPERS ────────────────────────────────────────────────────────────
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

// Close on overlay click
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.add('hidden');
    }
});

// ─── TOAST ────────────────────────────────────────────────────────────────────
let toastTimer;

function showToast(msg, type = '') {
    clearTimeout(toastTimer);
    const toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.className   = `toast ${type}`;
    toast.classList.remove('hidden');
    toastTimer = setTimeout(() => toast.classList.add('hidden'), 3500);
}
