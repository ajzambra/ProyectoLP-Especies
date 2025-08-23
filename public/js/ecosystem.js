"use strict";

document.addEventListener("DOMContentLoaded", () => {
  // DOM
  const form = document.getElementById("ecosystem-form");
  const nombreInput = document.getElementById("nombre");
  const clasificacionSelect = document.getElementById("clasificacion");
  const lugarInput = document.getElementById("lugar");
  const descripcionTextarea = document.getElementById("descripcion");
  const imagenInput = document.getElementById("imagen");
  const imagePreview = document.getElementById("image-preview");
  const imagePlaceholder = document.getElementById("image-preview-placeholder");

  // URL 
  const API_URL = `${location.origin}/ProyectoLP-Especies/api/ecosystems.php`;

  // Previsualización de imagen
  if (imagenInput) {
    imagenInput.addEventListener("change", () => {
      const file = imagenInput.files?.[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          imagePreview.src = e.target?.result || "";
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
  }

  // Envío REAL al backend
  if (form) {
    form.addEventListener("submit", async (evt) => {
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

      // Construir FormData
      const fd = new FormData();
      fd.append("nombre", nombreInput.value.trim());
      fd.append("clasificacion", clasificacionSelect.value);
      fd.append("lugar", lugarInput.value.trim());
      fd.append("descripcion", descripcionTextarea.value.trim());
      if (imagenInput.files?.[0]) fd.append("imagen", imagenInput.files[0]);

      // Enviar
      try {
        const res = await fetch(API_URL, { method: "POST", body: fd });
        let data = null;
        try { data = await res.json(); } catch (_) {}

        if (!res.ok) {
          console.error("Fallo POST:", res.status, data);
          alert(data?.error ?? `Error ${res.status} al registrar`);
          return;
        }

        alert("¡Ecosistema registrado!");
        console.log("Creado:", data);

        // Reset UI
        form.reset();
        imagePreview.src = "";
        imagePreview.classList.add("hidden");
        imagePlaceholder.classList.remove("hidden");
      } catch (err) {
        console.error(err);
        alert("Error de red o servidor");
      }
    });
  } else {
    console.warn("No encontré el formulario #ecosystem-form");
  }
});
