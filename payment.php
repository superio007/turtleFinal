<?php
require_once('SHAPaydollarSecure.php');
$merchantId='16000806';
$orderRef=date('YmdHis');
$currCode='036';
$amount=10.0;
$paymentType='N';
$mpsMode="NIL";
$payMethod="ALL";
$lang="E";
$successUrl="http://www.yourdomain.com/Success.html";
$failUrl="http://www.yourdomain.com/fail.html";
$cancelUrl="http://www.yourdomain.com/cancel.html";
$remark="";
$redirect="";
$oriCountry="";
$destCountry="";
$secureHashSecret='rp6RIf6VpNbT4vMTskQ9qu0Gusyp2yJB';
$paydollarSecure=new SHAPaydollarSecure(); 
$secureHash=$paydollarSecure->generatePaymentSecureHash($merchantId, $orderRef, $currCode, $amount, $paymentType, $secureHashSecret);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Insert title here</title>
	</head>
	<body>
		<form name="payFormCcard" method="post" action="https://test.paydollar.com/b2cDemo/eng/payment/payForm.jsp">
		<table>
			<tr><td >merchantId:</td><td ><input type="text" name="merchantId" value="<?php echo $merchantId?>" hidden> </td></tr>
			<tr><td >amount:</td><td ><input type="text" name="amount" value="<?php echo $amount?>"  hidden></td></tr>
			<tr><td >orderRef:</td><td ><input type="text" name="orderRef" value="<?php echo $orderRef?>" hidden></td></tr>
			<tr><td >currCode:</td><td ><input type="text" name="currCode" value="<?php echo $currCode?>"  hidden></td></tr>
			<tr><td >successUrl:</td><td ><input type="text" name="successUrl" value="<?php echo $successUrl?>" hidden></td></tr>
			<tr><td >failUrl:</td><td ><input type="text" name="failUrl" value="<?php echo $failUrl?>" hidden></td></tr>
			<tr><td >cancelUrl:</td><td ><input type="text" name="cancelUrl" value="<?php echo $cancelUrl?>" hidden></td></tr>
			<tr><td >payType:</td><td ><input type="text" name="payType" value="<?php echo $paymentType?>" hidden></td></tr>
			<tr><td >lang:</td><td ><input type="text" name="lang" value="<?php echo $lang?>" hidden></td></tr>
			<tr><td >mpsMode:</td><td ><input type="text" name="mpsMode" value="<?php echo $mpsMode?>" hidden></td></tr>
			<tr><td >payMethod:</td><td ><input type="text" name="payMethod" value="<?php echo $payMethod?>" hidden></td></tr>
			<tr><td  colspan="2">Optional Parameter for connect to our payment page</td></tr>
			<tr><td >secureHash:</td><td ><input type="text" name="secureHash" value="<?php echo $secureHash?>" hidden></td></tr>
			<tr><td >remark:</td><td ><input type="text" name="remark" value="<?php echo $remark?>" hidden></td></tr>
			<tr><td >redirect:</td><td ><input type="text" name="redirect" value="<?php echo $redirect?>" hidden></td></tr>
			<tr><td >oriCountry:</td><td ><input type="text" name="oriCountry" value="<?php echo $oriCountry?>" hidden></td></tr>
			<tr><td >destCountry:</td><td ><input type="text" name="destCountry" value="<?php echo $destCountry?>" hidden></td></tr>
			<tr><td colspan="2" ><input type="submit" name="submit"></td></tr>
		</table>
		</form>
	</body>
</html>