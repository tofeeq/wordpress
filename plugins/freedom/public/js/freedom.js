(function ($) {
	$.freedom = function($form) {
		this.form = ($form instanceof $) ? $form : $($form);
	};

	$.freedom.prototype = {

		userdata : '',
		repid : '',
		tempordernumber : '',
		shipmethod : '',
		shipmethodid : '',

		valid : false,
		/** 
		 * GLOBALS
		 * shoppingCart - Must set each item(productID) set to the default 0 items in cart
		 * priceList - For every item(productID) give it the appropriate dollar amount
		 * skipVerify - if enabled, will skip the verfication of credentials (used only for testing)
		 * skipSignup - if enabled, skips the rep signup by database. if skipped, the rest will fail since there will be no default new rep information
		 */
		skipVerify : false,
		skipSignup : false,

		init : function() {
			//this.setIframe();
			this.bindValidation();
			this.setupSameShipping();
			//this.setTooltip();
			this.setCountry();
			this.setSubscription();
			this.setValidation();
		},

		setIframe() {
			/*
			parent.document.getElementById('ygy-iframe')
				.style.height = document.body.clientHeight+50+'px';

			$(window).bind("resize click keypress", function() {
				parent.document.getElementById('ygy-iframe')
					.style.height = document.body.clientHeight+50+'px';
			});
			*/
		},

		bindValidation : function( ) {
			$('#username-id, #password').keypress(function(event) {
		        if(event.keyCode == 13) validateLogin();
		    });
		},

		setupSameShipping : function() {
			$('#same-shipping').click(function() {
				$("#shipping-info").toggle();
				if($(this).is(':checked')) {
					$('#country-ship,#zip-ship,#street-1-ship,#street-2-ship,#city-ship,#state-ship').addClass('uneditable-input');
					$('#ship-box').css('opacity','.5');
				}else{
					$('#country-ship,#zip-ship,#street-1-ship,#street-2-ship,#city-ship,#state-ship').removeClass('uneditable-input');
					$('#ship-box').css('opacity','1');
				}
			});
		},

		setTooltip : function() {
			$('.tool-tip').bind("mouseover mousedown", function() {
			  $(this).popover('show');
			}).bind("mouseout mouseup", function() {
			  $(this).popover('hide');
			});	
		},

		setCountry : function () {
			$('#country, #country-ship').change(function(){
				countrySelect = $(this);
				if(countrySelect.val() == 'USA'){
					selectOptions = '<option value="AL">Alabama</option><option value="AK">Alaska</option><option value="AZ">Arizona</option><option value="AR">Arkansas</option><option value="CA">California</option><option value="CO">Colorado</option><option value="CT">Connecticut</option><option value="DE">Delaware</option><option value="DC">District Of Columbia</option><option value="FL">Florida</option><option value="GA">Georgia</option><option value="HI">Hawaii</option><option value="ID">Idaho</option><option value="IL">Illinois</option><option value="IN">Indiana</option><option value="IA">Iowa</option><option value="KS">Kansas</option><option value="KY">Kentucky</option><option value="LA">Louisiana</option><option value="ME">Maine</option><option value="MD">Maryland</option><option value="MA">Massachusetts</option><option value="MI">Michigan</option><option value="MN">Minnesota</option><option value="MS">Mississippi</option><option value="MO">Missouri</option><option value="MT">Montana</option><option value="NE">Nebraska</option><option value="NV">Nevada</option><option value="NH">New Hampshire</option><option value="NJ">New Jersey</option><option value="NM">New Mexico</option><option value="NY">New York</option><option value="NC">North Carolina</option><option value="ND">North Dakota</option><option value="OH">Ohio</option><option value="OK">Oklahoma</option><option value="OR">Oregon</option><option value="PA">Pennsylvania</option><option value="RI">Rhode Island</option><option value="SC">South Carolina</option><option value="SD">South Dakota</option><option value="TN">Tennessee</option><option value="TX">Texas</option><option value="UT">Utah</option><option value="VT">Vermont</option><option value="VA">Virginia</option><option value="WA">Washington</option><option value="WV">West Virginia</option><option value="WI">Wisconsin</option><option value="WY">Wyoming</option><optgroup label="US Territories/Armed Forces"><option value="AS">American Samoa</option><option value="GU">Guam</option><option value="MP">Northern Mariana Islands</option><option value="PR">Puerto Rico</option><option value="UM">United States Minor Outlying Islands</option><option value="VI">Virgin Islands</option><option value="AA">Armed Forces Americas</option><option value="AP">Armed Forces Pacific</option><option value="AE">Armed Forces Others</option></optgroup>';
				}else if(countrySelect.val() == 'CANADA'){
					selectOptions = '<option value="AB">Alberta</option><option value="BC">British Columbia</option><option value="MB">Manitoba</option><option value="NB">New Brunswick</option><option value="NL">Newfoundland and Labrador</option><option value="NS">Nova Scotia</option><option value="ON">Ontario</option><option value="PE">Prince Edward Island</option><option value="QC">Quebec</option><option value="SK">Saskatchewan</option><option value="NT">Northwest Territories</option><option value="NU">Nunavut</option><option value="YT">Yukon</option>';
				}
				
				if(countrySelect.attr('id')=='country'){
					stateSelect = 'state';
				}else if(countrySelect.attr('id')=='country-ship'){
					stateSelect = 'state-ship';
				}
				
				$('#'+stateSelect).children().remove();
				$('#'+stateSelect).html( selectOptions );
			});
		},

		setSubscription : function() {
			$('#sub-options input[name="subscription"]').change(function(){
				window.location.href = "/iframe.php?subscriptionid="+this.value;
			});
		},

		setValidation  : function() {
			var self = this;
			$("button#freedom-checkout-btn", this.form).click( function(e) {
				e.preventDefault();
				$('#checkout-msg').empty().removeClass('error'); 
				
				self.proceedOrder();
				
			})
		},

		proceedOrder  : function(pdefer) {
			var self = this;
			var res = true;

			var defer = self.validateUserInput();

			defer.done(
				function( isValid ) {
					$("button#freedom-checkout-btn", this.form).html('Submit Order <i class="icon-circle-arrow-right"></i>').removeClass('disabled').removeAttr('disabled');
					res = true;
		  			self.createOrder(pdefer)
		  			
				}
			);

			defer.fail(function(error) {
				console.log(error);
				console.log("returing false in done");
	  			res = false;
	  			if (pdefer) {
	  				pdefer.reject(res);
	  			}
	  			$('#checkout-msg').addClass('error').html("Please provide correct billing and shippnig information.<br> Hints : " + error);
			}); 

			return res;
		},

		validateUserInput : function() {
			$("button#freedom-checkout-btn", this.form).html('Validating...')
					.addClass('disabled').attr('disabled');
			var defer = new $.Deferred();
			freedomValidator.validate(defer);
			return defer;
		},

		
		fillLoginData :function (user){
			userdata = user;
			$("#rep-number").val(user.RepNumber);
			$("#first-name").val(user.Firstname);
			$("#last-name").val(user.Lastname);
			$("#email").val(user.Email);
			$("#phone").val(user.Phone1);
			$("#country").val(user.BillCountry);
			$("#zip").val(user.BillPostalCode);
			$("#street-1").val(user.BillStreet1);
			$("#street-2").val(user.BillStreet2);
			$("#city").val(user.BillCity);
			$("#state").val(user.BillState);
			$("#country-ship").val(user.ShipCountry);
			$("#zip-ship").val(user.ShipPostalCode);
			$("#street-1-ship").val(user.ShipStreet1);
			$("#street-2-ship").val(user.ShipStreet2);
			$("#city-ship").val(user.ShipCity);
			$("#state-ship").val(user.ShipState);
		},
		//////////////////////////////////////////////////////////////////////

		createOrder :function (pdefer) {
			var self = this;

			$('#checkout-msg').empty().removeClass('error');

			$("button#freedom-checkout-btn", this.form).html('Processing...');
			if($("#rep-number").val()){
				var action = 'neworder';
			} else {
				var action = 'newsignuporder';
			}
			var RepNumber = $("#rep-number", this.form).val();
			var TaxID = $('#tax-id', this.form).val();
			var SponsorRepNumber = $("#sponsor-id", this.form).val();
			var ReplicatedURL = $("#username", this.form).val();
			var Email = $("#email", this.form).val();
			var Password = $("#zip", this.form).val();
			var Gender = $("#gender", this.form).val();

			if ($("#birth-year", this.form).length && $("#birth-month").length) {
				var Birthday = $("#birth-year", this.form).val()+'-'+$("#birth-month").val()+'-'+$("#birth-day").val();
			} else {
				var Birthday = $("#birthday", this.form).val().replace("/", "-");
			}
			var Firstname = $("#first-name", this.form).val();
			var Lastname = $("#last-name", this.form).val();
			var Phone1 = $("#phone", this.form).val();
			var BillCountry = $("#country", this.form).val();
			var BillPostalCode = $("#zip", this.form).val();
			var BillStreet1 = $("#street-1", this.form).val();
			var BillStreet2 = $("#street-2", this.form).val();
			var BillCity = $("#city", this.form).val();
			var BillState = $("#state", this.form).val();
			var Cart = $("#cart", this.form).val();

			if($('#same-shipping', this.form).is(':checked')) {
				var ShipCountry = BillCountry;
				var ShipPostalCode = BillPostalCode;
				var ShipStreet1 = BillStreet1;
				var ShipStreet2 = BillStreet2;
				var ShipCity = BillCity;
				var ShipState = BillState;
			}else {
				var ShipCountry = $("#country-ship", this.form).val();
				var ShipPostalCode = $("#zip-ship", this.form).val();
				var ShipStreet1 = $("#street-1-ship", this.form).val();
				var ShipStreet2 = $("#street-2-ship", this.form).val();
				var ShipCity = $("#city-ship", this.form).val();
				var ShipState = $("#state-ship", this.form).val();
			}
			var CreditCardNumber = $("#creditcard", this.form).val();
			if ($("#exp-month", this.form).length && $("#exp-year", this.form).length) {
				var ExpDateMonth = $("#exp-month", this.form).val();
		    	var ExpDateYear = $("#exp-year", this.form).val();
			} else {
				var inputdates = $("#cardexpiry").val().split("/");
				var ExpDateMonth = inputdates[0];
		    	var ExpDateYear = inputdates[1];
			}

			var CVV = $("#cardverify", this.form).val();
			$.ajax({
				type: "POST",
				url: frontend_ajax_object.ajaxurl,
				dataType : 'json',
				data: { 
					"action" : action, 
					"RepNumber" : RepNumber,
					"TaxID" : TaxID,
					"SponsorRepNumber" : SponsorRepNumber,
					"ReplicatedURL" : ReplicatedURL,
					"Email" : Email,
					"Password" : Password,
					"Gender" : Gender,
					"Birthday" : Birthday,
					"Firstname" : Firstname,
					"Lastname" : Lastname,
					"Phone1" : Phone1,
					"BillCountry" : BillCountry,
					"BillPostalCode" : BillPostalCode,
					"BillStreet1" : BillStreet1,
					"BillStreet2" : BillStreet2,
					"BillCity" : BillCity,
					"BillState" : BillState,
					"ShipStreet1" : ShipStreet1, 
					"ShipStreet2" : ShipStreet2,
					"ShipCity" : ShipCity,
					"ShipState" : ShipState,
					"ShipPostalCode" : ShipPostalCode,
					"ShipCountry" : ShipCountry,
					"CreditCardNumber" : CreditCardNumber,
					"ExpDateMonth" : ExpDateMonth,
					"ExpDateYear" : ExpDateYear,
					"CVV" : CVV,
					//"Cart" : this.encodeVal(JSON.stringify(sorteditems)),
					"Cart" : Cart ,
				},
				success: function(data) {
					if (pdefer) {
		  				pdefer.resolve(data);
		  			}

					if (data.success) {
						//alert('Order #'+data.orderid+' Successful.');
						$('#ship-total').html(data.info.ShippingTotal);
						//$('#order-tax').html(data.info.TaxTotal);
						$('#order-total').html(data.info.OrderTotal);
						//$('#order-shipping').html('['+data.info.Description+']');

						var url = window.location.href.replace(new RegExp('checkout.*'), 
							'sign-up-success/?OrderID=' + data.info.OrderID 
							+ '&RepID=' + data.info.CustomerID );

						//for plugin
						try {
							if (data.info.plugin.ocenture) {
								var ClientMemberID = [];
								var MembershipID = [];
								var ProductCode = [];
								var res;

								for (var i in data.info.plugin.ocenture) {
									res = data.info.plugin.ocenture[i];
									ClientMemberID.push(res.ClientMemberID);
									MembershipID.push(res.MembershipID);
									ProductCode.push(res.ProductCode);
								}

								url += "&ClientMemberID=" + ClientMemberID.join(",")
									+ "&MembershipID=" + MembershipID.join(",")
									+ "&ProductCode=" + ProductCode.join(",");

							}
						} catch (e) {

						}
						window.location = url;
						return true;

						if(action=='neworder') {

							$(".order-form").empty().html(
							'<div class="container"> \
								<div class="section"> \
									<div class="row"> \
										<div class="col-xs-12" style="height:260px !important;"> \
											<h2>Order Successful</h2> \
											<p>You will be receiving a welcome email tomorrow morning with your registration and utilization procedures.</p> \
											<p>You may also complete your registration today by calling Teladoc&reg; at (800) 835-2362 (800-Teladoc).  If you do call Teladoc&reg;, reference <strong>Youngevity Telecare</strong> as the company providing the Teladoc&reg; benefit or use the Company Code <strong>YT0414</strong>.</p> \
											<p>Order Information:</p> \
											<p>Customer ID: <strong>' + data.info.CustomerID + '</strong> </p> \
											<p>Order ID: <strong> ' + data.info.OrderID + ' </strong> </p> \
										</div>\
									</div>\
								</div>\
							</div>');


								 
						} else {
							$(".order-form").empty().html(
							'<div class="container"> \
								<div class="section"> \
									<div class="row"> \
										<div class="col-xs-12" style="height:280px !important;"> \
											<h2>Order Successful</h2> \
											<p>You will be receiving a welcome email tomorrow morning with your registration and utilization procedures.</p> \
											<p>You may also complete your registration today by calling Teladoc&reg; at (800) 835-2362 (800-Teladoc).  If you do call Teladoc&reg;, reference <strong>Youngevity Telecare</strong> as the company providing the Teladoc&reg; benefit or use the Company Code <strong>YT0414</strong>.</p> \
											<p>Order Information:</p> \
											<p>Customer ID: <strong>' + data.info.CustomerID + '</strong> </p> \
											<p>Order ID: <strong> ' + data.info.OrderID + ' </strong> </p> \
										</div>\
									</div>\
								</div>\
							</div>');	
						}
						
						$("button#freedom-checkout-btn", this.form).hide();
							
						if(typeof(redirect) != 'undefined' && redirect != ''){
							joiner = (redirect.match(/\?/)) ? '&' : '?';
							redirect_location = redirect+joiner+"OrderID="+data.info.OrderID+"&RepID="+data.info.RepNumber;	
							window.location.href = redirect_location;
						}
					} else {
					 	//alert(data.message);
					 	$('#checkout-msg').addClass('error').html("Error:  " + data.error);
						$("button#freedom-checkout-btn", this.form).html('Submit Order <i class="icon-circle-arrow-right"></i>').removeClass('disabled').removeAttr('disabled');
					}
				},
				error: function(data){
					if (pdefer) {
		  				pdefer.reject(data);
		  			}

					//alert("System Error. Could not finalize order. Please try again.");
					$("button#freedom-checkout-btn", this.form).html('Submit Order <i class="icon-circle-arrow-right"></i>').removeClass('disabled').removeAttr('disabled');
					return false;
				}
			});
		},

		encodeVal : function (str){
			encodedstr = encodeURIComponent(str);
			return encodedstr;
		},

		encodeElem : function (str){
			encodedstr = encodeURIComponent($(str).val());
			return encodedstr;
		},

		isInIframe : function () {
			var isInIframe = (window.location != window.parent.location) ? true : false;
			return isInIframe;
		}


	}

}(jQuery));
	 








	 /**
	 * validateInfo()
	 * The submission of the information to validate all the billing and shipping information
	 * It will run all the tests for each specific input, if they all pass it will continue to the next step
	 */
	 /*
	function validateInfo(){
		$('#user-information-btn').html('Validating...').addClass('disabled').attr('disabled');
		var err = true;
		err = (validateGender() && err) ? true : false;
		err = (validateBirthday() && err) ? true : false;
		err = (validateFirstName() && err) ? true : false;
		err = (validateLastName() && err) ? true : false;
		err = (validateEmail() && err) ? true : false;
		if($('#rep-number').val() == '') {
			err = (validateYGYID() && err) ? true : false;
			//err = (validatePassword() && err) ? true : false;
		}
		err = (validateStreetAddress() && err) ? true : false;
		err = (validateCity() && err) ? true : false;
		err = (validateState() && err) ? true : false;
		err = (validateCountry() && err) ? true : false;
		err = (validatePhone() && err) ? true : false;
		err = (validateTC() && err) ? true : false;		
		if(!$('#same-shipping').is(":checked")){
			err = (validateStreetAddressShip() && err) ? true : false;
			err = (validateCityShip() && err) ? true : false;
			err = (validateStateShip() && err) ? true : false;
			err = (validateCountryShip() && err) ? true : false;
		}
		err = (validateCCNumber() && err) ? true : false;
		err = (validateCCExp() && err) ? true : false;
		err = (validateCVV() && err) ? true : false;
		// If there are errors and the test parameters are not set, do not complete the first step
		if(!err && !skipVerify && !skipSignup){
			alert('Billing and/or Shipping Information is missing or incorrect. Please correct errors before continuing.');
			$('#user-information-btn').html('Submit Order <i class="icon-circle-arrow-right"></i>').removeClass('disabled').removeAttr('disabled');
			return false;
		}
		else{
			// On successful validation, submit the customer information to be processed into the system (unless in test mode)
			if(!skipVerify && !skipSignup){
				createOrder();
			}
			return true;
		}
	}


	function testRedirect(){
		var info = 'action=redirecttest';
		$.ajax({
			type: "GET",
			url: "data-check.php?"+info,
			success: function(data) {
				if(data.success){
					alert('Order #'+data.orderid+' Successful.');
					if(typeof(redirect) != 'undefined' && redirect != ''){
						window.location.href = redirect;
						joiner = (redirect.match(/\?/)) ? '&' : '?';
						message = redirect+joiner+"OrderID="+"13579"+"&RepID="+"86420";	
						if(this.isInIframe()==true){
							window.parent.postMessage(message, "*");
						}else{
							window.location.href = message;
						}
					}else{
						//window.location.href = '//telecare.youngevity.com/invoice.php';
					}
				}else{
				 	alert(data.message);	
				}
			},
			error: function(data){
				$(".accordion-inner:visible").unmask();
				alert("System Error. Could not redirect.");
				return false;
			}
		});
	}*/