<?php
session_start();

// Check if user is logged in via session
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // If no session, check for auto-login cookie
    if (isset($_COOKIE['auto_login']) && $_COOKIE['auto_login'] === 'true') {
        // Auto-login successful - create session
        $_SESSION['logged_in'] = true;
    } else {
        // No valid session or auto-login cookie - redirect to login
        header('Location: log-in.php');
        exit;
    }
}

// If reached here, user is logged in - proceed with secure page content
?>
<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Store Cards Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
  
  body {
    font-family: 'Poppins', sans-serif;
  }
  
  .card-hover {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  }
  
  .card-hover:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 20px -5px rgba(0, 0, 0, 0.1), 0 8px 8px -5px rgba(0, 0, 0, 0.04);
  }
  
  .floating {
    animation: floating 3s ease-in-out infinite;
  }
  
  @keyframes floating {
    0% { transform: translate(0, 0px); }
    50% { transform: translate(0, 10px); }
    100% { transform: translate(0, -0px); }
  }
  
  .pulse-glow {
    animation: pulse-glow 2s infinite;
  }
  
  @keyframes pulse-glow {
    0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
    70% { box-shadow: 0 0 0 8px rgba(59, 130, 246, 0); }
    100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
  }
  
  .fade-in {
    animation: fadeIn 0.5s ease-in;
  }
  
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
  }
  
  .gradient-text {
    background: linear-gradient(90deg, #3b82f6, #8b5cf6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  
  .search-suggestions {
    max-height: 200px;
    overflow-y: auto;
  }
  
  /* Custom scrollbar */
  .search-suggestions::-webkit-scrollbar {
    width: 6px;
  }
  
  .search-suggestions::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
  }
  
  .search-suggestions::-webkit-scrollbar-thumb {
    background: #c5c5c5;
    border-radius: 10px;
  }
  
  .search-suggestions::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
  }
  /* Mobile (default) */
.flex.justify-between.items-center.py-4 {
  width: 100%;
}

/* Desktop */
@media (min-width: 1024px) {
  .flex.justify-between.items-center.py-4 {
    width: unset;
  }
  .flex.flex-col.md\:flex-row.justify-between.items-center.py-4.gap-4 {
    padding: 0px 0px;
  }
}
</style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-50 dark:from-gray-900 dark:to-gray-900 min-h-screen flex flex-col dark:text-gray-200">

<!-- 🌐 Modern Navbar with Search -->
<nav class="bg-white dark:bg-gray-800 shadow-md dark:shadow-md sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row justify-between items-center py-4 gap-4">
      <!-- Logo -->
       <div class="flex justify-between items-center py-4">
      <div class="flex items-center space-x-3">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-3 rounded-xl shadow-lg">
          <i class="fa-solid fa-store text-white text-xl"></i>
        </div>
        <div>
          <h1 class="text-2xl font-bold gradient-text">InfoHub</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400">Your Business Directory</p>
        </div>
      </div>
      
      <!-- Mobile menu button -->
      <div class="md:hidden">
        <button type="button" id="mobile-menu-button" class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 focus:outline-none focus:text-blue-600">
          <i class="fa-solid fa-bars text-xl"></i>
        </button>
      </div>
      </div>
      
      <!-- Search Bar -->
      <div class="relative w-full md:w-1/3">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <i class="fa-solid fa-magnifying-glass text-gray-400 dark:text-gray-500"></i>
        </div>
        <input type="text" id="search" placeholder="Search stores, owners, cities..." 
          class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl py-3 pl-10 pr-4 focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-700 focus:border-blue-500 dark:focus:border-blue-500 outline-none transition-all duration-300 text-gray-900 dark:text-gray-200 dark:placeholder-gray-500">
        <div id="suggestions" class="absolute bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 w-full rounded-xl mt-2 hidden shadow-xl z-50 search-suggestions"></div>
      </div>
      
      <!-- Navigation Links -->
      <div class="hidden md:flex space-x-2">
        <a href="index.php" class="flex items-center px-4 py-2 rounded-lg bg-blue-50 dark:bg-gray-700 text-blue-600 dark:text-blue-400 transition-all duration-300">
          <i class="fa-solid fa-home mr-2"></i>
          <span>Home</span>
        </a>
        <a href="add_store_form.php" class="flex items-center px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-300">
          <i class="fa-solid fa-plus mr-2"></i>
          <span>Add Store</span>
        </a>
        <a href="view_store_cards.php" class="flex items-center px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-300">
          <i class="fa-solid fa-list mr-2"></i>
          <span>All Stores</span>
        </a>
        <a href="logout.php" class="flex items-center px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900 hover:text-red-600 dark:hover:text-red-400 transition-all duration-300 ">
          <i class="fa-solid fa-right-from-bracket mr-2"></i>
          <span>Log Out</span>
        </a>
      </div>
    </div>
    
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden pb-4 border-t border-gray-200 dark:border-gray-700">
      <div class="flex flex-col space-y-2 mt-2">
        <a href="index.php" class="flex items-center px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-300">
          <i class="fa-solid fa-home mr-2"></i>
          <span>Home</span>
        </a>
        <a href="add_store_form.php" class="flex items-center px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-300">
          <i class="fa-solid fa-plus mr-2"></i>
          <span>Add Store</span>
        </a>
        <a href="view_store_cards.php" class="flex items-center px-4 py-2 rounded-lg bg-blue-50 dark:bg-gray-700 text-blue-600 dark:text-blue-400 transition-all duration-300">
          <i class="fa-solid fa-list mr-2"></i>
          <span>All Stores</span>
        </a>
        <button id="mobile-theme-toggle" class="flex items-center px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-300">
          <i class="fa-solid fa-moon mr-2"></i>
          <span>Dark Mode</span>
        </button>
      </div>
    </div>
  </div>
</nav>

<!-- Search Results Header -->
<div id="searchResultsHeader" class="hidden bg-blue-50 dark:bg-gray-800 border-b border-blue-200 dark:border-gray-700 py-4 px-6">
  <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center">
    <div class="flex items-center mb-3 sm:mb-0">
      <i class="fa-solid fa-search text-blue-600 dark:text-blue-400 mr-2"></i>
      <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Search Results for: <span id="searchQuery" class="text-blue-600 dark:text-blue-400"></span></h2>
    </div>
  </div>
</div>
<!-- 🧩 Card Container -->
<div id="cardContainer" class="p-4 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 fade-in">
<?php
$sql = "SELECT * FROM store_cards ORDER BY RAND() LIMIT 10";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $index = 0;
  while ($row = $result->fetch_assoc()) {
      $store_name = htmlspecialchars($row['store_name']);
      $owner_name = htmlspecialchars($row['owner_name']);
      $contact1 = htmlspecialchars($row['contact_number']);
      $contact2 = htmlspecialchars($row['contact_number2']);
      $city = htmlspecialchars($row['city']);
      $website = htmlspecialchars($row['website']);
      $notes = htmlspecialchars($row['notes']);
?>
  <div class="rounded-2xl overflow-hidden shadow-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white card-hover fade-in transform hover:scale-105 transition-all duration-300" style="animation-delay: <?= $index * 0.1 ?>s">
      <!-- Card Header -->
      <div class="p-4 border-b border-gray-200 dark:border-gray-600">
        <div class="flex items-center justify-between">
          <div class="bg-gray-100 dark:bg-gray-700 rounded-full px-3.5 py-2 me-2 flex-shrink-0">
            <i class="fa-solid fa-store text-xs text-gray-600 dark:text-gray-300"></i>
          </div>
          <h2 class="text-lg font-bold truncate flex-1"><?= $store_name ?></h2>
        </div>
        <?php if ($owner_name): ?>
        <div class="flex items-center mt-2">
          <i class="fa-solid fa-user text-xs mr-2 text-gray-500 dark:text-gray-400"></i>
          <p class="text-sm text-gray-600 dark:text-gray-300 truncate"><?= $owner_name ?></p>
        </div>
        <?php endif; ?>
      </div>

<div class="absolute top-3 right-3 flex space-x-2">
      <button 
        class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white px-3 py-1.5 rounded-md text-xs"
        onclick="openEditModal(<?= $row['id'] ?>)">
        <i class="fa-solid fa-pen"></i>
      </button>
      <button 
        class="bg-gray-100 dark:bg-gray-700 hover:bg-red-500 text-gray-900 dark:text-white px-3 py-1.5 rounded-md text-xs"
        onclick="deleteStore(<?= $row['id'] ?>)">
        <i class="fa-solid fa-trash"></i>
      </button>
</div>
      <!-- Card Body -->
      <div class="p-4 space-y-3">
        <!-- Contact Numbers -->
        <?php if ($contact1): ?>
        <div class="flex items-center justify-between bg-gray-100 dark:bg-gray-700 rounded-lg p-2 transition-all hover:bg-gray-200 dark:hover:bg-gray-600">
          <div class="flex items-center min-w-0 ml-3 flex-1">
            <i class="fa-solid fa-phone text-xs mr-2 text-gray-500 dark:text-gray-400 flex-shrink-0"></i>
            <span class="text-sm truncate"><?= $contact1 ?></span>
          </div>
          <button onclick="copyText('<?= $contact1 ?>')" class="bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500  px-3 py-1.5 rounded-md transition-colors flex-shrink-0 ml-2">
            <i class="fa-regular fa-copy text-xs text-gray-600 dark:text-gray-300"></i>
          </button>
        </div>
        <?php endif; ?>
        
        <?php if ($contact2): ?>
        <div class="flex items-center justify-between bg-gray-100 dark:bg-gray-700 rounded-lg p-2 transition-all hover:bg-gray-200 dark:hover:bg-gray-600">
          <div class="flex items-center min-w-0 ml-3 flex-1">
            <i class="fa-solid fa-phone-volume text-xs mr-2 text-gray-500 dark:text-gray-400 flex-shrink-0"></i>
            <span class="text-sm truncate"><?= $contact2 ?></span>
          </div>
          <button onclick="copyText('<?= $contact2 ?>')" class="bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500  px-3 py-1.5 rounded-md transition-colors flex-shrink-0 ml-2">
            <i class="fa-regular fa-copy text-xs text-gray-600 dark:text-gray-300"></i>
          </button>
        </div>
        <?php endif; ?>

        <!-- Location -->
        <?php if ($city): ?>
        <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-2">
          <i class="fa-solid fa-location-dot text-xs mr-2 ml-3 text-gray-500 dark:text-gray-400 flex-shrink-0"></i>
          <p class="text-sm truncate"><?= $city ?></p>
        </div>
        <?php endif; ?>
        
        <!-- Website -->
        <?php if ($website): ?>
        <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-2">
          <i class="fa-solid fa-globe text-xs mr-2 ml-3 text-gray-500 dark:text-gray-400 flex-shrink-0"></i>
          <a href="<?= $website ?>" target="_blank" class="text-sm underline truncate hover:opacity-80 transition-opacity">Visit Website</a>
        </div>
        <?php endif; ?>
        
        <!-- Notes -->
        <?php if ($notes): ?>
        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-2">
          <div class="flex items-start">
            <i class="fa-solid fa-quote-left text-xs mr-2 mt-0.5 text-gray-500 dark:text-gray-400 ml-3 flex-shrink-0"></i>
            <p class="text-xs italic leading-relaxed line-clamp-2"><?= $notes ?></p>
          </div>
        </div>
        <?php endif; ?>
      </div>
  </div>
<?php
  $index++;
  }
} else {
  echo '<div class="col-span-full text-center py-12 fade-in">';
  echo '<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 max-w-md mx-auto">';
  echo '<div class="floating mb-4">';
  echo '<i class="fa-solid fa-store text-6xl text-gray-300 dark:text-gray-600"></i>';
  echo '</div>';
  echo '<h3 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-2">No Store Cards Found</h3>';
  echo '<p class="text-gray-500 dark:text-gray-400 mb-6">Get started by adding your first store card to the directory.</p>';
  echo '<a href="add_store_card.php" class="bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-800 dark:to-indigo-800 text-white px-6 py-3 rounded-xl inline-flex items-center gap-2 hover:from-blue-700 hover:to-indigo-700 dark:hover:from-blue-900 dark:hover:to-indigo-900 transition-all">';
  echo '<i class="fa-solid fa-plus"></i>';
  echo '<span>Add Your First Store</span>';
  echo '</a>';
  echo '</div>';
  echo '</div>';
}
?>
</div>
<!-- Floating Action Button -->
<a href="add_store_card.php" class="fixed bottom-6 right-6 bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-800 dark:to-indigo-800 text-white p-4 rounded-full shadow-xl hover:from-blue-700 hover:to-indigo-700 dark:hover:from-blue-900 dark:hover:to-indigo-900 transition-all transform hover:scale-110 z-40 floating">
  <i class="fa-solid fa-plus text-xl"></i>
</a>
<!-- 🧠 EDIT MODAL -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70 flex items-center justify-center z-50 p-4 transition-theme">
  <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6 relative transition-theme">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="bg-gradient-to-r from-blue-500 to-purple-500 p-2 rounded-lg">
          <i class="fa-solid fa-pen-to-square text-white text-lg"></i>
        </div>
        <div>
          <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Edit Store Information</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">Update store details</p>
        </div>
      </div>
      <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
        <i class="fa-solid fa-times text-xl"></i>
      </button>
    </div>

    <form id="editForm" class="space-y-4">
      <input type="hidden" id="edit_id">
      
      <!-- Store Name -->
      <div class="space-y-2">
        <label class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
          <i class="fa-solid fa-store mr-2 text-blue-500"></i>
          Store Name
        </label>
        <input type="text" id="edit_store_name" placeholder="Enter store name" 
               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-600 focus:border-blue-500 dark:focus:border-blue-400 outline-none transition-theme text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400">
      </div>

      <!-- Owner Name -->
      <div class="space-y-2">
        <label class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
          <i class="fa-solid fa-user mr-2 text-green-500"></i>
          Owner Name
        </label>
        <input type="text" id="edit_owner_name" placeholder="Enter owner name" 
               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-600 focus:border-blue-500 dark:focus:border-blue-400 outline-none transition-theme text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400">
      </div>

      <!-- Contact Numbers -->
      <div class="grid grid-cols-2 gap-4">
        <div class="space-y-2">
          <label class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
            <i class="fa-solid fa-phone mr-2 text-purple-500"></i>
            Primary Contact
          </label>
          <input type="text" id="edit_contact_number" placeholder="Contact 1" 
                 class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-600 focus:border-blue-500 dark:focus:border-blue-400 outline-none transition-theme text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400">
        </div>
        <div class="space-y-2">
          <label class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
            <i class="fa-solid fa-phone-volume mr-2 text-orange-500"></i>
            Secondary Contact
          </label>
          <input type="text" id="edit_contact_number2" placeholder="Contact 2" 
                 class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-600 focus:border-blue-500 dark:focus:border-blue-400 outline-none transition-theme text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400">
        </div>
      </div>

      <!-- City -->
      <div class="space-y-2">
        <label class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
          <i class="fa-solid fa-location-dot mr-2 text-red-500"></i>
          City
        </label>
        <input type="text" id="edit_city" placeholder="Enter city" 
               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-600 focus:border-blue-500 dark:focus:border-blue-400 outline-none transition-theme text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400">
      </div>

      <!-- Website -->
      <div class="space-y-2">
        <label class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
          <i class="fa-solid fa-globe mr-2 text-cyan-500"></i>
          Website
        </label>
        <input type="text" id="edit_website" placeholder="https://example.com" 
               class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-600 focus:border-blue-500 dark:focus:border-blue-400 outline-none transition-theme text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400">
      </div>

      <!-- Notes -->
      <div class="space-y-2">
        <label class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
          <i class="fa-solid fa-sticky-note mr-2 text-yellow-500"></i>
          Notes
        </label>
        <textarea id="edit_notes" placeholder="Enter additional notes..." rows="3"
                  class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-600 focus:border-blue-500 dark:focus:border-blue-400 outline-none transition-theme text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 resize-none"></textarea>
      </div>

      <!-- Action Buttons -->
      <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
        <button type="button" onclick="closeEditModal()" 
                class="flex items-center px-5 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300">
          <i class="fa-solid fa-times mr-2"></i>
          Cancel
        </button>
        <button type="submit" 
                class="flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
          <i class="fa-solid fa-check mr-2"></i>
          Update Store
        </button>
      </div>
    </form>
  </div>
</div>
<script>
// ✏️ Open Edit Modal
function openEditModal(id) {
  fetch('get_store_details.php?id=' + id)
    .then(res => res.json())
    .then(data => {
      document.getElementById('edit_id').value = data.id;
      document.getElementById('edit_store_name').value = data.store_name;
      document.getElementById('edit_owner_name').value = data.owner_name;
      document.getElementById('edit_contact_number').value = data.contact_number;
      document.getElementById('edit_contact_number2').value = data.contact_number2;
      document.getElementById('edit_city').value = data.city;
      document.getElementById('edit_website').value = data.website;
      document.getElementById('edit_notes').value = data.notes;
      document.getElementById('editModal').classList.remove('hidden');
    });
}

function closeEditModal() {
  document.getElementById('editModal').classList.add('hidden');
}

// ✅ Submit Edit Form
document.getElementById('editForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData();
  formData.append('id', document.getElementById('edit_id').value);
  formData.append('store_name', document.getElementById('edit_store_name').value);
  formData.append('owner_name', document.getElementById('edit_owner_name').value);
  formData.append('contact_number', document.getElementById('edit_contact_number').value);
  formData.append('contact_number2', document.getElementById('edit_contact_number2').value);
  formData.append('city', document.getElementById('edit_city').value);
  formData.append('website', document.getElementById('edit_website').value);
  formData.append('notes', document.getElementById('edit_notes').value);

  fetch('update_store.php', { method: 'POST', body: formData })
  .then(res => res.json())
  .then(resp => {
    Swal.fire({ icon: resp.status, title: resp.message, timer: 1500, showConfirmButton: false });
    if (resp.status === 'success') {
      setTimeout(() => location.reload(), 1000);
    }
  });
});

// 🗑️ Delete Store
function deleteStore(id) {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This will delete the store permanently!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#e3342f',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      fetch('delete_store.php?id=' + id)
      .then(res => res.json())
      .then(resp => {
        Swal.fire({ icon: resp.status, title: resp.message, timer: 1500, showConfirmButton: false });
        if (resp.status === 'success') {
          setTimeout(() => location.reload(), 1000);
        }
      });
    }
  });
}
// 📋 Copy to Clipboard with Toast
function copyText(text) {
  navigator.clipboard.writeText(text).then(() => {
    // Show custom toast notification
    showToast('Phone number copied!', text);
  }).catch(err => {
    console.error('Failed to copy: ', err);
    showToast('Failed to copy!', 'Please try again');
  });
}

// Custom Toast Function
function showToast(title, text) {
  const toast = document.createElement('div');
  toast.className = 'fixed top-4 right-4 bg-green-500 text-white p-4 rounded-lg shadow-xl z-50 transform transition-all duration-300 translate-x-0 opacity-100';
  toast.innerHTML = `
    <div class="flex items-center">
      <i class="fa-solid fa-check-circle mr-2"></i>
      <div>
        <div class="font-semibold">${title}</div>
        <div class="text-sm opacity-90">${text}</div>
      </div>
    </div>
  `;
  
  document.body.appendChild(toast);
  
  // Remove toast after 3 seconds
  setTimeout(() => {
    toast.style.transform = 'translateX(100%)';
    toast.style.opacity = '0';
    setTimeout(() => {
      document.body.removeChild(toast);
    }, 300);
  }, 3000);
}

// 🔍 Live Suggestion Dropdown
const searchInput = document.getElementById('search');
const suggestionBox = document.getElementById('suggestions');
const searchResultsHeader = document.getElementById('searchResultsHeader');
const searchQuery = document.getElementById('searchQuery');
const viewAllResults = document.getElementById('viewAllResults');

searchInput.addEventListener('input', function() {
  const query = this.value.trim();
  if (query.length < 2) { 
    suggestionBox.classList.add('hidden'); 
    return; 
  }

  fetch('search_store_sug.php?q=' + encodeURIComponent(query))
  .then(res => res.text())
  .then(text => {
    console.log('Response text for suggestions:', text);
    try {
      const trimmedText = text.trim();
      return JSON.parse(trimmedText);
    } catch (e) {
      console.error('JSON parse error:', e.message, 'Response text:', text);
      throw e;
    }
  })
  .then(data => {
      if (data.length > 0) {
          suggestionBox.innerHTML = data.map(item => 
              `<div class='px-4 py-3 hover:bg-blue-50 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-100 dark:border-gray-600 last:border-0 flex items-center' onclick='selectSuggestion("${item.store_name.replace(/'/g, "\\'")}")'>
                  <div class="bg-blue-100 dark:bg-gray-700 text-blue-600 dark:text-blue-400 p-2 rounded-lg mr-3">
                    <i class="fa-solid fa-store text-sm"></i>
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="font-medium truncate text-gray-900 dark:text-gray-200">${item.store_name}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 truncate">${item.owner_name || ''} ${item.city ? ' • ' + item.city : ''}</div>
                  </div>
              </div>`
          ).join('');
          suggestionBox.classList.remove('hidden');
      } else {
          suggestionBox.innerHTML = `<div class='px-4 py-3 text-gray-500 dark:text-gray-400 text-center'><i class="fa-solid fa-search mr-2"></i>No results found</div>`;
          suggestionBox.classList.remove('hidden');
      }
  })
  .catch(err => {
      console.error('Error fetching suggestions:', err);
      suggestionBox.innerHTML = `<div class='px-4 py-3 text-red-500 dark:text-red-400 text-center'><i class="fa-solid fa-exclamation-triangle mr-2"></i>Error loading suggestions</div>`;
      suggestionBox.classList.remove('hidden');
  });
});

function selectSuggestion(name) {
  searchInput.value = name;
  suggestionBox.classList.add('hidden');
  // Trigger search when suggestion is selected
  performSearch();
}

// Hide suggestions when clicking outside
document.addEventListener('click', function(e) {
  if (!searchInput.contains(e.target) && !suggestionBox.contains(e.target)) {
    suggestionBox.classList.add('hidden');
  }
});

// Perform search function
function performSearch() {
  const query = searchInput.value.trim();
  if (query.length < 1) return;

  // Show search results header
  searchQuery.textContent = query;
  searchResultsHeader.classList.remove('hidden');
  
  fetch('search_store.php?q=' + encodeURIComponent(query) + '&show=1')
  .then(res => res.text())
  .then(html => {
      document.getElementById('cardContainer').innerHTML = html;
      suggestionBox.classList.add('hidden');
      
      // Add fade-in animation to new cards
      const cards = document.querySelectorAll('#cardContainer > div');
      cards.forEach((card, index) => {
        card.classList.add('fade-in');
        card.style.animationDelay = `${index * 0.1}s`;
      });
  });
}

// View All Contact List Button
viewAllResults.addEventListener('click', function() {
  const query = searchInput.value.trim();
  if (query.length < 1) return;
  
  // Show all contacts in a modal or new view
  fetch('search_store.php?q=' + encodeURIComponent(query) + '&show=all')
  .then(res => res.text())
  .then(html => {
      // Create modal for all contacts
      const modal = document.createElement('div');
      modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
      modal.innerHTML = `
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
          <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200">All Contacts for: "${query}"</h3>
            <button onclick="this.closest('.fixed').remove()" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
              <i class="fa-solid fa-times text-xl"></i>
            </button>
          </div>
          <div class="p-6 overflow-y-auto max-h-[70vh]">
            ${html}
          </div>
        </div>
      `;
      document.body.appendChild(modal);
  });
});

// Search on Enter key
searchInput.addEventListener('keypress', function(e) {
  if (e.key === 'Enter') {
    performSearch();
  }
});

// Mobile menu toggle
document.getElementById('mobile-menu-button').addEventListener('click', function() {
  const menu = document.getElementById('mobile-menu');
  menu.classList.toggle('hidden');
});

// Add animation to cards on page load
document.addEventListener('DOMContentLoaded', function() {
  const cards = document.querySelectorAll('#cardContainer > div');
  cards.forEach((card, index) => {
    card.classList.add('fade-in');
    card.style.animationDelay = `${index * 0.1}s`;
  });
});

// Dark Mode Handling
function setTheme(theme) {
  if (theme === 'dark') {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
  localStorage.theme = theme;
}

const savedTheme = localStorage.theme;
const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

if (savedTheme) {
  setTheme(savedTheme);
} else {
  setTheme(prefersDark ? 'dark' : 'light');
}

function updateToggleButtons() {
  const toggles = [document.getElementById('theme-toggle'), document.getElementById('mobile-theme-toggle')];
  toggles.forEach(toggle => {
    if (toggle) {
      const icon = toggle.querySelector('i');
      const span = toggle.querySelector('span');
      if (document.documentElement.classList.contains('dark')) {
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
        span.textContent = 'Light Mode';
      } else {
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
        span.textContent = 'Dark Mode';
      }
    }
  });
}

updateToggleButtons();

const themeToggles = [document.getElementById('theme-toggle'), document.getElementById('mobile-theme-toggle')];
themeToggles.forEach(toggle => {
  if (toggle) {
    toggle.addEventListener('click', () => {
      const isDark = document.documentElement.classList.contains('dark');
      setTheme(isDark ? 'light' : 'dark');
      updateToggleButtons();
    });
  }
});
</script>
</body>
</html>