<?php 
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="90" valign="top" background="/site_static/images/topbg.jpg"><table width="960" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td width="227" height="90" align="center" valign="middle">logo显示位置</td>
        <td width="733" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="99%" height="24" align="right" valign="middle">
			{{db _name=head from='chq_plot' select='title,url' where='tidname=\"head\"'}}
			<a href="{{$head[head].url}}">{{$head[head].title}}</a> {{if !$smarty.db.head.last}}| {{/if}}{{/db}}</td>
            <td width="1%" align="right" valign="middle">&nbsp;</td>
          </tr>
          <tr>
            <td height="38" colspan="2" class="gray_font12">中国最专业的电视电话服务提供商</td>
          </tr>
          <tr>
            <td height="28" colspan="2">
            
            <div id="mainmenu_top">
                <ul>
<li><a id="mm1" href="/" onmouseover="showM(this,1);" onmouseout="OnMouseLeft();" target="_parent">网站首页</a></li>
{{section name=pxx loop=$headmenu_product}}
<li><a id="mm{{$smarty.section.pxx.index+$menu_start}}" href="{{$headmenu_product[pxx].url}}" onmouseover="showM(this,{{$smarty.section.pxx.index+$menu_start}});" onmouseout="OnMouseLeft();" target="_parent">{{$headmenu_product[pxx].name}}</a></li>
{{/section}}
			 </ul>
            </div></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="10"></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="29" bgcolor="#CC0001"><table width="960" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td width="227" height="29">&nbsp;</td>
        <td width="733">
        
        <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"> 
          <tr>
            <td>
            <div id="mainmenu_bottom" >
                <div class="mainmenu_rbg">
                    <ul id="mb1" class="hide">
                        <li><a href='/'>欢迎光临诚意普天，请选择您需要的服务</a></li>
                    </ul>
					<ul id="mb3" class="hide">
                        <li><a href='/case.html'>成功案例首页</a></li>
                    </ul>
{{getall _name=headmenu_download where="fid=1 and extras like '%menu%'" from='chq_downloadcate' limit=4 order='orderid desc'}}
	<ul id="mb2" class="hide">
	{{section name=xx loop=$headmenu_download}}
	<li><a href="{{$headmenu_download[xx].url}}" target="_parent">{{$headmenu_download[xx].name}}</a></li>
	{{/section}}
	</ul>		            
{{section name=xx loop=$headmenu_product}}
<ul id="mb{{$smarty.section.xx.index+$menu_start}}" class="hide">
	<!--<li><a href="/static/hosting/index_hosting.asp" target="_parent">主机服务首页</a></li>-->
	{{db from=chq_productcate where='extras like \"%menu%\"' fid=$headmenu_product[xx].id order='orderid desc'}}
	<li ><a href="{{$records[records].url}}" target="_parent">{{$records[records].name}}</a></li>
	{{/db}}
</ul>
{{/section}}

{{section name=xx loop=$headmenu_news}}
<ul id="mb{{$smarty.section.xx.index+$menu_start+$fcate_num.cc}}" class="hide">
	{{db from=chq_newscate where='extras like \"%menu%\"' fid=$headmenu_news[xx].id order='orderid desc'}}
	<li ><a href="{{$records[records].url}}" target="_parent">{{$records[records].name}}</a></li>
	{{/db}}
</ul>
{{/section}}
</div>
            </div>           
            </td>
          </tr>
        </table>
        </td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="3" background="/site_static/images/nav_line.jpg"></td>
  </tr>
</table>
<script language="javascript" type='text/javascript'>
var menu = ['','','/download/','/case.html',{{section name=xx loop=$headmenu_product}}'{{$headmenu_product[xx].url}}',{{/section}}{{section name=xx loop=$headmenu_news}}'{{$headmenu_news[xx].url}}',{{/section}}''];
window.load=rmenuURL()
</script>