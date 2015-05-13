<?php
include("NuTweet.php");
$nt=new NuTweet;
$nt->consumer_key="xxxxx";
$nt->consumer_secret="xxxxxx";
$nt->oauth_access_token="xxxx";
$nt->oauth_access_token_secret="xxxxxx";

// Update Status / Nge-Tweet
$nt->post(NuTweet::UPDATE_STATUS,["status"=>"Hello World..."];

// Timeline
$rows=$nt->get(NuTweet::HOME_TIMELINE,["count"=>100]);
$rows=json_decode($rows);
foreach($rows as $row){
	print_r($row);
}
// Followers
$rows=$nt->get(NuTweet::FOLLOWERS_LIST);
$rows=json_decode($rows->users);
foreach($rows as $row){
	print_r($row);
}

//Kirim Pesan
$nt->post(NuTweet::DIRECT_MESSAGE_NEW,["screen_name"=>"nubuntoe","text"=>"Hello..."]);
?>