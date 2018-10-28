<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo  csrf_token() ;?>">

    <title>Laraquick</title>

    <!-- Styles -->
    <link href="<?php echo  asset('css/app.css') ;?>" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="<?php echo  url('/') ;?>">
                        Laraquick
                    </a>

                    <a class="navbar-brand" href="<?php echo  url('/') ;?>">
						|    <?php echo $this->yieldContent('content3'); ?>
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
						
                        <!-- Authentication Links -->
                        <?php if(auth()->guard()->guest()){ ?>
                            <li><a href="<?php echo  route('login') ;?>">Login</a></li>
                            <li><a href="<?php echo  route('register') ;?>">Register</a></li>
                        <?php }else{ ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true" v-pre>
                                    <?php echo  Auth::user()->name ;?> <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="<?php echo  route('logout') ;?>"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="<?php echo  route('logout') ;?>" method="POST" style="display: none;">
                                            <?php echo  csrf_field() ;?>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </nav>
        <br>
        Here comes Links Section
        <br>
		<?php $this->startSection('links'); ?>
		
		<?php echo $this->showParent(); ?>
		<br>
		<?php $this->startSection('links2'); ?>				
			
		<?php echo $this->showParent(); ?>
		<br>
        <?php echo $this->yieldContent('content'); ?>
		<?php echo $this->yieldContent('content2'); ?>
		
    </div>

    <!-- Scripts -->
    <script src="<?php echo  asset('js/app.js') ;?>"></script>
</body>
</html>
