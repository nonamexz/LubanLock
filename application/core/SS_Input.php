<?php
class SS_input extends CI_Input{
	
	var $method;
	
	function __construct(){
		parent::__construct();
		$this->method=$this->server('REQUEST_METHOD');
	}
	
	/**
	 * 继承post方法，处理post数组
	 * 现可如下访问：
	 * $this->input->post('submit/newcase')
	 */
	function post($index = NULL, $xss_clean = FALSE){

		if(is_null($index)){
			return parent::post($index, $xss_clean);
		
		}else{
			
			if(parent::post($index, $xss_clean)!==false){
				return parent::post($index, $xss_clean);
			}
			
			$index_array=explode('/',$index);
			
			$post=parent::post($index_array[0], $xss_clean);
			
			for($i=1;$i<count($index_array);$i++){
				if(isset($post[$index_array[$i]])){
					$post=$post[$index_array[$i]];
				}else{
					return false;
				}
				
			}
			
			return $post;
		}
	}
	
	function put(){
		
		if($this->server('REQUEST_METHOD')!=='PUT'){
			return false;
		}
		
		$data=file_get_contents('php://input');
		
		$headers=$this->request_headers();

		if(array_key_exists('Content-type', $headers)){
			if(
				strpos($headers['Content-type'],'application/x-www-form-urlencoded')===0
				|| strpos($headers['Content-type'],'multipart/form-data')===0){
				parse_str($data,$data);
			}

			if($headers['Content-type']==='application/json'){
				$data=json_decode($data,JSON_OBJECT_AS_ARRAY);
			}
		}

		return $data;

	}
	
	function delete(){
		
		if($this->server('REQUEST_METHOD')!=='PUT'){
			return false;
		}
		
		return true;
	}
	
	/**
	 * @param string 一个变量名或数组变量路径
	 * @return array 返回储存于$_SESSION的post()值和$this->input->post()值取并集的结果
	 * 对于交集键，取$_SESSION的post()中的值
	 */
	function sessionPost($index){
		$post=array();
		
		$session_post=post($index);
		if(is_array($session_post)){
			$post+=$session_post;
		}
		
		$http_post=$this->post($index);
		if(is_array($http_post)){
			$post+=$http_post;
		}
		
		return $post;
	}
	
	function _clean_input_keys($str){   
		$config = &get_config('config');   
		if( ! preg_match("/^[".$config['permitted_uri_chars']."]+$/i", rawurlencode($str))){   
		   exit('Disallowed Key Characters.');   
		}   
		return $str;   
	}
	
	function header($name=NULL){
		$headers=getallheaders();
		if(isset($name)){
			if(isset($headers[$name])){
				return $headers[$name];
			}else{
				return false;
			}
		}else{
			return $headers;
		}
	}
}
?>