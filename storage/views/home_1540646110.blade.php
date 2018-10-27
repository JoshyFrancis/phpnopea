<?php $this->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>
				<?php
					echo "Going to skip Single Line comment<br>";
					
					echo "skipped<br>";



					 
					echo "skipped multiline comment <br>";
				?>
                <div class="panel-body">
                     
                        <div class="alert alert-success">
                            <?php echo  date('l jS \of F Y h:i:s A') ;?> @ <?php echo  date('now') ;?>
                        </div>
                     
						
                    You are logged in!
                    <br>
                    <?php 
						echo 'shared data : ' . $shared_data;
						echo '<br>';
						echo $test;
						var_dump($arr);







						
						$some_data='this should be avaialble to the followinf view';
                    ?>
                    <?php echo $this->view_make('include_test',$this->data,true)->render(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->stopSection(); ?>
<?php $this->startSection('content2'); ?>
	Section Content 2
<?php $this->stopSection(); ?>
<?php echo $this->view_make('layouts.app',$this->data,true)->render(); ?>
