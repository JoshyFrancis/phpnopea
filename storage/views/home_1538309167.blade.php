<?php $this->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>
                <div class="panel-body">
                     
                        <div class="alert alert-success">
                            <?php echo  date('l jS \of F Y h:i:s A') ;?> @ <?php echo  date('now') ;?>
                        </div>
                     
						
                    You are logged in!
                    <br>
                    <?php 
						echo $test;
						var_dump($arr);
						/*
						echo "<pre></pre>";
						foreach(debug_backtrace() as $item){
							echo "File : " . $item['file']  . ", line : " . $item['line'] . ", function : " . $item['function']."<br>"; 
							//var_dump($item['args']);
						}
						echo "</pre>";
						*/
                    ?>
                    <br>
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
