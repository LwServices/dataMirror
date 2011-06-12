<?php
function getFilesArray($myRootPath){
 if ($myHandle = opendir($myRootPath)) {
  while (false !== ($myFile = readdir($myHandle))) {
   if ($myFile != "." && $myFile != "..") {
    if(is_file($myRootPath.$myFile)) $dirArray[]=$myFile;
   }
  }
  closedir($myHandle);
 };
 return $dirArray;
};  
  
$eventName = $modx->event->name;
switch($eventName) {
    case 'OnWebPageInit':
    // claar cache
          $modx->cacheManager->clearCache();  
    
    // restore chunks
      $myRootPath=$modx->getOption('assets_path')."mirror/chunks/";
      $dirArray=getFilesArray($myRootPath);

      foreach($dirArray as $myCurrent){
        $chunk_id=(int)preg_replace('/-.*html/','',$myCurrent);
        echo $chunk_id.',<br/>';
	if($chunk_id){
          $fileandpath=$myRootPath.$myCurrent;
          $content=file_get_contents($fileandpath);
          $myChunk=$modx->getObject('modChunk',$chunk_id);
	  if($myChunk){
          	$myChunk->setContent($content);
          	$myChunk->save();
	  };
        };
      };
    //restore Resources
      $myRootPath=$modx->getOption('assets_path')."mirror/resources/";
      $dirArray=getFilesArray($myRootPath);

      foreach($dirArray as $myCurrent){
        $resId=(int)preg_replace('/-.*html/','',$myCurrent);
        if($resId){
          $fileandpath=$myRootPath.$myCurrent;
          $content=file_get_contents($fileandpath);
          $myRes=$modx->getObject('modResource',$resId);
	  if($myRes){
          	$myRes->setContent($content);
          	$myRes->save();
	  };
        };
      };

    //restore Snippets
      $myRootPath=$modx->getOption('assets_path')."mirror/snippets/";
      $dirArray=getFilesArray($myRootPath);

      foreach($dirArray as $myCurrent){
        $snipId=(int)preg_replace('/-.*php/','',$myCurrent);
        if($snipId){
          $fileandpath=$myRootPath.$myCurrent;
          $content=file_get_contents($fileandpath);
          $mySnip=$modx->getObject('modSnippet',$snipId);
          if($mySnip){
	  	$mySnip->setContent($content);
          	$mySnip->save();
	  };  
        };
      };    

    //restore Templates
      $myRootPath=$modx->getOption('assets_path')."mirror/templates/";
      $dirArray=getFilesArray($myRootPath);

      foreach($dirArray as $myCurrent){
        $tempId=(int)preg_replace('/-.*php/','',$myCurrent);
        if($tempId){
          $fileandpath=$myRootPath.$myCurrent;
          $content=file_get_contents($fileandpath);
          $myTemp=$modx->getObject('modTemplate',$tempId);
          if($myTemp){
	  	$myTemp->setContent($content);
          	$myTemp->save();
	  };
        };
      };   

      break;
    
    case 'OnChunkFormSave':    
      // mode   Either 'upd' or 'new', depending on the circumstance.
      // $mode=$modx->event->params['mode'];
      // chunk   A reference to the modChunk object.
      // id   The ID of the chunk.
      $chunkId=$modx->event->params['id'];
      $myChunk=$modx->event->params['chunk'];
      $myContent=$myChunk->getContent();
      $chunkFileName=$modx->getOption('assets_path')."mirror/chunks/"
        .$chunkId."-".$myChunk->get('name').".html";
      file_put_contents($chunkFileName,$myContent);
      @chmod($chunkFileName, 0664);
      break;
    
    case 'OnDocFormSave':
      //mode   Either 'new' or 'upd', depending on the circumstances.
      //resource   A reference to the modResource object.
      //id   The ID of the Resource. Will be 0 for new Resources.
      $resId=$modx->event->params['id'];
      $myRes=$modx->event->params['resource'];
      $myContent=$myRes->getContent();
      $resFileName=$modx->getOption('assets_path')."mirror/resources/"
        .$resId.".html";//$myRes->get('pagetitle')
      file_put_contents($resFileName,$myContent);
      @chmod($resFileName, 0664);    
      break;

    case 'OnSnipFormSave':
      //mode   Either 'new' or 'upd', depending on the circumstances.
      //resource   A reference to the modResource object.
      //id   The ID of the Resource. Will be 0 for new Resources.
      $snipId=$modx->event->params['id'];
      $mySnip=$modx->event->params['snippet'];
      $myContent=$mySnip->getContent();
      $snipFileName=$modx->getOption('assets_path')."mirror/snippets/"
        .$snipId."-".$mySnip->get('name').".php";
      file_put_contents($snipFileName,$myContent); 
      @chmod($snipFileName, 0664);
      //$cacheManager = $modx->getCacheManager();
      $modx->cacheManager->clearCache();  
      break;    

    case 'OnTempFormSave':
      //mode   Either 'new' or 'upd', depending on the circumstances.
      //resource   A reference to the modResource object.
      //id   The ID of the Resource. Will be 0 for new Resources.
      $tempId=$modx->event->params['id'];
      $myTemp=$modx->event->params['template'];
      $myContent=$myTemp->getContent();
      $tempFileName=$modx->getOption('assets_path')."mirror/templates/"
        .$tempId."-".$myTemp->get('templatename').".html";
      file_put_contents($tempFileName,$myContent); 
      @chmod($tempFileName, 0664);  
      break;   
    
  default:
      break;
}
  
