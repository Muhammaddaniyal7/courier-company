# Interactive Brush Reveal Animation

An interactive web animation featuring a woman's portrait where scribble-style hair is initially hidden beneath a solid white overlay. As users move their cursor (desktop) or finger (mobile) over the image, the overlay is erased in real time, revealing the artistic hair beneath with a natural "brushed" effect.

## Features

- **Smooth Brush Reveal Effect**: Natural, responsive brush strokes that follow cursor/touch movement
- **High-Resolution Canvas**: Supports high-DPI displays with proper scaling
- **Mobile & Touch Support**: Optimized for both desktop and mobile interactions
- **Responsive Design**: Adapts to different screen sizes and orientations
- **Instruction Overlay**: Subtle "Move to reveal" prompt that fades after first interaction
- **Velocity-Based Brush Size**: Brush size varies based on movement speed for natural effect
- **Modern UI**: Beautiful gradient background with smooth animations

## Technologies Used

- **HTML5 Canvas 2D**: For real-time brush masking and drawing
- **Vanilla JavaScript**: Clean, performant interaction handling
- **CSS3**: Modern styling with gradients, transitions, and responsive design
- **SVG**: Scalable vector graphics for the demo portrait

## How It Works

1. **Canvas Overlay**: A white canvas overlay covers the portrait image
2. **Brush Masking**: Uses `destination-out` composite operation to "erase" the overlay
3. **Smooth Interpolation**: Interpolates between mouse/touch points for fluid strokes
4. **Velocity Detection**: Calculates movement speed to vary brush size naturally
5. **High-DPI Support**: Automatically scales canvas for crisp rendering on all displays

## File Structure

```
/workspace/
├── index.html          # Main HTML structure
├── styles.css          # CSS styling and responsive design
├── script.js           # JavaScript brush reveal logic
├── woman-portrait.svg  # Demo SVG portrait with scribble hair
└── README.md          # This file
```

## Getting Started

1. **Clone or download** the project files
2. **Start a local server**:
   ```bash
   python3 -m http.server 8000
   # or
   npx http-server
   # or use any other local server
   ```
3. **Open browser** and navigate to `http://localhost:8000`
4. **Interact** by moving your cursor or touching the screen to reveal the hair!

## Customization

### Brush Settings
Modify the brush behavior in `script.js`:
```javascript
this.brushSize = 50;        // Base brush size
this.brushSoftness = 0.7;   // Brush edge softness (0-1)
```

### Visual Styling
Update colors and effects in `styles.css`:
```css
.container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

### Replace the Image
Replace `woman-portrait.svg` with your own high-resolution image featuring:
- Clear subject area (face visible)
- Detailed areas to reveal (hair, texture, patterns)
- Good contrast for effective masking

## Browser Compatibility

- **Modern Browsers**: Chrome 60+, Firefox 55+, Safari 12+, Edge 79+
- **Mobile**: iOS Safari 12+, Chrome Mobile 60+
- **Features Used**: Canvas 2D, Touch Events, CSS Grid, ES6 Classes

## Performance Notes

- **High-DPI Optimization**: Automatically adjusts canvas resolution
- **Smooth Animation**: Uses efficient drawing techniques for 60fps performance
- **Memory Management**: Limits stored points to prevent memory leaks
- **Touch Optimization**: Prevents default behaviors for smooth mobile interaction

## Inspiration

This project draws inspiration from premium web experiences like:
- [Made by Analogue Studio](https://madebyanalogue.co.uk/studio/)
- [Duten Brushed Steel Finish](https://www.duten.com/en/finish/brushed-stainless-steel/)

## License

Open source - feel free to use and modify for your projects!