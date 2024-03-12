<?php
require_once '../config.php';
$query =
    'SELECT prizes.year, candidates.name, candidates.surname, candidates.organization, countries.country, categories.category 
        FROM candidates 
        JOIN countries ON candidates.country_id = countries.id 
        JOIN prizes ON candidates.prize_id = prizes.id 
        JOIN categories ON prizes.category_id = categories.id;
    ';
$stmt = $pdo->query($query); // Execute the query using PDO
$data = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Add each row as an array to the $data array
    $data[] = array(
        $row['year'],
        $row['name'],
        $row['surname'],
        $row['organization'],
        $row['country'],
        $row['category']
    );
}

// Convert $data to JSON format
$data_json = json_encode($data, JSON_UNESCAPED_UNICODE);
// Output the JSON data
echo $data_json;

