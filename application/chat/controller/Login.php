<?php
namespace app\chat\controller;
use think\Controller;
use think\Session;
use think\Db;
use think\helper\hash\Md5;
class Login extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
    public function login()
    {
        $username = input('username');
        $password = input('password');
        $admin = Db::table('customer_service')->where('email','=',$username)->find();
        if($admin){
            if($admin['password'] === Md5(Md5($password))){
                //将登录id和名称存入session
                Session::set('id',$admin['id']);
                Session::set('name',$admin['name']);
                Db::table('customer_service')->where('id','=',$admin['id'])->update(['is_online'=>1]);
                return redirect('/');
            }else{
                return $this->error('账号或密码错误');
            }
        }else{
            return $this->error('用户不存在');
        }
    }
    public function loginOut()
    {
        $id = session('id');
        Db::table('customer_service')->where('id','=',$id)->update(['is_online'=>0,'updated_at'=>date('Y-m-d H:i:s')]);
        session(null);//退出清空session
        return redirect('index');
    }
}