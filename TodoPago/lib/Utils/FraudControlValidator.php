<?php

namespace TodoPago\Utils;

require_once(dirname(__FILE__)."/../Exception/FraudControlException.php");

class FraudControlValidator {
	
	protected $configfile = "/validation.json";
	protected $config;

	private $data = array();
	private $result = array();

	private $mailregex = "/^[A-Za-z0-9](([\.]?[a-zA-Z0-9_\-]+)*)@([A-Za-z0-9]+)(([\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z])+$/";
	private $ipregex = "/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/";
	private $yesnoregex = "/^[SsNn]$/";
	private $letterregex = "/^[A-Za-z]{1}$/";

	private static $csitcant;

	private function findState() {
		$postalcode = array(
			"A" => 4400,
			"B" => 1900,
			"C" => 1000,
			"D" => 5700,
			"E" => 3100,
			"F" => 5300,
			"G" => 4200,
			"H" => 3500,
			"J" => 5400,
			"K" => 4700,
			"L" => 6300,
			"M" => 5500,
			"N" => 3300,
			"P" => 3600,
			"Q" => 8300,
			"R" => 8500,
			"S" => 3000,
			"T" => 4001,
			"U" => 9103,
			"V" => 9410,
			"W" => 3400,
			"X" => 5000,
			"Y" => 4600,
			"Z" => 9400
 		);

		$state = $this->data["CSBTSTATE"];
		if(in_array($state,array_keys($postalcode))) {
			return $postalcode[$state];	
		}
		return $postalcode["C"];
		
	}

	protected function notEmpty($field, $message, $default = null) {
		if(empty($field)) {
			if($default == null) {
				throw new \TodoPago\Exception\FraudControlException($message);	
			} else if($default == "random") {
				return rand(1,100);
			} else if($default == "findState") {
				return $this->findState();
			}
			return $default;
		}
		return $field;
	}

	protected function clean($field) {
		$field = preg_replace('![^'.preg_quote('-').'a-zA-Z0-_9@.\s]+!', '', $field);
		$field = str_replace(array("<",">","="), "", $field);
		return $field;
	}

	protected function truncate($field, $params) {
		return substr($field, 0, $params[0]);
	}

	protected function hardcode($field, $params) {
		return $params[0];
	}

	protected function regex($field, $params, $message, $default = null) {
		if(preg_match($this->{$params[0]},$field)) {
			return $field;
		}
		if($default != null)
			return $default;
		throw new \TodoPago\Exception\FraudControlException($message);	
	}

	protected function upper($field) {
		return strtoupper($field);
	}

	protected function phone($number) {
        $number = str_replace(array(" ","(",")","-","+"),"",$number);
        
        if(substr($number,0,2)=="54") return $number;
        
        if(substr($number,0,2)=="15"){
            $number = substr($number,2,strlen($number));
        }
        if(strlen($number)==8) return "5411".$number;
        
        if(substr($number,0,1)=="0") return "54".substr($number,1,strlen($number));
        return "54".$number;
	}

	private function getCsitCant() {
		$desc = $this->data["CSITPRODUCTDESCRIPTION"];
		if(strlen($desc) > 254) {
			$descs = explode("#", $desc);
			$cant = count($descs);
			if((254/$cant) < 19) {
				foreach($descs as $d) {
					$descs[$i] = $this->truncate($d, array(254/19));
				}		
				$aux = "";			
				$i = 1;
				$c = 1;
				foreach($descs as $d) {
					$aux = $d."#";
					if(strlen($aux) > 254) {
						$c = $i;
					}
					$i++;
				}
				return $c;
			} else {
				foreach($descs as $i => $d) {
					$descs[$i] = $this->truncate($d, array(254/$cant));
				}
				return count($descs);
			}
		}
		$descs = explode("#", $desc);
		$cant = count($descs);
		return $cant;
	}

	protected function csitFormat($field, $number = false) {
		$cant = self::$csitcant;

		$limit = (int)254/$cant ;
		$arr = explode("#",$field);
		$res = array();
		if(count($arr) > $cant) {
			for($i = 0; $i < $cant; $i++) {
				$arr[$i] = $this->clean($res[$i]);
				if($number) {
					$res[$i] = $this->amount($arr[$i]);
				} else {
					$res[$i] = $this->truncate($arr[$i],array(19));	
				}
			}
		} else {
			foreach($arr as $key => $item) {
				$item = $this->clean($item);
				if($number) {
					$res[$key] = $this->amount($item);
				} else {
					$res[$key] = $this->truncate($item,array($limit));	
				}
			}
		}
		$field = implode("#", $res);
		return $field;
	}

	protected function amount($field) {
		return number_format($field, 2, ".","");
	}

	public function __construct($data) {
		$this->data = $data;
		$this->config = json_decode(file_get_contents(dirname(__FILE__).$this->configfile), true);
	}

	public function format() {
		foreach($this->config as $config) {
			if(isset($this->data[$config["field"]])) {
				$field = $this->data[$config["field"]];	
			} else {
				$field = "";
			}

			if(!isset($config["format"])) {
				$this->result[$config["field"]] = $field;
				continue;
			}

			$validation = $config["format"];

			foreach($validation as $val) {
				switch($val["function"]) {
					case "clean":
						$this->result[$config["field"]] = $this->clean($field);
					break;
					case "truncate":
						$this->result[$config["field"]] = $this->truncate($field,$val["params"]);
					break;					
					case "hardcode":
						$this->result[$config["field"]] = $this->hardcode($field,$val["params"]);
					break;
					case "upper":
						$this->result[$config["field"]] = $this->upper($field);
					break;
					case "phone":
						$this->result[$config["field"]] = $this->phone($field);
					break;
					case "amount":
						$this->result[$config["field"]] = $this->amount($field);
					break;
					case "csitFormat":
						$this->result[$config["field"]] = $this->csitFormat($field,$val["params"][0]);
					break;
					default:
						throw new \TodoPago\Exception\FraudControlException("format not implemented");	
					break;
				}
				$field = $this->result[$config["field"]];
			}
			unset($this->data[$config["field"]]);
		}
		if(count($this->data) > 0) {
			foreach($this->data as $key => $data) {
				$this->result[$key] = $this->clean($data);
			}
		}
	}

	public function validate($data) {
		foreach($this->config as $config) {
			if(isset($data[$config["field"]])) {
				$field = $data[$config["field"]];
			} else {
				$field = "";
			}
			
			if(!isset($config["validate"])) {
				$this->data[$config["field"]] = $field;
				continue;
			}

			$validation = $config["validate"];
			foreach($validation as $val) {
				switch($val["function"]) {
					case "notEmpty":
						if(isset($val["message"])) {
							$this->data[$config["field"]] = $this->notEmpty($field, $val["message"]);
						} else {
							$this->data[$config["field"]] = $this->notEmpty($field, "", $val["default"]);
						}
					break;
					case "regex":
						if(isset($val["message"])) {
							$this->data[$config["field"]] = $this->regex($field, $val["params"], $val["message"]);
						} else {
							$this->data[$config["field"]] = $this->regex($field, $val["params"], "", $val["default"]);
						}
					break;
					default:
						throw new \TodoPago\Exception\FraudControlException("validation not implemented");	
					break;
				}
				$field = $this->data[$config["field"]];
			}
		}
	}

	public function execute() {
		self::$csitcant = $this->getCsitCant();
		$this->validate($this->data);
		$this->format();
		$this->validate($this->result);
		return $this->result;
	}

}
