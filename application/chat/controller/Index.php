<?php
namespace app\chat\controller;
use think\Db;
class index extends Common
{
    public function index()
    {
        $id = session('id');
        $name = session('name');
        $friends = Db::query("
            select 
            max(m.id) id,max(m.created_at) created_at, c.id chat_id, c.guid
            from customer_msg as m 
            inner join 
            customer_chat as c on m.chat_id = c.id 
            where c.cs_id = '{$id}' and m.type = 0 
            group by m.chat_id
            ");
        
        foreach($friends as &$rs){
            $msg = Db::table('customer_msg')
                   ->where('id',$rs['id'])
                   ->find();
            $rs['friendid']=$rs['guid'];
            $rs['nickname']=$rs['guid'];
            $rs['summary']=$msg['content'];
            $rs['new']=$msg['is_read']==0?1:0;
            if($msg['is_read'] == 0){
                $rs['classname']='animated infinite flash';
            }else{
                $rs['classname']='';
            }
        }
        array_multisort(array_column($friends,'new'),SORT_DESC,array_column($friends,'created_at'),SORT_DESC,$friends);
        $this->assign('name',$name);
        $this->assign('friends',$friends);
        return $this->fetch();
    }
}