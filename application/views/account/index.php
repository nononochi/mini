<?php $this->setlayoutVar('title', 'アカウント'); ?>

<h2>アカウント</h2>

<p>
    ユーザーID:
    <a href="<?php echo $baseUrl; ?>/user/<?php echo $this->escape($user['user_name']); ?>">
        <strong><?php echo $this->escape($user['user_name']); ?></strong>
    </a>
</p>

<ul>
    <li>
        <a href="<?php echo $baseUrl ?>/">ホーム</a>
    </li>
    <li>
        <a href="<?php echo $baseUrl ?>/account/signout">ログアウト</a>
    </li>
</ul>
