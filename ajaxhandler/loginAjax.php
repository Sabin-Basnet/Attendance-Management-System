<?php
session_start();
require_once "../database/database.php";

$db = new Database();
$conn = $db->getConnection();

$un = $_POST['user_name'] ?? '';
$pw = $_POST['password'] ?? '';

if (!empty($un) && !empty($pw)) {
    // We match against the 'teacher_details' table we created in createtables.php
    $sql = "SELECT * FROM teacher_details WHERE user_name = :un AND password = :pw LIMIT 1";// limit 1: stop looking for another after first match
    $stmt = $conn->prepare($sql);
    $stmt->execute(['un' => $un, 'pw' => $pw]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($teacher) {
        // Set the sessions needed by attendance.php
        $_SESSION['teacher_id'] = $teacher['id'];
        $_SESSION['teacher_name'] = $teacher['name'];
        $_SESSION['teacher_dept'] = $teacher['department'];

        echo json_encode(["status" => "ALL OK"]);
    } else {
        echo json_encode(["status" => "INVALID", "message" => "Wrong credentials"]);
    }
} else {
    echo json_encode(["status" => "EMPTY", "message" => "Fields cannot be empty"]);
}
?>