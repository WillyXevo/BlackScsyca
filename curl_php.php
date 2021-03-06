<?php
require('lib/simplehtmldom/simple_html_dom.php');


function convert_date($tgl=''){
	$ex = explode(" ", $tgl);
	return $ex[0].'-'.get_bulan($ex[1]).'-'.$ex[2];
}

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
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

	$url="https://sicyca.dinamika.ac.id/?login"; 
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
	curl_setopt($ch, CURLOPT_URL, 'https://sicyca.dinamika.ac.id/akademik');

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
	$url="https://sicyca.dinamika.ac.id/?login"; 
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
	curl_setopt($ch, CURLOPT_URL, 'https://sicyca.dinamika.ac.id/biodata');

	//execute the request
	$content = curl_exec($ch);

	$dom = new simple_html_dom(null, true, true, DEFAULT_TARGET_CHARSET, true, DEFAULT_BR_TEXT, DEFAULT_SPAN_TEXT);

	$html = $dom->load($content, true, true);

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

	$b = array(
					'nama' 		=> $dt[0], 
					'nim' 		=> $dt[1], 
					'email' 	=> $dt[2], 
					'progstudi' => $dt[3], 
					'jk' 		=> $dt[4], 
					'ttl'	 	=> $dt[5], 
					'agama' 	=> $dt[6], 
					);
	
	curl_setopt($ch, CURLOPT_URL, 'https://sicyca.dinamika.ac.id/akademik/krs');

	//execute the request
	$content = curl_exec($ch);

	$dom = new simple_html_dom(null, true, true, DEFAULT_TARGET_CHARSET, true, DEFAULT_BR_TEXT, DEFAULT_SPAN_TEXT);

	$html = $dom->load($content, true, true);
	$i=0;
	$krs = [];
	foreach($html->find('#tableView tr') as $element){
		//echo $element;
		$a = [];
		$j=0;
		foreach ($element->find('td') as $e) {
			//echo $el->plaintext.' | ';
			switch ($j) {
				case 0:
					$a['hari'] = $e->plaintext;
					break;
				case 1:
					$a['waktu'] = $e->plaintext;
					break;
				case 2:
					$a['matkul'] = $e->plaintext;
					break;
				case 4:
					$a['ruang'] = $e->plaintext;
					break;
				case 5:
					$a['sks'] = $e->plaintext;
					break;
				case 7:
					$a['nmin'] = $e->plaintext;
					break;
				case 8:
					$a['kehadiran'] = $e->plaintext;
					break;
			}
			$j++;
		}
		foreach ($element->find('a[style]') as $e) {
			$oc = get_string_between($e->getAttribute('onclick'), '(', ')'); 
			$oc = explode("'", $oc);
			$a['kelas'] = $oc[1];
			$a['kode'] = $oc[3];
		}
		array_push($krs, $a);

	}
	curl_close($ch);

	$ret = array('bio' => $b, 
				'krs'	=> $krs);

	return $ret;
}



function detail_matkul($pi, $kls, $mk){

	$url="https://sicyca.dinamika.ac.id/?login"; 
	$postinfo = $pi;
	$cookie_file_path = "cookie.txt";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_NOBODY, false);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
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

	curl_setopt($ch, CURLOPT_URL, 'https://sicyca.dinamika.ac.id/table-proxy/?t=matakuliah&kls='.$kls.'&mk='.$mk);
	
	$content = curl_exec($ch);
	$dom = new simple_html_dom(null, true, true, DEFAULT_TARGET_CHARSET, true, DEFAULT_BR_TEXT, DEFAULT_SPAN_TEXT);

	$html = $dom->load($content, true, true);

	$matkul= [];
	$i = 0;
	foreach($html->find('table.sicycatablemanual') as $element){
		$j = 1;
		foreach($element->find('tr') as $el){
			foreach($el->find('th') as $e){
				$matkul['kode'] = $e->plaintext;
			}
			foreach($el->find('td') as $e){
				switch ($j) {
					case 2:
						$matkul['dosen'] = $e->plaintext;
						
						break;
					case 4:
						$matkul['nmatkul'] = $e->plaintext;
						break;
					case 6:
						$matkul['sks'] = $e->plaintext;
						break;
				}
				$j++;
			} 
		}
		if($i==0){
			break;
		} 
		$i++;
	}
	
	$peserta = [];
	foreach($html->find('.scrollContent tr') as $element){
		$a = [];
		$i=0;
		foreach($element->find('td') as $el){
			if($i==0){
				$a['nim'] = $el->plaintext; 
			}else{
				$a['nama'] = $el->plaintext; 
			}
			$i++;
		}
		array_push($peserta, $a);
	}


	curl_setopt($ch, CURLOPT_URL, 'https://sicyca.dinamika.ac.id/table-proxy/?t=kehadiran&kls='.$kls.'&mk='.$mk);

	$content = curl_exec($ch);
	curl_close($ch);
	$dom = new simple_html_dom(null, true, true, DEFAULT_TARGET_CHARSET, true, DEFAULT_BR_TEXT, DEFAULT_SPAN_TEXT);

	$html = $dom->load($content, true, true);
	$kehadiran = [];
	$j=0;
	foreach($html->find('.sicycatablemanual tr') as $element){
		$a = [];
		$i=0;
		foreach($element->find('td') as $el){
			if($i==0){
				$a['tanggal'] = $el->plaintext; 
			}elseif($i==1){
				$a['jam'] = $el->plaintext; 
			}elseif($i==2){
				$a['kdosen'] = $el->plaintext; 
			}elseif($i==3){
				$a['kmhs'] = $el->plaintext; 
			}
			$i++;
		}
		if($j>0){
			array_push($kehadiran, $a);
		}
		$j++;
	}
	$krs = array(
				'matkul' => $matkul, 
				'peserta' => $peserta, 
				'kehadiran' => $kehadiran, 
				);

	return $krs; 
}
if(isset($_GET['login'])){
	$nim = htmlspecialchars($_GET['anim']);
	$pin = htmlspecialchars($_GET['pin']);

	$p_info = "nim=$nim&pin=$pin";
	$bio = cek_log($p_info);
	
	echo json_encode($bio); 
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


if(isset($_GET['detail_matkul'])){
	$nim = htmlspecialchars($_GET['anim']);
	$pin = htmlspecialchars($_GET['pin']);
	$kls = strtoupper(htmlspecialchars($_GET['kls']));
	$mk = htmlspecialchars($_GET['mk']);

	$p_info = "nim=$nim&pin=$pin";
	$krs = detail_matkul($p_info, $kls, $mk);
	echo json_encode($krs); 
}


if(isset($_GET['cek'])){
	$nim = htmlspecialchars($_GET['anim']);
	$pin = htmlspecialchars($_GET['pin']);

	$p_info = "nim=$nim&pin=$pin";
	$krs = cek($p_info);
	//echo $krs;
	echo json_encode($krs); 
}



function cek($pi){

	$url="https://sicyca.dinamika.ac.id/?login"; 
	$postinfo = $pi;
	$cookie_file_path = "cookie.txt";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_NOBODY, false);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
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
	curl_setopt($ch, CURLOPT_URL, 'https://sicyca.dinamika.ac.id/biodata');

	//execute the request
	$content = curl_exec($ch);


	$error = curl_error($ch);
	$info = curl_getinfo($ch);

	echo '<pre>';
	echo 'INFO : <br>';
	print_r($info);
	echo "<hr>";
	echo 'ERROR : <br>';
	echo $error;
	echo "<hr>";
	echo '</pre>';
	

	$dom = new simple_html_dom(null, true, true, DEFAULT_TARGET_CHARSET, true, DEFAULT_BR_TEXT, DEFAULT_SPAN_TEXT);

	$html = $dom->load($content, true, true);
	echo $html;
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

	$b = array(
					'nama' 		=> $dt[0], 
					'nim' 		=> $dt[1], 
					'email' 	=> $dt[2], 
					'progstudi' => $dt[3], 
					'jk' 		=> $dt[4], 
					'ttl'	 	=> $dt[5], 
					'agama' 	=> $dt[6], 
					);
	
	curl_setopt($ch, CURLOPT_URL, 'https://sicyca.dinamika.ac.id/akademik/krs');

	//execute the request
	$content = curl_exec($ch);

	$error = curl_error($ch);
	$info = curl_getinfo($ch);

	echo '<pre>';
	echo 'INFO : <br>';
	print_r($info);
	echo "<hr>";
	echo 'ERROR : <br>';
	echo $error;
	echo "<hr>";
	echo '</pre>';

	$dom = new simple_html_dom(null, true, true, DEFAULT_TARGET_CHARSET, true, DEFAULT_BR_TEXT, DEFAULT_SPAN_TEXT);

	$html = $dom->load($content, true, true);
	echo $html;
	$i=0;
	$krs = [];
	foreach($html->find('#tableView tr') as $element){
		echo $element;
		$a = [];
		$j=0;
		foreach ($element->find('td') as $e) {
			//echo $el->plaintext.' | ';
			switch ($j) {
				case 0:
					$a['hari'] = $e->plaintext;
					break;
				case 1:
					$a['waktu'] = $e->plaintext;
					break;
				case 2:
					$a['matkul'] = $e->plaintext;
					break;
				case 4:
					$a['ruang'] = $e->plaintext;
					break;
				case 5:
					$a['sks'] = $e->plaintext;
					break;
				case 7:
					$a['nmin'] = $e->plaintext;
					break;
				case 8:
					$a['kehadiran'] = $e->plaintext;
					break;
			}
			$j++;
		}
		foreach ($element->find('a[style]') as $e) {
			$oc = get_string_between($e->getAttribute('onclick'), '(', ')'); 
			$oc = explode("'", $oc);
			$a['kelas'] = $oc[1];
			$a['kode'] = $oc[3];
		}
		array_push($krs, $a);

	}
	curl_close($ch);

	$ret = array('bio' => $b, 
				'krs'	=> $krs);

	return $ret; 
}



if(isset($_GET['ujian'])){
	$nim = htmlspecialchars($_GET['anim']);
	$pin = htmlspecialchars($_GET['pin']);

	$p_info = "nim=$nim&pin=$pin";
	$ujian = jadwal_ujian($p_info);
	echo '<pre>';
	echo json_encode($ujian);
	echo '</pre>';
}

function jadwal_ujian($pi){

	$url="https://sicyca.dinamika.ac.id/?login"; 
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
	curl_setopt($ch, CURLOPT_URL, 'https://sicyca.dinamika.ac.id/akademik/jadwal-ujian/');

	//execute the request
	$content = curl_exec($ch);

	curl_close($ch);
	//echo file_get_contents($content);
	$dom = new simple_html_dom(null, true, true, DEFAULT_TARGET_CHARSET, true, DEFAULT_BR_TEXT, DEFAULT_SPAN_TEXT);

	$html = $dom->load($content, true, true);
	

	$jadwal = [];
	$i = 0;
	$co = 0;
			$ujn = [];
	foreach($html->find('div.content') as $div){
		if($co==2){
			$ncdiv=0;
			foreach($div->find('div.tabletitle') as $cdiv){
				

				if($ncdiv==0){
					$ujn['uts'] = array(
										'judul'=> $cdiv->plaintext,
										'jadwal' => get_jadwal_ujian($div, $cdiv) 
										);
				}else{
					$ujn['uas'] = array(
										'judul'=> $cdiv->plaintext,
										'jadwal' => get_jadwal_ujian($div, $cdiv) 
										);
				}
				$ncdiv++;
			}
			array_push($jadwal, $ujn);
		}
		$co++;
	}
	return $jadwal;
}

function get_jadwal_ujian($div, $cdiv){

	$next = $cdiv->next_sibling();
	$regexp='/\<table class\=\"sicycatable\">(.*?)<\/table\>/s';
	preg_match($regexp, $next, $ketemu);
	if(!empty($ketemu)){
		$jadwal = [];
		$i=0;
		foreach($div->find('table.sicycatable tr') as $element){
			if($element->plaintext!=''){
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
		}
		return $jadwal;
	}else{
		return null;
	}
}
?>
