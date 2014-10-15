<?php

use core\view as View;

View::rendertemplate('contentFinish',$data);
View::rendertemplate('modal', $data);
View::rendertemplate('footer', $data);

?>
