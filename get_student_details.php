<?php
require_once 'db_config.php';

if (isset($_GET['lrn'])) {
    $lrn = $_GET['lrn'];
    
    $sql = "SELECT * FROM users WHERE lrn = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $lrn);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        echo json_encode($student);
    } else {
        echo json_encode(["error" => "Student not found"]);
    }
    
    $stmt->close();
} else {
    echo json_encode(["error" => "LRN not provided"]);
}

$conn->close();
?>