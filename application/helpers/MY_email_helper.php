<?php
function registerMessage($md5)
{
  return <<<EOT
According to our records, you have requested to become a
member to Pump Pro Edits at www.pumpproedits.com recently.

If you are that person, please go to the following URL:
http://www.pumpproedits.com/confirm/$md5

Remember to also put your password in the form provided.

If you did not request to register, you may delete this email
and not worry about it.

EOT;
}

function resetMessage($md5)
{
  return <<<EOL
According to our records, you have requested to reset your
password for your account on Pump Pro Edits at
www.pumpproedits.com.

If you are that person, please go to the following URL:
http://www.pumpproedits.com/reset/$md5

You will be asked to supply a new password of your choice.

If you did not request to reset your password, you may
delete this email and not worry about it.

EOL;
}

function resendMessage($md5)
{
  return <<<EOL
According to our records, you have requested a new confirmation
email message from Pump Pro Edits at www.pumpproedits.com recently.

If you are that person, please go to the following URL:
http://www.pumpproedits.com/confirm/$md5

Remember to also put your password in the form provided.

If you did not request to reconfirm your account,
you may delete this email and not worry about it.

EOL;
}
