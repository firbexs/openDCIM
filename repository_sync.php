<?php

	require_once( "db.inc.php" );
	require_once( "facilities.inc.php" );
	
	$t = new DeviceTemplate();
	$m = new Manufacturer();
	$ct = new CDUTemplate();
	
	$tList = $t->GetTemplateShareList();
	
	$c = curl_init('https://repository.opendcim.org/api/template');
	
	curl_setopt( $c, CURLOPT_CONNECTTIMEOUT, 30 );
	curl_setopt( $c, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
	curl_setopt( $c, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $c, CURLOPT_COOKIEFILE, "/tmp/repocookies.txt" );
	curl_setopt( $c, CURLOPT_COOKIEJAR, "/tmp/repocookies.txt" );
	curl_setopt( $c, CURLOPT_CUSTOMREQUEST, "PUT" );
	curl_setopt( $c, CURLOPT_FOLLOWLOCATION, 1 );
	curl_setopt( $c, CURLOPT_HTTPHEADER, array( "UserID: " . $config->ParameterArray["APIUserID"], "APIKey: " . $config->ParameterArray["APIKey"], "Content-Type: application/json" ) );
	
	foreach ( $tList as $temp ) {
		if ( $temp->ManufacturerID != $m->ManufacturerID ) {
			$m->ManufacturerID = $temp->ManufacturerID;
			$m->GetManufacturerByID();
		}
		
		$temp->ManufacturerID = $m->GlobalID;
		
		$tp = new TemplatePorts();
		$tp->TemplateID = $temp->TemplateID;
		$tpList = $tp->getPorts();
		
		// Convert the base template object to an associative array for easier manipulation
		$postData["template"] = json_decode( json_encode($temp), true );
		$postData["templateports"] = array();
		foreach ( $tpList as $tport ) {
			array_push( $postData["templateports"], json_decode(json_encode($tport), true) );
		}
		
		$tpp = new TemplatePowerPorts();
		$tpp->TemplateID = $temp->TemplateID;
		$tppList = $tpp->getPorts();
		
		$postData["templatepowerports"] = array();
		foreach( $tppList as $pport ) { 
			array_push( $postData["templatepowerports"], json_decode( json_encode( $pport), true ) );
		}
		
		if ( $temp->DeviceType == "Chassis" ) {
			$sList = Slot::GetAll( $temp->TemplateID );
			
			$postData["slots"] = array();
			foreach( $sList as $s ) {
				array_push( $postData["slots"], json_decode(json_encode($s), true) );
			}
		}
		
		if ( $temp->DeviceType == "CDU" ) {
			$ct->TemplateID = $temp->TemplateID;
			$ct->GetTemplate();
			$postData["cdutemplate"] = json_decode( json_encode( $ct ), true );
		}
		
		if ( $temp->DeviceType == "Sensor" ) {
		}
		
		curl_setopt( $c, CURLOPT_POSTFIELDS, json_encode( $postData ) );
		
		$result = curl_exec( $c );
		$jr = json_decode( $result ) ;
		
		$postData = array();
		
		if ( $temp->FrontPictureFile != "" ) {
			$postData["front"] = curl_file_create( "pictures/" . $temp->FrontPictureFile );
		}
		
		if ( $temp->RearPictureFile != "" ) {
			$postData["rear"] = curl_file_create( "pictures/" . $temp->RearPictureFile );
		}
		
		if ( @$jr->errorcode == 200 && sizeof( $postData ) > 0 ) {
			$p = curl_init( 'https://repository.opendcim.org/api/template/addpictures/' . $jr->template->RequestID );
			curl_setopt( $p, CURLOPT_CONNECTTIMEOUT, 30 );
			curl_setopt( $p, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
			curl_setopt( $p, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $p, CURLOPT_POST, true );
			curl_setopt( $p, CURLOPT_COOKIEFILE, "/tmp/repocookies.txt" );
			curl_setopt( $p, CURLOPT_COOKIEJAR, "/tmp/repocookies.txt" );
			curl_setopt( $p, CURLOPT_FOLLOWLOCATION, 1 );
			curl_setopt( $p, CURLOPT_HTTPHEADER, array( "UserID: " . $config->ParameterArray["APIUserID"], "APIKey: " . $config->ParameterArray["APIKey"] ) );
			curl_setopt( $p, CURLOPT_POSTFIELDS, $postData );
			
			$result = curl_exec( $p );
			$pr = json_decode( $result );
		}
		
		if ( @$jr->errorcode == 200 && @$pr->errorcode == 200 ) {
			if ( sizeof( $tpList ) == 0 ) {
				// $temp->clearShareFlag();
			}
		}
	}
	
	curl_close( $c );
?>
