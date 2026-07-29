# BroCore — A Shared Male Experience Platform

> A community-driven platform where men share relatable life experiences, stories, struggles, funny moments, and lessons learned. Built as a full-stack SPA to practice Laravel + Vue.js embedded architecture with REST API communication.

---

## 🎯 Project Goal

BroCore was built as a **learning project** to deeply understand how a Vue 3 frontend communicates with a Laravel backend through a REST API — without relying on higher-level abstractions like Inertia.js or Livewire. Every API call, auth flow, and state update was wired manually to build a solid foundation.

---

## ✨ Features

### 👤 User Roles

- **Guest Users** — browse posts, react, and comment without an account
- **Authenticated Users** — create, edit, and delete their own posts and comments

### 📝 Posts

- Full CRUD (Create, Read, Update, Delete)
- Category filtering and keyword search (server-side, debounced)
- Load more pagination
- Owner-only edit/delete with Laravel Policies

### 👍 Reactions

- Relatable (Upvote) / Not Relatable (Downvote) per post
- Toggle and switch reactions
- Guest reactions via `guest_identifier` stored in `localStorage`
- Optimistic UI with rollback on failure

### 💬 Comments

- Both guests and authenticated users can comment
- Guest comments display auto-generated guest name (`guest_XXXX`)
- Auth users can edit and delete their own comments
- Guests cannot edit or delete comments

### 🗂️ Categories

- Fixed predefined categories (Career, Relationships, Fitness, etc.)
- Filter posts by category

### 🔐 Authentication

- Cookie-based Laravel Sanctum auth
- Register, Login, Logout
- Route guards via Vue Router
- Persistent auth state via Pinia store

### 📊 Dashboard

- Manage your own posts (edit/delete)
- Responsive table on desktop, card layout on mobile
- Upvote + comment counts per post

---

## 🛠️ Tech Stack

### Backend

| Tool                 | Purpose                         |
| -------------------- | ------------------------------- |
| **Laravel 13**       | Backend framework, REST API     |
| **Laravel Sanctum**  | Cookie-based SPA authentication |
| **Laravel Policies** | Post and comment authorization  |
| **MySQL**            | Database                        |
| **PHP 8.4**          | Runtime                         |

### Frontend

| Tool                                   | Purpose                          |
| -------------------------------------- | -------------------------------- |
| **Vue 3**                              | Frontend framework               |
| **Composition API + `<script setup>`** | Component logic                  |
| **Vue Router 5**                       | SPA navigation with route guards |
| **Pinia**                              | Global auth state management     |
| **Axios**                              | HTTP client with interceptors    |
| **Tailwind CSS v4**                    | Utility-first styling            |
| **Lucide Vue Next**                    | Consistent icon system           |
| **Day.js**                             | Date formatting                  |

### Dev Tools

| Tool             | Purpose                       |
| ---------------- | ----------------------------- |
| **Vite**         | Frontend bundler              |
| **Laravel Herd** | Local development environment |
| **TablePlus**    | Database GUI                  |
| **dbdiagram.io** | Database schema design        |

---

## 🗄️ Database Schema

```
users
  id, name, email, password, timestamps

categories
  id, name, slug, timestamps

posts
  id, user_id → users, category_id → categories
  title, content, timestamps

comments
  id, post_id → posts, user_id → users (nullable)
  guest_name (nullable), content, timestamps

reactions
  id, post_id → posts, user_id → users (nullable)
  guest_identifier (nullable), reaction_type (upvote/downvote)
  timestamps
```

---

## 🏗️ Architecture

```
resources/js/
├── app.js                    # Vue app entry point
├── App.vue                   # Root component
├── router/
│   └── index.js              # Vue Router + route guards
├── stores/
│   └── AuthStore.js          # Pinia auth store
├── services/
│   └── api.js                # Axios instance + interceptors
├── composables/
│   ├── useGuestId.js         # Guest identifier management
│   ├── useGuest.js           # Guest name management
│   ├── useReaction.js        # Reaction API calls
│   ├── useReactionCounter.js # Upvote/downvote counts
│   └── useErrorHandler.js    # Consistent error messages
├── components/
│   ├── layouts/
│   │   └── DefaultLayout.vue # Navbar + Footer wrapper
│   ├── ui/
│   │   ├── BaseLoader.vue
│   │   ├── BaseError.vue
│   │   ├── LoadMoreBtn.vue
│   │   └── DeleteConfirmModal.vue
│   ├── posts/
│   │   ├── PostFormModal.vue  # Create + Edit modal
│   │   ├── CommentSection.vue
│   │   └── CommentItem.vue
│   └── TheNavbar.vue
│   └── TheFooter.vue
└── pages/
    ├── LandingPage.vue
    ├── Auth/
    │   ├── LoginPage.vue
    │   └── RegisterPage.vue
    ├── Post/
    │   ├── Posts.vue          # Explore + Search + Filter
    │   └── PostDetail.vue     # Full post + Reactions + Comments
    └── DashboardPage.vue      # Manage own posts
```

---

## 🎨 Design System

BroCore uses a custom **Dark Crimson** theme defined via Tailwind v4 `@theme` tokens:

```css
--color-bro-bg: #0a0a0a;
--color-bro-surface: #121212;
--color-bro-border: #1f1f1f;
--color-bro-muted: #9e9e9e;
--color-bro-light: #f5f5f5;
--color-bro-crimson: #b71c1c;
--color-bro-crimson-hover: #e53935;
```

---

## 🚀 Getting Started

### Prerequisites

- PHP 8.4+
- Node.js 20+
- MySQL
- Laravel Herd or Laragon

### Installation

```bash
# Clone the repository
git clone https://github.com/extraorjjnary/Blog-App.git
cd Blog-App

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Configure your database in .env
DB_CONNECTION=mysql
DB_DATABASE=blog
DB_USERNAME=root
DB_PASSWORD=

# Run migrations and seed
php artisan migrate --seed

# Start development
composer run dev
```

Visit `http://blog.test` or `http://localhost:8000`

---

## 📚 Key Learnings

This project was built to practice:

- ✅ Laravel + Vue SPA embedded architecture (no Inertia/Livewire)
- ✅ Cookie-based Sanctum auth flow (CSRF, withCredentials, interceptors)
- ✅ REST API design and consumption
- ✅ Laravel Policies for authorization
- ✅ Pinia for global reactive state
- ✅ Vue composables for reusable logic
- ✅ Optimistic UI for reactions
- ✅ Guest user experience (localStorage identifiers)
- ✅ Server-side search and filtering with debounce
- ✅ Layout-based routing pattern
- ✅ Responsive design (mobile + desktop)

---

## 👤 Author

**Utol** — Beginner developer from the Philippines 🇵🇭

Built this project from scratch after 1 month of Laravel experience and completing Vue.js self-study. Every line of logic was written with deep understanding — no magic abstractions.

---

## 📄 License

This project is open source and available under the [MIT License](LICENSE).
