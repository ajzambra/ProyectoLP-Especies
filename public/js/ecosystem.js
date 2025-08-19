"use strict";

// Referencias al DOM
const form = document.getElementById("ecosystem-form");
const nombreInput = document.getElementById("nombre");
const clasificacionSelect = document.getElementById("clasificacion");
const lugarInput = document.getElementById("lugar");
const descripcionTextarea = document.getElementById("descripcion");
const imagenInput = document.getElementById("imagen");
const imagePreview = document.getElementById("image-preview");
const imagePlaceholder = document.getElementById("image-preview-placeholder");

// Previsualización de imagen
imagenInput.addEventListener("change", () => {
  const file = imagenInput.files?.[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      const url = e.target?.result;
      imagePreview.src = url;
      imagePreview.classList.remove("hidden");
      imagePlaceholder.classList.add("hidden");
    };
    reader.readAsDataURL(file);
  } else {
    imagePreview.src = "";
    imagePreview.classList.add("hidden");
    imagePlaceholder.classList.remove("hidden");
  }
});

// Manejo del submit (simulación, sin backend)
form.addEventListener("submit", (evt) => {
  evt.preventDefault();

  // Validación mínima
  if (!nombreInput.value.trim()) {
    alert("El nombre es obligatorio");
    nombreInput.focus();
    return;
  }
  if (!clasificacionSelect.value) {
    alert("Seleccione una clasificación");
    clasificacionSelect.focus();
    return;
  }

  // Armar “payload” como lo mandaría al backend
  const payload = {
    nombre: nombreInput.value.trim(),
    clasificacion: clasificacionSelect.value,
    lugar: lugarInput.value.trim(),
    descripcion: descripcionTextarea.value.trim(),
    nombreArchivo: imagenInput.files?.[0]?.name || "Sin imagen",
    // Simulación de metadatos
    simulacion: {
      idGenerado: Math.floor(Math.random() * 10000) + 1,
      fechaHora: new Date().toISOString()
    }
  };

  console.log("---- Simulación de envío (Ecosistema) ----");
  console.log(payload);

  alert("¡Datos capturados! Revisa la consola (F12 → Console).");

  // Reset
  form.reset();
  imagePreview.src = "";
  imagePreview.classList.add("hidden");
  imagePlaceholder.classList.remove("hidden");
});
