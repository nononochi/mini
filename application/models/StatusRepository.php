<?php

class StatusRepository extends DbRepository
{

    /**
     * 発言をDBに登録する
     * 
     * @param int    $userId ユーザID
     * @param string $body   ひとこと
     */
    public function insert($userId, $body)
    {
        $now = new DateTime();

        $sql = "INSERT INTO status (user_id, body, created_at) VALUES (:user_id, :body, :created_at)";

        $this->execute($sql, array(
            ':user_id'   => $userId,
            ':body'      => $body,
            'created_at' => $now->format('Y-m-d H:i:s'),
        ));
    }

    /**
     * すべての発言を取得する
     * 
     * @return array         取得結果の配列 [intの番号]['id','user_id','body'・・・]
     */
    public function fetchAllPersonalArchives()
    {
        $sql = "SELECT a.*, u.user_name FROM status a LEFT JOIN user u ON a.user_id=u.id
            ORDER BY a.created_at DESC";

        return $this->fetchAll($sql);
    }

    /**
     * ひとことをバリデートし、エラーを返す
     * 
     * @param  string $body ひとこと
     * @return array        エラー内容
     */
    public function validateBody($body)
    {
        $errors = array();

        if (strlen($body) === 0) {
            $errors[] = 'ひとことを入力してください';
        } elseif (mb_strlen($body) > 200) {
            $errors[] = 'ひとことは200文字以内で、入力してください';
        }

        return $errors;
    }

    /**
     * ユーザーIDからひとことを取得する
     * 
     * @param  int   $userId ユーザー名
     * @return array
     */
    public function fetchAllByUserId($userId)
    {
        $sql = "SELECT a.*, u.user_name FROM status a LEFT JOIN user u ON a.user_id = u.id
            WHERE u.id = :user_id ORDER BY a.created_at DESC";

        return $this->fetchAll($sql, array(':user_id' => $userId));
    }

    /**
     * ユーザ名と発言IDから発言を取得する
     * 
     * @param  int    $id       発言ID
     * @param  string $userName ユーザ名
     * @return array
     */
    public function fetchByIdAndUserName($id, $userName)
    {
        $sql = "SELECT a.*, u.user_name FROM status a LEFT JOIN user u ON u.id = a.user_id
            WHERE a.id = :id AND u.user_name = :user_name";

        return $this->fetch($sql, array(
                    ':id'        => $id,
                    ':user_name' => $userName,
                ));
    }

}
