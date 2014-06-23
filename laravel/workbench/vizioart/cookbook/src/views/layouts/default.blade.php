<!doctype html>
<html>
<head>
	@include('cookbook::partials.head')
</head>
<body>
	@include('cookbook::partials.header')
	<div id="main" role="main">
		{{ $content }}
	</div>
	
	@include('cookbook::partials.footer')
	{{ $pageScripts }}
</body>
</html>