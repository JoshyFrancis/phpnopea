
<?php $this->startSection('links'); ?>
	<?php $this->startParent(); ?>
			Links
	<br>		
<?php $this->stopSection(); ?>
<?php $this->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>
					<h4>Include another view manually</h4>
					<?php
						include base_path().'/resources/views/include_view.php';
					?>
					<br>
                 <?php foreach($arr as $a){ ?>
				<?php
					echo "Going to skip Single Line comment<br>";
					// commented line contians vars {{test}} m
					echo "skipped<br>";//comment {{skipped }} m
					/*
						This is a 
						multiline comment contains vars {{test2}} m
					 */
					echo "skipped multiline comment <br>";
					$data0='This data is from PHP';
				?>
				<script>
					
					var div=document.createElement('div');
						div.innerHTML='Added by Javascript';
						div.innerHTML+='<br>Added by <?php echo 'php';//comment {{skipped }} ?>';//comment {{skipped }} m
						div.innerHTML+='<br>data0:<?php echo $data0;?> m';
						div.innerHTML+='<br>Javascript {{var1}} : {{var2}} m';
					/*
						This is a 
						multiline comment contains vars {{test2}} in script block
					 */	
					document.body.appendChild(div);
					
				</script>
					<br>
					<?php echo $a;?>
				<?php } ?>
				
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
						/*
						echo "<pre></pre>";
						foreach(debug_backtrace() as $item){
							echo "File : " . $item['file']  . ", line : " . $item['line'] . ", function : " . $item['function']."<br>"; 
							//var_dump($item['args']);
						}
						echo "</pre>";
						*/
						$some_data='this should be avaialble to the following view';
                    ?>
                    <br>
                    <?php $_view=$this->view_include('include_test',get_defined_vars());echo $_view->render(); ?>
                    <?php if($arr[1]===2){ ?>
						<?php echo  $arr[1] ;?>
						<br>
					<?php } ?>
                    <?php foreach($arr as $a){ ?>
						<?php echo $a;?>
						<br>
					<?php } ?>
                    <?php $_view=$this->view_include('include_test2',get_defined_vars());echo $_view->render(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->stopSection(); ?>
<?php $this->startSection('links2'); ?>
	<?php $this->startParent(); ?>
			Links2
	<br>
<?php $this->stopSection(); ?>
<?php $this->startSection('content2'); ?>
	Section Content 2
<?php $this->stopSection(); ?>
<?php $this->startSection('content3'); ?>
	Section Content 3
<?php $this->stopSection(); ?>
<?php echo $this->view_make('layouts.app',$this)->render(); ?>