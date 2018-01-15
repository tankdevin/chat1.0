<?php
namespace app\chat\controller;
use think\Controller;
class Common extends Controller
{
    public function _initialize()
    {
        if(!session('id')){
            return $this->error('请登录..',url('Login/index'));
        }
    }
}