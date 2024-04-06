CKEDITOR.editorConfig = function(config) {
    config.toolbarGroups = [
        { name: 'document', groups: ['document', 'doctools', 'mode'] },
        { name: 'tools', groups: ['tools'] },
        { name: 'clipboard', groups: ['clipboard', 'undo'] },
        { name: 'editing', groups: ['find', 'selection', 'spellchecker', 'editing'] },
        { name: 'forms', groups: ['forms'] },
        '/',
        { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
        { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph'] },
        { name: 'links', groups: ['links'] },
        { name: 'insert', groups: ['insert'] },
        '/',
        { name: 'styles', groups: ['styles'] },
        { name: 'colors', groups: ['colors'] },
        { name: 'others', groups: ['others'] },
        { name: 'about', groups: ['about'] }
    ];

    config.removeButtons = 'Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Language,BidiRtl,BidiLtr,CreateDiv,Link,Unlink,Anchor,Smiley,SpecialChar,Iframe,ShowBlocks,About,ExportPdf,Save,Templates,Source,Cut,Undo,Find,SelectAll,Replace,Redo,Copy,NewPage,PasteFromWord,PasteText,Paste,Bold,CopyFormatting,NumberedList,Outdent,Blockquote,JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock,Indent,BulletedList,RemoveFormat,Italic,Underline,Strike,Subscript,Superscript,PageBreak,HorizontalRule,Table,Image,Styles,Format,Font,FontSize,BGColor,TextColor';
};