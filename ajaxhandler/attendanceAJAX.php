<?php
session_start();
require_once "../database/database.php";

$db = new Database();
$conn = $db->getConnection();

// Get the action and teacher's department from session
$action = $_POST['action'] ?? '';
$dept = $_SESSION['teacher_dept'] ?? '';

// --- 1. Get Subjects based on Year and Teacher's Dept ---
if($action == "get_subjects") {
    $year = $_POST['year'];
    $stmt = $conn->prepare("SELECT id, course_name FROM course_details WHERE department = :dept AND year = :yr");
    $stmt->execute(['dept' => $dept, 'yr' => $year]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
}

// --- 2. Get Students with their Attendance Status for a specific date ---
if($action == "get_students") {
    $date = $_POST['date'];
    $course_id = $_POST['course_id'];
    
    // We search for students whose roll number contains the teacher's department (e.g., %BCT%)
    $search = "%" . $dept . "%";

    $sql = "SELECT s.id, s.roll_no, s.name, a.status 
            FROM student_details s 
            LEFT JOIN attendance_details a ON s.id = a.student_id 
                AND a.on_date = :dt 
                AND a.course_id = :cid
            WHERE s.roll_no LIKE :search 
            ORDER BY s.roll_no ASC";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'dt' => $date, 
        'cid' => $course_id, 
        'search' => $search
    ]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
}

// --- 3. Save or Update Attendance ---
if($action == "save_attendance") {
    $date = $_POST['date'];
    $course_id = $_POST['course_id'];
    $records = $_POST['attendance']; // This is the array from JS

    // Security check: No future dates
    if($date > date('Y-m-d')) {
        echo json_encode(["status" => "ERR", "message" => "Future dates are not allowed!"]);
        exit();
    }

    try {
        $conn->beginTransaction();

        // Step A: Clear existing records for this specific date and course
        // This allows the teacher to "Update" attendance by over-writing
        $del = $conn->prepare("DELETE FROM attendance_details WHERE on_date = ? AND course_id = ?");
        $del->execute([$date, $course_id]);

        // Step B: Insert the new statuses
        $sql = "INSERT INTO attendance_details (student_id, course_id, on_date, status) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        foreach($records as $r) {
            $stmt->execute([
                $r['student_id'], 
                $course_id, 
                $date, 
                $r['status']
            ]);
        }

        $conn->commit();
        echo json_encode(["status" => "OK", "message" => "Attendance synced for $date"]);
        
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(["status" => "ERR", "message" => "Database Error: " . $e->getMessage()]);
    }
}
// Add this inside attendanceAjax.php
if($action == "get_report") {
    $cid = $_POST['course_id'];
    $search = "%" . $dept . "%";

    // This query counts total classes for that subject and 
    // how many times each student was marked 'P'
    $sql = "SELECT s.roll_no, s.name,
            (SELECT COUNT(DISTINCT on_date) FROM attendance_details WHERE course_id = :cid1) as total_classes,
            COUNT(CASE WHEN a.status = 'P' THEN 1 END) as present_count
            FROM student_details s
            LEFT JOIN attendance_details a ON s.id = a.student_id AND a.course_id = :cid2
            WHERE s.roll_no LIKE :search
            GROUP BY s.id
            ORDER BY s.roll_no ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'cid1' => $cid,
        'cid2' => $cid,
        'search' => $search
    ]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
?>