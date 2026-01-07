# Assets Directory

This directory contains static assets for the Archival Management System.

## Directory Structure

```
assets/
├── images/          # General images (banners, backgrounds, etc.)
├── logos/           # Logo files (main logo, favicon, etc.)
└── README.md        # This file
```

## Usage Guidelines

### Logos (`/logos/`)
Place your logo files here:
- **logo.png** or **logo.svg** - Main application logo (recommended size: 512x512px)
- **logo-light.png** - Logo for dark backgrounds
- **logo-dark.png** - Logo for light backgrounds
- **favicon.ico** - Browser favicon (16x16, 32x32, 48x48)

### Images (`/images/`)
Place banner and background images here:
- **login-banner.jpg** or **login-banner.png** - Login page banner
- **background-pattern.svg** - Background patterns
- **header-banner.jpg** - Header banners

## File Naming Conventions

- Use lowercase letters
- Separate words with hyphens (kebab-case)
- Be descriptive: `login-background.jpg` instead of `bg.jpg`

## Recommended Image Formats

- **Logos**: SVG (scalable) or PNG (with transparency)
- **Photos/Banners**: JPG (optimized for web)
- **Icons**: SVG or PNG
- **Backgrounds**: SVG (patterns) or optimized JPG/PNG

## Optimization Tips

1. Compress images before uploading
2. Use appropriate dimensions (don't upload oversized images)
3. Consider using WebP format for better compression
4. Use SVG for logos and icons when possible

## Accessing Assets in Code

```vue
<!-- In Vue components -->
<img src="/assets/logos/logo.png" alt="Logo">
<img src="/assets/images/login-banner.jpg" alt="Banner">
```

```css
/* In CSS */
background-image: url('/assets/images/background-pattern.svg');
```
