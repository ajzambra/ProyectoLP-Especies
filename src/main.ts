
const form = document.getElementById('species-form') as HTMLFormElement;
const nombreComunInput = document.getElementById('nombreComun') as HTMLInputElement;
const ecosistemaSelect = document.getElementById('ecosistema') as HTMLSelectElement;
const descripcionTextarea = document.getElementById('descripcion') as HTMLTextAreaElement;
const fotografiaInput = document.getElementById('fotografia') as HTMLInputElement;
const imagePreview = document.getElementById('image-preview') as HTMLImageElement;
const imagePlaceholder = document.getElementById('image-preview-placeholder') as HTMLElement;

function handleSubmit(event: SubmitEvent) {
  event.preventDefault();

  const tipoSeleccionado = (document.querySelector('input[name="tipo"]:checked') as HTMLInputElement).value;
  
  const datosDelFormulario = {
    nombreComun: nombreComunInput.value,
    tipo: tipoSeleccionado,
    ecosistemaId: ecosistemaSelect.value,
    descripcion: descripcionTextarea.value,
    nombreArchivo: fotografiaInput.files?.[0]?.name || 'No se seleccionó archivo'
  };

  console.log('--- Simulación de Envío ---');
  console.log(datosDelFormulario);
  

  alert('¡Datos capturados! Revisa la consola del navegador (F12).');
  form.reset();
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