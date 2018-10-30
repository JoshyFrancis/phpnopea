<?php
function share_data_with_view(){
	var_dump(get_class($this));
	View::share('share_otf', 'share data on the fly');
}
