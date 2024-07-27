<?php
  /*
   * Написать класс, принимающий параметр - число фотографий
   * Он должен выводить на экран указанное число фото из 
   * json-файла https://jsonplaceholder.typicode.com/photos
   * Расширить его производным классом, который будет сохранять эти фото
   * и также выводить их на страницу, но читая с диска, а не из сети  
   * */

class Photos{
 public int $num_photos;

 public function __construct(int $num_photos){
 	$this->num_photos = $num_photos;
 
 }


 public function getFromAPI(){
	 $url = "https://jsonplaceholder.typicode.com/photos";
         $ch = curl_init();
	 curl_setopt($ch, CURLOPT_URL, $url);
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	 $response = curl_exec($ch);
	 curl_close($ch);
	 $array = json_decode($response, true);
	 return $array;
 }			
			   
				  
 public function showPhotos(){
	echo 'From API...'. '<br>';
	$array_photos = $this->getFromAPI();
	for($i = 0; $i < $this->num_photos; $i++){
	echo '<img src=' . $array_photos[$i]['thumbnailUrl'] . '>';
	}   	
 } 
	  
	   
   }

class Saver extends Photos{
  public string $path = __DIR__ . '\assets\\';
  
  // получить файлы из API и записать в директорию
  public function toWriteFiles(){
	   $array_photos = $this->getFromAPI();
	   for($i = 0; $i < $this->num_photos; $i++){
		   //получить данные из массива (пути к фото)
		   //получить из сети фото по этим путям через API
		   // и записать в директорию
             try{
	     	if(! ($ch = curl_init()))
	     	   throw new Exception('Curl init failed');
		$options = [
			CURLOPT_URL => $array_photos[$i]['thumbnailUrl'],
			CURLOPT_RETURNTRANSFER => true,
		];
		curl_setopt_array($ch, $options);
	        $file = curl_exec($ch);
	       // поместить файл в указанную директорию на сервере
		file_put_contents($this->path . 'photo'. $i . '.png', $file);	
	     } catch(Exception $e){
	     	echo $e -> getMessage();
	     }  	  
	   }
	   echo 'From directory...';
	  
  }

  // извлечь файлы из директории и вывести на страницу
  public function toExtract(){
		        if($dh = opendir($this->path)){
			   echo '<div>'; 
			   while (($file = readdir($dh)) !== false){
				   if(filetype($this->path . $file) == 'file'){
				   echo '<img src="' .'\assets\\' . $file . '">';
				   }		   
			}
			 echo '</div>';
			 closedir($dh);
	 }
   }

}

$obj_photos = new Photos(7);
echo '<div>';
$obj_photos -> showPhotos();
echo '</div>';

$obj_saver = new Saver(7);
echo '<pre>';
print_r($obj_saver->toWriteFiles());
echo '</pre>';
echo $obj_saver -> toExtract();
?>

