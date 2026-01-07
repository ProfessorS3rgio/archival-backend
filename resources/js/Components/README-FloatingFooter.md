# FloatingFooter Component

A reusable Vue 3 component for displaying a floating developer footer that stays fixed at the bottom of the page during scrolling.

## Features

- **Fixed positioning**: Stays at the bottom of the viewport during scroll
- **Customizable**: Multiple props for styling and content customization
- **Social media support**: Built-in icons for Facebook, Twitter, LinkedIn, and GitHub
- **Responsive**: Adapts to different screen sizes
- **Animated**: Smooth slide-up entrance animation
- **Accessible**: Respects `prefers-reduced-motion` setting

## Usage

### Basic Usage

```vue
<template>
  <div>
    <!-- Your page content -->
    <FloatingFooter />
  </div>
</template>

<script setup>
import FloatingFooter from '@/Components/FloatingFooter.vue'
</script>
```

### Advanced Usage with Custom Props

```vue
<template>
  <div>
    <!-- Your page content -->
    <FloatingFooter
      developer-name="John Doe"
      social-platform="github"
      social-url="https://github.com/johndoe"
      label-text="Created by:"
      :show-additional-content="true"
      additional-text="© 2025 My Company"
      background-class="bg-gray-900/90 backdrop-blur-md border-t border-gray-700/60"
      text-class="text-gray-300"
      link-class="text-green-400 hover:text-green-300"
    />
  </div>
</template>

<script setup>
import FloatingFooter from '@/Components/FloatingFooter.vue'
</script>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `developerName` | String | `'Billy Cabales Mamaril'` | The name of the developer |
| `socialUrl` | String | `'https://www.facebook.com/billy.mamaril.334'` | URL to the social media profile |
| `socialPlatform` | String | `'facebook'` | Social platform (`'facebook'`, `'twitter'`, `'linkedin'`, `'github'`) |
| `labelText` | String | `'Developed by:'` | Text displayed before the developer name |
| `additionalText` | String | `''` | Additional text displayed below the main content |
| `showAdditionalContent` | Boolean | `false` | Whether to show the additional text |
| `backgroundClass` | String | `'bg-slate-900/95 backdrop-blur-sm border-t border-slate-700/50'` | CSS classes for the background |
| `textClass` | String | `'text-slate-400'` | CSS classes for the text |
| `linkClass` | String | `'text-blue-400 hover:text-blue-300'` | CSS classes for the link |
| `iconClass` | String | `'w-4 h-4 group-hover:scale-110 transition-transform duration-200'` | CSS classes for the social icon |
| `show` | Boolean | `true` | Whether to show the footer |

## Styling

The component uses Tailwind CSS classes and includes custom animations. You can customize the appearance by passing different class strings to the props.

### Default Styling

- Background: Semi-transparent dark slate with blur effect
- Text: Light slate color
- Links: Blue with hover effects
- Icons: Scale and rotate on hover

### Custom Styling Example

```vue
<FloatingFooter
  background-class="bg-black/80 backdrop-blur-lg border-t-2 border-yellow-500/30"
  text-class="text-yellow-200"
  link-class="text-yellow-400 hover:text-yellow-300"
  icon-class="w-5 h-5 group-hover:scale-125 group-hover:rotate-12"
/>
```

## Accessibility

- Respects `prefers-reduced-motion` for users who prefer reduced animations
- Proper semantic HTML structure
- Keyboard accessible links
- Screen reader friendly

## Browser Support

- Modern browsers with CSS Grid and Flexbox support
- CSS `backdrop-filter` support (with fallback)
- ES6+ JavaScript features

## Dependencies

- Vue 3
- Tailwind CSS (for styling)
- No external dependencies required

## Integration Examples

### Dashboard Page Integration

The component has been integrated into the Dashboard page:

```vue
<!-- In Dashboard.vue -->
<template>
  <AdminLayout title="Dashboard">
    <!-- Dashboard content -->
    <FloatingFooter />
  </AdminLayout>
</template>

<script setup>
import FloatingFooter from '@/Components/FloatingFooter.vue'
// ... other imports
</script>
```

### Login Page Integration

The component has been integrated into the Login page:

```vue
<!-- In Login.vue -->
<template>
  <!-- Login form content -->
  <FloatingFooter />
</template>

<script setup>
import FloatingFooter from '@/Components/FloatingFooter.vue'
// ... other imports
</script>
```

## Examples

### Different Social Platforms

```vue
<!-- Facebook (default) -->
<FloatingFooter social-platform="facebook" />

<!-- Twitter -->
<FloatingFooter
  social-platform="twitter"
  social-url="https://twitter.com/username"
  developer-name="John Doe"
/>

<!-- GitHub -->
<FloatingFooter
  social-platform="github"
  social-url="https://github.com/username"
  developer-name="Jane Smith"
/>

<!-- LinkedIn -->
<FloatingFooter
  social-platform="linkedin"
  social-url="https://linkedin.com/in/username"
  developer-name="Bob Johnson"
/>
```

### Custom Styling for Different Themes

```vue
<!-- Dark theme -->
<FloatingFooter
  background-class="bg-gray-900/95 backdrop-blur-sm border-t border-gray-700/50"
  text-class="text-gray-300"
  link-class="text-blue-400 hover:text-blue-300"
/>

<!-- Light theme -->
<FloatingFooter
  background-class="bg-white/95 backdrop-blur-sm border-t border-gray-200/50"
  text-class="text-gray-600"
  link-class="text-blue-600 hover:text-blue-700"
/>

<!-- Custom branded -->
<FloatingFooter
  background-class="bg-gradient-to-r from-purple-900/95 to-blue-900/95 backdrop-blur-sm border-t border-purple-500/30"
  text-class="text-purple-200"
  link-class="text-purple-300 hover:text-purple-100"
  label-text="Built by:"
  additional-text="© 2025 Tech Corp"
  :show-additional-content="true"
/>
```