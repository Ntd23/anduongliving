# Teams Component Design Rules

## Overview
Modern, elegant team showcase component with glass morphism effects, smooth animations, and responsive grid layout.

## Visual Hierarchy
1. **Header Section**
   - Title with serif typography (Cormorant Garamond)
   - Subtitle and description with clear spacing
   - Decorative elements (line + dot) for visual interest

2. **Grid Layout**
   - Responsive columns: 1 (mobile) → 2 (tablet) → 3 (desktop) → 4 (large)
   - Consistent card spacing: 1.5rem gaps
   - Progressive animation delays for staggered entrance

3. **Card Design**
   - Glass morphism effect with backdrop blur
   - Image aspect ratio: 4/5 (portrait)
   - Hover state with elevation and shine effect

## Color Palette
- **Primary Dark**: `#1a120c` (card backgrounds, main text)
- **Warm Accent**: `#e3c8a8` (highlights, secondary text)
- **Primary Text**: `#fff7ef` (names, headings)
- **Secondary Text**: `rgba(255, 249, 240, 0.9)` (descriptions)
- **Glass Background**: `rgba(32, 22, 16, 0.4)` (glass morphism)
- **Social Links**: `rgba(108, 116, 79, 0.6)` (background)

## Typography System
- **Headers**: Cormorant Garamond serif, light weight (300)
- **Body**: System sans-serif, medium weight (500)
- **Sizes**: 
  - Title: `clamp(2.5rem, 5vw, 4rem)`
  - Name: `clamp(1.2rem, 2.5vw, 1.5rem)`
  - Title: `clamp(0.85rem, 1.5vw, 0.95rem)`
  - Description: `clamp(0.95rem, 1.5vw, 1.1rem)`

## Spacing System
- **Base Unit**: 0.5rem (8px)
- **Card Gaps**: 1.5rem (24px)
- **Container Padding**: 1rem (16px)
- **Glass Padding**: 1.2rem 1.5rem
- **Section Padding**: `clamp(5rem, 10vw, 8rem)`

## Animation Principles
1. **Staggered Entrance**
   - Header: 100ms → 500ms delays
   - Grid container: 600ms delay
   - Cards: 700ms + (index × 100ms) delays

2. **Card Animations**
   - Glass fade-in: 0.6s ease-out
   - Name: +100ms delay
   - Title: +200ms delay
   - Socials: +300ms delay

3. **Hover Effects**
   - Card elevation: `translateY(-8px)`
   - Image scale: `scale(1.05)`
   - Shine effect: diagonal sweep
   - Social links: `translateY(-2px) scale(1.05)`

## Responsive Breakpoints
- **Mobile**: `< 640px` (1 column)
- **Tablet**: `640px - 1023px` (2 columns)
- **Desktop**: `≥ 1024px` (3 columns)
- **Large**: `≥ 1280px` (4 columns)

## Component Structure
```
Teams Component
├── Header
│   ├── Content (title, subtitle, description)
│   └── Decoration (line + dot)
└── Grid
    └── Cards
        ├── Media (image + overlays)
        └── Body (glass + info + socials)
```

## Glass Morphism Effects
- **Backdrop Filter**: `blur(16px) saturate(180%)`
- **Border**: `1px solid rgba(248, 243, 234, 0.15)`
- **Shadow**: `inset 0 1px 0 rgba(255, 248, 237, 0.1)`
- **Background**: `rgba(32, 22, 16, 0.4)`

## Micro-interactions
1. **Social Links**
   - Hover: background darkening + scale + elevation
   - Smooth cubic-bezier transitions

2. **Name Links**
   - Hover: color change to accent
   - Maintains readability

3. **Card Hover**
   - Coordinated elevation and image effects
   - Shine animation for premium feel

## Performance Considerations
- Lazy loading images with `loading="lazy"`
- Transform-based animations for GPU acceleration
- Efficient backdrop-filter usage
- Minimal repaints/reflows

## Accessibility
- Semantic HTML structure
- ARIA labels for social links
- Keyboard navigation support
- High contrast ratios maintained

## Browser Support
- Modern browsers with backdrop-filter support
- Graceful degradation for older browsers
- CSS custom properties for theming
- Responsive typography with clamp()
