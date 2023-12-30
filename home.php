<?php include 'db_connect.php' ?>

<?php

	// Check the connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	// Check if the form is submitted
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (isset($_POST["send"])) {
			sendData($conn);
		} elseif (isset($_POST["cancel"])) {
			cancelData();
		} elseif (isset($_POST["delete"])) {
			deleteData($conn);
		}
	}

	function deleteData($conn){
		if (isset($_POST['delete'])) {
			$id = $_POST['delete'];

			// Delete the message from the database
			$query = "DELETE FROM message WHERE id = '$id'";

			if ($conn->query($query) === TRUE) {
				echo "Message deleted successfully!";
			} else {
				echo "Error deleting message: " . $conn->error;
			}
		}
	}

	function sendData($conn){
		// Check if the form is submitted
		if (isset($_POST['send'])) {
			// Retrieve the inputs from the textboxes
			$id = $_POST['id'];
			$from = $_SESSION['login_id']; // Set "from" to session login ID
			$to = $_POST['to'];
			$reply = -1; // Set reply to -1
			$content = $_POST['content'];
			$time = $_POST['time'] ? $_POST['time'] : null;
			$seen = 0;

			
			 // Check if required fields are empty
			if (empty($content)) {
				// Display a toast notification using JavaScript
				echo '
				<script>
					$(document).ready(function() {
						$("#toastMessage").toast("show");
					});
				</script>
				';

				// Display the toast notification modal
				echo '
				<div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="false" id="toastMessage">
					<div class="toast-header">
						<strong class="mr-auto">Error</strong>
						<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="toast-body">
						Please enter the message content.
					</div>
				</div>
				';

				return;
			}
	

			// Get the maximum ID from the table
			$query = "SELECT MAX(id) AS max_id FROM message";
			$result = $conn->query($query);
			$row = $result->fetch_assoc();
			$maxId = $row['max_id'];

			// Increment the ID
			$newId = $maxId + 1;

			// Insert the data into the database
			$query = "INSERT INTO message (id, `from`, `to`, reply, content, time, seen) VALUES ('$newId', '$from', '$to', '$reply', '$content', '$time', '$seen')";

			if ($conn->query($query) === TRUE) {
				echo "Message sent successfully!";
				cancelData();
			} else {
				echo "Error: " . $query . "<br>" . $conn->error;
			}
		}
	}

		function cancelData() {
			// Clear all entered information
			
			$_POST['to'] = '';
			$_POST['reply'] = '';
			$_POST['content'] = '';
			$_POST['time'] = '';

	}


	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['received'])) {
		$receivedMessageId = $_POST['received'];
		// Update the 'seen' value to 1 in the database for the received message
		$updateQuery = "UPDATE message SET seen = 1 WHERE id = '$receivedMessageId'";
		$conn->query($updateQuery);
	
		// Redirect to the home page using JavaScript
		echo '<script>window.location.href = "index.php";</script>';
		exit;
	}

	// Close the database connection
	// $conn->close();
?>

<style>
   span.float-right.summary_icon {
    font-size: 3rem;
    position: absolute;
    right: 1rem;
    top: 0;
}
.imgs{
		margin: .5em;
		max-width: calc(100%);
		max-height: calc(100%);
	}
	.imgs img{
		max-width: calc(100%);
		max-height: calc(100%);
		cursor: pointer;
	}
	#imagesCarousel,#imagesCarousel .carousel-inner,#imagesCarousel .carousel-item{
		height: 60vh !important;background: black;
	}
	#imagesCarousel .carousel-item.active{
		display: flex !important;
	}
	#imagesCarousel .carousel-item-next{
		display: flex !important;
	}
	#imagesCarousel .carousel-item img{
		margin: auto;
	}
	#imagesCarousel img{
		width: auto!important;
		height: auto!important;
		max-height: calc(100%)!important;
		max-width: calc(100%)!important;
	}
</style>

<div class="containe-fluid">
	<div class="row mt-3 ml-3 mr-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <?php echo "Welcome back ". $_SESSION['login_name']."!"  ?>
                    <hr>
                </div>

			<!-- Todolist -->

			<div class="col-md-8">
				<div class="card">
					<div class="card-header">
						<b>Today's task</b>
					</div>
					<div class="card-body">
						<table class="table table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="text-center">From</th>
									<th class="text-center">Content</th>
									<th class="text-center">Date</th>
									<th class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$login_id = $_SESSION['login_id'];

								// Fetch only the rows where seen is 0, sort by date and time (with time values of '00:00:00' appearing last)
								$message = $conn->query("SELECT m.*, u1.name AS from_name
									FROM message m
									JOIN users u1 ON m.from = u1.id
									WHERE m.to = '$login_id' AND m.seen = 0
									ORDER BY CASE WHEN TIME(m.time) = '00:00:00' THEN 1 ELSE 0 END, DATE(m.time) ASC, TIME(m.time) ASC");

								while ($row = $message->fetch_assoc()):
									$isFromLoggedInUser = ($row['from'] == $login_id);
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td>
										<p><b><?php echo $row['from_name'] ?></b></p>
									</td>
									<td>
										<p><b><?php echo $row['content'] ?></b></</td>
									<td>
										<?php
										$time = $row['time'];
										if ($time != "0000-00-00 00:00:00") {
											$formattedTime = date("Y-m-d H:i:s", strtotime($time));
											echo "<p><b>$formattedTime</b></p>";
										} else {
											echo "<p><b></b></p>";
										}
										?>
									</td>
									<td class="text-center">
										<form method="POST" action="">
											<input type="hidden" name="received" value="<?php echo $row['id'] ?>">
											<button class="btn btn-sm btn-success received_message" type="submit">Received</button>
											<!-- <button class="btn btn-sm btn-info reply_message" type="submit">Reply</button> -->
										</form>
									</td>
								</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- Todolist -->

            </div>      			
        </div>
    </div>
	

	
	
<div class="col-lg-12">
	

	<div class="row d-flex justify-content-center">
		<!-- FORM Panel -->
		<div class="col-md-6">
			<form method="POST" action="">
				<div class="card">
					<div class="card-header">
						Message
					</div>

					<div class="card-body">
						<input type="hidden" name="id">
						<div class="form-group">
							<label class="control-label">To</label>
							<select name="to" class="custom-select select2">
								<option value=""></option>
								<?php
									$login_id = $_SESSION['login_id'];
									$qry = $conn->query("SELECT * FROM users ORDER BY id ASC");
									while ($row = $qry->fetch_assoc()) {
										$uname[$row['id']] = $row['name'];
								?>
									
									<option value="<?php echo $row['id'] ?>" <?php echo ($row['id'] == $login_id) ? 'selected' : ''; ?>>
										<?php echo $row['name'] . (($row['id'] == $login_id) ? ' (myself)' : ''); ?>
									</option>								
								<?php }?>
							</select>
						</div>

						<div class="form-group">
							<label class="control-label">Content</label>
							<textarea name="content" id="content" cols="30" rows="4" class="form-control"></textarea>
						</div>

						<div class="form-group">
							<label class="control-label">Time</label>
								<input type="datetime-local" name="time" class="form-control">
							</div>
							
					</div>
							
					<div class="card-footer">
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-sm btn-primary col-sm-3 offset-md-3" type="submit" name="send"> Send</button>
								<button class="btn btn-sm btn-default col-sm-3" type="submit" name="cancel">Cancel</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
		<!-- FORM Panel -->
	</div>
	
	<div class="row d-flex justify-content-center">
		<!-- Table Panel -->
		<div class="">
			<div class="card">
				<div class="card-header">
					<b>Message List</b>
				</div>
				<div class="card-body">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<th class="text-center">#</th>
								<th class="text-center">From</th>
								<th class="text-center">To</th>
								<!-- <th class="text-center">Reply To</th> -->
								<th class="text-center">Content</th>
								<th class="text-center">Date</th>
								<th class="text-center">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$i = 1;
							$login_id = $_SESSION['login_id']; // Retrieve the login ID from the session
							$message = $conn->query("SELECT m.*, u1.name 
								AS from_name, u2.name 
								AS to_name 
								FROM message m
								JOIN users u1 ON m.from = u1.id
								JOIN users u2 ON m.to = u2.id
								WHERE m.from = '$login_id' OR m.to = '$login_id'
								ORDER BY m.id ASC");
							while ($row = $message->fetch_assoc()):
								$isFromLoggedInUser = ($row['from'] == $login_id);
							?>
							<tr>
								<td class="text-center"><?php echo $i++ ?></td>
								<td class="">
									<p><b><?php echo $row['from_name'] ?></b></p>
								</td>
								<td class="">
									<p><b><?php echo $row['to_name'] ?></b></p>
								</td>

								
												

								<td class="">
									<p><b><?php echo $row['content'] ?></b></p>
								</td>

								<td class="">
									<?php
										$time = $row['time'];
										if ($time != "0000-00-00 00:00:00") {
											$formattedTime = date("Y-m-d H:i:s", strtotime($time));
											echo "<p><b>$formattedTime</b></p>";
										} else {
											echo "<p><b></b></p>";
										}
									?>
								</td>
								
								<td class="text-center">
									<?php if ($isFromLoggedInUser): ?>
										<form method="POST" action="">
											<input type="hidden" name="delete" value="<?php echo $row['id'] ?>">
											<button class="btn btn-sm btn-danger delete_message" type="submit">Delete</button>
										</form>
									<?php endif; ?>		
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

<script>


	$('.delete_message').click(function(e){
    e.preventDefault();
    var form = $(this).closest('form');
    if (confirm("Are you sure you want to delete this message?")) {
        form.submit();
    }
});

	$('table').dataTable()

	$('#manage-records').submit(function(e){
        e.preventDefault()
        start_load()
        $.ajax({
            url:'ajax.php?action=save_track',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success:function(resp){
                resp=JSON.parse(resp)
                if(resp.status==1){
                    alert_toast("Data successfully saved",'success')
                    setTimeout(function(){
                        location.reload()
                    },800)

                }
                
            }
        })
    })
    $('#tracking_id').on('keypress',function(e){
        if(e.which == 13){
            get_person()
        }
    })
    $('#check').on('click',function(e){
            get_person()
    })
    function get_person(){
            start_load()
        $.ajax({
                url:'ajax.php?action=get_pdetails',
                method:"POST",
                data:{tracking_id : $('#tracking_id').val()},
                success:function(resp){
                    if(resp){
                        resp = JSON.parse(resp)
                        if(resp.status == 1){
                            $('#name').html(resp.name)
                            $('#address').html(resp.address)
                            $('[name="person_id"]').val(resp.id)
                            $('#details').show()
                            end_load()

                        }else if(resp.status == 2){
                            alert_toast("Unknow tracking id.",'danger');
                            end_load();
                        }
                    }
                }
            })
    }


</script>