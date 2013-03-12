<?php
/**
  * 徐福盛，forthxu.com
  * 微信公众平台类，修改自官方SDK
  */

//define your token
define("TOKEN", "dgsfhdgthrthgsrdsafsdafdasgv");//与平台约定token
$wechatObj = new wechatCallbackapiTest(TOKEN,true);//初始化类，token和是否为debug模式
//$wechatObj->valid();//注意！！！初始化和平台对接验证时开启，其余时间注释
$wechatObj->responseMsg();//使用时开启，互动回复在此函数类编写，也可放弃此函数将互动逻辑需要的地方使用$wechatObj->makeMsg($type='text',$data='',$flag=0)方法

class wechatCallbackapiTest
{
  var $time = '';
	var $token = '';
	var $debug = false;
	var $textTpl = '
					<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime><![CDATA[%s]]></CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>%d</FuncFlag>
					</xml>
					';
	var $musicTpl = '
					 <xml>
					 <ToUserName><![CDATA[%s]]></ToUserName>
					 <FromUserName><![CDATA[%s]]></FromUserName>
					 <CreateTime><![CDATA[%s]]></CreateTime>
					 <MsgType><![CDATA[music]]></MsgType>
					 <Music>
					 <Title><![CDATA[%s]]></Title>
					 <Description><![CDATA[%s]]></Description>
					 <MusicUrl><![CDATA[%s]]></MusicUrl>
					 <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
					 </Music>
					 <FuncFlag>%d</FuncFlag>
					 </xml>
					';
	var $newsTplWrap = '
					 <xml>
					 <ToUserName><![CDATA[%s]]></ToUserName>
					 <FromUserName><![CDATA[%s]]></FromUserName>
					 <CreateTime><![CDATA[%s]]></CreateTime>
					 <MsgType><![CDATA[news]]></MsgType>
					 <ArticleCount><![CDATA[%s]]></ArticleCount>
					 <Articles>%s
					 </Articles>
					 <FuncFlag>%d</FuncFlag>
					 </xml> 
 					';
	var $newsTplItem = '
					<item>
					 <Title><![CDATA[%s]]></Title> 
					 <Description><![CDATA[%s]]></Description>
					 <PicUrl><![CDATA[%s]]></PicUrl>
					 <Url><![CDATA[%s]]></Url>
 					</item>';
	
	/**
	* 类实例化，配置参数并获取平台传递的信息
	*/
	function __construct($token,$debug){
        $this->token = $token;
        $this->debug = $debug;
		$this->time = time();
		$this->getMsg();
	}
	
	/**
	* 初始化用于获取平台传递的信息
	*/
	private function getMsg(){
		$this->postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		if (!empty($this->postStr)){
                
              	$this->postObj = simplexml_load_string($this->postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
				
                $this->fromUsername = $this->postObj->FromUserName;
                $this->toUsername = $this->postObj->ToUserName;
				$this->msgType = $this->postObj->MsgType;
				$this->createTime = $this->postObj->CreateTime;

				if($this->postObj)
                {
              		switch($this->msgType){
						case 'text':
							$this->Content = trim($this->postObj->Content);
							$this->MsgId = $this->postObj->MsgId;
							break;
						case 'image':
							$this->PicUrl = $this->postObj->PicUrl;
							$this->MsgId = $this->postObj->MsgId;
							break;
						case 'location':
							$this->Location_X = $this->postObj->Location_X;
							$this->Location_Y = $this->postObj->Location_Y;
							$this->Scale = $this->postObj->Scale;
							$this->Label = $this->postObj->Label;
							$this->MsgId = $this->postObj->MsgId;
							break;
						case 'link':
							$this->Title = $this->postObj->Title;
							$this->Description = $this->postObj->Description;
							$this->Url = $this->postObj->Url;
							$this->MsgId = $this->postObj->MsgId;
							break;
						case 'event':
							$this->Event = $this->postObj->Event;
							$this->Latitude = $this->postObj->Latitude;
							$this->Longitude = $this->postObj->Longitude;
							$this->Precision = $this->postObj->Precision;
							break;
						default:
							$this->setLog('消息类型错误！'.$this->msgType);
					}
                }else{
                	$this->setLog('解析xml错误！');
                }

        }else{
        	$this->setLog('没能收到平台POST的数据！');
        	exit;
        }
	}

	/**
	* 按逻辑回复信息，主要互动回复需要编辑的地方
	*/
    public function responseMsg()
    {
		$keyword = trim($this->Content);
		if(!empty( $keyword ))
		{
			switch($keyword){//发送的是普通文本信息
				case 'Hello2BizUser':
					$MsgType = "text";
					$data['Content'] = "谢谢关注，回复help查询指令";
					break;
				case 'help':
					$MsgType = "text";
					$data['Content'] = "Welcome!\n可回复一下指令!\nhelp：出现帮助信息\nabout：关于我\nmusic：返回一首歌\nnews：返回两篇文章";
					break;
				case 'about':
					$MsgType = "text";
					$data['Content'] = "徐福盛编写的微信类！";
					break;
				case 'music':
					$MsgType = "music";
					$data=array('Title'=>'值得聆听的国外英文好曲','Description'=>'值得聆听的国外英文好曲值得聆听的国外英文好曲值得聆听的国外英文好曲值得聆听的国外英文好曲','MusicUrl'=>'http://qqmusic.djwma.com/mp3/%E5%80%BC%E5%BE%97%E8%81%86%E5%90%AC%E7%9A%84%E5%9B%BD%E5%A4%96%E8%8B%B1%E6%96%87%E5%A5%BD%E6%9B%B2.mp3','HQMusicUrl'=>'http://qqmusic.djwma.com/mp3/%E5%80%BC%E5%BE%97%E8%81%86%E5%90%AC%E7%9A%84%E5%9B%BD%E5%A4%96%E8%8B%B1%E6%96%87%E5%A5%BD%E6%9B%B2.mp3');
					break;
				case 'news':
					$MsgType = "news";
					$data=array(
						0=>array('Title'=>'有哪些值得推荐的学习网站？','Description'=>'综合类： TED: Ideas worth spreadinghttp://www.ted.co...','PicUrl'=>'http://xmit.sinaapp.com/public/main/dd5pic.jpg','Url'=>'http://xmit.sinaapp.com/detail-13.html'),
						1=>array('Title'=>'青春、励志、心情、人生、语录 、受伤、暖文章、治愈系、致梦 ','Description'=>'学会选择，懂得放弃，人生才能如鱼得水。 选择是一种量力而行的睿智与远见，放弃是一种顾全大局的果断和胆识。 ...','PicUrl'=>'http://wk.impress.sinaimg.cn/maxwidth.600/sto.kan.weibo.com/dd6581ebb979925a27d2578a01d1c6d8.jpg?width=580&height=387','Url'=>'http://xmit.sinaapp.com/detail-9.html')
					);
					break;
				default:
					$MsgType = "text";
					$data['Content'] = "您发的指令有错!回复help查询指令";
			}
			echo $this->makeMsg($MsgType,$data);
		}else{//发送的是图片、地理位置、链接消息、时间推送
			$this->setLog('非指令命令！');
		}
    }
	
	/**
	* 创建消息
	* 开发文档上说可回复的信息有文本、图文、语音、视频、音乐（这三个都指向music）和对收到的消息进行星标操作
	* $type = 
	* text $data=array('Content'=>'');
	* music $data=array('Title'=>'','Description'=>'','MusicUrl'=>'','HQMusicUrl'=>'');
	* news $data=array(0=>array('Title'=>'','Description'=>'','PicUrl'=>'','Url'=>''),1=>array('Title'=>'','Description'=>'','PicUrl'=>'','Url'=>''));
	* $flag=1做星号
	*/
	public function makeMsg($type='text',$data='',$flag=0){
		switch($type){
			case 'text':
				$result = sprintf($this->textTpl, $this->fromUsername, $this->toUsername, $this->time, $data['Content'] ,$flag);
				break;
			case 'music':
				$result = sprintf($this->musicTpl, $this->fromUsername, $this->toUsername, $this->time, $data['Title'], $data['Description'], $data['MusicUrl'], $data['HQMusicUrl'] ,$flag);
				break;
			case 'news':
				$news = $data;
				$items = '';
				foreach($news as $key=>$value){
					$items .= sprintf($this->newsTplItem, $value['Title'], $value['Description'], $value['PicUrl'], $value['Url']);
				}
				$result = sprintf($this->newsTplWrap, $this->fromUsername, $this->toUsername, $this->time, count($news), $items ,$flag);
				break; 
			default :
				$this->setLog('不能创建非指定信息！');
				$result = false;
		}
		return $result;
	}
	
	/**
	* 调试
	*/
	private function setLog($msg="错误",$stop=true){
		if($this->debug){
			$msgX['Content'] = $msg;
			echo $this->makeMsg('text',$msgX,1);
		}
		if($stop)exit;
	}
	
	/**
	* 平台对接验证
	*/
	public function valid()
    {
        $w_echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $w_echoStr;
        	exit;
        }
    }
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = $this->token;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>
