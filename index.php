<?php


// Set up the database
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "test";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

$url = $_GET["_url"];

if($url == "/api/robots"){
	$post_body = file_get_contents('php://input');
	if (!empty($post_body)) {

		// Adds a new robot
		$info = json_decode($post_body);
		$sql = "INSERT INTO robots (name, type, year) VALUES ('$info->name', '$info->type', '$info->year')";
		if ($conn->query($sql) === TRUE) {
		   echo "New record created successfully";
		} else {
		    echo "Error: " . $sql . "<br>" . $conn->error;
		}
	}else{
		
		// Retrieves all robots
		$sql = "SELECT * FROM robots ORDER BY name";
		$sql_result = $conn->query($sql);
		$data = array();
		while ($row = $sql_result->fetch_assoc()){
			$data[] = array(
			    'id'   => $row["id"],
			    'name' => $row["name"]
		       	);
		}
		echo json_encode($data);
	}
}else{
	$params = explode("/",$url);
	if($params[3] == "search"){

		// Searches for robots with $name in their name
		$sql = "SELECT * FROM robots WHERE name LIKE '%$params[4]%' ORDER BY name";
		$sql_result = $conn->query($sql);
		$data = array();
		while ($row = $sql_result->fetch_assoc()){
			$data[] = array(
			    'id'   => $row["id"],
			    'name' => $row["name"]
		       	);
		}
		echo json_encode($data);
	}elseif($_SERVER['REQUEST_METHOD'] == 'PUT') {
		$put_body = file_get_contents('php://input');
		
		// Updates a robot
		$info = json_decode($put_body);
		$sql = "UPDATE robots SET name = '$info->name', type = '$info->type', year = '$info->year' WHERE id = $params[3]";
		if ($conn->query($sql) === TRUE) {
		   echo "Updated successfully";
		} else {
		    echo "Error: " . $sql . "<br>" . $conn->error;
		}
		
	}elseif($_SERVER['REQUEST_METHOD'] == 'DELETE') {
		
		// Deletes a robot
		$sql = "DELETE FROM robots WHERE id = $params[3]";
		if ($conn->query($sql) === TRUE) {
		   echo "Deleted successfully";
		} else {
		    echo "Error: " . $sql . "<br>" . $conn->error;
		}
	}else{
		
		// Retrieves robots based on primary key
		$sql = "SELECT * FROM robots WHERE id = $params[3]";
		$sql_result = $conn->query($sql);
		$data = array();
		while ($row = $sql_result->fetch_assoc()){
			$data[] = array(
			    'id'   => $row["id"],
			    'name' => $row["name"]
		       	);
		}
		echo json_encode($data);
	}

}
	

$conn->close();
