<?php

namespace Model;

class Train extends \Model {
  public static function types() {
    return array(
  	/*	3 => array(
  			'name' => 'Databases',
        'quest_group_id' => 6,
  			),
  		2 => array(
  			'name' => 'Field',
        'quest_group_id' => 7,
      ),*/
  		1 => array(
  			'name' => 'Decryption',
        'quest_group_id' => 8,
  			),
  		);
  }
    public static function process($train_string) {
        if ($train_string) {
        	$train = json_decode($train_string, true);
        } else $train = array();

        for ($i = 1; $i <= 5; $i++) {
        	if (!isset($train[$i]) || !isset($train[$i]['level'])) {
        		$train[$i] = array( 'level' => 1, 'exp' => 0 );
        	}

        	$train[$i]['exp_next'] = Train::experience($train[$i]['level'] + 1);
          $train[$i]['wait'] = false;
          if (isset($train[$i]['last'])) {
            $train[$i]['wait'] = max(0, $train[$i]['last'] - time() + 6 * 60 * 60 );
          }
        }
        return $train;
    }

    public static function experience($level) {
    	return $level * 5;
    }

    public static function add_experience(&$train, $exp) {
        if (!isset($train['exp_next'])) $train['exp_next'] = Train::experience($train['level'] + 1);

        $train['exp'] += $exp;
        while($train['exp'] >= $train['exp_next']) {
            $train['level'] += 1;
            $train['exp'] -= $train['exp_next'];
            $train['exp_next'] = Train::experience($train['level'] + 1);
        }
    }

    public static function save($user_id, $train) {
      \Hacker::update(array('train' => json_encode($train)), $user_id);
    }
}
