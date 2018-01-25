<?php
namespace app\chat\controller;
use think\Db;
class index extends Common
{
    public function index()
    {
        $id = session('id');
        $name = session('name');
        $date = date('Y-m-d 00:00:00',strtotime('-3 day'));
        $friends = Db::query("
            select
        m.*, c.guid
        from customer_msg as m
        left join
        customer_chat as c on m.chat_id = c.id
        where c.cs_id = '{$id}' and m.created_at > '{$date}' 
        and m.id in (SELECT MAX(`id`) FROM `customer_msg` where type = 0  GROUP BY `chat_id`)
        order by id desc
            ");
        foreach($friends as &$rs){
            $rs['friendid']=$rs['guid'];
            $rs['nickname']=$rs['guid'];
            $rs['summary']=$rs['content'];
            $rs['chat_id']=$rs['chat_id'];
            $rs['created_at']=$rs['created_at'];
            $rs['new']=$rs['is_read']==0?1:0;
            if($rs['is_read'] == 0){
                $rs['classname']='animated infinite flash';
            }else{
                $rs['classname']='';
            }
        }
//         array_multisort(array_column($friends,'new'),SORT_DESC,array_column($friends,'created_at'),SORT_DESC,$friends);
        $this->assign('name',$name);
        $this->assign('friends',$friends);
        return $this->fetch();
    }
}