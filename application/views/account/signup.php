<?php $this->setLayoutVar('title', 'アカウント登録'); ?>

<h2>アカウント登録画面</h2>

<form action="<?php echo $baseUrl; ?>/account/register" method="post">
    <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>" />

    <?php if (isset($errors) && count($errors) > 0): ?>
        <?php echo $this->render('errors', array('errors' => $errors)); ?>
    <?php endif; ?>

    <?php echo $this->render('account/inputs', 
        array('userName' => $userName, 'password' => $password,)); ?>

    <p><input type="submit" value="登録" /></p>
</form>
