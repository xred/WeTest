<html>
<head>
	<title>微信公众平台功能测试盒</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="UTF-8" />
	<style type="text/css">
		.messageBox{
			width: 100%;
			height: 400px;
			border: 2px dashed #e2e2e2;
			overflow: scroll;
			padding: 10px;
		}
		#intro{
			float: right;
			width: 50%;
		}
		.left{
			float: left;
			width: 100%;
			text-align: left;
		}
		.right{
			float: right;
			width: 100%;
			text-align: right;
		}
		#wrapper{
			width: 80%;
			margin: 0 auto;
		}
	</style>
	<script type="text/javascript" src="jquery.js"></script>
	<script type="text/javascript">
	loadXML = function(xmlString){
        var xmlDoc=null;
        //判断浏览器的类型
        //支持IE浏览器 
        if(!window.DOMParser && window.ActiveXObject){   //window.DOMParser 判断是否是非ie浏览器
            var xmlDomVersions = ['MSXML.2.DOMDocument.6.0','MSXML.2.DOMDocument.3.0','Microsoft.XMLDOM'];
            for(var i=0;i<xmlDomVersions.length;i++){
                try{
                    xmlDoc = new ActiveXObject(xmlDomVersions[i]);
                    xmlDoc.async = false;
                    xmlDoc.loadXML(xmlString); //loadXML方法载入xml字符串
                    break;
                }catch(e){
                }
            }
        }
        //支持Mozilla浏览器
        else if(window.DOMParser && document.implementation && document.implementation.createDocument){
            try{
                /* DOMParser 对象解析 XML 文本并返回一个 XML Document 对象。
                 * 要使用 DOMParser，使用不带参数的构造函数来实例化它，然后调用其 parseFromString() 方法
                 * parseFromString(text, contentType) 参数text:要解析的 XML 标记 参数contentType文本的内容类型
                 * 可能是 "text/xml" 、"application/xml" 或 "application/xhtml+xml" 中的一个。注意，不支持 "text/html"。
                 */
                domParser = new  DOMParser();
                xmlDoc = domParser.parseFromString(xmlString, 'text/xml');
            }catch(e){
            }
        }
        else{
            return null;
        }

        return xmlDoc;
    }

    	function displayText(sendback){
    			var messageBoxContent = $(".messageBox").html();
				var html = messageBoxContent + "<span class='right'>回复："+sendback+"</span>";
				$(".messageBox").html(html);
    	}
    	function displayNews (title,url,description,isFirst) {
    			var messageBoxContent = $(".messageBox").html();
    			if (isFirst == 1) {
    				 var html = messageBoxContent + "<span class='right'>回复：图文消息:<a href='"+url+"' target='about_blank' >"+title+"</a></span><br/>";
    			}else{
    				 var html = messageBoxContent + "<span class='right'>图文消息:<a href='"+url+"' target='about_blank' >"+title+"</a></span><br/>";
    			}
    			$(".messageBox").html(html);
    	}
    	function displayMusic(title,url,description) {
    			var messageBoxContent = $(".messageBox").html();
    			var html = messageBoxContent + "<span class='right'>回复：音乐消息:<a href='"+url+"' target='about_blank' >"+title+"</a></span><br/>";
    			$(".messageBox").html(html);
    	}

		function responseParse(data){
			console.log(data);
			var xmldoc = loadXML(data);
			var sendback;
			var content = xmldoc.getElementsByTagName("Content");
			var msgtype = xmldoc.getElementsByTagName("MsgType")[0].firstChild.nodeValue;
			if (msgtype == "text") {
				for (var i = 0; i < content.length; i++) {
					sendback = content[i].firstChild.nodeValue;
					displayText(sendback);
				}
			}
			if (msgtype == "news") {
				var items = xmldoc.getElementsByTagName("item");
				for (var i = items.length - 1; i >= 0; i--) {
					//console.log(items[i])
					var title = items[i].getElementsByTagName("Title")[0].firstChild.nodeValue;
					var url = items[i].getElementsByTagName("Url")[0].firstChild.nodeValue;
					var description = items[i].getElementsByTagName("Description")[0].firstChild.nodeValue;
					if (i == items.length) {
						displayNews(title,url,description,1);
					}else{
						displayNews(title,url,description,0);
					}
				};
			}
			if (msgtype == "music") {
					var title = xmldoc.getElementsByTagName("Title")[0].firstChild.nodeValue;
					var url = xmldoc.getElementsByTagName("MusicUrl")[0].firstChild.nodeValue;
					var description = xmldoc.getElementsByTagName("Description")[0].firstChild.nodeValue;
					displayMusic(title,url,description);
			}

		}


		var url = "";
		var data;
		$(document).ready(function(){

			$(".setUrl").click(function(){
				console.log("set url");
				url = $(".url")[0].value;
			})

			$(".send").click(function(){

				var tousername,fromusername,msgtype,content,funcflag;
				var url = $(".url")[0].value;
				var keyword = $(".keyword")[0].value;
				var data = "<xml><Url><![CDATA[{url}]]></Url><ToUserName><![CDATA[{tousername}]]></ToUserName><FromUserName><![CDATA[{fromusername}]]></FromUserName><CreateTime>123</CreateTime><MsgType><![CDATA[{msgtype}]]></MsgType><Content><![CDATA[{content}]]></Content><FuncFlag>{funcflag}</FuncFlag></xml>";
				if (keyword=="") {
					displayBack("请输入内容");
					return 0;
				}
				content = $(".keyword")[0].value;
				tousername = $(".sendname")[0].value;
				fromusername = $(".receivename")[0].value;
				funcflag = 0;
				msgtype = "text";
				data = data.replace("{tousername}",tousername);
				data = data.replace("{fromusername}",fromusername);
				data = data.replace("{msgtype}",msgtype);
				data = data.replace("{content}",content);
				data = data.replace("{funcflag}",funcflag);
				data = data.replace("{url}",url);
				$(".keyword")[0].value = "";
				var messageBoxContent = $(".messageBox").html();
				var html = messageBoxContent + "<span class='left'>你说："+keyword+"</span>";
				$(".messageBox").html(html);


				$.ajax({
					type:"POST",
					url:"testpost.php",
					processData:false,
					data:data,	
					contentType:"text/xml",
					success:function(data){
						responseParse(data);
					},
					error:function (XMLHttpRequest, textStatus, errorThrown) {
	 					//alert(errorThrown); 
	 					console.log("error");
	 				}
				})
			})
		})

	</script>
</head>
<body>
  <script src="jquery.js"></script>
<div id="wrapper">
    <h2>微信公众平台功能在线测试：</h2>
	<input class="url" type="text" value="" placeholder="">
	<div class="messageBox"></div>
	输入内容：<input type="text" class="keyword" value="民解民忧"><br>
	发送者openID:<input type="text" class="sendname" value="red"><br>
	接收者openID:<br><input type="text" class="receivename" value="wechat"><br>
	<form action="buy.php" method="post">
			<input type="button" class="send btn " value="发送" />			
	</form>
      <hr>

      <footer>
        <p> By <a href="http://xhxh.me">@xred</a></p>
      </footer>

 </div>
</body>
</html>
