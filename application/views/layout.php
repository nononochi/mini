<!DOCTYPE html>  
<html lang="ja">  
<head>  
    <meta charset="utf-8"> 
    <link rel="stylesheet" type="texy/css" media="screen" href="/css/style.css" />
    <title>
        <?php if(isset($title) === true) : echo $this->escape($title) . ' - '; endif; ?>Mini Blog
    </title>
</head>  
<body> 

<header>
    <h1><a href="<?php echo $baseUrl; ?>/">Mini Blog</a></h1>
</header>

<nav>
    <p>
        <?php if ($session->isAuthenticated() === true): ?>
            <a href="<?php echo $baseUrl; ?>/">ホーム</a>
            <a href="<?php echo $baseUrl; ?>/account">アカウント</a>
        <?php else: ?>
            <a href="<?php echo $baseUrl; ?>/account/signin">ログイン</a>
            <a href="<?php echo $baseUrl; ?>/account/signup">アカウント登録</a>
        <?php endif; ?>
    </p>
</nav>

<div id="main">
    <?php echo $_content; ?>
</div>

</body>
</html>
