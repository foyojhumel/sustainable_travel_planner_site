const icon = document.getElementById('favIcon');
let isFavorited = false;

icon.addEventListener('click', () => {
  isFavorited = !isFavorited;
  icon.setAttribute('fill', isFavorited ? 'red' : '#434653');
});


function initMap() {
      const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 10,
        center: { lat: 14.179, lng: 121.169 } // initial center
      });

      const directionsService = new google.maps.DirectionsService();
      const directionsRenderer = new google.maps.DirectionsRenderer();
      directionsRenderer.setMap(map);

      // Example destinations (replace with fetch from your backend)
      const destinations = [
        { name: "Destination 1", lat: 14.179, lng: 121.169 },
        { name: "Destination 2", lat: 14.35, lng: 121.05 },
        { name: "Destination 3", lat: 14.55, lng: 121.02 }
      ];

      const origin = destinations[0];
      const destination = destinations[destinations.length - 1];
      const waypoints = destinations.slice(1, -1).map(d => ({
        location: new google.maps.LatLng(d.lat, d.lng),
        stopover: true
      }));

      directionsService.route({
        origin: new google.maps.LatLng(origin.lat, origin.lng),
        destination: new google.maps.LatLng(destination.lat, destination.lng),
        waypoints: waypoints,
        optimizeWaypoints: true, // automatically orders by shortest route
        travelMode: google.maps.TravelMode.DRIVING
      }, (result, status) => {
        if (status === google.maps.DirectionsStatus.OK) {
          directionsRenderer.setDirections(result);

          // Show distances between legs
          const route = result.routes[0];
          route.legs.forEach((leg, i) => {
            const info = `Leg ${i+1}: ${leg.start_address} → ${leg.end_address} 
                          Distance: ${leg.distance.text}, Duration: ${leg.duration.text}`;
            console.log(info);
            // You can also append this info to the page:
            const p = document.createElement("p");
            p.textContent = info;
            document.body.appendChild(p);
          });
        } else {
          console.error("Directions request failed due to " + status);
        }
      });
    }

    // Run when page loads
    window.onload = initMap;
