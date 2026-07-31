---
name: Fiscal Precision
colors:
  surface: '#faf9fc'
  surface-dim: '#dad9dd'
  surface-bright: '#faf9fc'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f4f3f7'
  surface-container: '#eeedf1'
  surface-container-high: '#e9e7eb'
  surface-container-highest: '#e3e2e6'
  on-surface: '#1a1c1e'
  on-surface-variant: '#43474e'
  inverse-surface: '#2f3033'
  inverse-on-surface: '#f1f0f4'
  outline: '#74777f'
  outline-variant: '#c4c6cf'
  surface-tint: '#455f87'
  primary: '#022448'
  on-primary: '#ffffff'
  primary-container: '#1e3a5f'
  on-primary-container: '#8aa4cf'
  inverse-primary: '#adc8f5'
  secondary: '#006e2d'
  on-secondary: '#ffffff'
  secondary-container: '#7cf994'
  on-secondary-container: '#007230'
  tertiary: '#341f00'
  on-tertiary: '#ffffff'
  tertiary-container: '#503300'
  on-tertiary-container: '#c69b5f'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d5e3ff'
  primary-fixed-dim: '#adc8f5'
  on-primary-fixed: '#001c3b'
  on-primary-fixed-variant: '#2d486d'
  secondary-fixed: '#7ffc97'
  secondary-fixed-dim: '#62df7d'
  on-secondary-fixed: '#002109'
  on-secondary-fixed-variant: '#005320'
  tertiary-fixed: '#ffddb2'
  tertiary-fixed-dim: '#edbf7f'
  on-tertiary-fixed: '#291800'
  on-tertiary-fixed-variant: '#60410c'
  background: '#faf9fc'
  on-background: '#1a1c1e'
  surface-variant: '#e3e2e6'
typography:
  display-lg:
    fontFamily: Inter
    fontSize: 36px
    fontWeight: '700'
    lineHeight: 44px
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Inter
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 34px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
  title-sm:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 26px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-caps:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
  data-mono:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '500'
    lineHeight: 20px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 4px
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 32px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 40px
---

## Brand & Style
The design system is engineered for high-stakes financial environments where clarity and trust are paramount. The brand personality is authoritative yet accessible, prioritizing cognitive ease over decorative flair. 

The aesthetic follows a **Corporate / Modern** style with a focus on functional minimalism. It utilizes a structured hierarchy, ample whitespace, and a high-contrast palette to ensure that complex data remains legible during rapid consultation. The emotional response should be one of stability, accuracy, and professional rigor.

## Colors
The color palette is strictly functional, mapping specific emotional cues to financial states. 

- **Primary (#1E3A5F):** Reserved for high-level navigation (sidebar) and primary calls to action. It establishes the "anchor" of the application.
- **Success/Secondary (#16A34A):** Utilized for positive growth, income, and completed states.
- **Alert (#DC2626) & Warning (#F59E0B):** Used sparingly for negative cash flow, delays, or pending approvals to maintain urgency without causing alarm fatigue.
- **Neutral Grays:** The background and surface colors create a layered effect that separates global navigation from the active workspace.

## Typography
The design system utilizes **Inter** for its exceptional legibility and extensive font features. For financial data, "Tabular Figures" (`tnum`) must be enabled to ensure that columns of numbers align perfectly for easy scanning.

Headlines use tighter letter-spacing and heavier weights to provide clear section anchors. Labels for table headers or small metadata should use the `label-caps` style to differentiate from interactive body text.

## Layout & Spacing
This design system employs a **Fixed Grid** model for desktop to ensure data-heavy tables do not become unreadably wide. On smaller screens, the layout transitions to a fluid model.

- **Desktop:** 12-column grid with a max-width of 1440px. 24px gutters.
- **Tablet:** 8-column fluid grid. 16px gutters.
- **Mobile:** 4-column fluid grid. 16px margins.

Spacing follows a strict 4px base unit. Generous internal padding (16px - 24px) is required within cards and table cells to prevent visual crowding of numerical data.

## Elevation & Depth
Depth is used to denote interactable surfaces against the structural background.

- **Level 0 (Background):** The Light Gray (#F4F6F8) serves as the canvas.
- **Level 1 (Cards/Surface):** White (#FFFFFF) surfaces with a subtle, 1px border (#E5E7EB).
- **Level 2 (Active/Hover):** A soft ambient shadow (Y: 4px, Blur: 12px, Color: rgba(0,0,0,0.05)) is applied to cards or dropdowns to suggest elevation.

Avoid heavy shadows or dramatic blurs; the goal is a "flat-plus" look that emphasizes the content rather than the container.

## Shapes
The shape language is approachable yet professional. A consistent 8px (`rounded`) radius is the standard for cards, input fields, and primary buttons. 

Use 4px (`rounded-sm`) for smaller utility elements like checkboxes or tags. Avoid fully circular (pill) shapes except for status badges to ensure they are visually distinct from buttons.

## Components

### Buttons
- **Primary:** Dark Blue (#1E3A5F) background with White text. 8px radius.
- **Success:** Green (#16A34A) for "Add Transaction" or "Confirm" actions.
- **Ghost:** No background, #6B7280 text; used for secondary actions like "Cancel."

### Side Navigation
The sidebar should use the Primary Dark Blue (#1E3A5F) as its background. Icons should be simplified line art. Active states are indicated by a subtle light blue highlight or a left-side accent bar in a brighter tint.

### Data Tables
- **Headers:** Light Gray (#F4F6F8) background, `label-caps` typography, 12px vertical padding.
- **Cells:** 16px vertical padding, 1px bottom border (#E5E7EB).
- **Alignment:** Numbers are always right-aligned; text is left-aligned.

### Input Fields
- **Default:** White background, 1px border (#E5E7EB), 8px radius.
- **Focus:** 1px border in Primary Color (#1E3A5F) with a subtle 2px outer glow.

### Cards
All cards must include a subtle 1px border. Financial summaries (KPIs) should use `display-lg` typography for the main amount, ensuring immediate visibility upon page load.