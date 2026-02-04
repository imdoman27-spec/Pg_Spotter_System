# 🎨 Visual Design Reference

## Color Codes Quick Reference

```
BRAND COLORS:
┌─────────────────────────────────┐
│ Primary Blue:   #3952a3         │
│ RGB(57, 82, 163)                │
│ HSL(216°, 48%, 43%)             │
│ CMYK(65%, 50%, 2%, 36%)         │
└─────────────────────────────────┘

┌─────────────────────────────────┐
│ Accent Orange:  #f7a01d         │
│ RGB(247, 160, 29)               │
│ HSL(35°, 95%, 54%)              │
│ CMYK(0%, 35%, 88%, 3%)          │
└─────────────────────────────────┘

┌─────────────────────────────────┐
│ White:          #ffffff         │
│ RGB(255, 255, 255)              │
│ HSL(0°, 0%, 100%)               │
└─────────────────────────────────┘

SUPPORTING COLORS:
┌─────────────────────────────────┐
│ Dark Blue:      #1f2d5e         │
│ Dark Blue Alt:  #2a3f7d         │
│ Light Gray:     #f8f9ff         │
│ Border Gray:    #e0e0e0         │
│ Text Gray:      #333333         │
│ Disabled Gray:  #666666         │
└─────────────────────────────────┘
```

---

## Typography Guide

### Font Family
- **Primary Font**: Roboto
- **Fallbacks**: Arial, sans-serif
- **Import**: Google Fonts

### Font Sizes & Hierarchy

```
DESKTOP SIZES:
├─ Hero Title (h1)          56px - Font Weight: 800
├─ Section Headers (h2)     36px - Font Weight: 800
├─ Card Titles (h3)         18px - Font Weight: 700
├─ Body Text (p)            16px - Font Weight: 400
├─ Small Text (p)           14px - Font Weight: 400
├─ Extra Small (label)      12px - Font Weight: 600
└─ Button Text              16px - Font Weight: 700

TABLET SIZES:
├─ Hero Title               42px
├─ Section Headers          28px
└─ Other text               responsive

MOBILE SIZES:
├─ Hero Title               32px
├─ Section Headers          24px
├─ Card Titles              16px
└─ Body Text                14px
```

### Font Weights
```
300 - Light              (Subtitles, secondary text)
400 - Regular            (Body text)
500 - Medium             (Nav links, labels)
600 - Semi-Bold          (Buttons, labels)
700 - Bold               (Headings, CTA text)
800 - Extra Bold         (Page titles, hero text)
```

---

## Spacing & Layout

### Container Widths
```
Desktop:     max-width: 1200px
Tablet:      90% width
Mobile:      90% width
```

### Padding & Margins
```
Hero Section:      60-80px top/bottom
Featured Section:  60px top/bottom
CTA Section:       60px top/bottom
Card Padding:      20px
Search Form:       25px
Button Padding:    12-14px
```

### Grid Gaps
```
Featured Listings:  30px (desktop)
                    20px (tablet)
                    15px (mobile)

Search Form:        15px (all sizes)
```

---

## Button Styles

### Primary Button (Spot It!)
```
Background:    #f7a01d → #e59018 (orange gradient)
Color:         white
Padding:       14px 40px
Border:        none
Border-radius: 8px
Font-weight:   700
Font-size:     16px
Hover:         darker orange + translateY(-2px)
Shadow:        0 4px 15px rgba(247, 160, 29, 0.3)
```

### Secondary Button (Details)
```
Background:    #3952a3 → #2a3f7d (blue gradient)
Color:         white
Padding:       12px 20px
Border-radius: 8px
Font-weight:   600
Hover:         darker blue + translateX(4px)
```

### CTA Buttons
```
Primary:
  Background:  white
  Color:       #f7a01d
  Border:      2px solid white
  
Secondary:
  Background:  transparent
  Color:       white
  Border:      2px solid white
  
Hover:
  Swap colors/transparency
```

---

## Shadow System

### Levels
```
Level 1 (Subtle):
  0 2px 4px rgba(0, 0, 0, 0.08)

Level 2 (Medium):
  0 4px 12px rgba(0, 0, 0, 0.08)

Level 3 (Heavy):
  0 4px 15px rgba(247, 160, 29, 0.3)

Level 4 (Extra Heavy):
  0 12px 30px rgba(57, 82, 163, 0.15)

Level 5 (Hover):
  0 6px 20px rgba(247, 160, 29, 0.4)
```

---

## Gradient References

### Hero Section Background
```css
background: linear-gradient(
  135deg,
  #3952a3 0%,
  #2a3f7d 50%,
  #1f2d5e 100%
);
```

### Button Gradients
```css
/* Orange Gradient */
background: linear-gradient(
  135deg,
  #f7a01d 0%,
  #ff8c00 100%
);

/* Blue Gradient */
background: linear-gradient(
  135deg,
  #3952a3 0%,
  #2a3f7d 100%
);
```

### Section Background
```css
background: linear-gradient(
  180deg,
  #f8f9ff 0%,
  #ffffff 100%
);
```

### Radial Overlays (Hero)
```css
background: 
  radial-gradient(circle at 20% 50%, 
    rgba(247, 160, 29, 0.1) 0%, 
    transparent 50%),
  radial-gradient(circle at 80% 80%, 
    rgba(255, 255, 255, 0.05) 0%, 
    transparent 50%);
```

---

## Border & Corners

### Border Radius
```
Hero & Sections:    12px
Buttons:            8px
Badge:              20px (pill-shaped)
Input Fields:       8px
Cards:              12px
Overlays:           50% (circles)
```

### Borders
```
Cards:              1px solid #f0f0f0
Input Focus:        2px solid #f7a01d
Input Default:      2px solid #e0e0e0
Hover State:        1px solid #f7a01d
```

---

## Transitions & Animations

### Timing
```
Standard:           0.3s ease
Fast:               0.2s ease
Slow:               0.5s ease
```

### Effects
```
Hover Effects:
  - translateY(-8px)    [Card lift]
  - translateY(-2px)    [Button lift]
  - translateX(4px)     [Button slide]
  - scale(1.05)         [Logo]
  - scale(1.1)          [Image zoom]

Transitions:
  - all 0.3s ease
  - transform 0.3s ease
  - color 0.3s ease
  - box-shadow 0.3s ease
```

---

## Breakpoints

```
Desktop:    > 768px
  - Full navigation
  - 3-column grid
  - Full features
  - Max width 1200px

Tablet:     768px - 480px
  - Compact navigation
  - 2-column grid
  - Optimized spacing
  - 90% width

Mobile:     < 480px
  - Hidden navigation
  - 1-column grid
  - Stacked buttons
  - 90% width
```

---

## Component Dimensions

### Logo
```
Desktop:    40px × 40px
Tablet:     35px × 35px
Mobile:     32px × 32px
```

### Card Dimensions
```
Desktop:    Grid auto-fit
            Min: 300px
            Gap: 30px

Tablet:     Grid auto-fit
            Min: 250px
            Gap: 20px

Mobile:     1 column
            100% width
            Gap: 15px
```

### Image Containers
```
Featured Card Image:   100% × 200px
Featured Badge:        6px 14px padding
```

---

## Icon System

### Search Form Icons
```
📍 Location       - #f7a01d orange
₹ Budget          - #f7a01d orange
⭐ Amenities      - #f7a01d orange
Position:         right 12px, vertically centered
Size:             18px
```

### Card Icons
```
📍 Location       - Before location text
Featured Badge:   Top-right corner
```

---

## Glass-Morphism Effect

```css
background: rgba(255, 255, 255, 0.95);
backdrop-filter: blur(10px);
border: 1px solid rgba(255, 255, 255, 0.2);
box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
```

---

## Z-Index Hierarchy

```
100  - Sticky Header
50   - Overlays/Modals
10   - Dropdowns
2    - Hero Content
1    - Base Content
0    - Hero Overlay (background)
```

---

## Accessibility

### Color Contrast
```
Text on Blue (#3952a3):
  - White text ✓ WCAG AAA

Text on Orange (#f7a01d):
  - White text ✓ WCAG AAA
  - Dark text ✓ WCAG AA

Text on White:
  - Dark text (#333) ✓ WCAG AAA
```

### Font Sizing
```
Minimum:    12px (for labels)
Body:       16px (readable)
Headings:   24px+ (clear)
Mobile:     14px+ (touch-friendly)
```

### Touch Targets
```
Buttons:    Minimum 44px height
Links:      Minimum 44px height
Inputs:     Minimum 44px height
```

---

## Image Optimization

### Featured Card Images
```
Aspect Ratio:  Responsive (auto)
Height:        200px on all devices
Object-fit:    cover
Format:        JPG/PNG optimized
Hover Effect:  scale(1.1)
Transition:    0.3s ease
```

---

## Spacing Scale

```
4px  - xs
8px  - sm
12px - md
15px - lg
20px - xl
30px - 2xl
40px - 3xl
60px - 4xl
80px - 5xl
```

---

## Complete Color Palette

```
BLUES:
  #3952a3  - Primary Blue
  #2a3f7d  - Dark Blue
  #1f2d5e  - Extra Dark Blue
  #f8f9ff  - Light Blue BG

ORANGES:
  #f7a01d  - Primary Orange
  #ff8c00  - Bright Orange
  #e59018  - Dark Orange
  #ffc107  - Gold (ratings)

NEUTRALS:
  #ffffff  - White
  #f0f0f0  - Light Gray
  #e0e0e0  - Border Gray
  #999999  - Medium Gray
  #666666  - Dark Gray
  #333333  - Text Dark
  #222222  - Extra Dark

SEMANTIC:
  success  - Green (future use)
  warning  - Yellow (future use)
  error    - Red (future use)
```

---

## Responsive Text

```
Hero Title:
  Desktop:  56px
  Tablet:   42px
  Mobile:   32px

Section Headers:
  Desktop:  36px
  Tablet:   28px
  Mobile:   24px

Card Titles:
  Desktop:  18px
  Mobile:   16px

Body Text:
  Desktop:  16px
  Mobile:   14px
```

---

## Component States

### Button States
```
Default:   Gradient background
Hover:     Darker gradient + transform
Active:    Same as hover
Disabled:  Opacity 0.5 (not implemented)
Focus:     Border outline (keyboard)
```

### Input States
```
Default:   Border #e0e0e0
Focus:     Border #f7a01d + shadow
Error:     Border red (future)
Disabled:  Opacity 0.5 (future)
```

### Card States
```
Default:   Subtle shadow
Hover:     Enhanced shadow + lift
Focus:     Highlight border
```

---

**Design Reference Version**: 1.0
**Last Updated**: February 4, 2026
