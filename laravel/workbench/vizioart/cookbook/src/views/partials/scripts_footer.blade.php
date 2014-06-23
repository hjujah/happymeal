@foreach ($scripts as $script)
	<?php
		$attrs = '';
		foreach ($script['attrs'] as $key => $value) {
			$attrs .= $key . '="' . $value . '" ';
		}
	?>
    <script {{$attrs}} src="{{$script["src"]}}"></script>
    <script>
    	// debug loading / script loading / bootstrapping
    	window.onload = function(){console.log('window loaded');}
    </script>
@endforeach
