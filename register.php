<?php
$message = ""; // To store success or error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        isset($_POST["username"], $_POST["password"], $_POST["email"], $_POST["phone"], $_POST["address"], $_POST["gender"], $_POST["dob"])
    ) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $email = $_POST["email"];
        $phone = $_POST["phone"];
        $address = $_POST["address"];
        $gender = $_POST["gender"];
        $dob = $_POST["dob"];

        if (!empty($username) && !empty($password) && !empty($email) && !empty($phone) && !empty($address) && !empty($gender) && !empty($dob)) {
            // Database connection
            $conn = new mysqli("localhost", "root", "", "userdb", 3306, "/opt/lampp/var/mysql/mysql.sock");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Check if username already exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $message = "<p class='error'>Username already taken. Please choose another.</p>";
            } else {
                // Insert new user
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("INSERT INTO users (username, password, email, phone, address, gender, dob) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $username, $hashed_password, $email, $phone, $address, $gender, $dob);
                $stmt->execute();
                $message = "<p class='success'>Registration successful! You can now log in.</p>";
            }

            $stmt->close();
            $conn->close();
        } else {
            $message = "<p class='error'>Please fill all fields.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <?= $message ?>
        <form action="register.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required autocomplete="username">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required autocomplete="email">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required autocomplete="new-password">

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" autocomplete="tel">

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" autocomplete="street-address">

            <label for="gender">Gender:</label>
            <select id="gender" name="gender">
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>

            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" autocomplete="bday">

            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>
