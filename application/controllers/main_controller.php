<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main_controller extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('main_model');
	}

	public function index()
	{
		$this->load->view('main_view');
	}
 
	/* Получаем ID игрока
	------------------------------------------------- */

	public function getId()
	{
		// GET-запрос
		$nickname = $this->input->get('acc_id');
		// Длина строки
		$nick_len = strlen($nickname);
		// Возвращаемое значение
		$result = $this->main_model->getPlayerId($nickname);

		if(is_numeric($nickname))
			$var = $nick_len == 17 ? $nickname : $result;
		else 
			$var = $result;


		print_r("Your ID32 = " . $var);

		// Получаем ID игроков с матчей
		//$players = $this->main_model->getPlayersIdFromMatches($var);
		$matches_count = $this->main_model->getCountMatches($var);

		echo "<br> Count of games: " . $matches_count . "<br><br>";

		//$p_id = $this->main_model->getDataFromMatches($var);
		//$j = 1;
		//foreach (array_unique($p_id) as $key) {
		//	echo "<br>" . $j++ . " = " . $key;
		//}
		$arr = array();

		// Массив топовых игроков
		$tops = $this->main_model->proId();
		// Список ID игроков с матчей
		//$arr = $this->main_model->getDataFromMatches($var, $tops, array());
		// Преобразуем двумерный массив в одномерный
		//$players_id = call_user_func_array('array_merge', $arr);
		$i = 0;
		
		//print_r($pro_id);
		//$result = array_intersect($players_id, $tops);
		
		//foreach ($result as $val) {
		//	echo $val . "<br>";
		//}

		$mas = array(
			0 => array(
				'p_id' => '54785786758',
				'm_kids' => array(
					0 => '453783678',
					1 => '453783678',
					2 => '453783678'
				)
			),
			1 => array(
				'p_id' => '54785786758',
				'm_kids' => array(
					0 => '453783678',
					1 => '453783678',
					2 => '453783678'
				)
			)
		);

		function print_tree($tree, $level = 0) {
		     foreach($tree AS $name => $node) {
		         if(
		               is_scalar($node) OR
		               (
		                   is_object($node) AND
		                   method_exists($node, '__toString')
		               )
		           ) {
		             echo str_repeat('-', $level).$name.': '.$node . "<br>";
		         }
		         else if(
		                   is_array($node) OR
		                   (
		                       is_object($node) AND
		                       $node InstanceOf Traversable
		                   )
		                ) {
		             echo str_repeat('-', $level).$name."<br>";
		             print_tree($node, $level+1);
		         }
		     }
		 }

		//print_tree($mas);

		 $this->main_model->getDataFromMatches($var, $tops, array());


		//}
		

		//foreach ($arr as $key => $value) {
		//	echo "<br> [" . $var++ . "] = " . $value;
		//}
	}

	/* Получаем ID игроков, участвующих в матчах
	------------------------------------------------- */
}

