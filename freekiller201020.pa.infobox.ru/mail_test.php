<?php
#!/usr/bin/php -q
$address="htc.burchenko@gmail.com";
$subj="Mail Test";
$text=date("D, d.m.y, H:i");
$head="Content-type:text/plain; \n\t charset=windows-1251;";
echo "Sending to $address";
mail($address, $subj, $text, $head);
echo "Ok, it works";
