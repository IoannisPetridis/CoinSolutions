<?php
require_once("validators.php");
function getPostValue($index, $key, $val_type, $field_type, $flag) {
	global $data;
	$value = $data[$index][$key];
	if ($flag) {
		if (($value == null)||($value=='')) {
			die("Null/Empty value for ".$key."!");
		}
	}
	if ($val_type == "string") {
		if (is_string($value)) {
			if ($field_type == "name") {
				//Could add more checks here...
				$newValue = filter_var($value, FILTER_SANITIZE_STRING);
				return $newValue;
			}
			else if ($field_type == "email") {
				if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
					return $value;
				}
				else {
					die("Invalid email value");
				}
			}
			else if ($field_type == "description") {
				//Could add more checks here...
				return filter_var($value, FILTER_SANITIZE_STRING);
			}
			else if ($field_type == "account id btc") {
				if (validateBtcAccount($value)) {
					return $value;
				}
				else {
					die("Invalid bitcoin account id");
				}
			}
			else if ($field_type == "account id eth") {
				if (validateEthAccount($value)) {
					return $value;
				}
				else {
					die("Invalid ethereum account id");
				}
			}
			else if ($field_type == "type") {
				if (($value!=null)||($value!='')) {
					return $value;
				}
				else {
					die("Null value for type");
				}
			}
		}
		else {
			die("Wrong value type");
		}
	}
	else if ($val_type == "double") {
		if (is_double($value)) {
			if ($field_type == "account balance btc") {
				if (checkAccountBalanceBtc()) {
					return $value;
				}
				else {
					die("Invalid bitcoin account balance");
				}
			}
			else if ($field_type == "account balance eth") {
				if (checkAccountBalanceEth()) {
					return $value;
				}
				else {
					die("Invalid ethereum account balance");
				}
			}
			else if ($field_type == "amount") {
				//Some sophisticated check for the amount
				return $value;
			}
		}
		else {
			die("Wrong value type");
		}
	}
	else if ($val_type == "int") {
		if (is_int($value)) {
			return $value;
		}
		else {
			die("Wrong value type");
		}
	}
}

?>