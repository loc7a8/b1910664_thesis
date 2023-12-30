<?php include('db_connect.php');?>

<?php

	// Check the connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	// Check if the form is submitted
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (isset($_POST["save"])) {
			saveData($conn);
		} elseif (isset($_POST["cancel"])) {
			cancelData();
		}
	}

	function saveData($conn) {
		// Check if the form is submitted
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			// Retrieve the inputs from the textboxes
			$id = $_POST['id'];
			$name = $_POST['name'];
			$description = $_POST['description'];

			if (!empty($id)) {
				// Check if the ID exists in the database
				$existingRecord = $conn->query("SELECT * FROM categories WHERE id = $id");
	
				if ($existingRecord->num_rows > 0) {
					// Update the existing record
					$query = "UPDATE categories SET name = '$name', description = '$description' WHERE id = $id";
	
					if ($conn->query($query) === TRUE) {
						echo "Data updated successfully!";
					} else {
						echo "Error updating data: " . $conn->error;
					}
				} else {
					echo "Error: Invalid ID";
				}		
			} else {
				// Get the maximum ID from the table
				$query = "SELECT MAX(id) AS max_id FROM categories";
				$result = $conn->query($query);
				$row = $result->fetch_assoc();
				$maxId = $row['max_id'];

				// Increment the ID
				$newId = $maxId + 1;

				// Insert the data into the database
				$query = "INSERT INTO categories (id, name, description) VALUES ('$newId', '$name', '$description')";
			

				if ($conn->query($query) === TRUE) {
					echo "Data saved successfully!";
				} else {
					echo "Error: " . $query . "<br>" . $conn->error;
				}

			}

		}
	}

	function cancelData() {
		// Clear all entered information
		$_POST['name'] = '';
		$_POST['description'] = '';

	}

	// Close the database connection
	// $conn->close();
?>

<div class="container-fluid">
	
	<div class="col-lg-12">
		<div class="row">
			<!-- FORM Panel -->
			<div class="col-md-4">
			<form method="post" action="">
				<div class="card">
					<div class="card-header">
						    Category Form
				  	</div>
					<div class="card-body">
						<input type="hidden" id="data-id" name="id" value="">
						<div class="form-group">
							<label for="name">Name</label>
							<input type="text" class="form-control" name="name" required>
						</div>
						<div class="form-group">
							<label for="description">Description</label>
							<textarea name="description" cols="30" rows="4" class="form-control"required></textarea>
						</div>
					</div>
							
					<div class="card-footer">
						<div class="row">
							<div class="col-md-12">					
								<button class="btn btn-sm btn-primary col-sm-3 offset-md-3" type="submit" name="save">Save</button>
								<button class="btn btn-sm btn-default col-sm-3" type="submit" name="cancel">Cancel</button>
							</div>							
						</div>	
					</div>
				</div>
			</form>
			</div>
			<!-- FORM Panel -->

			<!-- Table Panel -->
			<div class="col-md-8">
				<div class="card">
					<div class="card-header">
						<b>Category List</b>
					</div>
					<div class="card-body">
						<table class="table table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="text-center">Category Info.</th>
									<th class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$category = $conn->query("SELECT * FROM categories order by id asc");
								while($row=$category->fetch_assoc()):
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class="">
										<p>Name: <b><?php echo $row['name'] ?></b></p>
										<p><small>Description: <b><?php echo $row['description'] ?></b></small></p>
									</td>
									<td class="text-center">
										<input type="hidden" id="data-id" name="data-id" value="">
										<button class="btn btn-sm btn-primary edit_category" type="button" 
											data-id="<?php echo $row['id']; ?>"
											data-description="<?php echo $row['description'] ?>" 
											data-name="<?php echo $row['name'] ?>"
											onclick="setDataId('<?php echo $row['id']; ?>')" >
											Edit
										</button>	

										<button class="btn btn-sm btn-danger delete_category" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
									</td>
								</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- Table Panel -->
		</div>
	</div>	

</div>
<style>
	
	td{
		vertical-align: middle !important;
	}
	td p {
		margin:unset;
	}
</style>

<script>
	
	// JavaScript code to handle the click event of the "Edit" button
	var editBtns = document.querySelectorAll('.edit_category');

	editBtns.forEach(function(editBtn) {
		editBtn.addEventListener('click', function() {
		var id = editBtn.getAttribute('data-id');
		var name = editBtn.getAttribute('data-name');
		var description = editBtn.getAttribute('data-description');

		var form = document.getElementById('form');
		var hiddenIdInput = document.getElementById('data-id');
		var nameInput = document.querySelector('input[name="name"]');
		var descriptionTextarea = document.querySelector('textarea[name="description"]');

		// Set the values in the form inputs
		hiddenIdInput.value = id;
		nameInput.value = name;
		descriptionTextarea.value = description;

		// Submit the form
		form.submit();
		});
	});

	
	$('.delete_category').click(function(){
		_conf("Are you sure to delete this category?","delete_category",[$(this).attr('data-id')])
	})
	function delete_category($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_category',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
	$('table').dataTable()


</script>