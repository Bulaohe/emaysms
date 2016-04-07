<?php namespace Quan\Emay;
use Quan\Emay\Client;
class Emay {
	/**
	 * 网关地址
	 */

	var $gwUrl = 'http://sdk4report.eucp.b2m.cn:8080/sdk/SDKService';

	/**
	 * 序列号,请通过亿美销售人员获取
	 */
	var $serialNumber = '';

	/**
	 * 密码,请通过亿美销售人员获取
	 */
	var $password = '';

	/**
	 * 登录后所持有的SESSION KEY，即可通过login方法时创建
	 */
	// 	var $sessionKey = '123456';
	var $sessionKey = '';

	/**
	 * 连接超时时间，单位为秒
	 */
	var $connectTimeOut = 5;

	/**
	 * 远程信息读取超时时间，单位为秒
	 */
	var $readTimeOut = 10;
	var $proxyhost = false;
	var $proxyport = false;
	var $proxyusername = false;
	var $proxypassword = false;

	protected $client = null;

	public function __construct(){
		$this->serialNumber = config('emay_serial_number');
		$this->password = config('emay_password');
		$this->sessionKey = config('emay_session_key');
		
		$this->client = new Client(
				$this->gwUrl,
				$this->serialNumber,
				$this->password,
				$this->sessionKey,
				$this->proxyhost,
				$this->proxyport,
				$this->proxyusername,
				$this->proxypassword,
				$this->connectTimeOut,
				$this->readTimeOut
		);
		$this->client->setOutgoingEncoding("utf8");
	}

	/**
	 * 接口调用错误查看 用例
	 */
	public function chkError()	{
		$err = $this->client->getError();
		if ($err){
			/**
			 * 调用出错，可能是网络原因，接口版本原因 等非业务上错误的问题导致的错误
			 * 可在每个方法调用后查看，用于开发人员调试
			 */
			return $err;
		}
		return 'ok';
	}

	/**
	 * 登录 用例
	 */
	public function login(){
		/**
		 * 下面的操作是产生随机6位数 session key
		 * 注意: 如果要更换新的session key，则必须要求先成功执行 logout(注销操作)后才能更换
		 * 我们建议 sesson key不用常变
		 */
		//$sessionKey = $client->generateKey();
		//$statusCode = $client->login($sessionKey);

		$statusCode = $this->client->login();
		if ($statusCode!=null && $statusCode=="0") {
			//登录成功，并且做保存 $sessionKey 的操作，用于以后相关操作的使用
			$return['code'] = 1;
			$return['msg'] = "登录成功, session key:".$this->client->getSessionKey();
			$return['session_key'] = $this->client->getSessionKey();
		}else{
			//登录失败处理
			$return['code'] = 0;
			$return['msg'] = "登录失败";
			$return['statusCode'] = $statusCode;
		}

		return $return;
	}

	/**
	 * 注销登录 用例
	 */
	function logout(){
		$statusCode = $this->client->logout();
		return "处理状态码:".$statusCode;
	}

	/**
	 * 获取版本号 用例
	 */
	function getVersion()
	{
		return "版本:". $this->client->getVersion();

	}

	/**
	 * 取消短信转发 用例
	 */
	function cancelMOForward(){
		$statusCode = $this->client->cancelMOForward();
		return "处理状态码:".$statusCode;
	}

	/**
	 * 短信充值 用例
	 */
	function chargeUp()	{
		/**
		 * $cardId [充值卡卡号]
		 * $cardPass [密码]
		 *
		 * 请通过亿美销售人员获取 [充值卡卡号]长度为20内 [密码]长度为6
		 *
		 */

		$cardId = 'EMY01200810231542008';
		$cardPass = '123456';
		$statusCode = $this->client->chargeUp($cardId,$cardPass);
		return "处理状态码:".$statusCode;
	}

	/**
	 * 查询单条费用 用例
	 */
	function getEachFee() {
		$fee = $this->client->getEachFee();
		return "费用:".$fee;
	}

	/**
	 * 企业注册 用例
	 */
	function registDetailInfo()	{
		$eName = "xx公司";
		$linkMan = "陈xx";
		$phoneNum = "010-1111111";
		$mobile = "159xxxxxxxx";
		$email = "xx@yy.com";
		$fax = "010-1111111";
		$address = "xx路";
		$postcode = "111111";

		/**
		 * 企业注册  [邮政编码]长度为6 其它参数长度为20以内
		 *
		 * @param string $eName 	企业名称
		 * @param string $linkMan 	联系人姓名
		 * @param string $phoneNum 	联系电话
		 * @param string $mobile 	联系手机号码
		 * @param string $email 	联系电子邮件
		 * @param string $fax 		传真号码
		 * @param string $address 	联系地址
		 * @param string $postcode  邮政编码
		 *
		 * @return int 操作结果状态码
		 *
		 */
		$statusCode = $this->client->registDetailInfo($eName,$linkMan,$phoneNum,$mobile,$email,$fax,$address,$postcode);
		return "处理状态码:".$statusCode;

	}

	/**
	 * 更新密码 用例
	 */
	function updatePassword($password){
		/**
		 * [密码]长度为6
		 *
		 * 如下面的例子是将密码修改成: 654321
		 */
		$statusCode = $this->client->updatePassword($password);
		return "处理状态码:".$statusCode;
	}

	/**
	 * 短信转发 用例
	 */
	function setMOForward($phone)	{

		/**
		 * 向 159xxxxxxxx 进行转发短信
		 */
		$statusCode = $this->client->setMOForward($phone);
		return "处理状态码:".$statusCode;
	}

	/**
	 * 得到上行短信 用例
	 */
	function getMO() {
		$moResult = $this->client->getMO();
		$return[] = "返回数量:".count($moResult);
		foreach($moResult as $mo) {
			//$mo 是位于 Client.php 里的 Mo 对象
			// 实例代码为直接输出
			$return[] = "发送者附加码:".$mo->getAddSerial();
			$return[] = "接收者附加码:".$mo->getAddSerialRev();
			$return[] = "通道号:".$mo->getChannelnumber();
			$return[] = "手机号:".$mo->getMobileNumber();
			$return[] = "发送时间:".$mo->getSentTime();

			/**
			 * 由于服务端返回的编码是UTF-8
			*/
			$return[] = "短信内容:".$mo->getSmsContent();
			// 上行短信务必要保存,加入业务逻辑代码,如：保存数据库，写文件等等
		}
		return $return;
	}

	/**
	 * 短信发送 用例
	 *
	 * $phone is array
	 */
	function sendSMS($phone, $content) {
		/**
		 * 下面的代码将发送内容为 test 给 159xxxxxxxx 和 159xxxxxxxx
		 * $client->sendSMS还有更多可用参数，请参考 Client.php
		 */
		$smsId = time();
		// 		$priority = 1;
		$statusCode = $this->client->sendSMS($phone, $content, '', '', 'utf8', 5, $smsId);

		$return['smsMessageSid'] = $smsId;
		if($statusCode == '0'){
			$return['code'] = 1;
			$return['statusCode'] = $statusCode;
			$return['msg'] = 'success';
			return $return;
		}else{
			$return['code'] = 0;
			$return['statusCode'] = $statusCode;
			$return['msg'] = 'failed';
			return $return;
		}
	}

	/**
	 * 发送语音验证码 用例
	 *
	 * $phone is array
	 */
	function sendVoice($phone) {
		/**
		 * 下面的代码将发送验证码123456给 159xxxxxxxx
		 * $client->sendSMS还有更多可用参数，请参考 Client.php
		 */
		$statusCode = $this->client->sendVoice($phone,$content);
		return $statusCode;
	}

	/**
	 * 余额查询 用例
	 */
	function getBalance() {
		$balance = $this->client->getBalance();
		return "余额:".$balance;
	}

	/**
	 * 短信转发扩展 用例
	 *
	 * $phone is array
	 */
	function setMOForwardEx($phone)
	{
		/**
		 * 向多个号码进行转发短信
		 *
		 * 以数组形式填写手机号码
		 */
		$statusCode = $this->client->setMOForwardEx($phone);
		// 		"处理状态码:".$statusCode;
		return $statusCode;
	}
}