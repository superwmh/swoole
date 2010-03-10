/**
 * 用于表单验证
 * 支持的标签：
 * empty   值为空的时候，提示文字，并使当前表单元素获得焦点
 * equal   值必须等于某个数值
 * noequal 值必须不等于某个数值
 * equalo  值不惜等于某个对象的值
 * ctype   检查值的类型，支持email、tel、english、mobile、nickname几种格式
 */

/**
 * 去除字符串两边的空格
 */
function trim(str) {
	return str.replace(/(^\s*)|(\s*$)/g, "");
}
function ltrim(str) {
	return str.replace(/(^\s*)/g, "");
}
function rtrim(str) {
	return str.replace(/(\s*$)/g, "");
}
function isMobile(mobile) {
	return (/^(?:13\d|15\d|18\d)-?\d{5}(\d{3}|\*{3})$/).test(trim(mobile));
}
function isEmail(strValue) {
	return (/^[\w-\.]+@[\w-]+(\.(\w)+)*(\.(\w){2,4})$/).test(trim(strValue));
}
function isPhone(strValue){
	return (/^\d{3}-?\d{8}|\d{4}-?\d{7}$/).test(trim(strValue));	
}
function isTel(str) {
	var reg = /^\d{7,8}$/;
	var patt=new RegExp(reg);
	return patt.test(str);
}
/**
 * 获取单选框的值
 * @param radioName
 * @return
 */
function getRadioValue(radioName) {
	var obj = document.getElementsByName(radioName);
	var objLen = obj.length;
	var i;
	for (i = 0; i < objLen; i++) {
		if (obj[i].checked == true) {
			return obj[i].value;
		}
	}
	return null;
}
/**
 * 获取复选框的值
 */
function getCheckboxValue(radioName) {
	var obj = document.getElementsByName(radioName);
	var objLen = obj.length;
	var i;
	var result = "";
	for (i = 0; i < objLen; i++) {
		if (obj[i].checked == true) {
			result += obj[i].value + ",";
		}
	}
	return result;
}
/**
 * 复选框 是否处于 选中状态
 */
function CheckboxToChecked(eleName, cValue) {

	var obj = document.getElementsByName(eleName);

	var objLen = obj.length;
	var i;
	var result = "";
	for (i = 0; i < objLen; i++) {

		if (obj[i].value == cValue) {

			obj[i].checked = true;
		} else {
			obj[i].checked = false;
		}
	}
	return result;
}

// checkBox至少选中一项
function chkCheckBoxChs(objNam, txt) {
	var obj = document.getElementsByName(objNam);
	var objLen = obj.length;
	var num = 0;
	for (i = 0; i < objLen; i++) {
		if (obj[i].checked == true) {
			num++;
		}
	}
	if (num == 0) {
		alert(txt);
		return false;
	}
	return true;
}

function isEnglish(strValue) {
	var reg = /^[A-Za-z0-9]*$/gi;
	return reg.test(trim(strValue));
}
function isNickname(strValue) {
	var reg = /^[a-z-_\u4e00-\u9fa5]*$/gi;
	return reg.test(trim(strValue));
}
function ispassword(strValue) {
	var reg = strValue.length;
	if(reg >= 6 && reg <= 12 ){
	   return true;
	}else{
		return false;
	}
}
function isarea(strValue) {
	var reg = /^0\d{2,3}$/;
	var patt = new RegExp(reg);
	return patt.test(strValue);
}

//自定义过滤器
var custom_filter = new Array;

function checkform(event, oform) {
	event = event ? event : window.event;
	if (oform == undefined || oform == null)
		var oform = event.srcElement ? event.srcElement : event.target;
	var elms = oform.elements;
	
	var qs;
	var attr;
	var other_obj;
	var value;

	for ( var i = 0; i < elms.length; i++) {

		// 为空的情况 -empty
		if (elms[i].getAttribute('empty') && elms[i].value == '') {
			elms[i].focus();
			alert(elms[i].getAttribute('empty'));
			return false;
		}
		
		// 检查数值相等的情况 -equal
		if (elms[i].getAttribute('equal')) {
			attr = elms[i].getAttribute('equal');
			qs = attr.split('|');
			if (elms[i].value != qs[0]) {
				elms[i].focus();
				alert(qs[1]);
				return false;
			}
		}
		
		// 检查数值不相等的情况 -noequal
		if (elms[i].getAttribute('noequal')) {
			attr = elms[i].getAttribute('noequal');
			qs = attr.split('|');
			if (elms[i].value == qs[0]) {
				elms[i].focus();
				alert(qs[1]);
				return false;
			}
		}
		
		// 检查对象相等的情况 -equalo
		if (elms[i].getAttribute('equalo')) {
			attr = elms[i].getAttribute('equalo');
			qs = attr.split('|');
			other_obj = document.getElementById(qs[0]);
			if (elms[i].value != other_obj.value) {
				elms[i].focus();
				alert(qs[1]);
				return false;
			}
		}

		// 检查值的类型 -ctype
		if (elms[i].getAttribute('ctype')) {
			attr = elms[i].getAttribute('ctype');
			qs = attr.split('|');
			if (qs[0] == 'email' && !isEmail(elms[i].value)) {
				elms[i].focus();
				alert(qs[1]);
				return false;
			} else if (qs[0] == 'Tel' && !isTel(elms[i].value)) {
				elms[i].focus();
				alert(qs[1]);
				return false;
			} else if (qs[0] == 'nickname' && !isNickname(elms[i].value)) {
				elms[i].focus();
				alert(qs[1]);
				return false;
			} else if (qs[0] == 'password' && !ispassword(elms[i].value)) {
				elms[i].focus();
				alert(qs[1]);
				return false;
			} else if (qs[0] == 'area' && !isarea(elms[i].value)){
				elms[i].focus();
			    alert(qs[1]);
			    return false;
			}
		}
		
		for(var j=0;j<custom_filter.length;j++){
			if(elms[i].id==custom_filter[j].name || elms[i].name==custom_filter[j].name){
				if(custom_filter[j].callback(elms[i])==false){
					elms[i].focus();
					alert(custom_filter[j].msg);
					return false;
				}
			}
		}
	}
	return true;
}
/**
 * 增加自定义过滤条件
 * @return
 */
function add_filter(name,msg,callback){
	custom_filter.push({'name':name,'msg':msg,'callback':callback});
}
/**
 * 验证表单
 * @param id
 * @return
 */
function validator(id) {
	if(id==null) return false;
	var oform = document.getElementById(id);
	oform.onsubmit = checkform;
}
/**
 * 强制验证表单，用于非提交的处理，执行此函数时，即检查表单合格性
 * @param id
 * @return
 */
function validator_force(id) {
	var oform = document.getElementById(id);
	return checkform(null, oform);
}