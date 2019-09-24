

<?php $this->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Register</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="<?php echo $this->e( url('register') );?>">
                        <?php echo $this->e( csrf_field() );?>

                        <div class="form-group<?php echo $this->e( $errors->has('name') ? ' has-error' : '' );?>">
                            <label for="name" class="col-md-4 control-label">Name</label>

                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control" name="username" value="<?php echo $this->e( old('username') );?>" required autofocus>

                                <?php if($errors->has('username')){ ?>
                                    <span class="help-block">
                                        <strong><?php echo $this->e( $errors->first('username') );?></strong>
                                    </span>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="form-group<?php echo $this->e( $errors->has('email') ? ' has-error' : '' );?>">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="<?php echo $this->e( old('email') );?>" required>

                                <?php if($errors->has('email')){ ?>
                                    <span class="help-block">
                                        <strong><?php echo $this->e( $errors->first('email') );?></strong>
                                    </span>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="form-group<?php echo $this->e( $errors->has('password') ? ' has-error' : '' );?>">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                <?php if($errors->has('password')){ ?>
                                    <span class="help-block">
                                        <strong><?php echo $this->e( $errors->first('password') );?></strong>
                                    </span>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Register
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->stopSection(); ?>
<?php echo $this->view_make('layouts.app',$this)->render(); ?>