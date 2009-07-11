function ajax_post(url,method,data,callback)
{
	jQuery.ajax({
		   type: "POST",
		   url: url + '?method='+method,
		   dataType : 'json',
		   data: data,
		   success: callback,
		   error: function(error_code){alert(error_code);}
	   }); 
}