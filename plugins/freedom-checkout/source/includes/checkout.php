<?php
if (!defined('INCLUDED')) {
	die('File Not Found.');
	exit;
}else{
	$prop65items = ['10243','10245','10246','10247','10248','10249','10250','10251','10252','10253','10254','10256','10257','10258','10259','10260','10261','10262','10263','10264','10275','10280','10282','1050','1055','1062','1068','1069','1070','1071','1072','1082','1083','1084','1085','1086','1088','1091','1092','1093','13203','13203C','13204','13204C','13209','13209c','13217','1511','20030','21010C','21010V','21023C','21023CC','21023V','21023VC','23221','23230','50197','50223','50241','67507','81110','HTG-109','HTG-109P','HTG-109S','HTG-130','HTG-130P','HTG-130S','HTG-171','HTG-171P','HTG-171S','LV105','LV114','LV117','LV201A','TL004SYS','TL005SYS','TL006PROD','TL006PRODfs','TL007PROD','TL008PROD','TL010PROD','TL011PROD','TL014SYS','TL015SYS','TL018SYS','TL030SYS','TL031SYS','TL034SYS','TL040SYS','TL041SYS','TL042SYS','TL043SYS','TL044SYS','TL045SYS','TL046SYS','TL047SYS','TL048SYS','TL049SYS','USBI000008','USBI0002','USBI0003','USBI0008','USBI0009','USBI0011','USBI0013','USBI0014','USEW0002','USGF0006','USKC000001','USKC000002','USKC000003','USKC000004','USKC000005','USKC000006','USKC000007','USKC000008','USKC000009','USKC200001','USKC200002','USSN000001','USSN100000','USSN100001','USYC100120','USYC100130','USYC200110','USYC200120','USYC200130','USYC200140','USYC200405','USYC200410','USYC200415','USYC200904','USYC200905','USYC200906','USYC200908','USYC300100','USYC300110','USYC300120','USYC300130','USYC300140','USYC300150','USYC300904','USYC300906','USYC400001','USYG000009','USYG0008','USYG0010','USYG0011','USYG0012','USYG0013','USYG0023','USYG0061','USYG0062','USYG100061','USYG100062','USYG100075','USYG100076','USYG100077','USYG100078','USYG100095','USYG103210','USYG103211','USYG103212','USYG103230','x1070SP','x1093',];

	$prop65canceritems = ['20975','20975C','JF8005S','JF8010S','JF8015S','JF8020S','JF8025S','JF8035S','JF8040S','JF9002S','JF9003S','JF9004S','JF9005S','SP400','SP401','SP402','SP403','SP404','SP405','SP406','SP407','SP408','SP409','SP600','SP601','SP602','SP603','SP604','SP605','SP606','SP607','USAD500004','USAD500004FS','USAD500005','USAD500005FS','USAN574911','USYC100120','USYC100130','USYC200110','USYC200120','USYC200130','USYC200140','USYC200405','USYC200410','USYC200415','USYC200904','USYC200905','USYC200906','USYC200908','USYC300100','USYC300110','USYC300120','USYC300130','USYC300140','USYC300150','USYC300904','USYC300906','USYC400001','YBTC000001','YBTC000012'];

	$enrollment_items = ['1072','1070','1086','1097','1075','1074','1092','1091','1094','USMK0001','1063','1064','SP1000','10280','USFD400910','90101','1076','1077','1078','1079','1084','1089','USMK77777','USAN5000','USFT5000','USHM5000','USML5000','USYG5000','USYG5001','USYG5002','USYG5010'];



	include('freedom.class.php');
	$request = check_server_request_method();
	if( isset($request['destroy']) && $request['destroy'] == 1) unset($_SESSION['cart']);
	if( isset($request['testmode']) && $request['testmode'] == 1) { set_testmode();}
	if( empty($_SESSION['user']['SponsorRepNumber'])) check_for_sponsor_id();
	if( empty($_SESSION['redirect'])) check_for_redirect_location();
	if( isset($_SESSION['cart']['items']) || isset($_SESSION['cart']['packages'])) check_for_updated_items();
	else save_session_items();

	if( isset($request['interface']) && $request['interface'] == 1) $_SESSION['interface'] = 'youngevity';
	elseif(empty($request['interface']) || $request['interface'] == 0) $_SESSION['interface'] = 'default';


	if( isset($request['action'])){
		 switch($request['action']){
		 	case 'login':
				validate_login();
				save_session_items();
			break;
			/*
			case 'register':
				$_SESSION['form_validate'] = true;
				validate_registration();
			break;
			case 'update':
				$_SESSION['form_validate'] = true;
				validate_update_information();
			break;*/
		}
	}

	$cart = $_SESSION['cart'];
	$user = $_SESSION['user'];
	$sponsorid = $user['SponsorRepNumber'];
	$loggedin = isset($_SESSION['user']['LoggedIn'])?$_SESSION['user']['LoggedIn']:null;
	$enrollment_item = isset($_SESSION['enrollment_item'])?$_SESSION['enrollment_item']:null;

	$hassponsor = ($sponsorid || $loggedin) ? true : false;
}

function set_testmode(){
	$_SESSION['testmode'] = true;
	$_SESSION['user']['SponsorRepNumber'] = 312501;
	$_SESSION['user']['RepNumber'] = 'R007';
	if( empty($request['item-1'])){
		$_POST['item-1'] = '10245|1';
		$_GET['item-1'] = '10245|1';
	}
	if( empty($request['pkg-1']) && empty($request['pkgitems-1'])){
		$_POST['pkg-1'] = 'Amazing Athletes|1';
		$_POST['pkgitems-1'] = '10245|1,21251|1,13223|1';
		$_GET['pkg-1'] = 'Amazing Athletes|1';
		$_GET['pkgitems-1'] = '10245|1,21251|1,13223|1';
	}
}

function check_server_request_method(){
	if($_SERVER['REQUEST_METHOD'] === 'POST') $request = $_POST;
	elseif($_SERVER['REQUEST_METHOD'] === 'GET') $request = $_GET;
	return $request;
}

function check_for_sponsor_id(){
	$request = check_server_request_method();
	if(isset($request['sponsorid'])){
		if(empty($_SESSION['user']['SponsorRepNumber'])){
			if(verify_sponsor_id($request['sponsorid'])) $_SESSION['user']['SponsorRepNumber'] = $request['sponsorid'];
		}
	}
	if( empty($_SESSION['user']['SponsorRepNumber'])) $_SESSION['user']['SponsorRepNumber'] = 9999;
}

function verify_sponsor_id($sponsorid){
	$freedom = new Freedom();
	$data = $freedom->GetRepInfo($sponsorid);
	if($data['Success']) return TRUE;
	else return FALSE;
}

function check_for_updated_items(){
	$request = check_server_request_method();
	if( isset($request['action']) && $request['action'] == 'updateitems'){
		save_session_items();
		if(!empty($_SESSION['cart']['tempordernumber'])) update_cart_and_totals();
	}
}

function save_session_items(){
	$items;
	$packages;
	//$data = check_server_request_method();
	$data = FreedomCheckout::get_cart_items();
	$itemsleft = TRUE;
	$packagesleft  = TRUE;
	$i = 1;
	$packages = null;
	while( $itemsleft || $packagesleft){
		//Save all normal youngevity items
		if( isset($data['item-'.$i])){
			$item = explode('|', $data['item-'.$i]);
			$items[$i]['id'] = $item[0];
			$items[$i]['qty'] = $item[1];
		}else $itemsleft = FALSE;
		//Save all custom packages
		if( isset($data['pkg-'.$i])){
			$package = explode('|', $data['pkg-'.$i]);
			$packages[$i] = array('name' => $package[0], 'qty' => $package[1]);
			if( isset($data['pkgitems-'.$i])){
				$hascomma = strpos($data['pkgitems-'.$i], ',');
				if( ! empty($hascomma)){
					$packagecontents = explode(',', $data['pkgitems-'.$i]);
					$j = 0;
					foreach($packagecontents as $iteminfo){
						$package = explode('|', $iteminfo);
						$packages[$i]['items'][$j]['id'] = $package[0];
						$packages[$i]['items'][$j]['qty'] = $package[1];
						$j++;
					}
				}else{
					$itemcontents = explode('|', $data['pkgitems-'.$i]);
					$packages[$i]['items'][0]['id'] = $itemcontents[0];
					$packages[$i]['items'][0]['qty'] = $itemcontents[1];
				}
			}
		}else $packagesleft  = FALSE;
		$i++;
	}
	if( count($items) > 0) $_SESSION['cart']['items'] = $items;
	if( count($packages) > 0) $_SESSION['cart']['packages'] = $packages;
	save_item_data();
}

function save_item_data(){

	$productdata = array();
	$sorteditems  = array();
	$cartsubtotal = 0;
	$pkgdata = 0;
	$itemdata = $_SESSION['cart']['items'];
	if (isset($_SESSION['cart']['packages'])){
		$pkgdata = $_SESSION['cart']['packages'];
	}
	if( count($itemdata) > 0){
		$i = 1;
		foreach($itemdata as $item){
			$freedomdata = get_item_data($item['id'], $productdata);
			$productdata = $freedomdata;
			if (isset($sorteditems[$item['id']])){
				$sorteditems[$item['id']] += $itemdata[$i]['qty'];
			}else{
				$sorteditems[$item['id']] = $itemdata[$i]['qty'];
			}
			$itemdata[$i]['name'] = $productdata[$item['id']]['name'];
			$itemdata[$i]['price'] = $productdata[$item['id']]['price']; //25
			$itemdata[$i]['volume'] = $productdata[$item['id']]['volume'];
			$cartsubtotal += ($itemdata[$i]['qty']*$itemdata[$i]['price']);
			$i++;
		}
	}
	if( is_array($pkgdata) && count($pkgdata) > 0){
		$i = 1;
		foreach($pkgdata as $packages => $pack){
			$j = 0;
			$packprice = 0;
			foreach($pack['items'] as $items => $item){
				$freedomdata = get_item_data($item['id'], $freedomdata);
				$productdata = $freedomdata;
				$sorteditems[$item['id']] += $pkgdata[$i]['items'][$j]['qty']*$pkgdata[$i]['qty'];
				$pkgdata[$i]['items'][$j]['name'] = $productdata[$item['id']]['name'];
				$pkgdata[$i]['items'][$j]['price'] = $productdata[$item['id']]['price'];
				$pkgdata[$i]['items'][$j]['volume'] = $productdata[$item['id']]['volume'];
				$packprice += $productdata[$item['id']]['price']*$pkgdata[$i]['items'][$j]['qty'];
				$j++;
			}
			$pkgdata[$i]['price'] = $packprice;
			$cartsubtotal += ($pkgdata[$i]['qty']*$packprice);
			$i++;
		}
	}
	$_SESSION['cart']['items'] = $itemdata;
	$_SESSION['cart']['packages'] = $pkgdata;
	$_SESSION['sorteditems'] = $sorteditems;
	$_SESSION['cart']['subtotal'] = $cartsubtotal;
}

function get_item_data($itemid, $productdata){
	$freedom = new Freedom();
	if( empty($productdata[$itemid])){
		$freedomdata = $freedom->GetInventory_SingleItem($itemid);
		$data = $freedomdata['Inventory']['GetInventoryReturn_v3'];
		if($data['Category'] == 'Enrollment Packages' || $data['CategoryID']== '389'){
			$_SESSION['enrollment_item'] = true;
		}
		$itemdata = array('name' => $data['Description'], 'price' => $data['Price'], 'volume' => $data['Volume']);
		$productdata[$itemid] = $itemdata;
	}
	return $productdata;
}

function check_for_redirect_location(){
	$request = check_server_request_method();
	if(isset($request['redirect']) && empty($_SESSION['redirect'])){
		$_SESSION['redirect'] = $request['redirect'];
	}
}

function validate_login(){
	$username = filter_var( trim($_POST['username-id']), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
	$password = trim($_POST['password']);
	$freedom = new Freedom();
	if( !empty($username) && !empty($password)){
		$freedomdata = $freedom->LoginCheck_Rep($username,$password);
		if(!$freedomdata['Success']){
			$replookup = $freedom->GetRepInfo($username);
			if($replookup['Success']){
				$freedomdata = $freedom->LoginCheck_Rep($replookup['RepID'],$password);
			}
		}
		if($freedomdata['Success']){
			$_SESSION['formfill'] = true;
			$_SESSION['user']['LoggedIn']  = true;
			$_SESSION['user']['RepNumber'] = $freedomdata['RepNumber'];
			$_SESSION['user']['Firstname'] = $freedomdata['Firstname'];
			$_SESSION['user']['Lastname'] = $freedomdata['Lastname'];
			$_SESSION['user']['Email'] = $freedomdata['Email'];
			$_SESSION['user']['Phone1'] = $freedomdata['Phone1'];
			$_SESSION['user']['BillStreet1'] = $freedomdata['BillStreet1'];
			$_SESSION['user']['BillStreet2'] = $freedomdata['BillStreet2'];
			$_SESSION['user']['BillCity'] = $freedomdata['BillCity'];
			$_SESSION['user']['BillState'] = $freedomdata['BillState'];
			$_SESSION['user']['BillPostalCode'] = $freedomdata['BillPostalCode'];
			$_SESSION['user']['BillCountry'] = $freedomdata['BillCountry'];
			$_SESSION['user']['ShipStreet1'] = $freedomdata['ShipStreet1'];
			$_SESSION['user']['ShipStreet2'] = $freedomdata['ShipStreet2'];
			$_SESSION['user']['ShipCity'] = $freedomdata['ShipCity'];
			$_SESSION['user']['ShipState'] = $freedomdata['ShipState'];
			$_SESSION['user']['ShipPostalCode'] = $freedomdata['ShipPostalCode'];
			$_SESSION['user']['ShipCountry'] = $freedomdata['ShipCountry'];
			return true;
		}
	}
	return false;
}

/*
function validate_registration(){
	$request = check_server_request_method();
			$_SESSION['formfill'] = true;
			$_SESSION['user']['SponsorRepNumber'] = $request['SponsorRepNumber'];
			$_SESSION['user']['Firstname'] = $request['Firstname'];
			$_SESSION['user']['Lastname'] = $request['Lastname'];
			$_SESSION['user']['Username'] = $request['Username'];
			$_SESSION['user']['Email'] = $request['Email'];
			//$password1 = $request['Password1'];
			//$pasword2 = $request['Password2'];
			$tempdata['Password']= $request['Password1'];
			$_SESSION['user']['Phone1'] = $request['Phone1'];
			$_SESSION['user']['BillStreet1'] = $request['BillStreet1'];
			$_SESSION['user']['BillStreet2'] = $request['BillStreet2'];
			$_SESSION['user']['BillCity'] = $request['BillCity'];
			$_SESSION['user']['BillState'] = $request['BillState'];
			$_SESSION['user']['BillPostalCode'] = $request['BillPostalCode'];
			$_SESSION['user']['BillCountry'] = $request['BillCountry'];
			$_SESSION['user']['ShipStreet1'] = (!$request['SameShipping']) ? $request['ShipStreet1'] : $request['BillStreet1'];
			$_SESSION['user']['ShipStreet2'] = (!$request['SameShipping']) ? $request['ShipStreet2'] : $request['BillStreet2'];
			$_SESSION['user']['ShipCity'] = (!$request['SameShipping']) ? $request['ShipCity'] : $request['BillCity'];
			$_SESSION['user']['ShipState'] = (!$request['SameShipping']) ? $request['ShipState'] : $request['BillState'];
			$_SESSION['user']['ShipPostalCode'] = (!$request['SameShipping']) ? $request['ShipPostalCode'] : $request['BillPostalCode'];
			$_SESSION['user']['ShipCountry'] = (!$request['SameShipping']) ? $request['ShipCountry'] : $request['BillCountry'];
			$_SESSION['user']['Terms'] = $request['Terms'];
			$_SESSION['user']['Newsletter'] = $request['Newsletter'];
			create_user($tempdata);
}

function create_user(){
	$freedom = new Freedom();
	$freedomdata = $freedom->CreateOnlineSignUp($_SESSION['user'], $tempdata);
	if( ! empty($freedomdata)) $_SESSION['user']['SignUpID'] = $freedomdata;
	else {
		return false;
	}
}

function validate_update_information(){

}

function form_valid($fieldname){
	$result = '';
	if( isset($_SESSION['form_validate'])){
		if( empty($_SESSION['user'][$fieldname])) $result = 'error';
	}
	echo $result;
}
*/
function form_fill($fieldname){
	$result = '';
	if( isset($_SESSION['formfill'])){
		if( ! empty($_SESSION['user'][$fieldname])) $result = 'value = "'.$_SESSION['user'][$fieldname].'"';
	}
	echo $result;
}

function form_fill_select($fieldname, $value){
	if(isset($_SESSION['user'][$fieldname]) && $_SESSION['user'][$fieldname] == $value) echo 'selected';
}

function form_fill_checkbox($fieldname,$value){
	if(isset($_SESSION['user'][$fieldname]) && $_SESSION['user'][$fieldname] == $value) echo 'checked';
}

function update_cart_and_totals(){
	if(!empty($_SESSION['cart']['tempordernumber'])){
		$freedom = new Freedom();
		//clear cart
		$freedom->OnlineOrder_ClearItems($_SESSION['cart']['tempordernumber']);
		//re-add updated items
		$items = $_SESSION['sorteditems'];
		foreach($items as $itemid => $qty){
			$freedomdata = $freedom->OnlineOrder_AddItem($_SESSION['cart']['tempordernumber'], $itemid, $qty);
			if( is_numeric($freedomdata) && $freedomdata > 0 && !$itemsincart) $itemsincart = true;
		}
		if( isset($itemsincart)){
			$freedomdata = $freedom->OnlineOrder_GetShipMethods_v2($_SESSION['cart']['tempordernumber']);
			if( ! empty($freedomdata)){
				$shipmethod = (isset($freedomdata['ShipMethods_v2']['0']['ShipMethodID'])) ? $freedomdata['ShipMethods_v2'][0] : $freedomdata['ShipMethods_v2'];
				$_SESSION['cart']['shipmethod'] = $shipmethod['Description'];
				$_SESSION['cart']['shipmethodid'] = $shipmethod['ShipMethodID'];
				$updateshipmethodid = $freedom->OnlineOrder_UpdateShipMethod($_SESSION['cart']['tempordernumber'], $shipmethod['ShipMethodID']);
				if($updateshipmethodid){
					//get new totals
					$freedomdata = $freedom->OnlineOrder_GetTotals($_SESSION['cart']['tempordernumber']);
					if( ! empty($freedomdata)){
						$_SESSION['cart']['shiptotal'] = number_format($freedomdata['ShippingTotal'], 2, '.', ',');
						$_SESSION['cart']['taxtotal'] = number_format($freedomdata['TaxTotal'], 2, '.', ',');
						$_SESSION['cart']['total'] = number_format($freedomdata['OrderTotal'], 2, '.', ',');
					}
				}
			}
		}
	}
}

/* Generic Variables & Functions */

define('BASE_URL', '//shopfor90.com/freedom-checkout/');

function base_url(){
	echo BASE_URL;
}

function get_base_url(){
	return BASE_URL;
}
?>