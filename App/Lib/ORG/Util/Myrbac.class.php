<?php

class Myrbac
{

    protected   function  getMd5Key(){
            return 'a0dfd9338cfb2c2e';
    }
    // 认证方法
      public function authenticate($map,$model='')
    {
        if(empty($model)) $model =  C('USER_AUTH_MODEL');
        //使用给定的Map进行认证
        return M($model)->where($map)->find();
    }
    protected function encode($data) {
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_256,'',MCRYPT_MODE_CBC,'');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td),MCRYPT_RAND);
        mcrypt_generic_init($td,$this->getMd5Key(),$iv);
        $encrypted = mcrypt_generic($td,$data);
        mcrypt_generic_deinit($td);
        return $iv . $encrypted;
    }
    protected function decode($data) {
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_256,'',MCRYPT_MODE_CBC,'');
        $iv = mb_substr($data,0,32,'latin1');
        mcrypt_generic_init($td,$this->getMd5Key(),$iv);
        $data = mb_substr($data,32,mb_strlen($data,'latin1'),'latin1');
        $data = mdecrypt_generic($td,$data);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return trim($data);
    }

    //public function
    public   function setUser($userData = array())
    {
        $tostring = $this->encode(serialize($userData));
        if (C('USER_AUTH_COOKIE'))
            return cookie(C('USER_AUTH_KEY'), $tostring);
        else
            return session(C('USER_AUTH_KEY'), $tostring);
    }
    public function isAdminStatus(){
        $admin=$this->getUser();
        return $admin[C('ADMIN_AUTH_KEY')];
    }
    /**
     * 获取保存在 session 中的用户数据
     *
     * @return array
     */
    public function getUser()
    {
        if (C('USER_AUTH_COOKIE'))
            $str = cookie(C('USER_AUTH_KEY'));
        else
            $str = session(C('USER_AUTH_KEY'))? session(C('USER_AUTH_KEY')) : null;
        $ok = $this->decode($str);
        return unserialize($ok);
    }

    /**
     * 从 session 中清除用户数据
     */
    public function clearUser()
    {
        if (C('USER_AUTH_COOKIE')) {
            cookie(null);
        } else {
            session(null);
        }
    }

    //用于检测用户权限的方法,并保存到Session中
    public function saveAccessList($user = null)
    {
        if (null === $user) $user =$this->getUser();
        // 如果使用普通权限模式，保存当前用户的访问权限列表
        // 对管理员开发所有权限
        if (C('USER_AUTH_TYPE') != 2 && !$this->isAdminStatus())
            $this->setSession('_ACCESS_LIST', $this->getAccessList($user[C('USER_KEY_ID')]));
        return;
    }

    // 读取模块所属的记录访问权限
    public   function getModuleAccessList($authId,$module) {
        // Db方式
        $db     =   Db::getInstance(C('RBAC_DB_DSN'));
        $table = array('role'=>C('RBAC_ROLE_TABLE'),'user'=>C('RBAC_USER_TABLE'),'access'=>C('RBAC_ACCESS_TABLE'));
        $sql = ' node.nodeid,node.actions,node.args,node.pid,node.title,node.`level`   FROM ' .
            $table['access'] . ' AS access  LEFT JOIN ' .
            $table['node'] . ' AS node ON(access.`nodeid`=node.nodeid)  LEFT JOIN ' .
            $table['role'] . ' AS role ON(role.roleid=access.roleid) ' .
            'WHERE   role.roleid=' . $authId . '  (AND role.hiden=0 AND role.`level`=2 AND node.hiden=0 )  AND '." node.actions='".$module."'";
        $access = $db->query($sql);

        return $access;
    }
    protected  function setSession($name,$val=''){
        $webSessionKey=$this->getMd5Key();
        $_SESSION[$webSessionKey][$name]=$val;
    }
    protected function getSession($name){
        $webSessionKey=$this->getMd5Key();
        return  $_SESSION[$webSessionKey][$name];
    }
    //权限认证的过滤器方法
      public function AccessDecision($appName = APP_NAME)
    {
        //检查是否需要认证
        if ($this->checkAccess()) {
            //存在认证识别号，则进行进一步的访问决策
            $accessGuid = md5($appName . MODULE_NAME . ACTION_NAME);

            $auth=$this->isAdminStatus();
            if (empty($auth)) {
                if (C('USER_AUTH_TYPE') == 2) {
                    //加强验证和即时验证模式 更加安全 后台权限修改可以即时生效
                    //通过数据库进行访问检查
                    $user=$this->getUser();
                    $accessList = $this->getAccessList($user[C('USER_KEY_ID')]);
                } else {
                    // 如果是管理员或者当前操作已经认证过，无需再次认证
                    if ($this->getSession[$accessGuid]) {
                        return true;
                    }
                    //登录验证模式，比较登录后保存的权限访问列表
                    $accessList = $this->getSession['_ACCESS_LIST'];
                }
                //判断是否为组件化模式，如果是，验证其全模块名
                $module = defined('P_MODULE_NAME') ? P_MODULE_NAME : MODULE_NAME;
                if (!isset($accessList[strtoupper($appName)][strtoupper($module)][strtoupper(ACTION_NAME)])) {
                    $this->setSession($accessGuid,false);
                    return false;
                } else {
                    $this->setSession($accessGuid, true);
                }
            } else {
                //管理员无需认证
                return true;
            }
        }
        return true;
    }

    //检查当前操作是否需要认证
    public function checkAccess()
    {
        //如果项目要求认证，并且当前模块需要认证，则进行权限认证
        if (C('USER_AUTH_ON')) {
            $_module = array();
            $_action = array();
            if ("" != C('REQUIRE_AUTH_MODULE')) {
                //需要认证的模块
                $_module['yes'] = explode(',', strtoupper(C('REQUIRE_AUTH_MODULE')));
            } else {
                //无需认证的模块
                $_module['no'] = explode(',', strtoupper(C('NOT_AUTH_MODULE')));
            }

            //检查当前模块是否需要认证
            if ((!empty($_module['no']) &&  !in_array(strtoupper(MODULE_NAME), $_module['no'])) || (!empty($_module['yes']) && in_array(strtoupper(MODULE_NAME), $_module['yes']))) {

                if ("" != C('REQUIRE_AUTH_ACTION')) {
                    //需要认证的操作
                    $_action['yes'] = explode(',', strtoupper(C('REQUIRE_AUTH_ACTION')));
                } else {
                    //无需认证的操作
                    $_action['no'] = explode(',', strtoupper(C('NOT_AUTH_ACTION')));
                }


                //检查当前操作是否需要认证
                if ((!empty($_action['no']) && !in_array(strtoupper(ACTION_NAME), $_action['no'])) || (!empty($_action['yes']) && in_array(strtoupper(ACTION_NAME), $_action['yes']))) {
                    return true;
                } else {
                    return false;
                }
            } else {

                return false;
            }
        }
        return false;
    }

    /**
     * +----------------------------------------------------------
     * 取得当前认证号的所有权限列表
     * +----------------------------------------------------------
     * @param integer $authId 用户ID
     * +----------------------------------------------------------
     * @access public
     * +----------------------------------------------------------
     */
      public function getAccessList($uid='')
    {
        // Db方式权限数据
        if(empty($uid)){return '';}
        $db = Db::getInstance(C('RBAC_DB_DSN'));
        $table = array('role' => C('RBAC_ROLE_TABLE'), 'user' => C('RBAC_USER_TABLE'), 'access' => C('RBAC_ACCESS_TABLE'), 'node' => C('RBAC_NODE_TABLE'));
        $sql = 'SELECT  node.nodeid,node.actions,node.args,node.pid,node.`level`  FROM ' .
        $table['access'] . ' AS access  LEFT JOIN ' .
        $table['node'] . ' AS node ON(access.`nodeid`=node.nodeid)  LEFT JOIN ' .
        $table['role'] . ' AS role ON(role.roleid=access.roleid)  LEFT JOIN ' .
        $table['user'].'   AS users ON(users.roleid=role.roleid) '.
        'WHERE   users.uid=' . $uid . '  AND role.hiden=0 AND role.`level`=2 AND node.hiden=0 ';

        $apps = $db->query($sql);
        $access = array();
        $action = $moduleNames = array();
        foreach ($apps as $key => $app) {
           /* if ($app['level'] == '0') {
                $appName = $app['name'];
            }*/
            if ($app['level'] == '1') {
                $moduleNames[strtoupper($app['actions'])] = strtoupper($app['actions']);
            }
            if ($app['level'] == '2') {
                $action[strtoupper($app['actions'])] = $app['nodeid'];
            }
        }
        $access[APP_NAME][strtoupper($moduleNames)] = array_change_key_case($action, CASE_UPPER);
        return $access;
    }


}