<?php 
//              Validation Functions
function ValidateID($id){
    if (strlen($id) == 0) {
        return "ID Cannot be blank";
    } 
    return "";
}

function ValidateName ($name){
    if (strlen($name) == 0) {
        return "Name Cannot Be Blank";
    } 
    elseif (is_numeric($name)) {
        return "Please Enter A Valid Name";
    }
    return "";
}

function ValidatePhone($phoneNumber) {
    if (!preg_match("/^([1]-)?[2-9]{1}[0-9]{2}-[0-9]{3}-[0-9]{4}$/i", $phoneNumber)) {
        return "Phone Number Not In XXX-XXX-XXXX Format";
    }
    return "";
}

function ValidatePassword($password) {
    if (strlen($password) == 0) {
        return "Password Cannot Be Blank";
    }
    elseif (!ctype_alnum($password)){
        return "Password Has To Be Alphanumeric";
    }
    elseif (strlen($password) < 6) {
        return "Password Must be At Least 6 Characters Long";
    }
    elseif (!preg_match('/[A-Z]/', $password)) { //At least one uppercase
        return "Password Must Have At Least 1 Upper Case";
    }
    elseif (!preg_match('/[a-z]/', $password)) { //At least one lowercase
        return "Password Must Have At Least 1 Lower Case";
    }
    elseif (!preg_match('/[0-9]/', $password)) { //At least one digit
        return "Password Must Have At Least 1 Digit";
    }
    return "";
}

function ConfirmPassword($password, $passwordConfirm) {
    if ($passwordConfirm != $password) {
        return "Password Does Not Match The Confirm Password.";
    }
    elseif (strlen($password) == 0) {
        return "Cannot Be Blank";
    }
    return "";
}

function ValidatePage() {
    $args = func_get_args();
    if (array_unique($args) === array("")) { 
        return true;
    }
    else {
        return false;
    }
}