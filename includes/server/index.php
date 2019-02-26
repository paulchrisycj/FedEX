<?php

//Enable cross origin access control
header("Access-Control-Allow-Origin: *");

//Load recources
require_once('./classes/recordset.class.php');
// require_once('./classes/recordSet_rmv_header.class.php');
require_once('./classes/pdoDB.class.php');

//Start Session
session_start();

//Time and Date
$mysqlDateandTime = date("Y-m-d H:i:s");

//Standard REQUEST
$call = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'error';
$subject = isset($_REQUEST['subject']) ? $_REQUEST['subject'] : null;
$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

//Flight REQUEST
$flight_postal_code= isset($_REQUEST['flight_postal_code']) ? $_REQUEST['flight_postal_code'] : null;
$station_code= isset($_REQUEST['station_code']) ? $_REQUEST['station_code'] : null;


//Action and Subject to Route
$route = $call . ucfirst($subject);

// Connect to db
$db = pdoDB::getConnection();

//set the header to json because everything is returned in that format
header("Content-Type: application/json");

function option($retval, $type){

    $ret = json_decode($retval, true);
    $option = "<option></option>";

    if($ret['RowCount'] == 0){
        $option = '<option>No option available</option>';
    }else{
        foreach($ret['results'] as $value){
            $option = $option.'<option value = "'.$value[$type].'">'.$value[$type].'</option>';
        }
    }

    return $option;
}

switch ($route) {

//  Standard
    case 'showFilterByPostalCode':
        $sqlSearch = "SELECT flight_filter.station_code, COUNT(flight_details.flight_details_id) as count_of_package FROM flight_details RIGHT JOIN flight_filter ON flight_details.flight_details_postal_code = flight_filter.flight_postal_code GROUP BY flight_filter.station_code";
        $rs = new JSONRecordSet();
        $retval = $rs->getRecordSet($sqlSearch, null, null);
        echo $retval;
        break;

    case 'showAllFilter':
        $sqlSearch = "SELECT * FROM flight_filter";
        $rs = new JSONRecordSet();
        $retval = $rs->getRecordSet($sqlSearch, null, null);
        echo $retval;
        break;

    case 'showOneFilterById':
        $sqlSearch = "SELECT * FROM flight_filter WHERE flight_filter_id=:id";
        $rs = new JSONRecordSet();
        $retval = $rs->getRecordSet($sqlSearch, null,
            array(':id'=>$id));
        echo $retval;
        break;

    case 'addFilter':
        $sqlInsert="INSERT INTO `fedex`.`flight_filter` 
                      (
                        `flight_postal_code`, 
                        `station_code`
                      ) 
                      VALUES 
                      (
                        :flight_postal_code,
                        :station_code)";
        $rs = new JSONRecordSet();
        $retval = $rs->setRecord(
            $sqlInsert, null, array(
                ':flight_postal_code'=>$flight_postal_code,
                ':station_code'=>$station_code
            )
        );
        echo $retval;
        break;

    case 'updateFilter':
        $sqlUpdate="UPDATE `fedex`.`flight_filter` 
                    SET
                    `station_code`=:station_code, 
                    `flight_postal_code`=:flight_postal_code 
                    WHERE 
                    `filter_flight_id`=:id";
        $rs = new JSONRecordSet();
        $retval = $rs->setRecord(
            $sqlUpdate, null, array(
                ':flight_postal_code'=>$flight_postal_code,
                ':station_code'=>$station_code,
                ':id'=>$id
            )
        );
        echo $retval;
        break;



    case 'option_postal_code':
        $sqlSearch = "SELECT DISTINCT flight_details_postal_code FROM flight_details";
        $rs = new JSONRecordSet();
        $retval = $rs->getRecordSet($sqlSearch, null, null);
        echo option($retval, "flight_details_postal_code");
        break;

}//end of switch
?>