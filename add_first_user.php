<?php
	include 'db_connect.php';

	// Check the connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
?>

<?php
	// Retrieve max ID from users table
	$sql = "SELECT MAX(id) as max_id FROM users";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	$maxId = $row['max_id'];
	$newId = $maxId + 1;
?>

<?php
	// Check if the form is submitted
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		// Retrieve form data
		$name = $_POST["name"];
		$username = $_POST["username"];
		$password = $_POST["password"];
		$type = $_POST["type"] ?? 2; // Default value of 2 if type is not chosen

		// Validate input
		if (empty($name) || empty($username) || empty($password)) {
			echo "Please enter all required fields.";
		} else {
			// Prepare and execute the SQL statement
			$sql = "INSERT INTO users (id, name, username, password, type)
					VALUES ($newId, '$name', '$username', '".md5($password)."', $type)";
			if ($conn->query($sql) === TRUE) {
				echo "New record created successfully";
			} else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}
	}
?>


<!DOCTYPE html>
<html>
	<head>
		<style>
			body {
				font-family: Arial, sans-serif;
			}

			form {
				width: 300px;
				margin: 0 auto;
				padding: 20px;
				background-color: #f2f2f2;
				border: 1px solid #ccc;
				border-radius: 5px;
			}

			label {
				display: block;
				margin-bottom: 5px;
				font-weight: bold;
			}

			input[type="text"],
			input[type="password"],
			select {
				width: 100%;
				padding: 8px;
				margin-bottom: 10px;
				border: 1px solid #ccc;
				border-radius: 4px;
				box-sizing: border-box;
			}

			input[type="submit"] {
				background-color: #4CAF50;
				color: white;
				padding: 10px 15px;
				border: none;
				border-radius: 4px;
				cursor: pointer;
			}

			input[type="submit"]:hover {
				background-color: #45a049;
			}

			.error {
				color: red;
				margin-bottom: 10px;
			}
		</style>
	</head>
	
	<body>
		<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
			<label for="name">Name:</label>
			<input type="text" id="name" name="name" required><br>

			<label for="username">Username:</label>
			<input type="text" id="username" name="username" required><br>

			<label for="password">Password:</label>
			<input type="password" id="password" name="password" required><br>
			<p>(1=Admin; 2=Staff)</p>
			<label for="type">Type:</label>
			
			<select id="type" name="type">
				<option value="1">1</option>
				<option value="2">2</option>
			</select><br>

			<input type="submit" value="Submit">
		</form>
	</body>
</html>