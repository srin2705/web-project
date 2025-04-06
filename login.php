<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Start the session (important for logging in later)


// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pdtor";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["loginUsername"]);
    $password = $_POST["loginPassword"];

    $errors = [];

    // Validation (basic - you might want more robust checks)
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        // Check if the user exists
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            // Verify the password
            if (password_verify($password, $row["password"])) {
                // Authentication successful
                // In a real application, you would set session variables here
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["username"] = $row["username"];
                header("Location:2home.html"); // Redirect to the home page
                exit();
            } else {
                $errors[] = "Invalid username or password.";
            }
        } else {
            $errors[] = "Invalid username or password.";
        }
        $stmt->close();
    }

    // Display errors
    if (!empty($errors)) {
        echo "<p style='color: red;'>";
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
        echo "</p>";
    }
}

$conn->close();
?>
