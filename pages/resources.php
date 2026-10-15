<?php
require_once '../includes/config.php';
require_once '../includes/header.php';
require_once '../includes/db_functions.php';

try {
    $resources = get_all_resources();
    $recipe_cards = get_all_resources('Recipe Card');
    $tutorials = get_all_resources('Tutorial');
    $videos = get_all_resources('Video');
    $infographics = get_all_resources('Infographic');
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    $error = "Could not load resources. Please try again later.";
}

// Sample online resources (in a real app, these would come from your database)
$online_resources = [
    [
        'title' => 'Italian Pasta Guide',
        'description' => 'Complete guide to making authentic Italian pasta from scratch',
        'type' => 'Recipe Card',
        'image' => 'https://images.unsplash.com/photo-1555949258-eb67b1ef0ceb?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&h=600&q=80',
        'url' => '#',
        'file_url' => 'https://heartysol.net/wp-content/uploads/2025/01/FREE-Sourdough-Recipe-Book-1.pdf'
    ],
    [
        'title' => 'Knife Skills Tutorial',
        'description' => 'Master basic knife skills with this step-by-step visual guide',
        'type' => 'Tutorial',
        'image' => 'https://images.unsplash.com/photo-1606787366850-de6330128bfc?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&h=600&q=80',
        'url' => '#',
        'file_url' => 'https://heartysol.net/wp-content/uploads/2025/01/FREE-Sourdough-Recipe-Book-1.pdf'
    ],
     [
        'title' => 'Perfect Pasta Making Video',
        'description' => 'Learn how to make fresh homemade pasta from scratch with this step-by-step tutorial',
        'type' => 'Video',
        'image' => 'https://img.youtube.com/vi/G-Fg7l7G1zw/maxresdefault.jpg',
        'url' => 'https://www.youtube.com/embed/G-Fg7l7G1zw',
        'file_url' => 'https://heartysol.net/wp-content/uploads/2025/01/FREE-Sourdough-Recipe-Book-1.pdf'
    ],
    [
        'title' => 'Spice Pairing Chart',
        'description' => 'Infographic showing perfect spice pairings for different cuisines',
        'type' => 'Infographic',
        'image' => 'https://images.unsplash.com/photo-1606787366850-de6330128bfc?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&h=600&q=80',
        'url' => '#',
        'file_url' => 'https://heartysol.net/wp-content/uploads/2025/01/FREE-Sourdough-Recipe-Book-1.pdf'
    ],
    [
        'title' => 'Baking Conversions',
        'description' => 'Printable conversion chart for common baking measurements',
        'type' => 'Recipe Card',
        'image' => 'https://images.unsplash.com/photo-1606787366850-de6330128bfc?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&h=600&q=80',
        'url' => '#',
        'file_url' => 'https://heartysol.net/wp-content/uploads/2025/01/FREE-Sourdough-Recipe-Book-1.pdf'
    ],
    [
        'title' => 'Sourdough Masterclass',
        'description' => 'Complete video guide to making artisan sourdough bread',
        'type' => 'Video',
        'image' => 'https://img.youtube.com/vi/sTAiDki7AQA/maxresdefault.jpg',
        'url' => 'https://www.youtube.com/embed/sTAiDki7AQA',
        'file_url' => 'https://heartysol.net/wp-content/uploads/2025/01/FREE-Sourdough-Recipe-Book-1.pdf'
    ]
];
?>

<div class="container my-5">
    <h1 class="fw-bold mb-4">Culinary Resources</h1>
    <p class="lead mb-5">Enhance your cooking skills with our collection of downloadable resources, tutorials, and instructional materials.</p>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <ul class="nav nav-tabs mb-5" id="resourcesTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button">
                All Resources
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="cards-tab" data-bs-toggle="tab" data-bs-target="#cards" type="button">
                Recipe Cards
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tutorials-tab" data-bs-toggle="tab" data-bs-target="#tutorials" type="button">
                Tutorials
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="videos-tab" data-bs-toggle="tab" data-bs-target="#videos" type="button">
                Videos
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="infographics-tab" data-bs-toggle="tab" data-bs-target="#infographics" type="button">
                Infographics
            </button>
        </li>
    </ul>
    
    <div class="tab-content" id="resourcesTabContent">
        <div class="tab-pane fade show active" id="all" role="tabpanel">
            <div class="row g-4">
                <?php foreach ($online_resources as $resource): ?>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="card h-100 resource-card border-0 shadow-sm">
                            <?php if ($resource['type'] === 'Video'): ?>
                                <div class="ratio ratio-16x9">
                                    <iframe src="<?php echo $resource['url']; ?>" 
                                            frameborder="0" 
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                            allowfullscreen></iframe>
                                </div>
                            <?php else: ?>
                                <img src="<?php echo $resource['image']; ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($resource['title']); ?>"
                                     style="height: 180px; object-fit: cover;">
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <span class="badge bg-success mb-2"><?php echo $resource['type']; ?></span>
                                <h5 class="card-title"><?php echo htmlspecialchars($resource['title']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($resource['description']); ?></p>
                            </div>
                            <div class="card-footer bg-white border-0">
                                <?php if ($resource['type'] === 'Video'): ?>
                                    <a href="<?php echo $resource['url']; ?>" 
                                       class="btn btn-sm btn-success w-100" 
                                       target="_blank">
                                        <i class="fas fa-play me-2"></i>Watch Video
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo $resource['file_url']; ?>" 
                                       class="btn btn-sm btn-success w-100" 
                                       download>
                                        <i class="fas fa-download me-2"></i>Download
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="tab-pane fade" id="cards" role="tabpanel">
            <div class="row g-4">
                <?php foreach (array_filter($online_resources, fn($r) => $r['type'] === 'Recipe Card') as $resource): ?>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="card h-100 resource-card border-0 shadow-sm">
                            <img src="<?php echo $resource['image']; ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($resource['title']); ?>"
                                 style="height: 180px; object-fit: cover;">
                            <div class="card-body">
                                <span class="badge bg-success mb-2"><?php echo $resource['type']; ?></span>
                                <h5 class="card-title"><?php echo htmlspecialchars($resource['title']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($resource['description']); ?></p>
                            </div>
                            <div class="card-footer bg-white border-0">
                                 <a href="<?php echo $resource['file_url']; ?>" 
                                       class="btn btn-sm btn-success w-100" 
                                       download>
                                        <i class="fas fa-download me-2"></i>Download
                                    </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="tab-pane fade" id="tutorials" role="tabpanel">
            <div class="row g-4">
                <?php foreach (array_filter($online_resources, fn($r) => $r['type'] === 'Tutorial') as $resource): ?>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="card h-100 resource-card border-0 shadow-sm">
                            <img src="<?php echo $resource['image']; ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($resource['title']); ?>"
                                 style="height: 180px; object-fit: cover;">
                            <div class="card-body">
                                <span class="badge bg-success mb-2"><?php echo $resource['type']; ?></span>
                                <h5 class="card-title"><?php echo htmlspecialchars($resource['title']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($resource['description']); ?></p>
                            </div>
                            <div class="card-footer bg-white border-0">
                                  <a href="<?php echo $resource['file_url']; ?>" 
                                       class="btn btn-sm btn-success w-100" 
                                       download>
                                        <i class="fas fa-download me-2"></i>Download
                                    </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="tab-pane fade" id="videos" role="tabpanel">
            <div class="row g-4">
                <?php foreach (array_filter($online_resources, fn($r) => $r['type'] === 'Video') as $resource): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 resource-card border-0 shadow-sm">
                            <div class="ratio ratio-16x9">
                                <iframe src="<?php echo $resource['url']; ?>" 
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen></iframe>
                            </div>
                            <div class="card-body">
                                <span class="badge bg-success mb-2"><?php echo $resource['type']; ?></span>
                                <h5 class="card-title"><?php echo htmlspecialchars($resource['title']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($resource['description']); ?></p>
                            </div>
                            <div class="card-footer bg-white border-0">
                                <a href="<?php echo $resource['file_url']; ?>" 
                                       class="btn btn-sm btn-success w-100" 
                                       download>
                                        <i class="fas fa-download me-2"></i>Download
                                    </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="tab-pane fade" id="infographics" role="tabpanel">
            <div class="row g-4">
                <?php foreach (array_filter($online_resources, fn($r) => $r['type'] === 'Infographic') as $resource): ?>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="card h-100 resource-card border-0 shadow-sm">
                            <img src="<?php echo $resource['image']; ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($resource['title']); ?>"
                                 style="height: 180px; object-fit: cover;">
                            <div class="card-body">
                                <span class="badge bg-success mb-2"><?php echo $resource['type']; ?></span>
                                <h5 class="card-title"><?php echo htmlspecialchars($resource['title']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($resource['description']); ?></p>
                            </div>
                            <div class="card-footer bg-white border-0">
                                 <a href="<?php echo $resource['file_url']; ?>" 
                                       class="btn btn-sm btn-success w-100" 
                                       download>
                                        <i class="fas fa-download me-2"></i>Download
                                    </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>