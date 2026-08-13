# Filipino Cookbook API

## 1. API Title

Filipino Cookbook API

## 2. API Description

The Filipino Cookbook API is a RESTful web service that provides structured information about traditional Filipino dishes, including their categories, regional origins, and ingredients.

- **Purpose:** Serve as a searchable, structured data source for Filipino recipes for use in web or mobile client applications.
- **Type of information provided:** Foods, food categories, regional origins, ingredients, and the relationships between them (e.g., which ingredients belong to which dish).
- **Intended users:** Developers building client or driver applications (web or mobile) that need Filipino food data, as part of the Collaborative API Development and Integration Activity.
- **Main functions:** Retrieve all foods, retrieve a single food by ID, search foods by name, retrieve categories, retrieve ingredients, add a new food, retrieve a random food, and retrieve foods filtered by category or origin.
- **Technologies used:** PHP, Slim Framework 4, MySQL, Composer, JSON, Apache (via XAMPP).

## 3. Features

- Retrieve all Filipino foods with their category, origin, and ingredients
- Retrieve the full details of a specific food by ID
- Search for foods by name (partial match)
- Retrieve all food categories
- Retrieve all food origins
- Retrieve all ingredients
- Add a new food entry (with ingredients)
- Retrieve a randomly selected Filipino food
- Retrieve all foods belonging to a specific category
- Retrieve all foods belonging to a specific origin/region
- Authenticate requests using a Bearer token
- Return information in JSON format

## 4. Technologies Used

- PHP 8+
- Slim Framework 4 (`slim/slim`, `slim/psr7`)
- MySQL (PDO)
- Composer
- JSON
- Apache / XAMPP
- Thunder Client or Postman (for testing)
- Git
- GitHub

## 5. Installation Instructions

Follow these steps to clone the repository, install dependencies, create and import the database, configure the database connection, start the local server, run the API, and test the endpoints.

1. **Clone the repository** into your XAMPP `htdocs` folder:
   ```
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
   Then edit `config.php` with your own values:
   ```php
   $dbHost = "localhost";
   $dbName = "filipino_cookbook_api";
   $dbUser = "YOUR_DATABASE_USERNAME";
   $dbPass = "YOUR_DATABASE_PASSWORD";
   $apiToken = "YOUR_API_TOKEN";
   ```
   > **Note:** `config.php` is listed in `.gitignore` and will not be committed.

4. **Import the database** (see Section 6 below).

5. **Start Apache and MySQL** from the XAMPP Control Panel.

6. **Test the API** using the base URL in Section 7, with a tool like Postman or Thunder Client.

## 6. Database Setup

- **Database name:** `filipino_cookbook_api`
- **SQL file:** `database/filipino_cookbook_api.sql`

### Import Instructions

1. Open phpMyAdmin at `http://localhost/phpmyadmin`.
2. Click **Import**.
3. Select `database/filipino_cookbook_api.sql`.
4. Click **Go**. This will create the database and all tables automatically (the script includes `CREATE DATABASE`).

### Tables and Relationships

```
categories -> foods <- origins
foods -> food_ingredients <- ingredients
```

| Table | Description |
|---|---|
| `categories` | Stores food categories (e.g., Main Dish, Soup, Dessert) |
| `origins` | Stores regional origins (e.g., Philippines, Bicol Region) |
| `foods` | Stores food entries with foreign keys to `categories` and `origins` |
| `ingredients` | Stores individual ingredients |
| `food_ingredients` | Junction table for the many-to-many relationship between `foods` and `ingredients` |

- `categories` and `origins` each have a **one-to-many** relationship with `foods`.
- `foods` and `ingredients` have a **many-to-many** relationship, resolved through the `food_ingredients` junction table.

## 7. Base URL

```
http://localhost/filipino-cookbook-api-alvarado/public/api
```

The public welcome route (no token required) is available at:

```
http://localhost/filipino-cookbook-api-alvarado/public/
```

## 8. Authentication Instructions

All routes under `/api` require a **Bearer token** in the `Authorization` header.

The token value is set in `config.php` (the `$apiToken` variable) and is **not** committed to the repository.

### Required Header

```
Authorization: Bearer YOUR_ACCESS_TOKEN
```

### Expected Response When Authentication is Missing or Invalid

If the `Authorization` header is missing or the token is incorrect, the API returns a **401 Unauthorized** response:

```json
{
    "status": "error",
    "message": "Unauthorized access. Valid API token is required."
}
```

## 9. Endpoint Documentation

---

### GET /api/foods

**Description:** Returns all Filipino foods stored in the database with their category, origin, and ingredients.

**Required headers:**
```
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
```

**Example request:**
```
GET http://localhost/filipino-cookbook-api-alvarado/public/api/foods
```

**Example successful response:**
```json
[
    {
        "food_id": 1,
        "food_name": "Adobo",
        "category_name": "Main Dish",
        "origin_name": "Philippines",
        "instructions": "Marinate the meat with soy sauce, vinegar, garlic, bay leaves, and peppercorn. Simmer until the meat becomes tender and the sauce is reduced.",
        "ingredients": ["Bay leaves", "Chicken or pork", "Cooking oil", "Garlic", "Peppercorn", "Soy sauce", "Vinegar"]
    }
]
```

**Example error response:**
```json
{
    "status": "error",
    "message": "Unauthorized access. Valid API token is required."
}
```

---

### GET /api/foods/{id}

**Description:** Returns the full details of a single food by its ID, including category, origin, and ingredients.

**Required headers:**
```
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
```

**Example request:**
```
GET http://localhost/filipino-cookbook-api-alvarado/public/api/foods/1
```

**Example successful response:**
```json
{
    "food_id": 1,
    "food_name": "Adobo",
    "category_name": "Main Dish",
    "origin_name": "Philippines",
    "instructions": "Marinate the meat with soy sauce, vinegar, garlic, bay leaves, and peppercorn. Simmer until the meat becomes tender and the sauce is reduced.",
    "ingredients": ["Bay leaves", "Chicken or pork", "Cooking oil", "Garlic", "Peppercorn", "Soy sauce", "Vinegar"]
}
```

**Example error response (404):**
```json
{
    "status": "error",
    "message": "Food not found"
}
```

---

### GET /api/foods/search/{name}

**Description:** Searches for foods by partial name match. Returns all foods whose name contains the search term.

**Required headers:**
```
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
```

**Example request:**
```
GET http://localhost/filipino-cookbook-api-alvarado/public/api/foods/search/adobo
```

**Example successful response:**
```json
[
    {
        "food_id": 1,
        "food_name": "Adobo",
        "category_name": "Main Dish",
        "origin_name": "Philippines",
        "instructions": "Marinate the meat with soy sauce, vinegar, garlic, bay leaves, and peppercorn. Simmer until the meat becomes tender and the sauce is reduced.",
        "ingredients": ["Bay leaves", "Chicken or pork", "Cooking oil", "Garlic", "Peppercorn", "Soy sauce", "Vinegar"]
    }
]
```

**Example error response:**
```json
{
    "status": "error",
    "message": "Unauthorized access. Valid API token is required."
}
```

---

### GET /api/categories

**Description:** Returns all food categories.

**Required headers:**
```
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
```

**Example request:**
```
GET http://localhost/filipino-cookbook-api-alvarado/public/api/categories
```

**Example successful response:**
```json
[
    { "category_id": 1, "category_name": "Appetizer" },
    { "category_id": 2, "category_name": "Dessert" },
    { "category_id": 3, "category_name": "Grilled Dish" },
    { "category_id": 4, "category_name": "Main Dish" },
    { "category_id": 5, "category_name": "Noodle Dish" },
    { "category_id": 6, "category_name": "Soup" },
    { "category_id": 7, "category_name": "Vegetable Dish" }
]
```

**Example error response:**
```json
{
    "status": "error",
    "message": "Unauthorized access. Valid API token is required."
}
```

---

### GET /api/ingredients

**Description:** Returns all ingredients.

**Required headers:**
```
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
```

**Example request:**
```
GET http://localhost/filipino-cookbook-api-alvarado/public/api/ingredients
```

**Example successful response:**
```json
[
    { "ingredient_id": 1, "ingredient_name": "Annatto oil" },
    { "ingredient_id": 2, "ingredient_name": "Bagoong" },
    { "ingredient_id": 3, "ingredient_name": "Banana blossom" }
]
```

**Example error response:**
```json
{
    "status": "error",
    "message": "Unauthorized access. Valid API token is required."
}
```

---

### POST /api/foods

**Description:** Adds a new food entry to the database, including its ingredient relationships.

**Required headers:**
```
Authorization: Bearer YOUR_ACCESS_TOKEN
Content-Type: application/json
```

**Example request:**
```
POST http://localhost/filipino-cookbook-api-alvarado/public/api/foods
```

**Request body (JSON):**
```json
{
    "food_name": "Sinigang na Baboy",
    "category_id": 6,
    "origin_id": 4,
    "instructions": "Boil pork with tomatoes, add tamarind mix and vegetables.",
    "ingredient_ids": [1, 4, 7]
}
```

**Example successful response (201):**
```json
{
    "status": "success",
    "message": "Food added successfully."
}
```

**Example error response (400):**
```json
{
    "status": "error",
    "message": "Missing required fields: food_name, category_id, origin_id, instructions, ingredient_ids"
}
```

---

### GET /api/foods/random

**Description:** Returns one randomly selected food with full details and ingredients. *(Optional Enhancement)*

**Required headers:**
```
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
```

**Example request:**
```
GET http://localhost/filipino-cookbook-api-alvarado/public/api/foods/random
```

**Example successful response:**
```json
{
    "food_id": 5,
    "food_name": "Bicol Express",
    "category_name": "Main Dish",
    "origin_name": "Bicol Region",
    "instructions": "Saute garlic and onion. Add pork, shrimp paste, coconut milk, and chili peppers. Simmer until the sauce thickens.",
    "ingredients": ["Chili peppers", "Coconut milk", "Garlic", "Onion", "Pork", "Shrimp paste"]
}
```

**Example error response (404):**
```json
{
    "status": "error",
    "message": "No foods available."
}
```

---

### GET /api/categories/{id}/foods

**Description:** Returns all foods belonging to a specific category. Validates that the category exists first. *(Optional Enhancement)*

**Required headers:**
```
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
```

**Example request:**
```
GET http://localhost/filipino-cookbook-api-alvarado/public/api/categories/4/foods
```

**Example successful response:**
```json
[
    {
        "food_id": 1,
        "food_name": "Adobo",
        "category_name": "Main Dish",
        "origin_name": "Philippines",
        "instructions": "Marinate the meat with soy sauce, vinegar, garlic, bay leaves, and peppercorn. Simmer until the meat becomes tender and the sauce is reduced.",
        "ingredients": ["Bay leaves", "Chicken or pork", "Cooking oil", "Garlic", "Peppercorn", "Soy sauce", "Vinegar"]
    }
]
```

**Example error response (400):**
```json
{
    "status": "error",
    "message": "Invalid category id. Must be a positive integer."
}
```

**Example error response (404):**
```json
{
    "status": "error",
    "message": "Category not found."
}
```

---

### GET /api/origins/{id}/foods

**Description:** Returns all foods belonging to a specific origin/region. Validates that the origin exists first. *(Optional Enhancement)*

**Required headers:**
```
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json
```

**Example request:**
```
GET http://localhost/filipino-cookbook-api-alvarado/public/api/origins/2/foods
```

**Example successful response:**
```json
[
    {
        "food_id": 5,
        "food_name": "Bicol Express",
        "category_name": "Main Dish",
        "origin_name": "Bicol Region",
        "instructions": "Saute garlic and onion. Add pork, shrimp paste, coconut milk, and chili peppers. Simmer until the sauce thickens.",
        "ingredients": ["Chili peppers", "Coconut milk", "Garlic", "Onion", "Pork", "Shrimp paste"]
    },
    {
        "food_id": 7,
        "food_name": "Laing",
        "category_name": "Vegetable Dish",
        "origin_name": "Bicol Region",
        "instructions": "Cook dried taro leaves in coconut milk with garlic, onion, ginger, chili, and shrimp paste until creamy.",
        "ingredients": ["Chili peppers", "Coconut cream", "Coconut milk", "Dried taro leaves", "Garlic", "Ginger", "Onion", "Shrimp paste"]
    }
]
```

**Example error response (400):**
```json
{
    "status": "error",
    "message": "Invalid origin id. Must be a positive integer."
}
```

**Example error response (404):**
```json
{
    "status": "error",
    "message": "Origin not found."
}
```

## 10. HTTP Status Codes

| Status Code | Meaning |
|---|---|
| 200 | Request completed successfully |
| 201 | Resource created successfully |
| 400 | Invalid request or parameter |
| 401 | Missing or invalid authentication |
| 404 | Requested resource was not found |
| 500 | Internal server error |

## 11. Testing Evidence

Screenshots of successful endpoint requests and JSON responses, invalid or missing token requests, resource-not-found responses, and optional enhancements.

| Endpoint | Description | Screenshot |
|---|---|---|
| GET /api/foods | Retrieve all foods | ![GET all foods](testing%20screenshots/GET%20all%20foods.png) |
| GET /api/foods/1 | Retrieve a specific food by ID | ![GET food by ID](testing%20screenshots/GET%20food%20by%20ID.png) |
| GET /api/foods/999 | Food not found (404) | ![GET food not found](testing%20screenshots/GET%20food%20not%20found.png) |
| GET /api/foods (no token) | Missing token (401) | ![Missing token](testing%20screenshots/Missing%20token.png) |
| GET /api/foods/search/adobo | Search food by name | ![Search food by name](testing%20screenshots/Search%20food%20by%20name.png) |
| GET /api/categories | Retrieve all categories | ![GET all categories](testing%20screenshots/GET%20all%20categories.png) |
| GET /api/ingredients | Retrieve all ingredients | ![GET all ingredients](testing%20screenshots/GET%20all%20ingredients.png) |
| POST /api/foods | Add a new food (201) | ![POST add new food](testing%20screenshots/POST%20add%20new%20food.png) |
| POST /api/foods (missing fields) | Validation error (400) | ![POST missing fields](testing%20screenshots/POST%20missing%20fields.png) |
| GET /api/foods/random | Random food (optional) | ![GET random food](testing%20screenshots/GET%20random%20food.png) |
| GET /api/categories/4/foods | Foods by category (optional) | ![GET foods by category](testing%20screenshots/GET%20foods%20by%20category.png) |
| GET /api/categories/999/foods | Category not found (optional, 404) | ![Category not found](testing%20screenshots/Category%20not%20found.png) |
| GET /api/origins/2/foods | Foods by origin (optional) | ![GET foods by origin](testing%20screenshots/GET%20foods%20by%20origin.png) |
| GET /api/origins/999/foods | Origin not found (optional, 404) | ![Origin not found](testing%20screenshots/Origin%20not%20found.png) |

## 12. Developer Information

- **Student name:** Alvarado
- **Course and section:** BSIT 4A
- **GitHub username:** Burtzs
- **Repository link:** https://github.com/Burtzs/filipino-cookbook-api-alvarado
- **Date completed:** August 13, 2026
