#!/opt/plesk/php/7.0/bin/php

<?php
//$address="azudov@infobox.ru";
$address="a.lesnickaya@infobox.ru";
$subj="Mail Test";
$text=date("D, d.m.y, H:i");
$head="Content-type:text/plain; \n\t charset=windows-1251;";
echo "Sending to $address";
mail($address, $subj, $text, $head);
echo "Ok, it works";
