<?php 

$a =  "http://www.stores.ebay.com/id=381199165";
$b =  "http://www.ebay.de/itm/Zassenhaus-Santiago-Coffee-Mill-Burr-Grinder-Varnished-Beech-Wood-Genuine-NEW-/331269658962";

preg_match("/ebay.[a-z]+/" , $a , $matches );

$c = str_replace('ebay.de' , $matches[0] , $b);
echo $c;


?>
