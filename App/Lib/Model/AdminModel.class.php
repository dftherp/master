<?php
// 用户模型
class AdminModel extends  Model {
    protected $tableName='erp_admin';
    public $_validate	=	array(
        //name,pwd,tel,uname,role,fla
        array('name','/^[a-z]\w{3,}$/i','帐号格式错误'),
        array('pwd','require','密码必须'),
        array('uname','require','姓名必须'),
        array('repassword','require','确认密码必须'),
        array('repassword','password','确认密码不一致',self::EXISTS_VALIDATE,'confirm'),
        array('name','','帐号已经存在',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
        );

    public $_auto		=	array(
        array('password','md5',self::MODEL_BOTH,'function'),
        array('create_time','time',self::MODEL_INSERT,'function'),
        array('update_time','time',self::MODEL_UPDATE,'function'),
        );
    public function  login($name,$password,Myrbac $rbac)
    {
        $where=array(
            'name'=>$name,
            'fla'=>0
        );
        $authInfo=$this->where($where)->find();

        if (empty($authInfo)) {
            $this->error='帐号不存在或已禁用！';
            return false;
        } else {

            if ($authInfo['pwd'] != md5($password)) {
                $this->error='密码错误！';
                return false;
            }
            $admin_field=C('ADMIN_AUTH_KEY');
            $userinfo=array(
               'uid'=>$authInfo['aid'],
               'name'=>$authInfo['name'],
               'uname'=>$authInfo['uname'],
                $admin_field=>false
            );

            if ($authInfo['name'] == 'admin') {
                $userinfo[$admin_field] = true;
            }
            $rbac->setUser($userinfo);
            $rbac->saveAccessList($userinfo);
            //保存登录信息
            $ip = get_client_ip();
            $time = time();
            $data = array();
            $data['aid'] = $authInfo['aid'];
            $data['last_login_time'] = $time;
            $data['login_count'] = array('exp', 'login_count+1');
            $data['last_login_ip'] = $ip;
            $this->save($data);

            return true;
        }
    }
}