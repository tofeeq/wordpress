<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Freedom
 * @subpackage Freedom/public/partials
 */
?>


<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<article class="order-form">
    <h2 class="h-custom-headline h3 accent">
      <span>Freedom Products</span>
    </h2>

    <div class="x-column x-sm x-1-1">
		<form method="post">
			<div class="x-column x-1-1">
				<div class="x-column x-5-6">
					<h6>Product</h6>
				</div>
				<div class="x-column x-1-6">
					<h6>Price</h6>
				</div>
			</div>

			<?php foreach ($products as $product) { ?>
			<div class="x-column x-1-1">
				<div class="x-column x-5-6">
					<label for="<?php echo $product->ProductID; ?>">
						<input type="checkbox" name="freedom_products[]" id="<?php echo $product->ProductID; ?>" value="<?php echo $product->ProductID; ?>">
						<?php echo $product->Description; ?>
					</label>
					<?php echo $product->Explanation; ?>
				</div>
				<div class="x-column x-1-6">
						$<?php echo sprintf("%.2f", $product->Price); ?>
				</div>
			</div>
			<?php } ?>
			<div class="x-column x-1-1">
				<hr>
				<button  class="btn btn-primary pull-right">Order Now</button>
			</div>

			<input type="hidden" name="action" value="freedom_checkout" />
			<input type="hidden" id="rep-number" name="rep-number" value="<?php 
				echo get_query_var('freedom_rep'); ?>" >
		    <input type="hidden" id="signup-id" name="signup-id" value="<?php 
		    	echo('SignUpID'); ?>" >

		</form>
	</div>
 </article>