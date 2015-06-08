<?php
require 'vendor/autoload.php';
$app = new \Slim\Slim();
$app->response->headers->set('Content-Type', 'application/json; charset=utf-8');
$app->response->headers->set('Access-Control-Allow-Origin', '*');
$client = new GuzzleHttp\Client(['base_uri' => 'https://ac.khoslalabs.com/hackgate/hackathon/']);

$KEY='@Mrs123';
$dbuser='adminvwu78Cq';
$dbpass='J9amyZkYUvII';

$dbconn=mysql_connect($OPENSHIFT_MYSQL_DB_HOST,$dbuser,$dbpass,"",$OPENSHIFT_MYSQL_DB_PORT);
if(! $dbconn)
{
	die('Could not connect' .mysql_error());
}
mysql_selectdb('aadhaarmrs');

function query($sql) {
	GLOBAL $dbconn;
	$result=mysql_query($sql,$dbconn);
	if(! $result)
	{
		die('Could not get data' .mysql_error());
	}
    $rarray = array();
    while($row =mysql_fetch_assoc($result)) {
        $rarray[] = $row;
    }
	echo json_encode($rarray);
}

function insert_query($sql){
	GLOBAL $dbconn;
	$result=mysql_query($sql,$dbconn);
	if(! $result)
	{
		die('Could not insert data' .mysql_error());
	}
}

function return_json($sql){
	GLOBAL $dbconn;
	$rarray = array();
	$pid= mysql_query($sql,$dbconn);
	if(! $pid)
	{
		die('Could not return json' .mysql_error());
	}
	while($row =mysql_fetch_assoc($pid)) {
        $rarray[] = $row;
    }
	echo json_encode($rarray[0]);
}

function update_query($sql) {
	GLOBAL $dbconn;
	$result=mysql_query($sql,$dbconn);
	if(mysql_affected_rows($dbconn) >= 0)
	{
		echo "{\"success\":\"true\"}";
	}
	elseif (mysql_affected_rows($dbconn) == -1) {
		echo "{\"failure\":\"false\"}";
	}
    else{
    	echo("unknown error");
    }
}

$app->get("/getuser/:uid(/)",function($uid) use ($app,$dbconn) {
	query("SELECT `users`.* FROM `users` WHERE (`users`.`uid` =$uid)");
});

$app->group("/doctor/:uid",function() use($app,$dbconn) {
	$app->group("/vaccine",function() use($app,$dbconn) {
		$app->get("/:vid(/)",function($uid,$vid) use($app,$dbconn){
			//echo "Hi".$uid."vid".$vid."getvid";
			if(jwt_verify($app->request->params('token')))
			query("SELECT `vaccines`.* FROM `vaccines` WHERE ((`vaccines`.`vid` =$vid) AND (`vaccines`.`uid` =$uid))");
		});
		$app->get("/",function($uid) use($app,$dbconn){
			//echo "Hi".$uid."get";
			query("SELECT `vaccines`.* FROM `vaccines` WHERE (`vaccines`.`uid` =$uid)");
		});
		$app->post("/",function($uid) use($app,$dbconn){
			//echo "Hi".$uid."post";
			$vaccine=$app->request->params('vaccine');
			$date=$app->request->params('date');
			$place=$app->request->params('place');
			insert_query("INSERT INTO `vaccines`(`uid`, `vaccine`, `date`, `place`)
				VALUES ($uid,\"$vaccine\",\"$date\",\"$place\")");
			return_json("SELECT * FROM `vaccines` WHERE `vid`=LAST_INSERT_ID()");
		});
	});

	$app->group("/record",function() use($app,$dbconn) {
		$app->get("/:rid",function($uid,$rid) use($app,$dbconn) {
			//echo "Hi".$uid."rid".$rid."getrid";
			query("SELECT `records`.* FROM `records` WHERE ((`records`.`rid` =$rid) AND (`records`.`uid` =$uid))");
		});
		$app->get("/",function($uid) use($app,$dbconn) {
			//echo "Hi".$uid."get";
			query("SELECT `records`.* FROM `records` WHERE (`records`.`uid` =$uid)");
		});
		$app->post("/",function($uid) use($app,$dbconn) {				//not working
			//echo "Hi".$uid."post";
			$diagnosis=$app->request->params('diagnosis');
			$luid=$app->request->params('luid');
			$getdid=mysql_query("SELECT `doctors`.`did` FROM `doctors` WHERE (`doctors`.`uid` =$luid)");
			$date=$app->request->params('date');
			insert_query("INSERT INTO `records`(`uid`,`diagnosis`,`date`,`did`)
				VALUES ($uid,\"$diagnosis\",\"$date\",$did)");
			return_json("SELECT * FROM `records` WHERE `rid`=LAST_INSERT_ID()");
		});
	});

	$app->group("/allergy",function() use ($app,$dbconn) {
		$app->get("/:aid",function($uid,$aid) use($app,$dbconn){
			//echo "Hi".$uid."aid".$aid."getaid";
			query("SELECT `allergies`.* FROM `allergies` WHERE ((`allergies`.`aid` =$aid) AND (`allergies`.`uid` =$uid))");
		});
		$app->get("(/)",function($uid) use($app,$dbconn) {
			//echo "Hi".$uid."get";
			query("SELECT `allergies`.* FROM `allergies` WHERE (`allergies`.`uid` =$uid)");
		});
		$app->post("(/)",function($uid) use($app,$dbconn) {
			//echo "Hi".$uid."post";
			$allergen=$app->request->params('allergen');
			$reaction=$app->request->params('reaction');
			$severity=$app->request->params('severity');
			$comment=$app->request->params('comment');
			$actions=$app->request->params('actions');
			$lastupdated=$app->request->params('lastupdated');
			insert_query("INSERT INTO `allergies`(`uid`,`allergen`, `reaction`, `severity`, `comment`
				, `actions`, `lastupdated`) VALUES ($uid,\"$allergen\",\"$reaction\",\"$severity\",
				\"$comment\",\"$actions\", \"$lastupdated\")");
			return_json("SELECT * FROM `allergies` WHERE `aid`=LAST_INSERT_ID()");
		});
		/*$app->post("/edit/:aid",function($uid,$aid) use($app,$dbconn) {		//not working
			//echo "Hi".$uid."aid".$aid."putaid";
			$allergen=$app->request->params('allergen');
			$reaction=$app->request->params('reaction');
			$severity=$app->request->params('severity');
			$comment=$app->request->params('comment');
			$actions=$app->request->params('actions');
			$lastupdated=$app->request->params('lastupdated');
			update_query("UPDATE `allergies` SET `aid`=$aid,`uid`=$uid,`allergen`=\"$allergen\",`reaction`=\"$reaction\",
				`severity`=\"$severity\",`comment`=\"$comment\",`actions`=\"$actions\",
				`lastupdated`=\"$lastupdated\" WHERE 1");
			});*/
		$app->post("/delete/:aid",function($uid,$aid) use($app,$dbconn) {
			//echo "Hi".$uid."aid".$aid."deleteaid";
			update_query("DELETE FROM `allergies` WHERE `aid`=$aid");
		});
	});

	$app->group("/vital",function() use($app,$dbconn) {
		$app->get("/",function($uid) use($app,$dbconn) {
			//echo "Hi".$uid."get";
			query("SELECT `vitals`.* FROM `vitals` WHERE (`vitals`.`uid` =$uid)");
		});
		$app->post("/edit(/)",function($uid) use($app,$dbconn){
			//echo "Hi".$uid."put";
			$height=$app->request->params('height');
			$weight=$app->request->params('weight');
			$bmi=$app->request->params('bmi');
			$pulse=$app->request->params('pulse');
			$bp=$app->request->params('bp');
			update_query("INSERT INTO `vitals` (uid,height,weight,bmi,pulse,bp)
										VALUES ($uid,$height,$weight,$bmi,$pulse,$bp)
										ON DUPLICATE KEY
										UPDATE `height`=$height,`weight`=$weight,
										`bmi`=$bmi,`pulse`=$pulse,`bp`=$bp");
		});
	});
});

$app->group("/user/:uid",function() use ($app) {
	$app->group("/vaccine",function() use ($app) {
		$app->get("/:vid",function($uid,$vid) {
			//echo "Hi".$uid."vid".$vid."getvid";
			query("SELECT `vaccines`.* FROM `vaccines` WHERE ((`vaccines`.`vid` =$vid) AND (`vaccines`.`uid` =$uid))");
		});
		$app->get("/",function($uid) {
			//echo "Hi".$uid."get";
			query("SELECT `vaccines`.* FROM `vaccines` WHERE (`vaccines`.`uid` =$uid)");
		});
	});

	$app->group("/record",function() use ($app) {
		$app->get("/:rid",function($uid,$rid) {
			//echo "Hi".$uid."rid".$rid."getrid";
			query("SELECT `records`.* FROM `records` WHERE ((`records`.`rid` =$rid) AND (`records`.`uid` =$uid))");
		});
		$app->get("/",function($uid) {
			//echo "Hi".$uid."get";
			query("SELECT `records`.* FROM `records` WHERE (`records`.`uid` =$uid)");
		});
	});

	$app->group("/allergy",function() use ($app) {
		$app->get("/:aid",function($uid,$aid) {
			//echo "Hi".$uid."aid".$aid."getaid";
			query("SELECT `allergies`.* FROM `allergies` WHERE ((`allergies`.`aid` =$aid) AND (`allergies`.`uid` =$uid))");
		});
		$app->get("/",function($uid) {
			//echo "Hi".$uid."get";
			query("SELECT `allergies`.* FROM `allergies` WHERE (`allergies`.`uid` =$uid)");
		});
	});

	$app->group("/vital",function() use ($app) {
		$app->get("/:vitalid",function($uid,$vitalid) {
			//echo "Hi".$uid."vitalid".$vitalid."getvitalid";
			query("SELECT `vitals`.* FROM `vitals` WHERE ((`vitals`.`vitalid` =$vitalid) AND (`vitals`.`uid` =$uid))");
		});
		$app->get("/",function($uid) {
			//echo "Hi".$uid."get";
			query("SELECT `vitals`.* FROM `vitals` WHERE (`vitals`.`uid` =$uid)");
		});
	});
});

/*
$app->group("/assa",function() use ($app) {
	$app->group("/as",function() use ($app) {
		slimget
	});
});
*/

/*Megh api starts here*/

$app->post('/otp(/)', function () use ($app,$client) {
    $json=[
      "aadhaar-id"=>$app->request->params("aadhaar-id"),
      "device-id"=>"abfd",
      "certificate-type"=>"preprod",
      "channel"=>"EMAIL_AND_SMS",
      "location"=>[
        "type"=>"pincode",
        "pincode"=>"390008"
      ]
    ];
    $request=$client->post('otp',['json'=>$json]);
    echo $request->getBody();
});

$app->post('/kyc(/)',function () use ($app,$client,$dbconn) {
/*    $json=[
      "consent"=>"Y",
      "auth-capture-request"=>[
        "aadhaar-id"=>$app->request->params("aadhaar-id"),
        "device-id"=>"abfd",
        "certificate-type"=>"preprod",
        "location"=>[
          "type"=>"pincode",
          "pincode"=>"390008"
        ],
        "modality"=>"otp",
        "otp"=>$app->request->params("otp")
      ]
    ];
    $request=$client->post('kyc/raw',['json'=>$json]);
    $array=json_decode($request->getBody());*/
    $array=json_decode('{
  "kyc": {
    "aadhaar-id": "223334065242",
    "poi": {
      "name": "Parikh Jayesh",
      "dob": "18-06-1965",
      "gender": "M"
    },
    "poa": {
      "co": "S/O: Parikh Vallabhadas",
      "house": "B 109 JYOTI PARK SOCIETY",
      "lm": "BEHIND NAVRACHANA SCHOOL",
      "vtc": "Vemali",
      "subdist": "Vadodara",
      "dist": "Vadodara",
      "state": "Gujarat",
      "pc": "390008",
      "po": "Eme"
    },
    "local-data": {}
  },
  "aadhaar-id": "223334065242",
  "success": true,
  "aadhaar-reference-code": "a80d1e28458c4270b858427b94fa3745"
}',true);
    mysql_query("INSERT INTO `users`(`uid`,`name`,`dob`,`gender`,`dist`,`state`,`pc`)
      VALUES (
        {$array['kyc']['aadhaar-id']},
        \"{$array['kyc']['poi']['name']}\",
        DATE_FORMAT(STR_TO_DATE(\"{$array['kyc']['poi']['dob']}\", '%d-%m-%Y'), '%Y-%m-%d'),
        \"{$array['kyc']['poi']['gender']}\",
        \"{$array['kyc']['poa']['dist']}\",
        \"{$array['kyc']['poa']['state']}\",
        {$array['kyc']['poa']['pc']}
      )",
    $dbconn);
    //mysql_query("INSERT INTO `users`(`name`) VALUES(",$dbconn);

});

$app->post('/login/user(/)',function () use ($app,$dbconn,$client,$KEY) {
    $json=[
        "aadhaar-id"=>$app->request->params("aadhaar-id"),
        "device-id"=>"abfd",
        "certificate-type"=>"preprod",
        "location"=>[
          "type"=>"pincode",
          "pincode"=>"390008"
        ],
        "modality"=>"otp",
        "otp"=>$app->request->params("otp")
    ];
    $request=$client->post('auth/raw',['json'=>$json]);
    $array=(array) json_decode($request->getBody(true));

    if($array['success']!="true"){

    	$final=["success"=>false];
    }
    else {

    	$sql = "SELECT * FROM `users` WHERE `uid`={$app->request->params("aadhaar-id")}";
    	$query=mysql_query($sql);

	    if(mysql_num_rows($query)!=1)
	    	$final=["success"=>false];
		else{
			$token = array(
			    "uid" => $app->request->params('uid'),
			    "expires" => time()+(24*60*60)
			);

			$jwt = JWT::encode($token, $KEY);
			$row =mysql_fetch_assoc($query);
			$final=[
				"success"=>true,
				"token"=>$jwt,
				"uid"=>$row['uid'],
				"name"=>$row['name']
			];
		}
    }
    echo json_encode($final);
});

$app->post('/login/doctor(/)',function () use ($app,$dbconn,$client,$KEY) {
    $json=[
        "aadhaar-id"=>$app->request->params("aadhaar-id"),
        "device-id"=>"abfd",
        "certificate-type"=>"preprod",
        "location"=>[
          "type"=>"pincode",
          "pincode"=>"390008"
        ],
        "modality"=>"otp",
        "otp"=>$app->request->params("otp")
    ];
    $request=$client->post('auth/raw',['json'=>$json]);
    $array=(array) json_decode($request->getBody(true));

    if($array['success']!="true"){

    	$final=["success"=>false];
    }
    else {

    	$sql = "SELECT `users`.`uid`, `users`.`name`, `doctors`.`did`\n"
			    . "FROM `users`\n"
			    . " LEFT JOIN `mrs`.`doctors` ON `users`.`uid` = `doctors`.`uid`\n"
			    . "WHERE (`users`.`uid` ={$app->request->params("aadhaar-id")})\n"
			    . "\n"
			    . "";
    	$query=mysql_query($sql);

	    if(mysql_num_rows($query)!=1)
	    	$final=["success"=>false];
		else{
			$token = array(
			    "uid" => $app->request->params('uid'),
			    "expires" => time()+(24*60*60)
			);

			$jwt = JWT::encode($token, $KEY);
			$row =mysql_fetch_assoc($query);
			$final=[
				"success"=>true,
				"token"=>$jwt,
				"uid"=>$row['uid'],
				"name"=>$row['name'],
				"did"=>$row['did']
			];
		}
    }
    echo json_encode($final);
});

$app->post('/register/user(/)',function () use ($app,$dbconn,$client,$KEY) {
    $json=[
      "consent"=>"Y",
      "auth-capture-request"=>[
        "aadhaar-id"=>$app->request->params("aadhaar-id"),
        "device-id"=>"abfd",
        "certificate-type"=>"preprod",
        "location"=>[
          "type"=>"pincode",
          "pincode"=>"390008"
        ],
        "modality"=>"otp",
        "otp"=>$app->request->params("otp")
      ]
    ];
    $request=$client->post('kyc/raw',['json'=>$json]);
    $array=(array) json_decode($request->getBody(true));

    if($array['success']!="true"){

    	$final=["success"=>false];
    }
    else {
		mysql_query("INSERT INTO `users`(`uid`,`name`,`dob`,`gender`,`dist`,`state`,`pc`)
				      VALUES (
				        {$array['kyc']['aadhaar-id']},
				        \"{$array['kyc']['poi']['name']}\",
				        DATE_FORMAT(STR_TO_DATE(\"{$array['kyc']['poi']['dob']}\", '%d-%m-%Y'), '%Y-%m-%d'),
				        \"{$array['kyc']['poi']['gender']}\",
				        \"{$array['kyc']['poa']['dist']}\",
				        \"{$array['kyc']['poa']['state']}\",
				        {$array['kyc']['poa']['pc']}
				      )",
				    $dbconn);

		$sql = "SELECT `users`.`uid`, `users`.`name`, `doctors`.`did`\n"
			    . "FROM `users`\n"
			    . " LEFT JOIN `mrs`.`doctors` ON `users`.`uid` = `doctors`.`uid`\n"
			    . "WHERE (`users`.`uid` ={$app->request->params("aadhaar-id")})\n"
			    . "\n"
			    . "";
    	$query=mysql_query($sql);

	    if(mysql_num_rows($query)!=1)
	    	$final=["success"=>false];
		else{
			$token = array(
			    "uid" => $app->request->params('uid'),
			    "expires" => time()+(24*60*60)
			);

			$jwt = JWT::encode($token, $KEY);
			$row =mysql_fetch_assoc($query);
			$final=[
				"success"=>true,
				"token"=>$jwt,
				"uid"=>$row['uid'],
				"name"=>$row['name']
			];
		}
    }
    echo json_encode($final);
});

$app->post('/register/doctor(/)',function () use ($app,$dbconn,$client,$KEY) {
    $json=[
      "consent"=>"Y",
      "auth-capture-request"=>[
        "aadhaar-id"=>$app->request->params("aadhaar-id"),
        "device-id"=>"abfd",
        "certificate-type"=>"preprod",
        "location"=>[
          "type"=>"pincode",
          "pincode"=>"390008"
        ],
        "modality"=>"otp",
        "otp"=>$app->request->params("otp")
      ]
    ];
    $request=$client->post('kyc/raw',['json'=>$json]);
    $array=(array) json_decode($request->getBody(true));

    if($array['success']!="true"){

    	$final=["success"=>false];
    }
    else {
		mysql_query("INSERT INTO `users`(`uid`,`name`,`dob`,`gender`,`dist`,`state`,`pc`)
				      VALUES (
				        {$array['kyc']['aadhaar-id']},
				        \"{$array['kyc']['poi']['name']}\",
				        DATE_FORMAT(STR_TO_DATE(\"{$array['kyc']['poi']['dob']}\", '%d-%m-%Y'), '%Y-%m-%d'),
				        \"{$array['kyc']['poi']['gender']}\",
				        \"{$array['kyc']['poa']['dist']}\",
				        \"{$array['kyc']['poa']['state']}\",
				        {$array['kyc']['poa']['pc']}
				      )",
				    $dbconn);

		mysql_query("INSERT INTO `doctors`(`did`,`uid`)
				      VALUES (
				      	{$app->request->params("did")},
				        {$array['kyc']['aadhaar-id']}
				      )",
				    $dbconn);

    	$sql = "SELECT * FROM `users` WHERE `uid`={$app->request->params("aadhaar-id")}";
    	$query=mysql_query($sql);

	    if(mysql_num_rows($query)!=1)
	    	$final=["success"=>false];
		else{
			$token = array(
			    "uid" => $app->request->params('uid'),
			    "expires" => time()+(24*60*60)
			);

			$jwt = JWT::encode($token, $KEY);
			$row =mysql_fetch_assoc($query);
			$final=[
				"success"=>true,
				"token"=>$jwt,
				"uid"=>$row['uid'],
				"name"=>$row['name'],
				"did"=>$row['did']
			];
		}
    }
    echo json_encode($final);
});


function jwt_verify($token){
	GLOBAL $KEY;
	$decoded = JWT::decode($token, $KEY, array('HS256'));
	$decoded_array = (array) $decoded;
	if($decoded_array['expires']<time()) return false;
	return [
			"uid"=>$decoded_array['uid']
		];
}

$app->run();
?>
