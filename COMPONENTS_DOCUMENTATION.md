# Reusable Components Documentation

## DeleteConfirmationModal

A reusable modal component for delete confirmations with smooth animations.

### Usage

```vue
<template>
  <div>
    <button @click="showModal = true">Delete Item</button>
    
    <DeleteConfirmationModal
      :show="showModal"
      title="Delete Item"
      message="Are you sure you want to delete this item? This action cannot be undone."
      @confirm="handleDelete"
      @close="showModal = false"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import DeleteConfirmationModal from '@/Components/DeleteConfirmationModal.vue'

const showModal = ref(false)

const handleDelete = () => {
  // Perform delete action
  console.log('Item deleted')
  showModal.value = false
}
</script>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `show` | Boolean | `false` | Controls modal visibility |
| `title` | String | `'Confirm Delete'` | Modal title |
| `message` | String | `'Are you sure...'` | Confirmation message |
| `confirmText` | String | `'Delete'` | Confirm button text |
| `cancelText` | String | `'Cancel'` | Cancel button text |
| `loading` | Boolean | `false` | Disables buttons during async operations |

### Events

- `@confirm` - Emitted when confirm button is clicked
- `@close` - Emitted when cancel button or backdrop is clicked

---

## Alert Component

A reusable alert component for success, error, warning, and info messages.

### Usage

```vue
<template>
  <Alert
    v-if="showAlert"
    :show="true"
    type="success"
    title="Success!"
    message="Your changes have been saved successfully."
    @close="showAlert = false"
  />
</template>

<script setup>
import { ref } from 'vue'
import Alert from '@/Components/Alert.vue'

const showAlert = ref(true)
</script>
```

### With Flash Messages (Laravel)

```vue
<template>
  <Alert
    v-if="$page.props.flash.success"
    :show="true"
    type="success"
    :message="$page.props.flash.success"
    @close="$page.props.flash.success = null"
  />
  
  <Alert
    v-if="$page.props.flash.error"
    :show="true"
    type="error"
    :message="$page.props.flash.error"
    @close="$page.props.flash.error = null"
  />
</template>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `show` | Boolean | `false` | Controls alert visibility |
| `type` | String | `'success'` | Alert type: `success`, `error`, `warning`, `info` |
| `title` | String | `''` | Optional alert title |
| `message` | String | **Required** | Alert message text |
| `dismissible` | Boolean | `true` | Shows close button |

### Events

- `@close` - Emitted when close button is clicked

### Types and Colors

- **success**: Teal/green theme with checkmark icon in circular background
- **error**: Red theme with exclamation icon in circular background
- **warning**: Yellow theme with warning triangle icon in circular background
- **info**: Blue theme with info circle icon in circular background

---

## SimpleAlert Component

A flexible DaisyUI-inspired alert component with multiple style variants.

### Usage

```vue
<template>
  <!-- Solid style (default) -->
  <SimpleAlert
    :show="true"
    color="warning"
    message="Warning: Invalid email address!"
  />

  <!-- Outline style -->
  <SimpleAlert
    :show="true"
    style="outline"
    color="info"
    title="Information"
    message="Your session will expire in 5 minutes."
  />

  <!-- Dash outline style -->
  <SimpleAlert
    :show="true"
    style="dash"
    color="error"
    message="Connection failed. Please try again."
  />

  <!-- Soft style -->
  <SimpleAlert
    :show="true"
    style="soft"
    color="success"
    message="Profile updated successfully!"
  />

  <!-- Vertical layout (mobile friendly) -->
  <SimpleAlert
    :show="true"
    direction="vertical"
    color="warning"
    title="Important Notice"
    message="System maintenance scheduled for tonight."
  />
</template>

<script setup>
import SimpleAlert from '@/Components/SimpleAlert.vue'
</script>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `show` | Boolean | `false` | Controls alert visibility |
| `style` | String | `'solid'` | Style variant: `solid`, `outline`, `dash`, `soft` |
| `color` | String | `'info'` | Color theme: `info`, `success`, `warning`, `error` |
| `direction` | String | `'horizontal'` | Layout: `horizontal` (desktop), `vertical` (mobile) |
| `title` | String | `''` | Optional alert title |
| `message` | String | **Required** | Alert message text |
| `dismissible` | Boolean | `true` | Shows close button |
| `hideIcon` | Boolean | `false` | Hides the icon |

### Events

- `@close` - Emitted when close button is clicked

### Style Variants

- **solid**: Full background color (default)
- **outline**: Transparent background with border
- **dash**: Transparent background with dashed border
- **soft**: Semi-transparent soft background

### Color Themes

- **info**: Blue theme
- **success**: Teal/green theme
- **warning**: Yellow theme
- **error**: Red theme

### Direction Options

- **horizontal**: Icon and text side by side (good for desktop)
- **vertical**: Stacked layout (good for mobile)

---

## Examples in Action

### Delete Confirmation
See: `resources/js/Pages/Settings/SpTerms.vue`
See: `resources/js/Pages/SangguniangPanlalawigan/Officials.vue`

### Alert Messages
See: `resources/js/Pages/Settings/SpTerms.vue`
See: `resources/js/Pages/SangguniangPanlalawigan/Officials.vue`
See: `resources/js/Pages/SangguniangPanlalawigan/AddOfficial.vue`
