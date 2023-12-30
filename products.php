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
			$category_id = $_POST['category_id'];
			$name = $_POST['name'];
			$description = $_POST['description'];
			$price = $_POST['price'];
			$status = isset($_POST['status']) ? '1' : '0';

			if (!empty($id)) {
				// Check if the ID exists in the database
				$existingRecord = $conn->query("SELECT * FROM products WHERE id = $id");
	
				if ($existingRecord->num_rows > 0) {
					// Update the existing record
					$query = "UPDATE products SET category_id = '$category_id', name = '$name', description = '$description', price = '$price', status = '$status' WHERE id = $id";
	
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
				$query = "SELECT MAX(id) AS max_id FROM products";
				$result = $conn->query($query);
				$row = $result->fetch_assoc();
				$maxId = $row['max_id'];

				// Increment the ID
				$newId = $maxId + 1;

				// Insert the data into the database
				$query = "INSERT INTO products (id, category_id, name, description, price, status) VALUES ('$newId', '$category_id', '$name', '$description', '$price', '$status')";			

				if ($conn->query($query) === TRUE) {
					echo "Data saved successfully!";
					cancelData();
				} else {
					echo "Error: " . $query . "<br>" . $conn->error;
				}

			}

		}
	}

	function cancelData() {
		// Clear all entered information
		
		$_POST['category_id'] = '';
		$_POST['name'] = '';
		$_POST['description'] = '';
		$_POST['price'] = '';
		$_POST['status'] = '1';

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
                            Product Form
                        </div>
                        <div class="card-body">
                            <input type="hidden" id="data-id" name="id" value="">
                            <div class="form-group">
                                <label class="control-label">Category</label>
                                <select name="category_id" id="category_id" class="custom-select select2">
                                    <option value=""></option>
                                    <?php
                                    $qry = $conn->query("SELECT * FROM categories order by name asc");
                                    while ($row = $qry->fetch_assoc()) :
                                        $cname[$row['id']] = ucwords($row['name']);
                                    ?>
                                        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Name</label>
                                <input type="text" class="form-control" name="name">
                            </div>
                            <div class="form-group">
                                <label class="control-label">Description</label>
                                <textarea name="description" id="description" cols="30" rows="4" class="form-control"></textarea>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Price</label>
                                <input type="number" class="form-control text-right" name="price">
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="status" name="status" checked value="1">
                                    <label class="custom-control-label" for="status">Available</label>
                                </div>
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
                        <b>Product List</b>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Category</th>
                                    <th class="text-center">Product Info.</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                $product = $conn->query("SELECT * FROM products order by id asc");
                                while ($row = $product->fetch_assoc()) :
                                ?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class="">
										<p><b><?php echo $cname[$row['category_id']] ?></b></p>
									</td>
									<td class="">
										<p>Name: <b><?php echo $row['name'] ?></b></p>
										<p><small>Price: <b><?php echo number_format($row['price'], 2) ?></b></small></p>
										<p><small>Status: <b><?php echo $row['status'] == 1 ? " Available" : "Unavailable" ?></b></small></p>
										<p><small>Description: <b><?php echo $row['description'] ?></b></small></p>
									</td>
									<td class="text-center">
										<button class="btn btn-sm btn-primary edit_product" type="button" 
											data-id="<?php echo $row['id'] ?>" 
											data-description="<?php echo $row['description'] ?>" 
											data-name="<?php echo $row['name'] ?>" 
											data-price="<?php echo $row['price'] ?>"  
											data-status="<?php echo $row['status'] ?>" 
											data-category_id="<?php echo $row['category_id'] ?>">Edit</button>
										
										<button class="btn btn-sm btn-danger delete_product" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
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
	.custom-switch{
		cursor: pointer;
	}
	.custom-switch *{
		cursor: pointer;
	}
</style>
<script>
	
	// JavaScript code to handle the click event of the "Edit" button
	var editBtns = document.querySelectorAll('.edit_product');

	editBtns.forEach(function(editBtn) {
	editBtn.addEventListener('click', function() {
		var id = editBtn.getAttribute('data-id');
		var category_id = editBtn.getAttribute('data-categoryId');
		var name = editBtn.getAttribute('data-name');
		var description = editBtn.getAttribute('data-description');
		var price = editBtn.getAttribute('data-price');
		var status = editBtn.getAttribute('data-status');

		var form = document.getElementById('form');
		var hiddenIdInput = document.getElementById('data-id');
		var categoryIdInput = document.querySelector('select[name="category"]');
		var nameInput = document.querySelector('input[name="name"]');
		var descriptionTextarea = document.querySelector('textarea[name="description"]');
		var priceInput = document.querySelector('input[name="price"]');
		var statusCheckbox = document.querySelector('input[name="status"]');

		// Set the values in the form inputs
		hiddenIdInput.value = id;
		nameInput.value = name;
		descriptionTextarea.value = description;
		priceInput.value = price;
		statusCheckbox.checked = (status === '1');

		// Set the selected category option
		var options = categoryIdInput.options;
		for (var i = 0; i < options.length; i++) {
		if (options[i].value === category_id) {
			options[i].selected = true;
			break;
		}
		}

		// Submit the form
		form.submit();
		});
	});


	$('.delete_product').click(function(){
		_conf("Are you sure to delete this product?","delete_product",[$(this).attr('data-id')])
	})
	function delete_product($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_product',
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