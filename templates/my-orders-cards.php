
<main>
  <ul class="box-info">
	<li>
	<span class="text">
		<p>Total Requested Quotes</p>
		<h3><?php echo $total_quotes; ?></h3>
	  </span>
	  <i class="bx bxs-offer"></i>
	   
	</li>
	<li>
	<span class="text">
		<p>Total Spent Amount</p>
		<h3><?php echo wc_price( $total_amount ); ?></h3>
	  </span>
	  <i class="bx bx-money"></i>
	   
	</li>
	<li>
	<span class="text">
		<p>Total Pending Orders</p>
		<h3><?php echo $total_pending; ?></h3>
	  </span>
	  <i class="bx bxs-shopping-bag-alt"></i>
	  
	</li>
	<li>
	<span class="text">
		<p>Total Completed Orders</p>
		<h3><?php echo $total_completed; ?></h3>
	  </span>
	  <i class="bx bx-receipt"></i>
	   
	</li>
  </ul>
</main>
