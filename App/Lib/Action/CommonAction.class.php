<?php

class CommonAction extends Action {
    public $Myrbac;
    function _initialize() {
        import('@.ORG.Util.Myrbac');
        $this->Myrbac=new Myrbac();
        // 用户权限检查
        if (C('USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE')))) {
            if (! $this->Myrbac->AccessDecision()) {
                //检查认证识别号
                if (!$this->Myrbac->getUser()) {
                    //跳转到认证网关
                    redirect(PHP_FILE . C('USER_AUTH_GATEWAY'));
                }elseif (C('RBAC_ERROR_PAGE')) {
                    redirect(C('RBAC_ERROR_PAGE'));
                } else {
                    if (C('GUEST_AUTH_ON')) {
                        $this->assign('jumpUrl', PHP_FILE . C('USER_AUTH_GATEWAY'));
                    }
                    // 提示错误信息
                    $this->error(L('_VALID_ACCESS_'));
                }
            }
        }
    }

    public function menu(){
        $content = $this->fetch('Role/menu');
       // $msg->setInfo('content', $content)->setMessage(true, C('SUCCESS_MSG'));
        $this->ajaxReturn($content);
    }
    /**
      +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
      +----------------------------------------------------------
     * @access protected
      +----------------------------------------------------------
     * @param string $name 数据对象名称
      +----------------------------------------------------------
     * @return HashMap
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    protected function _search($name = '') {
        //生成查询条件
        if (empty($name)) {
            $name = $this->getActionName();
        }
        $name = $this->getActionName();
        $model = D($name);
        $map = array();
        foreach ($model->getDbFields() as $key => $val) {
            if (isset($_REQUEST [$val]) && $_REQUEST [$val] != '') {
                $map [$val] = $_REQUEST [$val];
            }
        }
        return $map;
    }

    /**
      +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
      +----------------------------------------------------------
     * @access protected
      +----------------------------------------------------------
     * @param Model $model 数据对象
     * @param HashMap $map 过滤条件
     * @param string $sortBy 排序
     * @param boolean $asc 是否正序
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    public  function getList($model, $where='', $fillds='*', $order='',$pageSize=20, $sortBy = 'desc') {
        //排序字段 默认为主键名
        if ($_POST ['orderField']) {
            $order = $_POST ['orderField'];
        }
        //排序方式默认按照倒序排列

        if ($_POST ['orderDirection']) {
            $sortBy = $_POST ['orderDirection'];
        }
        if(empty($order)){
            $order =$model->getPk();
        }
        //取得满足条件的记录数
        $count = $model->where($where)->count($model->getPk());

            import("@.ORG.Util.Page");

            $p = new Page($count, $pageSize);
            //分页查询数据
            $voList = $model->where($where)->order('`' . $order . '` ' . $sortBy)->limit($p->firstRow . ',' . $p->listRows)->field($fillds)->select();

            //模板赋值显示
            $this->assign('list', $voList);
            $this->assign('sortBy', $sortBy);
            $this->assign('order', $order);
            $this->assign('currentPage', $p->nowPage);
            $this->assign('totalCount', $count);
           $this->assign('pageisze', $pageSize);
             import("@.ORG.Util.Jui");
            $jui=new Jui();
        $this->assign('PagerForm', $jui->listPagerForm());
        $this->assign('Pagination', $jui->ListPagination($p));
        return $voList;

    }
    public function getNavTabId($action='',$model=''){
        if(empty($action)){
            $action=ACTION_NAME;
        }
        if(empty($model)){
            $model=MODULE_NAME;
        }
        return  $action . $model;
    }
    public function returnJUIAjax($result, $variables = null, $message = '', $callbackType = 'closeCurrent', $formMessage = null){
        import("@.ORG.Util.Jui");
        $jui=new Jui();
        $jui->setJuiResult($result, $variables, $message, $callbackType, $formMessage);
    }
     public function setSearchForm(){

     }
        public function setPageHeader(){

        }


    /**
      +----------------------------------------------------------
     * 默认恢复操作
     *
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws FcsException
      +----------------------------------------------------------
     */
    function resume() {
        //恢复指定记录
        $name = $this->getActionName();
        $model = D($name);
        $pk = $model->getPk();
        $id = $_GET [$pk];
        $condition = array($pk => array('in', $id));
        if (false !== $model->resume($condition)) {
            $this->success('状态恢复成功！',$this->getReturnUrl());
        } else {
            $this->error('状态恢复失败！');
        }
    }

    function saveSort() {
        $seqNoList = $_POST ['seqNoList'];
        if (!empty($seqNoList)) {
            //更新数据对象
            $name = $this->getActionName();
            $model = D($name);
            $col = explode(',', $seqNoList);
            //启动事务
            $model->startTrans();
            foreach ($col as $val) {
                $val = explode(':', $val);
                $model->id = $val [0];
                $model->sort = $val [1];
                $result = $model->save();
                if (!$result) {
                    break;
                }
            }
            //提交事务
            $model->commit();
            if ($result !== false) {
                //采用普通方式跳转刷新页面
                $this->success('更新成功');
            } else {
                $this->error($model->getError());
            }
        }
    }
}