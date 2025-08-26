<?php
include '../includes/db_connect.php';

// --- Settings ---
$results_per_page = 5; // Show 5 notices per page

// --- Get Categories (no change here) ---
$category_sql = "SELECT id, name FROM categories ORDER BY name ASC";
$category_result = $conn->query($category_sql);
$categories = [];
while($row = $category_result->fetch_assoc()) {
    $categories[] = $row;
}

// --- Build WHERE clauses for filtering (same as before) ---
$where_clauses = [];
$params = [];
$types = '';

if (isset($_GET['category']) && $_GET['category'] != 'all' && is_numeric($_GET['category'])) {
    $where_clauses[] = "n.category_id = ?";
    $params[] = $_GET['category'];
    $types .= 'i';
}
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $where_clauses[] = "(n.title LIKE ? OR n.content LIKE ?)";
    $searchTerm = '%' . $_GET['search'] . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'ss';
}
$where_sql = !empty($where_clauses) ? " WHERE " . implode(" AND ", $where_clauses) : "";

// --- NEW: First, count the total number of matching records ---
$total_sql = "SELECT COUNT(*) as total FROM notices n" . $where_sql;
$stmt = $conn->prepare($total_sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$total_results = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_results / $results_per_page);


// --- NEW: Determine current page and calculate the starting LIMIT ---
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($current_page - 1) * $results_per_page;


// --- Build the main query with Sorting and NEW LIMIT clause ---
$sql = "SELECT n.id, n.title, n.content, n.attachment_path, n.created_at, c.name AS category_name
        FROM notices n JOIN categories c ON n.category_id = c.id" . $where_sql;

$sort_order = (isset($_GET['sort']) && $_GET['sort'] == 'oldest') ? 'ASC' : 'DESC';
$sql .= " ORDER BY n.created_at " . $sort_order;
$sql .= " LIMIT ?, ?"; // Add LIMIT for pagination

// Add LIMIT params to the existing params array
$params[] = $start_from;
$params[] = $results_per_page;
$types .= 'ii'; // Add two integers for LIMIT

// --- Execute Query and Fetch Notices ---
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$notices = [];
while($row = $result->fetch_assoc()) {
    $notices[] = $row;
}

// --- Final Output with NEW pagination data ---
$output = [
    'categories' => $categories,
    'notices' => $notices,
    'pagination' => [
        'total_pages' => $total_pages,
        'current_page' => $current_page
    ]
];

header('Content-Type: application/json');
echo json_encode($output);

$stmt->close();
$conn->close();
?>