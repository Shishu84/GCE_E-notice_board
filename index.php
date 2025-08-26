<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GCE E-Notice Board</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>


        <header class="bg-primary text-white text-center p-4 shadow-sm">
    
       <div class="container d-flex justify-content-center align-items-center position-relative">
    <div>
        <h1>GCE E-Notice Board</h1>
        <p class="lead mb-0">Your one-stop destination for all official announcements</p>
        <div class="form-check form-switch position-absolute end-0">
            <input class="form-check-input" type="checkbox" role="switch" id="darkModeToggle">
            <label class="form-check-label" for="darkModeToggle"><i class="bi bi-moon-stars-fill"></i></label>
        </div>
        <div id="realtime-clock" class="mt-2"></div>
    </div>
    </div>
    </header>

    <div class="container mt-5">
        <div class="card p-3 mb-4 shadow-sm controls-card">
            <div class="row g-3 align-items-center">
                <div class="col-lg-6">
                    <div id="category-filters" class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-primary active" data-category="all">All</button>
                        </div>
                </div>
                <div class="col-lg-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="search-input" class="form-control" placeholder="Search notices...">
                    </div>
                </div>
                <div class="col-lg-3">
                    <select id="sort-select" class="form-select">
                        <option value="latest">Sort by: Latest</option>
                        <option value="oldest">Sort by: Oldest</option>
                    </select>
                </div>
            </div>
        </div>

        <div id="notice-container" class="row">
            
            <div class="text-center p-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
        <nav class="mt-5">
            <ul id="pagination-container" class="pagination justify-content-center">
                </ul>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>