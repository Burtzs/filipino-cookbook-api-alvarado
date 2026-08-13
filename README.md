# Filipino Cookbook API

## 1. API Description

The Filipino Cookbook API is a RESTful web service that provides structured information about traditional Filipino dishes, including their categories, regional origins, and ingredients.

- **Purpose:** Serve as a searchable, structured data source for Filipino recipes for use in web or mobile client applications.
- **Type of information provided:** Foods, food categories, regional origins, ingredients, and the relationships between them (e.g. which ingredients belong to which dish).
- **Intended users:** Developers building client/driver applications (web or mobile) that need Filipino food data, as part of the Collaborative API Development and Integration Activity.
- **Main functions:** Retrieve all foods, retrieve a single food by ID, search foods by name, retrieve categories, retrieve ingredients, add a new food, retrieve a random food, and retrieve foods filtered by category or origin.
- **Technologies used:** PHP, Slim Framework 4, MySQL, Composer, JSON, Apache (via XAMPP).

## 2. Features

- Retrieve all Filipino foods with their category, origin, and ingredients
- Retrieve the full details of a specific food by ID
- Search for foods by name (partial match)
- Retrieve all food categories
- Retrieve all ingredients
- Add a new food entry (with ingredients)
- Retrieve a randomly selected Filipino food
- Retrieve all foods belonging to a specific category
- Retrieve all foods belonging to a specific origin/region
- Token-based authentication on all `/api` routes
- JSON-formatted responses with consistent status/error structure

## 3. Technologies Used

- PHP 8+
- Slim Framework 4 (`slim/slim`, `slim/psr7`)
- MySQL (PDO)
- Composer
- JSON
- Apache / XAMPP
- Postman or Thunder Client (for testing)
- Git & GitHub

## 4. Installation Instructions

1. **Clone the repository** into your XAMPP `htdocs` folder:
   ```
   cd C:\xampp\htdocs
   git clone https://github.com/Burtzs/filipino-cookbook-api-alvarado.git
   cd filipino-cookbook-api-alvarado
   ```

2. **Install dependencies** with Composer:
   ```
   composer install
   ```

3. **Create your local configuration file.** Copy the example file and fill in your actual local database credentials and API token:
   ```
   copy config.example.php config.php
   ```
   Then edit `config.php`:
   ```php
   $dbHost = "localhost";
   $dbName = "filipino_cookbook_api";
   $dbUser = "YOUR_DATABASE_USERNAME";
   $dbPass = "YOUR_DATABASE_PASSWORD";
   $apiToken = "YOUR_API_TOKEN";
   ```
   `config.php` is listed in `.gitignore` and will never be committed.

4. **Import the database** (see Section 5 below).

5. **Start Apache and MySQL** from the XAMPP Control Panel.

6. **Test the API** using the base URL in Section 6, with a tool like Postman or Thunder Client.

## 5. Database Setup

- **Database name:** `filipino_cookbook_api`
- **SQL file:** `database/filipino_cookbook_api.sql`

**Import steps:**
1. Open phpMyAdmin at `http://localhost/phpmyadmin`.
2. Click **Import**.
3. Select `database/filipino_cookbook_api.sql`.
4. Click **Go**. This will create the database and all tables automatically (the script includes `CREATE DATABASE`).

**Tables and relationships:**
```
categories --< foods >-- origins
foods --< food_ingredients >-- ingredients
```
- `categories` and `origins` each have a one-to-many relationship with `foods`.
- `foods` and `ingredients` have a many-to-many relationship, resolved through the `food_ingredients` junction table.

## 6. Base URL

```
http://localhost/filipino-cookbook-api-alvarado/public/api
```

The public welcome route (no token required) is available at:
```
http://localhost/filipino-cookbook-api-alvarado/public/
```

## 7. Authentication Instructions

All routes under `/api` require a Bearer token in the `Authorization` header.

```
Authorization: Bearer YOUR_API_TOKEN
```

- The token value is set in `config.php` (`$apiToken`) and is **not** committed to the repository.
- If the header is missing or the token is incorrect, the API returns a `401 Unauthorized` response:
  ```json
  {
      "status": "error",
      "message": "Unauthorized access. Valid API token is required."
  }
  ```

## 8. Endpoint Documentation

### GET /api/foods
Returns all Filipino foods with category, origin, and ingredients.

**Headers**
```
Authorization: Bearer YOUR_API_TOKEN
```

**Example request**
```
GET http://localhost/filipino-cookbook-api-alvarado/public/api/foods
```

**Example success response**
```json
[
    {
        "food_id": 1,
        "food_name": "Adobo",
        "category_name": "Main Dish",
        "origin_name": "Philippines",
        "instructions": "Marinate the meat with soy sauce, vinegar, garlic, bay leaves, and peppercorn...",
        "ingredients": ["Garlic", "Soy Sauce", "Vinegar"]
    }
]
```

---

### GET /api/foods/{id}
Returns the full details of a single food by ID.

**Example request**
```
GET http://localhost/filipino-cookbook-api-alvarado/public/api/foods/1
```

**Example error response (404)**
```json
{
    "status": "error",
    "message": "Food not found"
}
```

---

### GET /api/foods/search/{name}
Searches foods by partial name match.

**Example request**
```
GET http://localhost/filipino-cookbook-api-alvarado/public/api/foods/search/adobo
```

---

### GET /api/categories
Returns all food categories.

**Example request**
```
GET http://localhost/filipino-cookbook-api-alvarado/public/api/categories
```

---

### GET /api/ingredients
Returns all ingredients.

**Example request**
```
GET http://localhost/filipino-cookbook-api-alvarado/public/api/ingredients
```

---

### POST /api/foods
Adds a new food entry.

**Body (JSON)**
```json
{
    "food_name": "Sinigang na Baboy",
    "category_id": 6,
    "origin_id": 4,
    "instructions": "Boil pork with tomatoes, add tamarind mix and vegetables.",
    "ingredient_ids": [1, 4, 7]
}
```

**Example success response (201)**
```json
{
    "status": "success",
    "message": "Food added successfully."
}
```

**Example error response (400)**
```json
{
    "status": "error",
    "message": "Missing required fields: food_name, category_id, origin_id, instructions, ingredient_ids"
}
```

---

### GET /api/foods/random *(Optional Enhancement)*
Returns one randomly selected food with full details.

**Example request**
```
GET http://localhost/filipino-cookbook-api-alvarado/public/api/foods/random
```

---

### GET /api/categories/{id}/foods *(Optional Enhancement)*
Returns all foods belonging to a specific category.

**Example request**
```
GET http://localhost/filipino-cookbook-api-alvarado/public/api/categories/4/foods
```

**Example error response (404)**
```json
{
    "status": "error",
    "message": "Category not found."
}
```

---

### GET /api/origins/{id}/foods *(Optional Enhancement)*
Returns all foods belonging to a specific origin/region.

**Example request**
```
GET http://localhost/filipino-cookbook-api-alvarado/public/api/origins/4/foods
```

## 9. HTTP Status Codes

| Status Code | Meaning |
|---|---|
| 200 | Request completed successfully |
| 201 | Resource created successfully |
| 400 | Invalid request or parameter |
| 401 | Missing or invalid authentication |
| 404 | Requested resource was not found |
| 500 | Internal server error |

## 10. Testing Evidence

_Add screenshots here showing successful requests, invalid/missing token responses, and not-found responses for each endpoint (Postman or Thunder Client)._

| Endpoint | Screenshot |
|---|---|
| GET /api/foods | _(add screenshot)_ |
| GET /api/foods/{id} — not found | _(add screenshot)_ |
| GET /api/foods without token — 401 | _(add screenshot)_ |
| GET /api/foods/random | _(add screenshot)_ |
| GET /api/categories/{id}/foods | _(add screenshot)_ |
| GET /api/origins/{id}/foods | _(add screenshot)_ |

## 11. Optional API Enhancements

**Enhancement type:** Option A (new endpoints) + secure configuration handling

### New Endpoints Added

| Endpoint | Description |
|---|---|
| `GET /api/foods/random` | Returns a randomly selected Filipino food with its full details and ingredients. |
| `GET /api/categories/{id}/foods` | Returns all foods that belong to a given category, validating that the category exists first. |
| `GET /api/origins/{id}/foods` | Returns all foods that belong to a given origin/region, validating that the origin exists first. |

**Purpose:** These endpoints make it easier for client applications to browse and filter the food catalog without having to fetch and filter the entire dataset on the client side.

**Files modified:**
- `public/index.php` — added the three new route handlers, validation, and prepared statements.

### Security Enhancements

- **Environment/config-based credentials:** Database credentials and the API token were moved out of `public/index.php` and into `config.php`, which is excluded from version control via `.gitignore`. A `config.example.php` with placeholder values is provided instead.
- **Secure error handling:** Raw PDO exception messages are no longer returned to the client. Database connection failures and insert failures now return a generic message while the real error is logged server-side with `error_log()`.
- **Input validation:** The new `{id}` parameters on `/api/categories/{id}/foods` and `/api/origins/{id}/foods` are validated as positive integers (`ctype_digit`) and checked for existence before querying, returning `400`/`404` as appropriate.

**Files modified:**
- `public/index.php`
- `config.example.php` *(new)*
- `.gitignore` *(new)*

**Instructions for testing:**
1. Request `GET /api/foods/random` multiple times with a valid token and confirm different foods are returned.
2. Request `GET /api/categories/4/foods` with a valid token and confirm only Main Dish items are returned.
3. Request `GET /api/categories/999/foods` and confirm a `404` "Category not found." response.
4. Request `GET /api/origins/4/foods` with a valid token and confirm only foods from that origin are returned.
5. Attempt to connect with an intentionally wrong database password in `config.php` and confirm the response no longer exposes the database driver's raw error message.

**Screenshots of successful testing:** _(add screenshots here)_

## 12. Developer Information

- **Student name:** Alvarado
- **Course and section:** BSIT 4A
- **GitHub username:** Burtzs
- **Repository link:** https://github.com/Burtzs/filipino-cookbook-api-alvarado
- **Date completed:** August 13, 2026
