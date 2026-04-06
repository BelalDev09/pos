// File: resources/js/pos/barcode.js

/**
 * BarcodeScanner — handles both USB HID scanners and camera-based scanning.
 * USB scanners work automatically via the focused input field.
 * Camera scanning uses QuaggaJS for real-time video scanning.
 */
class BarcodeScanner {
    constructor(options = {}) {
        this.onDetected = options.onDetected || (() => { });
        this.inputEl = options.inputEl || document.getElementById('barcode-input');
        this.buffer = '';
        this.lastScan = '';
        this.scanTimeout = null;
        this.isActive = false;
        this.cameraMode = false;
    }

    // ── USB HID Scanner Support ──────────────────────────────────────────────
    // USB barcode scanners send keystrokes very fast (< 50ms between keys)
    // followed by an Enter key. We detect this pattern to distinguish
    // scanner input from manual typing.

    start() {
        this.isActive = true;
        this._bindKeyboard();
        this._ensureFocus();
        return this;
    }

    stop() {
        this.isActive = false;
        if (this.cameraMode) {
            this._stopCamera();
        }
    }

    _bindKeyboard() {
        if (!this.inputEl) return;

        this.inputEl.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const value = this.inputEl.value.trim();
                if (value.length > 0) {
                    this._handleDetected(value);
                }
            }
        });
    }

    _ensureFocus() {
        // Keep barcode input focused at all times in POS mode
        document.addEventListener('keydown', (e) => {
            if (!this.isActive) return;
            if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) return;
            if (e.key.length === 1) {
                this.inputEl?.focus();
            }
        });

        // Re-focus when clicking outside form elements
        document.addEventListener('click', (e) => {
            if (!this.isActive) return;
            const tagName = e.target.tagName;
            if (!['INPUT', 'TEXTAREA', 'SELECT', 'BUTTON', 'A'].includes(tagName)) {
                setTimeout(() => this.inputEl?.focus(), 50);
            }
        });
    }

    _handleDetected(code) {
        // Debounce duplicate scans within 1.5s
        if (code === this.lastScan) return;

        this.lastScan = code;
        clearTimeout(this.scanTimeout);
        this.scanTimeout = setTimeout(() => { this.lastScan = ''; }, 1500);

        this.onDetected(code);
    }

    // ── Camera Scanner (QuaggaJS) ────────────────────────────────────────────

    async startCamera(viewportEl) {
        if (typeof Quagga === 'undefined') {
            await this._loadQuagga();
        }

        this.cameraMode = true;

        Quagga.init({
            inputStream: {
                name: 'Live',
                type: 'LiveStream',
                target: viewportEl,
                constraints: {
                    width: { min: 640 },
                    height: { min: 480 },
                    facingMode: 'environment',
                    aspectRatio: { min: 1, max: 2 },
                },
            },
            locator: { patchSize: 'medium', halfSample: true },
            numOfWorkers: navigator.hardwareConcurrency || 4,
            frequency: 10,
            decoder: {
                readers: [
                    'ean_reader', 'ean_8_reader',
                    'code_128_reader', 'code_39_reader',
                    'upc_reader', 'upc_e_reader',
                    'codabar_reader',
                ],
            },
            locate: true,
        }, (err) => {
            if (err) {
                console.error('[BarcodeScanner] Camera init failed:', err);
                return;
            }
            Quagga.start();
        });

        Quagga.onDetected((result) => {
            const code = result.codeResult.code;
            const confidence = result.codeResult.decodedCodes
                .filter(x => x.error !== undefined)
                .reduce((acc, x) => acc + x.error, 0);

            // Only accept high-confidence scans
            if (confidence < 0.1) {
                this._handleDetected(code);
            }
        });
    }

    _stopCamera() {
        if (typeof Quagga !== 'undefined') {
            Quagga.stop();
        }
    }

    async _loadQuagga() {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js';
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }
}

export default BarcodeScanner;