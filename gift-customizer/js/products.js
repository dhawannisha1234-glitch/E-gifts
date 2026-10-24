/**
 * Product Catalog & Customization Configuration
 * Defines available gifts, their customizable zones, and realistic render parameters.
 */
const PRODUCTS = [
    {
        id: 'ceramic-mug',
        name: 'Ceramic Keepsake Mug (11oz)',
        category: 'Drinkware',
        price: 18.99,
        badge: 'Bestseller ⭐',
        description: 'Premium ultra-glossy ceramic mug with wraparound photo finish and personalized quote.',
        colors: [
            { name: 'Pure White', hex: '#FFFFFF', dark: false },
            { name: 'Midnight Black', hex: '#1E293B', dark: true },
            { name: 'Soft Blush', hex: '#FCE7F3', dark: false },
            { name: 'Mint Sage', hex: '#E6F4EA', dark: false }
        ],
        canvasWidth: 800,
        canvasHeight: 800,
        mockupType: 'mug',
        printArea: {
            photo: { x: 300, y: 310, w: 230, h: 230, shape: 'rounded', rx: 20 },
            name: { x: 415, y: 565, maxFontSize: 30, weight: '700' },
            message: { x: 415, y: 605, maxFontSize: 20, weight: '400' },
            date: { x: 415, y: 640, maxFontSize: 16, weight: '500' }
        },
        defaultText: {
            name: 'Sophia & Liam',
            message: 'You make every morning brighter ☕',
            date: 'Est. Oct 2024'
        }
    },
    {
        id: 'wooden-frame',
        name: 'Nordic Oak Tabletop Photo Frame',
        category: 'Home Decor',
        price: 29.50,
        badge: 'Handcrafted',
        description: 'Natural oak wood frame with laser-engraved caption plate and archival photo print.',
        colors: [
            { name: 'Natural Oak', hex: '#D7A15C', dark: false },
            { name: 'Dark Walnut', hex: '#5C381E', dark: true },
            { name: 'Ash White', hex: '#F3F4F6', dark: false }
        ],
        canvasWidth: 800,
        canvasHeight: 800,
        mockupType: 'frame',
        printArea: {
            photo: { x: 230, y: 190, w: 340, h: 340, shape: 'square' },
            name: { x: 400, y: 565, maxFontSize: 28, weight: '700' },
            message: { x: 400, y: 605, maxFontSize: 20, weight: '400' },
            date: { x: 400, y: 640, maxFontSize: 16, weight: '500' }
        },
        defaultText: {
            name: 'The Henderson Family',
            message: 'Where life begins and love never ends ❤️',
            date: 'Summer 2024'
        }
    },
    {
        id: 'acrylic-lamp',
        name: 'Glowing Acrylic LED Night Lamp',
        category: 'Lighting',
        price: 34.00,
        badge: 'Trending 🔥',
        description: 'Crystal-clear acrylic plaque with soft warm LED illumination and custom laser etching.',
        colors: [
            { name: 'Warm Amber LED', hex: '#FFE7B3', dark: false },
            { name: 'Pure White LED', hex: '#E0F2FE', dark: false },
            { name: 'Romantic Pink LED', hex: '#FCE7F3', dark: false }
        ],
        canvasWidth: 800,
        canvasHeight: 800,
        mockupType: 'lamp',
        printArea: {
            photo: { x: 260, y: 170, w: 280, h: 280, shape: 'circle' },
            name: { x: 400, y: 495, maxFontSize: 30, weight: '700' },
            message: { x: 400, y: 535, maxFontSize: 19, weight: '400' },
            date: { x: 400, y: 570, maxFontSize: 15, weight: '500' }
        },
        defaultText: {
            name: 'Happy Anniversary',
            message: 'Forever my favorite chapter ✨',
            date: '24th September'
        }
    },
    {
        id: 'velvet-cushion',
        name: 'Custom Velvet Throw Pillow (16x16")',
        category: 'Living',
        price: 24.99,
        badge: 'Cozy Pick',
        description: 'Ultra-soft micro-velvet cushion with vibrant edge-to-edge sublimation print.',
        colors: [
            { name: 'Soft Cream', hex: '#FAF7F2', dark: false },
            { name: 'Pastel Slate', hex: '#CBD5E1', dark: false },
            { name: 'Dusty Rose', hex: '#E2B8B8', dark: false }
        ],
        canvasWidth: 800,
        canvasHeight: 800,
        mockupType: 'cushion',
        printArea: {
            photo: { x: 250, y: 220, w: 300, h: 260, shape: 'rounded', rx: 24 },
            name: { x: 400, y: 520, maxFontSize: 28, weight: '700' },
            message: { x: 400, y: 560, maxFontSize: 20, weight: '400' },
            date: { x: 400, y: 595, maxFontSize: 16, weight: '500' }
        },
        defaultText: {
            name: 'Home Sweet Home',
            message: 'Filled with memories and warm cuddles 🏡',
            date: 'Established 2023'
        }
    },
    {
        id: 'custom-tshirt',
        name: 'Personalized Graphic Cotton T-Shirt',
        category: 'Apparel',
        price: 22.50,
        badge: 'Top Pick 🔥',
        description: '100% premium ring-spun combed cotton t-shirt with vibrant direct-to-garment custom print.',
        colors: [
            { name: 'Jet Black', hex: '#1E293B', dark: true },
            { name: 'Pure White', hex: '#FFFFFF', dark: false },
            { name: 'Navy Blue', hex: '#1E3A8A', dark: true },
            { name: 'Heather Grey', hex: '#CBD5E1', dark: false }
        ],
        canvasWidth: 800,
        canvasHeight: 800,
        mockupType: 'tshirt',
        printArea: {
            photo: { x: 280, y: 250, w: 240, h: 220, shape: 'rounded', rx: 16 },
            name: { x: 400, y: 505, maxFontSize: 28, weight: '700' },
            message: { x: 400, y: 545, maxFontSize: 19, weight: '400' },
            date: { x: 400, y: 580, maxFontSize: 15, weight: '500' }
        },
        defaultText: {
            name: 'Liam & Olivia',
            message: 'Made For Each Other ✨',
            date: 'Together Since 2021'
        }
    },
    {
        id: 'crystal-keychain',
        name: 'Laser 3D Crystal Keychain',
        category: 'Keepsakes',
        price: 14.99,
        badge: 'Pocket Gift',
        description: 'Precision subsurface laser-engraved K9 optical crystal with bright LED accent light.',
        colors: [
            { name: 'Clear Crystal', hex: '#FFFFFF', dark: false },
            { name: 'Blue LED Accent', hex: '#38BDF8', dark: false }
        ],
        canvasWidth: 800,
        canvasHeight: 800,
        mockupType: 'keychain',
        printArea: {
            photo: { x: 290, y: 280, w: 220, h: 220, shape: 'rounded', rx: 14 },
            name: { x: 400, y: 535, maxFontSize: 24, weight: '700' },
            message: { x: 400, y: 570, maxFontSize: 17, weight: '400' },
            date: { x: 400, y: 600, maxFontSize: 14, weight: '500' }
        },
        defaultText: {
            name: 'Best Dad Ever',
            message: 'Always by my side ❤️',
            date: '2024'
        }
    }
];

// Sample presets for users to try with 1 click
const SAMPLE_PHOTOS = [
    {
        id: 'couple',
        title: 'Couple / Romance',
        icon: '💑',
        url: generateSampleImageSVG('couple')
    },
    {
        id: 'pet',
        title: 'Cute Pet',
        icon: '🐾',
        url: generateSampleImageSVG('pet')
    },
    {
        id: 'birthday',
        title: 'Celebration',
        icon: '🎉',
        url: generateSampleImageSVG('birthday')
    },
    {
        id: 'scenery',
        title: 'Sunset / Travel',
        icon: '🌅',
        url: generateSampleImageSVG('scenery')
    }
];

// Generate high quality sample SVG images as Data URLs so no external network dependency is needed
function generateSampleImageSVG(type) {
    let svgContent = '';
    if (type === 'couple') {
        svgContent = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600" width="600" height="600">
            <defs>
                <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#FF9A8B"/>
                    <stop offset="50%" stop-color="#FF6A88"/>
                    <stop offset="100%" stop-color="#FF99AC"/>
                </linearGradient>
                <filter id="softGlow">
                    <feGaussianBlur stdDeviation="8" result="blur"/>
                    <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
                </filter>
            </defs>
            <rect width="600" height="600" fill="url(#bg)"/>
            <circle cx="300" cy="180" r="110" fill="#FFE5D9" opacity="0.4"/>
            <!-- Heart icon stylized -->
            <path d="M300 450 C 200 370 120 280 120 200 C 120 140 165 100 225 100 C 265 100 290 120 300 135 C 310 120 335 100 375 100 C 435 100 480 140 480 200 C 480 280 400 370 300 450 Z" 
                  fill="#FFFFFF" opacity="0.92" filter="url(#softGlow)"/>
            <text x="300" y="275" font-family="'Segoe UI', Roboto, sans-serif" font-weight="bold" font-size="42" fill="#E11D48" text-anchor="middle">You &amp; Me</text>
            <text x="300" y="320" font-family="'Segoe UI', Roboto, sans-serif" font-size="22" fill="#BE123C" text-anchor="middle">Forever &amp; Always</text>
            <circle cx="160" cy="140" r="14" fill="#FFFFFF" opacity="0.6"/>
            <circle cx="440" cy="150" r="10" fill="#FFFFFF" opacity="0.6"/>
            <circle cx="470" cy="380" r="18" fill="#FFFFFF" opacity="0.5"/>
        </svg>`;
    } else if (type === 'pet') {
        svgContent = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600" width="600" height="600">
            <defs>
                <linearGradient id="petBg" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#4E65FF"/>
                    <stop offset="100%" stop-color="#92EFFD"/>
                </linearGradient>
            </defs>
            <rect width="600" height="600" fill="url(#petBg)"/>
            <circle cx="300" cy="270" r="170" fill="#FFF9ED"/>
            <!-- Dog/Cat Ear Left -->
            <polygon points="170,190 220,110 250,210" fill="#D97706"/>
            <!-- Dog/Cat Ear Right -->
            <polygon points="430,190 380,110 350,210" fill="#D97706"/>
            <!-- Eyes -->
            <ellipse cx="245" cy="250" rx="14" ry="18" fill="#1F2937"/>
            <circle cx="249" cy="245" r="5" fill="#FFFFFF"/>
            <ellipse cx="355" cy="250" rx="14" ry="18" fill="#1F2937"/>
            <circle cx="359" cy="245" r="5" fill="#FFFFFF"/>
            <!-- Nose & Tongue -->
            <polygon points="300,285 285,270 315,270" fill="#1F2937"/>
            <path d="M 285 295 Q 300 310 315 295" stroke="#1F2937" stroke-width="4" fill="none"/>
            <path d="M 294 303 Q 300 325 306 303" fill="#FB7185"/>
            <!-- Cute Paws -->
            <circle cx="220" cy="440" r="26" fill="#FBBF24" opacity="0.8"/>
            <circle cx="380" cy="440" r="26" fill="#FBBF24" opacity="0.8"/>
            <text x="300" y="520" font-family="'Segoe UI', Roboto, sans-serif" font-weight="bold" font-size="34" fill="#FFFFFF" text-anchor="middle">Best Furry Friend 🐾</text>
        </svg>`;
    } else if (type === 'birthday') {
        svgContent = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600" width="600" height="600">
            <defs>
                <linearGradient id="bdayBg" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#F59E0B"/>
                    <stop offset="50%" stop-color="#EC4899"/>
                    <stop offset="100%" stop-color="#8B5CF6"/>
                </linearGradient>
            </defs>
            <rect width="600" height="600" fill="url(#bdayBg)"/>
            <!-- Birthday Cake -->
            <rect x="200" y="310" width="200" height="110" rx="18" fill="#FFFBEB"/>
            <rect x="225" y="240" width="150" height="75" rx="14" fill="#FDE68A"/>
            <!-- Candle -->
            <rect x="294" y="180" width="12" height="60" fill="#60A5FA" rx="4"/>
            <!-- Flame -->
            <path d="M300 155 Q 312 170 300 180 Q 288 170 300 155 Z" fill="#F59E0B"/>
            <circle cx="300" cy="172" r="4" fill="#FEF08A"/>
            <!-- Text banner -->
            <text x="300" y="480" font-family="'Segoe UI', Roboto, sans-serif" font-weight="800" font-size="38" fill="#FFFFFF" text-anchor="middle">HAPPY BIRTHDAY!</text>
            <text x="300" y="525" font-family="'Segoe UI', Roboto, sans-serif" font-size="20" fill="#FDF4FF" text-anchor="middle">Celebrate the Good Times 🎈</text>
        </svg>`;
    } else {
        svgContent = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600" width="600" height="600">
            <defs>
                <linearGradient id="sunBg" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#1E1B4B"/>
                    <stop offset="40%" stop-color="#7C2D12"/>
                    <stop offset="70%" stop-color="#EA580C"/>
                    <stop offset="100%" stop-color="#FDE047"/>
                </linearGradient>
            </defs>
            <rect width="600" height="600" fill="url(#sunBg)"/>
            <circle cx="300" cy="330" r="85" fill="#FEF08A" opacity="0.9"/>
            <path d="M0 430 Q 150 380 300 420 T 600 400 L 600 600 L 0 600 Z" fill="#0F172A" opacity="0.95"/>
            <path d="M0 480 Q 200 440 400 490 T 600 470 L 600 600 L 0 600 Z" fill="#020617"/>
            <text x="300" y="555" font-family="'Segoe UI', Roboto, sans-serif" font-weight="700" font-size="30" fill="#FED7AA" text-anchor="middle">Golden Horizons 🌅</text>
        </svg>`;
    }
    return 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svgContent.trim());
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = { PRODUCTS, SAMPLE_PHOTOS, generateSampleImageSVG };
}
