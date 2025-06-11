<?php
$mysqli = new mysqli("localhost", "root", "", "pulsescheduler");

if ($mysqli->connect_errno) {
    die("Failed to connect: " . $mysqli->connect_error);
}

if (isset($_GET['q'])) {
    $query = $_GET['q'];
    
    // Updated: Search chambers by name or chamber_address fields
    $sql = "SELECT chamber.chamber_ID, chamber.chamber_name, 
                   chamber_address.house_no, chamber_address.road, chamber_address.area, 
                   chamber_address.thana, chamber_address.district, 
                   chamber_address.division, chamber_address.postal_code
            FROM chamber
            LEFT JOIN chamber_address ON chamber.chamber_ID = chamber_address.chamber_ID
            WHERE chamber.chamber_name LIKE ? 
               OR CONCAT_WS(', ', chamber_address.house_no, chamber_address.road, chamber_address.area, 
                            chamber_address.thana, chamber_address.district, 
                            chamber_address.division, chamber_address.postal_code) LIKE ?";

    $stmt = $mysqli->prepare($sql);
    $searchTerm = '%' . $query . '%';
    $stmt->bind_param('ss', $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $chambers = [];
    while ($row = $result->fetch_assoc()) {
        $chambers[] = [
            'value' => $row['chamber_ID'],
            'label' => $row['chamber_name'] . ', ' . $row['house_no'] . ', ' . $row['road'] . ', ' . $row['area'] . ', ' . $row['thana'] . ', ' . $row['district'] . ', ' . $row['division'] . ', ' . $row['postal_code']
        ];
    }

    echo json_encode($chambers);
}

$mysqli->close();
?>
