
document.addEventListener('focusin', function (e) {
    if (e.target.closest('.ck') !== null) {
        e.stopImmediatePropagation();
    }
}, true);

let add_editorInstance;

ClassicEditor.create(document.querySelector("#descriptionEditor"), {
    ckfinder: {
        uploadUrl: editorImgUrl
    },
    image: {
        toolbar: [
            'imageTextAlternative',
            '|',
            'imageStyle:alignLeft',
            'imageStyle:full',
            'imageStyle:alignRight',
            '|',
            'resizeImage:50',
            'resizeImage:75',
            'resizeImage:original'
        ],
        resizeUnit: 'px',
        resizeOptions: [
            {
                name: 'resizeImage:original',
                label: 'Original',
                value: null
            },
            {
                name: 'resizeImage:50',
                label: '50%',
                value: '50'
            },
            {
                name: 'resizeImage:75',
                label: '75%',
                value: '75'
            }
        ],
        styles: ['full', 'alignLeft', 'alignRight']
    },
})
.then(editor => {
    add_editorInstance = editor;
    console.log("CKEditor initialized with image resizing");
})
.catch(error => {
    console.error("CKEditor Error:", error);
});


let edit_editorInstance;
ClassicEditor.create(document.querySelector("#descriptionEditor_edit"), {
    ckfinder: {
        uploadUrl: editorImgUrl
    },
    image: {
        toolbar: [
            'imageTextAlternative',
            '|',
            'imageStyle:alignLeft',
            'imageStyle:full',
            'imageStyle:alignRight',
            '|',
            'resizeImage:50',
            'resizeImage:75',
            'resizeImage:original'
        ],
        resizeUnit: 'px',
        resizeOptions: [
            {
                name: 'resizeImage:original',
                label: 'Original',
                value: null
            },
            {
                name: 'resizeImage:50',
                label: '50%',
                value: '50'
            },
            {
                name: 'resizeImage:75',
                label: '75%',
                value: '75'
            }
        ],
        styles: ['full', 'alignLeft', 'alignRight']
    },
})
.then(editor => {
    edit_editorInstance = editor;
    console.log("CKEditor initialized with image resizing");
})
.catch(error => {
    console.error("CKEditor Error:", error);
});

// Function to set new data dynamically
function updateEditorContent(newContent) {
    if (edit_editorInstance) {
        edit_editorInstance.setData(newContent);
    }
}