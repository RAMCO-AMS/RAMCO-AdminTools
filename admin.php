<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <title>Admin Home</title>
    <?php 
require_once 'config.php';
require_once 'functions.php';
    ?>
   <link rel="stylesheet" type="text/css" href="Style.css">
    </head>
 <header>
<?php include_once("Header.html");?>
</header>

<body>
<br>
<div class="form">
    <?php echo "<a href='admin.php'>Reset Page</a>"; ?>
<h3>RAMCO Entity Management:</h3>
<li><a class="home" href="admin.php?formattedents=">List RAMCO Entities</a></li>
<br>
<form name="GetEntMeta" method="get" action="">
			<label for="Ent">Get Entity Attributes: </label>
			<input type="text" name="ent" id="ent">
		
			<input type="submit" name="send" id="send" value="Get Entity">
</form>
<br>
<hr>
<br>
<b>Build an Entity Lookup</b>
<form name="GetEntities" method="get" action="">
                        <label for="operation">Operation: </label> 
                        <select id="operations" name="operation">
                            <option value="GetEntities">GetEntities</option>
                            <option value="GetEntity">GetEntity</option>
                        </select>
                        <br>
                        <label for="entity">Entity:</label>
                        <input type="text" name="entity" id="entity">
                        <br>
                        <label for="entity">Filter OR GUID:</label>
                        <input type="text" size="100" name="filter" id="filter">
                        <br>
                        <label for="entity">Attributes:</label>
                        <input type="text" size="100" name="attributes" id="attributes">
                        <br>
			<input type="submit" name="buildent" id="buildent" value="Submit">
</form>
<br>
<hr>
<br>

<b>Get Contact Information</b>
<form name="contact" method="get" action="">
                                                
			<label for="Last">Last Name: </label> 
			<input type="text" name="lname" id="lname">
		    
			<label for="First">First Name: </label> 
			<input type="text" name="fname" id="fname">
			
			<input type="submit" name="namesubmit" id="namesubmit" value="submit"><br><br>
</form>
<br><hr><br>




<?php

if (isset($_GET['namesubmit'])){
    
    $lname = $_GET['lname'];
    $fname = $_GET['fname'];
    echo "<p>Search results for ".$fname." ".$lname."</p>";
    $searchResults = getContactFromName($lname, $fname, 'ParentCustomerId,ContactId,statecode,FirstName,LastName,cobalt_primaryassociationid,cobalt_NRDSID,EMailAddress1,cobalt_IARRELicenseNumber,cobalt_Username,cobalt_contact_cobalt_membership/cobalt_membertypeid,cobalt_contact_cobalt_membership/cobalt_OfficeId,cobalt_contact_cobalt_membership/statuscode,accountid');
    //pretty_print($searchResults);

            $sort_array = array();
            for ($i = 0; $i < sizeof($searchResults['Data']); $i++) {
                $Last = $searchResults['Data'][$i]['LastName'];
                $Last = strtolower($Last);
                $Last = ucfirst($Last);
                
                $First = $searchResults['Data'][$i]['FirstName'];
                $First = strtolower($First);
                $First = ucfirst($First);
                $license = $searchResults['Data'][$i]['cobalt_IARRELicenseNumber'];
                //$accountGUID = $searchResults['Data'][$i]['AccountId'];
                $stateCode = $searchResults['Data'][$i]['StateCode']['Display'];
                $ContactGUID = $searchResults['Data'][$i]['ContactId'];
                $ParentCust = $searchResults['Data'][$i]['ParentCustomerId']['Value'];
                $primaryAssocGUID = $searchResults['Data'][$i]['cobalt_PrimaryAssociationId']['Value'];
                $primaryAssocName = $searchResults['Data'][$i]['cobalt_PrimaryAssociationId']['Display'];
                if (isset($searchResults['Data'][$i]['cobalt_contact_cobalt_membership'][0]['cobalt_MemberTypeId']['Value'])){
                $OfficeName = $searchResults['Data'][$i]['cobalt_contact_cobalt_membership'][0]['cobalt_OfficeId']['Display'];
                $OfficeGUID = $searchResults['Data'][$i]['cobalt_contact_cobalt_membership'][0]['cobalt_OfficeId']['Value'];
                } else {
                    $OfficeGUID = "NA";
                }
                // Has membership record?
                               
                If (!isset($searchResults['Data'][$i]['cobalt_NRDSID'])) {
                    continue;
                } else {
                    $NRDS = $searchResults['Data'][$i]['cobalt_NRDSID'];
                }

                If (!isset($searchResults['Data'][$i]['cobalt_contact_cobalt_membership'][0])) {
                    $MemType = "NA";
                } elseif ($searchResults['Data'][$i]['cobalt_contact_cobalt_membership'][0]['cobalt_MemberTypeId']['Value'] == 'ce0b84b0-ef20-e111-b470-00155d000140') {
                    continue;
                } else {
                    $MemType = $searchResults['Data'][$i]['cobalt_contact_cobalt_membership'][0]['cobalt_MemberTypeId']['Display'];
                    $MemType = strtoupper($MemType);
                }

                If (!isset($searchResults['Data'][$i]['cobalt_contact_cobalt_membership'][0])) {
                    $Status = "NA";
                } else {
                    $Status = $searchResults['Data'][$i]['cobalt_contact_cobalt_membership'][0]['statuscode']['Display'];
                    $Status = strtoupper($Status);
                }
                $Full = "$Last, $First";
                                
                $temp_array = array("license" => $license, "primassocname" => $primaryAssocName, "primassocguid" => $primaryAssocGUID, "parentcustomer" => $ParentCust, "full" => $Full, "nrds" => $NRDS, "memtype" => $MemType, "contactguid" => $ContactGUID, "status" => $Status, "officename" => $OfficeName, "officeguid" => $OfficeGUID);
                array_push($sort_array, $temp_array);
            } //end for loop


            $element = array();
            foreach ($sort_array as $person) {
                $element[] = $person['status'];
            }
            array_multisort($element, SORT_ASC, $sort_array);
            //pretty_print($sort_array);
            
            ?>
<p>
<div>
    <?php
    for ($i = 0; $i < sizeof($sort_array); $i ++) {
    echo "<pre>";
    echo "<h3>".$sort_array[$i]['full']."</h3>";
    echo "<b>Member Type: </b>".$sort_array[$i]['memtype']."<br>"; 
    echo "<b>Status: </b>".$sort_array[$i]['status']."<br>";
    echo "<b>NRDS: </b>".$sort_array[$i]['nrds']."<br>";
    echo "<b>License: </b>".$sort_array[$i]['license']."<br>";
    echo "<b>Primary Association: </b>".$sort_array[$i]['primassocname']."<br>";
    echo "<b>Office: </b>".$sort_array[$i]['officename']."<br><br>";
    echo "<b>***************GUID's***************</b><br>";
    echo "<b>Primary Association GUID: </b>".$sort_array[$i]['primassocguid']."<br>";
    echo "<b>Contact GUID: </b>".$sort_array[$i]['contactguid']."<br>";
    //echo "<b>Parent Customer GUID: </b>".$sort_array[$i]['parentcustomer']."<br>";
    //echo "<b>Account GUID: </b>".$sort_array[$i]['accountguid']."<br>";
    echo "<b>Office GUID: </b>".$sort_array[$i]['officeguid']."<br>";
    
    echo "</pre>";
    echo "<hr>";
    }
    pretty_print($searchResults);
    ?>
</div>
</p>
<br>
<?php
}

if (isset($_GET['ent'])){
    $ent = $_GET['ent'];
    
    
        echo "<p>".$ent." - Entity Information</p>";
       
    $attr = getEntMetaArray($ent);
    asort($attr);
    $c = count($attr);
    $x=1;
         echo "<p>There are ".$c." attributes in ".$ent."</p>";
    while (list($var, $val) = each($attr)){
        
        echo $x++ .") ".$val."<br>";
        
    }    
        
    echo "<p>".$ent." - Entity Information</p>";
    
    $post = array();
    $post['key'] = API_KEY;
    $post['operation'] = 'GetEntityMetadata';
    $post['entity'] = $ent;
    $json = curl_request($post);
    $data = json_decode($json, true);
    
    pretty_print($data);
    
    
    echo "<p>".$ent." - Entity Information</p>";
    $attr = getEntMetaComma($ent);
    pretty_print($attr);
}

if (isset($_GET['formattedents'])){
    $post = array();
    $post['key'] = API_KEY;
    $post['operation'] = 'GetEntityTypes';
    $json = curl_request($post);
    $data = json_decode($json, true);

    asort($data['Data']);

    $x=1;
    foreach ($data['Data'] as $key => $value) {
        $url = "admin.php?ent=$key";
        echo $x++ .") <a class='home' href=".$url.">".$key."</a><br>";
        
    }

}

if (isset($_GET['contattrib'])){
    $post = array();
    $post['key'] = API_KEY;
    $post['operation'] = 'GetEntity';
    $post['entity'] = 'Contact';
    $post['guid'] = $_GET['contattrib'];
    $post['attributes'] = 'ParentCustomerId,ContactId,statuscode,FirstName,LastName,address1_addressid,address1_addresstypecode,cobalt_iarfirmjoindate,cobalt_preferredaddress,cobalt_joindate,mobilephone,address1_telephone1,address1_county,address1_city,address1_postalcode,address1_stateorprovince,cobalt_address1_stateprovinceid,address1_postofficebox,address1_line1,address1_line2,address1_name,cobalt_primaryassociationid,cobalt_NRDSID,cobalt_iarcustomdesignation,EMailAddress1,cobalt_IARRELicenseNumber,Telephone1,cobalt_Username,cobalt_contact_cobalt_membership/cobalt_membertypeid,cobalt_contact_cobalt_membership/cobalt_OfficeId,cobalt_contact_cobalt_membership/statuscode,address2_addressid,address2_addresstypecode,address2_telephone2,address2_county,address2_city,address2_postalcode,address2_stateorprovince,cobalt_address2_stateprovinceid,address2_postofficebox,address2_line1,address2_line2,address2_line3,address1_line3,address2_telephone1,address2_name,cobalt_federalpoliticalcoordinator,cobalt_iarbenefactorchamber,cobalt_preferredaddress'; //$post['maxresults'] = '5';

    $json = curl_request($post);
    $data = json_decode($json, true);
      
    pretty_print($data);
}

if (isset($_GET['buildent'])){
    
    var_dump($_GET);
    
    
    
    $operation = $_GET['operation'];
    if ($operation == 'GetEntities'){
    $entity = $_GET['entity'];
    $filter = $_GET['filter'];
    $attributes = $_GET['attributes'];
    
    $buildEnt = getEntities($entity,$filter,$attributes);
    
    pretty_print($buildEnt);
    }
    
    if ($operation == 'GetEntity'){
    $entity = $_GET['entity'];
    $guid = $_GET['filter'];
    $attributes = $_GET['attributes'];
    
    $buildEnt = getEntity($entity,$guid,$attributes);
    
    pretty_print($buildEnt);
    }
}
?>
<br>
</div>

<br>
</body>
</html>