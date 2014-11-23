<?php
// 角色模块
class RoleAction extends CommonAction {
    public function index(){
        $adminModel=D('Admin');
        $this->getList($adminModel);
        $this->display();
    }
    public function add(){
        $this->display();
    }
    public function schoolList(){
        $schoolModel=D('Schooladdr');
        $this->getList($schoolModel);
        $this->display();
    }
    public function cityList(){
        $adminModel=D('Admin');
        $this->getList($adminModel);$this->display();


    }
    public function _before_edit(){
       $Group = D('Role');
        //查找满足条件的列表数据
        $list     = $Group->field('id,name')->select();
        $this->assign('list',$list);

    }

    public function _before_add(){
       $Group = D('Role');
        //查找满足条件的列表数据
        $list     = $Group->field('id,name')->select();
        $this->assign('list',$list);

    }
}