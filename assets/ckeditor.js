// On initialise CKEditor
var editors = document.querySelectorAll( '.editor' );
var form = editors[0].form.id

editors.forEach(editor5 => {
    ClassicEditor
        .create( editor5 )
        .then(editor => {
            var formHidden = editor.sourceElement.value;
            editor.setData(formHidden);

            document.getElementById(form).addEventListener('submit', function(e){
                e.preventDefault();
                var formElemContent = editor.getData();
                editor.sourceElement.value = formElemContent;
                return;
            })
        })
        .catch( error => {
            console.error( error );
        });
});

document.getElementById(form).addEventListener('submit', function(e){
    setTimeout(function(){
        document.getElementById(form).submit();
    }, 100);
})