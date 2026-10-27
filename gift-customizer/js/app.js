/**
 * App Controller - Connects UI components, state management, and user interactions.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Customizer Engine
    const customizer = new GiftCustomizer('customizerCanvas');
    window.appCustomizer = customizer;

    // Toast manager
    const showToast = (message, icon = '✨') => {
        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = `<span>${icon}</span><span>${message}</span>`;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    };

    // 2. Render Product Selector
    const productGrid = document.getElementById('productGrid');
    const colorSwatchesContainer = document.getElementById('colorSwatches');
    const productPriceDisplay = document.getElementById('productPriceDisplay');

    function renderProducts() {
        productGrid.innerHTML = '';
        PRODUCTS.forEach(p => {
            const card = document.createElement('div');
            card.className = `product-option ${p.id === customizer.product.id ? 'selected' : ''}`;
            card.dataset.id = p.id;
            card.innerHTML = `
                ${p.badge ? `<span class="product-option-badge">${p.badge}</span>` : ''}
                <div class="product-option-title">${p.name}</div>
                <div class="product-option-price">₹${p.price.toLocaleString('en-IN')}</div>
            `;
            card.addEventListener('click', () => {
                selectProduct(p.id);
            });
            productGrid.appendChild(card);
        });
        renderColorVariants();
        updatePriceDisplay();
    }

    function selectProduct(productId) {
        customizer.setProduct(productId);
        
        // Update product selector UI
        document.querySelectorAll('.product-option').forEach(el => {
            el.classList.toggle('selected', el.dataset.id === productId);
        });

        // Sync text inputs
        document.getElementById('inputRecipientName').value = customizer.text.name;
        document.getElementById('inputCustomMessage').value = customizer.text.message;
        document.getElementById('inputSubtitleDate').value = customizer.text.date;

        renderColorVariants();
        updatePriceDisplay();
        showToast(`Switched to ${customizer.product.name}`);
    }
    window.selectProduct = selectProduct;

    function renderColorVariants() {
        colorSwatchesContainer.innerHTML = '';
        customizer.product.colors.forEach((col, idx) => {
            const swatch = document.createElement('div');
            swatch.className = `color-swatch ${col.hex.toLowerCase() === customizer.productColor.toLowerCase() ? 'active' : ''}`;
            swatch.style.backgroundColor = col.hex;
            swatch.title = col.name;
            swatch.addEventListener('click', () => {
                customizer.setProductColor(col.hex);
                document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
                swatch.classList.add('active');
            });
            colorSwatchesContainer.appendChild(swatch);
        });
    }

    function updatePriceDisplay() {
        if (productPriceDisplay) {
            productPriceDisplay.textContent = `₹${customizer.product.price.toLocaleString('en-IN')}`;
        }
    }

    // 3. Photo Upload & Drag-and-Drop
    const dropzone = document.getElementById('photoDropzone');
    const photoFileInput = document.getElementById('photoFileInput');
    const zoomSlider = document.getElementById('zoomSlider');
    const rotateSlider = document.getElementById('rotateSlider');
    const resetPhotoBtn = document.getElementById('resetPhotoBtn');
    const removePhotoBtn = document.getElementById('removePhotoBtn');

    // Trigger file chooser
    dropzone.addEventListener('click', () => photoFileInput.click());

    // Drag-and-drop feedback
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.add('dragover');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('dragover');
        });
    });

    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            handleUploadedFile(files[0]);
        }
    });

    photoFileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleUploadedFile(e.target.files[0]);
        }
    });

    function handleUploadedFile(file) {
        if (!file.type.match('image.*')) {
            alert('Please select a valid image file (PNG, JPG, WebP)');
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            customizer.loadPhoto(e.target.result, (success) => {
                if (success) {
                    zoomSlider.value = 1.0;
                    rotateSlider.value = 0;
                    showToast('Photo uploaded successfully! 🎉', '📷');
                }
            });
        };
        reader.readAsDataURL(file);
    }

    // Sample Photo Presets
    const samplePhotoPresets = document.getElementById('samplePhotoPresets');
    SAMPLE_PHOTOS.forEach(sample => {
        const chip = document.createElement('button');
        chip.className = 'preset-chip';
        chip.innerHTML = `<span>${sample.icon}</span> <span>${sample.title}</span>`;
        chip.addEventListener('click', () => {
            customizer.loadPhoto(sample.url, (success) => {
                if (success) {
                    zoomSlider.value = 1.0;
                    rotateSlider.value = 0;
                    showToast(`Loaded sample: ${sample.title}`, sample.icon);
                }
            });
        });
        samplePhotoPresets.appendChild(chip);
    });

    // Zoom & Rotate sliders
    zoomSlider.addEventListener('input', (e) => {
        customizer.updateTransform({ scale: parseFloat(e.target.value) });
    });

    window.addEventListener('customizer:scaleChange', (e) => {
        zoomSlider.value = e.detail.scale;
    });

    rotateSlider.addEventListener('input', (e) => {
        customizer.updateTransform({ rotation: parseInt(e.target.value, 10) });
    });

    resetPhotoBtn.addEventListener('click', () => {
        zoomSlider.value = 1.0;
        rotateSlider.value = 0;
        customizer.updateTransform({ scale: 1.0, rotation: 0, panX: 0, panY: 0 });
        showToast('Photo position reset');
    });

    removePhotoBtn.addEventListener('click', () => {
        customizer.clearPhoto();
        photoFileInput.value = '';
        showToast('Photo removed');
    });

    // Image Filters
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            customizer.updateTransform({ filter: btn.dataset.filter });
            showToast(`Filter applied: ${btn.textContent.trim()}`);
        });
    });

    // 4. Text Customization Controls
    const inputName = document.getElementById('inputRecipientName');
    const inputMessage = document.getElementById('inputCustomMessage');
    const inputDate = document.getElementById('inputSubtitleDate');
    const fontSelect = document.getElementById('fontSelect');
    const textSizeSlider = document.getElementById('textSizeSlider');

    // Populate initial text values from customizer
    inputName.value = customizer.text.name;
    inputMessage.value = customizer.text.message;
    inputDate.value = customizer.text.date;

    inputName.addEventListener('input', (e) => {
        customizer.updateText({ name: e.target.value });
    });

    inputMessage.addEventListener('input', (e) => {
        customizer.updateText({ message: e.target.value });
    });

    inputDate.addEventListener('input', (e) => {
        customizer.updateText({ date: e.target.value });
    });

    fontSelect.addEventListener('change', (e) => {
        customizer.updateText({ font: e.target.value });
    });

    textSizeSlider.addEventListener('input', (e) => {
        customizer.updateText({ sizeScale: parseFloat(e.target.value) });
    });

    // Text Color Swatches
    document.querySelectorAll('.text-color-dot').forEach(dot => {
        dot.addEventListener('click', () => {
            document.querySelectorAll('.text-color-dot').forEach(d => d.classList.remove('active'));
            dot.classList.add('active');
            customizer.updateText({ color: dot.dataset.color });
        });
    });

    // 5. View Mode Switcher (3D Realistic Mockup vs Flat Print)
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const mode = btn.dataset.mode;
            customizer.setPreviewMode(mode);
        });
    });

    // 6. Save Customization & Add to Cart
    const saveCustomizationBtn = document.getElementById('saveCustomizationBtn');
    const downloadPreviewBtn = document.getElementById('downloadPreviewBtn');
    const cartBtn = document.getElementById('cartBtn');
    const cartCountBadge = document.getElementById('cartCountBadge');
    const cartDrawerOverlay = document.getElementById('cartDrawerOverlay');
    const cartDrawer = document.getElementById('cartDrawer');
    const closeCartDrawerBtn = document.getElementById('closeCartDrawerBtn');
    const cartDrawerItems = document.getElementById('cartDrawerItems');
    const cartSubtotalDisplay = document.getElementById('cartSubtotalDisplay');
    const cartShippingDisplay = document.getElementById('cartShippingDisplay');
    const cartTotalDisplay = document.getElementById('cartTotalDisplay');
    const checkoutBtn = document.getElementById('checkoutBtn');

    // Modal
    const orderModalOverlay = document.getElementById('orderModalOverlay');
    const closeOrderModalBtn = document.getElementById('closeOrderModalBtn');
    const modalOrderDetails = document.getElementById('modalOrderDetails');

    saveCustomizationBtn.addEventListener('click', () => {
        // Generate high quality thumbnail snapshot
        const thumbnail = customizer.exportThumbnail(200, 200);
        const payload = customizer.getCustomizationPayload();

        // Add to Cart
        const addedItem = window.cartManager.addItem(customizer.product, payload, thumbnail);

        // Visual feedback
        showToast(`Saved customization for "${customizer.product.name}"! 🎁`, '⭐');
        
        // Post to optional backend server if available
        trySaveToServer(addedItem);

        // Open cart drawer after brief pause to show the saved product
        setTimeout(() => {
            openCartDrawer();
        }, 400);
    });

    // Download high-resolution PNG
    downloadPreviewBtn.addEventListener('click', () => {
        const dataUrl = customizer.exportHighResDataURL();
        const a = document.createElement('a');
        a.href = dataUrl;
        a.download = `customized-${customizer.product.id}-${Date.now()}.png`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        showToast('High-resolution preview downloaded! 📥', '💾');
    });

    // Cart Drawer Controls
    function openCartDrawer() {
        cartDrawerOverlay.classList.add('open');
        cartDrawer.classList.add('open');
        renderCartUI();
    }

    function closeCartDrawer() {
        cartDrawerOverlay.classList.remove('open');
        cartDrawer.classList.remove('open');
    }

    cartBtn.addEventListener('click', openCartDrawer);
    closeCartDrawerBtn.addEventListener('click', closeCartDrawer);
    cartDrawerOverlay.addEventListener('click', (e) => {
        if (e.target === cartDrawerOverlay) closeCartDrawer();
    });

    // Render Cart Drawer Contents
    function renderCartUI() {
        const items = window.cartManager.items;
        cartCountBadge.textContent = window.cartManager.getItemCount();

        if (items.length === 0) {
            cartDrawerItems.innerHTML = `
                <div class="empty-cart-state">
                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">🎁</div>
                    <div style="font-weight: 600; color: #1E293B;">Your cart is empty</div>
                    <div style="font-size: 0.85rem; margin-top: 0.25rem;">Customize a gift above and click "Save Customization"</div>
                </div>
            `;
            cartSubtotalDisplay.textContent = '₹0';
            cartShippingDisplay.textContent = '₹0';
            cartTotalDisplay.textContent = '₹0';
            checkoutBtn.disabled = true;
            checkoutBtn.style.opacity = '0.5';
            return;
        }

        checkoutBtn.disabled = false;
        checkoutBtn.style.opacity = '1';

        cartDrawerItems.innerHTML = '';
        items.forEach(item => {
            const itemEl = document.createElement('div');
            itemEl.className = 'cart-item-card';
            itemEl.innerHTML = `
                <button class="item-delete-btn" title="Remove Item" data-id="${item.id}">&times;</button>
                <img src="${item.thumbnail}" alt="${item.productName}" class="cart-item-thumb">
                <div class="cart-item-info">
                    <div>
                        <div class="cart-item-title">${item.productName}</div>
                        <div class="cart-item-meta">
                            <div><span class="cart-item-badge">Custom</span> "${item.customization.name || 'Untitled'}"</div>
                            ${item.customization.message ? `<div style="font-style: italic; color: #64748B;">“${item.customization.message}”</div>` : ''}
                            <div style="font-size: 0.72rem; color: #94A3B8;">${item.customization.hasPhoto ? '📷 Custom photo included' : 'No photo'}</div>
                        </div>
                    </div>
                    <div class="cart-item-bottom">
                        <div class="cart-item-price">₹${Math.round(item.unitPrice * item.quantity).toLocaleString('en-IN')}</div>
                        <div class="qty-controls">
                            <button class="qty-btn qty-dec" data-id="${item.id}">-</button>
                            <span class="qty-value">${item.quantity}</span>
                            <button class="qty-btn qty-inc" data-id="${item.id}">+</button>
                        </div>
                    </div>
                </div>
            `;

            // Delete item
            itemEl.querySelector('.item-delete-btn').addEventListener('click', () => {
                window.cartManager.removeItem(item.id);
                renderCartUI();
                showToast('Item removed from cart');
            });

            // Increment
            itemEl.querySelector('.qty-inc').addEventListener('click', () => {
                window.cartManager.updateQuantity(item.id, 1);
                renderCartUI();
            });

            // Decrement
            itemEl.querySelector('.qty-dec').addEventListener('click', () => {
                window.cartManager.updateQuantity(item.id, -1);
                renderCartUI();
            });

            cartDrawerItems.appendChild(itemEl);
        });

        cartSubtotalDisplay.textContent = `₹${Math.round(window.cartManager.getSubtotal()).toLocaleString('en-IN')}`;
        const shipping = window.cartManager.getShipping();
        cartShippingDisplay.textContent = shipping === 0 ? 'FREE' : `₹${Math.round(shipping).toLocaleString('en-IN')}`;
        cartTotalDisplay.textContent = `₹${Math.round(window.cartManager.getTotal()).toLocaleString('en-IN')}`;
    }

    // Listen to cart updates globally
    window.addEventListener('cart:updated', (e) => {
        cartCountBadge.textContent = e.detail.count;
    });

    // 7. Checkout Simulation Modal
    checkoutBtn.addEventListener('click', () => {
        const orderId = 'ORD-' + Math.floor(100000 + Math.random() * 900000);
        const count = window.cartManager.getItemCount();
        const total = Math.round(window.cartManager.getTotal()).toLocaleString('en-IN');

        modalOrderDetails.innerHTML = `
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 1.25rem; text-align: left; margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-weight: 700;">
                    <span>Order Number:</span>
                    <span style="color: #4F46E5;">${orderId}</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: #64748B;">
                    <span>Items Ordered:</span>
                    <span>${count} item(s)</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: #64748B;">
                    <span>Total Amount Paid:</span>
                    <span style="font-weight: 700; color: #0F172A;">₹${total}</span>
                </div>
                <div style="border-top: 1px dashed #CBD5E1; padding-top: 0.5rem; font-size: 0.85rem; color: #10B981; font-weight: 600;">
                    ✓ All custom photos &amp; text specifications forwarded to production shop.
                </div>
            </div>
        `;

        closeCartDrawer();
        orderModalOverlay.classList.add('open');
        window.cartManager.clearCart();
        renderCartUI();
    });

    closeOrderModalBtn.addEventListener('click', () => {
        orderModalOverlay.classList.remove('open');
    });

    // Helper: Persist to PHP backend server
    function trySaveToServer(cartItem) {
        fetch('api/save-customization.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(cartItem)
        }).then(res => {
            if (res.ok) {
                console.log('Saved customization payload to PHP backend.');
            }
        }).catch(() => {
            // LocalStorage already safely persists it
        });
    }

    // Init display
    renderProducts();
    renderCartUI();
    
    // Auto load the romantic sample photo so the user immediately sees a gorgeous preview out of the box!
    customizer.loadPhoto(SAMPLE_PHOTOS[0].url, () => {
        customizer.render();
    });
});
