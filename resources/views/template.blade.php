<!DOCTYPE html>
<html lang="en">
<head>
  <title>@yield('title')</title>
 @include('partials.header')

  <!-- <link rel="stylesheet" type="text/css" href="/css/style.css"> -->

</head>
<body>
<div class="wrapper">
	
	<div id="content">
		{{-- @include('partials.nav') --}}
	  <div class="container-fluid">
			<div class="container">
				@yield('content')
	  	</div>
	  </div>
	</div>
</div>

<!-- <script type="text/javascript" src="/js/script.js"></script> -->

{{-- @include('partials.footer') --}}
</body>
</html>