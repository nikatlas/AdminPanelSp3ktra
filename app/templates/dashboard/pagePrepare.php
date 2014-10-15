<?php
use core\view as View;

View::rendertemplate('header', $data);
View::rendertemplate('menu', $data);
View::rendertemplate('contentIntro',$data);

?>
