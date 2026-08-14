# ShopLaravel — E-Commerce Boilerplate

A full-stack e-commerce boilerplate built as a learning project following structured architecture principles. Built with **Laravel 12**, **Inertia.js**, **Vue 3**, and **Tailwind CSS**.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Vue 3 (Composition API + TypeScript) |
| Bridge | Inertia.js v2 |
| Styling | Tailwind CSS v3 |
| Auth | Laravel Breeze (Vue stack) |
| Database | PostgreSQL (configurable) |
| Build | Vite 7 + @vitejs/plugin-vue |
| ORM | Eloquent |

---

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+ and npm
- PostgreSQL (or change `DB_CONNECTION` to `sqlite` for local dev)

---

## Local Setup

### 1. Clone and install dependencies

```bash
git clone <repo-url>
cd ecommerce

composer install
npm install
```

### 2. Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your database credentials:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ecom
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

> To use SQLite instead, set `DB_CONNECTION=sqlite` and create `database/database.sqlite`.

### 3. Migrate and seed

```bash
php artisan migrate:fresh --seed
```

This creates all tables and seeds:
- 1 admin user
- 1 customer user
- 6 categories with 8 products each (48 products total)

### 4. Run the development servers

Open **two terminals**:

```bash
# Terminal 1 — Laravel
php artisan serve

# Terminal 2 — Vite
npm run dev
```

Visit `http://localhost:8000`

---

## Seeded Accounts

| Email | Password | Role |
|---|---|---|
| `admin@example.com` | `password` | admin |
| `customer@example.com` | `password` | customer |

---

## Features

### Storefront (public)
- Product grid with search and category filter pills
- Pagination (12 products per page)
- Stock badges (In stock / Low stock / Out of stock)
- Hero banner with CTA
- Responsive navbar with cart icon and badge
- Footer with navigation links

### Customer (authenticated)
- Add to Cart from product grid
- Cart page — adjust quantity, remove items, order summary
- Place Order — server-side stock validation, price snapshot, DB transaction
- Order history with status and payment badges
- Order detail with itemized breakdown and totals
- Profile management (Breeze)

### Admin (authenticated + admin role)
- Product list with pagination
- Create product form (name, SKU, category, description, price, stock, active toggle)
- Edit product form
- Delete product
- Category management (CRUD scaffold)

### Auth (Laravel Breeze)
- Login / Register
- Password reset
- Email verification
- Profile update and delete account

---

## Role-Based Redirect

After login, `/dashboard` redirects based on role:

| Role | Redirects to |
|---|---|
| `admin` | `/admin/products` |
| `customer` | `/orders` |

---

## Project Structure

```
app/
├── Actions/
│   └── Products/
│       └── CreateProduct.php       # Business logic for product creation
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── ProductController.php
│   │   │   └── CategoryController.php
│   │   ├── Store/
│   │   │   ├── CartController.php
│   │   │   └── OrderController.php
│   │   └── Auth/                   # Breeze auth controllers
│   ├── Middleware/
│   │   └── HandleInertiaRequests.php  # Shares auth, cartCount, flash globally
│   └── Requests/
│       ├── StoreProductRequest.php
│       └── UpdateProductRequest.php
├── Models/
│   ├── User.php
│   ├── Category.php
│   ├── Product.php
│   ├── ProductVariant.php
│   ├── ProductImage.php
│   ├── Cart.php
│   ├── CartItem.php
│   ├── Order.php
│   ├── OrderItem.php
│   ├── Payment.php
│   ├── Shipment.php
│   ├── InventoryMovement.php
│   └── Address.php
└── Policies/
    └── ProductPolicy.php

resources/js/
├── Components/                     # Breeze UI components
├── Layouts/
│   ├── AuthenticatedLayout.vue
│   └── GuestLayout.vue
└── Pages/
    ├── Auth/                       # Login, Register, etc.
    ├── Admin/
    │   └── Products/
    │       ├── Index.vue
    │       ├── Create.vue
    │       └── Edit.vue
    ├── Store/
    │   ├── Home.vue                # Storefront with product grid
    │   ├── Cart.vue                # Cart page
    │   ├── Orders.vue              # Order history
    │   └── OrderDetail.vue         # Single order detail
    └── Profile/

database/
├── migrations/                     # 16 migrations in dependency order
├── factories/
│   ├── CategoryFactory.php
│   └── ProductFactory.php
└── seeders/
    └── DatabaseSeeder.php

routes/
├── web.php                         # All application routes
└── auth.php                        # Breeze auth routes
```

---

## Database Schema

```
users               — id, name, email, password, role (admin|customer)
addresses           — id, user_id, street, city, province, zip, country
categories          — id, name, slug, description, is_active
products            — id, category_id, name, slug, sku, description, price, stock, is_active
product_variants    — id, product_id, name, sku, price, stock
product_images      — id, product_id, path, sort_order
carts               — id, user_id, status (active|completed)
cart_items          — id, cart_id, product_id, product_variant_id, quantity
orders              — id, user_id, order_number, status, payment_status, fulfillment_status, subtotal, shipping_total, discount_total, tax_total, grand_total, shipping_address
order_items         — id, order_id, product_id, product_name, sku, unit_price, quantity, subtotal
payments            — id, order_id, gateway, reference, status, amount
shipments           — id, order_id, carrier, tracking_number, status
inventory_movements — id, product_id, order_id, type, quantity, balance_after, reason
```

---

## Routes

### Public
| Method | URI | Description |
|---|---|---|
| GET | `/` | Storefront — product grid |
| GET | `/login` | Login page |
| GET | `/register` | Register page |

### Customer (auth required)
| Method | URI | Description |
|---|---|---|
| GET | `/cart` | View cart |
| POST | `/cart` | Add item to cart |
| PATCH | `/cart/{item}` | Update item quantity |
| DELETE | `/cart/{item}` | Remove item |
| GET | `/orders` | Order history |
| POST | `/orders` | Place order |
| GET | `/orders/{order}` | Order detail |
| GET | `/profile` | Edit profile |

### Admin (auth required)
| Method | URI | Description |
|---|---|---|
| GET | `/admin/products` | Product list |
| GET | `/admin/products/create` | Create product form |
| POST | `/admin/products` | Store product |
| GET | `/admin/products/{id}/edit` | Edit product form |
| PUT | `/admin/products/{id}` | Update product |
| DELETE | `/admin/products/{id}` | Delete product |
| — | `/admin/categories/*` | Category CRUD |

---

## Architecture Decisions

**Actions** — Business operations (e.g. `CreateProduct`) live outside controllers. Controllers stay thin.

**Form Requests** — All validation and authorization in dedicated request classes (`StoreProductRequest`, `UpdateProductRequest`).

**Policies** — `ProductPolicy` gates every admin mutation. `authorize()` is available via `AuthorizesRequests` trait on the base `Controller`.

**Server-side totals** — Prices and stock are always re-read from the database on order placement. Client-sent totals are never trusted.

**DB Transaction** — Order placement wraps all writes (order, order items, stock deduction, cart completion) in a single transaction.

**Shared Inertia props** — `auth.user`, `cartCount`, and `flash` messages are shared globally via `HandleInertiaRequests` so every page has access without explicit passing.

---

## What's Not Yet Built

These are scaffolded in the DB schema but not yet implemented:

- [ ] Checkout with address selection
- [ ] Payment gateway integration (contract defined)
- [ ] Shipping provider integration (contract defined)
- [ ] Product variants UI
- [ ] Product image uploads
- [ ] Admin order management
- [ ] Admin dashboard with stats
- [ ] Inventory movement tracking
- [ ] Coupon / discount system
- [ ] Product reviews
- [ ] Wishlist
- [ ] Email notifications

---

## Running Tests

```bash
php artisan test
```

---

## Production Build

```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm ci
npm run build
php artisan migrate --force
```

> Always verify deployment commands against your server setup, process manager, and hosting provider before running in production.
