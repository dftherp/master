<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-3-22
 * Time: 上午10:20
 */
class SchooladdrModel extends Model{
    protected $tableName='school_addr';
    public function add2db($imgsrc=0){
        $row=$_POST['post'];
        print_r($row);
       // UploadImg

    }
	public function getlist($field='id,title,tj',$len=60){
		return $this->field($field)->order('id DESC')->limit($len)->select();
	}
}