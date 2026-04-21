<?php

require_once 'dbConnect.php'; // pulls in the database connection from dbConnect.php

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
if (strlen($query) < 2) {
    echo json_encode([]);
    $conn->close();
    exit;
}

$like = "%{$query}%";

// Search provinces
$sqlProvinces = "SELECT province_id, province, 'province' as type, NULL as location_name,
                NULL as destination_name
                FROM province
                WHERE province LIKE ?
                LIMIT 5";

// Search locations (include parent province name)
$sqlLocations = "SELECT l.location_id, l.location as location_name, p.province as province_name,
                 'location' as type, p.province_id
                 FROM location l
                 JOIN province p ON l.province_id = p.province_id
                 WHERE l.location LIKE ?
                 LIMIT 5";

// Search destinations (include parent location and province names)
$sqlDestinations = "SELECT d.destination_id, d.destination as destination_name, l.location as location_name,
                    p.province as province_name, 'destination' as type, p.province_id
                    FROM destination d
                    JOIN location l ON d.location_id = l.location_id
                    JOIN province p ON l.province_id = p.province_id
                    WHERE d.destination LIKE ?
                    LIMIT 5";

$stmtProv = $conn->prepare($sqlProvinces);
$stmtProv->bind_param("s", $like);
$stmtProv->execute();
$provinces = $stmtProv->get_result()->fetch_all(MYSQLI_ASSOC);

$stmtLoc = $conn->prepare($sqlLocations);
$stmtLoc->bind_param("s", $like);
$stmtLoc->execute();
$locations = $stmtLoc->get_result()->fetch_all(MYSQLI_ASSOC);

$stmtDest = $conn->prepare($sqlDestinations);
$stmtDest->bind_param("s", $like);
$stmtDest->execute();
$destinations = $stmtDest->get_result()->fetch_all(MYSQLI_ASSOC);

// Combine results
$suggestions = array_merge($provinces, $locations, $destinations);

// Format each suggestion for display
$output = [];
foreach ($suggestions as $item) {
    if ($item['type'] == 'province') {
        $label = $item['province'] . ' (Province)';
        $province_id = $item['province_id'];
    } elseif ($item['type'] == 'location') {
        $label = $item['location_name'] . ' (Location in ' . $item['province_name'] . ')';
        $province_id = $item['province_id'];
    } else { // destination
        $label = $item['destination_name'] . ' (in ' . $item['location_name'] . ', ' . $item['province_name'] . ')';
        $province_id = $item['province_id'];
    }
    $output[] = [
        'label' => $label,
        'province_id' => $province_id,
        'type' => $item['type']
    ];
}

header('Content-Type: application/json');
echo json_encode($output);

$conn->close();

?>