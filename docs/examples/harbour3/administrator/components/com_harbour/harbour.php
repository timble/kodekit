<?php
$options = array(
	'default_view' 		=> 'boats',
	'default_layout'	=> 'form'
);
KFactory::get('admin::com.harbour.dispatcher', $options)->dispatch();
