"use strict";
const form = document.getElementById('species-form');
const nombreComunInput = document.getElementById('nombreComun');
const ecosistemaSelect = document.getElementById('ecosistema');
const descripcionTextarea = document.getElementById('descripcion');
const fotografiaInput = document.getElementById('fotografia');
const imagePreview = document.getElementById('image-preview');
const imagePlaceholder = document.getElementById('image-preview-placeholder');
function handleSubmit(event) {
    var _a, _b;
    event.preventDefault();
    const tipoSeleccionado = document.querySelector('input[name="tipo"]:checked').value;
    const datosDelFormulario = {
        nombreComun: nombreComunInput.value,
        tipo: tipoSeleccionado,
        ecosistemaId: ecosistemaSelect.value,
        descripcion: descripcionTextarea.value,
        nombreArchivo: ((_b = (_a = fotografiaInput.files) === null || _a === void 0 ? void 0 : _a[0]) === null || _b === void 0 ? void 0 : _b.name) || 'No se seleccionó archivo'
    };
    console.log('--- Simulación de Envío ---');
    console.log(datosDelFormulario);
    alert('¡Datos capturados! Revisa la consola del navegador (F12).');
    form.reset();
}
fotografiaInput.addEventListener('change', () => {
    var _a;
    const file = (_a = fotografiaInput.files) === null || _a === void 0 ? void 0 : _a[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            var _a;
            const imageUrl = (_a = e.target) === null || _a === void 0 ? void 0 : _a.result;
            imagePreview.src = imageUrl;
            imagePreview.classList.remove('hidden');
            imagePlaceholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
    else {
        imagePreview.src = '';
        imagePreview.classList.add('hidden');
        imagePlaceholder.classList.remove('hidden');
    }
});
form.addEventListener('submit', handleSubmit);
