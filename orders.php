<?php include('db_connect.php');?>
<style>
	input[type=checkbox]
{
  /* Double-sized Checkboxes */
  -ms-transform: scale(1.3); /* IE */
  -moz-transform: scale(1.3); /* FF */
  -webkit-transform: scale(1.3); /* Safari and Chrome */
  -o-transform: scale(1.3); /* Opera */
  transform: scale(1.3);
  padding: 10px;
  cursor:pointer;
}
</style>

<?php



if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["edit_order"])) {
	$orderId = $_POST["edit_order"];
  
	// Retrieve the total_amount value for the specified order
	$totalAmountResult = $conn->query("SELECT total_amount FROM orders WHERE id = $orderId");
	if ($totalAmountResult->num_rows > 0) {
	  $row = $totalAmountResult->fetch_assoc();
	  $totalAmount = $row["total_amount"];
  
	  // Update the 'amount_tendered' column with the 'total_amount' value
	  $updateResult = $conn->query("UPDATE orders SET amount_tendered = $totalAmount WHERE id = $orderId");
	  if ($updateResult) {
		echo "Successfully updated";
	  } else {
		echo "Error updating " . $conn->error;
	  }
	}
  }

?>

<div class="container-fluid">
	
	<div class="col-lg-12">
		<div class="row mb-4 mt-4">
			<div class="col-md-12">
				
			</div>
		</div>
		<div class="row">
			<!-- FORM Panel -->
			<form method="post" action="">
			<input type="hidden" name="order_id" value="">
			<!-- Table Panel -->
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<b>List of Orders </b>
					</div>
					<div class="card-body">
						<table class="table table-condensed table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="">Date</th>
									<th class="">Total</th>
									<!-- <th class="">Order Number</th> -->
									<th class="">Amount</th>
									<th class="">Status</th>
									<th class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$order = $conn->query("SELECT * FROM orders order by unix_timestamp(date_created) desc ");
								while($row=$order->fetch_assoc()):
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td>
										<p> <b><?php echo date("M d,Y",strtotime($row['date_created'])) ?></b></p>
									</td>
									
									<td>
										<p> <b><?php echo $row['amount_tendered'] > 0 ? $row['amount_tendered'] : 'N/A' ?></b></p>
									</td>
									
									
									<td>
										<p class="text-right"> <b><?php echo number_format($row['total_amount'],2) ?></b></p>
									</td>
									<td class="text-center">
										<?php if($row['amount_tendered'] > 0): ?>
											<span class="badge badge-success">Paid</span>
										<?php else: ?>
											<span class="badge badge-primary">Unpaid</span>
										<?php endif; ?>
									</td>
									<td class="text-center">										
										<!-- <button class="btn btn-sm btn-outline-primary " type="button" onclick="location.href='billing/index.php?id=<?php echo $row['id'] ?>'" data-id="<?php echo $row['id'] ?>" >Edit</button> -->
										<?php if($row['amount_tendered'] > 0): ?>
											<button class="btn btn-sm btn-outline-primary" type="button" disabled>Paid</button>
										<?php else: ?>
											<button class="btn btn-sm btn-primary" type="submit" name="edit_order" value="<?php echo $row['id'] ?>">Pay now</button>
										<?php endif; ?>
										<button class="btn btn-sm btn-outline-primary view_order" type="button" data-id="<?php echo $row['id'] ?>">View</button>
										<button class="btn btn-sm btn-outline-danger delete_order" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
									</td>
								</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- Table Panel -->
		</form>
		</div>
	</div>	

</div>
<style>
	
	td{
		vertical-align: middle !important;
	}
	td p{
		margin: unset
	}
	img{
		max-width:100px;
		max-height: :150px;
	}
</style>

<script>
	$(document).ready(function(){
		$('table').dataTable()
	})
	$('#new_order').click(function(){
		uni_modal("New order ","manage_order.php","mid-large")
		
	})
	$('.view_order').click(function(){
		uni_modal("Order  Details","view_order.php?id="+$(this).attr('data-id'),"mid-large")
		
	})
	$('.delete_order').click(function(){
		_conf("Are you sure to delete this order ?","delete_order",[$(this).attr('data-id')])
	})
	function delete_order($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_order',
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
</script>