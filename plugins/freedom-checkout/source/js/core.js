/* core.js - Handles all the AJAX , Error Checking, and general javascript functions */

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
$(document).ready(function(){
		
	$(window).bind("resize click keypress", function() {
		screenHeight = resizeIframe(screenHeight);
	});
	screenHeight = resizeIframe(screenHeight);
	
	$('#username-id, #password').keypress(function(event) {
        if(event.keyCode == 13) validateLogin();
    });
	$('#same-shipping').click(function(){
		$("#shipping-info").toggle();
		if($(this).is(':checked')) {
			$('#country-ship,#zip-ship,#street-1-ship,#street-2-ship,#city-ship,#state-ship').addClass('uneditable-input');
			$('#ship-box').css('opacity','.5');
		}else{
			$('#country-ship,#zip-ship,#street-1-ship,#street-2-ship,#city-ship,#state-ship').removeClass('uneditable-input');
			$('#ship-box').css('opacity','1');
		}
	});
	
	$('.tool-tip').bind("mouseover mousedown", function() {
	  $(this).popover('show');
	}).bind("mouseout mouseup", function() {
	  $(this).popover('hide');
	});
	
	$('#distributor-signup').click(function(){
		$('#tax-id-block').toggle();
		if($(this).is(':checked')) {
			var sub_total = subtotal + 25;
			$('#order-table tbody tr:first').before('<tr id="associate-kit-item"><td>90101</td><td><h5 class="no-margin">New Associate Kit</h5></td><td>&nbsp;</td><td class="text-right">1 <br/><small>(@ $25.00 each)</small></td><td class="text-right">$25.00</td></tr>');
			$('#sub-total').html('$'+sub_total.toFixed(2));
		} else {
			$('#associate-kit-item').remove();
			$('#sub-total').html('$'+subtotal.toFixed(2));
		}
	});
	
	$('#autoship-tc').click(function(){
		$('#autoship-block').toggle();
		if($(this).is(':checked')) {
		}
	});
	
	$('#same-shipping').click(function(){
		if($(this).is(':checked') && $('#state').val() == 'CA'){
			showProp65();
		}else if($('#state-ship').val() == 'CA'){
			showProp65();
		}else {
			hideProp65();	
		}
	});
	
	$('#state').change(function(){
		if($(this).val() == 'CA' && $('#same-shipping').is(':checked')){
			showProp65();	
		}else {
			hideProp65();	
		}
		if($(this).val() == 'MT'){	
			hideMTEnrollment();
		}else{
			showMTEnrollment()
		}
	});
	
	$('#state-ship').change(function(){
		if($(this).val() == 'CA'){
			showProp65();	
		}else {
			hideProp65();	
		}
		if($(this).val() == 'MT'){	
			hideMTEnrollment();
		}else{
			showMTEnrollment()
		}
	});
	
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
		
		checkMTEnrollment(enrollment_items, cart);
	});
	
	$('#as-checkall').click(function(){
		if(this.checked){
			$.each($(".as-checkbox").not("#as-checkall"), function(){
				$(this).attr('checked','checked');
        	});	
		}else{
			$.each($(".as-checkbox").not("#as-checkall"), function(){
				$(this).removeAttr('checked');
        	});
		}
		checkAS();
	});
	
	$(".as-checkbox").click(function(){
		asAllItems = true;
		$.each($(".as-checkbox").not("#as-checkall"), function(){
			if($(this).attr('checked')!='checked' && asAllItems==true){
				asAllItems = false;
			}
		});
		if(asAllItems==true){
			$("#as-checkall").attr('checked','checked');
		}else{
			$("#as-checkall").removeAttr('checked');
		}
		checkAS();
		a = getASOrderItems();
	});
		
	$("#first-name, #last-name, #username, #email, #phone, #zip, #street-1, #street-2, #city, #zip-ship, #street-1-ship, #street-2-ship, #city-ship").on('keyup blur change click input', function(){
		var formfield_id = $(this).attr('id');
		var formfield_val = $(this).val();
		autoSaveForm(formfield_id, formfield_val);
	});
	
	$("#country, #state, #country-ship, #state-ship").on('blur change click input',function(){
		var formfield_id = $(this).attr('id');
		var formfield_val = $(this).val();
		autoSaveForm(formfield_id, formfield_val);
	});
	/*
	 $('#processing').on('show', function (e) {
        if (window.top.document.querySelector('iframe')) {
			var position = 'top: '+ window.top.scrollY + 'px !important';
			$('#processing').prop("style", position);
        }
    });
	*/
	checkMTEnrollment(enrollment_items, cart);
	
	autoRepopulateForm();
});

function resizeIframe(screenHeight){
	height = $("body").height();
	if(screenHeight != height){
		parent.document.getElementById('ygy-iframe').style.height = height+'px';
		screenHeight = height;
	}
	return screenHeight;
}

function showProp65(){
	$('.prop65').show();
}
function hideProp65(){
	$('.prop65').hide();
}

function showMTEnrollment(){
	$('#distributor-signup-info').show();
	$('#distributor-signup').val('1');
}
function hideMTEnrollment(){
	$('#distributor-signup-info').hide();
	$('#distributor-signup').val('0');
	if($('#distributor-signup').is(':checked')){ $('#distributor-signup').removeAttr('checked'); } 
}

function checkMTEnrollment(enrollment_items, cart){
	var has_enrollment = false;
	var obj = jQuery.parseJSON(cart);
	
	if(obj.items != null){
		$.each(obj.items, function(key,data){
			var item_id = data.id;
			if(enrollment_items.indexOf(item_id) >= 0 && has_enrollment == false) has_enrollment = true;
		});
		
		if(has_enrollment == true){
			$("#state option[value='MT'], #state-ship [value='MT']").attr('disabled','disabled').html('Montana (No Enrollment Items Allowed)');
		}else {
			$("#state option[value='MT'], #state-ship [value='MT']").removeAttr('disabled').html('Montana');
		}	
	}
}

function checkAS(){
	hasAS = false;
	$.each($(".as-checkbox").not("#as-checkall"), function(){
		if($(this).attr('checked')=='checked' && hasAS == false){
			hasAS = true;
		}
    });
	if(hasAS== true){
		$('.autoship-required').show();
	}else{
		$('.autoship-required').hide();
	}
	return hasAS;
}

function autoRepopulateForm(){
	var user = jQuery.jStorage.get("user");
	var state = [];
	if(user != null){
		for(i=0; i < user.length; i++){
			if(user[i].id=='country' || user[i].id=='country-ship'){
				$('#'+user[i].id).val(user[i].value);
				$('#country,#country-ship').trigger('change');
			}
		}
		for(i=0; i < user.length; i++){
			$('#'+user[i].id).val(user[i].value);
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
		$(input).closest('.control-group .controls').find('.help-block').html(msg);
		if(!valid){
			$(input).closest('.control-group').removeClass('success').addClass('error');
			return false;
		}
		else{
			$(input).closest('.control-group').removeClass('error').addClass('success');
			return true;
		}
}
// Validates a string value
function validWord(str){
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
// Validate the Sponsor YGY ID
function validateYGYID(){
	var input = $('#sponsor-id').val();
	var retval = validWord(input);
	if(!retval) msg = 'You must enter a Sponsor Youngevity ID number.';
	else  msg = '';
	requiredAction('#sponsor-id', retval, msg);
	return retval;
}
// Validate the First Name
function validateFirstName(){
	var input = $('#first-name').val();
	var retval = validWord(input);
	if(!retval) msg = 'You must enter a First Name.';
	else  msg = '';
	requiredAction('#first-name', retval, msg);
	return retval;
}
// Validate the Last Name
function validateLastName(){
	var input = $('#last-name').val();
	var retval = validWord(input);
	if(!retval) msg = 'You must enter a Last Name.';
	else  msg = '';
	requiredAction('#last-name', retval, msg);
	return retval;
}
// Validate the User Name
function validateUserName(){
	var input = $('#username').val();
	var retval = validUserName(input);
	if(!retval) msg = 'You must enter a Username at least 8 characters long.';
	else  msg = '';
	if(retval == true){
		$.ajax({
			async : true,
			type: "POST",
			url: "data-check.php",
			data: { "action" : 'usernametest', "username" : encodeVal(input) },
			success: function(data) {
				if(data.success==false){
					retval = false;
					msg = data.message;
				}else{
					retval = true;
				}
				requiredAction('#username', retval, msg);
				return false;
			},
			error: function(data){
				logError(data);
				alert("System Error. Please try again.");
				return false;
			}
		});
	}
	requiredAction('#username', retval, msg);
	return retval;
}
// Validate the email
function validateEmail(){
	var input = $('#email').val();
	var retval = validEmail($("#email").val());	
	if(!retval) msg = 'You must enter a valid E-mail Address.';
	else  msg = '';
	if(retval == true){
		$.ajax({
			async : true,
			type: "POST",
			url: "data-check.php",
			data: { "action" : 'emailtest', "email" : encodeVal(input) },
			success: function(data) {
				if(!data.success){
					retval = false;
					if(!retval) msg = data.message;
				}
				requiredAction('#email', data.success);
				return data.success;
			},
			error: function(data){
				logError(data);
				alert("System Error. Please try again.");
				return false;
			}
		});
	}
	requiredAction('#email', retval, msg);
	return retval;
}
// Validate password
function validatePassword(){
	var retval = validPassword($('#password-1').val());
	if(!retval) msg = 'You must enter a valid Password.';
	else  msg = '';
	requiredAction('#password-1', retval, msg);
	return retval;
}
// Verify password
function verifyPassword(){
	var retval = ($('#password-1').val() == $('#password-2').val()) ? true : false;
	if(!retval) msg = 'The 2 Password fields must match.';
	else  msg = '';
	requiredAction('#password-2', retval, msg);
	return retval;
}
// Validate  phone number
function validatePhone(){
	var input = $('#phone').val();
	var retval = validWord(input);
	if(!retval) msg = 'You must enter a valid Phone Number.';
	else  msg = '';
	requiredAction('#phone', retval, msg);
	return retval;
}
// Validate country
function validateCountry(){
	var input = $('#country').val();
	var retval = validWord(input);
	if(input != '' && $('#zip').val() != '') validateZip();
	if(!retval) msg = 'You select a valid Country.';
	else  msg = '';
	requiredAction('#country', retval, msg);
	return retval;
}
// Validate zip
function validateZip(){
	var input = $('#zip').val();
	var country = $('#country').val();
	var retval = validZipCode(input, country);
	if(!retval) msg = 'You must enter a valid Zip/Postal Code.';
	else  msg = '';
	if(retval == true && country == 'USA'){
		$.ajax({
			type: "POST",
			url: "data-check.php",
			data: { "action" : 'ziptest', "zip" : encodeVal(input) },
			success: function(data) {
				if(data.success){
					if(typeof data.city-state != 'undefined'){			
							  
					}else{
					  $('#city').val(data.city);
					  $('#state').val(data.state);
					  validateCity();
					  validateState();
					}
				}
			}
		});	
	}
	requiredAction('#zip', retval,msg);
	return retval;
}
// Validate street address
function validateStreetAddress(){
	var input = $('#street-1').val();
	var retval = validWord(input);
	if(!retval) msg = 'You must enter a valid Street Address.';
	else  msg = '';
	requiredAction('#street-1', retval, msg);
	return retval;
}
// Validate city
function validateCity(){
	var input = $('#city').val();
	var retval = validWord(input);
	if(!retval) msg = "can't be blank.";
	else  msg = '';
	requiredAction('#city',retval, msg);
	return retval;
}
// Validate state
function validateState(){
	var input = $('#state').val();
	var retval = validWord(input);
	if(!retval) msg = "can't be blank.";
	else  msg = '';
	requiredAction('#state', retval, msg);
	return retval;
}
// Validate country
function validateCountryShip(){
	var input = $('#country-ship').val();
	var retval = validWord(input);
	if(!retval) msg = 'You must select a valid Country.';
	else  msg = '';
	if(!$('#same-shipping').is(":checked")){
		if(input != '' && $('#zip-ship').val() != '') validateZipShip();
		requiredAction('#country-ship', retval, msg);
	}
	return retval;
}
// Validate zip
function validateZipShip(){
	var input = $('#zip-ship').val();
	var country = $('#country-ship').val();
	var retval = validZipCode(input, country);
	if(!retval) msg = 'You must enter a valid Zip/Postal Code.';
	else  msg = '';
	if(retval == true && country == 'USA'){
		$.ajax({
			type: "POST",
			url: "data-check.php",
			data: { "action" : 'ziptest', "zip" : encodeVal(input) },
			success: function(data) {
				if(data.success && !$('#same-shipping').is(":checked")){
					if(typeof data.city-state != 'undefined'){	
									  
					}else{
					  $('#city-ship').val(data.city);
					$('#state-ship').val(data.state);
					validateCityShip();
					validateStateShip();
					}
				}
			}
		});	
	}
	if(!$('#same-shipping').is(":checked")) requiredAction('#zip-ship', retval, msg);
	return retval;
}
// Validate street address
function validateStreetAddressShip(){
	var input = $('#street-1-ship').val();
	var retval = validWord(input);
	if(!retval) msg = 'You must enter a valid Street Address.';
	else  msg = '';
	if(!$('#same-shipping').is(":checked")) requiredAction('#street-1-ship', retval, msg);
	return retval;
}
// Validate city
function validateCityShip(){
	var input = $('#city-ship').val();
	var retval = validWord(input);
	if(!retval) msg = "can't be blank.";
	else  msg = '';
	if(!$('#same-shipping').is(":checked")) requiredAction('#city-ship', retval, msg);
	return retval;
}
// Validate state
function validateStateShip(){
	var input = $('#state-ship').val();
	var retval = validWord(input);
	if(!retval) msg = "can't be blank.";
	else  msg = '';
	if(!$('#same-shipping').is(":checked")) requiredAction('#state-ship', retval, msg);
	return retval;
}
// Validate terms and conditions is checked
function validateTC(){
	var retval = $('#terms-conditions').is(':checked');
	requiredAction('#terms-conditions',retval);
	return retval;
}
// Validate Autoship terms and conditions is checked
function validateASTC(){
	asAllItems = false;
	$.each($(".as-checkbox").not("#as-checkall"), function(){
			if($(this).is(':checked') && asAllItems==false){
				asAllItems = true;
			}
	});
	if(asAllItems == true){
		var retval = $('#autoship-tc').is(':checked');
		requiredAction('#autoship-tc',retval);
		return retval;
	}else {
		return true;
	}
}
function validateASExist(){
	var retval = false;
	if($('#autoship-tc').is(':checked')){
		$.each($(".as-checkbox").not("#as-checkall"), function(){
			if($(this).is(':checked')){
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
	var retval = $('#distributor-signup').is(':checked');
	var input = $('#tax-id').val();
	if(retval == true || enrollmentitem == true) {
		if(validWord(input) == false){
			requiredAction('#tax-id', false, 'You must enter a valid Tax ID.');
			return false;
		}else {
			requiredAction('#tax-id', true);
			return true;
		}
	}else {
		return true;
	}
}
// Validate credit card number
function validateCCNumber(){
	var input = $('#creditcard').val();
	var retval = validCCNum(input);
	if(retval == true){
		$.ajax({
			async : true,
			type: "POST",
			url: "data-check.php",
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
	var inputMonth = parseInt($('#exp-month').val(), 10);
	var inputYear = $('#exp-year').val();
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
	var input = $('#exp-month').val();
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
	var input = $('#exp-year').val();
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
	var input = $('#cardverify').val();
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
	$('#processing').modal('show');
	setTimeout(validateInfo, 1500)
}
 
function validateInfo(){
	$('#user-information-btn').html('Validating...').addClass('disabled').prop('disabled');
	var err = true;
	err = (validateFirstName() && err) ? true : false;
	err = (validateLastName() && err) ? true : false;
	if($('#rep-number').val() == '') {
		err = (validateUserName() && err) ? true : false;
		err = (validateEmail() && err) ? true : false;
		err = (validatePassword() && err) ? true : false;
	}
	err = (validateStreetAddress() && err) ? true : false;
	err = (validateCity() && err) ? true : false;
	err = (validateState() && err) ? true : false;
	err = (validateCountry() && err) ? true : false;
	err = (validatePhone() && err) ? true : false;
	if(!$('#same-shipping').is(":checked")){
		err = (validateStreetAddressShip() && err) ? true : false;
		err = (validateCityShip() && err) ? true : false;
		err = (validateStateShip() && err) ? true : false;
		err = (validateCountryShip() && err) ? true : false;
	}
	if(!err && !skipVerify && !skipSignup){
		$('#processing').modal('hide');
		alert('Information in Billing and/or Shipping section is missing or incorrect. Please correct errors in red before continuing.');
		$('#user-information-btn').html('Submit Order <i class="icon-circle-arrow-right"></i>').removeClass('disabled').removeAttr('disabled');
		return false;
	}else{
		validationCCInfo();
	}
}

function validationCCInfo(){
	var err = true;
	err = (validateCCNumber() && err) ? true : false;
	err = (validateCCExp() && err) ? true : false;
	err = (validateCVV() && err) ? true : false;
	if(!err && !skipVerify && !skipSignup){
		$('#processing').modal('hide');
		alert('Information in Credit Card section is missing or incorrect. Please correct errors in red before continuing.');
		$('#user-information-btn').html('Submit Order <i class="icon-circle-arrow-right"></i>').removeClass('disabled').removeAttr('disabled');
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
		$('#user-information-btn').html('Submit Order <i class="icon-circle-arrow-right"></i>').removeClass('disabled').removeAttr('disabled');
		return false;
	}
	err = (validateTC() && err) ? true : false;
	if(!err && !skipVerify && !skipSignup){
		alert('You must agree to the Sale Terms and Conditions in order to proceed. ');
		$('#user-information-btn').html('Submit Order <i class="icon-circle-arrow-right"></i>').removeClass('disabled').removeAttr('disabled');
		return false;
	}
	err = (validateASTC() && err) ? true : false;
	if(!err && !skipVerify && !skipSignup){
		alert('Because you have elected to create an autoship, you must agree to the Autoship Terms and Conditions in order to proceed.');
		$('#user-information-btn').html('Submit Order <i class="icon-circle-arrow-right"></i>').removeClass('disabled').removeAttr('disabled');
		return false;
	}
	err = (validateASExist() && err) ? true : false;
	if(!err && !skipVerify && !skipSignup){
		alert('It appear you have choosen to create an autoship, but have not selected any items. Check the autoship boxes below to select which items you want sent to you every month.');
		$('#user-information-btn').html('Submit Order <i class="icon-circle-arrow-right"></i>').removeClass('disabled').removeAttr('disabled');
		return false;
	}
	// If there are errors and the test parameters are not set, do not complete the first step
	if(!err && !skipVerify && !skipSignup){
		$('#processing').modal('hide');
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

function validateLogin(){
	var username = $('#username-id').val();
	var password = $('#password').val();
	var uservalid = validWord(username);
	var passvalid = validWord(password);
	var retval = (uservalid && passvalid) ? true : false;
	if(retval == true){
		$.ajax({
			async : true,
			type: "POST",
			url: "data-check.php",
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
					$('#signup-account-info, #distributor-signup-info, #sponsor-form').hide();
					$('#login').modal('hide');
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
	$("#rep-number").val(user.RepNumber);
	$("#first-name").val(user.Firstname);
	$("#last-name").val(user.Lastname);
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
}

function estimateShipping(){
	$('#estimate-shipping-btn').html('Calculating...').addClass('disabled').attr('disabled');
	if($('#same-shipping').is(':checked')){
		var ShipCountry = encodeVal($("#country").val());
		var ShipPostalCode = encodeVal($("#zip").val());
		var ShipStreet1 = encodeVal($("#street-1").val());
		var ShipCity = encodeVal($("#city").val());
		var ShipState = encodeVal($("#state").val());
	}else {
		var ShipCountry = encodeVal($("#country-ship").val());
		var ShipPostalCode = encodeVal($("#zip-ship").val());
		var ShipStreet1 = encodeVal($("#street-1-ship").val());
		var ShipCity = encodeVal($("#city-ship").val());
		var ShipState = encodeVal($("#state-ship").val());
	}
	
	if(ShipCountry !='' && ShipPostalCode !='' && ShipStreet1 !='' && ShipCity !='' && ShipState !=''){
		if($("#rep-number").val()){
			var action = 'neworder';
		} else {
			var action = 'newsignuporder';
			if($('#distributor-signup').is(':checked') || enrollmentitem == true){
				var enrollmenttype = 'distributor';
			} else {
				var enrollmenttype = 'customer';	
			}
		}
		$.ajax({
			async : true,
			type: "POST",
			url: "data-check.php",
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
				$('#estimate-shipping-btn').html('Estimate Shipping and Tax.').removeClass('disabled').removeAttr('disabled');
				if(data.success){
					$('#ship-total').html(data.info.ShippingTotal);
					$('#order-tax').html(data.info.TaxTotal);
					$('#order-total').html(data.info.OrderTotal);
					$('#order-shipping').html('['+data.info.Description+']');
					$('#order-table tfoot tr td.muted').removeClass('muted');
					$('#estimate-shipping-btn').html('Estimate Shipping and Tax.').removeClass('disabled').removeAttr('disabled');
					return true;
				} else {
					alert(data.message);
					$('#estimate-shipping-btn').html('Estimate Shipping and Tax.').removeClass('disabled').removeAttr('disabled');
					return false;
				}
			},
			error: function(data){
				logError(data);
				$('#estimate-shipping-btn').html('Estimate Shipping and Tax.').removeClass('disabled').removeAttr('disabled');
				return false;
			}
		});
	} else {
		alert('Please enter your shipping information to get your estimate.');
		$('#estimate-shipping-btn').html('Estimate Shipping and Tax.').removeClass('disabled').removeAttr('disabled');
		return false;	
	}
}

function createOrder(){
	$('#user-information-btn').html('Processing...');
	if($("#rep-number").val()){
		var action = 'neworder';
	} else {
		var action = 'newsignuporder';
		var EnrollmentType = '';
		if($('#distributor-signup').is(':checked') || enrollmentitem == true){
			EnrollmentType = 'distributor';
		} else {
			EnrollmentType = 'customer';	
		}
		if(EnrollmentType == 'distributor' && $("#state").val() == 'MT') EnrollmentType = 'customer';
		else if(EnrollmentType == 'distributor' && $("#state-ship").val() == 'MT') EnrollmentType = 'customer';
	}
	var RepNumber = $("#rep-number").val();
	var TaxID = $('#tax-id').val();
	var SponsorRepNumber = $("#sponsor-id").val();
	var ReplicatedURL = $("#username").val();
	var Email = $("#email").val();
	var Password = $("#password-1").val();
	var Firstname = $("#first-name").val();
	var Lastname = $("#last-name").val();
	var Phone1 = $("#phone").val();
	var BillCountry = $("#country").val();
	var BillPostalCode = $("#zip").val();
	var BillStreet1 = $("#street-1").val();
	var BillStreet2 = $("#street-2").val();
	var BillCity = $("#city").val();
	var BillState = $("#state").val();
	if($('#same-shipping').is(':checked')) {
		var ShipCountry = BillCountry;
		var ShipPostalCode = BillPostalCode;
		var ShipStreet1 = BillStreet1;
		var ShipStreet2 = BillStreet2;
		var ShipCity = BillCity;
		var ShipState = BillState;
	}else {
		var ShipCountry = $("#country-ship").val();
		var ShipPostalCode = $("#zip-ship").val();
		var ShipStreet1 = $("#street-1-ship").val();
		var ShipStreet2 = $("#street-2-ship").val();
		var ShipCity = $("#city-ship").val();
		var ShipState = $("#state-ship").val();
	}
	var CreditCardNumber = $("#creditcard").val();
	var ExpDateMonth = $("#exp-month").val();
    var ExpDateYear = $("#exp-year").val();
    var CVV = $("#cardverify").val();
	var ASItems = getASOrderItems();
	var ASDate = $("#autoship-date").val();
	$.ajax({
		type: "POST",
		url: "data-check.php",
		data: { "action" : action, 
				"RepNumber" : RepNumber,
				"TaxID" : TaxID,
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
				"EnrollmentType" : EnrollmentType,
				"ASItems" : encodeVal(JSON.stringify(ASItems)),
				"ASDate" : ASDate
		},
		success: function(data) {
			if(data.success || 1){
				$('#processing').modal('hide');
				alert('success');
				//alert('Order #'+data.orderid+' Successful.');
				$('#ship-total').html(data.info.ShippingTotal);
				$('#order-tax').html(data.info.TaxTotal);
				$('#order-total').html(data.info.OrderTotal);
				$('#order-shipping').html('['+data.info.Description+']');
				if(action=='neworder'){
					$('#tc-block').after('<h4>Order Successful! Your order number is:'+data.info.OrderID+'</h4><h4>Your new Youngevity ID is: '+data.info.RepNumber+'</h4><h4>You Will Receive An Email Shortly</h4>');
				}else {
					$('#tc-block').after('<h4>Order Successful! Your order number is: '+data.info.OrderID+'</h4><h4>Your new Youngevity ID is: '+data.info.RepNumber+'</h4><h4>You Will Receive An Email Shortly!</h4>');
				}
				$('#credit-card-block, #tc-block,#already-member,#estimate-shipping-btn').remove();
				$('#user-information-btn').hide();
				//window.top.deleteSessionCart();
				//$.jStorage.deleteKey('user');			
				if(typeof(redirect) != 'undefined' && redirect != ''){
					joiner = (redirect.match(/\?/)) ? '&' : '?';
					redirect_location = redirect+joiner+"OrderID="+data.info.OrderID+"&RepID="+data.info.RepNumber;	
					window.location.href = redirect_location;
				}
			}else{
				$('#processing').modal('hide');
			 	alert(data.message);
				$('#user-information-btn').html('Submit Order <i class="icon-circle-arrow-right"></i>').removeClass('disabled').removeAttr('disabled');
	
			}
		},
		error: function(data){
			logError(data);
			alert("System Error. Could not finalize order. Please try again.");
			$('#user-information-btn').html('Submit Order <i class="icon-circle-arrow-right"></i>').removeClass('disabled').removeProp('disabled');
			return false;
		}
	});
}

function getASOrderItems(){
	var asitems = new Array;
	$.each($(".as-checkbox").not("#as-checkall"), function(){
		if($(this).attr('checked')=='checked'){
			var sku = $(this).attr('data-sku');
			var qty = $(this).attr('data-qty');
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
	$.ajax({
		type: "POST",
		url: "data-check.php",
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
	encodedstr = encodeURIComponent($(str).val());
	return encodedstr;
}

function isInIframe(){
	var isInIFrame = (window.location != window.parent.location) ? true : false;
	return isInIFrame;
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