<?php
/*
	example: {res path="css/screen.css"}
*/
function smarty_function_res($params, &$smarty)
{
	if( empty($params['path']) )
	{
		return "[plugin:res] missing parameter.";
	}
	return \Flight::get('root').'web/'.$params['path'];
}
?>