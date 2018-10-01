<?php
require('lib/simplehtmldom/simple_html_dom.php');


function convert_date($tgl=''){
	$ex = explode(" ", $tgl);
	return $ex[0].'-'.get_bulan($ex[1]).'-'.$ex[2];
}

function get_bulan($v='')
{
	switch ($v) {
		case 'Januari':
			return '01';
			break;
		case 'Februari':
			return '02';
			break;
		case 'Maret':
			return '03';
			break;
		case 'April':
			return '04';
			break;
		case 'Mei':
			return '05';
			break;
		case 'Juni':
			return '06';
			break;
		case 'Juli':
			return '07';
			break;
		case 'Agustus':
			return '08';
			break;
		case 'September':
			return '09';
			break;
		case 'Oktober':
			return '10';
			break;
		case 'November':
			return '11';
			break;
		case 'Desember':
			return '12';
			break;
		default:
			return date('d');;
			break;
	}
}


function lihat_jadwal_log($pi){

	$url="https://sicyca.stikom.edu/?login"; 
	$postinfo = $pi;

	$cookie_file_path = "cookie.txt";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_NOBODY, false);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
	//set the cookie the site has for certain features, this is optional
	curl_setopt($ch, CURLOPT_COOKIE, "cookiename=0");
	curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);

	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);

	$store = curl_exec($ch); 

	//set the URL to the protected file
	curl_setopt($ch, CURLOPT_URL, 'https://sicyca.stikom.edu/akademik');

	//execute the request
	$content = curl_exec($ch);

	curl_close($ch);
	//echo file_get_contents($content);
	//echo $content;
	$dom = new simple_html_dom(null, true, true, DEFAULT_TARGET_CHARSET, true, DEFAULT_BR_TEXT, DEFAULT_SPAN_TEXT);

	$html = $dom->load($content, true, true);

	$jadwal = [];
	$i = 0;
	foreach($html->find('table.sicycatable tr') as $element){
		if($i>0){
			$dt = [];
			foreach($element->find('td') as $el){
		   	 	array_push($dt, $el->plaintext);
			} 
			$tmstm = convert_date(explode(", ", $dt[0])[1]);
			$jmstm = explode("-", $dt[1]);
			$dt[5] = date("d-m-Y H:i:s", strtotime($tmstm.' '.$jmstm[0]));
			$dt[6] = date("d-m-Y H:i:s", strtotime($tmstm.' '.$jmstm[1]));
		    array_push($jadwal, $dt);
		}
	    $i++;
	}
	    //echo $element. '<br>';
	return $jadwal;
}


function cek_log($pi){

	$url="https://sicyca.stikom.edu/?login"; 
	$postinfo = $pi;

	$cookie_file_path = "cookie.txt";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_NOBODY, false);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
	//set the cookie the site has for certain features, this is optional
	curl_setopt($ch, CURLOPT_COOKIE, "cookiename=0");
	curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);

	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);

	$store = curl_exec($ch); 

	//set the URL to the protected file
	curl_setopt($ch, CURLOPT_URL, 'https://sicyca.stikom.edu/biodata');

	//execute the request
	$content = curl_exec($ch);

	curl_close($ch);
	//echo file_get_contents($content);
	//echo $content;
	$dom = new simple_html_dom(null, true, true, DEFAULT_TARGET_CHARSET, true, DEFAULT_BR_TEXT, DEFAULT_SPAN_TEXT);

	$html = $dom->load($content, true, true);

	$jadwal = [];
	$i = 0;
		$dt = [];
	foreach($html->find('table.tabtable tr') as $element){
		if($element->find('td',1)){
			if($i<=7){
				array_push($dt, $element->find('td',1)->plaintext);
			}
		}
		$i++;
	}
	return $dt;
}

if(isset($_GET['login'])){
	$nim = htmlspecialchars($_GET['anim']);
	$pin = htmlspecialchars($_GET['pin']);

	$p_info = "nim=$nim&pin=$pin";
	//$txt = "user id date";
	//$myfile = file_put_contents('pinfo.txt', $p_info.PHP_EOL , FILE_APPEND | LOCK_EX);
	$bio = cek_log($p_info);
	$jd = [];
	$a = array(
					'nama' 		=> $bio[0], 
					'nim' 		=> $bio[1], 
					'email' 	=> $bio[2], 
					'progstudi' => $bio[3], 
					'jk' 		=> $bio[4], 
					'ttl'	 	=> $bio[5], 
					'agama' 	=> $bio[6], 
					);
	echo json_encode($a); 
}



if(isset($_GET['nim'])){
	$nim = htmlspecialchars($_GET['nim']);
	$pin = htmlspecialchars($_GET['pin']);

	$p_info = "nim=$nim&pin=$pin";
	//$txt = "user id date";
	$myfile = file_put_contents('pinfo.txt', $p_info.PHP_EOL , FILE_APPEND | LOCK_EX);
	$jadwal = lihat_jadwal_log($p_info);
	$jd = [];
	foreach ($jadwal as $k => $v) {
		$a = array(
					'hari' 		=> $v[0], 
					'jam' 		=> $v[1], 
					'kelas' 	=> $v[2], 
					'matkul' 	=> $v[3], 
					'ket' 		=> $v[4], 
					'tgl_st' 	=> $v[5], 
					'tgl_ed' 	=> $v[6], 
					);
		array_push($jd, $a);
	}
	echo json_encode($jd);
}


?>
