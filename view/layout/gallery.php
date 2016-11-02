<?php
$r = new \Http\Request();
$all_images = $this->content['all_images'];
$folder = $this->content['folder_image'];
$current_folder = $this->content['current_folder'];
if($current_folder!='/'){
    echo '<div class="item-galery"><a href="'.$r->getUrlGallery('..').'"><img class="folder_img" width="150px" height="150px" src="/images/folder.png"></a><div class="name-dir"><a>..</a></div></div>';
}
if (is_array($all_images)) {
    foreach ($all_images as $image) {
        if(is_dir($_SERVER['DOCUMENT_ROOT'].$folder.$current_folder.'/'.$image['file'])){
            echo '<div class="item-galery"><a href="'.$image['url'].'"><img class="folder_img" width="150px" height="150px" src="/images/folder.png"></a><div ondblclick="reNameItem(\''.$image['file'].'\')" class="name-dir"><a>'.$image['file'].'</a></div><a onclick="deleteElement(\''.$image['file'].'\')" class="deleteimage" data-image="/gallery/'.$image['file'].'">x</a></div>';
        } else {
            echo '<div class="item-galery"><img src="'.$folder.$current_folder.'/'.$image['file'].'"><a onclick="deleteElement(\''.$image['file'].'\')" class="deleteimage" data-image="/gallery/'.$image['file'].'">x</a><div class="item-name" ondblclick="reNameItem(\''.$image['file'].'\')">'.$image['file_name'].'</div></div>';
        }
    }
} else {
    echo $all_images;
}
