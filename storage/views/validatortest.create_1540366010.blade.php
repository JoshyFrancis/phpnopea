<?php $this->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>
					<h3>Validation test</h3>
                <div class="panel-body">
                    <?php if ($errors->any()){ ?>
						<div class="alert alert-danger">
							<ul>
								<?php foreach ($errors->all() as $error){ ?>
									<li><?php echo  $error ;?></li>
								<?php } ?>
							</ul>
						</div>
					<?php } ?>
                </div>
                <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="<?php echo  url('validatortest') ;?>" >
					<?php echo  csrf_field() ;?>
					<div class="form-group<?php echo  $errors->has('username') ? ' has-error' : '' ;?>">
						<label for="username" class="col-md-4 control-label">User Name</label>
						<div class="col-md-6">
							<input id="username" type="text" class="form-control" name="username" value="<?php echo  old('username') ;?>" notrequired autofocus>
							<?php if ($errors->has('username')){ ?>
								<span class="help-block">
									<strong><?php echo  $errors->first('username') ;?></strong>
								</span>
							<?php } ?>
						</div>
					</div>
					<div class="form-group<?php echo  $errors->has('title') ? ' has-error' : '' ;?>">
						<label for="title" class="col-md-4 control-label">Title</label>
						<div class="col-md-6">
							<input id="title" type="text" class="form-control" name="title" value="<?php echo  old('title') ;?>" notrequired>
							<?php if ($errors->has('title')){ ?>
								<span class="help-block">
									<strong><?php echo  $errors->first('title') ;?></strong>
								</span>
							<?php } ?>
						</div>
					</div>
					<div class="form-group<?php echo  $errors->has('profilepic') ? ' has-error' : '' ;?>">
						<label for="profilepic" class="col-md-4 control-label">Profile Picture</label>
						<div class="col-md-6"> 
							<input type="file" name="profilepic" accept="image/*" accept2="*/*">
							<?php if ($errors->has('profilepic')){ ?>
								<span class="help-block">
									<strong><?php echo  $errors->first('profilepic') ;?></strong>
								</span>
							<?php } ?>
						</div>	 
					</div> 
					<div class="form-group">
						<div class="col-md-8 col-md-offset-4">
							<button type="submit" class="btn btn-primary">
								Submit
							</button>
							<button type="reset" class="btn btn-primary">
								Reset
							</button>
						</div>
					</div>
				</form>
            </div>
        </div>
    </div>
</div>
<?php $this->stopSection(); ?>
<?php echo $this->view_make('layouts.app',$this->data,true)->render(); ?>
