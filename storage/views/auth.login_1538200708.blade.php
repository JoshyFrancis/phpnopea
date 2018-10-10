<?php $this->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Login</div>
                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="<?php echo  route('login') ;?>">
                        <?php echo  csrf_field() ;?>
                        <div class="form-group<?php echo  $errors->has('email') ? ' has-error' : '' ;?>">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="<?php echo  old('email') ;?>" required autofocus>
								<?php
									//var_dump($errors);
								?>
                                <?php if ($errors->has('email')){ ?>
                                    <span class="help-block">
                                        <strong><?php echo  $errors->first('email') ;?></strong>
                                    </span>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group<?php echo  $errors->has('password') ? ' has-error' : '' ;?>">
                            <label for="password" class="col-md-4 control-label">Password</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>
                                <?php if ($errors->has('password')){ ?>
                                    <span class="help-block">
                                        <strong><?php echo  $errors->first('password') ;?></strong>
                                    </span>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" <?php echo  old('remember') ? 'checked' : '' ;?>> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>
                                <a class="btn btn-link" href="<?php echo  route('password.request') ;?>">
                                    Forgot Your Password?
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->stopSection(); ?>
<?php echo $this->view_make('layouts.app',$this->data,true)->render(); ?>
