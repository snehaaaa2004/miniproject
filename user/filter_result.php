<?php
session_start();
include('../connect.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header("Location: ../login.html");
    exit();
}

$specializations = isset($_POST['specialization']) ? (array)$_POST['specialization'] : [];
$genders         = isset($_POST['gender']) ? (array)$_POST['gender'] : [];
$language        = isset($_POST['language']) ? trim($_POST['language']) : '';
$modes           = isset($_POST['mode']) ? (array)$_POST['mode'] : [];
$availabilities  = isset($_POST['availability']) ? (array)$_POST['availability'] : [];

$sql = "SELECT t.*, u.name, u.email FROM therapists t JOIN users u ON t.user_id = u.id WHERE t.approved = 1";
$whereConditions = [];

// Filters
if (!empty($specializations)) {
    $specs = array_map(fn($s) => "LOWER(t.specialization) LIKE LOWER('%" . mysqli_real_escape_string($conn, trim($s)) . "%')", $specializations);
    $whereConditions[] = '(' . implode(" OR ", $specs) . ')';
}
if (!empty($genders)) {
    $genderConds = array_map(fn($g) => "LOWER(t.gender) = LOWER('" . mysqli_real_escape_string($conn, trim($g)) . "')", $genders);
    $whereConditions[] = '(' . implode(" OR ", $genderConds) . ')';
}
if (!empty($language)) {
    $escapedLang = mysqli_real_escape_string($conn, $language);
    $whereConditions[] = "LOWER(t.language) LIKE LOWER('%$escapedLang%')";
}
if (!empty($modes)) {
    $modeConds = array_map(fn($m) => "LOWER(t.mode) LIKE LOWER('%" . mysqli_real_escape_string($conn, trim($m)) . "%')", $modes);
    $whereConditions[] = '(' . implode(" OR ", $modeConds) . ')';
}
if (!empty($availabilities)) {
    $availConds = array_map(fn($a) => "LOWER(t.availability) LIKE LOWER('%" . mysqli_real_escape_string($conn, trim($a)) . "%')", $availabilities);
    $whereConditions[] = '(' . implode(" OR ", $availConds) . ')';
}

if (!empty($whereConditions)) {
    $sql .= " AND " . implode(" AND ", $whereConditions);
}

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Filtered Therapists</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 2rem;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .card {
            background: #fff;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
            position: relative;
        }
        .card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 6px;
        }
        .card h4 {
            margin: 0.8rem 0 0.3rem;
            color: #2f6690;
        }
        .card p {
            margin: 0.2rem 0;
            font-size: 0.9rem;
            color: #444;
        }
        .book-btn {
            margin-top: 1rem;
            background: #2f6690;
            color: #fff;
            border: none;
            padding: 0.6rem 1rem;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-container {
            display: none;
            margin-top: 1rem;
            padding: 1rem;
            background: #f8f8f8;
            border-radius: 6px;
            border: 1px solid #ddd;
        }
        .form-container label {
            display: block;
            margin-top: 0.6rem;
            font-weight: bold;
        }
        .form-container input,
        .form-container select,
        .form-container textarea {
            width: 100%;
            margin-top: 0.3rem;
            padding: 0.5rem;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .form-container button {
            margin-top: 0.8rem;
            background: #28a745;
            color: #fff;
            border: none;
            padding: 0.6rem 1rem;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">Filtered Therapist Profiles</h2>
<div class="grid">
<?php
while ($row = mysqli_fetch_assoc($result)) {
    $therapistId = $row['id'];
    $imagePath = "../uploads/" . htmlspecialchars($row['image']);
    $defaultImage = "../uploads/default.png";
    $finalImage = (!empty($row['image']) && file_exists($imagePath)) ? $imagePath : $defaultImage;

    echo "<div class='card'>";
    echo "<img src='$finalImage' alt='Therapist Image'>";
    echo "<h4>" . htmlspecialchars($row['name']) . "</h4>";
    echo "<p><strong>Email:</strong> " . htmlspecialchars($row['email']) . "</p>";
    echo "<p><strong>Gender:</strong> " . htmlspecialchars($row['gender']) . "</p>";
    echo "<p><strong>Specialization:</strong> " . htmlspecialchars($row['specialization']) . "</p>";
    echo "<p><strong>Experience:</strong> " . htmlspecialchars($row['experience']) . " years</p>";
    echo "<p><strong>Language:</strong> " . htmlspecialchars($row['language']) . "</p>";
    echo "<p><strong>Mode:</strong> " . htmlspecialchars($row['mode']) . "</p>";
    echo "<p><strong>Availability:</strong> " . htmlspecialchars($row['availability']) . "</p>";
    echo "<p><strong>Fees:</strong> $" . htmlspecialchars($row['fees']) . "</p>";


    // Book Button & Form
    echo "<button class='book-btn' onclick=\"toggleForm('form_$therapistId')\">Book Appointment</button>";
    echo "<div class='form-container' id='form_$therapistId'>";
    echo "<form method='POST' action='book_appointment.php'>";
    
    // Time options from availability
    echo "<label for='time_$therapistId'>Time</label>";
echo "<label for='time_$therapistId'>Time</label>";
echo "<select name='appointment_time' required>";

$availabilityRaw = $row['availability'];
$timeSlots = [];

// Extract time range using regex: e.g., "Afternoon (12:00 PM - 05:00 PM)"
if (preg_match('/\((\d{1,2}:\d{2}\s[APM]{2})\s*-\s*(\d{1,2}:\d{2}\s[APM]{2})\)/', $availabilityRaw, $matches)) {
    $startTime = strtotime($matches[1]);
    $endTime = strtotime($matches[2]);

    while ($startTime < $endTime) {
        $timeSlots[] = date("h:i A", $startTime);
        $startTime = strtotime("+15 minutes", $startTime);
    }
}

if (!empty($timeSlots)) {
    foreach ($timeSlots as $slot) {
        echo "<option value='$slot'>$slot</option>";
    }
} else {
    echo "<option disabled>No available slots</option>";
}

echo "</select>";




    // Mode options based on therapist data
    echo "<label>Choose Mode:</label>";
    echo "<select name='mode' required>";
    $modesAvailable = explode(',', $row['mode']);
    foreach ($modesAvailable as $modeOption) {
        $cleaned = htmlspecialchars(trim($modeOption));
        echo "<option value='$cleaned'>$cleaned</option>";
    }
    echo "</select>";

    echo "<label>Appointment Date:</label>";
    echo "<input type='date' name='appointment_date' required min='" . date('Y-m-d') . "'>";

    echo "<label>Phone:</label>";
    echo "<input type='tel' name='phone' required>";

    echo "<label>Description (Optional):</label>";
    echo "<textarea name='description' rows='3'></textarea>";

    echo "<input type='hidden' name='therapist_id' value='$therapistId'>";
    echo "<button type='submit'>Submit Appointment</button>";
    echo "</form>";
    echo "</div>";

    echo "</div>";
}
?>
</div>

<script>
function toggleForm(id) {
    const form = document.getElementById(id);
    form.style.display = form.style.display === "block" ? "none" : "block";
}
</script>

</body>
</html>
