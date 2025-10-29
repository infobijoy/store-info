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
<title>Add Store Card - InfoHub</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
  
  body {
    font-family: 'Poppins', sans-serif;
  }
  
  .gradient-text {
    background: linear-gradient(90deg, #3b82f6, #8b5cf6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  
  .fade-in {
    animation: fadeIn 0.5s ease-in;
  }
  
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }
  
  .form-input:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }
  
  .file-upload {
    transition: all 0.3s ease;
  }
  
  .file-upload:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
  }
</style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-gray-900 min-h-screen dark:text-gray-200">

<!-- 🌐 Modern Navbar -->
<nav class="bg-white dark:bg-gray-800 shadow-md sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center py-4">
      <!-- Logo -->
      <div class="flex items-center space-x-3">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-3 rounded-xl shadow-lg">
          <i class="fa-solid fa-store text-white text-xl"></i>
        </div>
        <div>
          <h1 class="text-2xl font-bold gradient-text">InfoHub</h1>
          <p class="text-xs text-gray-500 dark:text-gray-400">Your Business Directory</p>
        </div>
      </div>
      
      <!-- Desktop Navigation Links -->
      <div class="hidden md:flex space-x-2">
        <a href="index.php" class="flex items-center px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-300">
          <i class="fa-solid fa-home mr-2"></i>
          <span>Home</span>
        </a>
        <a href="add_store_form.php" class="flex items-center px-4 py-2 rounded-lg bg-blue-50 dark:bg-gray-700 text-blue-600 dark:text-blue-400 transition-all duration-300">
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

      <!-- Mobile menu button -->
      <div class="md:hidden">
        <button type="button" id="mobile-menu-button" class="text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 focus:outline-none focus:text-blue-600">
          <i class="fa-solid fa-bars text-xl"></i>
        </button>
      </div>
    </div>
    
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden pb-4 border-t border-gray-200 dark:border-gray-700">
      <div class="flex flex-col space-y-2 mt-2">
        <a href="index.php" class="flex items-center px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-300">
          <i class="fa-solid fa-home mr-2"></i>
          <span>Home</span>
        </a>
        <a href="add_store_form.php" class="flex items-center px-4 py-2 rounded-lg bg-blue-50 dark:bg-gray-700 text-blue-600 dark:text-blue-400 transition-all duration-300">
          <i class="fa-solid fa-plus mr-2"></i>
          <span>Add Store</span>
        </a>
        <a href="view_store_cards.php" class="flex items-center px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-300">
          <i class="fa-solid fa-list mr-2"></i>
          <span>All Stores</span>
        </a>
      </div>
    </div>
  </div>
</nav>

<!-- Main Form Content -->
<div class="flex-1 py-8 px-4 sm:px-6 lg:px-8">
  <div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="text-center mb-8 fade-in">
      <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-2">Add New Store Card</h2>
      <p class="text-gray-600 dark:text-gray-400">Fill in the details below to add a new store to your directory</p>
    </div>

    <!-- Form Container -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden fade-in" style="animation-delay: 0.1s">
      <form id="storeForm" enctype="multipart/form-data" class="p-6 sm:p-8">
        <!-- Store Information Section -->
        <div class="mb-8">
          <div class="flex items-center mb-6">
            <div class="bg-blue-100 dark:bg-gray-700 p-2 rounded-lg mr-3">
              <i class="fa-solid fa-store text-blue-600 dark:text-gray-100"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Store Information</h3>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Store Name -->
            <div class="md:col-span-2">
              <label for="store_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fa-solid fa-signature mr-1 text-blue-500 dark:text-gray-400"></i>
                Store Name <span class="text-red-500">*</span>
              </label>
              <input type="text" id="store_name" name="store_name" required
                     class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl form-input focus:ring-2 focus:ring-blue-300 dark:focus:ring-gray-500 focus:border-blue-500 dark:focus:border-gray-500 outline-none transition-all duration-300 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 dark:placeholder-gray-500"
                     placeholder="Enter store name">
            </div>

            <!-- Owner Name -->
            <div>
              <label for="owner_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fa-solid fa-user mr-1 text-blue-500 dark:text-gray-400"></i>
                Owner Name
              </label>
              <input type="text" id="owner_name" name="owner_name"
                     class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl form-input focus:ring-2 focus:ring-blue-300 dark:focus:ring-gray-500 focus:border-blue-500 dark:focus:border-gray-500 outline-none transition-all duration-300 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 dark:placeholder-gray-500"
                     placeholder="Enter owner name">
            </div>

            <!-- Email -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fa-solid fa-envelope mr-1 text-blue-500 dark:text-gray-400"></i>
                Email Address
              </label>
              <input type="email" id="email" name="email"
                     class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl form-input focus:ring-2 focus:ring-blue-300 dark:focus:ring-gray-500 focus:border-blue-500 dark:focus:border-gray-500 outline-none transition-all duration-300 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 dark:placeholder-gray-500"
                     placeholder="Enter email address">
            </div>
          </div>
        </div>

        <!-- Contact Information Section -->
        <div class="mb-8">
          <div class="flex items-center mb-6">
            <div class="bg-green-100 dark:bg-gray-700 p-2 rounded-lg mr-3">
              <i class="fa-solid fa-phone text-green-600 dark:text-gray-100"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Contact Information</h3>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Primary Contact -->
            <div>
              <label for="contact_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fa-solid fa-phone mr-1 text-green-500 dark:text-gray-400"></i>
                Primary Contact Number
              </label>
              <input type="tel" id="contact_number" name="contact_number"
                     class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl form-input focus:ring-2 focus:ring-blue-300 dark:focus:ring-gray-500 focus:border-blue-500 dark:focus:border-gray-500 outline-none transition-all duration-300 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 dark:placeholder-gray-500"
                     placeholder="Enter primary contact">
            </div>

            <!-- Secondary Contact -->
            <div>
              <label for="contact_number2" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fa-solid fa-phone-volume mr-1 text-green-500 dark:text-gray-400"></i>
                Secondary Contact Number
              </label>
              <input type="tel" id="contact_number2" name="contact_number2"
                     class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl form-input focus:ring-2 focus:ring-blue-300 dark:focus:ring-gray-500 focus:border-blue-500 dark:focus:border-gray-500 outline-none transition-all duration-300 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 dark:placeholder-gray-500"
                     placeholder="Enter secondary contact">
            </div>
          </div>
        </div>

        <!-- Location Information Section -->
        <div class="mb-8">
          <div class="flex items-center mb-6">
            <div class="bg-purple-100 dark:bg-gray-700 p-2 rounded-lg mr-3">
              <i class="fa-solid fa-location-dot text-purple-600 dark:text-gray-100"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Location Information</h3>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Address -->
            <div class="md:col-span-2">
              <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fa-solid fa-map-marker-alt mr-1 text-purple-500 dark:text-gray-400"></i>
                Address
              </label>
              <input type="text" id="address" name="address"
                     class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl form-input focus:ring-2 focus:ring-blue-300 dark:focus:ring-gray-500 focus:border-blue-500 dark:focus:border-gray-500 outline-none transition-all duration-300 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 dark:placeholder-gray-500"
                     placeholder="Enter full address">
            </div>

            <!-- City -->
            <div>
              <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fa-solid fa-city mr-1 text-purple-500 dark:text-gray-400"></i>
                City
              </label>
              <input type="text" id="city" name="city"
                     class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl form-input focus:ring-2 focus:ring-blue-300 dark:focus:ring-gray-500 focus:border-blue-500 dark:focus:border-gray-500 outline-none transition-all duration-300 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 dark:placeholder-gray-500"
                     placeholder="Enter city">
            </div>

            <!-- Website -->
            <div>
              <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fa-solid fa-globe mr-1 text-purple-500 dark:text-gray-400"></i>
                Website
              </label>
              <input type="url" id="website" name="website"
                     class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl form-input focus:ring-2 focus:ring-blue-300 dark:focus:ring-gray-500 focus:border-blue-500 dark:focus:border-gray-500 outline-none transition-all duration-300 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 dark:placeholder-gray-500"
                     placeholder="https://example.com">
            </div>
          </div>
        </div>

        <!-- Images Section -->
        <div class="mb-8">
          <div class="flex items-center mb-6">
            <div class="bg-yellow-100 dark:bg-gray-700 p-2 rounded-lg mr-3">
              <i class="fa-solid fa-images text-yellow-600 dark:text-gray-100"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Store Images</h3>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Front Image -->
            <div>
              <label for="front_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fa-solid fa-image mr-1 text-yellow-500 dark:text-gray-400"></i>
                Front Image
              </label>
              <div class="file-upload border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center hover:border-blue-400 dark:hover:border-gray-400 transition-all duration-300 cursor-pointer">
                <input type="file" id="front_image" name="front_image" accept="image/*" 
                       class="hidden" onchange="previewImage(this, 'frontPreview')">
                <i class="fa-solid fa-upload text-3xl text-gray-400 mb-3"></i>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Click to upload front image</p>
                <p class="text-xs text-gray-500">PNG, JPG, JPEG (Max 5MB)</p>
                <div id="frontPreview" class="mt-3 hidden">
                  <img class="mx-auto h-32 rounded-lg shadow-md">
                </div>
              </div>
            </div>

            <!-- Back Image -->
            <div>
              <label for="back_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fa-solid fa-image mr-1 text-yellow-500 dark:text-gray-400"></i>
                Back Image
              </label>
              <div class="file-upload border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center hover:border-blue-400 dark:hover:border-gray-400 transition-all duration-300 cursor-pointer">
                <input type="file" id="back_image" name="back_image" accept="image/*" 
                       class="hidden" onchange="previewImage(this, 'backPreview')">
                <i class="fa-solid fa-upload text-3xl text-gray-400 mb-3"></i>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Click to upload back image</p>
                <p class="text-xs text-gray-500">PNG, JPG, JPEG (Max 5MB)</p>
                <div id="backPreview" class="mt-3 hidden">
                  <img class="mx-auto h-32 rounded-lg shadow-md">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Notes Section -->
        <div class="mb-8">
          <div class="flex items-center mb-6">
            <div class="bg-red-100 dark:bg-gray-700 p-2 rounded-lg mr-3">
              <i class="fa-solid fa-sticky-note text-red-600 dark:text-gray-100"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Additional Notes</h3>
          </div>
          
          <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              <i class="fa-solid fa-pen mr-1 text-red-500 dark:text-gray-400"></i>
              Notes
            </label>
            <textarea id="notes" name="notes" rows="4"
                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl form-input focus:ring-2 focus:ring-blue-300 dark:focus:ring-gray-500 focus:border-blue-500 dark:focus:border-gray-500 outline-none transition-all duration-300 resize-none bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 dark:placeholder-gray-500"
                      placeholder="Enter any additional notes about the store..."></textarea>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex flex-col sm:flex-row gap-4 justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
          <button type="button" onclick="window.history.back()" 
                  class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 flex items-center justify-center gap-2">
            <i class="fa-solid fa-arrow-left"></i>
            Back
          </button>
          <button type="submit" id="submitBtn"
                  class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-800 dark:to-indigo-800 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 dark:hover:from-blue-900 dark:hover:to-indigo-900 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
            <i class="fa-solid fa-plus"></i>
            Add Store Card
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Mobile menu toggle functionality
document.getElementById('mobile-menu-button').addEventListener('click', function() {
  const menu = document.getElementById('mobile-menu');
  menu.classList.toggle('hidden');
});

// Close mobile menu when clicking outside
document.addEventListener('click', function(event) {
  const menu = document.getElementById('mobile-menu');
  const button = document.getElementById('mobile-menu-button');
  
  if (!menu.contains(event.target) && !button.contains(event.target)) {
    menu.classList.add('hidden');
  }
});

function previewImage(input, previewId) {
  const preview = document.getElementById(previewId);
  const file = input.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = e => {
    preview.classList.remove('hidden');
    preview.querySelector('img').src = e.target.result;
  };
  reader.readAsDataURL(file);
}

document.querySelectorAll('.file-upload').forEach(el => {
  el.addEventListener('click', () => el.querySelector('input').click());
});

document.getElementById('storeForm').addEventListener('submit', async e => {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);
  const btn = document.getElementById('submitBtn');
  const oldText = btn.innerHTML;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
  btn.disabled = true;

  try {
    const res = await fetch('add_store_card.php', { method: 'POST', body: formData });
    const text = await res.text();
    let data;
    try {
      data = JSON.parse(text);
    } catch {
      console.error('Invalid JSON from server:', text);
      throw new Error('Server did not return valid JSON.');
    }

    if (data.status === 'success') {
      await Swal.fire({ icon: 'success', title: 'Success!', text: data.message, confirmButtonColor: '#10b981' });
      form.reset();
      document.querySelectorAll('#frontPreview,#backPreview').forEach(p => p.classList.add('hidden'));
    } else {
      throw new Error(data.message || 'Insert failed.');
    }
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Submission Failed!',
      html: `<p>${err.message}</p>`,
      confirmButtonColor: '#ef4444'
    });
  } finally {
    btn.innerHTML = oldText;
    btn.disabled = false;
  }
});

// Add animation to form on load
document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('.bg-white');
  form.classList.add('fade-in');
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