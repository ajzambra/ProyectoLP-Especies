"use strict";
const form = document.getElementById('species-form');
const nombreComunInput = document.getElementById('nombreComun');
const ecosistemaSelect = document.getElementById('ecosistema');
const descripcionTextarea = document.getElementById('descripcion');
const fotografiaInput = document.getElementById('fotografia');
const imagePreview = document.getElementById('image-preview');
const imagePlaceholder = document.getElementById('image-preview-placeholder');
function handleSubmit(event) {
    event.preventDefault();
    const formData = new FormData(form);
    fetch('/ProyectoLP-Especies/api/species.php?action=store', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(result => {
        alert('¡Especie registrada correctamente!');
        form.reset();
        imagePreview.src = '';
        imagePreview.classList.add('hidden');
        imagePlaceholder.classList.remove('hidden');
    })
        .catch(error => {
        console.error('Error al guardar la especie', error);
        alert('Error al guardar la especie');
    });
}
function cargarEcosistemas() {
    fetch('/ProyectoLP-Especies/api/ecosystems.php?action=list')
        .then(response => {
        if (!response.ok)
            throw new Error('No se pudieron cargar los ecosistemas');
        return response.json(); // ahora sí será JSON válido
    })
        .then((data) => {
        ecosistemaSelect.innerHTML = '<option value="">-- Seleccione un ecosistema --</option>';
        data.forEach(ecosistema => {
            const option = document.createElement('option');
            option.value = ecosistema.id.toString();
            option.textContent = ecosistema.nombre;
            ecosistemaSelect.appendChild(option);
        });
    })
        .catch(error => {
        console.error('Error cargando ecosistemas:', error);
    });
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
document.addEventListener('DOMContentLoaded', () => {
    cargarEcosistemas();
});
