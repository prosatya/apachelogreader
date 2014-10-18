<?php
if(isset($_POST['save']) && ($_FILES["uploadFile"]["name"]!="")){
	include 'logreader.php';
	$target_dir = "uploads/";
	$target_dir = $target_dir . basename( $_FILES["uploadFile"]["name"]);
	$uploadFile_type = explode(".", strtolower($_FILES["uploadFile"]["name"]));
	$uploadOk=1;
	$errormessgae = "" ; 

	if (file_exists($target_dir . $_FILES["uploadFile"]["name"])) {
		$errormessgae =  "Sorry, file already exists.";
		$uploadOk = 0;
	}

	if (!($uploadFile_type[1] == "log")) {
		$errormessgae =  "Sorry, only *.log files are allowed.";
		$uploadOk = 0;
	}
	else if($uploadOk==1){
		if (move_uploaded_file($_FILES["uploadFile"]["tmp_name"], $target_dir)) {
			$message="The file ". basename( $_FILES["uploadFile"]["name"]). " has been uploaded.<br>";
		} else {
			echo "Sorry, there was an error uploading your file.<br>";
		}
		$file = $target_dir;
		$log = new apachelogparser();
		$fstats = $log->logfileStats($file);
		$farr = $log->log2arr($file);
		//iterate through the array above and get the info we need from each line
		$tarrayIp=array();
		$noarrayIp=array();
		$noarrayDate=array();
		$hits=array();
		$data=array();
		$i=0;
		foreach ($farr as $key=>$line){       
			$data[]= $log->parselogEntry($line);
			$Ip=$data[$i]['ip']."<br>";
			$tarrayIp[]=$Ip;
			$Date=$data[$i]['date']."<br>";

			if(!in_array($Ip, $noarrayIp)){
				$noarrayIp[]=$Ip;
			}
			if(!in_array($Date, $noarrayDate)){
				$noarrayDate[]=$Date;
			}
			$i++;       
		}
		  
		$total=  count($tarrayIp);
		$no=  count($noarrayIp);
		$avg=($total/$no);
	}  
}
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Log File</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
        <link href="style.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
	<?php if(isset($_POST['save']) && ($_FILES["uploadFile"]["name"]!="") && ($uploadOk ==1)){ ?>
	<div id="result-main">
            
	<hr>
		<h1>Results</h1>
	<hr>
	<?php 
    if(!empty($noarrayDate)){
       $TotalHits=0;
       $z=1;
       foreach($noarrayDate as $key=>$date)
           {
                $j=0;
                $data1=array();
                $allIps=array();
                $uniqeIps=array();
          
              foreach($farr as $key=>$line)
                {
                    $data1[]= $log->parselogEntry($line);
                    $Date=$data1[$j]['date']."<br>";
                    $tips=$data1[$j]['ip'];
                    if($date==$Date){
                        $allIps[$j]= $data1[$j]['ip']."<br>";

                        if(!in_array($data1[$j]['ip'], $uniqeIps)){

                            $uniqeIps[]=$data1[$j]['ip'];
                        }

                    }
                   $j++; 
           }
 ?>
          <div class="table">
                
                <h3>Day <?php echo $z;?></h3>
  
                <div class="row" >
                    
                    <p><?php echo "Total hits on day".$z." = ".count($allIps);?></p>
                    <p><?php echo "Uniqe Visiter on day".$z." = ".count($uniqeIps);?></p>
                    
                </div>
                
            </div>
   <?php   $z++;  }?>
            
            
            <div class="row2" >
                    
                    <p><?php echo "Average number of hits / visitor(for the whole period of ".count($noarrayDate)."days)= ".round($avg,2);?></p>
                    
                </div>
            <?php }?>  
            <div align="center"><a href="index.php">Upload More</a></div>
            
        </div>
	<?php }else{ ?> 
        <div id="form-main">
            
            <h1>Upload Log File</h1>
            
            <form method="POST" enctype="multipart/form-data">
                <div>
				
			
				
                <label>Upload File</label>
                <input type="file" name="uploadFile" required="true">
                </div>
                <div align="center">
                    <input class="save-btn" type="submit" name="save" value="save">
                </div>
				<div align="center">
					<?php if(isset($uploadOk) && $uploadOk==0){?>
						<label style="color:red">
						<?php echo $errormessgae ; ?> 
						</label>
						<br/>
					<?php }?> 
				</div>
            </form>
            
        </div>
		<?php } ?> 
    </body>
</html>
