<?php
// File: /template-parts/hch-map.php
// Purpose: Map section for Health Center of Hillsborough
?>

<section class="map-section">
  <div class="map-container">
    <div class="map-info">
      <h2>Find Us Here</h2>
      
      <div class="address-info">
        <h3 style="color: var(--text-dark); font-size: 1.2rem; margin-bottom: 15px;">ADDRESS</h3>
        <p style="margin-bottom: 20px; line-height: 1.6;">
          401 Meadowlands Dr<br>
          Hillsborough, NC 27278
        </p>
      </div>
      
      <div class="hours-info">
        <h3 style="color: var(--text-dark); font-size: 1.2rem; margin-bottom: 15px;">HOURS</h3>
        <div style="margin-bottom: 20px;">
          <p style="margin: 5px 0;"><strong>Monday:</strong> 9:00 – 1:00 & 3:00 – 5:00</p>
          <p style="margin: 5px 0;"><strong>Tuesday:</strong> 9:00 – 1:00 & 3:00 – 5:00</p>
          <p style="margin: 5px 0;"><strong>Wednesday:</strong> 9:00 – 1:00 & 3:00 – 5:00</p>
          <p style="margin: 5px 0;"><strong>Thursday:</strong> 9:00 – 1:00 & 3:00 – 5:00</p>
          <p style="margin: 5px 0;"><strong>Friday:</strong> 9:00 – 1:00</p>
          <p style="margin: 5px 0;"><strong>Saturday:</strong> Closed</p>
          <p style="margin: 5px 0;"><strong>Sunday:</strong> 10:00 – 1:00 (By Appointment)</p>
        </div>
      </div>
      
      <div class="contact-info">
        <h3 style="color: var(--text-dark); font-size: 1.2rem; margin-bottom: 15px;">CONTACT</h3>
        <p style="margin-bottom: 10px;">
          <strong>Phone:</strong> <a href="tel:9192415092" style="color: var(--red-accent);">(919) 241-5092</a>
        </p>
        <p style="margin-bottom: 20px;">
          <strong>Email:</strong> <a href="mailto:info@youhealit.com" style="color: var(--red-accent);">info@youhealit.com</a>
        </p>
      </div>
      
      <div style="margin-top: 30px;">
        <a href="#" class="btn btn-red">Get Directions</a>
      </div>
    </div>
    
    <div class="map-embed">
      <!-- Address Input for Directions -->
      <div class="directions-input" style="margin-bottom: 15px; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; gap: 10px; align-items: center;">
          <input 
            type="text" 
            id="user-address" 
            placeholder="Enter your address for directions"
            style="flex: 1; padding: 10px; border: 2px solid #ddd; border-radius: 4px; font-size: 14px;"
          />
          <button 
            onclick="getDirections()" 
            class="btn btn-red"
            style="white-space: nowrap;"
          >
            Get Directions
          </button>
        </div>
      </div>
      
      <!-- Google Map with Pin -->
      <iframe 
        id="google-map"
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3242.123456789!2d-79.09876543!3d36.07654321!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89ace7c8b123456%3A0x987654321abcdef!2s401%20Meadowlands%20Dr%2C%20Hillsborough%2C%20NC%2027278%2C%20USA!5e0!3m2!1sen!2sus!4v1234567890123!5m2!1sen!2sus"
        width="100%" 
        height="100%" 
        style="border:0; border-radius: 8px;" 
        allowfullscreen="" 
        loading="lazy" 
        referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>
    
    <script>
    function getDirections() {
      const userAddress = document.getElementById('user-address').value.trim();
      const destinationAddress = '401 Meadowlands Dr, Hillsborough, NC 27278';
      
      if (userAddress === '') {
        alert('Please enter your address to get directions.');
        return;
      }
      
      // Create directions URL
      const directionsUrl = `https://www.google.com/maps/dir/${encodeURIComponent(userAddress)}/${encodeURIComponent(destinationAddress)}`;
      
      // Update the iframe with directions
      const mapIframe = document.getElementById('google-map');
      const embedUrl = `https://www.google.com/maps/embed/v1/directions?key=YOUR_API_KEY&origin=${encodeURIComponent(userAddress)}&destination=${encodeURIComponent(destinationAddress)}&mode=driving`;
      
      // For now, open in new window since we need API key for embed directions
      window.open(directionsUrl, '_blank');
      
      // Alternative: Update iframe if you have API key
      // mapIframe.src = embedUrl;
    }
    
    // Allow Enter key to trigger directions
    document.getElementById('user-address').addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        getDirections();
      }
    });
    </script>
  </div>
</section>