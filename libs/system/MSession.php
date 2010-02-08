<?php
if(!defined('SESSION_LIFETIME')) define('SESSION_LIFETIME',3600);
if(!defined('SESSION_LEFTTIME')) define('SESSION_DOMAIN',@$_ENV['HOST']);

/**
 * 会话控制类
 * 功能:自主实现基于Memcache存储的 Session 功能
 * 描述:这个类就是实现Session的功能，基本上是通过
 *    设置客户端的Cookie来保存SessionID，
 *    然后把用户的数据保存在服务器端，最后通过
 *    Cookie中的Session Id来确定一个数据是否是用户的，
 *    然后进行相应的数据操作
 *
 *    本方式适合Memcache内存方式存储Session数据的方式，
 *    同时如果构建分布式的Memcache服务器，
 *    能够保存相当多缓存数据，并且适合用户量比较多并发比较大的情况
 * @author Tianfeng.Han
 * @package SwooleSystem
 * @package Login
 */
class MSession
{   
    // 类成员属性定义
    static  $mSessSavePath;
    static  $mSessName;
    static $cache;

    /**
     * 构造函数
     *
     * @param string $login_user    登录用户
     * @param int $login_type       用户类型
     * @param string $login_sess    登录Session值
     * @return Esession
     */
    public function __construct($cache)
    {
        self::$cache = $cache;
    }

    /**
     * 打开Session
     * @param   String  $pSavePath
     * @param   String  $pSessName
     *
     * @return  Bool    TRUE/FALSE
     */
    static public function sessOpen($pSavePath = '', $pSessName = '')
    {
        self::$mSessSavePath    = $pSavePath;
        self::$mSessName        = $pSessName;
        return TRUE;
    }

    /**
     * 关闭Session
     * @param   NULL
     *
     * @return  Bool    TRUE/FALSE
     */
    static public function sessClose()
    {
        return TRUE;
    }

    /**
     * 读取Session
     * @param   String  $wSessId
     *
     * @return  Bool    TRUE/FALSE
     */
    static public function sessRead($wSessId = '')
    {
        $wData = self::$cache->get($wSessId);

        //先读数据，如果没有，就初始化一个
        if (!empty($wData))
        {
            return $wData;
        }
        else
        {
            //初始化一条空记录
            $ret = self::$cache->set($wSessId, '', 0, SESSION_LIFETIME);
            if (TRUE != $ret)
            {
                die("Fatal Error: Session ID $wSessId init failed!");
                return FALSE;
            }
            return TRUE;
        }
    }

    /**
     *
     * @param   String  $wSessId
     * @param   String  $wData
     *
     * @return  Bool    TRUE/FALSE
     */
    static public function sessWrite($wSessId = '', $wData = '')
    {
        $ret = self::$cache->replace($wSessId, $wData, 0, SESSION_LIFETIME);

        if (TRUE != $ret)
        {
            die("Fatal Error: SessionID $wSessId Save data failed!");
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 销毁Session
     * @param   String  $wSessId
     * @return  Bool    TRUE/FALSE
     */
    static public function sessDestroy($wSessId = '')
    {
        self::sessWrite($wSessId);
        return FALSE;
    }

    /**
     *
     * @param   NULL
     * @return  Bool    TRUE/FALSE
     */
    static public function sessGc()
    {
        //无需额外回收,memcache有自己的过期回收机制
        return TRUE;
    }

    /**
     * 初始化Session，配置Session
     * @param   NULL
     *
     * @return  Bool    TRUE/FALSE
     */
    public function initSess()
    {
        //不使用 GET/POST 变量方式
        ini_set('session.use_trans_sid',    0);

        //设置垃圾回收最大生存时间
        ini_set('session.gc_maxlifetime',   SESSION_LIFETIME);

        //使用 COOKIE 保存 SESSION ID 的方式
        ini_set('session.use_cookies',      1);
        ini_set('session.cookie_path',      '/');

        //多主机共享保存 SESSION ID 的 COOKIE
        ini_set('session.cookie_domain',    SESSION_DOMAIN);

        //将 session.save_handler 设置为 user，而不是默认的 files
        session_module_name('user');

        //定义 SESSION 各项操作所对应的方法名：
        session_set_save_handler(
                array('MSession', 'sessOpen'),   //对应于静态方法 My_Sess::open()，下同。
                array('MSession', 'sessClose'),
                array('MSession', 'sessRead'),
                array('MSession', 'sessWrite'),
                array('MSession', 'sessDestroy'),
                array('MSession', 'sessGc')
                );
        session_start();
        return TRUE;
    }
}
?>