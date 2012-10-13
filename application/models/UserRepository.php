<?php

class UserRepository extends DbRepository
{

    /**
     * Userテーブルに書き込む
     *
     * @param string $userName ユーザー名
     * @param string $password パスワード
     */
    public function insert($userName, $password)
    {
        $password = $this->hashPassword($password);
        $now = new DateTime();

        $sql = "INSERT INTO user(user_name, password, created_at)
            values(:user_name, :password, :created_at)";

        $this->execute($sql, array(
            ':user_name'  => $userName,
            ':password'   => $password,
            ':created_at' => $now->format('Y-m-d H:i:s'),
        ));
    }

    /**
     * ユーザー名からユーザを検索する
     *
     * @param  string $userName ユーザー名
     * @return array            検索結果
     */
    public function fetchByUserName($userName)
    {
        $sql = "SELECT * FROM user WHERE user_name = :user_name";

        return $this->fetch($sql, array(':user_name' => $userName));
    }

    /**
     * ユーザー名がすでに使われていないかどうか確認する
     *
     * @param  string  $userName ユーザー名
     * @return boolean           使われていない場合true
     */
    public function isUniqueUserName($userName)
    {
        $sql = "SELECT COUNT(id) as count FROM user WHERE user_name = :user_name";

        $row = $this->fetch($sql, array(':user_name' => $userName));

        if ((int) $row['count'] === 0) {
            return true;
        }

        return false;
    }

    /*
     * パスワードをハッシュ化する
     *
     * @param  string $password パスワード
     * @return string           ハッシュ化されたパスワード
     */

    public function hashPassword($password)
    {
        return sha1($password . 'SecretKey');
    }

    /**
     * ユーザー名のバリデート
     * 
     * @param  string $userName ユーザー名
     * @return string           正しい場合空文字列
     */
    public function validateUserName($userName)
    {
        if (strlen($userName) === 0) {
            return 'ユーザーIDを入力してください';
        } elseif (preg_match('/^\w{3,20}$/', $userName) === false) {
            return 'ユーザーIDは、半角英数字およびアンダースコアを3~20文字以内で入力してください';
        } elseif ($this->isUniqueUserName($userName) === false) {
            return 'ユーザーIDは、既に使用されています';
        }

        return '';
    }

    /**
     * パスワードのバリデート
     * 
     * @param  string $password パスワード
     * @return string 正しい場合空文字列
     */
    public function validatePassword($password)
    {
        if (strlen($password) === 0) {
            return 'パスワードを入力してください';
        } elseif (strlen($password) < 4 || strlen($password) > 30) {
            return 'パスワードは、4～30文字以内で入力してください';
        }

        return '';
    }

}
