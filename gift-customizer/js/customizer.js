/**
 * Customizer Engine - HTML5 Canvas & Live Interactive Gift Mockup Renderer
 * Supports photo upload, drag-to-pan, pinch/wheel zoom, font styling, and realistic product rendering.
 */

class GiftCustomizer {
    constructor(canvasId) {
        this.canvas = document.getElementById(canvasId);
        if (!this.canvas) {
            throw new Error(`Canvas with id ${canvasId} not found`);
        }
        this.ctx = this.canvas.getContext('2d');
        
        // Customization State
        this.product = PRODUCTS[0];
        this.productColor = this.product.colors[0].hex;
        this.previewMode = 'mockup'; // 'mockup' or 'flat'
        
        this.photoImg = null;
        this.photoDataUrl = null;
        this.photoTransform = {
            scale: 1.0,
            panX: 0,
            panY: 0,
            rotation: 0,
            filter: 'normal' // 'normal', 'grayscale', 'sepia', 'warm', 'vintage'
        };
        
        this.text = {
            name: this.product.defaultText.name,
            message: this.product.defaultText.message,
            date: this.product.defaultText.date,
            font: "'Playfair Display', serif",
            color: '#1E293B',
            sizeScale: 1.0,
            align: 'center'
        };

        // Interaction state for dragging photo directly on canvas
        this.isDragging = false;
        this.dragStartX = 0;
        this.dragStartY = 0;
        this.initialPanX = 0;
        this.initialPanY = 0;

        this.initCanvasInteraction();
    }

    setProduct(productId) {
        const prod = PRODUCTS.find(p => p.id === productId);
        if (!prod) return;
        this.product = prod;
        this.productColor = prod.colors[0].hex;
        
        // Reset or adapt texts if empty
        if (!this.text.name && prod.defaultText) {
            this.text.name = prod.defaultText.name;
            this.text.message = prod.defaultText.message;
            this.text.date = prod.defaultText.date;
        }
        
        // Re-render
        this.render();
    }

    setProductColor(hex) {
        this.productColor = hex;
        this.render();
    }

    setPreviewMode(mode) {
        this.previewMode = mode;
        this.render();
    }

    loadPhoto(imageSource, callback) {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = () => {
            this.photoImg = img;
            this.photoDataUrl = imageSource;
            // Reset transform center
            this.photoTransform.panX = 0;
            this.photoTransform.panY = 0;
            this.photoTransform.scale = 1.0;
            this.render();
            if (callback) callback(true);
        };
        img.onerror = () => {
            console.error('Failed to load image:', imageSource);
            if (callback) callback(false);
        };
        img.src = imageSource;
    }

    clearPhoto() {
        this.photoImg = null;
        this.photoDataUrl = null;
        this.photoTransform.panX = 0;
        this.photoTransform.panY = 0;
        this.photoTransform.scale = 1.0;
        this.render();
    }

    updateTransform(newProps) {
        Object.assign(this.photoTransform, newProps);
        this.render();
    }

    updateText(newTextProps) {
        Object.assign(this.text, newTextProps);
        this.render();
    }

    initCanvasInteraction() {
        const canvas = this.canvas;

        const getPos = (e) => {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return {
                x: (clientX - rect.left) * scaleX,
                y: (clientY - rect.top) * scaleY
            };
        };

        const onDown = (e) => {
            if (!this.photoImg) return;
            const pos = getPos(e);
            this.isDragging = true;
            this.dragStartX = pos.x;
            this.dragStartY = pos.y;
            this.initialPanX = this.photoTransform.panX;
            this.initialPanY = this.photoTransform.panY;
            canvas.style.cursor = 'grabbing';
            if (e.touches) e.preventDefault();
        };

        const onMove = (e) => {
            if (!this.isDragging || !this.photoImg) return;
            const pos = getPos(e);
            const dx = pos.x - this.dragStartX;
            const dy = pos.y - this.dragStartY;
            this.photoTransform.panX = this.initialPanX + dx;
            this.photoTransform.panY = this.initialPanY + dy;
            this.render();
            if (e.touches) e.preventDefault();
        };

        const onUp = () => {
            this.isDragging = false;
            canvas.style.cursor = this.photoImg ? 'grab' : 'default';
        };

        canvas.addEventListener('mousedown', onDown);
        window.addEventListener('mousemove', onMove);
        window.addEventListener('mouseup', onUp);

        canvas.addEventListener('touchstart', onDown, { passive: false });
        window.addEventListener('touchmove', onMove, { passive: false });
        window.addEventListener('touchend', onUp);

        // Mouse wheel to zoom
        canvas.addEventListener('wheel', (e) => {
            if (!this.photoImg) return;
            e.preventDefault();
            const delta = e.deltaY < 0 ? 0.08 : -0.08;
            let newScale = Math.min(Math.max(this.photoTransform.scale + delta, 0.4), 3.0);
            this.photoTransform.scale = Number(newScale.toFixed(2));
            
            // Dispatch event for UI slider sync
            const event = new CustomEvent('customizer:scaleChange', { detail: { scale: this.photoTransform.scale } });
            window.dispatchEvent(event);
            
            this.render();
        }, { passive: false });
    }

    /**
     * Master Render Loop
     */
    render() {
        const { ctx, canvas } = this;
        const width = canvas.width;
        const height = canvas.height;

        ctx.clearRect(0, 0, width, height);

        if (this.previewMode === 'flat') {
            this.renderFlatPrintView();
            return;
        }

        // Render realistic 3D mockup according to product type
        switch (this.product.mockupType) {
            case 'mug':
                this.renderMugMockup();
                break;
            case 'frame':
                this.renderFrameMockup();
                break;
            case 'lamp':
                this.renderLampMockup();
                break;
            case 'cushion':
                this.renderCushionMockup();
                break;
            case 'tshirt':
                this.renderTshirtMockup();
                break;
            case 'keychain':
                this.renderKeychainMockup();
                break;
            default:
                this.renderMugMockup();
        }
    }

    /**
     * 1. Flat Production Print View
     */
    renderFlatPrintView() {
        const { ctx, canvas } = this;
        const W = canvas.width;
        const H = canvas.height;

        // Clean technical blueprint / artboard background
        ctx.fillStyle = '#F8FAFC';
        ctx.fillRect(0, 0, W, H);

        // Grid lines
        ctx.strokeStyle = '#E2E8F0';
        ctx.lineWidth = 1;
        for (let x = 40; x < W; x += 40) {
            ctx.beginPath(); ctx.moveTo(x, 0); ctx.lineTo(x, H); ctx.stroke();
        }
        for (let y = 40; y < H; y += 40) {
            ctx.beginPath(); ctx.moveTo(0, y); ctx.lineTo(W, y); ctx.stroke();
        }

        // Printable Zone Card
        const margin = 80;
        const printW = W - margin * 2;
        const printH = H - margin * 2;

        ctx.save();
        ctx.shadowColor = 'rgba(0,0,0,0.06)';
        ctx.shadowBlur = 20;
        ctx.shadowOffsetY = 10;
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(margin, margin, printW, printH);
        ctx.restore();

        // Print Border & Safe Margins
        ctx.strokeStyle = '#3B82F6';
        ctx.lineWidth = 2;
        ctx.setLineDash([6, 6]);
        ctx.strokeRect(margin + 20, margin + 20, printW - 40, printH - 40);
        ctx.setLineDash([]);

        // Header label
        ctx.fillStyle = '#64748B';
        ctx.font = '14px "Segoe UI", sans-serif';
        ctx.textAlign = 'left';
        ctx.fillText(`PRODUCTION PRINT TEMPLATE • ${this.product.name.toUpperCase()} (SAFE AREA: 95%)`, margin + 24, margin - 15);

        // Render photo in print zone
        const photoZone = {
            x: margin + 80,
            y: margin + 60,
            w: printW - 160,
            h: printH - 240
        };

        this.renderPhoto(photoZone.x, photoZone.y, photoZone.w, photoZone.h, 'rounded', 16);

        // Render text in lower zone
        const textZone = {
            x: W / 2,
            nameY: margin + printH - 140,
            msgY: margin + printH - 95,
            dateY: margin + printH - 60
        };

        this.renderTypography(textZone.x, textZone.nameY, textZone.msgY, textZone.dateY, false);
    }

    /**
     * 2. Ceramic Mug Mockup
     */
    renderMugMockup() {
        const { ctx, canvas } = this;
        const W = canvas.width;
        const H = canvas.height;

        // Elegant studio background with soft warm vignette
        const bgGrad = ctx.createRadialGradient(W / 2, H / 2 - 40, 100, W / 2, H / 2, W * 0.7);
        bgGrad.addColorStop(0, '#FFFFFF');
        bgGrad.addColorStop(1, '#E2E8F0');
        ctx.fillStyle = bgGrad;
        ctx.fillRect(0, 0, W, H);

        // Tabletop shadow
        ctx.save();
        ctx.beginPath();
        ctx.ellipse(400, 680, 200, 28, 0, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(15, 23, 42, 0.18)';
        ctx.filter = 'blur(14px)';
        ctx.fill();
        ctx.restore();

        const mugX = 230;
        const mugY = 220;
        const mugW = 340;
        const mugH = 430;
        const mugRadius = 36;
        const baseColor = this.productColor;

        // --- Ceramic Handle on Right ---
        ctx.save();
        ctx.beginPath();
        ctx.arc(570, 425, 95, -Math.PI * 0.42, Math.PI * 0.42, false);
        ctx.lineWidth = 42;
        ctx.strokeStyle = baseColor;
        ctx.lineCap = 'round';
        ctx.stroke();

        // Handle inner shadow
        ctx.lineWidth = 36;
        const handleGrad = ctx.createLinearGradient(540, 330, 670, 425);
        handleGrad.addColorStop(0, 'rgba(0,0,0,0.12)');
        handleGrad.addColorStop(0.5, 'rgba(255,255,255,0.4)');
        handleGrad.addColorStop(1, 'rgba(0,0,0,0.22)');
        ctx.strokeStyle = handleGrad;
        ctx.stroke();
        ctx.restore();

        // --- Main Mug Body ---
        ctx.save();
        ctx.beginPath();
        ctx.roundRect(mugX, mugY, mugW, mugH, [0, 0, mugRadius, mugRadius]);
        ctx.fillStyle = baseColor;
        ctx.fill();

        // Clip to Mug Body for Photo & Text
        ctx.save();
        ctx.beginPath();
        ctx.roundRect(mugX, mugY, mugW, mugH, [0, 0, mugRadius, mugRadius]);
        ctx.clip();

        // Base 3D Cylindrical Shading
        const cylGrad = ctx.createLinearGradient(mugX, 0, mugX + mugW, 0);
        cylGrad.addColorStop(0.0, 'rgba(0, 0, 0, 0.32)');
        cylGrad.addColorStop(0.12, 'rgba(0, 0, 0, 0.08)');
        cylGrad.addColorStop(0.28, 'rgba(255, 255, 255, 0.35)');
        cylGrad.addColorStop(0.55, 'rgba(0, 0, 0, 0.02)');
        cylGrad.addColorStop(0.9, 'rgba(0, 0, 0, 0.15)');
        cylGrad.addColorStop(1.0, 'rgba(0, 0, 0, 0.40)');
        ctx.fillStyle = cylGrad;
        ctx.fillRect(mugX, mugY, mugW, mugH);

        // --- Custom Photo Placement ---
        const photoZone = this.product.printArea.photo;
        this.renderPhoto(photoZone.x, photoZone.y, photoZone.w, photoZone.h, photoZone.shape, photoZone.rx);

        // --- Custom Text Placement ---
        const pa = this.product.printArea;
        this.renderTypography(pa.name.x, pa.name.y, pa.message.y, pa.date.y, false);

        // Glossy Cylindrical Specular Glare across mug surface
        const glossGrad = ctx.createLinearGradient(mugX + 50, mugY, mugX + 170, mugY);
        glossGrad.addColorStop(0.0, 'rgba(255, 255, 255, 0.0)');
        glossGrad.addColorStop(0.5, 'rgba(255, 255, 255, 0.45)');
        glossGrad.addColorStop(1.0, 'rgba(255, 255, 255, 0.0)');
        ctx.fillStyle = glossGrad;
        ctx.fillRect(mugX, mugY, mugW, mugH);

        ctx.restore(); // Unclip

        // --- Mug Top Oval Opening (Rim) ---
        ctx.beginPath();
        ctx.ellipse(mugX + mugW / 2, mugY, mugW / 2, 22, 0, 0, Math.PI * 2);
        ctx.fillStyle = '#F8FAFC';
        ctx.fill();
        ctx.lineWidth = 4;
        ctx.strokeStyle = 'rgba(0,0,0,0.1)';
        ctx.stroke();

        // Inner cup cavity dark gradient
        ctx.beginPath();
        ctx.ellipse(mugX + mugW / 2, mugY + 2, (mugW / 2) - 8, 16, 0, 0, Math.PI * 2);
        const innerGrad = ctx.createLinearGradient(0, mugY - 10, 0, mugY + 20);
        innerGrad.addColorStop(0, '#0F172A');
        innerGrad.addColorStop(0.6, '#334155');
        innerGrad.addColorStop(1, '#64748B');
        ctx.fillStyle = innerGrad;
        ctx.fill();

        // Outer contour stroke
        ctx.beginPath();
        ctx.roundRect(mugX, mugY, mugW, mugH, [0, 0, mugRadius, mugRadius]);
        ctx.strokeStyle = 'rgba(0, 0, 0, 0.12)';
        ctx.lineWidth = 2;
        ctx.stroke();

        ctx.restore();
    }

    /**
     * 3. Nordic Wooden Frame Mockup
     */
    renderFrameMockup() {
        const { ctx, canvas } = this;
        const W = canvas.width;
        const H = canvas.height;

        // Elegant interior wall background
        const wallGrad = ctx.createRadialGradient(W / 2, 350, 100, W / 2, 350, W * 0.7);
        wallGrad.addColorStop(0, '#FDFBF7');
        wallGrad.addColorStop(1, '#E7E0D3');
        ctx.fillStyle = wallGrad;
        ctx.fillRect(0, 0, W, H);

        // Frame Outer Bounds
        const fx = 160;
        const fy = 110;
        const fw = 480;
        const fh = 580;
        const frameThick = 44;

        // Cast shadow behind frame
        ctx.save();
        ctx.shadowColor = 'rgba(40, 25, 10, 0.28)';
        ctx.shadowBlur = 32;
        ctx.shadowOffsetY = 24;
        ctx.fillStyle = this.productColor;
        ctx.fillRect(fx, fy, fw, fh);
        ctx.restore();

        // Wood texture bevel & highlights
        ctx.save();
        ctx.strokeStyle = 'rgba(255,255,255,0.3)';
        ctx.lineWidth = 2;
        ctx.strokeRect(fx + 2, fy + 2, fw - 4, fh - 4);
        ctx.strokeStyle = 'rgba(0,0,0,0.35)';
        ctx.strokeRect(fx + frameThick - 1, fy + frameThick - 1, fw - frameThick * 2 + 2, fh - frameThick * 2 + 2);

        // Diagonal miter lines at 4 corners
        ctx.strokeStyle = 'rgba(0,0,0,0.18)';
        ctx.lineWidth = 1.5;
        [[fx, fy, fx + frameThick, fy + frameThick],
         [fx + fw, fy, fx + fw - frameThick, fy + frameThick],
         [fx, fy + fh, fx + frameThick, fy + fh - frameThick],
         [fx + fw, fy + fh, fx + fw - frameThick, fy + fh - frameThick]
        ].forEach(([x1, y1, x2, y2]) => {
            ctx.beginPath(); ctx.moveTo(x1, y1); ctx.lineTo(x2, y2); ctx.stroke();
        });
        ctx.restore();

        // Passe-partout / Mat Board (Off-white paper)
        const mx = fx + frameThick;
        const my = fy + frameThick;
        const mw = fw - frameThick * 2;
        const mh = fh - frameThick * 2;

        ctx.save();
        ctx.fillStyle = '#FAF8F5';
        ctx.fillRect(mx, my, mw, mh);

        // Inner bevel cutout shadow
        const px = this.product.printArea.photo.x;
        const py = this.product.printArea.photo.y;
        const pw = this.product.printArea.photo.w;
        const ph = this.product.printArea.photo.h;

        ctx.shadowColor = 'rgba(0,0,0,0.2)';
        ctx.shadowBlur = 10;
        ctx.shadowOffsetY = 4;
        ctx.strokeRect(px, py, pw, ph);
        ctx.restore();

        // Render Photo
        this.renderPhoto(px, py, pw, ph, 'square', 0);

        // Laser Engraved Brass / Silver Nameplate Banner
        const plateW = 320;
        const plateH = 92;
        const plateX = (W - plateW) / 2;
        const plateY = 560;

        ctx.save();
        ctx.shadowColor = 'rgba(0,0,0,0.12)';
        ctx.shadowBlur = 8;
        ctx.shadowOffsetY = 3;
        
        // Metallic brushed plate gradient
        const plateGrad = ctx.createLinearGradient(plateX, plateY, plateX + plateW, plateY + plateH);
        plateGrad.addColorStop(0, '#EFE8DE');
        plateGrad.addColorStop(0.5, '#FFFDF9');
        plateGrad.addColorStop(1, '#E6DCCF');
        ctx.fillStyle = plateGrad;
        ctx.roundRect(plateX, plateY, plateW, plateH, 8);
        ctx.fill();

        ctx.strokeStyle = '#D1BA9C';
        ctx.lineWidth = 1.5;
        ctx.stroke();

        // 4 corner tiny brass screws
        [[plateX + 8, plateY + 8], [plateX + plateW - 8, plateY + 8],
         [plateX + 8, plateY + plateH - 8], [plateX + plateW - 8, plateY + plateH - 8]
        ].forEach(([sx, sy]) => {
            ctx.beginPath();
            ctx.arc(sx, sy, 3, 0, Math.PI * 2);
            ctx.fillStyle = '#B49B76';
            ctx.fill();
        });
        ctx.restore();

        // Engraved text styling
        this.renderTypography(W / 2, plateY + 30, plateY + 56, plateY + 78, true);

        // Glass reflection sheen diagonally
        ctx.save();
        ctx.beginPath();
        ctx.moveTo(mx, my);
        ctx.lineTo(mx + mw * 0.45, my);
        ctx.lineTo(mx, my + mh * 0.7);
        ctx.closePath();
        const glassGrad = ctx.createLinearGradient(mx, my, mx + mw * 0.4, my + mh * 0.6);
        glassGrad.addColorStop(0, 'rgba(255,255,255,0.22)');
        glassGrad.addColorStop(1, 'rgba(255,255,255,0.0)');
        ctx.fillStyle = glassGrad;
        ctx.fill();
        ctx.restore();
    }

    /**
     * 4. Acrylic LED Lamp Mockup
     */
    renderLampMockup() {
        const { ctx, canvas } = this;
        const W = canvas.width;
        const H = canvas.height;

        // Dark mood background for LED glow effect
        const bgGrad = ctx.createRadialGradient(W / 2, 400, 60, W / 2, 400, W * 0.7);
        bgGrad.addColorStop(0, '#1E1B4B');
        bgGrad.addColorStop(0.6, '#0F172A');
        bgGrad.addColorStop(1, '#020617');
        ctx.fillStyle = bgGrad;
        ctx.fillRect(0, 0, W, H);

        const acrylicX = 220;
        const acrylicY = 120;
        const acrylicW = 360;
        const acrylicH = 480;
        const baseLEDColor = this.productColor; // warm amber or chosen LED hue

        // Ambient LED glow burst from base
        ctx.save();
        const glowGrad = ctx.createRadialGradient(400, 590, 20, 400, 460, 260);
        glowGrad.addColorStop(0, baseLEDColor);
        glowGrad.addColorStop(0.4, 'rgba(255, 200, 120, 0.4)');
        glowGrad.addColorStop(1, 'rgba(0, 0, 0, 0.0)');
        ctx.fillStyle = glowGrad;
        ctx.fillRect(100, 200, 600, 450);
        ctx.restore();

        // --- Acrylic Plate (Transparent Crystal look) ---
        ctx.save();
        ctx.beginPath();
        ctx.roundRect(acrylicX, acrylicY, acrylicW, acrylicH, 28);
        ctx.fillStyle = 'rgba(255, 255, 255, 0.06)';
        ctx.fill();

        // Edge refraction lighting
        ctx.lineWidth = 3.5;
        ctx.strokeStyle = baseLEDColor;
        ctx.shadowColor = baseLEDColor;
        ctx.shadowBlur = 18;
        ctx.stroke();
        ctx.restore();

        // --- Render Engraved Photo in Circle ---
        const photoZone = this.product.printArea.photo;
        ctx.save();
        // Give photo etched luminous appearance
        ctx.shadowColor = baseLEDColor;
        ctx.shadowBlur = 12;
        this.renderPhoto(photoZone.x, photoZone.y, photoZone.w, photoZone.h, 'circle', 0);
        ctx.restore();

        // --- Render Luminous Engraved Text ---
        const pa = this.product.printArea;
        ctx.save();
        ctx.shadowColor = baseLEDColor;
        ctx.shadowBlur = 15;
        this.renderTypography(pa.name.x, pa.name.y, pa.message.y, pa.date.y, false, baseLEDColor);
        ctx.restore();

        // --- Solid Beechwood Lighted Base Stand ---
        const baseX = 180;
        const baseY = 575;
        const baseW = 440;
        const baseH = 85;

        // Base shadow on table
        ctx.save();
        ctx.beginPath();
        ctx.ellipse(400, 670, 230, 25, 0, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(0,0,0,0.6)';
        ctx.filter = 'blur(12px)';
        ctx.fill();
        ctx.restore();

        // Solid wood pedestal
        ctx.save();
        ctx.beginPath();
        ctx.roundRect(baseX, baseY, baseW, baseH, [14, 14, 18, 18]);
        const woodGrad = ctx.createLinearGradient(baseX, baseY, baseX + baseW, baseY);
        woodGrad.addColorStop(0, '#784C28');
        woodGrad.addColorStop(0.3, '#A06B3E');
        woodGrad.addColorStop(0.7, '#8C5A30');
        woodGrad.addColorStop(1, '#5E381A');
        ctx.fillStyle = woodGrad;
        ctx.fill();

        // LED slot inset at top of wood base
        ctx.fillStyle = '#3E2412';
        ctx.fillRect(acrylicX - 10, baseY + 4, acrylicW + 20, 10);

        // Core LED light strip glowing directly beneath acrylic sheet
        ctx.fillStyle = '#FFFFFF';
        ctx.shadowColor = baseLEDColor;
        ctx.shadowBlur = 24;
        ctx.fillRect(acrylicX, baseY + 6, acrylicW, 6);

        // Bevel highlight on wood front edge
        ctx.strokeStyle = 'rgba(255,255,255,0.25)';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(baseX + 10, baseY + 2);
        ctx.lineTo(baseX + baseW - 10, baseY + 2);
        ctx.stroke();
        ctx.restore();
    }

    /**
     * 5. Velvet Throw Pillow Mockup
     */
    renderCushionMockup() {
        const { ctx, canvas } = this;
        const W = canvas.width;
        const H = canvas.height;

        // Soft lifestyle bedroom / couch backdrop
        ctx.fillStyle = '#F1F5F9';
        ctx.fillRect(0, 0, W, H);

        const cx = 170;
        const cy = 130;
        const cw = 460;
        const ch = 520;
        const cornerR = 70;

        // Cushion cast drop shadow
        ctx.save();
        ctx.beginPath();
        ctx.ellipse(400, 670, 240, 36, 0, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(15, 23, 42, 0.22)';
        ctx.filter = 'blur(16px)';
        ctx.fill();
        ctx.restore();

        // Cushion body with puffy pillowy curvature
        ctx.save();
        ctx.beginPath();
        ctx.roundRect(cx, cy, cw, ch, cornerR);
        ctx.fillStyle = this.productColor;
        ctx.fill();

        // Puffy 3D cushion volume shading
        const pillowGrad = ctx.createRadialGradient(W / 2, H / 2 - 20, 80, W / 2, H / 2, cw * 0.75);
        pillowGrad.addColorStop(0, 'rgba(255,255,255,0.25)');
        pillowGrad.addColorStop(0.65, 'rgba(0,0,0,0.02)');
        pillowGrad.addColorStop(1, 'rgba(0,0,0,0.28)');
        ctx.fillStyle = pillowGrad;
        ctx.fill();

        // Piping / seam border around edge
        ctx.strokeStyle = 'rgba(0,0,0,0.14)';
        ctx.lineWidth = 4;
        ctx.stroke();

        ctx.strokeStyle = 'rgba(255,255,255,0.28)';
        ctx.lineWidth = 2;
        ctx.roundRect(cx + 8, cy + 8, cw - 16, ch - 16, cornerR - 6);
        ctx.stroke();

        // Clip custom zone for photo & text
        ctx.save();
        ctx.roundRect(cx + 14, cy + 14, cw - 28, ch - 28, cornerR - 10);
        ctx.clip();

        // Render Photo
        const photoZone = this.product.printArea.photo;
        this.renderPhoto(photoZone.x, photoZone.y, photoZone.w, photoZone.h, photoZone.shape, photoZone.rx);

        // Render Typography
        const pa = this.product.printArea;
        this.renderTypography(pa.name.x, pa.name.y, pa.message.y, pa.date.y, false);

        ctx.restore(); // Unclip

        ctx.restore();
    }

    /**
     * 6. Custom Cotton T-Shirt Mockup
     */
    renderTshirtMockup() {
        const { ctx, canvas } = this;
        const W = canvas.width;
        const H = canvas.height;

        // Clean photography backdrop
        ctx.fillStyle = '#F8FAFC';
        ctx.fillRect(0, 0, W, H);

        // Ground shadow
        ctx.save();
        ctx.beginPath();
        ctx.ellipse(400, 710, 260, 32, 0, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(15, 23, 42, 0.18)';
        ctx.filter = 'blur(16px)';
        ctx.fill();
        ctx.restore();

        // --- Realistic T-Shirt Geometry ---
        ctx.save();
        ctx.beginPath();
        // Start at left shoulder / collar
        ctx.moveTo(330, 150);
        // Collar dip (front neckline)
        ctx.bezierCurveTo(360, 210, 440, 210, 470, 150);
        // Right shoulder slope
        ctx.lineTo(600, 210);
        // Right sleeve outer edge
        ctx.lineTo(660, 320);
        // Right sleeve hem
        ctx.lineTo(580, 360);
        // Right underarm
        ctx.lineTo(540, 310);
        // Right torso side
        ctx.bezierCurveTo(550, 480, 545, 620, 555, 680);
        // Bottom hem
        ctx.bezierCurveTo(450, 700, 350, 700, 245, 680);
        // Left torso side
        ctx.bezierCurveTo(255, 620, 250, 480, 260, 310);
        // Left underarm
        ctx.lineTo(220, 360);
        // Left sleeve hem
        ctx.lineTo(140, 320);
        // Left sleeve outer edge
        ctx.lineTo(200, 210);
        ctx.closePath();

        // Shirt fill color
        ctx.fillStyle = this.productColor;
        ctx.fill();

        // Subtle 3D fabric fold & volume shading
        const fabricGrad = ctx.createLinearGradient(150, 150, 650, 700);
        fabricGrad.addColorStop(0, 'rgba(255,255,255,0.18)');
        fabricGrad.addColorStop(0.35, 'rgba(255,255,255,0.02)');
        fabricGrad.addColorStop(0.7, 'rgba(0,0,0,0.08)');
        fabricGrad.addColorStop(1, 'rgba(0,0,0,0.24)');
        ctx.fillStyle = fabricGrad;
        ctx.fill();

        // Shirt contour stroke
        ctx.strokeStyle = 'rgba(0,0,0,0.12)';
        ctx.lineWidth = 2.5;
        ctx.stroke();

        // Collar Ribbing (Crew neck)
        ctx.beginPath();
        ctx.moveTo(320, 145);
        ctx.bezierCurveTo(360, 225, 440, 225, 480, 145);
        ctx.strokeStyle = 'rgba(0,0,0,0.2)';
        ctx.lineWidth = 5;
        ctx.stroke();

        ctx.beginPath();
        ctx.moveTo(330, 150);
        ctx.bezierCurveTo(360, 210, 440, 210, 470, 150);
        ctx.strokeStyle = 'rgba(255,255,255,0.25)';
        ctx.lineWidth = 2;
        ctx.stroke();

        // Sleeve seams
        ctx.beginPath();
        ctx.moveTo(200, 210);
        ctx.quadraticCurveTo(240, 260, 260, 310);
        ctx.moveTo(600, 210);
        ctx.quadraticCurveTo(560, 260, 540, 310);
        ctx.strokeStyle = 'rgba(0,0,0,0.1)';
        ctx.lineWidth = 1.5;
        ctx.stroke();

        // Clip Chest Printable Area
        ctx.save();
        ctx.beginPath();
        ctx.roundRect(260, 230, 280, 400, 16);
        ctx.clip();

        // Render Photo onto T-shirt chest
        const photoZone = this.product.printArea.photo;
        this.renderPhoto(photoZone.x, photoZone.y, photoZone.w, photoZone.h, photoZone.shape, photoZone.rx);

        // Render Typography
        const pa = this.product.printArea;
        this.renderTypography(pa.name.x, pa.name.y, pa.message.y, pa.date.y, false);

        ctx.restore(); // Unclip

        // Subtle fabric texture overlay over print
        ctx.save();
        const clothOver = ctx.createLinearGradient(280, 250, 520, 550);
        clothOver.addColorStop(0, 'rgba(255,255,255,0.06)');
        clothOver.addColorStop(0.5, 'rgba(0,0,0,0.0)');
        clothOver.addColorStop(1, 'rgba(0,0,0,0.08)');
        ctx.fillStyle = clothOver;
        ctx.fillRect(260, 230, 280, 400);
        ctx.restore();

        ctx.restore();
    }

    /**
     * 7. Crystal Laser Keychain Mockup
     */
    renderKeychainMockup() {
        const { ctx, canvas } = this;
        const W = canvas.width;
        const H = canvas.height;

        // Gradient table backdrop
        const bgGrad = ctx.createRadialGradient(W / 2, H / 2, 80, W / 2, H / 2, W * 0.7);
        bgGrad.addColorStop(0, '#FFFFFF');
        bgGrad.addColorStop(1, '#E2E8F0');
        ctx.fillStyle = bgGrad;
        ctx.fillRect(0, 0, W, H);

        // Ground shadow
        ctx.save();
        ctx.beginPath();
        ctx.ellipse(400, 680, 180, 24, 0, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(15, 23, 42, 0.2)';
        ctx.filter = 'blur(14px)';
        ctx.fill();
        ctx.restore();

        // --- 1. Metallic Split Keyring ---
        ctx.save();
        const ringX = 400;
        const ringY = 120;
        const ringR = 45;

        // Ring outer
        ctx.beginPath();
        ctx.arc(ringX, ringY, ringR, 0, Math.PI * 2);
        const silverGrad = ctx.createLinearGradient(ringX - ringR, ringY - ringR, ringX + ringR, ringY + ringR);
        silverGrad.addColorStop(0, '#94A3B8');
        silverGrad.addColorStop(0.3, '#F8FAFC');
        silverGrad.addColorStop(0.7, '#64748B');
        silverGrad.addColorStop(1, '#CBD5E1');
        ctx.lineWidth = 10;
        ctx.strokeStyle = silverGrad;
        ctx.stroke();

        // Ring inner bevel
        ctx.lineWidth = 2;
        ctx.strokeStyle = 'rgba(255,255,255,0.6)';
        ctx.stroke();

        // --- 2. Metal Chain Links ---
        for (let i = 0; i < 3; i++) {
            const linkY = 175 + i * 22;
            ctx.beginPath();
            ctx.ellipse(400, linkY, 8, 14, 0, 0, Math.PI * 2);
            ctx.lineWidth = 4;
            ctx.strokeStyle = silverGrad;
            ctx.stroke();
        }

        // --- 3. Chrome LED Mount Cap ---
        const capY = 240;
        ctx.beginPath();
        ctx.roundRect(365, capY, 70, 30, [8, 8, 2, 2]);
        ctx.fillStyle = silverGrad;
        ctx.fill();
        ctx.strokeStyle = '#475569';
        ctx.lineWidth = 1.5;
        ctx.stroke();
        ctx.restore();

        // --- 4. Optical K9 Crystal Body ---
        const cx = 250;
        const cy = 270;
        const cw = 300;
        const ch = 380;
        const chamfer = 24;

        ctx.save();
        // Crystal cast inner illumination
        const isBlueLED = this.productColor.toLowerCase().includes('38bdf8');
        const glowColor = isBlueLED ? 'rgba(56, 189, 248, 0.45)' : 'rgba(255, 255, 255, 0.35)';

        // Crystal body outline
        ctx.beginPath();
        ctx.roundRect(cx, cy, cw, ch, chamfer);
        ctx.fillStyle = isBlueLED ? 'rgba(224, 242, 254, 0.3)' : 'rgba(255, 255, 255, 0.35)';
        ctx.fill();

        // 3D Beveled faceted corners
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.85)';
        ctx.lineWidth = 3;
        ctx.stroke();

        // Inner refraction border
        ctx.beginPath();
        ctx.roundRect(cx + 14, cy + 14, cw - 28, ch - 28, chamfer - 8);
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.4)';
        ctx.lineWidth = 1.5;
        ctx.stroke();

        // Diagonal glass reflection streak
        ctx.beginPath();
        ctx.moveTo(cx + 20, cy + 10);
        ctx.lineTo(cx + cw * 0.5, cy + 10);
        ctx.lineTo(cx + 10, cy + ch * 0.7);
        ctx.closePath();
        const streakGrad = ctx.createLinearGradient(cx, cy, cx + cw * 0.4, cy + ch * 0.5);
        streakGrad.addColorStop(0, 'rgba(255,255,255,0.4)');
        streakGrad.addColorStop(1, 'rgba(255,255,255,0.0)');
        ctx.fillStyle = streakGrad;
        ctx.fill();

        // Subsurface 3D Laser Etching (Photo & Text)
        ctx.save();
        ctx.shadowColor = glowColor;
        ctx.shadowBlur = 12;

        const photoZone = this.product.printArea.photo;
        this.renderPhoto(photoZone.x, photoZone.y, photoZone.w, photoZone.h, photoZone.shape, photoZone.rx);

        const pa = this.product.printArea;
        this.renderTypography(pa.name.x, pa.name.y, pa.message.y, pa.date.y, false);
        ctx.restore();

        // Glass edge highlight
        ctx.strokeStyle = 'rgba(255,255,255,0.9)';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(cx + chamfer, cy);
        ctx.lineTo(cx + cw - chamfer, cy);
        ctx.stroke();

        ctx.restore();
    }

    /**
     * Photo Rendering Engine with Zoom, Pan, Rotation, Crop Shapes, and Filters
     */
    renderPhoto(x, y, w, h, shape = 'rounded', rx = 16) {
        const { ctx } = this;
        const centerX = x + w / 2;
        const centerY = y + h / 2;

        ctx.save();

        // 1. Establish Clipping Region
        ctx.beginPath();
        if (shape === 'circle') {
            const radius = Math.min(w, h) / 2;
            ctx.arc(centerX, centerY, radius, 0, Math.PI * 2);
        } else if (shape === 'rounded') {
            ctx.roundRect(x, y, w, h, rx);
        } else {
            ctx.rect(x, y, w, h);
        }
        ctx.clip();

        // If no photo uploaded yet, show attractive placeholder
        if (!this.photoImg) {
            // Neutral backdrop
            ctx.fillStyle = '#F1F5F9';
            ctx.fillRect(x, y, w, h);

            // Dotted border
            ctx.strokeStyle = '#94A3B8';
            ctx.lineWidth = 2.5;
            ctx.setLineDash([8, 8]);
            ctx.stroke();
            ctx.setLineDash([]);

            // Icon + instructions
            ctx.fillStyle = '#64748B';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.font = 'bold 36px "Segoe UI", sans-serif';
            ctx.fillText('📷', centerX, centerY - 28);
            ctx.font = 'bold 16px "Segoe UI", sans-serif';
            ctx.fillText('Click "Upload Photo" or Drag & Drop', centerX, centerY + 18);
            ctx.font = '13px "Segoe UI", sans-serif';
            ctx.fillStyle = '#94A3B8';
            ctx.fillText('JPG, PNG, WebP supported', centerX, centerY + 42);

            ctx.restore();
            return;
        }

        // Apply Image Filters
        switch (this.photoTransform.filter) {
            case 'grayscale':
                ctx.filter = 'grayscale(100%) contrast(110%)';
                break;
            case 'sepia':
                ctx.filter = 'sepia(80%) saturate(120%)';
                break;
            case 'warm':
                ctx.filter = 'sepia(30%) saturate(140%) brightness(105%)';
                break;
            case 'vintage':
                ctx.filter = 'sepia(50%) contrast(120%) brightness(95%)';
                break;
            default:
                ctx.filter = 'none';
        }

        // Apply transforms: Move origin to center of target slot, rotate, scale, pan
        ctx.translate(centerX + this.photoTransform.panX, centerY + this.photoTransform.panY);
        ctx.rotate((this.photoTransform.rotation * Math.PI) / 180);

        // Aspect fit / cover calculation
        const img = this.photoImg;
        const imgAspect = img.width / img.height;
        const targetAspect = w / h;
        let drawW, drawH;

        if (imgAspect > targetAspect) {
            drawH = h * this.photoTransform.scale;
            drawW = drawH * imgAspect;
        } else {
            drawW = w * this.photoTransform.scale;
            drawH = drawW / imgAspect;
        }

        ctx.drawImage(img, -drawW / 2, -drawH / 2, drawW, drawH);

        ctx.restore(); // Restore filter and transform

        // Border outline around photo slot
        ctx.save();
        ctx.beginPath();
        if (shape === 'circle') {
            ctx.arc(centerX, centerY, Math.min(w, h) / 2, 0, Math.PI * 2);
        } else if (shape === 'rounded') {
            ctx.roundRect(x, y, w, h, rx);
        } else {
            ctx.rect(x, y, w, h);
        }
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.4)';
        ctx.lineWidth = 3;
        ctx.stroke();
        ctx.restore();
    }

    /**
     * Typography Renderer for Recipient Name, Custom Message, and Date/Subtitle
     */
    renderTypography(x, nameY, msgY, dateY, isEngraved = false, overrideColor = null) {
        const { ctx, text } = this;
        ctx.save();
        ctx.textAlign = text.align || 'center';
        ctx.textBaseline = 'middle';

        const textColor = overrideColor || text.color;

        // 1. Recipient Name / Main Header
        if (text.name && text.name.trim().length > 0) {
            const fontSize = Math.round(28 * text.sizeScale);
            ctx.font = `bold ${fontSize}px ${text.font}`;
            ctx.fillStyle = textColor;

            if (isEngraved) {
                // Subtle carved inner shadow
                ctx.shadowColor = 'rgba(255,255,255,0.7)';
                ctx.shadowOffsetY = 1;
            }

            ctx.fillText(text.name.trim(), x, nameY);
        }

        // 2. Custom Message / Quote
        if (text.message && text.message.trim().length > 0) {
            const fontSize = Math.round(18 * text.sizeScale);
            ctx.font = `italic 500 ${fontSize}px ${text.font}`;
            ctx.fillStyle = textColor;
            ctx.fillText(text.message.trim(), x, msgY);
        }

        // 3. Date / Subtitle
        if (text.date && text.date.trim().length > 0) {
            const fontSize = Math.round(14 * text.sizeScale);
            ctx.font = `600 ${fontSize}px "Segoe UI", sans-serif`;
            ctx.fillStyle = isEngraved ? '#78716C' : (overrideColor || '#64748B');
            ctx.fillText(text.date.trim(), x, dateY);
        }

        ctx.restore();
    }

    /**
     * Export Snapshot Thumbnail (used for cart & order payload)
     */
    exportThumbnail(thumbW = 200, thumbH = 200) {
        const offCanvas = document.createElement('canvas');
        offCanvas.width = thumbW;
        offCanvas.height = thumbH;
        const offCtx = offCanvas.getContext('2d');
        offCtx.drawImage(this.canvas, 0, 0, thumbW, thumbH);
        return offCanvas.toDataURL('image/png', 0.9);
    }

    /**
     * Export High-Res Preview for Download
     */
    exportHighResDataURL() {
        return this.canvas.toDataURL('image/png', 1.0);
    }

    /**
     * Get snapshot configuration data object
     */
    getCustomizationPayload() {
        return {
            productId: this.product.id,
            productName: this.product.name,
            productColor: this.productColor,
            price: this.product.price,
            photoDataUrl: this.photoDataUrl,
            photoTransform: { ...this.photoTransform },
            text: { ...this.text },
            timestamp: new Date().toISOString()
        };
    }

    /**
     * Restore from a saved customization payload
     */
    loadCustomizationPayload(payload) {
        if (!payload) return;
        this.setProduct(payload.productId);
        this.productColor = payload.productColor;
        this.text = { ...payload.text };
        if (payload.photoTransform) {
            this.photoTransform = { ...payload.photoTransform };
        }
        if (payload.photoDataUrl) {
            this.loadPhoto(payload.photoDataUrl, () => {
                this.render();
            });
        } else {
            this.clearPhoto();
        }
    }
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = { GiftCustomizer };
}
