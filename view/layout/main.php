<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>
<div class="container center">
    <div class="form-load">
        <div id="status_message"></div>
        <form id="formload" onsubmit="return submitForm(this);">
            <input type="file" accept="image/jpeg, image/gif, image/png" id="fileimage" name="imagefile" onchange="fileSelected();"/>
            <input type="hidden" id="old-name" value="">
            <button id="saveimage" class="center">Save</button>
        </form>
        <div class="build-folder">
            <input type="text" class="center namefolder" id="name-folder" value="" placeholder="Enter folder name">
            <button onclick="showField();" class="center" id="add-dir">Add Folder</button>
        </div>
    </div>
    <div class="gallery-block" id="gallery"></div>
</div>
<script type="text/javascript" src="/js/main.js"></script>
</body>
</html>