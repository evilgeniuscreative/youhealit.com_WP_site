// /js/maps.js

let map, directionsService, directionsRenderer, circle, clinicMarker;
const clinicLatLng = { lat: 36.078157, lng: -79.090653 }; // The Clinic

function initMap() {
    map = new google.maps.Map(document.getElementById('city-map'), {
        center: clinicLatLng,
        zoom: 9,
    });

    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer();
    directionsRenderer.setMap(map);

    // Clinic info window content
    const infoWindowContent = `
    <div style="font-size: 14px;">
      <strong><a href="https://www.google.com/maps?q=401+Meadowlands+Dr,+Hillsborough,+NC+27278" target="_blank">Health Center of the Triangle</a></strong><br>
      401 Meadowlands Dr, Ste. 101<br>
      Hillsborough, NC 27278<br>
      <a href="tel:+19192415032">(919) 241-5032</a>
    </div>
  `;

    const infoWindow = new google.maps.InfoWindow({
        content: infoWindowContent,
    });

    clinicMarker = new google.maps.Marker({
        position: clinicLatLng,
        map,
        title: 'Health Center of the Triangle',
    });

    clinicMarker.addListener('click', () => {
        infoWindow.open(map, clinicMarker);
    });

    // Circle: 50-mile radius
    circle = new google.maps.Circle({
        map,
        center: clinicLatLng,
        radius: 80467.2, // meters
        fillColor: '#FF0000',
        fillOpacity: 0.1,
        strokeWeight: 0,
    });

    document.getElementById('go-button').addEventListener('click', () => {
        const address = document.getElementById('user-address').value;
        if (address) {
            routeFromAddress(address);
        }
    });

    const cityCenter = document.getElementById('city-center')?.value;
    if (cityCenter) {
        routeFromAddress(cityCenter);
    }
}

function routeFromAddress(start) {
    directionsService.route(
        {
            origin: start,
            destination: clinicLatLng,
            travelMode: google.maps.TravelMode.DRIVING,
        },
        (response, status) => {
            if (status === 'OK') {
                directionsRenderer.setDirections(response);
            } else {
                alert('Could not calculate route: ' + status);
            }
        }
    );
}
