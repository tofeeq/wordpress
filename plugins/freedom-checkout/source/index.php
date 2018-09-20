<?php

define('INCLUDED',1);
require_once('includes/checkout.php');
?>
<style type="text/css">
  .as-checkbox {
   margin-top: 0 !important;
 }
 .autoship-month {
   line-height: 30px;
   text-align: right;
   padding-right: 15px;
 }
/* .row-fluid .span12 {
  width: 70%;
}*/
.form-horizontal .control-group, .controls .help-block {
  clear: both;
}
.s-wrapper{
  width: 100%;
  text-align: center;
}
.s-wrapper .span12{
  text-align: left;
  float: none;
}
</style>
<!--[if lt IE 8]><script src="includes/ie6/warning.js"></script><script>window.onload=function(){e("includes/ie6/")}</script><![endif]-->
<?php ?>
<div id="cart" style="display: none;">
  <!--<h4>Need to update the items your cart? <a href="../cart.php" target="_parent">Click here to go back.</a></h4>-->
  <?php if(!empty($cart['items']) || !empty($cart['packages'])) : ?>
    <table class="table table-striped table-condensed table-bordered table-hover" id="order-table">
      <thead>
        <tr>
          <th class="">Product ID</th>
          <th class="">Product</th>
          <th class=""><input type="checkbox" class="as-checkbox" id="as-checkall" name="as-checkall">
            &nbsp;  Autoship (automatic monthly order) <br>
            Free Shipping on Orders over $50</th>
            <th class="">Quantity</th>
            <th class="">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php if(count($cart['items']) > 0) : ?>
            <?php $i = 1; foreach($cart['items'] as $items => $item):?>
            <tr>
              <td><?php echo $item['id']; ?></td>
              <td><h5 class="no-margin"><?php echo $item['name']; ?></h5>
                <?php
                if( in_array($item['id'], $prop65items)) echo '<span class="prop65" style="display:none;">WARNING: This product contains a chemical known to the State of California to cause birth defects, or other reproductive harm.</span>';
                elseif(in_array($item['id'], $prop65canceritems)) echo '<span class="prop65" style="display:none;">WARNING: This product contains a chemical known to the State of California to cause cancer, birth defects, or other reproductive harm.</span>';
                ?></td>
                <td class=""><input type="checkbox" class="as-checkbox" id="as-item-checkbox-<?= $i;?>" name="as-item-checkbox-<?= $i;?>" data-sku="<?= $item['id'];?>" data-qty="<?= $item['qty'];?>">
                  Autoship this item </td>
                  <td class="text-right"><?php echo $item['qty']; ?> <br/>
                    <small>(@ $<?php echo number_format($item['price'], 2, '.', ','); ?> each)</small></td>
                    <td class="text-right">$<?php echo number_format($item['qty']*$item['price'], 2, '.', ','); ?></td>
                  </tr>
                  <?php $i++; endforeach; ?>
                <?php endif; ?>
                <?php if(is_array($cart['packages']) && count($cart['packages']) > 0) : ?>
                  <?php  $i = 1; foreach($cart['packages'] as $packages => $pack):?>
                  <tr>
                    <td><ul>
                      <?php foreach($pack['items'] as $items => $item):?>
                        <li><?php echo $item['id']; ?></li>
                      <?php endforeach; ?>
                    </ul></td>
                    <td><h5 class="no-margin"><?php echo $pack['name']; ?></h5>
                      <ul>
                        <?php foreach($pack['items'] as $items => $item):?>
                          <li><?php echo $item['qty']; ?> &ndash; <?php echo ucwords(strtolower($item['name'])); ?></li>
                        <?php endforeach; ?>
                      </ul></td>
                      <td class=""><input type="checkbox" class="as-checkbox" id="as-pkg-checkbox-<?= $i;?>" name="as-pkg-checkbox-<?= $i;?>" data-sku="<?= $item['id'];?>" data-qty="<?= $item['qty'];?>">
                        Autoship this item </td>
                        <td class="text-right"><?php echo $pack['qty']; ?> <br/>
                          <small>(@ $<?php echo number_format($pack['price'], 2, '.', ','); ?> each)</small></td>
                          <td class="text-right">$<?php echo number_format($pack['qty']*$pack['price'], 2, '.', ','); ?></td>
                        </tr>
                        <?php $i++; endforeach; ?>
                      <?php endif; ?>
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="5"><strong>Subtotal</strong><span class="pull-right" id="sub-total">
                          <?php if( isset($cart['subtotal'])) echo '$ '.number_format($cart['subtotal'], 2, '.', ','); ?>
                        </span></td>
                      </tr>
                      <?php if(empty($cart['shipmethod']) && empty($cart['shiptotal']) && empty($cart['taxtotal']) && empty($cart['total'])) : ?>
                        <tr>
                          <td colspan="5" class="muted"><strong>Shipping/Handling <span id="order-shipping"></span></strong><span class="pull-right" id="ship-total"><i class="icon icon-caret-up"></i></span></td>
                        </tr>
                        <tr>
                          <td colspan="5" class="muted"><strong>Tax</strong><span class="pull-right" id="order-tax">Login or Use Estimate Below.</span></td>
                        </tr>
                        <tr>
                          <td colspan="5" class="muted"><strong>Total</strong><span class="pull-right" id="order-total"><i class="icon icon-caret-down"></i></span></td>
                        </tr>
                      <?php else : ?>
                        <tr>
                          <td colspan="5" ><strong>Shipping/Handling <span id="order-shipping"><?php echo $cart['shipmethod'];?></span></strong><span class="pull-right" id="ship-total">$ <?php echo $cart['shiptotal']; ?></span></td>
                        </tr>
                        <tr>
                          <td colspan="5"><strong>Tax</strong><span class="pull-right" id="order-tax">$ <?php echo $cart['taxtotal']; ?></span></td>
                        </tr>
                        <tr>
                          <td colspan="5"><strong>Total</strong><span class="pull-right" id="order-total">$ <?php echo $cart['total']; ?></span></td>
                        </tr>
                      <?php endif; ?>
                    </tfoot>
                  </table>
                <?php else : ?>
                  <h4>No Item(s) in Cart</h4>
                <?php endif; ?>
              </div>
              <?php ?>
              <div id="content">
                <hr>
                <noscript>
                  <div id="nojs" class="well text-center">
                    <h4>This checkout form requires Javascript to function properly. Please enable Javascript and reload the page.</h4>
                  </div>
                </noscript>
                <div class="order-form dac-sign-up">
                  <div class="row-fluid" id="bill-ship-block" >
                    <form class="form-horizontal">
                      <div class="s-wrapper">
                        <div class="span12">
                          <h3>Agent Information
            <?php /*if(!$loggedin) : ?>
            <a href="#login" id="already-member" role="button" class="btn" data-toggle="modal">Already a member? Login here.</a>
          <?php endif; */?>
        </h3>
        <input type="hidden" id="rep-number" name="rep-number" <?php form_fill('RepNumber'); ?>>
        <input type="hidden" id="signup-id" name="signup-id" <?php form_fill('SignUpID'); ?>>

        <?php 
        /*
        $sponsor_id = null;
        $sponsor_name = null;
        if (isset($_COOKIE['_rep'])){
          $sponsor = get_sponsor_info($_COOKIE['_rep']);
          $sponsor_id =  $sponsor['RepDID'];
          $sponsor_name = $sponsor['FirstName']. ' ' . $sponsor['LastName'];
        }*/
        ?>
        <p>Fields marked with an<span class="text-error">*</span> are required</p>
        <div class="control-group" >
          <label class="control-label tool-tip" for="sponsor-id" data-toggle="popover" data-placement="left" data-content="Agent ID# number for the person who referred you." data-original-title="Required">Agent ID# of who referred you<span class="text-error"><strong>*</strong></span></label>
          <div class="controls">
            <input type="text" id="sponsor-id"  display: inline-block;" name="sponsor-id" placeholder="Agent ID#" class="span12" value="<?php /*echo $sponsor_id;*/ ?>" <?php /*echo $sponsor_id ? 'disabled' : ''; */?>> <button type="button" class="btn btn-primary validate-sponsor-id"  onclick="validateYGYID();">Validate</button>
            <span class="help-block"></span> </div>
          </div>

          <div class="control-group" >
            <label class="control-label tool-tip" for="referer-name" data-toggle="popover" data-placement="left" data-content="Enter the name of person who referred you.">Name of person who referred you<span class="text-error"></span></label>
            <div class="controls">
              <input type="text" id="referer-name" name="referer-name"  onblur="validateRefererName();" placeholder="Referer's Name" class="span12" value="<?php /*echo $sponsor_name;*/ ?>" <?php /*echo $sponsor_name ? 'disabled' : ''; */?>>
              <span class="help-block"></span> </div>
            </div>


            <div class="control-group">
              <label class="control-label tool-tip" for="first-name" data-toggle="popover" data-placement="top" data-content="Enter your First Name" data-original-title="Required">First Name<span class="text-error"><strong>*</strong></span></label>
              <div class="controls">
                <input type="text" id="first-name" name="first-name" placeholder="First Name" class="span12" onblur="validateFirstName();" <?php form_fill('Firstname'); ?>>
                <span class="help-block"></span> </div>
              </div>
              <div class="control-group">
                <label class="control-label tool-tip" for="last-name" data-toggle="popover" data-placement="top" data-content="Enter your Last Name" data-original-title="Required">Last Name<span class="text-error"><strong>*</strong></span></label>
                <div class="controls">
                  <input type="text" id="last-name"  name="last-name" placeholder="Last Name" class="span12" onblur="validateLastName();" <?php form_fill('Lastname'); ?>>
                  <span class="help-block"></span> </div>
                </div>
                <div id="signup-account-info">
                  <div class="control-group">
                    <label class="control-label tool-tip" for="username" data-toggle="popover" data-placement="top" data-content="Enter a Username. Minimum 8 characters. Alphanumeric only." data-original-title="Required">Username<span class="text-error"><strong>*</strong></span></label>
                    <div class="controls">
                      <input type="text" id="username"  name="username" placeholder="Username" class="span12" >
                      <span class="help-block"></span> </div>
                    </div>
                    <div class="control-group">
                      <label class="control-label tool-tip" for="email" data-toggle="popover" data-placement="top" data-content="Enter your Email Address." data-original-title="Required">E-mail<span class="text-error"><strong>*</strong></span></label>
                      <div class="controls">
                        <input type="text" id="email"  name="email" placeholder="E-mail" class="span12" >
                        <span class="help-block"></span> </div>
                      </div>
                      <div class="control-group">
                        <label class="control-label tool-tip" for="password-1" data-toggle="popover" data-placement="top" data-content="Enter your Password. Minimum 6 characters." data-original-title="Required">Password<span class="text-error"><strong>*</strong></span></label>
                        <div class="controls">
                          <input type="password" id="password-1"  name="password-1" placeholder="Password" class="span12" onblur="validatePassword();">
                          <span class="help-block"></span> </div>
                        </div>
                        <div class="control-group">
                          <label class="control-label tool-tip" for="password-2" data-toggle="popover" data-placement="top" data-content="Confirm your Password. It must match the Password above." data-original-title="Required">Confirm Password<span class="text-error"><strong>*</strong></span></label>
                          <div class="controls">
                            <input type="password" id="password-2"  name="password-2" placeholder="Confirm Password" class="span12" onblur="verifyPassword();">
                            <span class="help-block"></span> </div>
                          </div>
                          <?php if($enrollment_item): ?>
                            <div class="control-group">
                              <label class="control-label" for="tax-id" data-toggle="popover" data-placement="top" data-content="Enter your tax ID number." data-original-title="Required">Tax ID<span class="text-error"><strong>*</strong></span></label>
                              <div class="controls">
                                <input type="text" id="tax-id"  name="tax-id" placeholder="Tax ID (SSN, EIN, ITIN)" class="span12" onblur="validateTaxID();">
                                <span class="help-block"></span> </div>
                              </div>
                            <?php endif; ?>
                          </div>
                          <div class="control-group">
                            <label class="control-label tool-tip" for="phone" data-toggle="popover" data-placement="top" data-content="Enter your Phone Number." data-original-title="Required">Phone<span class="text-error"><strong>*</strong></span></label>
                            <div class="controls">
                              <input type="text" id="phone"  name="phone" placeholder="Phone" class="span12" onblur="validatePhone();" <?php form_fill('Phone1'); ?>>
                              <span class="help-block"></span> </div>
                            </div>
                            <div class="control-group">
                              <label class="control-label tool-tip" for="tax_id" data-toggle="popover" data-placement="top" data-content="Enter your Social Security or Tax ID." data-original-title="Required">Social Security or Tax ID#<span class="text-error"><strong>*</strong></span></label>
                              <div class="controls">
                                <input type="text" id="tax_id"  name="tax_id" placeholder="Tax ID" class="span12" <?php form_fill('TaxId'); ?>>
                                <span class="help-block"></span> </div>
                              </div>
                              <div class="control-group">
                                <label class="control-label tool-tip" for="city" data-toggle="popover" data-placement="top" data-content="Enter your Company Name." >Company Name (if doing business under a legal entity)</span></label>
                                <div class="controls">
                                  <input type="text" id="company_name"  name="company_name" placeholder="Company Name" class="span12" <?php form_fill('CompanyName'); ?>>
                                  <span class="help-block"></span> </div>
                                </div>
                                <div class="control-group">
                                  <label class="control-label tool-tip" for="country" data-toggle="popover" data-placement="top" data-content="Select your Country." data-original-title="Required">Country<span class="text-error"><strong>*</strong></span></label>
                                  <div class="controls">
                                    <select id="country"  name="country" class="span12" onblur="validateCountry();">
                                      <option value="USA" <?php form_fill_select('BillCountry', 'USA'); ?>>USA</option>
                                      <!--<option value="CANADA" <?php form_fill_select('BillCountry', 'CANADA'); ?>>Canada</option>-->
                                    </select>
                                    <span class="help-block"></span> </div>
                                  </div>

                                  <div class="control-group">
                                    <label class="control-label tool-tip" for="street-1" data-toggle="popover" data-placement="top" data-content="Enter your Street Address." data-original-title="Required">Street Address<span class="text-error"><strong>*</strong></span></label>
                                    <div class="controls">
                                      <input type="text" id="street-1"  name="street-1" placeholder="Street Address" class="span12" onblur="validateStreetAddress();" <?php form_fill('BillStreet1'); ?>>
                                      <span class="help-block"></span> </div>
                                    </div>
                                    <div class="control-group">
                                      <label class="control-label tool-tip" for="street-2" data-toggle="popover" data-placement="top" data-content="Enter your APT, UNIT, STE." data-original-title="Optional">Street Address 2</label>
                                      <div class="controls">
                                        <input type="text" id="street-2"  name="street-2" placeholder="Street Address 2" class="span12" <?php form_fill('BillStreet2'); ?>>
                                        <span class="help-block"></span> </div>
                                      </div>
                                      <div class="control-group">
                                        <label class="control-label tool-tip" for="city" data-toggle="popover" data-placement="top" data-content="Enter your City." data-original-title="Required">City<span class="text-error"><strong>*</strong></span></label>
                                        <div class="controls">
                                          <input type="text" id="city"  name="city" placeholder="City" class="span12" onblur="validateCity();" <?php form_fill('BillCity'); ?>>
                                          <span class="help-block"></span> </div>
                                        </div>
                                        <div class="control-group">
                                          <label class="control-label tool-tip" for="state" data-toggle="popover" data-placement="top" data-content="Enter your State/Province." data-original-title="Required">State/Province<span class="text-error"><strong>*</strong></span></label>
                                          <div class="controls">
                                            <select id="state"  name="state" placeholder="State/Province" class="span12" onblur="validateState();" <?php /*form_fill_select('BillState');*/ ?>>
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
                                            <span class="help-block"></span> 
                                          </div>
                                        </div>
                                        <div class="control-group">
                                          <label class="control-label tool-tip" for="zip" data-toggle="popover" data-placement="top" data-content="Enter your Zip/Postal Code." data-original-title="Required">Zip/Postal Code<span class="text-error"><strong>*</strong></span></label>
                                          <div class="controls">
                                            <input type="text" id="zip"  name="zip" placeholder="Zip/Postal Code" class="span12" <?php form_fill('BillPostalCode'); ?>>
                                            <span class="help-block"></span> </div>
                                          </div>
                                          <?php if((!empty($cart['items']) || !empty($cart['packages'])) && false ) : ?>
                                            <div id="credit-card-block">
                                              <h3>Credit Card Information</h3>
                                              <p class="muted">This transaction will appear on your bill as <strong>Youngevity</strong>.</p>
                                              <div class="control-group">
                                                <label class="control-label" for="creditcard">Credit Card Number</label>
                                                <div class="controls">
                                                  <input type="text" id="creditcard"  name="creditcard" placeholder="Credit Card Number" maxlength="16" class="span12" onblur="validateCCNumber();">
                                                  <span class="help-block"></span> </div>
                                                </div>
                                                <div class="control-group">
                                                  <label class="control-label" for="exp-month">Expiration Month</label>
                                                  <div class="controls">
                                                    <select id="exp-month"  name="exp-month" class="span12" onchange="validateCCMonth();">
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
                                                    <span class="help-block"></span> </div>
                                                  </div>
                                                  <div class="control-group">
                                                    <label class="control-label" for="exp-year">Expiration Year</label>
                                                    <div class="controls">
                                                      <select id="exp-year"  name="exp-year" class="span12" onchange="validateCCYear();">
                                                        <?php for($i=0; $i<21; $i++) :?>
                                                          <option value="<?php echo date("y")+$i; ?>"><?php echo date("Y")+$i; ?></option>
                                                        <?php endfor; ?>
                                                      </select>
                                                      <span class="help-block"></span> </div>
                                                    </div>
                                                    <div class="control-group">
                                                      <label class="control-label" for="cardverify">Credit Card Verification</label>
                                                      <div class="controls">
                                                        <input type="text" id="cardverify"  name="cardverify" placeholder="Credit Card Verification" maxlength="4"  class="span12" onkeyup="validateCVV();">
                                                        <span class="help-block"></span> </div>
                                                      </div>
                                                    </div>
                                                  <?php endif; ?>
           <? /*
        </div>

        <div class="span6">
          <h3>Shipping Information <?php /*<a href="#cart" id="estimate-shipping-btn" role="button" class="btn" onclick="estimateShipping();" style="font-weight:normal !important;">Estimate Shipping and Tax.</a>  ?></h3>
          <label class="checkbox" for="same-shipping">
          <input type="checkbox" id="same-shipping" name="same-shipping" value="1" <?php form_fill_checkbox('SameShip',$user['SameShip']); ?>>
          <strong>My billing address is my shipping address.</strong>
          </label>
          <div id="ship-box">
            <div class="control-group">
              <label class="control-label tool-tip" for="country-ship" data-toggle="popover" data-placement="top" data-content="Select your Shipping Country." data-original-title="Required">Country<span class="text-error"><strong>*</strong></span></label>
              <div class="controls">
                <select id="country-ship"  name="country-ship" class="span12" onblur="validateCountryShip();">
                  <option value="USA" <?php form_fill_select('ShipCountry', 'USA'); ?>>USA</option>
                  <option value="CANADA" <?php form_fill_select('ShipCountry', 'CANADA'); ?>>Canada</option>
                </select>
                <span class="help-block"></span> </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="zip-ship" data-toggle="popover" data-placement="top" data-content="Enter your Shipping Zip/Postal Code." data-original-title="Required">Zip/Postal Code<span class="text-error"><strong>*</strong></span></label>
              <div class="controls">
                <input type="text" id="zip-ship"  name="zip-ship" placeholder="Zip/Postal Code" class="span12" onblur="validateZipShip();" <?php form_fill('ShipPostalCode'); ?>>
                <span class="help-block"></span> </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="street-1-ship" data-toggle="popover" data-placement="top" data-content="Enter your Shipping Street Address." data-original-title="Required">Street Address<span class="text-error"><strong>*</strong></span></label>
              <div class="controls">
                <input type="text" id="street-1-ship"  name="street-1-ship" placeholder="Street Address" class="span12" onblur="validateStreetAddressShip();" <?php form_fill('ShipStreet1'); ?>>
                <span class="help-block"></span> </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="street-2-ship" data-toggle="popover" data-placement="top" data-content="Enter your Shipping APT, UNIT, STE." data-original-title="Optional">Street Address 2</label>
              <div class="controls">
                <input type="text" id="street-2-ship"  name="street-2-ship" placeholder="Street Address 2" class="span12" <?php form_fill('ShipStreet2'); ?>>
                <span class="help-block"></span> </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="city-ship" data-toggle="popover" data-placement="top" data-content="Enter your Shipping City." data-original-title="Required">City<span class="text-error"><strong>*</strong></span></label>
              <div class="controls">
                <input type="text" id="city-ship"  name="city-ship" placeholder="City" class="span12" onblur="validateCityShip();" <?php form_fill('ShipCity'); ?>>
                <span class="help-block"></span> </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="state-ship" data-toggle="popover" data-placement="top" data-content="Enter your Shipping State/Province." data-original-title="Required">State/Province<span class="text-error"><strong>*</strong></span></label>
              <div class="controls">
                <select id="state-ship"  name="state-ship" placeholder="State/Province" class="span12" onblur="validateStateShip();" <?php form_fill_select('ShipState'); ?>>
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
                <span class="help-block"></span> </div>
            </div>
          </div>
        */ ?>
        <div id="tc-block">
          <!--
            <h4>Terms and Conditions</h4>
            <div id="tc-box" class="span12 well">
              <p><strong>Youngevity International Payment Gateway</strong></p>
              <p><strong>Terms of Use and Return Policy</strong></p>
              <p>Thank you for your patronage and welcome to the Youngevity International Payment Gateway. As such, YGYI has provided this secure payment gateway to facilitate the purchase of YGYI products. YGYI's customer service staff is also available for information specific to your purchase (see customer service contact information below). Purchases made through this gateway are subject to YGYI's standard terms of use and product cancellation/return policy. Please review the following terms carefully as they govern the use of this Payment Gateway, and your use of same constitutes agreement to these terms and conditions. Please be aware that these terms may change from time to time as deemed necessary by YGYI. If at the time of purchase you do not agree to these terms, please do not use this payment gateway.</p>
              <p><strong>Merchandise Pricing and Availability</strong></p>
              <p>YGYI prides itself in providing exclusive, quality merchandise at a reasonable price.  Every attempt is made by YGYI to ensure that the pricing and product information on the referring site is true and correct, however, should there be a discrepancy in pricing, the pricing on the completed order through this gateway shall be considered the current price and shall prevail.</p>
              <p>As YGYI products are from natural sources, and many different ingredients from many different sources go into each product, availability of some items may be limited from time to time. Display of an item on the referring site is not a guarantee that the item is currently in stock or that it will be available at any time in the future. For any items that are unavailable at the time your order is processed (either on backorder or discontinued), you will have the option of substituting that item for another available item, or cancelling a portion of, or your entire order for a full refund. If you have any questions regarding the current or future availability of any item, please use the customer service contact information below. YGYI reserves the right, without prior notice, to limit the order quantity on any product and/or to refuse service to any customer that we believe would violate any of our business practices, applicable laws or policies. We also may require verification of information prior to the acceptance and/or shipment of any order.  No shipment of any order will be unreasonably withheld. Any and all refused orders will be promptly cancelled and refunded.</p>
              <p><strong>Product Information</strong></p>
              <p>The products displayed on the referring site are intended for sale in, and to be shipped to addresses in the United States.  Some products, however, may also be shipped outside of the United States for personal consumption only. Please use the customer service contact information below for more information on products that may or may not be shipped outside of the United States. For any shipments outside the United States, additional shipping and handling charges may be applied upon or subsequent to order processing. All prices are in U.S. Dollars and are valid and effective only in the United States.</p>
              <p><strong>Links to Other Web Sites and Services</strong></p>
              <p>The referring site may contain links to non-YGYI sites. YGYI does not assume any responsibility for, nor does YGYI endorse any content of any non-YGYI sites. Morningside does not participate in the building of a direct sales business, but rather promotes Youngevity Products.</p>
              <p><strong>Refund Policy</strong></p>
              <p>In accordance with the Refund policy, no refunds are given or offered after 30 days from the date product is received by the customer. Any and all returns must include an RMA (Returned Merchandise Authorization).  Please use the customer service information below to initiate a merchandise return, or use the contact information included with the invoice accompanying your shipment.  Upon proper receipt of merchandise by YGYI, a credit will be issued within 7-14 business days.  All sales on food items are final, including chocolates.</p>
              <p><strong>Customer Service Contact Information</strong></p>
              <p>Youngevity<br>
                2400 Boswell Rd<br>
                Chula Vista, CA, 91914<br>
                Office Hours: Mon-Fri 7:00 am to 5:00 pm (Pacific Standard Time)<br>
                (800) 982-3189</p>
            </div>
          -->
          <div class="clearfix"></div>

          <?php /* <input type="hidden" value="<?php echo $sponsorid; ?>" id="sponsor-id" name="sponsor-id" /> */ ?>
          <div class="control-group">
            <label class="checkbox" for="terms-conditions" id="accept-terms">
              <input type="checkbox" id="terms-conditions" name="terms-conditions" value="1" onclick="validateTC();" <?php /*form_fill_checkbox('Terms',$user['Terms']);*/ ?>>
              <strong>I have read and agree to the <a href="<?php echo $this->plugin_url;?>files/terms.pdf" target="_blank">terms and conditions</a>.</strong>

            </label>
          </div>
          <?php 
            /* if(!$enrollment_item): ?>
            <div id="distributor-signup-info">
              <div class="control-group">
                <label class="checkbox" for="distributor-signup">
                <input type="checkbox" id="distributor-signup" name="distributor-signup" value="1">
                <strong>(Optional) I’d also like to sign up to become a distributor. ($25 additional fee applies.)</strong>
                </label>
              </div>
              <div class="control-group" id="tax-id-block" <?php if(!$enrollment_item) echo 'style="display:none;"'; ?>>
                <label class="control-label" for="tax-id" data-toggle="popover" data-placement="top" data-content="Enter your tax ID number." data-original-title="Required">Tax ID<span class="text-error"><strong>*</strong></span></label>
                <div class="controls">
                  <input type="text" id="tax-id"  name="tax-id" placeholder="Tax ID (SSN, EIN, ITIN)" class="span12" onblur="validateTaxID();">
                  <span class="help-block"></span> </div>
              </div>
            </div>
            <?php endif; ?>
           
            <div id="autoship-info">
              <div class="control-group">
                <label class="checkbox" for="autoship-tc">
                <input type="checkbox" id="autoship-tc" name="autoship-tc" value="1">
                <strong>(Optional) Please check to agree to the <a href="autoship-policies.html" target="_blank">Autoship Terms and Conditions</a>.<span class="autoship-required text-error">*</span></strong>
                </label>
              </div>
              <div class="control-group" id="autoship-block" style="display:none;">
                <label class="control-label" for="autoship-date">Next Ship Date<span class="text-error"><strong>*</strong></span></label>
                <div class="controls">
                  <div class="span3 autoship-month"><?php echo date('M', strtotime('+1 month')); ?></div>
                  <select  id="autoship-date"  name="autoship-date" class="span9" onblur="" >
                    <?php
            $blackoutdates = array(1, 4, 5, 7, 15, 17, 20, 26, 28);
            for ($i = 1; $i <= 28; $i++){
              $blackedout = (in_array($i,$blackoutdates,true)) ? true : false;
              if(!$blackedout){
               echo '<option value="'.$i.'">';
                 $lastdigit = substr($i, -1);
               if($lastdigit == 1)  echo $i.'st';
                else if($lastdigit == 2)  echo $i.'nd';
                else if($lastdigit == 3)  echo $i.'rd';
                else echo $i.'th';
              echo "</option>";
              }
            }
          ?>
                  </select>
                  <span class="help-block"></span> </div>
              </div>
            </div>
            */ ?>
            <br>
            <div class="row-fluid">
              <!--<div class="span8"> <img src="<?php echo $this->plugin_url; ?>img/autoship-ad.png" class="img-responsive"> </div>-->
              <div class="span12">
                <button type="button" class="btn btn-primary pull-right" id="user-information-btn" onclick="event.preventDefault();showOverlay();">Join Now <i class="icon-circle-arrow-right"></i></button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
</div>
<?php /*
<div class="modal  fade" id="login">
 <div class="modal-dialog">
 <div class="modal-content">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Login to your Youngevity account</h3>
  </div>
  <div class="modal-body">
    <form class="form-horizontal">
      <div class="control-group">
        <label class="control-label" for="username-id">E-mail / User ID</label>
        <div class="controls">
          <input type="text" id="username-id" name="username-id" placeholder="E-mail / User ID" >
          <span class="help-inline hide" id="invalid-login">Invalid Login Information</span> </div>
      </div>
      <div class="control-group">
        <label class="control-label"  for="password">Password</label>
        <div class="controls">
          <input type="password" id="password" name="password" placeholder="Password" >
        </div>
      </div>
      <div class="control-group">
        <div class="controls">
          <input type="hidden" name="action" value="login">
          <button id="login-button" type="button" class="btn btn-primary" onclick="validateLogin();"><i class="icon-signin"></i> Login</button>
        </div>
      </div>
    </form>
    </div>
    </div>
  </div>
</div>
*/?>
<div id="processing" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="processingLabel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
       We’re processing your Order and appreciate your patience.  Please do not refresh or navigate away from this page.
     </div>
   </div>
 </div>
</div>
<!--<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>-->
<!--<script type="text/javascript" src="source/js/bootstrap.min.js"></script>-->
<!--<script type="text/javascript" src="js/jstorage.js"></script>-->
<?php if(isset($_SESSION['testmode'])) { 
  if( $_SESSION['testmode'] == true) {
    echo '<script type="text/javascript"> var testmode = true; </script>'; 
  }
} 
?>
<script type="text/javascript">
  var user = <?php echo json_encode($_SESSION['user']); ?>;
  var cart = '<?php echo json_encode($_SESSION['cart']); ?>';
  var sorteditems = <?php echo json_encode($_SESSION['sorteditems']); ?>;
  var enrollmentitem = <?php if(isset($_SESSION['enrollment_item'])) { if ($_SESSION['enrollment_item']){ echo 'true';} else{ echo 'false'; } }else{ echo 'false'; }?>;
  var redirect = "<?php echo FreedomCheckout::get_redirect_page(); ?>";
  var subtotal = <?php echo number_format($cart['subtotal'], 2, '.', ','); ?>;
</script>
<!--<script type="text/javascript" src="js/core.js"></script>-->