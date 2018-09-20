var $ = jQuery.noConflict();

window.freedomValidator = {

	isValid : {
		'ygyid' : false,
		//'username' : false,
		'gender' : false,
		'birthday' : false,
		'firstname' : false,
		'lastname' : false,
		'email' : false,
		'street' : false,
		'city' : false,
		'state' : false,
		'country' : false,
		'zip' : false,
		'shipstreet' : false,
		'shipzip' : false,
		'shipcountry' : false,
		'shipsate' : false,
		'shipcity' : false,
		'phone' : false,
		'tc' : false,
		'ccnum' : false,
		'cvv' : false,
		'ccexpiry' : false
	},



	//validate all user inputs
		
	validate : function(defer) {

		this.validateGender();
		this.validateBirthday();
		this.validateFirstName();
		this.validateLastName();

		this.validateStreetAddress();

		
		
		this.validateCity();
		this.validateState();
		this.validateCountry();

		this.validateShipingAddress();

		this.validatePhone();
		this.validateTC() ;		
		

		this.validateCCNumber();
		this.validateCCExp();
		this.validateCVV();
		
		//ajax
		var self = this;
		var isValid = true;

		this.validateYGYID()
			.then(this.validateEmail())
			//.then(this.validateUserName())
			.then(this.validateZip())
			.then(this.validateCCNumber())
			.done(function() {
				validateResult = self.checkValidations();
				if (validateResult == true) {
					defer.resolve(validateResult);
				} else {
					defer.reject(validateResult);
				}
				return defer;
			})
	},

	checkValidations : function() {
		var valid = true;
		var error;
		for (var i in this.isValid) {
			if (!this.isValid[i]) {
				valid = false;
				error = i;
				console.log(error);
				break;
			}
		}
		if (valid)
			return valid;
		else 
			return error;
	},
	//////////////////////////////////////////
	requiredAction : function (input,valid,msg){
		$(input).closest('p').find('.help-block').html(msg);
		if(!valid){
			$(input).closest('p').removeClass('success').addClass('error');
			return false;
		}
		else{
			$(input).closest('p').removeClass('error').addClass('success');
			return true;
		}
	},
	
	/////////////////////////////////////////////////////////////
	//validate gender
	validateGender : function (){
		var input = $('#gender').val();
		var retval = this.validWord(input);

		if( !retval ) { 
			this.isValid.gender = false;
			msg = 'You must select a Gender.'; 
		} else { 
			this.isValid.gender = true;
			msg = ''; 
		}
		this.requiredAction('#gender', retval, msg);
		
	},

	//validate birthday
	validateBirthday : function (){
		var input1 = $('#birth-month').val();
		var input2 = $('#birth-day').val();
		var input3 = $('#birth-year').val();
		var retval = (input1==0 || input2==0 || input3==0) ? false : true;

		if( !retval ) { 
			this.isValid.birthday = false;
			msg = 'You must enter your Birthday.';
		} else { 
			this.isValid.birthday = true;
			msg = ''; 
		}
		this.requiredAction('#birth-month,#birth-day,#birth-year', retval, msg);
	},


	// Validate the First Name
	validateFirstName : function (){
		var input = $('#first-name').val();
		var retval = this.validWord(input);

		if( !retval ) { 
			this.isValid.firstname = false;
			msg = 'You must enter a First Name.';
		} else { 
			this.isValid.firstname = true;
			msg = ''; 
		}

		this.requiredAction('#first-name', retval, msg);
	},

	
	// Validate the Last Name
	validateLastName : function (){
		var input = $('#last-name').val();
		var retval = this.validWord(input);

		if( !retval ) { 
			this.isValid.lastname = false;
			msg = 'You must enter a Last Name.';
		} else { 
			this.isValid.lastname = true;
			msg = ''; 
		}
 
		this.requiredAction('#last-name', retval, msg);
	},

	 

		// Validate country
	validateCountry: function (){
		var input = $('#country').val();
		var retval = this.validWord(input);

		if( !retval ) { 
			this.isValid.country = false;
			msg = 'You must select a valid Country.';
		} else { 
			this.isValid.country = true;
			msg = ''; 
		}
		 
		this.requiredAction('#country', retval, msg);
	},

	validateZip : function() {
		var input = $('#zip').val();
		var country = $('#country').val();
		var retval = this.validZipCode(input, country);
		if(!retval) {
			this.isValid.zip = false;
			msg = 'You must enter a valid Zip/Postal Code.';
		} else { 
			this.isValid.zip = true;
			msg = ''; 
		}
		 
		this.requiredAction('#zip', retval,msg);
	},

	
	// Validate street address
	validateStreetAddress : function (){
		var input = $('#street-1').val();
		var retval = this.validWord(input);

		if( !retval ) { 
			this.isValid.street = false;
			msg = 'You must enter a valid Street Address.';
		} else { 
			this.isValid.street = true;
			msg = ''; 
		}

		 
		this.requiredAction('#street-1', retval, msg);
	},

	
	// Validate city
	validateCity  :function (){
		var input = $('#city').val();
		var retval = this.validWord(input);

		if( !retval ) { 
			this.isValid.city = false;
			msg = "can't be blank.";
		} else { 
			this.isValid.city = true;
			msg = ''; 
		}
 
		this.requiredAction('#city',retval, msg);
	},

	
	// Validate state
	validateState : function (){
		var input = $('#state').val();
		var retval = this.validWord(input);

		if( !retval ) { 
			this.isValid.state = false;
			msg = "can't be blank.";
		} else { 
			this.isValid.state = true;
			msg = ''; 
		}

		 
		this.requiredAction('#state', retval, msg);
	},

	validateShipingAddress : function() {
		if(!$('#same-shipping').is(":checked")){
			this.validateStreetAddressShip() ;
			this.validateCityShip();
			this.validateStateShip();
			this.validateCountryShip();
			this.validateZipShip();
		} else {
			this.isValid.shipstreet = true;
			this.isValid.shipzip = true;
			this.isValid.shipcountry = true;
			this.isValid.shipsate = true;
			this.isValid.shipcity = true;
		}
	},
		// Validate country
	validateCountryShip : function (){
		var input = $('#country-ship').val();
		var retval = this.validWord(input);

		if( !retval ) { 
			this.isValid.shipcountry = false;
			msg = 'You must select a valid Country.';
		} else { 
			this.isValid.shipcountry = true;
			msg = ''; 
		}

		this.requiredAction('#country-ship', retval, msg);
	},

	// Validate street address
	validateStreetAddressShip : function (){

		var input = $('#street-1-ship').val();
		var retval = this.validWord(input);

		if( !retval ) { 
			this.isValid.shipstreet = false;
			msg = 'You must enter a valid Street Address.';
		} else { 
			this.isValid.shipstreet = true;
			msg = ''; 
		}
		this.requiredAction('#street-1-ship', retval, msg);
		 
	},

	// Validate city
	validateCityShip: function (){
		var input = $('#city-ship').val();
		var retval = this.validWord(input);

		if( !retval ) { 
			this.isValid.shipcity = false;
			 msg = "can't be blank.";
		} else { 
			this.isValid.shipcity = true;
			msg = ''; 
		}

		this.requiredAction('#city-ship', retval, msg);

	},

	// Validate state
	validateStateShip: function (){
		var input = $('#state-ship').val();
		var retval = this.validWord(input);

		if( !retval ) { 
			this.isValid.shipsate = false;
			msg = "can't be blank.";
		} else { 
			this.isValid.shipsate = true;
			msg = ''; 
		}
	
		this.requiredAction('#state-ship', retval, msg);
	},

	// Validate credit card month
	validateCCMonth: function (){
		var input = $('#exp-month').val();
		var retval = this.validMonth(input);
		if( !retval ) { 
			this.isValid.ccexpirymonth = false;
		} else {
			this.isValid.ccexpirymonth = true;
		}
		this.requiredAction('#exp-month',retval);
		return retval;
	},

	// Validate password
	validatePassword : function (){
		var retval = this.validPassword($('#password-1').val());

		if( !retval ) { 
			this.isValid.password = false;
			msg = 'You must enter a valid Password.';
		} else { 
			this.isValid.password = true;
			msg = ''; 
		}
		 
		this.requiredAction('#password-1', retval, msg);
	},

	
	// Validate  phone number
	validatePhone : function (){
		var input = $('#phone').val();
		var retval = this.validWord(input);

		if( !retval ) { 
			this.isValid.phone = false;
			msg = 'You must enter a valid Phone Number.';
		} else { 
			this.isValid.phone = true;
			msg = ''; 
		}
 
		this.requiredAction('#phone', retval, msg);
	},

	// Validate terms and conditions is checked
	validateTC : function () {
		if (jQuery('#terms-conditions').length) {
			var retval = jQuery('#terms-conditions').is(':checked');
			if (retval) {
				this.isValid.tc = true;this.isValid.tc = true;
			} else {
				this.isValid.tc = false;
			}
		} else {
			this.isValid.tc = true;
		}
		this.requiredAction('#terms-conditions',retval);
	},

	validateCCExp : function (){
		var d = new Date();
		var month = d.getMonth()+1;
		var fullyear = d.getFullYear();
		var year = fullyear.toString().substr(2, 2);

		if ($('#exp-month').length && $('#exp-year').length) {
			var inputMonth = parseInt($('#exp-month').val(), 10);
			var inputYear = $('#exp-year').val();
		} else {
			var inputdates = $("#cardexpiry").val().split("/");
			var inputMonth = inputdates[0];
			var inputYear = inputdates[1];
		}

		
		if(inputYear >= year) {
			if(inputYear == year && inputMonth < month) {
				this.requiredAction('#exp-month', false, 'Credit Card Month invalid, card has expired.');
				this.requiredAction('#exp-year', true, '');
				this.isValid.ccexpiry = false;	
			} else {
				this.requiredAction('#exp-month', true, '');
				this.requiredAction('#exp-year', true, '');
				this.isValid.ccexpiry = true;	
			}
		} else {
			this.requiredAction('#exp-month', false, '');
			this.requiredAction('#exp-year', false, 'Credit Card Year invalid, card has expired.');
			this.isValid.ccexpiry = false;	
		}
	},

	// Validate credit card year
	validateCCYear : function (){
		var input = $('#exp-year').val();
		var retval = this.validYear(input);
		if( !retval ) { 
			this.isValid.ccexpiry = false;
		} else {
			this.isValid.ccexpiry = true;
		}
		this.requiredAction('#exp-year',retval);
		return retval;
	},
	

	// Validate CVV for credit card
	validateCVV : function (){
		var input = $('#cardverify').val();
		var retval = this.validCVV(input);

		if( !retval ) { 
			this.isValid.cvv = false;
			 msg = 'Card Verification must be 3 or 4 digits.';
		} else { 
			this.isValid.cvv = true;
			msg = ''; 
		}
		this.requiredAction('#cardverify',retval,msg);
	},

	// Verify password
	verifyPassword : function (){
		var retval = ($('#password-1').val() == $('#password-2').val()) ? true : false;
		
		if( !retval ) { 
			this.isValid.password = false;
			msg = 'The 2 Password fields must match.';
		} else { 
			this.isValid.password = true;
			msg = ''; 
		}

		this.requiredAction('#password-2', retval, msg);
	},

	///////////////////////////////////////////////////////////////////
	 /**
	 * this.requiredAction(id,valid)
	 * Description: Takes the id and changes the object given its validity.
	 *		Adds the check or x mark and turns text red on false, otherwise show some green
	 * id - The id of the inputs error box  ie. city-r
	 * valid - Which validy to change it to
	 */

	


	/////////// UTILITY FUNCTIONS ////////////////
	//these functions return true or false, they are not directly used
	// so can't change the isValid value of class

	// Validates a string value
	validWord : function (str){
		if (!str) {
			str = "";
		}
	    str = str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
		if(!str || str == '' || str == ' '){
			return false;
		}
		return true;
	},

	// Validates an email address
	validEmail : function (str){ 
		var filter=  new RegExp(/^.+@.+\..{2,3}$/);
		if (!filter.test(str)){
			return false;
		}
		return true;
	},


	// Validates a username that has letters and numbers
	validUserName :  function(str){
		user = new RegExp(/^[A-Za-z0-9_]*$/);
		if(!str || str == '' || str == ' '){
			return false;
		}
	    if(!user.test(str)) {
			return false;
	    }
		if(str.length < 8){
			return false;
		}
		return true;
	},

	// Validate zip code
	validZipCode : function (str, country){
		 // Check for correct zip code
		 var reZip = new RegExp(/^(?:[A-Z0-9]+([- ]?[A-Z0-9]+)*)?$/);

		 if(country == '') reZip = new RegExp(/_/);
	     if(country == 'USA') reZip = new RegExp(/(^\d{5}$)|(^\d{5}-\d{4}$)/);
		 if(country == 'CANADA') reZip = new RegExp(/^[ABCEGHJKLMNPRSTVXY]{1}\d{1}[A-Z]{1} *\d{1}[A-Z]{1}\d{1}$/);
	     if (!reZip.test(str)) {
	          return false;
	     }
	     return true;
	},

	// Validates a password to specific regex
	validPassword : function (str){
		pass = new RegExp(/^[A-Za-z0-9@\$=!:.#%]{8,}.*$/);
	     if (!pass.test(str)) {
	        return false;
	     } 
	     return true;
	},

	

	// Validates a month
	validCCNum : function (str) {
		ccnum = new RegExp(/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$/);
	    if (!ccnum.test(str)) {
	         return false;
	    }
	    return true;
	},


	// Validates a month
	validMonth : function (str){
		month = new RegExp(/^\d{1,2}$/);
	     if (!month.test(str)) {
	          return false;
	     }
		 return true;
	},

	
	

	// Validates a year
	validYear : function (str){
		year = new RegExp(/^\d{2}$/);
	     if (!year.test(str)) {
	          return false;
	     } 
		 return true;
	},
	

	

	// Validates a number
	validCVV : function (str){
		num = new RegExp(/^[0-9]{3,4}$/);
	     if (!num.test(str)) {
	          return false;
	     } 
		 return true;
	},
	/////////////// ajax functions //////////////////

	// Validate the Sponsor YGY ID
	validateYGYID : function () {
		var defer = new $.Deferred();
		var self = this;

		var msg = "";
		var input = $('#sponsor-id').val();
		var retval = this.validWord(input);
		if(!retval) {
			console.log('no id number found');
			msg = 'You must enter an ID number.';
			self.isValid.ygyid = false;
			defer.resolve();
		} else {
			 //self.isValid.ygyid = true;
			 $("#sponsor-name").val('Searching...').attr('readonly', 'readonly');
			 $.ajax({
				async : true,
				type: "GET",
				url: frontend_ajax_object.ajaxurl,
				dataType : 'json',
				data: { "action" : 'repinfo', "repnumber" : input},
				success: function(data) {
					console.log('found by ajax');
					self.isValid.ygyid = true;
					defer.resolve();
					if(data.success){
						$("#sponsor-name").val(data.repName)
						.attr('readonly', 'readonly');
					} else {
						$("#sponsor-name").removeAttr('readonly');
					}
				},
				error: function(data){
					console.log('NOT found by ajax');
					$("#sponsor-name").removeAttr('readonly');
					defer.reject();
					alert("System Error. Please try again.");
					return false;
				}
			});
		}

		this.requiredAction('#sponsor-id', retval, msg);
		
		return defer;
	},

	// Validate the User Name
	validateUserName : function (){
		var self = this;
		var defer = new $.Deferred();
		var input = $('#username').val();
		var retval = this.validUserName(input);

		if(!retval) {
			msg = 'You must enter a Username at least 8 characters long.';
			this.isValid.username = false;
		} else  {
			this.isValid.username = true;
			msg = '';
		}
		defer.resolve();
		this.requiredAction('#username', retval, msg);
		return defer;
	},

	// Validate the email
	validateEmail : function () {
		var self = this;
		var defer = new $.Deferred();
		var input = $('#email').val();
		var retval = this.validEmail(input);	
		if(!retval) {
			this.isValid.email = false;
			msg = 'You must enter a valid E-mail Address.';
		} else  {
			this.isValid.email = true;
			msg = '';
		}
	 	defer.resolve();
		this.requiredAction('#email', retval, msg);
		return defer;
	}, 

	// Validate zip
	validateZip : function (){
		var defer = new $.Deferred();
		var self = this;
		var input = jQuery('#zip').val();
		var country = jQuery('#country').val();
		var retval = this.validZipCode(input, country);
		
		if(retval == true){
			// && country == 'USA'
			this.isValid.zip = true;	
			msg = ""; 
		} else {
			msg = 'You must enter a valid Zip/Postal Code.';
			this.isValid.zip = false;
		}

		this.requiredAction('#zip', retval,msg);
		defer.resolve();
		return defer;
	},

	// Validate zip
	validateZipShip : function (){
		var self = this;
		var defer = new $.Deferred();

		if(!$('#same-shipping').is(":checked")) {
			var input = $('#zip-ship').val();
			var country = $('#country-ship').val();
			var retval = this.validZipCode(input, country);
			 
			if(retval == true ){
				// /&& country == 'USA'
				 msg = "";
				 self.isValid.shipzip = true;
			} else {
				self.isValid.shipzip = false;
				msg = 'You must enter a valid Zip/Postal Code.';
			}
		} else {
			self.isValid.shipzip = true;
			msg = "";
			retval = true;
		}

		defer.resolve();
        this.requiredAction('#zip-ship', retval, msg);
		return defer;
	},

	// Validate credit card number
	validateCCNumber : function (){
		var self = this;
		var defer = new $.Deferred();
		var input = $('#creditcard').val();
		var retval = this.validCCNum(input);
		 
		if(!retval) {
			msg = 'Credit Card must be 15 or 16 digits.';
			this.isValid.ccnum = false;
		} else  {
			this.isValid.ccnum = true;
			msg = '';
		}
		defer.resolve();
		this.requiredAction('#creditcard', retval, msg);
		return defer;
	},
	/////////////////////////////////////////////////
	validateLogin :  function (){
		var self = this;
		var defer = new $.Deferred();
		var username = $('#username-id').val();
		var password = $('#password').val();
		var uservalid = this.validWord(username);
		var passvalid = this.validWord(password);
		var retval = (uservalid && passvalid) ? true : false;
		if(retval == true){
			$.ajax({
				async : true,
				type: "POST",
				url: "data-check.php",
				data: { "action" : 'login', "username" : this.encodeVal(username), "password" : this.encodeVal(password) },
				success: function(data) {
					defer.resolve();
					if(!data.success){
						self.requiredAction('#username-id', false);
						self.requiredAction('#password', false);
						alert("Invalid Login. Username and Password do not match.");
						return false;
					}
					if(data.success){
						fillLoginData(data.user);
						$('#signup-account-info, #sponsor-form').hide();
						$('#login').modal('hide');
						return true;
					}
				},
				error: function(data){
					defer.reject();
					alert("System Error. Please try again.");
					return false;
				}
			});
		}else{
			this.requiredAction('#username-id',uservalid);
			this.requiredAction('#password',passvalid);
			return retval;
		}
	},
	encodeVal : function (str){
			encodedstr = encodeURIComponent(str);
			return encodedstr;
		},
} 
/////////////.////////////////////////////////////////////////////////