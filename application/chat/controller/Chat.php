<?php
namespace app\chat\controller;
use think\Db;
class Chat extends Common
{
    public function index()
    {
        $guid = input('uid');
        $id = session('id');
        $action=input('action');
        if($action=='history'){
            #清除未读消息标识
            $chatid = Db::table('customer_chat')
            ->where('cs_id',$id)
            ->where('guid',$guid)
            ->value('id');
            Db::table('customer_msg')
            ->where('chat_id',$chatid)
            ->where('is_read',0)
            ->update(['is_read'=>1]);
            $T=Db::table('customer_msg')
                ->alias('m')
                ->field('m.*,c.cs_id,c.guid')
                ->join('customer_chat c','m.chat_id = c.id')
                ->where('c.cs_id',$id)
                ->where('c.guid',$guid)
                ->order('m.created_at DESC')
                ->select();
            $msg=array();
            foreach($T as $rs){
                $item=array(
                    'type'=>'user',
                    'msgid'=>$rs['id'],
                    'userid'=>$rs['account'],
                    'nickname'=>$rs['username'],
                    'style'=>'',
                    'message'=>$rs['content'],
                    'time'=>$rs['created_at'],
                );
                if($rs['type']==1){
                    $item['type']='me';
                }
                $msg[]=$item;
            }
            $A['history']=$msg;
            return $A;
        }elseif($action == 'send'){
            $message=input('message');
            $chatid = Db::table('customer_chat')
            ->where('cs_id',$id)
            ->where('guid',$guid)
            ->value('id');
            $user = Db::table('customer_service')
            ->where('id',$id)
            ->find();
            #发送方
            $msg=array(
            'chat_id'=>$chatid,
            'username'=>$user['name'],
            'account'=>$id,
            'is_read'=>0,//已读
            'type'=>1,
            'content'=>$message
            );
            $msgid1=Db::table('customer_msg')->insertGetId($msg);
            
            
            $A['print']=array(
                'type'=>'me',
                'msgid'=>$msgid1,
                'nickname'=>$user['name'],
                'style'=>'',
                'message'=>$message,
                'msgtype'=>'talk',
                'time'=>Date('Y-m-d H:i:s')
            );
            $data       = json_encode([
                'guid'       => (int)$guid,
                'type'       => 1,
                'feedbackid' => (int)$msgid1,
                'updatetime' => time(),
            ]);
            $serverData = post_curl_c('http://103.90.136.206:8081', 'GMCommand', ['Command' => 'FeedBack', 'Data' => $data, 'sign' => get_c_sign('Command=FeedBack&Data=' . $data)]);
            return $A;
        }elseif($action == 'touch'){
            $chatid = input('chat_id');
            $chatData = Db::table('customer_msg')
            ->where('chat_id',$chatid)
            ->where('is_read',0)
            ->where('type',0)
            ->order('created_at desc')
            ->select();
            if(!empty($chatData)){
                Db::table('customer_msg')
                ->where('chat_id',$chatid)
                ->where('is_read',0)
                ->where('type',0)
                ->update(['is_read'=>1]);
            }
            if(!empty($chatData)){
                foreach($chatData as &$cal){
                    $msg['type'] = 'user';
                    $msg['msgid'] = $cal['id'];
                    $msg['nickname'] = $cal['username'];
                    $msg['style'] = '';
                    $msg['message'] = $cal['content'];
                    $msg['msgtype'] = 'talk';
                    $msg['time'] = Date('Y-m-d H:i:s');
                    $A['print'][] = $msg;
                }
            }else{
                $A = 0;
            }
            return $A;
        }
        $qrselect = Db::table('quick_reply')->field('title,content')->where('status',0)->select();
        $this->assign('qr_select',$qrselect);
        $this->assign('name',$guid);
        return $this->fetch();
    }
}