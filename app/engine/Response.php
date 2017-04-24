<?php
namespace bilibili\raichu\engine;
use bilibili\raichu\engine\AbstractController;
/**
 * 响应处理.
 * User: gukai@bilibili.com
 * Date: 17/2/21
 * Time: 下午17:51
 */
class Response
{

    /**
     * @var array $_ret 注册http响应数组
     */
    protected $_ret;


    /**
     * 通过设置成员变量来设置响应数据
     *
     * @param  string $name  键值
     * @param  mix    $value 键值
     * @return void
     */
    public function __set($name, $value)
    {
        $this->_ret[$name] = $value;
    }

    /**
     * 通过成员变量来获取响应数据
     *
     * @param  string $name 键值
     * @return mixed
     */
    public function __get($name)
    {
        return $this->_ret[$name];
    }



    public function setHeader($code)
    {
        $message = self::$httpStatuses[$code];
        header("{$_SERVER['SERVER_PROTOCOL']} {$code} {$message}");
    }


    public function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }


    public function ajaxReturn($data, $type = 'Json', $json_option = JSON_ERROR_NONE)
    {
        switch (strtoupper($type)) {
        case 'JSON':
            // 返回JSON数据格式到客户端 包含状态信息
            header('Content-Type:application/json; charset=utf-8');
            exit(json_encode($data, $json_option));
        case 'JSONP':
            // 返回JSON数据格式到客户端 包含状态信息
            header('Content-Type:application/json; charset=utf-8');
            exit(Request::getInstance()->getRequest('callback').'('.json_encode($data, $json_option).');');
        default:
            return;
        }
    }


    public function abort($code, $message = '')
    {
        $accept_code = [404, 405, 500, 502, 503, 504];
        if (!in_array($code, $accept_code)) {
            $code = 500;
        }
        if (!$message) {
            $message = $code.' '.AbstractController::getResponseDescription($code);
        }
        header($_SERVER['SERVER_PROTOCOL'].' '.$message);
        echo $message;
    }


    /**
     * 返回注册到$_ret的json数据
     *
     * @return void
     */
    public function response()
    {
        if ($this->_ret) {
            $this->ajaxReturn($this->_ret);
        }
    }


}
