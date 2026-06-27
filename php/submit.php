<?php
// ── Booking Submission Handler ──────────────────────────────────────────────
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// ── Collect & sanitize inputs ───────────────────────────────────────────────
$couple_name = trim($_POST['couple_name'] ?? '');
$contact     = trim($_POST['contact']     ?? '');
$email       = trim($_POST['email']       ?? '');
$location    = trim($_POST['location']    ?? '');
$package     = trim($_POST['package']     ?? '');
$story_notes = trim($_POST['story_notes'] ?? '');
$date        = trim($_POST['date']        ?? '');

// ── Validation ──────────────────────────────────────────────────────────────
$errors = [];

// Couple name: letters, spaces, ampersand, and "and" only — no special chars
if ($couple_name === '') {
    $errors[] = 'Couple name is required.';
} elseif (!preg_match('/^[A-Za-z\s&]+$/', $couple_name)) {
    $errors[] = 'Couple name must contain letters, spaces, or "and" only — no special characters.';
} elseif (mb_strlen($couple_name) > 150) {
    $errors[] = 'Couple name is too long (max 150 characters).';
}

// Contact number
if ($contact === '') {
    $errors[] = 'Contact number is required.';
} elseif (!preg_match('/^[0-9+\-\s()]{7,20}$/', $contact)) {
    $errors[] = 'Please enter a valid contact number.';
}

// Email
if ($email === '') {
    $errors[] = 'Email address is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

// Location
if ($location === '') {
    $errors[] = 'Location of shoot is required.';
}

// Package
$validPackages = ['Pilot', 'Mainstream', 'Blockbuster', 'Travel'];
if (!in_array($package, $validPackages, true)) {
    $errors[] = 'Please select a valid package.';
}

// Date — must be provided and must fall on Mon, Sat, or Sun
if ($date === '') {
    $errors[] = 'Please select a date from the calendar.';
} else {
    $dateObj = DateTime::createFromFormat('Y-m-d', $date);
    if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
        $errors[] = 'Invalid date format.';
    } else {
        $dayOfWeek = (int)$dateObj->format('N'); // 1=Mon … 7=Sun
        if (!in_array($dayOfWeek, [1, 6, 7], true)) {
            $errors[] = 'Selected date must be a Monday, Saturday, or Sunday.';
        }
        // Must not be in the past
        $today = new DateTime('today');
        if ($dateObj < $today) {
            $errors[] = 'Selected date cannot be in the past.';
        }
    }
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// ── Insert into database ─────────────────────────────────────────────────────
try {
    $pdo = getDB();
    $sql = "INSERT INTO bookings
              (couple_name, contact, email, location, package, story_notes, date)
            VALUES
              (:couple_name, :contact, :email, :location, :package, :story_notes, :date)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':couple_name' => $couple_name,
        ':contact'     => $contact,
        ':email'       => $email,
        ':location'    => $location,
        ':package'     => $package,
        ':story_notes' => $story_notes,
        ':date'        => $date,
    ]);

    $newId = $pdo->lastInsertId();

    echo json_encode([
        'success'   => true,
        'message'   => 'Booking confirmed! We\'ll be in touch soon.',
        'couple_id' => $newId,
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Could not save your booking. Please try again later.',
        // Uncomment below for debugging only — remove in production:
        // 'debug' => $e->getMessage(),
    ]);
}
