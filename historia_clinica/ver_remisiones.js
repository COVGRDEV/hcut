if (CKEDITOR.env.ie && CKEDITOR.env.version < 9) {
    CKEDITOR.tools.enableHtml5Elements(document);
}

CKEDITOR.config.height = 70;
CKEDITOR.config.width = "auto";

var initCKEditorRemisiones = (function (id_obj) {
    var wysiwygareaAvailable = isWysiwygareaAvailable(),
            isBBCodeBuiltIn = !!CKEDITOR.plugins.get("bbcode");

    return function (id_obj) {
        var editorElement = CKEDITOR.document.getById(id_obj);

        //Dependiendo de la disponibilidad del plugin wysiwygare se incia el editor clásico o el editor en línea.
        if (wysiwygareaAvailable) {
            CKEDITOR.replace(id_obj);
        } else {
            editorElement.setAttribute("contenteditable", "true");
            CKEDITOR.inline(id_obj);
        }
    };

    function isWysiwygareaAvailable() {
        if (CKEDITOR.revision == ("%RE" + "V%")) {
            return true;
        }

        return !!CKEDITOR.plugins.get("wysiwygarea");
    }
})();

var arr_textarea_ids = [];
function ajustar_textareas() {
    for (i = 0; i < arr_textarea_ids.length; i++) {
        $("#" + arr_textarea_ids[i]).trigger("input");
    }
}

function mostrar_observaciones(id_div) {
    if ($('#' + id_div).css('display') == 'block') {
        $('#' + id_div).slideUp(400).css('display', 'none');
        $('#' + id_div + '_ver').css('background-image', 'url("../imagenes/ver_derecha.png")');
    } else {
        $('#' + id_div).slideDown(400).css('display', 'block');
        $('#' + id_div + '_ver').css('background-image', 'url("../imagenes/ver_abajo.png")');
    }
}