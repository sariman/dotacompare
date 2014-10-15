<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main_model extends CI_Model {

	public function index()
	{
		$this->load->view('main_view');
	}

	/* Получаем ID игроков, участвующих в матче
	---------------------------------------------------- */
	public function getPlayerId($nickname)
	{
		$url = "http://steamcommunity.com/id/" . $nickname . "?xml=1";
		$dom = new DOMDocument();

		if($dom->load($url))
		{
			$errorNodes = $dom->getElementsByTagName('error'); 

			if($errorNodes->length == 0)
			{
				return $this->convert32($dom->getElementsByTagName('steamID64')->item(0)->nodeValue);	
			}
			else
				return 0;		
		}	
	}

	/* Получаем ID игроков, участвующих в матче
	---------------------------------------------------- */
	public function getDataFromMatches($myId32, $data, $top_players, $l_m_id = 0, $j = 0)
	{
		$this->load->library("Curl");

		$url = "https://api.steampowered.com/IDOTA2Match_570/GetMatchHistory/V001/?start_at_match_id=" . $l_m_id . "&key=21E317263AE5BBF6B4246783D0246848&account_id=" . $myId32;
		
		$json = $this->loadPageFromURL($url);
		$page = json_decode($json, true);
		$m_id = array();

		//$last_match_id = $page['result']['matches'][99]['match_id'];

		// Заходим в ветку матчей
		foreach ($page['result']['matches'] as $matches)
		{
			// Ищем последний матч
			foreach ($matches as $i => $match_id) {
				if($i == "match_id")
					$last_match_id = $match_id;
			}

			foreach ($matches["players"] as $players) 
			{
				foreach ($players as $i => $account_id) 
				{ 
					if($i == "account_id" && $account_id != $myId32 && $account_id != 4294967295)
					{
						$mas[$j]['p_id'] = $account_id;
						$mas[$j]['m_id'] = $matches["match_id"];
					}

					
				}


			}	
			
			$j++;


			/*

			// Записываем ID игроков
			foreach ($matches["players"] as $players) 
			{
				foreach ($players as $i => $account_id) 
				{ 
					if($i == "account_id" && $account_id != $myId32 && $account_id != 4294967295)
					{
						$arr[] = $account_id;
					}
				}
			}		

			*/
		}

		echo $last_match_id;
		
		print_tree($mas);

		

		/*

		// Заносим элементы в конечный массив
		$data[] = $arr;

		// Выходим из рекурсии, если
		if($l_m_id != $last_match_id)
			return $this->getDataFromMatches($myId32, $top_players, $data, $last_match_id);
		else
			return $data;	

		*/
	}

	/* Получаем кол-во матчей
	---------------------------------------------------- */

	function getCountMatches($player_id32)
	{
		$this->load->library("Curl");

		$url = "http://dotabuff.com/players/" . $player_id32 . "/matches";
		$page = $this->loadPageFromURL($url);

		$dom = new DOMDocument;

		libxml_use_internal_errors(true);

		if($dom->loadHTML($page))
		{
			libxml_clear_errors();

			$xPath = new DOMXPath($dom);
			$count = $xPath->query('//div[contains(concat(" ",normalize-space(@class)," ")," viewport ")]');
			$count1 = explode(" of" ,$count->item(0)->nodeValue);

			return $count1[1];
		}
	}



	/* Грузим страницу через URL
	---------------------------------------------------- */

	function loadPageFromURL($url)
	{
		$uagent = "Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14";

		$ch = curl_init( $url );

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
		curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
		curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
		curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // таймаут соединения
		curl_setopt($ch, CURLOPT_TIMEOUT, 120);        // таймаут ответа
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$content = curl_exec( $ch );
		$err     = curl_errno( $ch );
		$errmsg  = curl_error( $ch );
		$header  = curl_getinfo( $ch );
		curl_close( $ch );

		//$header['errno']   = $err;
		//$header['errmsg']  = $errmsg;
		$header['content'] = $content;

		// Возвращаем содержимое
		return $header['content'];
	}

	/* Convert ID64 to ID32
	---------------------------------------------------- */
	function convert32($myId64)
	{
		$srx = (substr($myId64, -1) % 2 == 0) ? 0 : 1;
      	$arx = bcsub($myId64, "76561197960265728");

      	$arx = bcsub($arx, $srx);
      	$arx = bcdiv($arx, 2);

      	$steam_id = sprintf("STEAM_0:%s:%s", $srx, $arx);

      	$msr = explode(':', $steam_id);
    	$msr2 = ($msr[2] * 2) + $msr[1];

    	return $msr2;
	}

	

	/* Convert ID64 to ID32
	---------------------------------------------------- */
 


	function proId()
	{
		$db = file_get_contents(base_url("forapp/players.json"));
		$db = json_decode($db, true);

		$arr = array();

		foreach ($db as $i => $value) {
			foreach ($value as $j => $val) {
				if($j == 'p_id')
				{
					$arr[] = $val;
					//echo $val . "<br>";
				}
			}

		}



		return $arr;






	}

}
