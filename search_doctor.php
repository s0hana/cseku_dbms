<?php
include 'db.php';

$q = $_GET['q'];

$sql = "SELECT d.user_ID as doctor_ID, d.bmdc_registration_number, d.specialization,
        s.full_name, c.chamber_ID, c.chamber_name,
        a.house_no, a.road, a.area, a.thana, a.district
        FROM doctor d
        JOIN systemuser s ON s.user_ID = d.user_ID
        JOIN works_in w ON w.doctor_ID = d.user_ID
        JOIN chamber c ON c.chamber_ID = w.chamber_ID
        JOIN chamber_address a ON a.chamber_ID = c.chamber_ID
        WHERE 
        s.full_name LIKE ? OR d.specialization LIKE ? OR d.bmdc_registration_number LIKE ? OR 
        c.chamber_name LIKE ? OR a.area LIKE ?";

$stmt = $conn->prepare($sql);
$param = "%" . $q . "%";
$stmt->bind_param("sssss", $param, $param, $param, $param, $param);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while($row = $result->fetch_assoc()) {
    $full_address = implode(', ', array_filter([
        $row['house_no'],
        $row['road'],
        $row['area'],
        $row['thana'],
        $row['district']
    ]));

    $data[] = [
        "doctor_id" => $row['doctor_ID'],
        "doctor_name" => $row['full_name'],
        "specialization" => $row['specialization'],
        "registration_number" => $row['bmdc_registration_number'],
        "chamber_id" => $row['chamber_ID'],
        "chamber_name" => $row['chamber_name'],
        "chamber_address" => $full_address
    ];
}

echo json_encode($data);
?>
