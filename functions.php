<?php

//Handles posting a Ramco API request****************************************
function curl_request($post) {
    $curl = curl_init();

    // Set the request url and specify port 443 for SSL.
    curl_setopt($curl, CURLOPT_URL, API_URL);
    curl_setopt($curl, CURLOPT_PORT , 443);

    // Specify that the request should be posted and add the post data.
    curl_setopt($curl, CURLOPT_POST, True);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);

    // Verbose can be turned on to see LOTS of detail about the request and response.
    curl_setopt($curl, CURLOPT_VERBOSE, True);

    // No custom headers are needed.
    curl_setopt($curl, CURLOPT_HEADER, False);

    // Tell curl how to verify the SSL certificate.
    //curl_setopt($curl, CURLOPT_SSLVERSION,3);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
    curl_setopt($curl, CURLOPT_CAINFO, PEM_FILE);

    // Tell curl that curl_exec should return the response as a string instead of a direct output.
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 0);

    // Get the response.
    $resp_data = curl_exec($curl);
    $resp_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    // Return the response
    return $resp_data;
}



// GET Entities ************************************************************
//CONTACT RELATED FUNCTIONS
function getContactFromName($lname,$fname,$attr){
    $post = array();
    $post['key'] = API_KEY;
    $post['operation'] = 'GetEntities';
    $post['entity'] = 'Contact';
    $post['attributes'] = "$attr";
	$post['filter'] = 'LastName<sb>#'.$lname.'# and FirstName<sb>#'.$fname.'#';
	//pretty_print($post);
    $json = curl_request($post);
    $data = json_decode($json, true);
    //pretty_print($data);
    return $data;					
  
}


function getContactFromContactId($guid,$attr){
    $post = array();
    $post['key'] = API_KEY;
    $post['operation'] = 'GetEntity';
    $post['entity'] = 'Contact';
    $post['guid'] = $guid;
    $post['attributes'] = "$attr";
    //pretty_print($post);
    $json = curl_request($post);
    $data = json_decode($json, true);
    //pretty_print($data);
    return $data;
}


    

//CREATE/UPDATE ENTITIES
function modifyEntity($entity,$attr){
    $post = array();
    $post['key'] = API_KEY;
    $post['operation'] = 'CreateEntity';
    $post['entity'] = $entity;
    $post['attributevalues'] = $attr;
    pretty_print($post);
    $json = curl_request($post);
    $data = json_decode($json, true);
    pretty_print($data);
    return $data;
}




// Most excellent functions***********************************************
function testConnection(){
    
    $post = array();
    $post['key'] = API_KEY;
    $post['operation'] = 'GetEntities';
    $post['entity'] = 'Contact';
    $post['filter'] = 'LastName<sb>#Brewer# and FirstName<sb>#Matt#';
    $post['attributes'] = 'FirstName,LastName';
    $json = curl_request($post);
    
    $dataCont = json_decode($json, true);
    
    //pretty_print ($dataCont);
    if ($dataCont['ResponseCode'] != "200") {
        
        echo "<Strong>Response Code: ".$dataCont['ResponseCode'] ."</strong><br>";
        echo "<p class='announce'>There appears to be a problem. Check that you have an Internet connection and then contact support@iar.org and give them the \"Response Code\"</p>";
        echo "<hr>";

    } 
    
    
}

function pretty_print($arr){
  
				 echo "<pre>";
				 print_r($arr);
				 echo "</a>";

}

function brk() {
	echo "<br>";
}

function DateIsNow() {
	$timeStamp = time();
	$timeStamp = strftime("%m/%d/%y",$timeStamp);
	$noZeros = str_replace('*0','', $timeStamp);
	$cleanedString = str_replace('*', '', $noZeros);
	return $cleanedString;
}	

function clearCache(){
  
				    $post = array();
                    $post['key'] = API_KEY;
                    $post['operation'] = 'clearCache';
					
                    $json = curl_request($post);
                    $data = json_decode($json,true);
					

}


function getEntMetaComma($entity){
    $post = array();
    $post['key'] = API_KEY;
    $post['operation'] = 'GetEntityMetadata';
    $post['entity'] = $entity;
    $json = curl_request($post);
    $data = json_decode($json, true);

    $getEnt = $data['Data']['Attributes'];
    $getEnt = array_keys($getEnt);  
    $attr = implode(",", $getEnt);
    return $attr;
}

function getEntMetaArray($entity){
    $post = array();
    $post['key'] = API_KEY;
    $post['operation'] = 'GetEntityMetadata';
    $post['entity'] = $entity;
    $json = curl_request($post);
    $data = json_decode($json, true);

    $getEnt = $data['Data']['Attributes'];
    $getEnt = array_keys($getEnt);  
    
    return $getEnt;
}

//TODO - Fix this...
function createAttributeVariable($ent){
    $getEnt = getEntMeta($ent);
    $getEnt = $getEnt['Data']['Attributes'];
    $getEnt = array_keys($getEnt);
    for ($i = 0; $i < sizeof($getEnt); $i++) {
        $attr = $getEnt[$i] . ",";
        //$attr = substr($attr, 0, -1);
        //echo $attr;
    }
    return $attr;
    
}

function getEntity($entity,$guid,$attr){
    $post = array();
    $post['key'] = API_KEY;
    $post['operation'] = 'GetEntity';
    $post['entity'] = $entity;
    $post['guid'] = $guid;
    $post['attributes'] = $attr;
    pretty_print($post);
    $json = curl_request($post);
    $data = json_decode($json, true);
    //pretty_print($data);
    return $data;
}

function getEntities($entity,$filter,$attribute){
    $post = array();
    $post['key'] = API_KEY;
    $post['operation'] = 'GetEntities';
    $post['entity'] = $entity;
    $post['filter'] = $filter;
    $post['attributes'] = $attribute;
    //$post['attributes'] = 'cobalt_membertypeid,cobalt_stateassociationid,cobalt_membersubclass,cobalt_MemberSubclass,statuscode';
    //pretty_print($post);
    $json = curl_request($post);
    $data = json_decode($json, true);
    //pretty_print($data);
           
    return $data;
}


echo "<hr>";

?>
