<?php
include 'config.php';
header('Content-Type: application/json; charset=UTF-8');

// Create uploads directory if not exists
$uploadDir = "uploads/";
if (!file_exists($uploadDir)) {
  mkdir($uploadDir, 0777, true);
}

$response = ['status' => 'error', 'message' => 'Something went wrong.'];

try {
  $store_name = $_POST['store_name'] ?? '';
  $owner_name = $_POST['owner_name'] ?? '';
  $contact_number = $_POST['contact_number'] ?? '';
  $contact_number2 = $_POST['contact_number2'] ?? '';
  $address = $_POST['address'] ?? '';
  $city = $_POST['city'] ?? '';
  $website = $_POST['website'] ?? '';
  $notes = $_POST['notes'] ?? '';

  $front_image = '';
  $back_image = '';

  // File upload - front
  if (!empty($_FILES['front_image']['name'])) {
    $fileTmp = $_FILES['front_image']['tmp_name'];
    $fileName = uniqid() . "_front_" . basename($_FILES['front_image']['name']);
    $filePath = $uploadDir . $fileName;
    if (move_uploaded_file($fileTmp, $filePath)) {
      $front_image = $filePath;
    }
  }

  // File upload - back
  if (!empty($_FILES['back_image']['name'])) {
    $fileTmp = $_FILES['back_image']['tmp_name'];
    $fileName = uniqid() . "_back_" . basename($_FILES['back_image']['name']);
    $filePath = $uploadDir . $fileName;
    if (move_uploaded_file($fileTmp, $filePath)) {
      $back_image = $filePath;
    }
  }

  $stmt = $conn->prepare("INSERT INTO store_cards (store_name, owner_name, contact_number, contact_number2, address, city, website, notes, front_image, back_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssssssss", $store_name, $owner_name, $contact_number, $contact_number2, $address, $city, $website, $notes, $front_image, $back_image);

  if ($stmt->execute()) {
    $response = ['status' => 'success', 'message' => 'Store card added successfully!'];
  } else {
    $response = ['status' => 'error', 'message' => 'Database insert failed.'];
  }

  $stmt->close();
} catch (Exception $e) {
  $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
exit;
?>
