/* core.js - Handles all the AJAX , Error Checking, and general javascript functions */

var cart = 0;
var userdata;
var repid;
var tempordernumber;
var shipmethod;
var shipmethodid;
var screenHeight = 0;

/** 
 * GLOBALS
 * shoppingCart - Must set each item(productID) set to the default 0 items in cart
 * priceList - For every item(productID) give it the appropriate dollar amount
 * skipVerify - if enabled, will skip the verfication of credentials (used only for testing)
 * skipSignup - if enabled, skips the rep signup by database. if skipped, the rest will fail since there will be no default new rep information
 */
 var skipVerify = false;
 var skipSignup = false;

 var prop65items = ['10243','10245','10246','10247','10248','10249','10250','10251','10252','10253','10254','10256','10257','10258','10259','10260','10261','10262','10263','10264','10275','10280','10282','1050','1055','1062','1068','1069','1070','1071','1072','1082','1083','1084','1085','1086','1088','1091','1092','1093','13203','13203C','13204','13204C','13209','13209c','13217','1511','20030','21010C','21010V','21023C','21023CC','21023V','21023VC','23221','23230','50197','50223','50241','67507','81110','HTG-109','HTG-109P','HTG-109S','HTG-130','HTG-130P','HTG-130S','HTG-171','HTG-171P','HTG-171S','LV105','LV114','LV117','LV201A','TL004SYS','TL005SYS','TL006PROD','TL006PRODfs','TL007PROD','TL008PROD','TL010PROD','TL011PROD','TL014SYS','TL015SYS','TL018SYS','TL030SYS','TL031SYS','TL034SYS','TL040SYS','TL041SYS','TL042SYS','TL043SYS','TL044SYS','TL045SYS','TL046SYS','TL047SYS','TL048SYS','TL049SYS','USBI000008','USBI0002','USBI0003','USBI0008','USBI0009','USBI0011','USBI0013','USBI0014','USEW0002','USGF0006','USKC000001','USKC000002','USKC000003','USKC000004','USKC000005','USKC000006','USKC000007','USKC000008','USKC000009','USKC200001','USKC200002','USSN000001','USSN100000','USSN100001','USYC100120','USYC100130','USYC200110','USYC200120','USYC200130','USYC200140','USYC200405','USYC200410','USYC200415','USYC200904','USYC200905','USYC200906','USYC200908','USYC300100','USYC300110','USYC300120','USYC300130','USYC300140','USYC300150','USYC300904','USYC300906','USYC400001','USYG000009','USYG0008','USYG0010','USYG0011','USYG0012','USYG0013','USYG0023','USYG0061','USYG0062','USYG100061','USYG100062','USYG100075','USYG100076','USYG100077','USYG100078','USYG100095','USYG103210','USYG103211','USYG103212','USYG103230','x1070SP','x1093',];

 var prop65canceritems = ['20975','20975C','JF8005S','JF8010S','JF8015S','JF8020S','JF8025S','JF8035S','JF8040S','JF9002S','JF9003S','JF9004S','JF9005S','SP400','SP401','SP402','SP403','SP404','SP405','SP406','SP407','SP408','SP409','SP600','SP601','SP602','SP603','SP604','SP605','SP606','SP607','USAD500004','USAD500004FS','USAD500005','USAD500005FS','USAN574911','USYC100120','USYC100130','USYC200110','USYC200120','USYC200130','USYC200140','USYC200405','USYC200410','USYC200415','USYC200904','USYC200905','USYC200906','USYC200908','USYC300100','USYC300110','USYC300120','USYC300130','USYC300140','USYC300150','USYC300904','USYC300906','USYC400001','YBTC000001','YBTC000012'];

 var enrollment_items = ['1072','1070','1086','1097','1075','1074','1092','1091','1094','USMK0001','1063','1064','SP1000','10280','USFD400910','90101','1076','1077','1078','1079','1084','1089','USMK77777','USAN5000','USFT5000','USHM5000','USML5000','USYG5000','USYG5001','USYG5002','USYG5010'];


/**
 * jQuery page actions
 * handles basic features such as mousehovers, clicks, and cart scrolling
 */
 jQuery(document).ready(function(){
/*
 	jQuery(window).bind("resize click keypress", function() {
 		screenHeight = resizeIframe(screenHeight);
 	});
 	screenHeight = resizeIframe(screenHeight);
 	*/
/* 	if (fc_referrer_name != ''){
 		jQuery('.dac_referrer_name').val(fc_referrer_name).prop( "readonly", true );
 	}
 	if (fc_referrer_id != ''){
 		jQuery('.dac_referrer_id').val(fc_referrer_id).prop( "readonly", true );
 		jQuery('button.validate-sponsor-id').hide();
 		
 	}
 	if (fc_referrer_email != ''){
 		jQuery('.dac_referrer_email').val(fc_referrer_email);
 	}*/
 	jQuery('#username-id, #password').keypress(function(event) {
 		if(event.keyCode == 13) validateLogin();
 	});
 	jQuery('#same-shipping').click(function(){
 		jQuery("#shipping-info").toggle();
 		if(jQuery(this).is(':checked')) {
 			jQuery('#country-ship,#zip-ship,#street-1-ship,#street-2-ship,#city-ship,#state-ship').addClass('uneditable-input');
 			jQuery('#ship-box').css('opacity','.5');
 		}else{
 			jQuery('#country-ship,#zip-ship,#street-1-ship,#street-2-ship,#city-ship,#state-ship').removeClass('uneditable-input');
 			jQuery('#ship-box').css('opacity','1');
 		}
 	});

 	jQuery('.tool-tip').bind("mouseover mousedown", function() {
 		jQuery(this).popover('show');
 	}).bind("mouseout mouseup", function() {
 		jQuery(this).popover('hide');
 	});

 	jQuery('#distributor-signup').click(function(){
 		jQuery('#tax-id-block').toggle();
 		if(jQuery(this).is(':checked')) {
 			var sub_total = subtotal + 25;
 			jQuery('#order-table tbody tr:first').before('<tr id="associate-kit-item"><td>90101</td><td><h5 class="no-margin">New Associate Kit</h5></td><td>&nbsp;</td><td class="text-right">1 <br/><small>(@ $25.00 each)</small></td><td class="text-right">$25.00</td></tr>');
 			jQuery('#sub-total').html('$'+sub_total.toFixed(2));
 		} else {
 			jQuery('#associate-kit-item').remove();
 			jQuery('#sub-total').html('$'+subtotal.toFixed(2));
 		}
 	});

 	jQuery('#autoship-tc').click(function(){
 		jQuery('#autoship-block').toggle();
 		if(jQuery(this).is(':checked')) {
 		}
 	});

 	jQuery('#same-shipping').click(function(){
 		if(jQuery(this).is(':checked') && jQuery('#state').val() == 'CA'){
 			showProp65();
 		}else if(jQuery('#state-ship').val() == 'CA'){
 			showProp65();
 		}else {
 			hideProp65();	
 		}
 	});

 	jQuery('#state').change(function(){
 		if(jQuery(this).val() == 'CA' && jQuery('#same-shipping').is(':checked')){
 			showProp65();	
 		}else {
 			hideProp65();	
 		}
 		if(jQuery(this).val() == 'MT'){	
 			hideMTEnrollment();
 		}else{
 			showMTEnrollment()
 		}
 	});

 	jQuery('#state-ship').change(function(){
 		if(jQuery(this).val() == 'CA'){
 			showProp65();	
 		}else {
 			hideProp65();	
 		}
 		if(jQuery(this).val() == 'MT'){	
 			hideMTEnrollment();
 		}else{
 			showMTEnrollment()
 		}
 	});

 	jQuery('#country, #country-ship').change(function(){
 		countrySelect = jQuery(this);
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

 		jQuery('#'+stateSelect).children().remove();
 		jQuery('#'+stateSelect).html( selectOptions );

 		//checkMTEnrollment(enrollment_items, cart);
 	});

 	jQuery('#as-checkall').click(function(){
 		if(this.checked){
 			jQuery.each(jQuery(".as-checkbox").not("#as-checkall"), function(){
 				jQuery(this).attr('checked','checked');
 			});	
 		}else{
 			jQuery.each(jQuery(".as-checkbox").not("#as-checkall"), function(){
 				jQuery(this).removeAttr('checked');
 			});
 		}
 		checkAS();
 	});

 	jQuery(".as-checkbox").click(function(){
 		asAllItems = true;
 		jQuery.each(jQuery(".as-checkbox").not("#as-checkall"), function(){
 			if(jQuery(this).attr('checked')!='checked' && asAllItems==true){
 				asAllItems = false;
 			}
 		});
 		if(asAllItems==true){
 			jQuery("#as-checkall").attr('checked','checked');
 		}else{
 			jQuery("#as-checkall").removeAttr('checked');
 		}
 		checkAS();
 		a = getASOrderItems();
 	});

/* 	jQuery("#first-name, #last-name, #username, #email, #phone, #zip, #street-1, #street-2, #city, #zip-ship, #street-1-ship, #street-2-ship, #city-ship, #tax_id, #company_name").on('keyup blur change click input', function(){
 		var formfield_id = jQuery(this).attr('id');
 		var formfield_val = jQuery(this).val();
 		autoSaveForm(formfield_id, formfield_val);
 	});

 	jQuery("#country, #state, #country-ship, #state-ship").on('blur change click input',function(){
 		var formfield_id = jQuery(this).attr('id');
 		var formfield_val = jQuery(this).val();
 		autoSaveForm(formfield_id, formfield_val);
 	});*/

 	jQuery('#processing').on('show', function (e) {
 		if (window.top.document.querySelector('iframe')) {
 			var position = 'top: '+ window.top.scrollY + 'px !important';
 			jQuery('#processing').prop("style", position);
 		}
 	});

 	//checkMTEnrollment(enrollment_items, cart);

 	//autoRepopulateForm();
 });

function resizeIframe(screenHeight){
	height = jQuery("body").height();
	if(screenHeight != height){
		parent.document.getElementById('ygy-iframe').style.height = height+'px';
		screenHeight = height;
	}
	return screenHeight;
}

function showProp65(){
	jQuery('.prop65').show();
}
function hideProp65(){
	jQuery('.prop65').hide();
}

function showMTEnrollment(){
	jQuery('#distributor-signup-info').show();
	jQuery('#distributor-signup').val('1');
}
function hideMTEnrollment(){
	jQuery('#distributor-signup-info').hide();
	jQuery('#distributor-signup').val('0');
	if(jQuery('#distributor-signup').is(':checked')){ jQuery('#distributor-signup').removeAttr('checked'); } 
}

function checkMTEnrollment(enrollment_items, cart){
	var has_enrollment = false;
	var obj = jQuery.parseJSON(cart);
	
	if(obj.items != null){
		jQuery.each(obj.items, function(key,data){
			var item_id = data.id;
			if(enrollment_items.indexOf(item_id) >= 0 && has_enrollment == false) has_enrollment = true;
		});
		
		if(has_enrollment == true){
			jQuery("#state option[value='MT'], #state-ship [value='MT']").attr('disabled','disabled').html('Montana (No Enrollment Items Allowed)');
		}else {
			jQuery("#state option[value='MT'], #state-ship [value='MT']").removeAttr('disabled').html('Montana');
		}	
	}
}

function checkAS(){
	hasAS = false;
	jQuery.each(jQuery(".as-checkbox").not("#as-checkall"), function(){
		if(jQuery(this).attr('checked')=='checked' && hasAS == false){
			hasAS = true;
		}
	});
	if(hasAS== true){
		jQuery('.autoship-required').show();
	}else{
		jQuery('.autoship-required').hide();
	}
	return hasAS;
}

function autoRepopulateForm(){
	var user = jQuery.jStorage.get("user");
	var state = [];
	if(user != null){
		for(i=0; i < user.length; i++){
			if(user[i].id=='country' || user[i].id=='country-ship'){
				jQuery('#'+user[i].id).val(user[i].value);
				jQuery('#country,#country-ship').trigger('change');
			}
		}
		for(i=0; i < user.length; i++){
			jQuery('#'+user[i].id).val(user[i].value);
		}
	}
}

function autoSaveForm(formfield_id, formfield_val){
	var user = jQuery.jStorage.get("user");
	var in_user = false;
	if(user == null){
		user = [{'id':formfield_id, 'value':formfield_val}];
	}else{
		for(i=0; i < user.length; i++){
			if(user[i].id == formfield_id) {
				in_user = true;
				key = i; 
			}
		}
		if(in_user == true) {
			user[key].value = formfield_val;
		} else {
			user.push({'id':formfield_id, 'value':formfield_val});
		}
	}
	jQuery.jStorage.set("user", user);
}

/**
 * ERROR VALIDATION BEGIN
 * A section dedication to validation of the user input
 */
 
 /**
 * requiredAction(id,valid)
 * Description: Takes the id and changes the object given its validity.
 *		Adds the check or x mark and turns text red on false, otherwise show some green
 * id - The id of the inputs error box  ie. city-r
 * valid - Which validy to change it to
 */
 function requiredAction(input,valid,msg){
 	jQuery(input).closest('fieldset').find('.error').html(msg);
 	if(!valid){
 		jQuery(input).closest('fieldset').removeClass('success').addClass('error');
 		return false;
 	}
 	else{
 		jQuery(input).closest('fieldset').removeClass('error').addClass('success');
 		return true;
 	}
 }
// Validates a string value
function validWord(str){
	if (!str) return false;

	str = str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
	if(!str || str == '' || str == ' '){
		return false;
	}
	return true;
}
// Validates a username that has letters and numbers
function validUserName(str){
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
}
// Validates an email address
function validEmail(str){ 
	var filter=  new RegExp(/^.+@.+\..{2,3}$/);
	if (!filter.test(str)){
		return false;
	}
	return true;
}
// Validate zip code
function validZipCode(str, country){
	 // Check for correct zip code
	 var reZip = '';
	 
	 if(country == '') reZip = new RegExp(/_/);
	 if(country == 'USA') reZip = new RegExp(/(^\d{5}$)|(^\d{5}-\d{4}$)/);
	 if(country == 'CANADA') reZip = new RegExp(/^[ABCEGHJKLMNPRSTVXY]{1}\d{1}[A-Z]{1} *\d{1}[A-Z]{1}\d{1}$/);
	 if (!reZip.test(str)) {
	 	return false;
	 }
	 return true;
	}
// Validates a password to specific regex
function validPassword(str){
	pass = new RegExp(/^[a-zA-Z0-9\W]{8,}$/);
	if (!pass.test(str)) {
		return false;
	} 
	return true;
}
/*// Validate the Sponsor YGY ID
function validateYGYID(){
	var input = jQuery('#sponsor-id').val();
	var retval = validWord(input);
	if(!retval) msg = 'You must enter a Sponsor Youngevity ID number.';
	else  msg = '';
	requiredAction('#sponsor-id', retval, msg);
	return retval;
}*/
// Validate the Sponsor YGY ID. I accepts both URL and YGY ID
function validateYGYID(){
	var input = jQuery('#sponsor-id').val();
	var retval = validWord(input);
	if(!retval) {
		msg = 'Field can\'t be empty.';
		return false;
	}
	return jQuery.ajax({
		type: "POST",
		cache: false,
		url: fc_data_check_url,
		data: { "action" : 'check_sponsor_id', "sponsor_id" : encodeVal(input) },
		success: function(data) {
			if(data.success){
				jQuery.cookie("dac_ref", data.sponsor_id, {path: '/' });
				jQuery('#sponsor-id').val(data.sponsor_id);
				jQuery('#referer-name').val(data.name);
			}
			msg = data.message;
			requiredAction('#sponsor-id', data.success, msg);
			return false;
		},
		error: function(data){
			logError(data);
			alert("System Error. Please try again.");
			return false;
		}
	});
}
function validateRefererName(){
	var input = jQuery('#referer-name').val();
	if(input.length > 1){ var retval = true; }
	else { var retval = false; }
	var msg = '';
	if(!retval) msg = 'You must enter a referer\'s name.';
	requiredAction('#referer-name', retval, msg);
	return retval;
}
// Validate the First Name
function validateFirstName(){
	var input = jQuery('#first-name').val();
	var retval = validWord(input);
	if(!retval) msg = 'You must enter a First Name.';
	else  msg = '';
	requiredAction('#first-name', retval, msg);
	return retval;
}
// Validate the Last Name
function validateLastName(){
	var input = jQuery('#last-name').val();
	var retval = validWord(input);
	if(!retval) msg = 'You must enter a Last Name.';
	else  msg = '';
	requiredAction('#last-name', retval, msg);
	return retval;
}
// Validate the User Name
function validateUserName(){
	var input = jQuery('#username').val();
	var retval = validUserName(input);
	if(!retval) {
		msg = 'You must enter a Username at least 8 characters long.';
		requiredAction('#username', retval, msg);
		return retval;
	}
	return jQuery.ajax({
		type: "POST",
		url: fc_data_check_url,
		data: { "action" : 'usernametest', "username" : encodeVal(input) },
		success: function(data) {
			if(!data.success){
				msg = data.message;
			}else{
				msg = '';
			}
			requiredAction('#username', data.success, msg);
			console.log('username', data.success);
			return data.success;
		},
		error: function(data){
			logError(data);
			alert("System Error. Please try again.");
			return false;
		}
	});
}
// Validate the email
function validateEmail(){
	var input = jQuery('#email').val();
	var retval = validEmail(jQuery("#email").val());
	if(!retval) {
		msg = 'You must enter a valid E-mail Address.';
		requiredAction('#email', retval, msg);
		return retval;
	}
	return jQuery.ajax({
		type: "POST",
		url: fc_data_check_url,
		data: { "action" : 'emailtest', "email" : encodeVal(input) },
		success: function(data) {
			if(!data.success){
				msg = data.message;
			}else{
				msg = '';
			}
			requiredAction('#email', data.success, msg);
			console.log('email', data.success);
			return data.success;
		},
		error: function(data){
			logError(data);
			alert("System Error. Please try again.");
			return false;
		}
	});
	console.log('return email');
}
// Validate password
function validatePassword(){
	var retval = validPassword(jQuery('#password-1').val());
	if(!retval) msg = 'You must enter a valid Password.';
	else  msg = '';
	requiredAction('#password-1', retval, msg);
	return retval;
}
// Verify password
function verifyPassword(){
	var retval = (jQuery('#password-1').val() == jQuery('#password-2').val()) ? true : false;
	if(!retval) msg = 'The 2 Password fields must match.';
	else  msg = '';
	requiredAction('#password-2', retval, msg);
	return retval;
}
// Validate  phone number
function validatePhone(){
	var input = jQuery('#phone').val();
	var retval = validWord(input);
	if(!retval) msg = 'You must enter a valid Phone Number.';
	else  msg = '';
	requiredAction('#phone', retval, msg);
	return retval;
}

// Validate  Age
function validateAge (){
	var input = jQuery('#dob').val();
	var retval = validWord(input);
	if (retval) {
		var age = getAge(input);
		if (age < 18 || age > 100) {
			retval = false;
		}
	}

	if(!retval) msg = 'You must enter a valid date of birth.';
	else  msg = '';
	requiredAction('#dob', retval, msg);
	return retval;
}
// Validate country
function validateCountry(){
	return true;
	var input = jQuery('#country').val();
	var retval = validWord(input);
	if(input != '' && jQuery('#zip').val() != '') validateZip();
	if(!retval) msg = 'You select a valid Country.';
	else  msg = '';
	requiredAction('#country', retval, msg);
	return retval;
}
// Validate zip
function validateZip(){
	var input = jQuery('#zip').val();
	var country = jQuery('#country').val();
	var retval = validZipCode(input, country);
	if(!retval) {
		msg = 'You must enter a valid Zip/Postal Code.';
		requiredAction('#zip', retval,msg);
		return retval;
	}
	return jQuery.ajax({
		type: "POST",
		url: fc_data_check_url,
		data: { "action" : 'ziptest', "zip" : encodeVal(input) },
		success: function(data) {
			if (data){
				if(!data.success){
					msg = 'You must enter a valid Zip/Postal Code.';
				}else{
					msg = '';
				}
				requiredAction('#zip', data.success, msg);
				console.log('zip', data.success);
				return data.success;
			}
		},
		error: function(data){
			logError(data);
			alert("System Error. Please try again.");
			return false;
		}
	});	
	
	
}
// Validate street address
function validateStreetAddress(){
	var input = jQuery('#street-1').val();
	var retval = validWord(input);
	if(!retval) msg = 'You must enter a valid Street Address.';
	else  msg = '';
	requiredAction('#street-1', retval, msg);
	return retval;
}
// Validate city
function validateCity(){
	var input = jQuery('#city').val();
	var retval = validWord(input);
	if(!retval) msg = "can't be blank.";
	else  msg = '';
	requiredAction('#city',retval, msg);
	return retval;
}
// Validate state
function validateState(){
	var input = jQuery('#state').val();
	var retval = validWord(input);
	if(!retval) msg = "can't be blank.";
	else  msg = '';
	requiredAction('#state', retval, msg);
	return retval;
}
// Validate country
function validateCountryShip(){
	var input = jQuery('#country-ship').val();
	var retval = validWord(input);
	if(!retval) msg = 'You must select a valid Country.';
	else  msg = '';
	if(!jQuery('#same-shipping').is(":checked")){
		if(input != '' && jQuery('#zip-ship').val() != '') validateZipShip();
		requiredAction('#country-ship', retval, msg);
	}
	return retval;
}
// Validate zip
function validateZipShip(){
	var input = jQuery('#zip-ship').val();
	var country = jQuery('#country-ship').val();
	var retval = validZipCode(input, country);
	if(!retval) msg = 'You must enter a valid Zip/Postal Code.';
	else  msg = '';
	if(retval == true && country == 'USA'){
		jQuery.ajax({
			type: "POST",
			url: fc_data_check_url,
			data: { "action" : 'ziptest', "zip" : encodeVal(input) },
			success: function(data) {
				if(data.success && !jQuery('#same-shipping').is(":checked")){
					if(typeof data.city-state != 'undefined'){	

					}else{
						jQuery('#city-ship').val(data.city);
						jQuery('#state-ship').val(data.state);
						validateCityShip();
						validateStateShip();
					}
				}
			}
		});	
	}
	if(!jQuery('#same-shipping').is(":checked")) requiredAction('#zip-ship', retval, msg);
	return retval;
}
// Validate street address
function validateStreetAddressShip(){
	var input = jQuery('#street-1-ship').val();
	var retval = validWord(input);
	if(!retval) msg = 'You must enter a valid Street Address.';
	else  msg = '';
	if(!jQuery('#same-shipping').is(":checked")) requiredAction('#street-1-ship', retval, msg);
	return retval;
}
// Validate city
function validateCityShip(){
	var input = jQuery('#city-ship').val();
	var retval = validWord(input);
	if(!retval) msg = "can't be blank.";
	else  msg = '';
	if(!jQuery('#same-shipping').is(":checked")) requiredAction('#city-ship', retval, msg);
	return retval;
}
// Validate state
function validateStateShip(){
	var input = jQuery('#state-ship').val();
	var retval = validWord(input);
	if(!retval) msg = "can't be blank.";
	else  msg = '';
	if(!jQuery('#same-shipping').is(":checked")) requiredAction('#state-ship', retval, msg);
	return retval;
}
// Validate terms and conditions is checked
function validateTC(){
	var retval = jQuery('#terms-conditions').is(':checked');
	requiredAction('#terms-conditions',retval);
	return retval;
}
// Validate Autoship terms and conditions is checked
function validateASTC(){
	asAllItems = false;
	jQuery.each(jQuery(".as-checkbox").not("#as-checkall"), function(){
		if(jQuery(this).is(':checked') && asAllItems==false){
			asAllItems = true;
		}
	});
	if(asAllItems == true){
		var retval = jQuery('#autoship-tc').is(':checked');
		requiredAction('#autoship-tc',retval);
		return retval;
	}else {
		return true;
	}
}
function validateASExist(){
	var retval = false;
	if(jQuery('#autoship-tc').is(':checked')){
		jQuery.each(jQuery(".as-checkbox").not("#as-checkall"), function(){
			if(jQuery(this).is(':checked')){
				retval = true;
			}
		});
		requiredAction('#autoship-tc',retval);
		return retval;
	}else {
		return true;
	}
}
// Validate tax id if it is checked
function validateTaxID(){
	var input = jQuery("#tax_id").val();
	if(validWord(input) == false){
		requiredAction('#tax_id', false, 'You must enter a valid Tax ID.');
		return false;
	}else {
		requiredAction('#tax_id', true);
		return true;
	}


	var retval = jQuery('#distributor-signup').is(':checked');
	var input = jQuery('#tax-id').val();
	if(retval == true || enrollmentitem == true) {
		
	}else {
		return true;
	}
}
// Validate credit card number
function validateCCNumber(){
	var input = jQuery('#creditcard').val();
	var retval = validCCNum(input);
	if(retval == true){
		jQuery.ajax({
			async : true,
			type: "POST",
			url: fc_data_check_url,
			data: { "action" : 'cctest', "cc" : encodeVal(input) },
			success: function(data) {
				if(!data.success){
					retval = false;
				}
				if(!retval) msg = 'You must enter a valid Credit Card Number.';
				else  msg = '';
				requiredAction('#creditcard', data.success, msg);
				return data.success;
			},
			error: function(data){
				logError(data);
				alert("System Error. Please try again.");
				return false;
			}
		});
	}
	if(!retval) msg = 'Credit Card must be 15 or 16 digits.';
	else  msg = '';
	requiredAction('#creditcard', retval, msg);
	return retval;
}
// Validates a month
function validCCNum(str){
	ccnum = new RegExp(/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$/);
	if (!ccnum.test(str)) {
		return false;
	}
	return true;
}

function validateCCExp(){
	var d = new Date();
	var month = d.getMonth()+1;
	var fullyear = d.getFullYear();
	var year = fullyear.toString().substr(2, 2);
	var inputMonth = parseInt(jQuery('#exp-month').val(), 10);
	var inputYear = jQuery('#exp-year').val();
	if(inputYear >= year) {
		if(inputYear == year && inputMonth < month) {
			requiredAction('#exp-month', false, 'Credit Card Month invalid, card has expired.');
			requiredAction('#exp-year', true, '');
			return false;	
		} else {
			requiredAction('#exp-month', true, '');
			requiredAction('#exp-year', true, '');
			return true;
		}
	} else {
		requiredAction('#exp-month', false, '');
		requiredAction('#exp-year', false, 'Credit Card Year invalid, card has expired.');
		return false;
	}
}

// Validate credit card month
function validateCCMonth(){
	var input = jQuery('#exp-month').val();
	var retval = validMonth(input);
	requiredAction('#exp-month',retval);
	return retval;
}
// Validates a month
function validMonth(str){
	month = new RegExp(/^\d{1,2}$/);
	if (!month.test(str)) {
		return false;
	}
	return true;
}
// Validate credit card year
function validateCCYear(){
	var input = jQuery('#exp-year').val();
	var retval = validYear(input);
	requiredAction('#exp-year',retval);
	return retval;
}
// Validates a year
function validYear(str){
	year = new RegExp(/^\d{2}$/);
	if (!year.test(str)) {
		return false;
	} 
	return true;
}
// Validate CVV for credit card
function validateCVV(){
	var input = jQuery('#cardverify').val();
	var retval = validCVV(input);
	if(!retval) msg = 'Card Verification must be 3 or 4 digits.';
	else  msg = '';
	requiredAction('#cardverify',retval,msg);
	return retval;
}
// Validates a number
function validCVV(str){
	num = new RegExp(/^[0-9]{3,4}$/);
	if (!num.test(str)) {
		return false;
	} 
	return true;
}

 /**
 * validateInfo()
 * The submission of the information to validate all the billing and shipping information
 * It will run all the tests for each specific input, if they all pass it will continue to the next step
 */
 
 function showOverlay(){
 	jQuery('#processing').dialog('open');
 	setTimeout(validateInfo, 1500)
 }
 
 function validateInfo(){
 	jQuery('#user-information-btn').html('Validating...').addClass('disabled').prop('disabled');
 	var valid = true;
 	//err = (validateYGYID() && err) ? true : false;
 	valid = (validateRefererName() && valid) ? true : false;
 	valid = (validateFirstName() && valid) ? true : false;
 	valid = (validateLastName() && valid) ? true : false;
 	//err = (validateUserName() && err) ? true : false;
 	//err = (validateEmail() && err) ? true : false;
 	valid = (validatePassword() && valid) ? true : false;
 	valid = (validateStreetAddress() && valid) ? true : false;
 	valid = (validateCity() && valid) ? true : false;
 	valid = (validateState() && valid) ? true : false;
 	valid = (validateCountry() && valid) ? true : false;
 	valid = (validatePhone() && valid) ? true : false;
 	valid = (validateAge() && valid) ? true : false;
 	//err = (validateZip() && err) ? true : false;
 	if (valid){
 		jQuery.when( validateYGYID(), validateEmail(), validateUserName(), validateZip()).done(function(val1, val2, val3, val4) {
 			if (val1 && val2 && val3 && val4){
 				if (val1[0].success && val2[0].success && val3[0].success && val4[0].success){
 					validateConditionalInfo();
 				}else{
 					displayValidationError();
 				}
 			}else{
 				displayValidationError();
 			}
 	//if(!valid && !skipVerify && !skipSignup){
 	});
 	}else{
 		displayValidationError();
 	}
 }
 function displayValidationError(){
 	jQuery('#processing').dialog('close');
 	alert('Information in Billing and/or Shipping section is missing or incorrect. Please correct errors in red before continuing.');
 	jQuery('#user-information-btn').html('Submit Order <i class="icon-circle-arrow-right"></i>').removeClass('disabled').removeAttr('disabled');
 	return false;
 }
 function validationCCInfo(){
 	var err = true;
 	err = (validateCCNumber() && err) ? true : false;
 	err = (validateCCExp() && err) ? true : false;
 	err = (validateCVV() && err) ? true : false;
 	if(!err && !skipVerify && !skipSignup){
 		jQuery('#processing').dialog('close');
 		alert('Information in Credit Card section is missing or incorrect. Please correct errors in red before continuing.');
 		jQuery('#user-information-btn').html('Submit Order <i class="icon-circle-arrow-right"></i>').removeClass('disabled').removeAttr('disabled');
 		return false;
 	}else{
 		validateConditionalInfo();
 	}
 }

 function validateConditionalInfo(){
 	var err = true;
 	err = (validateTaxID() && err) ? true : false;
 	if(!err && !skipVerify && !skipSignup){
 		alert('Because you have elected to become a Distributor, you must provide your Tax ID information in order to proceed.');
 		jQuery('#processing').dialog('close');
 		jQuery('#user-information-btn').html('Submit Order <i class="icon-circle-arrow-right"></i>').removeClass('disabled').removeAttr('disabled');
 		return false;
 	}
 	err = (validateTC() && err) ? true : false;
 	if(!err && !skipVerify && !skipSignup){
 		alert('You must agree to the Sale Terms and Conditions in order to proceed. ');
 		jQuery('#processing').dialog('close');
 		jQuery('#user-information-btn').html('Submit Order <i class="icon-circle-arrow-right"></i>').removeClass('disabled').removeAttr('disabled');
 		return false;
 	}
 	err = (validateASTC() && err) ? true : false;
 	if(!err && !skipVerify && !skipSignup){
 		alert('Because you have elected to create an autoship, you must agree to the Autoship Terms and Conditions in order to proceed.');
 		jQuery('#processing').dialog('close');
 		jQuery('#user-information-btn').html('Submit Order <i class="icon-circle-arrow-right"></i>').removeClass('disabled').removeAttr('disabled');
 		return false;
 	}
 	err = (validateASExist() && err) ? true : false;
 	if(!err && !skipVerify && !skipSignup){
 		alert('It appear you have choosen to create an autoship, but have not selected any items. Check the autoship boxes below to select which items you want sent to you every month.');
 		jQuery('#processing').dialog('close');
 		jQuery('#user-information-btn').html('Submit Order <i class="icon-circle-arrow-right"></i>').removeClass('disabled').removeAttr('disabled');
 		return false;
 	}
	// If there are errors and the test parameters are not set, do not complete the first step
	if(!err && !skipVerify && !skipSignup){
		jQuery('#processing').dialog('close');
		jQuery('#user-information-btn').html('Submit Order <i class="icon-circle-arrow-right"></i>').removeClass('disabled').removeAttr('disabled');
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

function validateLogin(){
	var username = jQuery('#username-id').val();
	var password = jQuery('#password').val();
	var uservalid = validWord(username);
	var passvalid = validWord(password);
	var retval = (uservalid && passvalid) ? true : false;
	if(retval == true){
		jQuery.ajax({
			async : true,
			type: "POST",
			url: fc_data_check_url,
			data: { "action" : 'login', "username" : encodeVal(username), "password" : encodeVal(password) },
			success: function(data) {
				if(!data.success){
					requiredAction('#username-id', false);
					requiredAction('#password', false);
					alert("Invalid Login. Username and Password do not match.");
					return false;
				}
				if(data.success){
					fillLoginData(data.user);
					jQuery('#signup-account-info, #distributor-signup-info, #sponsor-form').hide();
					jQuery('#processing').dialog('close');
					estimateShipping();
					return true;
				}
			},
			error: function(data){
				logError(data);
				alert("System Error. Please try again.");
				return false;
			}
		});
	}else{
		requiredAction('#username-id',uservalid);
		requiredAction('#password',passvalid);
		return retval;
	}
}

function fillLoginData(user){
	userdata = user;
	jQuery("#rep-number").val(user.RepNumber);
	jQuery("#first-name").val(user.Firstname);
	jQuery("#last-name").val(user.Lastname);
	jQuery("#phone").val(user.Phone1);
	jQuery("#country").val(user.BillCountry);
	jQuery("#zip").val(user.BillPostalCode);
	jQuery("#street-1").val(user.BillStreet1);
	jQuery("#street-2").val(user.BillStreet2);
	jQuery("#city").val(user.BillCity);
	jQuery("#state").val(user.BillState);
	jQuery("#country-ship").val(user.ShipCountry);
	jQuery("#zip-ship").val(user.ShipPostalCode);
	jQuery("#street-1-ship").val(user.ShipStreet1);
	jQuery("#street-2-ship").val(user.ShipStreet2);
	jQuery("#city-ship").val(user.ShipCity);
	jQuery("#state-ship").val(user.ShipState);
}

function estimateShipping(){
	jQuery('#estimate-shipping-btn').html('Calculating...').addClass('disabled').attr('disabled');
	if(jQuery('#same-shipping').is(':checked')){
		var ShipCountry = encodeVal(jQuery("#country").val());
		var ShipPostalCode = encodeVal(jQuery("#zip").val());
		var ShipStreet1 = encodeVal(jQuery("#street-1").val());
		var ShipCity = encodeVal(jQuery("#city").val());
		var ShipState = encodeVal(jQuery("#state").val());
	}else {
		var ShipCountry = encodeVal(jQuery("#country-ship").val());
		var ShipPostalCode = encodeVal(jQuery("#zip-ship").val());
		var ShipStreet1 = encodeVal(jQuery("#street-1-ship").val());
		var ShipCity = encodeVal(jQuery("#city-ship").val());
		var ShipState = encodeVal(jQuery("#state-ship").val());
	}
	
	if(ShipCountry !='' && ShipPostalCode !='' && ShipStreet1 !='' && ShipCity !='' && ShipState !=''){
		if(jQuery("#rep-number").val()){
			var action = 'neworder';
		} else {
			var action = 'newsignuporder';
			if(jQuery('#distributor-signup').is(':checked') || enrollmentitem == true){
				var enrollmenttype = 'distributor';
			} else {
				var enrollmenttype = 'customer';	
			}
		}
		jQuery.ajax({
			async : true,
			type: "POST",
			url: fc_data_check_url,
			data: { "action" : 'shippingestimate', 
			"ShipStreet1" : ShipStreet1, 
			"ShipCity" : ShipCity,
			"ShipState" : ShipState,
			"ShipPostalCode" : ShipPostalCode,
			"ShipCountry" : ShipCountry,
			"Cart" : encodeVal(JSON.stringify(sorteditems)),
			"EnrollmentItem" : enrollmentitem,
			"EnrollmentType" : enrollmenttype
		},
		success: function(data) {
			jQuery('#estimate-shipping-btn').html('Estimate Shipping and Tax.').removeClass('disabled').removeAttr('disabled');
			if(data.success){
				jQuery('#ship-total').html(data.info.ShippingTotal);
				jQuery('#order-tax').html(data.info.TaxTotal);
				jQuery('#order-total').html(data.info.OrderTotal);
				jQuery('#order-shipping').html('['+data.info.Description+']');
				jQuery('#order-table tfoot tr td.muted').removeClass('muted');
				jQuery('#estimate-shipping-btn').html('Estimate Shipping and Tax.').removeClass('disabled').removeAttr('disabled');
				return true;
			} else {
				alert(data.message);
				jQuery('#estimate-shipping-btn').html('Estimate Shipping and Tax.').removeClass('disabled').removeAttr('disabled');
				return false;
			}
		},
		error: function(data){
			logError(data);
			jQuery('#estimate-shipping-btn').html('Estimate Shipping and Tax.').removeClass('disabled').removeAttr('disabled');
			return false;
		}
	});
	} else {
		alert('Please enter your shipping information to get your estimate.');
		jQuery('#estimate-shipping-btn').html('Estimate Shipping and Tax.').removeClass('disabled').removeAttr('disabled');
		return false;	
	}
}

function createOrder(){
	jQuery('#user-information-btn').html('Processing...');
	if(jQuery("#rep-number").val()){
		var action = 'neworder';
	} else {
		var action = 'newsignuporder';
		var EnrollmentType = '';
		if(jQuery('#distributor-signup').is(':checked') || enrollmentitem == true){
			EnrollmentType = 'distributor';
		} else {
			EnrollmentType = 'customer';	
		}
		if(EnrollmentType == 'distributor' && jQuery("#state").val() == 'MT') EnrollmentType = 'customer';
		else if(EnrollmentType == 'distributor' && jQuery("#state-ship").val() == 'MT') EnrollmentType = 'customer';
	}
	var RepNumber = jQuery("#rep-number").val();
	var TaxID = jQuery("#tax_id").val();
	var DateOfBirth = jQuery("#dob").val();
	var SponsorRepNumber = jQuery("#sponsor-id").val();
	var ReplicatedURL = jQuery("#username").val();
	var Email = jQuery("#email").val();
	var Password = jQuery("#password-1").val();
	var Firstname = jQuery("#first-name").val();
	var Lastname = jQuery("#last-name").val();
	var Phone1 = jQuery("#phone").val();
	var BillCountry = jQuery("#country").val();
	var BillPostalCode = jQuery("#zip").val();
	var BillStreet1 = jQuery("#street-1").val();
	var BillStreet2 = jQuery("#street-2").val();
	var BillCity = jQuery("#city").val();
	var BillState = jQuery("#state").val();
	var AccountType = jQuery("#account_type").val();
	if(jQuery('#same-shipping').is(':checked')) {
		var ShipCountry = BillCountry;
		var ShipPostalCode = BillPostalCode;
		var ShipStreet1 = BillStreet1;
		var ShipStreet2 = BillStreet2;
		var ShipCity = BillCity;
		var ShipState = BillState;
	}else {
		var ShipCountry = jQuery("#country-ship").val();
		var ShipPostalCode = jQuery("#zip-ship").val();
		var ShipStreet1 = jQuery("#street-1-ship").val();
		var ShipStreet2 = jQuery("#street-2-ship").val();
		var ShipCity = jQuery("#city-ship").val();
		var ShipState = jQuery("#state-ship").val();
	}
	/*
	var CreditCardNumber = jQuery("#creditcard").val();
	var ExpDateMonth = jQuery("#exp-month").val();
	var ExpDateYear = jQuery("#exp-year").val();
	var CVV = jQuery("#cardverify").val();
	*/
	var CreditCardNumber = '5178059371085235';//'4111111111111111';
	var ExpDateMonth = '12';
	var ExpDateYear = '22';
	var CVV = '123';

	var CompanyName = jQuery("#company_name").val();

	var ASItems = getASOrderItems();
	var ASDate = jQuery("#autoship-date").val();
	jQuery.ajax({
		type: "POST",
		url: fc_data_check_url,
		data: { "action" : action, 
		"RepNumber" : RepNumber,
		"TaxID" : TaxID,
		"DateOfBirth" : DateOfBirth,
		"SponsorRepNumber" : SponsorRepNumber,
		"ReplicatedURL" : ReplicatedURL,
		"Password" : Password,
		"Email" : Email,
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
		"Cart" : encodeVal(JSON.stringify(sorteditems)),
		"EnrollmentItem" : enrollmentitem,
		//"EnrollmentType" : EnrollmentType,
		"EnrollmentType" : AccountType,
		"ASItems" : encodeVal(JSON.stringify(ASItems)),
		"ASDate" : ASDate,
		"CompanyName" : CompanyName,
	},
	success: function(data) {
		if(data.success){
			jQuery('#processing').dialog('close');
				//alert('Order #'+data.orderid+' Successful.');
				jQuery('#ship-total').html(data.info.ShippingTotal);
				jQuery('#order-tax').html(data.info.TaxTotal);
				jQuery('#order-total').html(data.info.OrderTotal);
				jQuery('#order-shipping').html('['+data.info.Description+']');
				if(action=='neworder'){
					jQuery('#tc-block').after('<h4>Order Successful! Your order number is:'+data.info.OrderID+'</h4><h4>Your new Youngevity ID is: '+data.info.RepNumber+'</h4><h4>You Will Receive An Email Shortly</h4>');
				}else {
					jQuery('#tc-block').after('<h4>Order Successful! Your order number is: '+data.info.OrderID+'</h4><h4>Your new Youngevity ID is: '+data.info.RepNumber+'</h4><h4>You Will Receive An Email Shortly!</h4>');
				}
				jQuery('#credit-card-block, #tc-block,#already-member,#estimate-shipping-btn').remove();
				jQuery('#user-information-btn').hide();
				//window.top.deleteSessionCart();
				//jQuery.jStorage.deleteKey('user');			
				if(typeof(redirect) != 'undefined' && redirect != ''){
					joiner = (redirect.match(/\?/)) ? '&' : '?';
					redirect_location = redirect+joiner+"OrderID="+data.info.OrderID+"&RepID="+data.info.RepNumber
						+ "&RepFName=" + data.info.Firstname 
						+ "&RepLName=" + data.info.Lastname
						+ "&RepURL=" + data.info.ReplicatedURL
						;	
					window.location.href = redirect_location;
				}
			}else{
				jQuery('#processing').dialog('close');
				alert(data.message);
				jQuery('#user-information-btn').html('Submit Order <i class="icon-circle-arrow-right"></i>').removeClass('disabled').removeAttr('disabled');

			}
		},
		error: function(data){
			logError(data);
			alert("System Error. Could not finalize order. Please try again.");
			jQuery('#user-information-btn').html('Submit Order <i class="icon-circle-arrow-right"></i>').removeClass('disabled').removeProp('disabled');
			return false;
		}
	});
}

function getASOrderItems(){
	var asitems = new Array;
	jQuery.each(jQuery(".as-checkbox").not("#as-checkall"), function(){
		if(jQuery(this).attr('checked')=='checked'){
			var sku = jQuery(this).attr('data-sku');
			var qty = jQuery(this).attr('data-qty');
			var itemexists = false;
			var counter = 0;
			for(var i = 0; i < asitems.length; i++){
				if(asitems[i].sku == sku) {
					itemexists == true;
					counter = i;
					continue;
				}
			}
			if(itemexists){
				asitems[counter].qty += qty;
			} else {
				asitems.push({"sku":sku,"qty":qty});
			}
		}
	});
	return asitems;
}

function logError(data){
	jQuery.ajax({
		type: "POST",
		url: fc_data_check_url,
		data: { "action" : 'logerror', 
		"DataResponse" : data,
	},
	success: function(data) {
		return true;
	},
	error: function(data){
		logError(data);
		return false;
	}
});
}

function encodeVal(str){
	encodedstr = encodeURIComponent(str);
	return encodedstr;
}

function encodeElem(str){
	encodedstr = encodeURIComponent(jQuery(str).val());
	return encodedstr;
}

function isInIframe(){
	var isInIFrame = (window.location != window.parent.location) ? true : false;
	return isInIFrame;
}

function testRedirect(){
	var info = 'action=redirecttest';
	jQuery.ajax({
		type: "GET",
		url: "data-check.php?"+info,
		success: function(data) {
			if(data.success){
				alert('Order #'+data.orderid+' Successful.');
				if(typeof(redirect) != 'undefined' && redirect != ''){
					window.location.href = redirect;
					joiner = (redirect.match(/\?/)) ? '&' : '?';
					message = redirect+joiner+"OrderID="+"13579"+"&RepID="+"86420";	
					if(isInIframe()==true){
						window.parent.postMessage(message, "*");
					}else{
						window.location.href = message;
					}
				}else{
					//window.location.href = '//ygy1.com/btc-checkout/invoice.php';
				}
			}else{
				alert(data.message);	
			}
		},
		error: function(data){
			logError(data);
			alert("System Error. Could not redirect.");
			return false;
		}
	});
}

function getAge(dateString) {
    var today = new Date();
    var birthDate = new Date(dateString);
    var age = today.getFullYear() - birthDate.getFullYear();
    var m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}