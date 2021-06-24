// Place post editor
let quill = new Quill('#editor-container', {
    modules: {
        toolbar: [
            [{ header: [1, 2, false] }],
            ['bold', 'italic', 'underline'],
            ['code-block']
        ]
    },
    placeholder: 'Enter Text',
    theme: 'snow'
});