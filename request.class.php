<?php

class Request {

	private $source;
	private $format;
    
	public function __construct() {
		$this->source = 'http://'.YL_ADDRESS.'/';
		$this->format = '?f=j'; // JSON
	}    

    /**
     * Curl
     */
     private function curl($function) {
        $ch = curl_init($this->source.$function);
        
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $output = curl_exec($ch);       
        
        curl_close($ch);

		return $output;
    }
       

    /**
     * Get live data
     */
	public function getLiveData() {
		return $this->curl('a'.$this->format);
	}

    /**
     * Get historical data
     */
	public function getHistoricalData($type, $number) {
		return $this->curl('V?'.$type.'='.$number.$this->format);
	}    
	
    /**
     * Get last day
     */
	public function getLastDay() {
		$part1 = json_decode($this->getHistoricalData('w',1), true);
		$part2 = json_decode($this->getHistoricalData('w',2), true);
		$part3 = json_decode($this->getHistoricalData('w',3), true);
		
		$values = array_merge($part3['val'], $part2['val'], $part1['val']);
		
		foreach($values as $k => $v){
			if($v == NULL){
				unset($values[$k]);
			}
			elseif($v == '*')
			{
				$values[$k] =' 0';
			}
		}
		$val = implode('","', $values);
		
		$data = '{"un": "'. $part3['un'] .'","tm": "'. $part3['tm'] .'", "dt": '. $part3['dt'] .', "val": ["'. $val .'"]}'; 

		return $data;
	} 	
	
    /**
     * Get last hour
     */
	public function getLastHour() {
		$part1 = json_decode($this->getHistoricalData('h',1), true);
		$part2 = json_decode($this->getHistoricalData('h',2), true);
		
		$values = array_merge($part2['val'], $part1['val']);
		
		foreach($values as $k => $v){
			if($v == NULL){
				unset($values[$k]);
			}
			elseif($v == '*')
			{
				$values[$k] = '0';
			}
		}
		$val = implode('","', $values);
		
		$data = '{"un": "'. $part2['un'] .'","tm": "'. $part2['tm'] .'", "dt": '. $part2['dt'] .', "val": ["'. $val .'"]}'; 

		return $data;
	} 	
	
    /**
     * Get specific month
     */
	public function getSpecificMonth($month) {
		$json = json_decode($this->getHistoricalData('m',$month), true);
		
		$values = $json['val'];
		foreach($values as $k => $v){
			if($v == NULL){
				unset($values[$k]);
			}
			elseif($v == '*')
			{
				$values[$k] = '0';
			}
		}
		$val = implode('","', $values);
		
		$data = '{"un": "'. $json['un'] .'","tm": "'. $json['tm'] .'", "dt": '. $json['dt'] .', "val": ["'. $val .'"]}'; 

		return $data;
	} 	
}

?>
