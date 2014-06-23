<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js oldie lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js oldie lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js oldie lt-ie9"> <![endif]-->
<!--[if IE 9]>         <html class="no-js oldie ie9"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js"> <!--<![endif]-->
<!--
Filip Arneric and Clement Grellier
 _             _   _______ _____ _____  _   _  _____ _   _ 
| |           | | / /_   _|_   _/  __ \| | | ||  ___| \ | |
| |__  _   _  | |/ /  | |   | | | /  \/| |_| || |__ |  \| |
| '_ \| | | | |    \  | |   | | | |    |  _  ||  __|| . ` |
| |_) | |_| | | |\  \_| |_  | | | \__/\| | | || |___| |\  |
|_.__/ \__, | \_| \_/\___/  \_/  \____/\_| |_/\____/\_| \_/
        __/ |                                              
       |___/                                                                                                                               
-->
	<head>
		<meta charset="utf-8">
		<title>{{$meta_title}}</title>

		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="author" content="Kitchen">
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />

		<link rel="shortcut icon" href="/favicon.png">
		
		<!-- Generated CSS file -->
		<!--<link href="{{ url() }}/css/main.min.css?v=115" rel="stylesheet">-->
		<link href="{{ url() }}/css/main.css?v=12" rel="stylesheet">

		<script>
    	var ie8 = false,
    		ie9 = false,
    		ie = false;
   		</script>
   		 		
		<!--[if IE]>
			<script> var ie = true; </script>
			<script src='{{ url() }}/js/vendor/html5shiv.js'></script>
			<script src='{{ url() }}/js/vendor/respond.js'></script>
		<![endif]-->

		<!--[if lte IE 9]>
			<script> var ie9 = true; </script>
		<![endif]-->

		<!--[if lt IE 9]>
			<script> var ie8 = true; </script>
		<![endif]-->
		
	</head>
	
	<body>
		<div id="container">
		
			<!-- loader -->
			<div id='loader'>
				<div class='vCenter'>
					<div id='holder' class='vCenterKid'>
						<div class='relative'>
							<!--[if lte IE 9]>
								<img src='{{ url() }}/img/loading/compass.png' />
								<img id='arrow' src='{{ url() }}/img/loading/arrow.png' />
							<![endif]-->
							<img class="hiddenIe" src='{{ url() }}/img/loading/compass.svg' />
							<img id='arrow' class="hiddenIe" src='{{ url() }}/img/loading/arrow.svg' />
						</div>
					</div>
				</div>
			</div>

				
			<!-- Navigation -->
			<div id='navigation'>{{ $navigation }}</div>

			<!-- Page -->
			<div id='main'>
				{{ $content }}
			</div>

			<!-- Intro Loader -->
			<div id="intro"></div>

		</div>
		
		<script>
			var absurl = '{{ url() }}';
			var language = '{{ app::getLocale() }}';
		</script>
<!-- 		<script data-main="{{ url() }}/js/app.min.js?v=115" src="{{ url() }}/js/vendor/require.js"></script> -->
		<script data-main="{{ url() }}/js/load" src="{{ url() }}/js/vendor/require.js"></script>
		
		 <!-- Google Analytics -->
		 <script>   
        	var _gaq=[["_setAccount","UA-50551889-1"],["_trackPageview"]];
(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.async=1;
g.src=("https:"==location.protocol?"//ssl":"//www")+".google-analytics.com/ga.js";
s.parentNode.insertBefore(g,s)}(document,"script"));
	   </script>
	   
	    <!-- Google Code for Remarketing Tag -->
		<script type="text/javascript">
		/* <![CDATA[ */
		var google_conversion_id = 987669425;
		var google_custom_params = window.google_tag_params;
		var google_remarketing_only = true;
		/* ]]> */
		</script>
		<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
		</script>
		<noscript>
		<div style="display:inline;">
		<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/987669425/?value=0&amp;guid=ON&amp;script=0"/>
		</div>
		</noscript>
		
		<!-- Seznam Targeting -->
		<script type="text/javascript">
		/* <![CDATA[ */
		var seznam_retargeting_id = 15452;
		/* ]]> */
		</script>
		<script type="text/javascript"  src="//c.imedia.cz/js/retargeting.js"></script>
	   
	</body>
</html>