const icon = document.getElementById('favIcon');
let isFavorited = false;

icon.addEventListener('click', () => {
  isFavorited = !isFavorited;
  icon.setAttribute('fill', isFavorited ? 'red' : '#434653');
});


// --- Your list of destinations (use real, specific addresses) ---
const destinations = [
    "Vayang Rolling Hills, Batanes, Philippines",
    "Alapad Pass, Batanes, Philippines",
    "Basco Lighthouse, Batanes, Philippines"
];

// Main async function to initialize the map and all APIs
async function initMap() {
    try {
        // 1. Import the required libraries dynamically
        const { Map } = await google.maps.importLibrary("maps");
        const { DirectionsService, DirectionsRenderer } = await google.maps.importLibrary("routes");
        const { Geocoder } = await google.maps.importLibrary("geocoding");
        const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

        // 2. Define the map's center (default to first destination)
        const map = new Map(document.getElementById("map"), {
            zoom: 12,
            center: { lat: 48.8566, lng: 2.3522 }, // Center of Paris
            mapId: 'DEMO_MAP_ID', // <-- REQUIRED for advanced markers
        });

        // 3. Initialize Directions services
        const directionsService = new DirectionsService();
        const directionsRenderer = new DirectionsRenderer({ map: map });

        // 4. Geocode all destinations to get their coordinates
        const geocoder = new Geocoder();
        const geocodePromises = destinations.map(destination => {
            return new Promise((resolve, reject) => {
                geocoder.geocode({ address: destination }, (results, status) => {
                    if (status === "OK" && results[0]) {
                        resolve(results[0].geometry.location);
                    } else {
                        console.error(`Geocode failed for ${destination}: ${status}`);
                        reject(status);
                    }
                });
            });
        });

        // Wait for all geocoding to complete
        const coordinates = await Promise.all(geocodePromises);

        // 5. Add advanced markers for each location
        coordinates.forEach((coord, index) => {
            const marker = new AdvancedMarkerElement({
                map: map,
                position: coord,
                title: destinations[index],
            });
            // Optional: Add an info window on click
            const infoWindow = new google.maps.InfoWindow({
                content: `<strong>${destinations[index]}</strong>`,
            });
            marker.addListener("gmp-click", () => {
                infoWindow.open(map, marker);
            });
        });

        // 6. Calculate and display the route
        if (coordinates.length >= 2) {
            const origin = coordinates[0];
            const destination = coordinates[coordinates.length - 1];
            const waypoints = coordinates.slice(1, -1).map(point => ({
                location: point,
                stopover: true,
            }));

            const request = {
                origin: origin,
                destination: destination,
                waypoints: waypoints,
                optimizeWaypoints: true, // Let Google reorder for the most efficient route
                travelMode: google.maps.TravelMode.DRIVING,
            };

            directionsService.route(request, (result, status) => {
                if (status === "OK") {
                    directionsRenderer.setDirections(result);
                    // Display total distance and duration in the panel
                    const totalDistance = result.routes[0].legs.reduce((total, leg) => total + leg.distance.value, 0) / 1000;
                    const totalDuration = result.routes[0].legs.reduce((total, leg) => total + leg.duration.value, 0) / 60;
                    /*document.getElementById('directions-panel').innerHTML = `
                        <h3>Total Trip Distance: ${totalDistance.toFixed(2)} km</h3>
                        <p>Estimated Travel Time: ${Math.round(totalDuration)} minutes</p>
                    `;*/
                    document.getElementById('totalDistance').innerText = totalDistance.toFixed(1) + ' km';
                    document.getElementById('totalDuration').innerText = Math.round(totalDuration) + ' min';

                } else {
                    console.error("Directions request failed due to " + status);
                    document.getElementById('directions-panel').innerHTML = `<p>Could not calculate route. Error: ${status}</p>`;
                }
            });
        }
    } catch (error) {
        console.error("Failed to initialize map:", error);
        document.getElementById('map').innerHTML = '<p style="color: red;">Error loading map. Check console for details.</p>';
    }
}

// Ensure initMap is globally available and called after the API loads
window.initMap = initMap;