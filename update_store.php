<?php
include 'config.php';
$id = intval($_POST['id']);
$store_name = $_POST['store_name'];
$owner_name = $_POST['owner_name'];
$contact_number = $_POST['contact_number'];
$contact_number2 = $_POST['contact_number2'];
$city = $_POST['city'];
$website = $_POST['website'];
$notes = $_POST['notes'];

$sql = "UPDATE store_cards SET 
  store_name='$store_name',
  owner_name='$owner_name',
  contact_number='$contact_number',
  contact_number2='$contact_number2',
  city='$city',
  website='$website',
  notes='$notes'
  WHERE id=$id";

if ($conn->query($sql)) {
  echo json_encode(['status' => 'success', 'message' => 'Store updated successfully!']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Failed to update store!']);
}
?>
