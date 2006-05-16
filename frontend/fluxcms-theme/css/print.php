<?php
$handle = opendir(".");
			while (false !== ($file = readdir($handle))) {
					if($file == 'main.css') $files[] = $file;
			}
			closedir($handle);

			$length = count($files);
			$iter = 0;
			while ($iter < $length){
				if($files[$iter] != 'print.php'){
								echo '<br/><br/><br/>****************************************************<br/>';
				echo $files[$iter];
				echo '<br/>****************************************************<br/>';
				
				$str = file_get_contents($files[$iter]);
print(highlight_string ($str));
				}
				$iter = $iter + 1;
			}
			
			
			


?>
