# TaskFlow Test – Laravel Full Stack

## 📦 Installation

1. **Clone the repository**

```bash
git clone git@github.com:herihaja/task-manager.git task-manager
cd task-manager
```

2. **Configure environment**

```bash
cp .env.example .env
# Fill in MYSQL_DATABASE, MYSQL_USER, MYSQL_PASSWORD
```

3. **Start Docker environment**

```bash
docker-compose up -d --build
```

4. **Install dependencies and setup**

```bash
docker exec -it taskflow_app bash
composer install
npm install
npm run dev
php artisan key:generate
php artisan migrate --seed
```

5. **Register a user via the web UI (/register)**

6. **Seed categories for the newly registered user:**
   php artisan db:seed --class=CategorySeeder

7. **Populate demo tasks for that user via Tinker (Optional):**

```bash
   php artisan tinker
   $u = App\Models\User::first();
   App\Models\Task::factory()->count(50)->create(['user_id' => $u->id]);
```

8. **Access the application**

- Livewire CRUD: [http://localhost:8000/task-manager](http://localhost:8000/task-manager)
- Inertia detail page: [http://localhost:8000/tasks/{id}](http://localhost:8000/tasks/{id})
- API endpoints: `/api/tasks`, `/api/categories`

---

## ⚠️ Important Notes / Good to know

- **Categories**: Users currently have no UI to create categories. Default categories are seeded for testing purposes.
- **New users**: Categories are required to create tasks. Seeder ensures at least some categories exist.
- **API access**: Token-based API access can be created via Tinker:

```bash
php artisan tinker
$user = App\Models\User::first();
$token = $user->createToken('api-token')->plainTextToken;
```

- **Testing**: Feature and Unit tests are in `tests/Feature` and `tests/Unit`. Run all tests with:

```bash
php artisan test
```

---

## 🛠 Technical Choices

- **Introduced TaskService to encapsulate business logic for tasks**
- **Implemented seeder for categories to ensure users have default categories**
- **Designed feature and unit tests to cover core functionality**
- **Created Dockerized environment for reproducible setup**
- **Documented optional enhancements for future improvements (multi-tenant DB, caching, search)**

---

## ⚡ Limitations / Points Bloquants

- **Category creation in UI is not implemented; seeding is used for testing**
- **No advanced caching or search integration yet**
- **Single-tenant database**: All users share the same DB; multi-tenant design could improve isolation and scaling.

---

## 🚀 Possible Enhancements

1. **Multi-tenant database per user**: Separate schema or DB for each user.
2. **Caching**: Use Redis or query caching to improve Livewire task list performance.
3. **Search & Filters**: Integrate OpenSearch + Laravel Scout for performant task search.
4. **Notifications & Jobs**: Task reminders, priority alerts, queued notifications.
5. **Livewire optimizations**: Debounced inputs, lazy-loading, pagination for large datasets.
6. **API versioning**: Prepare `/api/v1/...` endpoints for future backward compatibility.
7. **Full frontend styling**: Responsive Tailwind theme, mobile-first design, polished UX.

---

## ✅ Summary

This project demonstrates:

- Core Laravel architecture and service layer separation
- Reactive Livewire components and Inertia SPA view
- REST API endpoints for tasks and categories
- Unit and feature test coverage
- Dockerized development environment for quick setup and testing
