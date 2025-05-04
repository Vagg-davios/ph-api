
# Pornstar Feed API

A Laravel-based REST API that processes and serves data from a daily-updated JSON feed. I hope I didn't forget anything.

## 🚀 Features

- Fetches and caches pornstars from a remote JSON feed
- Caches thumbnail images locally for performance
- Provides a RESTful API for the `Pornstar` entity
- Supports filtering, searching, sorting, and pagination
- Includes endpoints for statistics and license options
- Includes unit tests
- Postman collection available for API testing (included in this repo)

---

## ⚙ Prerequisites 
- Linux / MacOS / Windows with WSL2
- Docker

## 📦 Stack

- **Laravel** 
- **Redis**
- **MySQL**
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

DB username & password are set, just add the CDN link to **FEED_URL**

### 3. Composer install

```bash
docker run --rm \
 -v $(pwd):/opt \
 -w /opt \
 composer:latest install
```
> Make sure Docker Desktop is running.

### 4. Fire up Docker

```bash
./vendor/bin/sail up -d
```

### 5. Generate App Key

```bash
./vendor/bin/sail artisan key:generate
```

### 6. Run Migrations

```bash
./vendor/bin/sail artisan migrate
```

### 7. Fetch data

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
GET /api/pornstars/search?q=Jane&license=exclusive&wl_status=true&sort_by=name&sort_dir=desc&per_page=10
```

---

## 📬 Postman Collection

1. Open Postman
2. Go to **Collections** → click **Import**
3. Paste the contents of `postman_collection.json` (provided in this repo) inside there
4. Click **Import**

---

##  📝️ Key takeaways

* Laravel is awesome. Hadn't used it in such depth, and I was amazed at how much it can do for you. Like how quickly you can fire up an app and how it allows you to just focus on coding. 
* Setup was a bit of a pain on Windows though, but we made it nevertheless.
* Great job to whoever designed the assignment, it was very clever and fun. Loved the little challenges that you face along the way, truly a unique experience. 
* I hope I covered whatever was needed. No **UPDATE** or **DELETE** operations were added due to time and since Laravel makes it simple enough to add. 
* Excuse the one commit to the master branch type of uploading, I thought I'd just send it as a zip. 

---
