function update_payment_status1(urlpath,id, status)
{
	//alert(id);

	$.ajax({
		type: "POST",
		url: urlpath+'/mlm/ajaxFunction.php', 
		data: 'do=statuschange'+'&userId='+id +'&status='+ status,
		dataType: 'html',  
		beforeSend : function(){
			$('#resultmsg_'+id).html('<img src="'+ urlpath +'/mlm/images/ajax-loader.gif" class="loadImage" />');
		},
		success: function(msg){
			//alert(msg);						
			$('#resultmsg_'+id).html(msg);
		},
		error: function(){
			alert('some error has occured...');	
		},
		
		start: function(){
			alert('ajax has been started...');	
		}
	});
	
	
}

function update_payment_status(urlpath, id, status)
{
	var xmlhttp;
	document.getElementById("resultmsg_"+id).innerHTML= '<img src="'+ urlpath +'/mlm/images/ajax-loader.gif" class="loadImage" />';
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	xmlhttp.onreadystatechange=function()
	  {
	if (xmlhttp.status==200 && xmlhttp.readyState==4)
	{
	 document.getElementById("resultmsg_"+id).innerHTML=xmlhttp.responseText;
	}
	}
	xmlhttp.open("GET", urlpath+'/mlm/ajaxFunction.php?do=statuschange'+'&userId='+id +'&status='+ status,true);
	xmlhttp.send();
}
