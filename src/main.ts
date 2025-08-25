
const form = document.getElementById('species-form') as HTMLFormElement;
const nombreComunInput = document.getElementById('nombreComun') as HTMLInputElement;
const ecosistemaSelect = document.getElementById('ecosistema') as HTMLSelectElement;
const descripcionTextarea = document.getElementById('descripcion') as HTMLTextAreaElement;
const fotografiaInput = document.getElementById('fotografia') as HTMLInputElement;
const imagePreview = document.getElementById('image-preview') as HTMLImageElement;
const imagePlaceholder = document.getElementById('image-preview-placeholder') as HTMLElement;

function handleSubmit(event: SubmitEvent) {
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
            if (!response.ok) throw new Error('No se pudieron cargar los ecosistemas');
            return response.json(); // ahora sí será JSON válido
        })
        .then((data: {id: number, nombre: string}[]) => {
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
    const file = fotografiaInput.files?.[0];

    if (file) {
        const reader = new FileReader();

        reader.onload = (e) => {
            const imageUrl = e.target?.result as string;

            imagePreview.src = imageUrl;
            imagePreview.classList.remove('hidden');
            imagePlaceholder.classList.add('hidden');
        };

        reader.readAsDataURL(file);
    } else {
        imagePreview.src = '';
        imagePreview.classList.add('hidden');
        imagePlaceholder.classList.remove('hidden');
    }
});

form.addEventListener('submit', handleSubmit);
document.addEventListener('DOMContentLoaded', () => {
    cargarEcosistemas();
});