<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="<?php echo $this->e( csrf_token() );?>">
<title>Laranopea</title>
<link href="<?php echo $this->e( asset('css/app.css') );?>" rel="stylesheet">
</head>
<body>
<div id="app">
<nav class="navbar navbar-default navbar-static-top">
<div class="container">
<div class="navbar-header">
<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
<span class="sr-only">Toggle Navigation</span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<a class="navbar-brand" href="<?php echo $this->e( url('/') );?>">
Laranopea
</a>
<a class="navbar-brand" href="<?php echo $this->e( url('/') );?>">
|    <?php echo $this->yieldContent('content3'); ?>
</a>
</div>
<div class="collapse navbar-collapse" id="app-navbar-collapse">
<ul class="nav navbar-nav">
&nbsp;
</ul>
<ul class="nav navbar-nav navbar-right">
<?php if(auth()->guard()->guest()){ ?>
<li><a href="<?php echo $this->e( url('login') );?>">Login</a></li>
<li><a href="<?php echo $this->e( url('register') );?>">Register</a></li>
<?php }else{ ?>
<li class="dropdown">
<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true" v-pre>
<?php echo $this->e( Auth::user()->name );?> <span class="caret"></span>
</a>
<ul class="dropdown-menu">
<li>
<a href="<?php echo $this->e( url('logout') );?>"
onclick="event.preventDefault();
document.getElementById('logout-form').submit();">
Logout
</a>
<form id="logout-form" action="<?php echo $this->e( url('logout') );?>" method="POST" style="display: none;">
<?php echo $this->e( csrf_field() );?>
</form>
</li>
</ul>
</li>
<?php } ?>
</ul>
</div>
</div>
</nav>
<?php $action='index';?>
<?php if($action =='index'){ ?>
<h3>Index</h3>
<?php } ?>
Here comes Links Section
<br>
<?php $this->startSection('links'); ?>
<?php echo $this->showParent(); ?>
<?php
var_dump(get_class($this));
share_data_with_view();
?>
<?php  ?>
<br>
<?php echo $this->yieldContent('content'); ?>
<?php $this->startSection('links2'); ?>
<?php echo $this->showParent(); ?>
<?php echo $this->yieldContent('content2'); ?>
</div>
<script src="<?php echo $this->e( asset('js/app.js') );?>"></script>
</body>
</html>
