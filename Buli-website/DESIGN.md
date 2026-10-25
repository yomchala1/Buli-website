# DESIGN.md — Impeccable Design System Specification

**Institution**: Major General Mulu Geta Buli Polytechnic College (MGMBPTC)  
**Specification Version**: v2.0  
**Design System Lane**: Academic Modernism & Polytechnic Engineering  

---

## 1. Design Tokens & Color Architecture

### 1.1 Core Institutional Palette

| Token Name | Hex Code | HSL Value | OKLCH Value | Role & Usage |
| :--- | :--- | :--- | :--- | :--- |
| `--green-dark` | `#1a3d1f` | `hsl(128, 40%, 17%)` | `oklch(0.28 0.07 142)` | Main Header BG, Footer BG, High-contrast text |
| `--green` | `#1e4d24` | `hsl(128, 43%, 21%)` | `oklch(0.33 0.09 142)` | Primary Brand Accent, Primary Buttons |
| `--green-mid` | `#2e6b36` | `hsl(128, 40%, 30%)` | `oklch(0.44 0.10 142)` | Interactive Links, Secondary Highlights |
| `--green-pale` | `#eaf4eb` | `hsl(125, 33%, 94%)` | `oklch(0.95 0.02 142)` | Light Card Fill, Success Alert BG |
| `--gold` | `#c9a227` | `hsl(45, 67%, 47%)` | `oklch(0.70 0.16 88)` | Primary Accent Border, Crest Details |
| `--gold-bright` | `#e6b830` | `hsl(45, 78%, 54%)` | `oklch(0.78 0.17 88)` | Active Nav Indicator, Hero Badge, Button Hover Glow |
| `--gold-pale` | `#fdf6e0` | `hsl(45, 88%, 93%)` | `oklch(0.96 0.03 88)` | Notice Board Highlight, Warning Alert BG |
| `--blue` | `#1a4480` | `hsl(215, 66%, 30%)` | `oklch(0.36 0.12 255)` | IT & Engineering Section Accent |
| `--teal` | `#0d7377` | `hsl(182, 80%, 26%)` | `oklch(0.48 0.11 190)` | Technology Transfer Accent |

### 1.2 Neutral Palette & Surface System

| Token Name | Hex Code | Usage |
| :--- | :--- | :--- |
| `--gray-900` | `#111827` | Primary Heading Text (`h1`, `h2`, `h3`) |
| `--gray-800` | `#1f2937` | Body Text (`p`, `li`, `td`) |
| `--gray-600` | `#4b5563` | Subtitles, Form Labels, Meta Info |
| `--gray-300` | `#d1d5db` | Subdued Borders, Table Separators |
| `--gray-100` | `#f3f4f6` | Input Backgrounds, Disabled States |
| `--bg-light` | `#e8eee8` | Global Page Canvas Background |
| `--bg-white` | `#ffffff` | Primary Card & Container Fill |
| `--border-color` | `#b0c2b0` | Standard Container & Grid Border |

---

## 2. Typography System

### 2.1 Font Families
* **Display / Headings**: `"Plus Jakarta Sans"`, `"Inter"`, system-ui, sans-serif
* **Body / UI Elements**: `"Inter"`, system-ui, -apple-system, sans-serif
* **Technical / Code Data**: `"Fira Code"`, monospace

### 2.2 Fluid Type Scale (`clamp()`)

```css
h1 { font-size: clamp(1.85rem, 4vw, 2.85rem); line-height: 1.2; font-weight: 800; letter-spacing: -0.02em; }
h2 { font-size: clamp(1.45rem, 3vw, 2.10rem); line-height: 1.25; font-weight: 700; letter-spacing: -0.01em; }
h3 { font-size: clamp(1.20rem, 2vw, 1.45rem); line-height: 1.3; font-weight: 600; }
h4 { font-size: 1.05rem; line-height: 1.4; font-weight: 600; }
p  { font-size: 1rem; line-height: 1.65; margin-bottom: 0.85rem; }
small, .caption { font-size: 0.85rem; line-height: 1.4; }
```

---

## 3. Spacing Grid & Layout Tokens

### 3.1 4px / 8px Spacing Scale
* `space-1`: `0.25rem` (4px)
* `space-2`: `0.5rem` (8px)
* `space-3`: `0.75rem` (12px)
* `space-4`: `1.0rem` (16px)
* `space-6`: `1.5rem` (24px)
* `space-8`: `2.0rem` (32px)
* `space-12`: `3.0rem` (48px)
* `space-16`: `4.0rem` (64px)

### 3.2 Containers & Grid Breakpoints
* **Max Width**: `--max-width: 1200px`
* **Mobile Breakpoint**: `max-width: 768px` (Stacks single column, enables mobile hamburger menu)
* **Tablet Breakpoint**: `769px` to `1024px` (2-column grids)
* **Desktop Grid**: `1025px+` (3 & 4 column cards, multi-column dashboard layouts)

---

## 4. Component Design Patterns

### 4.1 Buttons & Interactive Controls

```css
/* Primary Button */
.btn-primary {
  background: var(--green);
  color: var(--white);
  padding: 0.75rem 1.6rem;
  border-radius: var(--radius-sm);
  font-weight: 600;
  border: 1px solid var(--green-dark);
  transition: transform 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
  box-shadow: var(--shadow-sm);
}

.btn-primary:hover {
  background: var(--green-dark);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

/* Secondary Gold Accent Button */
.btn-accent {
  background: var(--gold-bright);
  color: var(--green-dark);
  padding: 0.75rem 1.6rem;
  border-radius: var(--radius-sm);
  font-weight: 700;
  border: 1px solid var(--gold);
  transition: transform 0.18s ease, box-shadow 0.18s ease;
}

.btn-accent:hover {
  background: #f0c238;
  transform: translateY(-2px);
  box-shadow: 0 4px 14px rgba(230, 184, 48, 0.4);
}
```

### 4.2 Cards & Surface Containers
* **Base Structure**: White surface (`--bg-white`), `1px solid var(--border-color)`, `10px` border radius (`--radius-md`).
* **Accent Top Stripe**: `border-top: 4px solid var(--gold-bright)` for featured/announcement cards.
* **Hover Interaction**: `transform: translateY(-4px)` with shadow elevation transition (`var(--shadow-md)` to `var(--shadow-lg)`).

### 4.3 Navigation & Header
* **Top Ticker Bar**: Background `#0d1f10`, ticker badge in Imperial Gold (`--gold-bright`), white text.
* **Main Header**: Sticky layout (`position: sticky; top: 0; z-index: 1000`), backdrop-filter blur (`backdrop-filter: blur(8px)`), dynamic height compression on scroll.

### 4.4 Data Tables & Catalogs
* **Headers**: Background `var(--green-dark)` with white bold text (`var(--white)`).
* **Zebra Striping**: Alternating row backgrounds (`var(--white)` / `var(--green-pale)`).
* **Hover Rows**: `background-color: #d8eadb`.
* **Badges**: Status indicators (e.g. "Active", "Full-Time", "Level 4") formatted as rounded pill elements (`border-radius: 9999px`).

---

## 5. Elevation, Depth & Glassmorphism

```css
:root {
  --shadow-sm: 0 4px 12px rgba(15, 35, 20, 0.09), 0 1px 3px rgba(0, 0, 0, 0.06);
  --shadow-md: 0 8px 24px rgba(15, 35, 20, 0.16), 0 2px 6px rgba(0, 0, 0, 0.08);
  --shadow-lg: 0 14px 38px rgba(15, 35, 20, 0.22), 0 4px 12px rgba(0, 0, 0, 0.10);
  --glass-bg:  rgba(255, 255, 255, 0.85);
  --glass-blur: blur(12px);
}
```

---

## 6. Accessibility & Motion Principles

1. **Contrast Compliance**: All text elements adhere to WCAG 2.1 AA standard (4.5:1 ratio for normal text, 3:1 for large headers).
2. **Focus Rings**: All interactive inputs, links, and buttons feature visible keyboard focus outlines (`outline: 3px solid var(--gold-bright); outline-offset: 2px`).
3. **Motion Scaling**:
   * Micro-interactions: `150ms cubic-bezier(0.4, 0, 0.2, 1)`
   * Medium transitions: `250ms ease`
   * Respect `@media (prefers-reduced-motion: reduce)` by disabling transform shifts and linearizing transitions.
