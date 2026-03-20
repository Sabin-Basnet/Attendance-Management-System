<?php
session_start();
// Path: Up one level to root, then into database folder
require_once "../database/database.php"; 

$db = new Database();
$conn = $db->getConnection();

$action = $_POST['action'] ?? '';
$dept = $_SESSION['teacher_dept'] ?? '';

if (!$dept) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "ERR", "message" => "Session expired."]);
    exit;
}

header('Content-Type: application/json');

try {
    if($action == "get_subjects") {
        $year = (int)$_POST['year'];
        $stmt = $conn->prepare("SELECT id, course_name FROM course_details WHERE department = ? AND year = ? ORDER BY course_name ASC");
        $stmt->execute([$dept, $year]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    else if($action == "get_students") {
        $year = (int)$_POST['year'];
        $date = $_POST['date'];
        $course_id = $_POST['course_id'];

        // This query joins students with their attendance for this specific date/subject
        $sql = "SELECT s.id, s.name, s.roll_no, a.status 
                FROM student_details s 
                LEFT JOIN attendance_details a ON s.id = a.student_id 
                AND a.on_date = :dt AND a.course_id = :cid
                WHERE s.department = :dept AND s.year = :yr 
                ORDER BY s.roll_no ASC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'dt'   => $date,
            'cid'  => $course_id,
            'dept' => $dept,
            'yr'   => $year
        ]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    else if($action == "save_attendance") {
        $date = $_POST['date'];
        $course_id = $_POST['course_id'];
        $records = $_POST['attendance'];

        $conn->beginTransaction();

        // 1. Delete existing records for this date/subject to allow UPDATING
        $del = $conn->prepare("DELETE FROM attendance_details WHERE on_date = ? AND course_id = ?");
        $del->execute([$date, $course_id]);

        // 2. Insert new records
        $stmt = $conn->prepare("INSERT INTO attendance_details (student_id, course_id, on_date, status) VALUES (?, ?, ?, ?)");
        foreach($records as $r) {
            $stmt->execute([$r['student_id'], $course_id, $date, $r['status']]);
        }

        $conn->commit();
        echo json_encode(["status" => "OK", "message" => "Saved Successfully"]);
    }

    // --- ACTION: Generate Subject Report ---
    else if($action == "get_report") {
    $course_id = $_POST['course_id'];
    
    // We fetch the count of unique dates attendance was taken for this course
    $sql = "SELECT 
                s.roll_no, 
                s.name, 
                (SELECT COUNT(DISTINCT on_date) FROM attendance_details WHERE course_id = :cid1) as total_classes,
                SUM(CASE WHEN a.status = 'P' THEN 1 ELSE 0 END) as present_count
            FROM student_details s
            LEFT JOIN attendance_details a ON s.id = a.student_id AND a.course_id = :cid2
            WHERE s.department = :dept 
            AND s.year = (SELECT year FROM course_details WHERE id = :cid3)
            GROUP BY s.id
            ORDER BY s.roll_no ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'cid1' => $course_id,
        'cid2' => $course_id,
        'cid3' => $course_id,
        'dept' => $dept
    ]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // to add new student
    else if($action == "add_student") {
        $name = $_POST['stu_name'];
        $roll = $_POST['stu_roll'];
        $year = $_POST['stu_year'];
        $dept = $_SESSION['teacher_dept'];

        // 1. Check if Roll No already exists
        $check = $conn->prepare("SELECT id FROM student_details WHERE roll_no = ?");
        $check->execute([$roll]);
        
        if($check->rowCount() > 0) {
            echo json_encode(["status" => "ERR", "message" => "Roll Number already exists!"]);
        } else {
            // 2. Insert Student
            $stmt = $conn->prepare("INSERT INTO student_details (name, roll_no, year, department) VALUES (?, ?, ?, ?)");
            if($stmt->execute([$name, $roll, $year, $dept])) {
                echo json_encode(["status" => "OK", "message" => "Student added successfully"]);
            } else {
                echo json_encode(["status" => "ERR", "message" => "Failed to add student"]);
            }
        }
    }

    // to delete studet
    else if($action == "delete_student") {
        $stu_id = $_POST['stu_id'];

        try {
            $conn->beginTransaction();

            // 1. Delete student's attendance records first (Referential Integrity)
            $delAttendance = $conn->prepare("DELETE FROM attendance_details WHERE student_id = ?");
            $delAttendance->execute([$stu_id]);

            // 2. Delete the student
            $delStudent = $conn->prepare("DELETE FROM student_details WHERE id = ?");
            $delStudent->execute([$stu_id]);

            $conn->commit();
            echo json_encode(["status" => "OK", "message" => "Student and records deleted successfully"]);
        } catch (Exception $e) {
            $conn->rollBack();
            echo json_encode(["status" => "ERR", "message" => $e->getMessage()]);
        }
    }

} catch (Exception $e) {
    if(isset($conn)) $conn->rollBack();
    echo json_encode(["status" => "ERR", "message" => $e->getMessage()]);
}
?>