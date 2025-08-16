<?php
session_start();
include('../connect.php');

// Ensure the user is logged in and is a therapist
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Therapist') {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

// Get form inputs safely
$specialization = $_POST['specialization'] ?? '';
$experience     = $_POST['experience'] ?? '';
$language       = $_POST['language'] ?? '';
$gender         = $_POST['gender'] ?? '';
$fees           = $_POST['fees'] ?? 0; // ✅ Ensure fees is defined
$from_time      = $_POST['from_time'] ?? '';
$to_time        = $_POST['to_time'] ?? '';

// Convert to availability format
$availability = "";
if (!empty($from_time) && !empty($to_time)) {
    $fromMinutes = strtotime($from_time);
    $toMinutes   = strtotime($to_time);

    if ($fromMinutes == strtotime('08:00 AM') && $toMinutes == strtotime('12:00 PM')) {
        $availability = "Mornings (8AM-12PM)";
    } elseif ($fromMinutes == strtotime('12:00 PM') && $toMinutes == strtotime('05:00 PM')) {
        $availability = "Afternoons (12PM-5PM)";
    } elseif ($fromMinutes == strtotime('05:00 PM') && $toMinutes == strtotime('09:00 PM')) {
        $availability = "Evenings (5PM-9PM)";
    } elseif ($from_time === 'Weekends') {
        $availability = "Weekends";
    } else {
        $availability = "$from_time - $to_time";
    }
}

// Convert mode array to string
$modeArray  = $_POST['mode'] ?? [];
$modeString = implode(', ', $modeArray);

// Generate custom therapist ID like THR001
$check = mysqli_query($conn, "SELECT COUNT(*) AS total FROM therapists");
$countData = mysqli_fetch_assoc($check);
$nextId = $countData['total'] + 1;
$therapistId = 'THR' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

// Handle image upload
$imagePath = ''; // Final value to store in DB
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $imageName = $therapistId . '_' . time() . '.' . $ext;

    $uploadDir = 'uploads/'; // Relative path for DB
    $fullUploadPath = '../' . $uploadDir; // Physical path for saving file

    if (!is_dir($fullUploadPath)) {
        mkdir($fullUploadPath, 0777, true);
    }

    $targetPath = $fullUploadPath . $imageName;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        $imagePath = $uploadDir . $imageName; // ✅ Store path for DB
    }
}

// Insert into therapists table
$sql = "INSERT INTO therapists (id, user_id, gender, specialization, experience, language, availability, mode, fees, image)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param(
    $stmt,
    "ssssssssds",
    $therapistId,
    $user_id,
    $gender,
    $specialization,
    $experience,
    $language,
    $availability,
    $modeString,
    $fees,
    $imagePath
);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('Profile submitted successfully!'); window.location.href='../therapist/therapihome.php';</script>";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
