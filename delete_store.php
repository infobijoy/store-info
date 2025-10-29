<?php
include 'config.php';
$id = intval($_GET['id']);
$sql = "DELETE FROM store_cards WHERE id=$id";
if ($conn->query($sql)) {
  echo json_encode(['status' => 'success', 'message' => 'Store deleted successfully!']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Failed to delete store!']);
}
?>
