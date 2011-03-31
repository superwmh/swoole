<?php
/**
 * 过滤类
 * 用于过滤过外部输入的数据，过滤数组或者变量中的不安全字符，以及HTML标签
 * @author Tianfeng.Han
 * @package SwooleSystem
 * @subpackage request_filter
 *
 */
class Filter
{
    static $error_url;
    public $mode;

    function __construct($mode='deny',$error_url=false)
    {
        $this->mode = $mode;
        self::$error_url = $error_url;
    }
    function post($param)
    {
        $this->_check($_POST,$param);
    }
    function get($param)
    {
        $this->_check($_GET,$param);
    }
    function cookie($param)
    {
        $this->_check($_COOKIE,$param);
    }
    /**
     * 根据提供的参数对数据进行检查
     * @param $data
     * @param $param
     * @return unknown_type
     */
    function _check(&$data,$param)
    {
        foreach($param as $k=>$p)
        {
            if(!isset($data[$k]))
            {
                if(isset($p['require']) and $p['require']) self::raise('param require');
                else continue;
            }

            if(isset($p['type']))
            {
                $data[$k] = Validate::$p['type']($data[$k]);
                if($data[$k]===false) self::raise();

                //最小值参数
                if(isset($p['min']) and is_numeric($data[$k]) and $data[$k]<$p['min']) self::raise('num too small');
                //最大值参数
                if(isset($p['max']) and is_numeric($data[$k]) and $data[$k]>$p['max']) self::raise('num too big');

                //最小值参数
                if(isset($p['short']) and is_string($data[$k]) and mb_strlen($data[$k])<$p['short']) self::raise('string too short');
                //最大值参数
                if(isset($p['long']) and is_string($data[$k]) and mb_strlen($data[$k])>$p['long']) self::raise('string too long');

                //自定义的正则表达式
                if($p['type']=='regx' and isset($p['regx']) and preg_match($p['regx'],$data[$k])===false) self::raise();
            }
        }
        //如果为拒绝模式，所有不在过滤参数$param中的键值都将被删除
        if($this->mode=='deny')
        {
            $allow = array_keys($param);
            $have = array_keys($data);
            foreach($have as $ha) if(!in_array($ha,$allow)) unset($data[$ha]);
        }
    }
    static function raise($text=false)
    {
        if(self::$error_url) Swoole_client::redirect(self::$error_url);
        if($text) exit($text);
        else exit('Web input param error!');
    }
    static function request()
    {
        //过滤$_GET $_POST $_REQUEST $_COOKIE 请
        Filter::filter_array($_POST);
        Filter::filter_array($_GET);
        Filter::filter_array($_REQUEST);
        Filter::filter_array($_COOKIE);
    }
    static function safe(&$content)
    {
        if(DBCHARSET=='utf8') $charset = 'utf-8';
        else $charset = DBCHARSET;
        Filter::deslash($content);
        $content = html_entity_decode($content,ENT_QUOTES,$charset);
    }
    public static function filter_var($var,$type)
    {
        switch($type)
        {
            case 'int':
                return intval($var);
            case 'string':
                return htmlspecialchars(strval($var),ENT_QUOTES);
            case 'float':
                return floatval($var);
            default:
                return false;
        }
    }

    public static function filter_array(&$array,$allow_html=false)
    {
        foreach($array as &$string)
        {
            if($allow_html===false)
            {
                if(is_array($string))
                {
                    self::filter_array($string);
                }
                else
                {
                    if(DBCHARSET=='utf8') $charset = 'utf-8';
                    else $charset = DBCHARSET;
                    $string = htmlspecialchars($string,ENT_QUOTES,$charset);
                }
            }
            if(is_array($string))
            {
                self::filter_array($string);
            }
            else self::addslash($string);
        }
    }
    /**
     * 移除HTML中的危险代码，如iframe和script
     * @param $val
     * @return unknown_type
     */
    public static function RemoveXSS(&$val)
    {
        // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
        // this prevents some character re-spacing such as <java\0script>
        // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
        $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
        // straight replacements, the user should never need these since they're normal characters
        // this prevents like <IMG SRC=@avascript:alert('XSS')>
        $search = 'abcdefghijklmnopqrstuvwxyz';
        $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $search .= '1234567890!@#$%^&*()';
        $search .= '~`";:?+/={}[]-_|\'\\';
        for ($i = 0; $i < strlen($search); $i++) {
            // ;? matches the ;, which is optional
            // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

            // @ @ search for the hex values
            $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
            // @ @ 0{0,7} matches '0' zero to seven times
            $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
        }

        // now the only remaining whitespace attacks are \t, \n, and \r
        $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed',
         'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
        $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate','onbeforecopy', 'onbeforecut',
        'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur',
         'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable',
         'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave',
         'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin',
         'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown',
         'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend',
         'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart',
         'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart',
         'onstart', 'onstop', 'onsubmit', 'onunload');
        $ra = array_merge($ra1, $ra2);

        $found = true; // keep replacing as long as the previous round replaced something
        while ($found == true) {
            $val_before = $val;
            for ($i = 0; $i < sizeof($ra); $i++) {
                $pattern = '/';
                for ($j = 0; $j < strlen($ra[$i]); $j++) {
                    if ($j > 0) {
                        $pattern .= '(';
                        $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                        $pattern .= '|';
                        $pattern .= '|(&#0{0,8}([9|10|13]);)';
                        $pattern .= ')*';
                    }
                    $pattern .= $ra[$i][$j];
                }
                $pattern .= '/i';
                $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
                $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
                if ($val_before == $val) {
                    // no replacements were made, so exit the loop
                    $found = false;
                }
            }
        }
    }

    public static function addslash(&$string)
    {
        if(!get_magic_quotes_gpc()) $string = addslashes($string);
    }

    public static function deslash(&$string)
    {
        $string = stripslashes($string);
    }
}
?>