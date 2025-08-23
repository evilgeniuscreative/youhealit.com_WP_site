<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wellness Center of the Triangle - Service Area Map</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }
        
        .map-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .map-header {
            background: #8bc34a;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .map-header h1 {
            margin: 0 0 10px 0;
            font-size: 1.8rem;
        }
        
        .map-header p {
            margin: 0;
            opacity: 0.9;
        }
        
        #map {
            width: 100%;
            height: 600px;
        }
        
        .map-info {
            padding: 20px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .info-card {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #8bc34a;
        }
        
        .info-card h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 1.1rem;
        }
        
        .info-card p {
            margin: 0;
            color: #666;
            line-height: 1.5;
        }
        
        .legend {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 50%;
        }
        
        .pin-color {
            background: #f44336;
        }
        
        .circle-color {
            background: rgba(255, 235, 59, 0.3);
            border: 2px solid #ffeb3b;
        }
    </style>
</head>
<body>
    <div class="map-container">
        <div class="map-header">
            <h1>Wellness Center of the Triangle NC</h1>
            <p>Service Area Coverage - 50 Mile Radius</p>
        </div>
        
        <div id="map"></div>
        
        <div class="map-info">
            <div class="info-grid">
                <div class="info-card">
                    <h3>📍 Clinic Location</h3>
                    <p>401 Meadowlands Drive<br>
                    Hillsborough, NC 27278<br>
                    Phone: <?php // echo YHI_PHONE ?></p>
                </div>
                
                <div class="info-card">
                    <h3>🎯 Service Area</h3>
                    <p>We proudly serve patients within a 50-mile radius of our Hillsborough location, covering the greater Triangle area and surrounding communities.</p>
                </div>
                
                <div class="info-card">
                    <h3>🚗 Coverage Includes</h3>
                    <p>Raleigh, Durham, Chapel Hill, Cary, Apex, Wake Forest, Burlington, Graham, and many other Triangle communities.</p>
                </div>
            </div>
            
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color pin-color"></div>
                    <span>Clinic Location</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color circle-color"></div>
                    <span>50-Mile Service Area</span>
                </div>
            </div>
        </div>
    </div>

    <script async defer 
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBUwf5B1CElRb8bKO5sM1IZ0xF7rS5iMJc&callback=initMap">
    </script>
    
    <script>
        function initMap() {
            // Clinic coordinates: 401 Meadowlands Drive, Hillsborough, NC 27278
            const clinicLocation = { lat: 36.0773, lng: -79.0947 };
            
            // Create the map
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 8,
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
                    <div style="padding: 10px; font-family: Arial, sans-serif;">
                        <h3 style="margin: 0 0 10px 0; color: #8bc34a;">Wellness Center of the Triangle NC</h3>
                        <p style="margin: 0 0 5px 0;"><strong>📍 Address:</strong><br>401 Meadowlands Drive<br>Hillsborough, NC 27278</p>
                        <p style="margin: 0 0 5px 0;"><strong>📞 Phone:</strong> <?php // echo YHI_PHONE ?></p>
                        <p style="margin: 0;"><strong>🕒 Hours:</strong> Mon-Fri 9am-5pm</p>
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
                radius: 80467.2 // 50 miles in meters (50 * 1609.344)
            });
            
            // Adjust map bounds to show the entire circle
            map.fitBounds(serviceAreaCircle.getBounds());
        }
        
        // Fallback if Google Maps fails to load
        window.setTimeout(function() {
            if (typeof google === 'undefined') {
                document.getElementById('map').innerHTML = `
                    <div style="display: flex; align-items: center; justify-content: center; height: 600px; background: #f5f5f5; color: #666; font-family: Arial, sans-serif;">
                        <div style="text-align: center;">
                            <h3>Map Temporarily Unavailable</h3>
                            <p>Please visit us at:<br>401 Meadowlands Drive, Hillsborough, NC 27278</p>
                        </div>
                    </div>
                `;
            }
        }, 5000);
    </script>
</body>
</html>