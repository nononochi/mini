<?php

/**
 * $_SESSIONのラッパークラス
 */
class Session
{

    protected static $_sessionStarted = false;
    protected static $_sessionIdRegenerated = false;

    public function __construct()
    {
        if (self::$_sessionStarted === false) {
            session_start();
            self::$_sessionStarted = true;
        }
    }

    /**
     * セッションを設定する
     *
     * @param string $name  セッション名
     * @param string $value セッションの値　
     */
    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * セッションの値を取得する
     *
     * @param  string $name    セッション名
     * @param  string $default セッションがなかった場合のデフォルト値
     * @return string セッションがあればその値、なければデフォルト値
     */
    public function get($name, $default = null)
    {
        if (isset($_SESSION[$name]) === true) {
            return $_SESSION[$name];
        }

        return $default;
    }

    /**
     * セッションの値を削除する
     *
     * @param string $name セッションの値
     */
    public function remove($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * セッションをクリアする
     */
    public function clear()
    {
        $_SESSION = array();
    }

    /**
     * セッションIDを新しく発行する
     *
     * @param boolean $destory 現在のIDを削除する場合true
     */
    public function regenerate($destroy = true)
    {
        if (self::$_sessionIdRegenerated === false) {
            session_regenerate_id($destory);

            self::$_sessionIdRegenerated = true;
        }
    }

    /**
     * ログイン状態をセッションにセットする
     *
     * @param boolean $bool ログイン時はtrue
     */
    public function setAuthenticated($bool)
    {
        $this->set('_authenticated', (bool) $bool);
        $this->regenerate();
    }

    /**
     * ログイン状態かどうかを確認する
     *
     * @return boolean ログイン時true
     */
    public function isAuthenticated()
    {
        return $this->get('_authenticated', false);
    }

}
