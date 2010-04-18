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
