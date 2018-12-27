<?php
$table = new Schema('app_notes'); # app_notes is the table name which will be created automatically when installing
$table->add('title','varchar', 200);
$table->add('contents','text');
$table->add('created_at','TIMESTAMP','','null');
$table->add('updated_at','TIMESTAMP','','null');
$table->save();