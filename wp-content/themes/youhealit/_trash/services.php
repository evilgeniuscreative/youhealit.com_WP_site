<?php
// Standalone Services Page (not a WordPress template)

// Include WordPress functions if available
if (file_exists('wp-load.php')) {
    require_once('wp-load.php');
} elseif (file_exists('../wp-load.php')) {
    require_once('../wp-load.php');
}


// Sort services alphabetically
$services = $available_services;
usort($services, function($a, $b) {
    return strcmp($a['name'], $b['name']);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - Health Center</title>
    <style>
        :root {
            --primary-green: #2c5530;
            --red-accent: #c41e3a;
            --text-light: #666;
            --light-gray: #f8f9fa;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        
        .main-content {
            min-height: 100vh;
        }
        
        .page-hero {
            background: var(--primary-green);
            padding: 80px 20px;
            text-align: center;
            color: white;
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .service-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
        }
        
        .service-image {
            height: 200px;
            background: linear-gradient(135deg, var(--primary-green), var(--red-accent));
            position: relative;
        }
        
        .service-content {
            padding: 25px;
        }
        
        .btn {
            background: var(--red-accent);
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            transition: background 0.3s ease;
        }
        
        .btn:hover {
            background: #a01729;
        }
        
        #search-input {
            width: 100%;
            max-width: 500px;
            padding: 12px 16px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 8px;
            margin: 0 auto 40px;
            display: block;
        }
        
        .suggestions {
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .suggestion-item {
            padding: 10px 16px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }
        
        .suggestion-item:hover {
            background: #f5f5f5;
        }
    </style>
</head>
<body>
    <main class="main-content">
        <!-- Services Hero Section -->
        <section class="page-hero">
            <div style="max-width: 1200px; margin: 0 auto;">
                <h1 style="font-size: 3rem; margin-bottom: 20px; font-weight: 300;">SERVICES</h1>
                <p style="font-size: 1.2rem; max-width: 600px; margin: 0 auto;">Comprehensive natural health solutions under one roof</p>
            </div>
        </section>

        <!-- Search Section -->
        <section style="max-width: 1200px; margin: 40px auto; padding: 0 20px; position: relative;">
            <input type="text" id="search-input" placeholder="Search services..." />
            <div id="suggestions" class="suggestions" style="display: none;"></div>
        </section>

        <!-- Services Grid -->
        <section style="max-width: 1200px; margin: 0 auto 80px; padding: 0 20px;">
            <div class="services-grid" id="services-grid">
                <?php foreach ($services as $service): ?>
                    <div class="service-card" data-service="<?php echo strtolower($service['name']); ?>" id="<?php echo str_replace(' ', '', $service['name']); ?>">
                        <div class="service-image">
                            <div style="position: absolute; bottom: 20px; left: 20px; color: white;">
                                <h3 style="margin: 0; font-size: 1.3rem; font-weight: 600;"><?php echo ucwords($service['name']); ?></h3>
                            </div>
                        </div>
                        <div class="service-content">
                            <p style="color: var(--text-light); font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px;">
                                <?php echo $service['description']; ?>
                            </p>
                            <a href="#" class="btn">Learn More</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <script>
        // Services data for autocomplete
        const servicesData = <?php echo json_encode(array_map(function($service) {
            return ['label' => ucwords($service['name']), 'value' => $service['name']];
        }, $services)); ?>;

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const suggestions = document.getElementById('suggestions');
            
            // Filter and display services
            function filterServices(query) {
                const cards = document.querySelectorAll('.service-card');
                const lowerQuery = query.toLowerCase();
                
                cards.forEach(card => {
                    const serviceName = card.getAttribute('data-service');
                    const serviceText = card.textContent.toLowerCase();
                    
                    if (serviceName.includes(lowerQuery) || serviceText.includes(lowerQuery)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }
            
            // Show suggestions
            function showSuggestions(query) {
                const filtered = servicesData.filter(service => 
                    service.label.toLowerCase().includes(query.toLowerCase())
                ).slice(0, 8);
                
                suggestions.innerHTML = '';
                
                if (filtered.length > 0 && query.length > 0) {
                    filtered.forEach(service => {
                        const div = document.createElement('div');
                        div.className = 'suggestion-item';
                        div.textContent = service.label;
                        
                        div.addEventListener('click', () => {
                            searchInput.value = service.label;
                            suggestions.style.display = 'none';
                            filterServices(service.value);
                        });
                        
                        suggestions.appendChild(div);
                    });
                    suggestions.style.display = 'block';
                } else {
                    suggestions.style.display = 'none';
                }
            }
            
            // Event listeners
            searchInput.addEventListener('input', (e) => {
                const query = e.target.value;
                filterServices(query);
                showSuggestions(query);
            });
            
            searchInput.addEventListener('blur', () => {
                setTimeout(() => {
                    suggestions.style.display = 'none';
                }, 200);
            });
        });
    </script>
</body>
</html>
