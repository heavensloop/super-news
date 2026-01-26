# Super News

**Super News** is a Laravel-based news aggregation and publishing platform. It is designed to crawl external news sources, normalize their content, and present articles in a clean, searchable interface.

> Built with PHP and Laravel. Actively evolving.

---

## 🧠 Features

* 📰 Crawl and ingest news articles from external websites
* 🗂 Store articles with metadata (title, source, category, publish date)
* 🔍 Search and filter articles
* 🛠 Admin-ready structure for managing content
* ⚙️ Extensible crawler architecture

---

## 🚀 Tech Stack

| Technology              | Purpose                    |
| ----------------------- | -------------------------- |
| PHP 8+                  | Core language              |
| Laravel                 | Backend framework          |
| Composer                | Dependency management      |
| Blade                   | Templating engine          |
| MySQL / PostgreSQL      | Database                   |
| Laravel Sail (optional) | Dockerized dev environment |

---

## 📥 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/heavensloop/super-news.git
cd super-news
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database

Edit `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=super_news
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Serve the Application

```bash
php artisan serve
```

Visit: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 🧰 Project Commands

This project includes custom Artisan commands for importing news metadata and populating full article content.

You can view all available commands with:

```bash
php artisan list
```

---

## 1️⃣ Import News from External Sources

Fetches article listings from external news sources and stores them in the database.

```bash
php artisan app:news:import
```

### Description

This command:

* Builds a `NewsQuery` object using filters (category, date range, age, limit)
* Iterates through selected news sources
* Dispatches a `ProcessNewsImport` job per source
* Does **not** fetch full article content (that happens later via `app:news:crawl`)

### Arguments

| Argument   | Required | Description                    |
| ---------- | -------- | ------------------------------ |
| `category` | No       | Category of the news to import |

**Example**

```bash
php artisan app:news:import business
```

### Options

| Option      | Description                                   |
| ----------- | --------------------------------------------- |
| `--source=` | News source type (enum value)                 |
| `--age=`    | Import news published in the last N days      |
| `--from=`   | Import news published from this date (Y-m-d)  |
| `--to=`     | Import news published up to this date (Y-m-d) |
| `--limit=`  | Maximum number of news items to import        |

### Examples

Import all recent news:

```bash
php artisan app:news:import
```

Import technology news from the last 3 days:

```bash
php artisan app:news:import technology --age=3
```

Import news from a specific date range:

```bash
php artisan app:news:import --from=2026-01-01 --to=2026-01-15
```

Import from a specific source:

```bash
php artisan app:news:import --source=bbc
```

Limit imports to 20 articles:

```bash
php artisan app:news:import --limit=20
```

Combine all filters:

```bash
php artisan app:news:import sports --source=nytimes --from=2026-01-10 --to=2026-01-20 --limit=10
```

### How It Works (Under the Hood)

Internally, the command:

1. Reads user input:

   * Category via `getCategoryInput()`
   * Source(s) via `getNewsSource()`
   * Date filters via `--age`, `--from`, `--to`
   * Limit via `--limit`
2. Builds a `NewsQuery` object:

```php
$query = new NewsQuery($category);
```

3. Applies filters:

```php
$query->setPublishedFrom(...)
$query->setPublishedTo(...)
$query->setLimit(...)
```

4. Creates a source-specific crawler:

```php
$newsSource = $this->newsSourceFactory->create($source);
```

5. Dispatches a background job:

```php
dispatch(new ProcessNewsImport($query, $source));
```

---

## 2️⃣ Crawl & Populate Article Content

Fetches and parses the full content for articles already stored in the database.

```bash
php artisan app:news:crawl
```

### Description

This command:

* Selects articles with `content_status = PENDING`
* Optionally filters by category and/or source
* Optionally limits the number of articles processed
* Dispatches a `PopulateArticleContent` job per article

This separates **metadata import** from **content extraction**, making the system more reliable and scalable.

### Arguments

| Argument   | Required | Description                       |
| ---------- | -------- | --------------------------------- |
| `category` | No       | Category of news to import (enum) |

### Options

| Option      | Description                           |
| ----------- | ------------------------------------- |
| `--source=` | News source type (enum value)         |
| `--limit=`  | Maximum number of articles to process |

### Examples

Crawl all pending articles:

```bash
php artisan app:news:crawl
```

Crawl only sports articles:

```bash
php artisan app:news:crawl sports
```

Crawl from a specific source:

```bash
php artisan app:news:crawl --source=nytimes
```

Limit to 10 articles:

```bash
php artisan app:news:crawl --limit=10
```

Combine all filters:

```bash
php artisan app:news:crawl technology --source=bbc --limit=5
```

### How It Works (Under the Hood)

Internally, the command:

1. Reads user input:

   * Category via `getCategoryInput(false)`
   * Source(s) via `getNewsSource()`
   * Limit via `--limit`
2. Builds a query:

```php
Article::where('content_status', ContentStatus::PENDING)
```

3. Applies filters:

   * Category filter
   * Source filter
   * Limit
4. Dispatches a background job per article:

```php
dispatch(new PopulateArticleContent($article));
```

---

## ⚙️ Queue Worker (Required)

Both commands dispatch jobs and **require a running queue worker**:

```bash
php artisan queue:work
```

Recommended production settings:

```bash
php artisan queue:work --tries=3 --timeout=120
```

---

## 🛠 Adding More Commands

Custom commands live in:

```
app/Console/Commands/
```

If you add more (e.g., `news:refresh`, `news:sources`, `news:retry-failures`), document them here using the same format.

---

## Additional Notes

* Enums used in commands: `NewsCategory`, `NewsSource`, `ContentStatus`
* Trait `HasNewsParameters` standardizes category/source handling
* Typical workflow:

```bash
php artisan app:news:import
php artisan app:news:crawl
```
