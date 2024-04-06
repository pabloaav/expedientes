$(document).ready(function() {

    // obtiene el valor del select al iniciar la vista para asignarle el color cargado previamente (vista editar Tipo documento)
    window.onload = function() {
        let colorSelectedEdit = document.getElementById("color").value;
        var selectStyleEdit = document.getElementById("color");

        if (colorSelectedEdit !== null) {
            selectStyleEdit.style.background = colorSelectedEdit;
        }
    };

    // detecta el cambio de opcion en el select para que lo pinte de acuerdo al seleccionado
    $(function() {
        $('#color').on('change', changeColor);
    });

    function changeColor() {
        let colorSelected = document.getElementById("color").value; // obtengo el valor del option seleccionado
        var selectStyle = document.getElementById("color"); // obtengo el select completo
        // console.log(colorSelected);

        if (colorSelected !== null) {
            selectStyle.style.background = colorSelected; // asigno color de fondo que está seleccionado
        }
    }
});