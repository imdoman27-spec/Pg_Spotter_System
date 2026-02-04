# CSS Classes Reference Guide

## New CSS Classes & Styling

### Hero Section Classes

#### `.hero-section`
- Rich blue gradient background (135deg)
- Padding: 80px 0
- Position relative with overflow hidden

#### `.hero-overlay`
- Radial gradients for depth
- Orange and white accent overlays
- Position absolute covering entire section

#### `.hero-content-wrapper`
- Flexbox display
- Center aligned content
- Position relative z-index 2

#### `.hero-text-section`
- Max-width: 900px
- Full width with centering

#### `.hero-title`
- Font-size: 56px (desktop)
- Color: #ffffff
- Font-weight: 800
- Letter-spacing: -1px
- Responsive sizes: 42px (tablet), 32px (mobile)

#### `.hero-subtitle`
- Font-size: 20px
- Color: rgba(255, 255, 255, 0.9)
- Font-weight: 300
- Responsive sizes: 18px (tablet), 16px (mobile)

---

### Search Form Classes

#### `.search-form`
- Background: rgba(255, 255, 255, 0.95)
- Backdrop-filter: blur(10px) - Glass morphism effect
- Padding: 25px
- Border-radius: 12px
- Box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2)

#### `.search-form-wrapper`
- Flexbox with wrap
- Gap: 15px
- Width: 100%

#### `.search-input-group`
- Position: relative
- Flex: 1 1 180px
- Min-width: 0

#### `.search-input-group input`
- Width: 100%
- Padding: 14px 40px 14px 15px
- Border: 2px solid #e0e0e0
- Border-radius: 8px
- Background-color: #f8f9fa
- Transition: all 0.3s ease

#### `.search-input-group input:focus`
- Border-color: #f7a01d
- Background-color: white
- Box-shadow: 0 0 0 3px rgba(247, 160, 29, 0.1)

#### `.search-icon`
- Position: absolute
- Right: 12px
- Top: 50%
- Transform: translateY(-50%)
- Color: #f7a01d
- Font-size: 18px

#### `.spot-it-btn`
- Background: linear-gradient(135deg, #f7a01d → #e59018)
- Color: white
- Padding: 14px 40px
- Font-weight: 700
- Text-transform: uppercase
- Box-shadow: 0 4px 15px rgba(247, 160, 29, 0.3)

#### `.spot-it-btn:hover`
- Background: darker orange
- Transform: translateY(-2px)
- Box-shadow: enhanced

---

### Featured Listings Classes

#### `.featured-listings`
- Padding: 60px 0
- Background: linear-gradient(180deg, #f8f9ff → #ffffff)

#### `.section-header`
- Text-align: center
- Margin-bottom: 50px

#### `.section-header h2`
- Font-size: 36px
- Color: #3952a3
- Font-weight: 800
- Responsive: 28px (tablet), 24px (mobile)

#### `.section-subtitle`
- Font-size: 16px
- Color: #666
- Font-weight: 300

#### `.listings-container`
- Display: grid
- Grid-template-columns: repeat(auto-fit, minmax(300px, 1fr))
- Gap: 30px
- Responsive: 250px (tablet), 1fr (mobile)

#### `.pg-card`
- Background: white
- Border-radius: 12px
- Box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08)
- Border: 1px solid #f0f0f0
- Display: flex flex-direction: column
- Height: 100%

#### `.pg-card:hover`
- Transform: translateY(-8px)
- Box-shadow: 0 12px 30px rgba(57, 82, 163, 0.15)
- Border-color: #f7a01d

#### `.card-image-wrapper`
- Position: relative
- Width: 100%
- Height: 200px
- Overflow: hidden
- Background: linear-gradient(135deg, #e0e0e0 → #f0f0f0)

#### `.card-image-wrapper img`
- Width: 100%
- Height: 100%
- Object-fit: cover
- Transition: transform 0.3s ease

#### `.card-image-wrapper img:hover`
- Transform: scale(1.1)

#### `.featured-badge`
- Position: absolute top right
- Background: linear-gradient(135deg, #f7a01d → #ff8c00)
- Color: white
- Padding: 6px 14px
- Border-radius: 20px
- Font-size: 12px
- Font-weight: 700
- Text-transform: uppercase
- Box-shadow: 0 4px 10px rgba(247, 160, 29, 0.3)

#### `.card-content`
- Padding: 20px
- Flex-grow: 1
- Display: flex flex-direction: column

#### `.card-content h3`
- Font-size: 18px
- Color: #333
- Font-weight: 700
- Margin-bottom: 8px

#### `.card-content .location`
- Color: #f7a01d
- Font-size: 14px
- Font-weight: 500
- Icon: 📍

#### `.rating`
- Display: flex
- Align-items: center
- Gap: 8px

#### `.rating-stars`
- Color: #ffc107
- Font-size: 16px

#### `.rating-value`
- Color: #666
- Font-size: 14px
- Font-weight: 600

#### `.card-content .price`
- Font-size: 20px
- Color: #3952a3
- Font-weight: 700
- Margin-bottom: 15px

#### `.price-label`
- Font-size: 14px
- Color: #999
- Font-weight: 400

#### `.details-btn`
- Background: linear-gradient(135deg, #3952a3 → #2a3f7d)
- Color: white
- Padding: 12px 20px
- Border-radius: 8px
- Font-weight: 600
- Transition: all 0.3s ease

#### `.details-btn:hover`
- Background: darker blue gradient
- Transform: translateX(4px)

#### `.view-more-section`
- Text-align: center
- Margin-top: 20px

#### `.browse-all-btn`
- Background: linear-gradient(135deg, #f7a01d → #ff8c00)
- Color: white
- Padding: 14px 40px
- Border-radius: 8px
- Font-weight: 700
- Font-size: 16px
- Text-transform: uppercase
- Box-shadow: 0 4px 15px rgba(247, 160, 29, 0.3)

#### `.browse-all-btn:hover`
- Transform: translateY(-2px)
- Box-shadow: enhanced

#### `.no-listings-message`
- Text-align: center
- Padding: 40px 20px
- Color: #999
- Grid-column: 1 / -1

---

### Call-to-Action Section Classes

#### `.cta-section`
- Background: linear-gradient(135deg, #f7a01d → #ff8c00)
- Padding: 60px 0
- Text-align: center
- Color: white
- Position: relative
- Overflow: hidden

#### `.cta-section::before`, `.cta-section::after`
- Decorative circles using radial gradients
- Position: absolute
- Background: rgba(255, 255, 255, opacity)
- Border-radius: 50%
- Pointer-events: none

#### `.cta-content`
- Position: relative
- Z-index: 2

#### `.cta-section h2`
- Font-size: 36px
- Font-weight: 800
- Margin-bottom: 15px
- Responsive: 28px (tablet)

#### `.cta-primary-btn`
- Background: white
- Color: #f7a01d
- Padding: 14px 40px
- Border-radius: 8px
- Font-weight: 700
- Border: 2px solid white
- Transition: all 0.3s ease

#### `.cta-primary-btn:hover`
- Background: transparent
- Color: white
- Transform: translateY(-2px)

#### `.cta-secondary-btn`
- Background: transparent
- Color: white
- Padding: 14px 40px
- Border: 2px solid white
- Font-weight: 700
- Transition: all 0.3s ease

#### `.cta-secondary-btn:hover`
- Background: white
- Color: #f7a01d

#### `.cta-secondary-text`
- Font-size: 14px
- Margin: 20px 0
- Opacity: 0.9

---

### Header Classes

#### `.main-header`
- Background: linear-gradient(135deg, #ffffff → #f8f9ff)
- Box-shadow: 0 4px 12px rgba(57, 82, 163, 0.1)
- Padding: 12px 0
- Position: sticky
- Top: 0
- Z-index: 100

#### `.header-content`
- Display: flex
- Justify-content: space-between
- Align-items: center
- Padding: 0 20px

#### `.logo a`
- Display: flex
- Align-items: center
- Gap: 10px
- Font-size: 24px
- Font-weight: bold
- Color: #3952a3
- Transition: transform 0.3s ease

#### `.logo a:hover`
- Transform: scale(1.05)

#### `.logo-image`
- Width: 40px
- Height: 40px
- Object-fit: contain

#### `.logo-text`
- Background: linear-gradient(135deg, #3952a3 → #f7a01d)
- -webkit-background-clip: text
- -webkit-text-fill-color: transparent
- Font-weight: 800
- Letter-spacing: -0.5px

#### `.main-nav ul`
- Display: flex
- Gap: 30px
- List-style: none

#### `.main-nav a`
- Text-decoration: none
- Color: #555
- Padding: 8px 0
- Font-weight: 500
- Position: relative
- Transition: all 0.3s ease

#### `.main-nav a::after`
- Animated underline effect
- Width: 0 → 100% on hover/active
- Height: 2px
- Background: linear-gradient(90deg, #f7a01d → #3952a3)

#### `.auth-links a`
- Padding: 8px 15px
- Border-radius: 6px
- Font-weight: 600
- Font-size: 14px

#### `.signup-btn`
- Background: linear-gradient(135deg, #f7a01d → #ff8c00)
- Color: white
- Box-shadow: 0 4px 12px rgba(247, 160, 29, 0.3)

#### `.login-btn`
- Background: transparent
- Border: 2px solid #3952a3
- Color: #3952a3

#### `.login-btn:hover`
- Background-color: #3952a3
- Color: white

---

### Responsive Media Query Classes

#### `@media (max-width: 768px)`
- Tablet optimizations
- Adjusted font sizes
- Column count changes
- Navigation adjustments

#### `@media (max-width: 480px)`
- Mobile optimizations
- Single column layout
- Hidden navigation
- Stacked buttons
- Full-width inputs

---

## Color Palette

```css
/* Primary Colors */
--primary-blue: #3952a3;
--accent-orange: #f7a01d;
--dark-blue: #1f2d5e;
--dark-blue-alt: #2a3f7d;

/* Neutral Colors */
--white: #ffffff;
--light-gray: #f8f9ff;
--light-gray-alt: #f0f0f0;
--medium-gray: #666;
--dark-gray: #333;

/* Semantic Colors */
--border-color: #e0e0e0;
--shadow-color: rgba(0, 0, 0, 0.08);
--hover-shadow: rgba(57, 82, 163, 0.15);
--gold: #ffc107;
```

---

## Typography

```css
/* Font Family */
font-family: 'Roboto', Arial, sans-serif;

/* Font Sizes (Desktop) */
h1 (hero-title): 56px
h2 (section headers): 36px
h3 (card titles): 18px
p (body): 16px
p (small): 14px
p (extra-small): 12px

/* Font Weights */
Light: 300
Regular: 400
Medium: 500
Bold: 600
Extra Bold: 700
Black: 800

/* Line Height */
Default: 1.6
Headings: 1.2
```

---

## Transitions & Animations

```css
/* Default Transition */
transition: all 0.3s ease;

/* Common Effects */
transform: translateY(-8px);  /* Card lift */
transform: translateY(-2px);  /* Button lift */
transform: translateX(4px);   /* Button slide */
transform: scale(1.05);       /* Logo scale */
transform: scale(1.1);        /* Image zoom */
```

---

## Shadows

```css
/* Subtle Shadow */
box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);

/* Medium Shadow */
box-shadow: 0 4px 15px rgba(247, 160, 29, 0.3);

/* Heavy Shadow */
box-shadow: 0 12px 30px rgba(57, 82, 163, 0.15);

/* Extra Heavy */
box-shadow: 0 6px 20px rgba(247, 160, 29, 0.4);
```

---

**Last Updated**: February 4, 2026
**Version**: 1.0
