<?php
// 角色模块
class RoleAction extends CommonAction {
    public function index(){
        $adminModel=D('Admin');
        $this->getList($adminModel);
        $this->display();
    }
    public function section(){
        $roleModel=D('Role');
        $list=$this->getList($roleModel,'pid=0');
        foreach($list as $role){
           $roleList[$role['roleid']]= $roleModel->getRole($role['roleid']);
        }
        $this->assign('role',$roleList);
        $this->display();
    }

    public function role(){
        $roleModel=D('Role');
        $this->getList($roleModel);
        $this->display();
    }
    public function adminadd(){
        $schoolModel=D('Schooladdr');
        $this->getList($schoolModel,null,'*','orders',200);

        $this->display();
    }
    public function schoolList(){

        $schoolModel=D('Schooladdr');
        $this->getList($schoolModel,null,'*','orders');
        $city=array();
        include getCache('city',C('ERP_CACHE_PATH'));
        $this->assign('city',$city['city']);

        $this->display();
    }
    public function schooladd(){
        $db=D('Schooladdr');
        $info=array('areaid'=>'3101','id'=>0);
        if($_GET['id']){
            $id=$_GET['id'];
            $info= $db->where('id='.$id)->find() ;
        }
        $this->assign('info',$info);
        $this->display();
    }
    public function schoolCreate(){
        $row=$_POST['post'];
        $kid=$_POST['id'];
        $db=D('Schooladdr');
        if($kid){
            $db->where('id='.$kid)->save($row);
        }else{
            $row['created']=time();
            $db->add($row);
        }
        $this->returnJUIAjax('200',array('navTabId'=>$this->getNavTabId()));

    }
    public function schoolDelete(){
        $db=D('Schooladdr');
        $kids=$_POST['kids'];
        foreach($kids as $kid){
                 $db->where('id='.$kid)->delete();
        }
        $this->returnJUIAjax('200',array('navTabId'=>$this->getNavTabId('schoolList'),'callbackType'=>'forward'));
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