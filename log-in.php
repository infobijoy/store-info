<?php
session_start();

// If user is already logged in, redirect to main page
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: view_store_cards.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === 'salman' && $password === 'mypass') {
        $_SESSION['logged_in'] = true;
        
        // Set auto-login cookie for 1 year
        setcookie('auto_login', 'true', time() + (365 * 24 * 60 * 60), '/'); // 1 year
        
        header('Location: view_store_cards.php'); // Redirect to main page
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - InfoHub</title>
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
</style>
</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-800 min-h-screen flex items-center justify-center text-gray-200">

<!-- Login Form Container -->
<div class="bg-gray-800 rounded-2xl shadow-xl overflow-hidden fade-in w-full max-w-md p-8">
  <div class="text-center mb-8">
    <h2 class="text-3xl font-bold text-gray-200 mb-2">Login to InfoHub</h2>
    <p class="text-gray-400">Enter your credentials to access the dashboard</p>
  </div>

  <?php if (isset($error)): ?>
    <div class="bg-red-900 border border-red-600 text-red-200 px-4 py-3 rounded-xl mb-6">
      <?php echo $error; ?>
    </div>
  <?php endif; ?>

  <form method="POST" class="space-y-6">
    <!-- Username -->
    <div>
      <label for="username" class="block text-sm font-medium text-gray-300 mb-2">
        <i class="fa-solid fa-user mr-1 text-gray-400"></i>
        Username
      </label>
      <input type="text" id="username" name="username" required
             class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl focus:ring-2 focus:ring-gray-500 focus:border-gray-500 outline-none transition-all duration-300 text-gray-200 placeholder-gray-500"
             placeholder="Enter username">
    </div>

    <!-- Password -->
    <div>
      <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
        <i class="fa-solid fa-lock mr-1 text-gray-400"></i>
        Password
      </label>
      <input type="password" id="password" name="password" required
             class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-xl focus:ring-2 focus:ring-gray-500 focus:border-gray-500 outline-none transition-all duration-300 text-gray-200 placeholder-gray-500"
             placeholder="Enter password">
    </div>

    <!-- Remember Me Checkbox -->
    <div class="flex items-center">
      <input type="checkbox" id="remember_me" name="remember_me" class="w-4 h-4 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
      <label for="remember_me" class="ml-2 text-sm text-gray-300">Keep me logged in for 1 year</label>
    </div>

    <!-- Submit Button -->
    <button type="submit"
            class="w-full px-8 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-xl hover:from-gray-700 hover:to-gray-800 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
      <i class="fa-solid fa-sign-in-alt"></i>
      Login
    </button>
  </form>
</div>

<script>
// Add animation on load
document.addEventListener('DOMContentLoaded', function() {
  const formContainer = document.querySelector('.bg-gray-800');
  formContainer.classList.add('fade-in');
});
</script>
</body>
</html>