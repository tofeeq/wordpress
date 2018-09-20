<div class="wrap">
	<h1 class="wp-heading-inline">View User</h1>

 	<a href="<?php echo admin_url('edit.php?post_type=ocenture') ?>" class="page-title-action">Go Back</a>
 	<a href="<?php echo admin_url('post.php?post=' . $_GET['post'] . '&action=edit') ?>" class="page-title-action">Edit User</a>
	<hr class="wp-header-end">
	<style>
		.oc_userinfo label {
			display: inline-block;
			width: 200px;
			font-weight: bold;
		}
		.oc_userinfo p {
			padding-left: 40px;
		}
		.oc_userinfo p span {
			display: inline-block;
		}
	</style>
	<div class="oc_userinfo">
		<h2><?php echo $title; ?></h2>
		<hr />
		<h3>Subscription Info</h3>
		<p>
			<label>Membership ID</label>
			<span><?php echo $membership_id; ?></span>
		</p>
		<p>
			<label>Product Code</label>
			<span><?php echo $product_code; ?></span>
		</p>
		<p>
			<label>Rep ID</label>
			<span><?php echo $rep_id; ?></span>
		</p>
		<p>
			<label>Status</label>
			<span><?php echo $status == 1 ? 'Activated' : 'Cancelled'; ?></span>
		</p>
		<p>
			<label>Order Date</label>
			<span><?php echo date("Y-m-d H:i:s", $date) ?></span>
		</p>
		<h3>User Info</h3>
		<p>
			<label>First Name</label>
			<span><?php echo $fname; ?></span>
		</p>
		<p>
			<label>Last Name</label>
			<span><?php echo $lname; ?></span>
		</p>
		<p>
			<label>Email</label>
			<span><?php echo $email; ?></span>
		</p>
		<p>
			<label>Phone</label>
			<span><?php echo $phone; ?></span>
		</p>
		<p>
			<label>Address</label>
			<span><?php echo $address; ?></span>
		</p>
		<p>
			<label>City</label>
			<span><?php echo $city; ?></span>
		</p>
		<p>
			<label>State</label>
			<span><?php echo $state; ?></span>
		</p>
		<p>
			<label>Zip</label>
			<span><?php echo $zip; ?></span>
		</p>
	</div>
</div>

