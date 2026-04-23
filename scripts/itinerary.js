const icon = document.getElementById('favIcon');
let isFavorited = false;

icon.addEventListener('click', () => {
  isFavorited = !isFavorited;
  icon.setAttribute('fill', isFavorited ? 'red' : '#434653');
});

// Map display
let map, directionsService, directionsRenderer;

async function initMap() {
    const urlParams = new URLSearchParams(window.location.search);
    const locationId = urlParams.get('location_id');
    const startDestId = urlParams.get('destination_id');

    console.log('=== initMap ===');
    console.log('locationId:', locationId);
    console.log('startDestId:', startDestId);

    if (!locationId) {
        const cardsContainer = document.getElementById('itineraryCards');
        if (cardsContainer) cardsContainer.innerHTML = '<p class="text-red-500">No location selected.</p>';
        return;
    }

    try {
        const apiUrl = `${BASE_PATH}/php/getDestinationsByLocation.php?location_id=${locationId}`;
        const response = await fetch(apiUrl);
        const data = await response.json();

        if (data.error) throw new Error(data.error);
        if (!data.destinations || data.destinations.length === 0) throw new Error('No destinations found.');

        // Populate header
        const locationNameEl = document.getElementById('locationName');
        const locationDescEl = document.getElementById('locationDesc');
        if (locationNameEl) locationNameEl.innerText = data.location.name || data.location;
        if (locationDescEl) locationDescEl.innerText = data.location.description || '';

        const allDestinations = data.destinations;
        console.log('Addresses to geocode:', allDestinations.map(d => d.address));

        // Geocode all destinations
        const geocoder = new google.maps.Geocoder();
        const destinationsWithCoords = [];

        for (const dest of allDestinations) {
            try {
                const result = await new Promise((resolve, reject) => {
                    geocoder.geocode({ address: dest.address }, (results, status) => {
                        if (status === 'OK' && results[0]) {
                            const loc = results[0].geometry.location;
                            resolve({ ...dest, lat: loc.lat(), lng: loc.lng() });
                        } else {
                            reject(status);
                        }
                    });
                });
                destinationsWithCoords.push(result);
            } catch (err) {
                console.warn(`Skipping "${dest.destination}" – geocoding failed: ${err}`);
            }
        }

        if (destinationsWithCoords.length === 0) {
            throw new Error('No destinations could be geocoded.');
        }

        // Determine starting destination
        let startDest = destinationsWithCoords[0];
        if (startDestId) {
            const found = destinationsWithCoords.find(d => d.destination_id == startDestId);
            if (found) startDest = found;
            else console.warn(`Start destination ID ${startDestId} not found, using first.`);
        }

        // Initialize map
        const { Map } = await google.maps.importLibrary("maps");
        const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
        const { DirectionsService, DirectionsRenderer } = await google.maps.importLibrary("routes");

        map = new Map(document.getElementById("map"), {
            zoom: 12,
            center: { lat: startDest.lat, lng: startDest.lng },
            mapId: 'DEMO_MAP_ID'
        });

        directionsService = new DirectionsService();
        directionsRenderer = new DirectionsRenderer({ map: map });

        // Add markers
        destinationsWithCoords.forEach(dest => {
            new AdvancedMarkerElement({
                map: map,
                position: { lat: dest.lat, lng: dest.lng },
                title: dest.destination
            });
        });

        // If only one destination
        if (destinationsWithCoords.length === 1) {
            renderItineraryCards([startDest], startDest.destination_id);
            map.setCenter({ lat: startDest.lat, lng: startDest.lng });
            map.setZoom(14);
            document.getElementById('totalDistance').innerText = '—';
            document.getElementById('totalDuration').innerText = '—';
            return;
        }

        // Build route request
        const otherDestinations = destinationsWithCoords.filter(d => d.destination_id !== startDest.destination_id);
        
        // Separate the last destination as the final stop (we'll keep it for the end)
        // We'll let Google reorder the waypoints, but the final destination will stay at the end
        const finalDestinationObj = otherDestinations[otherDestinations.length - 1];
        const waypointDestinations = otherDestinations.slice(0, -1); // all except last

        const waypoints = waypointDestinations.map(dest => ({
            location: { lat: dest.lat, lng: dest.lng },
            stopover: true
        }));

        const request = {
            origin: { lat: startDest.lat, lng: startDest.lng },
            destination: { lat: finalDestinationObj.lat, lng: finalDestinationObj.lng },
            waypoints: waypoints,
            optimizeWaypoints: true,
            travelMode: google.maps.TravelMode.DRIVING
        };

        directionsService.route(request, (result, status) => {
            if (status === 'OK') {
                directionsRenderer.setDirections(result);
                const route = result.routes[0];
                const waypointOrder = route.waypoint_order; // indices into waypoints array

                // Build ordered list: start, then reordered waypoints, then final destination
                const orderedDestinations = [startDest];
                waypointOrder.forEach(idx => {
                    orderedDestinations.push(waypointDestinations[idx]);
                });
                orderedDestinations.push(finalDestinationObj);

                renderItineraryCards(orderedDestinations, startDest.destination_id);

                // Calculate totals
                let totalDistance = 0;
                let totalDuration = 0;
                route.legs.forEach(leg => {
                    totalDistance += leg.distance.value;
                    totalDuration += leg.duration.value;
                });

                const totalDistanceKm = (totalDistance / 1000).toFixed(1);
                const totalDurationHours = (totalDuration / 3600).toFixed(1);
                const totalDurationMin = Math.round(totalDuration / 60);

                const distanceEl = document.getElementById('totalDistance');
                const durationEl = document.getElementById('totalDuration');
                if (distanceEl) distanceEl.innerText = `${totalDistanceKm} km`;
                if (durationEl) durationEl.innerText = totalDuration >= 3600 ? `${totalDurationHours} hrs` : `${totalDurationMin} min`;

                // Fit bounds
                const bounds = new google.maps.LatLngBounds();
                orderedDestinations.forEach(d => bounds.extend({ lat: d.lat, lng: d.lng }));
                map.fitBounds(bounds);
            } else {
                console.error('Directions request failed:', status);
                document.getElementById('itineraryCards').innerHTML = `<p class="text-red-500">Route calculation failed: ${status}</p>`;
            }
        });
    } catch (error) {
        console.error('Error in initMap:', error);
        const cardsContainer = document.getElementById('itineraryCards');
        if (cardsContainer) cardsContainer.innerHTML = `<p class="text-red-500">${error.message}</p>`;
        const mapDiv = document.getElementById('map');
        if (mapDiv) mapDiv.innerHTML = '<p class="text-red-500 p-4">Map failed to load. Check console.</p>';
    }
}

function renderItineraryCards(destinations, startId) {
    const container = document.getElementById('itineraryCards');
    if (!container) return;
    container.innerHTML = destinations.map((dest, idx) => `
        <div class="itinerary_card relative pl-12 mb-12">
            <!--Motor Icon-->
            <div class="absolute left-0 top-1 w-8 h-8 rounded-full bg-surface-container-highest border border-outline-variant/50 flex items-center justify-center z-10">
                <svg height="18px" viewBox="0 -960 960 960" width="18px" fill="#00327d">
                    <path d="M428-520h-70 150-80ZM200-200q-83 0-141.5-58.5T0-400q0-83 58.5-141.5T200-600h464l-80-80H440v-80h143q16 0 30.5 6t25.5 17l139 139q78 6 130 63t52 135q0 83-58.5 141.5T760-200q-83 0-141.5-58.5T560-400q0-18 2.5-35.5T572-470L462-360h-66q-14 70-69 115t-127 45Zm560-80q50 0 85-35t35-85q0-50-35-85t-85-35q-50 0-85 35t-35 85q0 50 35 85t85 35Zm-560 0q38 0 68.5-22t43.5-58H200v-80h112q-13-36-43.5-58T200-520q-50 0-85 35t-35 85q0 50 35 85t85 35Zm198-160h30l80-80H358q15 17 25 37t15 43Z"/>
                </svg>
            </div>
            <div class="flex justify-between items-baseline mb-2">
                <h3 class="font-headline font-bold text-xl text-primary">
                    ${escapeHtml(dest.destination)}
                </h3>
                <span class="text-sm text-gray-500">Stop ${idx+1}</span>
            </div>
            <div class="bg-on-primary p-5 rounded-xl editorial-shadow ${dest.destination_id == startId ? 'border-l-4 border-primary' : ''}">
                <!--Eco-Indicator and Eco-Rating-->
                <div class="flex justify-between mb-2">
                    <div class="bg-eco-indicator/90 backdrop-blur text-on-primary px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest flex items-center gap-1">
                        <!--Eco Icon-->
                        <svg height="18px" viewBox="0 -960 960 960" width="18px" fill="#ffffff">
                            <path d="M216-176q-45-45-70.5-104T120-402q0-63 24-124.5T222-642q35-35 86.5-60t122-39.5Q501-756 591.5-759t202.5 7q8 106 5 195t-16.5 160.5q-13.5 71.5-38 125T684-182q-53 53-112.5 77.5T450-80q-65 0-127-25.5T216-176Zm112-16q29 17 59.5 24.5T450-160q46 0 91-18.5t86-59.5q18-18 36.5-50.5t32-85Q709-426 716-500.5t2-177.5q-49-2-110.5-1.5T485-670q-61 9-116 29t-90 55q-45 45-62 89t-17 85q0 59 22.5 103.5T262-246q42-80 111-153.5T534-520q-72 63-125.5 142.5T328-192Zm0 0Zm0 0Z"/>
                        </svg>
                        ${escapeHtml(dest.eco_indicator)}
                    </div>
                    <div class="text-right flex items-center gap-1 text-secondary justify-end">
                        <span class="font-variation-settings: 'FILL' 1;">🌱</span>
                        <span class="font-bold">${escapeHtml(dest.rating)}</span>
                    </div>
                </div>
                <img src="${dest.path}" alt="${dest.description}" class="w-full h-40 object-cover rounded-lg mb-2"/>
                <p class="text-on-surface-variant">
                    ${escapeHtml(dest.description)}
                </p>
            </div>
        </div>
    `).join('');
}

window.initMap = initMap;