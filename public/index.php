<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';

// ============================================================================
// Database Connection (PDO)
// ============================================================================

$host = 'localhost';
$dbname = 'filipino_cookbook_api';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $e->getMessage()
    ]));
}

// ============================================================================
// Create Slim App
// ============================================================================

$app = AppFactory::create();

// Set base path for XAMPP subdirectory deployment
$app->setBasePath((function () {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $basePath = dirname($scriptName);
    if ($basePath === '\\' || $basePath === '/') {
        return '';
    }
    return $basePath;
})());

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// ============================================================================
// Helper: JSON Response
// ============================================================================

function jsonResponse(Response $response, $data, int $statusCode = 200): Response
{
    $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($statusCode);
}

// ============================================================================
// Token-Based Security Middleware
// ============================================================================

$API_TOKEN = 'dmmmsu-cookbook-token-2026';

$tokenMiddleware = function (Request $request, $handler) use ($API_TOKEN) {
    $authHeader = $request->getHeaderLine('Authorization');

    // Check if Authorization header is present and has correct Bearer token
    if (empty($authHeader) || $authHeader !== 'Bearer ' . $API_TOKEN) {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => 'Unauthorized access. Valid API token is required.'
        ], JSON_PRETTY_PRINT));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(401);
    }

    return $handler->handle($request);
};

// ============================================================================
// 1. Public Welcome Route (No token required)
// ============================================================================

$app->get('/', function (Request $request, Response $response) {
    return jsonResponse($response, [
        'message' => 'Welcome to the Secured Filipino Cookbook API',
        'note' => 'Use a valid Bearer token to access /api endpoints.'
    ]);
});

// ============================================================================
// Secured API Routes (Token required)
// ============================================================================

$app->group('/api', function (RouteCollectorProxy $group) use ($pdo) {

    // ========================================================================
    // 2. Get All Foods
    // GET /api/foods
    // ========================================================================

    $group->get('/foods', function (Request $request, Response $response) use ($pdo) {
        // Get all foods with category and origin names
        $stmt = $pdo->query("
            SELECT f.food_id, f.food_name, c.category_name, o.origin_name, f.instructions
            FROM foods f
            JOIN categories c ON f.category_id = c.category_id
            JOIN origins o ON f.origin_id = o.origin_id
            ORDER BY f.food_id
        ");
        $foods = $stmt->fetchAll();

        // Get all food_ingredients with ingredient names
        $ingredientStmt = $pdo->query("
            SELECT fi.food_id, i.ingredient_name
            FROM food_ingredients fi
            JOIN ingredients i ON fi.ingredient_id = i.ingredient_id
            ORDER BY i.ingredient_name
        ");
        $allIngredients = $ingredientStmt->fetchAll();

        // Group ingredients by food_id
        $ingredientsByFood = [];
        foreach ($allIngredients as $row) {
            $ingredientsByFood[$row['food_id']][] = $row['ingredient_name'];
        }

        // Attach ingredients to each food
        foreach ($foods as &$food) {
            $food['food_id'] = (int)$food['food_id'];
            $food['ingredients'] = $ingredientsByFood[$food['food_id']] ?? [];
        }

        return jsonResponse($response, $foods);
    });

    // ========================================================================
    // 3. Get Food by ID
    // GET /api/foods/{id}
    // ========================================================================

    $group->get('/foods/{id}', function (Request $request, Response $response, array $args) use ($pdo) {
        $foodId = (int)$args['id'];

        // Get food with category and origin
        $stmt = $pdo->prepare("
            SELECT f.food_id, f.food_name, c.category_name, o.origin_name, f.instructions
            FROM foods f
            JOIN categories c ON f.category_id = c.category_id
            JOIN origins o ON f.origin_id = o.origin_id
            WHERE f.food_id = :id
        ");
        $stmt->execute(['id' => $foodId]);
        $food = $stmt->fetch();

        if (!$food) {
            return jsonResponse($response, [
                'status' => 'error',
                'message' => 'Food not found'
            ], 404);
        }

        // Get ingredients for this food
        $ingredientStmt = $pdo->prepare("
            SELECT i.ingredient_name
            FROM food_ingredients fi
            JOIN ingredients i ON fi.ingredient_id = i.ingredient_id
            WHERE fi.food_id = :id
            ORDER BY i.ingredient_name
        ");
        $ingredientStmt->execute(['id' => $foodId]);
        $ingredients = $ingredientStmt->fetchAll(PDO::FETCH_COLUMN);

        $food['food_id'] = (int)$food['food_id'];
        $food['ingredients'] = $ingredients;

        return jsonResponse($response, $food);
    });

    // ========================================================================
    // 4. Search Food by Name
    // GET /api/foods/search/{name}
    // ========================================================================

    $group->get('/foods/search/{name}', function (Request $request, Response $response, array $args) use ($pdo) {
        $searchName = $args['name'];

        // Search foods by name (partial match)
        $stmt = $pdo->prepare("
            SELECT f.food_id, f.food_name, c.category_name, o.origin_name, f.instructions
            FROM foods f
            JOIN categories c ON f.category_id = c.category_id
            JOIN origins o ON f.origin_id = o.origin_id
            WHERE f.food_name LIKE :name
            ORDER BY f.food_id
        ");
        $stmt->execute(['name' => '%' . $searchName . '%']);
        $foods = $stmt->fetchAll();

        // Get ingredients for matched foods
        foreach ($foods as &$food) {
            $food['food_id'] = (int)$food['food_id'];

            $ingredientStmt = $pdo->prepare("
                SELECT i.ingredient_name
                FROM food_ingredients fi
                JOIN ingredients i ON fi.ingredient_id = i.ingredient_id
                WHERE fi.food_id = :id
                ORDER BY i.ingredient_name
            ");
            $ingredientStmt->execute(['id' => $food['food_id']]);
            $food['ingredients'] = $ingredientStmt->fetchAll(PDO::FETCH_COLUMN);
        }

        return jsonResponse($response, $foods);
    });

    // ========================================================================
    // 5. Get All Categories
    // GET /api/categories
    // ========================================================================

    $group->get('/categories', function (Request $request, Response $response) use ($pdo) {
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY category_id");
        $categories = $stmt->fetchAll();

        // Cast IDs to int
        foreach ($categories as &$cat) {
            $cat['category_id'] = (int)$cat['category_id'];
        }

        return jsonResponse($response, $categories);
    });

    // ========================================================================
    // 6. Get All Ingredients
    // GET /api/ingredients
    // ========================================================================

    $group->get('/ingredients', function (Request $request, Response $response) use ($pdo) {
        $stmt = $pdo->query("SELECT * FROM ingredients ORDER BY ingredient_id");
        $ingredients = $stmt->fetchAll();

        // Cast IDs to int
        foreach ($ingredients as &$ing) {
            $ing['ingredient_id'] = (int)$ing['ingredient_id'];
        }

        return jsonResponse($response, $ingredients);
    });

    // ========================================================================
    // 7. Add New Food
    // POST /api/foods
    // ========================================================================

    $group->post('/foods', function (Request $request, Response $response) use ($pdo) {
        $data = $request->getParsedBody();

        // Validate required fields
        if (
            empty($data['food_name']) ||
            empty($data['category_id']) ||
            empty($data['origin_id']) ||
            empty($data['instructions']) ||
            !isset($data['ingredient_ids']) ||
            !is_array($data['ingredient_ids'])
        ) {
            return jsonResponse($response, [
                'status' => 'error',
                'message' => 'Missing required fields: food_name, category_id, origin_id, instructions, ingredient_ids'
            ], 400);
        }

        try {
            $pdo->beginTransaction();

            // Get the next food_id
            $maxStmt = $pdo->query("SELECT COALESCE(MAX(food_id), 0) + 1 AS next_id FROM foods");
            $nextId = (int)$maxStmt->fetch()['next_id'];

            // Insert the food
            $stmt = $pdo->prepare("
                INSERT INTO foods (food_id, food_name, category_id, origin_id, instructions)
                VALUES (:food_id, :food_name, :category_id, :origin_id, :instructions)
            ");
            $stmt->execute([
                'food_id' => $nextId,
                'food_name' => $data['food_name'],
                'category_id' => (int)$data['category_id'],
                'origin_id' => (int)$data['origin_id'],
                'instructions' => $data['instructions']
            ]);

            // Insert food_ingredients relationships
            $ingredientStmt = $pdo->prepare("
                INSERT INTO food_ingredients (food_id, ingredient_id)
                VALUES (:food_id, :ingredient_id)
            ");
            foreach ($data['ingredient_ids'] as $ingredientId) {
                $ingredientStmt->execute([
                    'food_id' => $nextId,
                    'ingredient_id' => (int)$ingredientId
                ]);
            }

            $pdo->commit();

            return jsonResponse($response, [
                'status' => 'success',
                'message' => 'Food added successfully.'
            ], 201);

        } catch (PDOException $e) {
            $pdo->rollBack();
            return jsonResponse($response, [
                'status' => 'error',
                'message' => 'Failed to add food: ' . $e->getMessage()
            ], 500);
        }
    });

})->add($tokenMiddleware);

// ============================================================================
// Run the Application
// ============================================================================

$app->run();