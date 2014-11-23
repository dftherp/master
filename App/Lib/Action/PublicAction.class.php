<?php
class PublicAction extends CommonAction {


    // 用户登录页面
    public function login() {
            $this->display();
    }
    public function imgcode(){
      //  import("@.ORG.Util.Image");
        import('ORG/Util.Image');
        Image::buildImageVerify(4,1,'png',60,30);
    }


    // 用户登出
    public function logout() {
        if(isset($_SESSION[C('USER_AUTH_KEY')])) {
            unset($_SESSION[C('USER_AUTH_KEY')]);
            unset($_SESSION);
            session_destroy();
            $this->success('登出成功！',__URL__.'/login/');
        }else {
            $this->error('已经登出！');
        }
    }

    // 登录检测
    public function checkLogin() {
        if(empty($_POST['username'])) {
            $this->error('帐号错误！');
        }elseif (empty($_POST['password'])){
            $this->error('密码必须！');
        }elseif (empty($_POST['verifycode'])){
            $this->error('验证码必须！');
        }

        //生成认证条件
        if(session('verify') != md5($_POST['verifycode'])) {
            $this->error('验证码错误！');
        }
        $adminModel=D('Admin');
        $loginStatus= $adminModel->login($_POST['username'],$_POST['password'],$this->Myrbac);
        if($loginStatus){
            $url=U('Admin/main');
            redirect($url);
        }else{
            $this->error($adminModel->getError());
        }

    }

}