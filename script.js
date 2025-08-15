class BrushRevealEffect {
    constructor() {
        this.canvas = null;
        this.ctx = null;
        this.baseImage = null;
        this.imageContainer = null;
        this.instructionOverlay = null;
        
        this.isDrawing = false;
        this.hasInteracted = false;
        this.pixelRatio = window.devicePixelRatio || 1;
        
        // Brush settings
        this.brushSize = 50;
        this.brushSoftness = 0.7;
        this.lastPoint = { x: 0, y: 0 };
        
        // Animation settings
        this.animationId = null;
        this.points = [];
        this.maxPoints = 10;
        
        this.init();
    }
    
    init() {
        this.setupElements();
        this.setupCanvas();
        this.setupEventListeners();
        this.createWhiteOverlay();
        
        // Position cursor over face on desktop load
        if (window.innerWidth > 768) {
            this.positionInitialCursor();
        }
    }
    
    setupElements() {
        this.canvas = document.getElementById('brushCanvas');
        this.baseImage = document.getElementById('baseImage');
        this.imageContainer = document.getElementById('imageContainer');
        this.instructionOverlay = document.getElementById('instructionOverlay');
        this.ctx = this.canvas.getContext('2d');
    }
    
    setupCanvas() {
        const updateCanvasSize = () => {
            const rect = this.baseImage.getBoundingClientRect();
            
            // Set canvas display size
            this.canvas.style.width = rect.width + 'px';
            this.canvas.style.height = rect.height + 'px';
            
            // Set canvas actual size (for high DPI)
            this.canvas.width = rect.width * this.pixelRatio;
            this.canvas.height = rect.height * this.pixelRatio;
            
            // Scale context for high DPI
            this.ctx.scale(this.pixelRatio, this.pixelRatio);
            
            // Recreate white overlay after resize
            this.createWhiteOverlay();
        };
        
        // Wait for image to load
        if (this.baseImage.complete) {
            updateCanvasSize();
            this.imageContainer.classList.add('loaded');
        } else {
            this.baseImage.addEventListener('load', () => {
                updateCanvasSize();
                this.imageContainer.classList.add('loaded');
            });
        }
        
        // Handle window resize
        window.addEventListener('resize', updateCanvasSize);
    }
    
    createWhiteOverlay() {
        if (!this.canvas.width || !this.canvas.height) return;
        
        // Fill canvas with white overlay
        this.ctx.fillStyle = '#ffffff';
        this.ctx.fillRect(0, 0, this.canvas.width / this.pixelRatio, this.canvas.height / this.pixelRatio);
        
        // Set up blending mode for brush effect
        this.ctx.globalCompositeOperation = 'destination-out';
    }
    
    setupEventListeners() {
        // Mouse events (desktop)
        this.canvas.addEventListener('mousedown', this.handleStart.bind(this));
        this.canvas.addEventListener('mousemove', this.handleMove.bind(this));
        this.canvas.addEventListener('mouseup', this.handleEnd.bind(this));
        this.canvas.addEventListener('mouseleave', this.handleEnd.bind(this));
        
        // Touch events (mobile)
        this.canvas.addEventListener('touchstart', this.handleStart.bind(this));
        this.canvas.addEventListener('touchmove', this.handleMove.bind(this));
        this.canvas.addEventListener('touchend', this.handleEnd.bind(this));
        this.canvas.addEventListener('touchcancel', this.handleEnd.bind(this));
        
        // Prevent default behaviors
        this.canvas.addEventListener('touchstart', e => e.preventDefault());
        this.canvas.addEventListener('touchmove', e => e.preventDefault());
    }
    
    getEventPos(e) {
        const rect = this.canvas.getBoundingClientRect();
        let x, y;
        
        if (e.touches && e.touches.length > 0) {
            // Touch event
            x = e.touches[0].clientX - rect.left;
            y = e.touches[0].clientY - rect.top;
        } else {
            // Mouse event
            x = e.clientX - rect.left;
            y = e.clientY - rect.top;
        }
        
        return { x, y };
    }
    
    handleStart(e) {
        e.preventDefault();
        
        if (!this.hasInteracted) {
            this.hideInstructions();
            this.hasInteracted = true;
        }
        
        this.isDrawing = true;
        const pos = this.getEventPos(e);
        this.lastPoint = pos;
        this.points = [pos];
        
        // Draw initial point
        this.drawBrush(pos.x, pos.y, 0);
    }
    
    handleMove(e) {
        e.preventDefault();
        
        if (!this.isDrawing) return;
        
        const pos = this.getEventPos(e);
        this.points.push(pos);
        
        // Keep only recent points for smooth interpolation
        if (this.points.length > this.maxPoints) {
            this.points.shift();
        }
        
        // Calculate velocity for brush size variation
        const velocity = this.calculateVelocity(this.lastPoint, pos);
        
        // Draw smooth line between points
        this.drawSmoothLine(this.lastPoint, pos, velocity);
        
        this.lastPoint = pos;
    }
    
    handleEnd(e) {
        e.preventDefault();
        this.isDrawing = false;
        this.points = [];
    }
    
    calculateVelocity(point1, point2) {
        const dx = point2.x - point1.x;
        const dy = point2.y - point1.y;
        const distance = Math.sqrt(dx * dx + dy * dy);
        return Math.min(distance * 0.05, 1); // Normalize velocity
    }
    
    drawSmoothLine(from, to, velocity) {
        const steps = Math.max(Math.abs(to.x - from.x), Math.abs(to.y - from.y));
        
        for (let i = 0; i <= steps; i++) {
            const t = i / steps;
            const x = from.x + (to.x - from.x) * t;
            const y = from.y + (to.y - from.y) * t;
            
            this.drawBrush(x, y, velocity);
        }
    }
    
    drawBrush(x, y, velocity) {
        // Dynamic brush size based on velocity and base size
        const size = this.brushSize * (0.5 + velocity * 0.5);
        
        // Create gradient for soft brush effect
        const gradient = this.ctx.createRadialGradient(x, y, 0, x, y, size);
        gradient.addColorStop(0, `rgba(0, 0, 0, ${this.brushSoftness})`);
        gradient.addColorStop(0.7, `rgba(0, 0, 0, ${this.brushSoftness * 0.5})`);
        gradient.addColorStop(1, 'rgba(0, 0, 0, 0)');
        
        this.ctx.fillStyle = gradient;
        
        // Draw brush stroke
        this.ctx.beginPath();
        this.ctx.arc(x, y, size, 0, Math.PI * 2);
        this.ctx.fill();
    }
    
    hideInstructions() {
        this.instructionOverlay.classList.add('hidden');
    }
    
    positionInitialCursor() {
        // Position cursor over the woman's face area (approximately center-upper area)
        setTimeout(() => {
            const rect = this.canvas.getBoundingClientRect();
            const centerX = rect.left + rect.width * 0.5;
            const centerY = rect.top + rect.height * 0.35; // Slightly above center for face area
            
            // Create a fake mouse event to position cursor
            const fakeEvent = new MouseEvent('mousemove', {
                clientX: centerX,
                clientY: centerY,
                bubbles: true
            });
            
            document.dispatchEvent(fakeEvent);
        }, 100);
    }
    
    // Method to adjust brush size based on screen size
    adjustBrushSize() {
        const baseSize = window.innerWidth > 768 ? 50 : 30;
        this.brushSize = baseSize * (this.canvas.width / 800); // Scale with canvas size
    }
}

// Initialize the effect when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new BrushRevealEffect();
});

// Handle orientation change on mobile
window.addEventListener('orientationchange', () => {
    setTimeout(() => {
        location.reload();
    }, 100);
});