<?php
/**
 * 上传类
 * 
 * @author Demon.zhang@samesamechina.com
 * 
 */ 
class upload{
	/**
	 * 上传文件信息数组
	 * 
	 * @var array
	 */
	private $_files;
	/**
	 * 上传路径
	 * 
	 * @var string
	 */
	private $_dir = '/usr/home/masterkong/www/';
	
	/**
	 * 上传文件大小,默认500k
	 * 
	 * @var int
	 */
	private $_fileSize = 51200000;
	
	/**
	 * 上传文件类型
	 * 
	 * @var array
	 */
	private $_fileType = array('img'=>array('.jpg','.png','.gif','.jpeg'),'flv'=>array('.flv','.mp4','.mp3','.avi','.amv','.wmv','.wav'));
	
	/**
	 * 文件验证成功标识
	 * 
	 * @var boolean
	 */
	private $_tag = false;
	
	/**
	 * 设置返回错误信息
	 * 
	 * @var array
	 */
	private $_errors = array('code'=>1,'msg'=>'文件上传成功');
	
	/**
	 * 构造函数，初始化数据
	 * 
	 * @param array $_FILES
	 * @param string $fileDir
	 * @param string $fileType
	 * @param int $fileSize
	 * 
	 * @return void
	 */
	public function __construct($_FILES,$fileDir,$fileSize=null,$fileType='img')
	{
		$this->_files = $_FILES;
		$this->_fileType = $this->_fileType[$fileType];
		$this->_dir = $this->_dir . $fileDir;
		!$fileSize || $this->_fileSize = $fileSize;		
		!$this->judgingFile() || $this->_tag = true;
	}
	
	/**
	 * 判断文件是否符合规则
	 * 
	 * @return boolean
	 */
	private function judgingFile()
	{
		foreach ($this->_files AS $key=>$value){
			$fileType = strtolower(substr($value['name'],strrpos($value['name'],".")));
			if(!in_array($fileType,$this->_fileType)){
				$this->_errors['code'] = 3;
				$this->_errors['msg'] = '文件' . $value['name'] . '类型错误，请上传' . implode($this->_fileType) . '类型文件';
				return false;
			}
			if($value['size']>$this->_fileSize){
				$this->_errors['code'] = 2;
				$this->_errors['msg'] = '文件' . $value['name'] . '大小超出了限制('.$this->_fileSize.')';
				return false;
			}
		}
		return true;
	}
	
	/**
	 * 上传文件并返回新的文件名
	 * 
	 * @return json
	 */
	public function uploadFile()
	{
		if(!$this->_tag)
			return json_encode($this->_errors);
			
		$this->_errors['code'] = 1;	
		$this->_errors['msg'] = array();
		foreach ($this->_files AS $key=>$value){
			if($value['error']==0) {
				if (is_uploaded_file($value['tmp_name'])) 	{
					$file_name = date("YmdHis") . rand(000,999) . substr($value['name'],strrpos($value['name'],"."));
					if (move_uploaded_file($value['tmp_name'], $this->_dir . '/' .$file_name)){						
						$this->_errors['msg'][] = "http://" . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"].dirname($_SERVER["SCRIPT_NAME"])."/upload/img/yimg/".$file_name;
					}else{
						$this->_errors['code'] = 6;
						$this->_errors['msg'] = '文件' . $value['name'] . '上传失败';
					
					}
				}else{
					$this->_errors['code'] = 5;
					$this->_errors['msg'] = '文件' . $value['name'] . '上传失败';
				
				}
			}else{
				$this->_errors['code'] = 4;
				$this->_errors['msg'] = '文件' . $value['name'] . '上传失败';
			
			}
		}
		return json_encode($this->_errors);
	}
	
}