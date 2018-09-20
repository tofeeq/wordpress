 <article class="order-form">

    <h2 class="h-custom-headline h3 accent">
      <span>Products</span>
    </h2>

    <div class="x-column x-sm x-1-1">
      <div class="x-column x-sm x-3-5 ">
        <strong>Product</strong>
      </div>
      <div class="x-column x-sm x-1-5">
        <strong>Quantity</strong>
      </div>
      <div class="x-column x-sm x-1-5">
        <strong>Price</strong>
      </div>
    </div>

    <?php foreach ($products as $product) { ?>
      <div class="x-column x-sm x-1-1">
        <div class="x-column x-sm x-3-5 ">
          <?php echo $product->Description; ?>
        </div>
        <div class="x-column x-sm x-1-5">
          1
        </div>
        <div class="x-column x-sm x-1-5">
          $<?php echo sprintf("%.2f", $product->Price); ?>
        </div>
      </div>
    <?php 
      }
    ?>
    <p>&nbsp;</p>
    <h2 class="h-custom-headline h3 accent">
      <span>Checkout</span>
    </h2>
    <div role="form" class="wpcf7">
      <form id="freedom-checkout-form">
        <div class="x-column x-sm x-1-2">
          <h3 class="h-custom-headline h5 accent">
            <span>Billing Information</span>
          </h3>
          <p>
            <label class="tool-tip" for="sponsor-id" data-toggle="popover" data-placement="left" data-content="ID number for the person who referrred you. Use 9999, if unsure." data-original-title="<strong>Required</strong>">ID# of who referred you<span class="text-error"><strong>*</strong></span></label>
            <span>
               <input type="text" id="sponsor-id" name="sponsor-id" placeholder="ID#" onblur="freedomValidator.validateYGYID();" value="<?php 
        echo get_query_var('freedom_rep') ?>" <?php echo get_query_var('freedom_rep')
        ? 'readonly': "" ?>>
            </span>
            <span class="help-block">You must enter an ID number.</span>
          </p>
          <p>
            <label class="tool-tip" for="sponsor-name" data-toggle="popover" data-placement="left" data-content="Name of person who referred you." data-original-title="<strong>Required</strong>">Name of person who referred you<span class="text-error"><strong>*</strong></span></label>
            <span>
               <input type="text" id="sponsor-name" name="sponsor-name" placeholder="Referrer Name" value="<?php echo $repName; ?>" <?php echo get_query_var('freedom_rep')
        ? 'readonly': "" ?>>
            </span>
            <span class="help-block"></span>
          </p>

          <p>
            <label class="tool-tip" for="gender" data-toggle="popover" data-placement="top" data-content="Select your gender, this is used by the Telecare service." data-original-title="<strong>Required</strong>">Gender<span class="text-error"><strong>*</strong></span></label>
            <span>
              <select id="gender" name="gender" onblur="freedomValidator.validateGender();">
                <option value="">Gender</option>
                <option value="M">Male</option>
                <option value="F">Female</option>
              </select>
            </span>
              <span class="help-block"></span>
          </p>
      
          <p>
            <label class="tool-tip" for="birth-month" data-toggle="popover" data-placement="top" data-content="Enter your birthday, this is used by the Telecare service." data-original-title="<strong>Required</strong>">Birthday<span class="text-error"><strong>*</strong></span></label>
            <span>
              <span class="x-column x-sm x-1-3">
                <select name="birth-month" id="birth-month">
                  <option value="0">Month</option>

                  <?php for ($i = 1; $i <= 12; $i++ ) { ?>
                  <option value="<?php echo sprintf('%02d', $i); ?>"><?php echo sprintf('%02d', $i); ?></option>
                  <?php } ?>

                </select>
              </span>
              <span class="x-column x-sm x-1-3">
                <select name="birth-day" id="birth-day">
                  <option value="0">Day</option>

                  <?php for ($i = 1; $i <= 31; $i++ ) { ?>
                  <option value="<?php echo sprintf('%02d', $i); ?>"><?php echo sprintf('%02d', $i); ?></option>
                  <?php } ?>
                  
                </select>
              </span>

              <span class="x-column x-sm x-1-3">
                <select name="birth-year" id="birth-year">
                  <option value="0">Year</option>
                  <?php for ($i = date("Y"); $i > date("Y") - 100; $i-- ) { ?>
                  <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                  <?php } ?>
                </select>
              </span>  
            </span>
            <span class="help-block"></span> 
          </p>
          

          <p>
            <label class="tool-tip" for="first-name" data-toggle="popover" data-placement="top" data-content="Enter your First Name as it appears on your Credit Card." data-original-title="<strong>Required</strong>">First Name<span class="text-error"><strong>*</strong></span></label>
            <span>
              <input type="text" id="first-name" name="first-name" placeholder="First Name" onblur="freedomValidator.validateFirstName();">
            </span>
            <span class="help-block"></span> 
          </p>

          <p>
            <label class="tool-tip" for="last-name" data-toggle="popover" data-placement="top" data-content="Enter your Last Name as it appears on your Credit Card." data-original-title="<strong>Required</strong>">Last Name<span class="text-error"><strong>*</strong></span></label>
            <span>
              <input type="text" id="last-name" name="last-name" placeholder="Last Name" onblur="freedomValidator.validateLastName();">
            </span>
            <span class="help-block"></span> 
          </p>


          <p>
            <label class="tool-tip" for="email" data-toggle="popover" data-placement="top" data-content="Enter your Email Address." data-original-title="<strong>Required</strong>">E-mail<span class="text-error"><strong>*</strong></span></label>
            <span>
              <input type="text" id="email" name="email" placeholder="E-mail" onblur="freedomValidator.validateEmail();">
            </span>
            <span class="help-block"></span> 
          </p>


          <p style="display:none;">
            <label class="tool-tip" for="username" data-toggle="popover" data-placement="top" data-content="Enter a Username. Minimum 8 characters. Alphanumeric only." data-original-title="<strong>Required</strong>">Username<span class="text-error"><strong>*</strong></span></label>
            <span>
              <input type="text" id="username" name="username" placeholder="Username">
            </span>
            <span class="help-block"></span> 
          </p>


          <p style="display: none;">
            <label class="tool-tip" for="password-1" data-toggle="popover" data-placement="top" data-content="Enter your Password. Minimum 8 characters." data-original-title="<strong>Required</strong>">Password<span class="text-error"><strong>*</strong></span></label>
            <span>
              <input type="password" id="password-1" name="password-1" placeholder="Password">
            </span>
            <span class="help-block"></span> 
          </p>


          <p style="display: none;">
            <label class="tool-tip" for="password-2" data-toggle="popover" data-placement="top" data-content="Confirm your Password. It must match the Password above." data-original-title="<strong>Required</strong>">Confirm Password<span class="text-error"><strong>*</strong></span></label>
            <span>
              <input type="password" id="password-2" name="password-2" placeholder="Confirm Password">
            </span>
            <span class="help-block"></span> 
          </p>
          

          <p>
            <label class="tool-tip" for="phone" data-toggle="popover" data-placement="top" data-content="Enter your Phone Number." data-original-title="<strong>Required</strong>">Phone<span class="text-error"><strong>*</strong></span></label>
            <span>
              <input type="text" id="phone" name="phone" placeholder="Phone" onblur="freedomValidator.validatePhone();">
            </span>
            <span class="help-block"></span> 
          </p>

          <p style="display:none;">
            <label class="tool-tip" for="country" data-toggle="popover" data-placement="top" data-content="Select your Country as it appears on your Credit Card Billing Address." data-original-title="<strong>Required</strong>">Country<span class="text-error"><strong>*</strong></span></label>
            <span>
              <select id="country" name="country" onblur="freedomValidator.validateCountry();">
                <option value="USA">USA</option>
              </select>
            </span>
            <span class="help-block"></span> 
          </p>

          <p>
            <label class="tool-tip" for="zip" data-toggle="popover" data-placement="top" data-content="Enter your Zip/Postal Code as it appears on your Credit Card Billing Address." data-original-title="<strong>Required</strong>">Zip/Postal Code<span class="text-error"><strong>*</strong></span></label>
            <span>
              <input type="text" id="zip" name="zip" placeholder="Zip/Postal Code" onblur="freedomValidator.validateZip();">
            </span>
            <span class="help-block"></span> 
          </p>


          <p>
            <label class="tool-tip" for="street-1" data-toggle="popover" data-placement="top" data-content="Enter your Street Address as it appears on your Credit Card Billing Address." data-original-title="<strong>Required</strong>">Street Address<span class="text-error"><strong>*</strong></span></label>
            <span>
              <input type="text" id="street-1" name="street-1" placeholder="Street Address" onblur="freedomValidator.validateStreetAddress();">
            </span>
            <span class="help-block"></span> 
          </p>


          <p>
            <label class="tool-tip" for="street-2" data-toggle="popover" data-placement="top" data-content="Enter your APT, UNIT, STE as it appears on your Credit Card Billing Address." data-original-title="<strong>Optional</strong>">Street Address 2</label>
            <span>
              <input type="text" id="street-2" name="street-2" placeholder="Street Address 2">
            </span>  
            <span class="help-block"></span> 
          </p>

          <p>
            <label class="tool-tip" for="city" data-toggle="popover" data-placement="top" data-content="Enter your City as it appears on your Credit Card Billing Address." data-original-title="<strong>Required</strong>">City<span class="text-error"><strong>*</strong></span></label>
            <span>
              <input type="text" id="city" name="city" placeholder="City" onblur="freedomValidator.validateCity();">
            </span>
            <span class="help-block"></span> 
          </p>
          
          <p>
            <label class="tool-tip" for="state" data-toggle="popover" data-placement="top" data-content="Enter your State/Province as it appears on your Credit Card Billing Address." data-original-title="<strong>Required</strong>">State/Province<span class="text-error"><strong>*</strong></span></label>
            
            <span>
              <select id="state" name="state" placeholder="State/Province" onblur="freedomValidator.validateState();">
                <option value="AL">Alabama</option>
                <option value="AK">Alaska</option>
                <option value="AZ">Arizona</option>
                <option value="AR">Arkansas</option>
                <option value="CA">California</option>
                <option value="CO">Colorado</option>
                <option value="CT">Connecticut</option>
                <option value="DE">Delaware</option>
                <option value="DC">District Of Columbia</option>
                <option value="FL">Florida</option>
                <option value="GA">Georgia</option>
                <option value="HI">Hawaii</option>
                <option value="ID">Idaho</option>
                <option value="IL">Illinois</option>
                <option value="IN">Indiana</option>
                <option value="IA">Iowa</option>
                <option value="KS">Kansas</option>
                <option value="KY">Kentucky</option>
                <option value="LA">Louisiana</option>
                <option value="ME">Maine</option>
                <option value="MD">Maryland</option>
                <option value="MA">Massachusetts</option>
                <option value="MI">Michigan</option>
                <option value="MN">Minnesota</option>
                <option value="MS">Mississippi</option>
                <option value="MO">Missouri</option>
                <option value="MT">Montana</option>
                <option value="NE">Nebraska</option>
                <option value="NV">Nevada</option>
                <option value="NH">New Hampshire</option>
                <option value="NJ">New Jersey</option>
                <option value="NM">New Mexico</option>
                <option value="NY">New York</option>
                <option value="NC">North Carolina</option>
                <option value="ND">North Dakota</option>
                <option value="OH">Ohio</option>
                <option value="OK">Oklahoma</option>
                <option value="OR">Oregon</option>
                <option value="PA">Pennsylvania</option>
                <option value="RI">Rhode Island</option>
                <option value="SC">South Carolina</option>
                <option value="SD">South Dakota</option>
                <option value="TN">Tennessee</option>
                <option value="TX">Texas</option>
                <option value="UT">Utah</option>
                <option value="VT">Vermont</option>
                <option value="VA">Virginia</option>
                <option value="WA">Washington</option>
                <option value="WV">West Virginia</option>
                <option value="WI">Wisconsin</option>
                <option value="WY">Wyoming</option>

                <optgroup label="US Territories/Armed Forces">
                  <option value="AS">American Samoa</option>
                  <option value="GU">Guam</option>
                  <option value="MP">Northern Mariana Islands</option>
                  <option value="PR">Puerto Rico</option>
                  <option value="UM">United States Minor Outlying Islands</option>
                  <option value="VI">Virgin Islands</option>
                  <option value="AA">Armed Forces Americas</option>
                  <option value="AP">Armed Forces Pacific</option>
                  <option value="AE">Armed Forces Others</option>
                </optgroup>

              </select>
            </span>
            <span class="help-block"></span> 
          </p>
          
          <!-- credit card section -->
          <h3 class="h-custom-headline h5 accent">
            <span>Credit Card Information</span>
          </h3>
          <em>This transaction will appear on your bill as <strong>Youngevity</strong>.</em><br /><br />

          <p>
            <label for="creditcard">Credit Card Number</label>
            <span>
              <input type="text" id="creditcard" name="creditcard" placeholder="Credit Card Number" maxlength="16"  onkeyup="freedomValidator.validateCCNumber();">
            </span>
            <span class="help-block"></span> 
          </p>

          <p>
            <label for="exp-month">Expiration Month</label>
            <span>
              <select id="exp-month" name="exp-month" onchange="freedomValidator.validateCCMonth();">
                <option value="01">01 / Jan</option>
                <option value="02">02 / Feb</option>
                <option value="03">03 / Mar</option>
                <option value="04">04 / Apr</option>
                <option value="05">05 / May</option>
                <option value="06">06 / Jun</option>
                <option value="07">07 / Jul</option>
                <option value="08">08 / Aug</option>
                <option value="09">09 / Sep</option>
                <option value="10">10 / Oct</option>
                <option value="11">11 / Nov</option>
                <option value="12">12 / Dec</option>
              </select>
            </span>
            <span class="help-block"></span> 
          </p>


          <p>
            <label for="exp-year">Expiration Year</label>
            <span>
              <select id="exp-year" name="exp-year"  onchange="freedomValidator.validateCCYear();">

                <?php 
                $dateFull = date("Y");
                $date = date("y");

                for ($i = 0; $i <= 20; $i++ ) { ?>
                <option value="<?php echo $date + $i; ?>"><?php echo $dateFull + $i; ?></option>
                <?php } ?>

              </select>
            </span>
            <span class="help-block"></span> 
          </p>
          
           <p>
            <label for="cardverify">Credit Card Verification</label>
            <span>
              <input type="text" id="cardverify" name="cardverify" placeholder="Credit Card Verification" maxlength="4" onkeyup="freedomValidator.validateCVV();">
            </span>
            <span class="help-block"></span> 
          </p>

          <!-- end first column -->
        </div>
      


        <!-- second column -->
        <div class="x-column x-sm x-1-2 last">
          <h3 class="h-custom-headline h5 accent">
            <span>Mailing Information</span>
          </h3>

          <p>
            <label class="checkbox" for="same-shipping">
              <input type="checkbox" id="same-shipping" name="same-shipping" value="1">
              <span class="h6">
                <strong>My billing address is my mailing address.</strong>
              </span>
            </label>
          </p>

          <div id="ship-box">
            <p style="display:none;">
              <label tool-tip"="" for="country-ship" data-toggle="popover" data-placement="top" data-content="Select your Shipping Country." data-original-title="<strong>Required</strong>">Country<span class="text-error"><strong>*</strong></span>
              <span>
                <select id="country-ship" name="country-ship" onblur="freedomValidator.validateCountryShip();">
                  <option value="USA">USA</option>
                </select>
              </span>
              <span class="help-block"></span> 
            </p>


            <p>
              <label for="zip-ship" data-toggle="popover" data-placement="top" data-content="Enter your Shipping Zip/Postal Code." data-original-title="<strong>Required</strong>">Zip/Postal Code<span class="text-error"><strong>*</strong></span></label>
              <span>
                <input type="text" id="zip-ship" name="zip-ship" placeholder="Zip/Postal Code" onblur="freedomValidator.validateZipShip();">
              </span>
              <span class="help-block"></span> 
            </p>

            <p>
              <label for="street-1-ship" data-toggle="popover" data-placement="top" data-content="Enter your Shipping Street Address." data-original-title="<strong>Required</strong>">Street Address<span class="text-error"><strong>*</strong></span></label>
              <span>
                <input type="text" id="street-1-ship" name="street-1-ship" placeholder="Street Address" onblur="freedomValidator.validateStreetAddressShip();">
              </span>
              <span class="help-block"></span> 
            </p>

            <p>
              <label for="street-2-ship" data-toggle="popover" data-placement="top" data-content="Enter your Shipping APT, UNIT, STE." data-original-title="<strong>Optional</strong>">Street Address 2</label>
              <span>
                <input type="text" id="street-2-ship" name="street-2-ship" placeholder="Street Address 2">
              </span>
              <span class="help-block"></span> 
            </p>


            <p>
              <label for="city-ship" data-toggle="popover" data-placement="top" data-content="Enter your Shipping City." data-original-title="<strong>Required</strong>">City<span class="text-error"><strong>*</strong></span></label>
              <span>
                <input type="text" id="city-ship" name="city-ship" placeholder="City" onblur="freedomValidator.validateCityShip();">
              </span>
              <span class="help-block"></span> 
            </p>

            <p>
              <label for="state-ship" data-toggle="popover" data-placement="top" data-content="Enter your Shipping State/Province." data-original-title="<strong>Required</strong>">State/Province
              <span class="text-error"><strong>*</strong></span></label>
              <span>
                <select id="state-ship" name="state-ship" placeholder="State/Province" onblur="freedomValidator.validateStateShip();">
                  <option value="AL">Alabama</option>
                  <option value="AK">Alaska</option>
                  <option value="AZ">Arizona</option>
                  <option value="AR">Arkansas</option>
                  <option value="CA">California</option>
                  <option value="CO">Colorado</option>
                  <option value="CT">Connecticut</option>
                  <option value="DE">Delaware</option>
                  <option value="DC">District Of Columbia</option>
                  <option value="FL">Florida</option>
                  <option value="GA">Georgia</option>
                  <option value="HI">Hawaii</option>
                  <option value="ID">Idaho</option>
                  <option value="IL">Illinois</option>
                  <option value="IN">Indiana</option>
                  <option value="IA">Iowa</option>
                  <option value="KS">Kansas</option>
                  <option value="KY">Kentucky</option>
                  <option value="LA">Louisiana</option>
                  <option value="ME">Maine</option>
                  <option value="MD">Maryland</option>
                  <option value="MA">Massachusetts</option>
                  <option value="MI">Michigan</option>
                  <option value="MN">Minnesota</option>
                  <option value="MS">Mississippi</option>
                  <option value="MO">Missouri</option>
                  <option value="MT">Montana</option>
                  <option value="NE">Nebraska</option>
                  <option value="NV">Nevada</option>
                  <option value="NH">New Hampshire</option>
                  <option value="NJ">New Jersey</option>
                  <option value="NM">New Mexico</option>
                  <option value="NY">New York</option>
                  <option value="NC">North Carolina</option>
                  <option value="ND">North Dakota</option>
                  <option value="OH">Ohio</option>
                  <option value="OK">Oklahoma</option>
                  <option value="OR">Oregon</option>
                  <option value="PA">Pennsylvania</option>
                  <option value="RI">Rhode Island</option>
                  <option value="SC">South Carolina</option>
                  <option value="SD">South Dakota</option>
                  <option value="TN">Tennessee</option>
                  <option value="TX">Texas</option>
                  <option value="UT">Utah</option>
                  <option value="VT">Vermont</option>
                  <option value="VA">Virginia</option>
                  <option value="WA">Washington</option>
                  <option value="WV">West Virginia</option>
                  <option value="WI">Wisconsin</option>
                  <option value="WY">Wyoming</option>

                  <optgroup label="US Territories/Armed Forces">
                    <option value="AS">American Samoa</option>
                    <option value="GU">Guam</option>
                    <option value="MP">Northern Mariana Islands</option>
                    <option value="PR">Puerto Rico</option>
                    <option value="UM">United States Minor Outlying Islands</option>
                    <option value="VI">Virgin Islands</option>
                    <option value="AA">Armed Forces Americas</option>
                    <option value="AP">Armed Forces Pacific</option>
                    <option value="AE">Armed Forces Others</option>
                  </optgroup>
                </select>
              </span>
              <span class="help-block"></span> 
            </p>
          </div>

          <!-- terms and conditions -->
          <h3 class="h-custom-headline h5 accent">
            <span>Terms and Conditions</span>
          </h3>
          <div id="tc-box" class="well">
            <p><strong>Youngevity International Payment Gateway</strong></p>
            <p><strong>Terms of Use and Return Policy</strong></p>
            <p>Thank you for your patronage and welcome to the Youngevity International Payment Gateway. As such, YGYI has provided this secure payment gateway to facilitate the purchase of YGYI products. YGYI's customer service staff is also available for information specific to your purchase (see customer service contact information below). Purchases made through this gateway are subject to YGYI's standard terms of use and product cancellation/return policy. Please review the following terms carefully as they govern the use of this Payment Gateway, and your use of same constitutes agreement to these terms and conditions. Please be aware that these terms may change from time to time as deemed necessary by YGYI. If at the time of purchase you do not agree to these terms, please do not use this payment gateway.</p>
            <p><strong>Merchandise Pricing and Availability</strong></p>
            <p>YGYI prides itself in providing exclusive, quality merchandise at a reasonable price.  Every attempt is made by YGYI to ensure that the pricing and product information on the referring site is true and correct, however, should there be a discrepancy in pricing, the pricing on the completed order through this gateway shall be considered the current price and shall prevail.</p>
            <p>As YGYI products are from natural sources, and many different ingredients from many different sources go into each product, availability of some items may be limited from time to time. Display of an item on the referring site is not a guarantee that the item is currently in stock or that it will be available at any time in the future. For any items that are unavailable at the time your order is processed (either on backorder or discontinued), you will have the option of substituting that item for another available item, or cancelling a portion of, or your entire order for a full refund. If you have any questions regarding the current or future availability of any item, please use the customer service contact information below. YGYI reserves the right, without prior notice, to limit the order quantity on any product and/or to refuse service to any customer that we believe would violate any of our business practices, applicable laws or policies. We also may require verification of information prior to the acceptance and/or shipment of any order.  No shipment of any order will be unreasonably withheld. Any and all refused orders will be promptly cancelled and refunded.</p>
            <p><strong>Product Information</strong></p>
            <p>The products displayed on the referring site are intended for sale in, and to be shipped to addresses in the United States.  Some products, however, may also be shipped outside of the United States for personal consumption only. Please use the customer service contact information below for more information on products that may or may not be shipped outside of the United States. For any shipments outside the United States, additional shipping and handling charges may be applied upon or subsequent to order processing. All prices are in U.S. Dollars and are valid and effective only in the United States.</p>
            <p><strong>Links to Other Web Sites and Services</strong></p>
            <p>The referring site may contain links to non-YGYI sites. YGYI does not assume any responsibility for, nor does YGYI endorse any content of any non-YGYI sites.</p>
            <p><strong>Refund Policy</strong></p>
            <p>In accordance with the Refund policy, no refunds are given or offered after 30 days from the date product is received by the customer. Any and all returns must include an RMA (Returned Merchandise Authorization).  Please use the customer service information below to initiate a merchandise return, or use the contact information included with the invoice accompanying your shipment.  Upon proper receipt of merchandise by YGYI, a credit will be issued within 7-14 business days.  All sales on food items are final, including chocolates.</p>
            <p><strong>Customer Service Contact Information</strong></p>
            <p>Youngevity<br>
              2400 Boswell Rd<br>
              Chula Vista, CA, 91914<br>
              Office Hours: Mon-Fri 7:00 am to 5:00 pm (Pacific Standard Time)<br>
              (800) 982-3189</p>
          </div>  


          <p>
            <label class="checkbox" for="terms-conditions" id="accept-terms">
              <input type="checkbox" id="terms-conditions" name="terms-conditions" value="1" onclick="freedomValidator.validateTC();">

              <span class="h6"><strong>Please check to agree to the Terms and Conditions.<span class="text-error"><strong>*</strong></span></strong></span>
            </label>
          </p>
          
          <p>
            <button type="button" class="btn btn-primary pull-right" id="freedom-checkout-btn">Submit Order <i class="icon-circle-arrow-right"></i></button>
          </p>
          
          <p>    
            <div id="checkout-msg"></div>
          </p>
          <!-- end second column -->
        </div> 
      
        <!-- hidden items -->
        <div class="hidden">
          <input type="hidden" id="cart" name="cart" value="<?php echo implode(",", $cart) ?>">
        </div>


      </form>
    </div>
  
</article>