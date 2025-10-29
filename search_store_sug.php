<?php
include 'config.php';

$q = $_GET['q'] ?? '';
$show = $_GET['show'] ?? '';

if (strlen($q) < 2 && !$show) {
    echo json_encode([]);
    exit;
}

if (!$show) {
    // Suggestion Mode (JSON)
    header('Content-Type: application/json');
    $stmt = $conn->prepare("SELECT store_name, owner_name, city FROM store_cards WHERE store_name LIKE ? OR owner_name LIKE ? OR notes LIKE ? ORDER BY store_name LIMIT 10");
    $like = "%$q%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} else {
    // Search Result Mode (HTML)
    $colors = [
        "from-indigo-500 to-purple-500",
        "from-blue-500 to-teal-400",
        "from-green-500 to-lime-400",
        "from-orange-500 to-yellow-400",
        "from-pink-500 to-rose-500",
        "from-cyan-500 to-blue-400",
        "from-fuchsia-500 to-pink-400",
        "from-violet-500 to-purple-400"
    ];

    $stmt = $conn->prepare("SELECT * FROM store_cards WHERE store_name LIKE ? OR owner_name LIKE ? OR notes LIKE ? ORDER BY id DESC");
    $like = "%$q%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p class='col-span-full text-center text-gray-600 py-8'>No matching results found for \"$q\".</p>";
        exit;
    }

    while ($row = $result->fetch_assoc()) {
        $color = $colors[array_rand($colors)];
        $store_name = htmlspecialchars($row['store_name']);
        $owner_name = htmlspecialchars($row['owner_name']);
        $contact1 = htmlspecialchars($row['contact_number']);
        $contact2 = htmlspecialchars($row['contact_number2']);
        $city = htmlspecialchars($row['city']);
        $website = htmlspecialchars($row['website']);
        $notes = htmlspecialchars($row['notes']);

        echo "<div class='rounded-2xl overflow-hidden shadow-lg bg-gradient-to-br $color text-white card-hover transform hover:scale-105 transition-all duration-300'>";
        
        // Card Header
        echo "<div class='p-4 border-b border-white/20'>
                <div class='flex items-center justify-between'>
                    <h2 class='text-lg font-bold truncate flex-1'>$store_name</h2>
                    <div class='bg-white/20 rounded-full px-3.5 py-2 ml-2 flex-shrink-0'>
                        <i class='fa-solid fa-store text-xs'></i>
                    </div>
                </div>";
        if ($owner_name) {
            echo "<div class='flex items-center mt-2'>
                    <i class='fa-solid fa-user text-xs mr-2 opacity-80'></i>
                    <p class='text-sm opacity-90 truncate'>$owner_name</p>
                  </div>";
        }
        echo "</div>";

        // Card Body
        echo "<div class='p-4 space-y-3'>";

        // Contact Numbers
        if ($contact1) {
            echo "<div class='flex items-center justify-between bg-white/15 rounded-lg p-2 transition-all hover:bg-white/20'>
                    <div class='flex items-center min-w-0 ml-3 flex-1'>
                        <i class='fa-solid fa-phone text-xs mr-2 opacity-80 flex-shrink-0'></i>
                        <span class='text-sm truncate'>$contact1</span>
                    </div>
                    <button onclick='copyText(\"$contact1\")' class='bg-white/25 hover:bg-white/35  px-3 py-1.5 rounded-md transition-colors flex-shrink-0 ml-2'>
                        <i class='fa-regular fa-copy text-xs'></i>
                    </button>
                  </div>";
        }
        
        if ($contact2) {
            echo "<div class='flex items-center justify-between bg-white/15 rounded-lg p-2 transition-all hover:bg-white/20'>
                    <div class='flex items-center min-w-0 ml-3 flex-1'>
                        <i class='fa-solid fa-phone-volume text-xs mr-2 opacity-80 flex-shrink-0'></i>
                        <span class='text-sm truncate'>$contact2</span>
                    </div>
                    <button onclick='copyText(\"$contact2\")' class='bg-white/25 hover:bg-white/35  px-3 py-1.5 rounded-md transition-colors flex-shrink-0 ml-2'>
                        <i class='fa-regular fa-copy text-xs'></i>
                    </button>
                  </div>";
        }

        // Location
        if ($city) {
            echo "<div class='flex items-center bg-white/10 rounded-lg p-2'>
                    <i class='fa-solid fa-location-dot text-xs mr-2 ml-3 opacity-80 flex-shrink-0'></i>
                    <p class='text-sm truncate'>$city</p>
                  </div>";
        }
        
        // Website
        if ($website) {
            echo "<div class='flex items-center bg-white/10 rounded-lg p-2'>
                    <i class='fa-solid fa-globe text-xs mr-2 ml-3 opacity-80 flex-shrink-0'></i>
                    <a href='$website' target='_blank' class='text-sm underline truncate hover:opacity-80 transition-opacity'>Visit Website</a>
                  </div>";
        }
        
        // Notes
        if ($notes) {
            echo "<div class='bg-white/10 rounded-lg p-2'>
                    <div class='flex items-start'>
                        <i class='fa-solid fa-quote-left text-xs mr-2 mt-0.5 opacity-80 ml-3 flex-shrink-0'></i>
                        <p class='text-xs italic leading-relaxed line-clamp-2'>$notes</p>
                    </div>
                  </div>";
        }

        echo "</div></div>";
    }
}
?>