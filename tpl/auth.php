<?php

if ($this->lib->user->id > 0) {
    echo $this->lib->user->name
        .' <a href="'.$this->link('/auth/logout').'">'
        .$this->lib->t('Logout')
        .'</a>';
} else {

$values = array(
    'login'    => '',
    'password' => '',
    'cookie'   => '',
);

if (!empty($_POST)) {
    foreach ($values as $name=>&$value) {
        if (isset($_POST[$name])) {
            $value = html_entity_decode($_POST[$name], ENT_HTML5, 'UTF-8');
        }
    }
    
    if (!empty($values['login']) && !empty($values['password'])) {
        $user = $this->lib->user->login(
            $values['login'], 
            $values['password'], 
            ($values['cookie']=='yes' ? 31536000 : 0) // login for 1 year
        );
    }
    
}

?>

<form id="form_add" name="form_add" method="post" action="<?=$this->link('/auth/login')?>">
	
	<label for="login">Login or email</label>
	<input type="text" value="<?=$values['login']?>" id="login" name="login">
	<br>
	
	<label for="password">Password</label>
	<input type="password" value="<?=$values['password']?>" id="password" name="password">
    <br>
    
    <input id="cookie" type="checkbox" style="display:inline" <?=(empty($_POST) || $values['cookie']=='yes')?'checked="checked"':''?> value="yes" name="cookie">
    <label for="cookie" style="display:inline">Remember me</label>
	
	<br><br>
	
	<input type="submit" value="Отправить" id="submit">
    
</form>

<?php
}