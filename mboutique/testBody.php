<?php
$tokenCode = "wqerewew";
$email = "sianxiao@hotmail.com";
$body = "<p>"
            . "Thank for contancting us, <br>"
            . "please click this link to set a new password <br>"
            . "<a href='http://localhost/project_sit/token_for_reset_password.php?tokenCode=$tokenCode&email=$email'>Click here to set new password</a>"
            . "</p>";
echo $body;
?>