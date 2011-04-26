<?php
class RandomKey
{
    /**
     * 随机生成一个字符串
     * @param $length
     * @param $number
     * @param $not_o0
     * @return unknown_type
     */
    static function string($length=8,$number=true,$not_o0=false)
	{
        $strings = 'ABCDEFGHIJKLOMNOPQRSTUVWXYZ';  //字符池
        $numbers = '0123456789';                    //数字池
        if($not_o0)
        {
            $strings = str_replace('O','',$strings);
            $numbers = str_replace('0','',$numbers);
        }
        $pattern = $strings.$number;
        $max = strlen($pattern)-1;
        $key = '';
        for($i=0;$i<$length;$i++)
        {
            $key .= $pattern{mt_rand(0,$max)};    //生成php随机数
        }
        return $key;
	}
	/**
	 * 按ID计算散列
	 * @param $uid
	 * @param $base
	 * @return unknown_type
	 */
    static function idhash($uid,$base=1000)
	{
		return intval($uid/$base);
	}
	/**
	 * 按UNIX时间戳产生随机数
	 * @param $length
	 * @return unknown_type
	 */
	static function randtime($length=8,$time_length=6)
	{
	    $min = intval('1'.str_pad(0,$length-1));
	    $max = intval(str_pad(9,$length));
	    return rand($min,$max).substr(time(),-6,$time_length);
	}
	/**
	 * 产生一个随机MD5字符的一部分
	 * @param $length
	 * @param $seed
	 * @return unknown_type
	 */
	static function randmd5($length=8,$seed=null)
	{
	    if(empty($seed)) $seed = self::string(16);
		return substr(md5($seed.rand(111111,999999)),0,$length);
	}
}
?>