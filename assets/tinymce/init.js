tinymce.init({
    selector: 'textarea#mytextarea',
    language: 'fr_FR',
    plugins: 'advlist autolink lists link image charmap preview searchreplace visualblocks code codesample fullscreen insertdatetime media table image code',
    toolbar: 'fullscreen code preview | undo redo | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | numlist bullist | forecolor backcolor removeformat | image media link',

    image_title: true, 
    // enable automatic uploads of images represented by blob or data URIs
    automatic_uploads: true,
    // add custom filepicker only to Image dialog
    file_picker_types: 'image',
    file_picker_callback: function(cb, value, meta) {
    var input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');

    input.onchange = function() {
    var file = this.files[0];
    var reader = new FileReader();

    reader.onload = function () {
    var id = 'blobid' + (new Date()).getTime();
    var blobCache = tinymce.activeEditor.editorUpload.blobCache;
    var base64 = reader.result.split(',')[1];
    var blobInfo = blobCache.create(id, file, base64);
    blobCache.add(blobInfo);

    // call the callback and populate the Title field with the file name
    cb(blobInfo.blobUri(), { title: file.name });
    };
    reader.readAsDataURL(file);
    };

    input.click();
    }
});

console.log('Tiny marche bien');