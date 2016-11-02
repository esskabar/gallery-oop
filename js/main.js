

// (1)


// (2)
function hideMessage() {
    document.getElementById('message').style.display = "none";
}



function loadingGallery() {
    var gallery = new XMLHttpRequest();
    gallery.onreadystatechange = function() {
        if (gallery.readyState == 4 && gallery.status == 200) {
            document.getElementById("gallery").innerHTML = gallery.responseText;
        }
    };
    gallery.open("POST", "/ajax/loadallimages", true);
    gallery.send();
}

/**
 *
 * @param route
 * @param data
 * @returns {boolean}
 */
function ajaxQuery(route, data) {
    if(data!=undefined){
        var data_query = new FormData();
        if(Array.isArray(data)){
            console.log(data);
            for (var i = 0; i < data.length; i++) {
                data_query.append(data[i][0],data[i][1]);
            }
        } else {
            data_query.append('data', data);
        }

    }
    var ajax_query = new XMLHttpRequest();
    ajax_query.onreadystatechange = function() {
        if (ajax_query.readyState == 4 && ajax_query.status == 200) {
            document.getElementById("status_message").innerHTML = ajax_query.responseText;
            loadingGallery();
        }
    };
    ajax_query.open("POST", route, true);
    ajax_query.send(data_query);
    return false;
}

/**
 *
 * @param FormElement
 * @returns {boolean}
 */
function submitForm(FormElement){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            document.getElementById("status_message").innerHTML = xhttp.responseText;
            loadingGallery();
        }
    };
    xhttp.open("POST", "/ajax/downloadimage", true);
    xhttp.send(new FormData (FormElement));
    return false;
}

/**
 *
 * @param element
 */
function deleteElement(element){
    ajaxQuery("/ajax/removefile",element);
    setInterval(hideMessage, 3000);
    loadingGallery();
}
/**
 *
 * @returns {boolean}
 */
function showField() {
    if(document.getElementById("name-folder").style.display=='none' || document.getElementById("name-folder").style.display==''){
        document.getElementById("name-folder").style.display = "block";
    }else{
        var folderName = document.getElementById("name-folder").value;
        console.log(folderName);
        ajaxQuery("/ajax/createfolder",folderName);
        if(document.getElementById("name-folder").style.display=='block'){
            document.getElementById("name-folder").style.display = "none";
            document.getElementById("name-folder").value="";
            loadingGallery();
        }else{
            document.getElementById("name-folder").style.display = "block";
        }
        console.log(document.getElementById("name-folder").style.display);
    }
    //setInterval(hideMessage, 3000);
    return false;
}
/**
 *
 * @param nameOldFolder
 * @returns {boolean}
 * @constructor
 */
function reNameItem(nameOldFolder) {
    if(document.getElementById("name-folder").style.display=='none' || document.getElementById("name-folder").style.display==''){
        document.getElementById("name-folder").style.display = "block";
        document.getElementById("old-name").value = nameOldFolder;
        document.getElementById("name-folder").value = nameOldFolder;
        document.getElementById("add-dir").setAttribute('id', 'reName-dir');
        document.getElementById("reName-dir").setAttribute('onclick', 'reNameDir();');
        document.getElementById("name-folder").setAttribute('id', 'newFolderName');
        document.getElementById("reName-dir").innerHTML = 'Rename folder';
    }else{
        var folderName = document.getElementById("name-folder").value;
        console.log(folderName);
        ajaxQuery("/ajax/renamefolder",folderName);
        if(document.getElementById("name-folder").style.display=='block'){
            document.getElementById("name-folder").style.display = "none";
            document.getElementById("name-folder").value="";
            loadingGallery();
        }else{
            document.getElementById("name-folder").style.display = "block";
        }
        document.getElementById("add-dir").value = 'New folder';
    }
    setInterval(hideMessage, 3000);
    return false;
}

/**
 *
 * @constructor
 */
function reNameDir(){
    var newName = document.getElementById("newFolderName").value;
    var oldName = document.getElementById("old-name").value;
    var folder_name = [ [ 'newname', newName], [ 'oldname',oldName ] ]
    ajaxQuery("/ajax/renamefolder",folder_name);
    document.getElementById("newFolderName").setAttribute('id', 'name-folder');
    document.getElementById("name-folder").style.display = "none";
    document.getElementById("reName-dir").innerHTML = 'Add Folder';
    document.getElementById("reName-dir").setAttribute('id', 'add-dir');
    document.getElementById("add-dir").setAttribute('onclick', 'showField();');
   // setInterval(hideMessage, 3000);
}

function fileSelected() {
    var file = document.getElementById('fileimage').files[0];
    if (file) {
        var fileSize = 0;
        if (file.size > 1024 * 1024)
            fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
        else
            fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';
        console.log(file);
        document.getElementById('fileName').innerHTML = 'Name: ' + file.name;
        document.getElementById('fileSize').innerHTML = 'Size: ' + fileSize;
        document.getElementById('fileType').innerHTML = 'Type: ' + file.type;
    }
}

document.addEventListener("DOMContentLoaded", loadingGallery);
