# AlkesLink 🏥

**Platform Pencocokan & Penyaluran Alat Kesehatan Bekas Layak Pakai**

---

## Tech Stack
- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Blade + Tailwind CSS (via Vite)
- **Database**: SQLite (local) / MySQL (production)
- **AI Widget**: IBM Granite via Watson Machine Learning REST API

---

## Quick Start

```bash
# 1. Install PHP dependencies
composer install

# 2. Copy environment file
cp .env.example .env

# 3. Generate app key
php artisan key:generate

# 4. Create SQLite database
touch database/database.sqlite

# 5. Run migrations + seed demo data
php artisan migrate --seed

# 6. Create storage symlink
php artisan storage:link

# 7. Install Node dependencies & build assets
npm install && npm run dev

# 8. Start development server
php artisan serve
```

Open **http://localhost:8000**

---

## Demo Accounts

| Role      | Email                          | Password   |
|-----------|-------------------------------|------------|
| Admin     | admin@alkeslink.id             | password   |
| Donor     | budi.donor@example.com         | password   |
| Penerima  | andi.penerima@example.com      | password   |

---

## AI Assistant Setup (IBM Bob / Watson)

Add these to your `.env`:

```env
AI_API_KEY=your_ibm_api_key
AI_API_URL=https://us-south.ml.cloud.ibm.com/ml/v1/text/generation
AI_MODEL_ID=ibm/granite-13b-chat-v2
IBM_PROJECT_ID=your_project_id
```

---

## Key URLs

| URL                    | Description                |
|------------------------|---------------------------|
| `/`                    | Landing page               |
| `/catalog`             | Browse alkes               |
| `/donor/items/create`  | Upload barang (donor)      |
| `/donor/requests`      | Kelola permintaan (donor)  |
| `/recipient/requests`  | Status pengajuan (penerima)|
| `/admin`               | Dashboard admin            |

---

## Project Structure (Key Files)

```
app/
  Http/Controllers/     — LandingController, ItemController, RequestController,
                          ImpactController, AiAssistantController, Admin/AdminController
  Models/               — User, Item, ItemImage, Category, DonationRequest,
                          AuditLog, ImpactStat, ChatSession, ChatMessage
  Services/             — ImpactService, MatchingService, AiService
  Http/Requests/        — StoreItemRequest, UpdateItemRequest, StoreRequestForm
  Http/Middleware/       — RoleMiddleware

database/
  migrations/           — 9 migration files
  seeders/              — CategorySeeder, DemoDataSeeder

resources/
  views/layouts/        — app.blade.php (main layout)
  views/components/     — item-card, status-badge, ai-widget
  views/pages/          — landing, catalog/index, catalog/show,
                          donor/upload, donor/my-items, donor/requests,
                          recipient/my-requests, admin/dashboard
  js/modules/           — ai-chat.js, impact-counter.js, image-preview.js

routes/
  web.php               — All page routes (public + auth + role-based)
  api.php               — /api/impact, /api/ai/chat, /api/ai/history
```
