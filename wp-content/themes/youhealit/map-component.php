<?php
/**
 * Flexible Google Map Component
 * Usage: <?php include 'map-component.php'; echo wellness_map('800px', '500px'); ?>
 * Or: <?php include 'map-component.php'; echo wellness_map('100%', '400px'); ?>
 */

function wellness_map($width = '100%', $height = '400px', $zoom = 8) {
    // Generate unique ID for this map instance
    $map_id = 'wellness_map_' . uniqid();
    
    ob_start();
    ?>
    <div class="wellness-map-container" style="width: <?php echo esc_attr($width); ?>; height: <?php echo esc_attr($height); ?>; position: relative;">
        <div id="<?php echo $map_id; ?>" style="width: 100%; height: 100%; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1);"></div>
    </div>

    <script>
        (function() {
            function init<?php echo str_replace('_', '', $map_id); ?>() {
                // Clinic coordinates: 401 Meadowlands Drive, Hillsborough, NC 27278
                const clinicLocation = { lat: 36.0773, lng: -79.0947 };
                
                // Create the map
                const map = new google.maps.Map(document.getElementById("<?php echo $map_id; ?>"), {
                    zoom: <?php echo intval($zoom); ?>,
                    center: clinicLocation,
                    mapTypeId: "roadmap",
                    styles: [
                        {
                            featureType: "poi.business",
                            stylers: [{ visibility: "off" }]
                        },
                        {
                            featureType: "poi.park",
                            elementType: "labels.text",
                            stylers: [{ visibility: "off" }]
                        }
                    ]
                });
                
                // Add marker for the clinic
                const clinicMarker = new google.maps.Marker({
                    position: clinicLocation,
                    map: map,
                    title: "Wellness Center of the Triangle NC",
                    icon: {
                        url: "data:image/svg+xml;charset=UTF-8," + encodeURIComponent(`
                            <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="16" cy="16" r="12" fill="#f44336" stroke="white" stroke-width="2"/>
                                <circle cx="16" cy="16" r="4" fill="white"/>
                            </svg>
                        `),
                        scaledSize: new google.maps.Size(32, 32),
                        anchor: new google.maps.Point(16, 16)
                    }
                });
                
                // Add info window for the clinic
                const infoWindow = new google.maps.InfoWindow({
                    content: `
                        <div style="padding: 10px; font-family: Arial, sans-serif; max-width: 250px;">
                            <h3 style="margin: 0 0 10px 0; color: #8bc34a; font-size: 16px;">Wellness Center of the Triangle NC</h3>
                            <p style="margin: 0 0 5px 0; font-size: 14px;"><strong>📍 Address:</strong><br>401 Meadowlands Drive<br>Hillsborough, NC 27278</p>
                            <p style="margin: 0 0 5px 0; font-size: 14px;"><strong>📞 Phone:</strong> ${YHI_PHONE}</p>
                            <p style="margin: 0; font-size: 14px;"><strong>🕒 Hours:</strong> Mon-Fri 9am-5pm</p>
                        </div>
                    `
                });
                
                // Open info window when marker is clicked
                clinicMarker.addListener("click", () => {
                    infoWindow.open(map, clinicMarker);
                });
                
                // Add 50-mile radius circle
                const serviceAreaCircle = new google.maps.Circle({
                    strokeColor: "#ffeb3b",
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: "#ffeb3b",
                    fillOpacity: 0.15,
                    map: map,
                    center: clinicLocation,
                    radius: 80467.2 // 50 miles in meters
                });
                
                // Adjust map bounds to show the entire circle if zoom allows
                if (<?php echo intval($zoom); ?> <= 8) {
                    map.fitBounds(serviceAreaCircle.getBounds());
                }
            }
            
            // Initialize map when Google Maps is loaded
            if (typeof google !== 'undefined' && google.maps) {
                init<?php echo str_replace('_', '', $map_id); ?>();
            } else {
                // Wait for Google Maps to load
                window.addEventListener('load', function() {
                    if (typeof google !== 'undefined' && google.maps) {
                        init<?php echo str_replace('_', '', $map_id); ?>();
                    } else {
                        // Fallback if Google Maps fails
                        document.getElementById('<?php echo $map_id; ?>').innerHTML = `
                            <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f5f5f5; color: #666; font-family: Arial, sans-serif; border-radius: 8px;">
                                <div style="text-align: center; padding: 20px;">
                                    <h4 style="margin: 0 0 10px 0;">Map Temporarily Unavailable</h4>
                                    <p style="margin: 0; font-size: 14px;">401 Meadowlands Drive<br>Hillsborough, NC 27278<br>${YHI_PHONE}</p>
                                </div>
                            </div>
                        `;
                    }
                });
            }
        })();
    </script>
    <?php
    return ob_get_clean();
}

// Simple usage function for quick implementation
function wellness_map_simple($width = '100%', $height = '400px') {
    return wellness_map($width, $height);
}

// Include Google Maps API if not already loaded
function wellness_enqueue_google_maps() {
    static $enqueued = false;
    if (!$enqueued) {
        echo '<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBo4vLsaDOziABBhiiq8DSe-qc9oN1Qj-4"></script>';
        $enqueued = true;
    }
}

// Auto-enqueue Google Maps if this file is included
wellness_enqueue_google_maps();
?>