<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta
	content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no"
	name="viewport">
<meta name="format-detection" content="telephone=no" />
<meta name="keywords" content="">
<meta name="description" content="">
<title>23</title>
<script type="text/javascript" src="http://style.273.com.cn/js/jquery-1.11.0.min.js"></script>
</head>
<body>
<?php echo 'this is a page of ' . $this->name;?>
<span id="haha">123</span>
</body>
<script type="text/javascript">
$("#haha").click(function(){
	$.ajax({
	    type: 'GET',
	    url : "http://www.fesphp2.com",
	    data: "",
	    dataType: 'json',
	    timeout: 4000,
	    success: function(ret) {
	    }
	});
});
</script>
</html>
