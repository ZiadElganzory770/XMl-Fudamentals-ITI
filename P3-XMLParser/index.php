<?php
// Initialize the XML file path
$xmlFilePath = 'data.xml';
if (!file_exists($xmlFilePath)) {
    $dom = new DOMDocument('1.0', 'UTF-8');
    $root = $dom->createElement('contacts');
    $dom->appendChild($root);
    $dom->save($xmlFilePath);
} else {
    $dom = new DOMDocument();
    $dom->load($xmlFilePath);
}

function insertContact($name, $phone, $address, $email) {
    global $dom, $xmlFilePath;
    $contact = $dom->createElement('contact');
    $contact->appendChild($dom->createElement('name', $name));
    $contact->appendChild($dom->createElement('phone', $phone));
    $contact->appendChild($dom->createElement('address', $address));
    $contact->appendChild($dom->createElement('email', $email));
    $dom->documentElement->appendChild($contact);
    $dom->save($xmlFilePath);
}

function updateContact($oldName, $newName, $newPhone, $newAddress, $newEmail) {
    global $dom, $xmlFilePath;
    $contacts = $dom->getElementsByTagName('contact');
    foreach ($contacts as $contact) {
        if ($contact->getElementsByTagName('name')->item(0)->nodeValue == $oldName) {
            if (!empty($newName)) {
                $contact->getElementsByTagName('name')->item(0)->nodeValue = $newName;
            }
            if (!empty($newPhone)) {
                $contact->getElementsByTagName('phone')->item(0)->nodeValue = $newPhone;
            }
            if (!empty($newAddress)) {
                $contact->getElementsByTagName('address')->item(0)->nodeValue = $newAddress;
            }
            if (!empty($newEmail)) {
                $contact->getElementsByTagName('email')->item(0)->nodeValue = $newEmail;
            }
            $dom->save($xmlFilePath);
            return;
        }
    }
}

function deleteContact($name) {
    global $dom, $xmlFilePath;
    $contacts = $dom->getElementsByTagName('contact');
    foreach ($contacts as $contact) {
        if ($contact->getElementsByTagName('name')->item(0)->nodeValue == $name) {
            $dom->documentElement->removeChild($contact);
            $dom->save($xmlFilePath);
            return;
        }
    }
}

function searchContacts($searchValue) {
    global $dom;
    $searchResults = [];
    $contacts = $dom->getElementsByTagName('contact');
    foreach ($contacts as $contact) {
        $currentName = $contact->getElementsByTagName('name')->item(0)->nodeValue;
        if (str_contains(strtolower($currentName), strtolower($searchValue))) {
            array_push($searchResults, $contact);
        }
    }
    return $searchResults;
}

$searchResults = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $email = trim($_POST['email'] ?? '');

    switch ($action) {
        case 'Insert':
            insertContact($name, $phone, $address, $email);
            break;
        case 'Update':
            updateContact($name, $name, $phone, $address, $email);
            break;
        case 'Delete':
            deleteContact($name);
            break;
        case 'Search':
            $searchValue = trim($_POST['searchValue'] ?? '');
            $searchResults = searchContacts($searchValue);
            break;
    }
}

function displaySearchResults($searchResults) {
    if (!empty($searchResults)) {
        echo '<div class="search-results mt-4">';
        echo '<h2 class="mb-3">Search Results</h2>';
        foreach ($searchResults as $contact) {
            echo '<div class="card mb-3">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title">' . htmlspecialchars($contact->getElementsByTagName('name')->item(0)->nodeValue) . '</h5>';
            echo '<p class="card-text"><strong>Phone:</strong> ' . htmlspecialchars($contact->getElementsByTagName('phone')->item(0)->nodeValue) . '</p>';
            echo '<p class="card-text"><strong>Address:</strong> ' . htmlspecialchars($contact->getElementsByTagName('address')->item(0)->nodeValue) . '</p>';
            echo '<p class="card-text"><strong>Email:</strong> ' . htmlspecialchars($contact->getElementsByTagName('email')->item(0)->nodeValue) . '</p>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Manager</title>
</head>
<body>
    <h1>Contact Manager</h1>
    <form action="index.php" method="post">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" placeholder="Name"><br>
        <label for="phone">Phone:</label><br>
        <input type="text" id="phone" name="phone" placeholder="Phone"><br>
        <label for="address">Address:</label><br>
        <input type="text" id="address" name="address" placeholder="Address"><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" placeholder="Email"><br><br>
        <button type="submit" name="action" value="Insert">Insert</button>
        <button type="submit" name="action" value="Update">Update</button>
        <button type="submit" name="action" value="Delete">Delete</button><br><br>
        <label for="searchValue">Search:</label><br>
        <input type="text" id="searchValue" name="searchValue" placeholder="Search By Name"><br><br>
        <button type="submit" name="action" value="Search">Search</button>
    </form>
    <?php
        if (!empty($searchResults)) {
            displaySearchResults($searchResults);
        }
    ?>
</body>
</html>
