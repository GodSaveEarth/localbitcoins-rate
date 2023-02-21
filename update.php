<?php

	$buy_rate = get_avg_rate("https://localbitcoins.net/buy-bitcoins-online/rub/.json", 5, 1);
	$sell_rate = get_avg_rate("https://localbitcoins.net/sell-bitcoins-online/CNY/national-bank-transfer/.json", 5, 1);
	$cross_rate = round($buy_rate/$sell_rate,2);
	
	if(!$buy_rate || !$sell_rate) die("Unable to get rates! Die.");

	// фикс до 0.9
	// $cross_rate = 10.4;
	$cent = intval(substr($cross_rate * 100, -1));
	if($cent <= 3)
		$cross_rate = $cross_rate - ($cent+1)/100;
	// echo "$cross_rate $cent ";
	// echo "$cross_rate ";
	// exit;

	// // echo $price;
	// echo "buy rate: $buy_rate rub ";
	// echo "sell rate: $sell_rate cny ";
	// echo "cross rate: $cross_rate ";

      //Set the Content Type
      header('Content-type: image/jpeg');

      // Create Image From Existing File
      $jpg_image = imagecreatefromjpeg('template.jpg');

      // Allocate A Color For The Text
      $white = imagecolorallocate($jpg_image, 255, 255, 255);

      // Set Path to Font File
      $font_path = 'NotoSans-Regular.ttf';

      // Set Text to Be Printed On Image
      $text = "Курс обмена на ".date("d.m.Y H:i");
      $text2 = "1 ¥ = $cross_rate Р";
      $text3 = "отдаете $cross_rate руб, получаете 1 юань";

      // Print Text On Image
      imagettftext($jpg_image, 25, 0, 180, 50, $white, $font_path, $text);
      imagettftext($jpg_image, 55, 0, 175, 130, $white, $font_path, $text2);
      imagettftext($jpg_image, 20, 0, 185, 170, $white, $font_path, $text3);

      // Send Image to Browserpath/
      $fp = fopen(dirname(__FILE__)."/rate2.jpg", "w+");
      // fwrite($fp,"test");
      // fclose($fp);

      // $path = dirname(__FILE__);
      // // unlink("{$path}/rate2.jpg");
      // $fp = fopen("{$path}/rate2.jpg", "w");
      imagejpeg($jpg_image, $fp);
      fclose($fp);
      // imagejpeg($jpg_image);

      // Clear Memory
      imagedestroy($jpg_image);
      exit("OK");

function get_avg_rate($url, $skip, $avg_num) {
	// курс продажи
	$json = file_get_contents($url);
	$json = json_decode($json);
	$ads = $json->data->ad_list;
	if(!$json || !$ads) return false;
	$price = 0; $count = 0;
	for($i=$skip; $i<$skip+$avg_num; $i++) {
		// print_r($ads[$i]->data);
		if(isset($ads[$i])) $price += $ads[$i]->data->temp_price;
		else break;
		$count++;
	}
	if(!$price) return false;
	return $price/$count;
}	