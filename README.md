
# Pornstar Feed API

A Laravel-based RESTful API that processes and serves data from a daily-updated JSON feed of pornstars. Built for a technical assessment, this application demonstrates backend architecture, data handling, Docker deployment, and test coverage. I hope I didn't forget anything.

## 🚀 Features

- Fetches and caches pornstars from a remote JSON feed
- Caches thumbnail images locally for performance
- Provides a RESTful API for the `Pornstar` entity
- Supports filtering, searching, sorting, and pagination
- Includes endpoints for statistics and license options
- Uses Docker for consistent deployment
- Includes unit tests
- Postman collection available for API testing (included in this repo)

---

## 📦 Tech Stack

- **Laravel** 
- **Redis**
- **MySQL**
- **Docker + Sail**
- **PHPUnit**

---

## 🛠 Setup & Installation

### 1. Clone the Repo

```bash
git clone https://github.com/Vagg-davios/ph-api.git
cd ph-api
````

### 2. Copy the `.env`

```bash
cp .env.example .env
```

Customize DB credentials and other environment variables as needed.

### 3. Start Docker with Sail

```bash
./vendor/bin/sail up -d
```

> Make sure Docker Desktop is running.

### 4. Install Dependencies

```bash
./vendor/bin/sail composer install
```

### 5. Generate App Key

```bash
./vendor/bin/sail artisan key:generate
```

### 6. Run Migrations

```bash
./vendor/bin/sail artisan migrate
```

---

## 📥 Import Pornstar Feed

```bash
./vendor/bin/sail artisan feed:download
```

This command:

* Downloads the JSON feed from the CDN endpoint
* Processes and stores pornstar records
* Caches their thumbnail images
* Is executed daily

---

## 📡 API Endpoints

| Method | Endpoint                       | Description                          |
| ------ | ------------------------------ | ------------------------------------ |
| GET    | `/api/pornstars`               | List pornstars with filters/search   |
| GET    | `/api/pornstars/{external_id}` | Get a single pornstar by external ID |
| GET    | `/api/pornstars/licenses`      | Get available licenses               |
| GET    | `/api/pornstars/stats`         | Get aggregate statistics             |
| GET    | `/api/pornstars/search?q=name` | Search by name or alias              |

### 🔍 Example Query

```http
GET /api/pornstars?search=Jane&license=exclusive&wl_status=true&sort_by=name&sort_dir=desc&per_page=10
```

---

## 📬 Postman Collection

1. Open Postman
2. Go to **Collections** → click **Import**
3. Paste the contents of `postman_collection.json` (provided in this repo) inside there
4. Click **Import**

---

## ✅ Running Tests

```bash
./vendor/bin/sail artisan test
```

Or for PHPUnit directly:

```bash
./vendor/bin/sail phpunit
```

---

## 📁 Project Structure Highlights

* `app/Services/FeedProcessorService.php` — Downloads and processes the JSON feed
* `app/Http/Controllers/Api/PornstarController.php` — API controller logic
* `app/Http/Resources/PornstarResource.php` — API resource for serialization
* `routes/api.php` — Route definitions
* `tests/Unit` — Unit tests for feed processing

---

## 🐳 Docker Commands

```bash
# Start containers
./vendor/bin/sail up -d

# Run Artisan commands
./vendor/bin/sail artisan <command>

# Run Tests
./vendor/bin/sail test
```

---

##  📝️ Key takeaways

* Laravel is awesome. Hadn't used it in such depth, and I was amazed at how much it can do for you. Like how quickly you can fire up an app and how it allows you to just focus on coding. 
* Setup was a bit of a pain on Windows though, but we made it nevertheless.
* Great job to whoever designed the assignment, it was very clever and fun. Loved the little challenges that you face along the way, truly a unique experience. 
* I hope I covered whatever was needed. No **UPDATE** or **DELETE** operations were added due to time and since Laravel makes it simple enough to add. 
* Excuse the one commit to the master branch type of uploading, I thought I'd just send it as a zip. 

---
