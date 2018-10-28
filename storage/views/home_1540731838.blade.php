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
				<?php
					echo "Going to skip Single Line comment<br>";
					// commented line contians vars<?php echo $this->curly_braces_open;?>test<?php echo $this->curly_braces_close;?> m
					echo "skipped<br>";//comment <?php echo $this->curly_braces_open;?>skipped <?php echo $this->curly_braces_close;?> m
					/*
						This is a 
						multiline comment contains vars <?php echo $this->curly_braces_open;?>test2<?php echo $this->curly_braces_close;?> m
					 */
					echo "skipped multiline comment <br>";
					$data0='data0';
				?>
				<script>
					
					var div=document.createElement('div');
						div.innerHTML='Added by Javascript';//comment <?php echo $this->curly_braces_open;?>skipped <?php echo $this->curly_braces_close;?> m
						div.innerHTML+='<br>Added by <?php echo 'php';//comment <?php echo $this->curly_braces_open;?>skipped <?php echo $this->curly_braces_close;?> ?>';//comment <?php echo $this->curly_braces_open;?>skipped <?php echo $this->curly_braces_close;?> m
						div.innerHTML+='<br>data0:<?php echo $data0;?> m';
						div.innerHTML+='<br>Javascript <?php echo $this->curly_braces_open;?>var1<?php echo $this->curly_braces_close;?> : <?php echo $this->curly_braces_open;?>var2<?php echo $this->curly_braces_close;?> m';
					/*
						This is a 
						multiline comment contains vars <?php echo $this->curly_braces_open;?>test2<?php echo $this->curly_braces_close;?> in script block
					 */	
					document.body.appendChild(div);
					
				</script>
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
                    <?php $_view=$this->view_make('include_test',$this);$_view->compile();include $_view->storage_path; ?>
                    <?php if($arr[1]===2){ ?>
						<?php echo  $arr[1] ;?>
						<br>
					<?php } ?>
                    <?php foreach($arr as $a){ ?>
						<?php echo $a;?>
						<br>
					<?php } ?>
                    <?php $_view=$this->view_make('include_test2',$this);$_view->compile();include $_view->storage_path; ?>
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
<?php echo $this->view_make('layouts.app',$this)->compile_render(); ?>
