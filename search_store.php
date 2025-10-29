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
    exit;
} else {
    // Search Result Mode (HTML)
    $stmt = $conn->prepare("SELECT * FROM store_cards WHERE store_name LIKE ? OR owner_name LIKE ? OR notes LIKE ? ORDER BY id DESC");
    $like = "%$q%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p class='col-span-full text-center text-gray-600 dark:text-gray-400 py-8'>No matching results found for \"$q\".</p>";
        exit;
    }

    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $store_name = htmlspecialchars($row['store_name']);
        $owner_name = htmlspecialchars($row['owner_name']);
        $contact1 = htmlspecialchars($row['contact_number']);
        $contact2 = htmlspecialchars($row['contact_number2']);
        $city = htmlspecialchars($row['city']);
        $website = htmlspecialchars($row['website']);
        $notes = htmlspecialchars($row['notes']);

        echo "<div id='card-$id' class='rounded-2xl overflow-hidden shadow-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white card-hover transform hover:scale-105 transition-all duration-300'>";

        // Card Header
        echo "<div class='p-4 border-b border-gray-200 dark:border-gray-600'>
                <div class='flex items-center justify-between'>
                    <h2 class='text-lg font-bold truncate flex-1'>$store_name</h2>
                    <div class='flex gap-2 ml-2 flex-shrink-0'>
                        <button onclick='openEditModal($id)' class='bg-gray-100 dark:bg-gray-700 px-3 py-1.5 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition text-gray-900 dark:text-white'><i class='fa-solid fa-pen text-xs'></i></button>
                        <button onclick='deleteCard($id)' class='bg-gray-100 dark:bg-gray-700 px-3 py-1.5 rounded-md hover:bg-red-600 transition text-gray-900 dark:text-white'><i class='fa-solid fa-trash text-xs'></i></button>
                    </div>
                </div>";
        if ($owner_name) {
            echo "<div class='flex items-center mt-2'>
                    <i class='fa-solid fa-user text-xs mr-2 opacity-80 text-gray-500 dark:text-gray-400'></i>
                    <p class='text-sm opacity-90 truncate text-gray-600 dark:text-gray-300'>$owner_name</p>
                  </div>";
        }
        echo "</div>";

        // Card Body
        echo "<div class='p-4 space-y-3'>";
        if ($contact1) {
            echo "<div class='flex items-center justify-between bg-gray-100 dark:bg-gray-700 rounded-lg p-2 transition-all hover:bg-gray-200 dark:hover:bg-gray-600'>
                    <div class='flex items-center min-w-0 ml-3 flex-1'>
                        <i class='fa-solid fa-phone text-xs mr-2 opacity-80 flex-shrink-0 text-gray-500 dark:text-gray-400'></i>
                        <span class='text-sm truncate'>$contact1</span>
                    </div>
                    <button onclick='copyText(\"$contact1\")' class='bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 px-3 py-1.5 rounded-md transition-colors flex-shrink-0 ml-2 text-gray-600 dark:text-gray-300'>
                        <i class='fa-regular fa-copy text-xs'></i>
                    </button>
                  </div>";
        }

        if ($contact2) {
            echo "<div class='flex items-center justify-between bg-gray-100 dark:bg-gray-700 rounded-lg p-2 transition-all hover:bg-gray-200 dark:hover:bg-gray-600'>
                    <div class='flex items-center min-w-0 ml-3 flex-1'>
                        <i class='fa-solid fa-phone-volume text-xs mr-2 opacity-80 flex-shrink-0 text-gray-500 dark:text-gray-400'></i>
                        <span class='text-sm truncate'>$contact2</span>
                    </div>
                    <button onclick='copyText(\"$contact2\")' class='bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 px-3 py-1.5 rounded-md transition-colors flex-shrink-0 ml-2 text-gray-600 dark:text-gray-300'>
                        <i class='fa-regular fa-copy text-xs'></i>
                    </button>
                  </div>";
        }

        if ($city) {
            echo "<div class='flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-2'>
                    <i class='fa-solid fa-location-dot text-xs mr-2 ml-3 opacity-80 flex-shrink-0 text-gray-500 dark:text-gray-400'></i>
                    <p class='text-sm truncate'>$city</p>
                  </div>";
        }

        if ($website) {
            echo "<div class='flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-2'>
                    <i class='fa-solid fa-globe text-xs mr-2 ml-3 opacity-80 flex-shrink-0 text-gray-500 dark:text-gray-400'></i>
                    <a href='$website' target='_blank' class='text-sm underline truncate hover:opacity-80 transition-opacity'>Visit Website</a>
                  </div>";
        }

        if ($notes) {
            echo "<div class='bg-gray-100 dark:bg-gray-700 rounded-lg p-2'>
                    <div class='flex items-start'>
                        <i class='fa-solid fa-quote-left text-xs mr-2 mt-0.5 opacity-80 ml-3 flex-shrink-0 text-gray-500 dark:text-gray-400'></i>
                        <p class='text-xs italic leading-relaxed line-clamp-2'>$notes</p>
                    </div>
                  </div>";
        }

        echo "</div></div>";
    }
}
?>
<script>
// Edit Modal
function openEditModal(id) {
    fetch('get_store.php?id=' + id)
    .then(res => res.json())
    .then(data => {
        const modal = document.createElement('div');
        modal.id = 'edit-modal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
        modal.innerHTML = `
            <div class='bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg p-6 overflow-auto'>
                <h3 class='text-xl font-bold mb-4 text-gray-800 dark:text-gray-200'>Edit Store</h3>
                <form id='editForm'>
                    <input type='hidden' name='id' value='${data.id}' />
                    <div class='space-y-3'>
                        <input type='text' name='store_name' value='${data.store_name}' placeholder='Store Name' class='w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 px-3 py-2 rounded-lg text-gray-900 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500'/>
                        <input type='text' name='owner_name' value='${data.owner_name}' placeholder='Owner Name' class='w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 px-3 py-2 rounded-lg text-gray-900 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500'/>
                        <input type='text' name='contact_number' value='${data.contact_number}' placeholder='Contact 1' class='w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 px-3 py-2 rounded-lg text-gray-900 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500'/>
                        <input type='text' name='contact_number2' value='${data.contact_number2}' placeholder='Contact 2' class='w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 px-3 py-2 rounded-lg text-gray-900 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500'/>
                        <input type='text' name='city' value='${data.city}' placeholder='City' class='w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 px-3 py-2 rounded-lg text-gray-900 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500'/>
                        <input type='text' name='website' value='${data.website}' placeholder='Website' class='w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 px-3 py-2 rounded-lg text-gray-900 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500'/>
                        <textarea name='notes' placeholder='Notes' class='w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 px-3 py-2 rounded-lg text-gray-900 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500'>${data.notes}</textarea>
                    </div>
                    <div class='flex justify-end gap-3 mt-4'>
                        <button type='button' onclick='closeModal()' class='px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-200'>Cancel</button>
                        <button type='submit' class='px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700'>Update</button>
                    </div>
                </form>
            </div>
        `;
        document.body.appendChild(modal);

        document.getElementById('editForm').addEventListener('submit', function(e){
            e.preventDefault();
            const formData = new FormData(this);
            fetch('update_store.php', {
                method: 'POST',
                body: formData
            }).then(res => res.json())
            .then(resp => {
                showToast(resp.status, resp.message);
                if(resp.status === 'success'){
                    closeModal();
                    // refresh the card
                    performSearch();
                }
            });
        });
    });
}

function closeModal() {
    const modal = document.getElementById('edit-modal');
    if(modal) modal.remove();
}

// Delete
function deleteCard(id){
    if(!confirm('Are you sure to delete this store?')) return;
    fetch('delete_store.php?id='+id)
    .then(res => res.json())
    .then(resp=>{
        showToast(resp.status, resp.message);
        if(resp.status === 'success'){
            const card = document.getElementById('card-'+id);
            if(card) card.remove();
        }
    });
}
</script>