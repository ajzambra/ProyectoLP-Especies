"use strict";
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
const params = new URLSearchParams(window.location.search);
const id = params.get('id');
const detailContainer = document.getElementById('detail-container');
if (id) {
    fetch(`/ProyectoLP-Especies/api/species.php?action=get&id=${id}`)
        .then((res) => __awaiter(void 0, void 0, void 0, function* () {
        if (!res.ok)
            throw new Error(`Error en la respuesta del servidor: ${res.status}`);
        const text = yield res.text();
        try {
            return JSON.parse(text);
        }
        catch (e) {
            console.error('Respuesta no válida JSON:', text);
            throw new Error('El servidor no devolvió un JSON válido.');
        }
    }))
        .then((species) => {
        detailContainer.innerHTML = `
                <h1 class="main-title">Información detallada de la especie</h1>
                <h2 class="species-name">${species.nombre_comun}</h2>

                <section class="species-section">
                    <div class="species-left">
                        <img src="/ProyectoLP-Especies/public/${species.imagen_url}" alt="${species.nombre_comun}">
                    </div>
                    <div class="species-right">
                        <p><strong>Tipo:</strong> ${species.tipo}</p>
                        <p><strong>Ecosistema:</strong> ${species.nombre_ecosistema}</p>
                        <p><strong>Descripción:</strong> ${species.descripcion}</p>
                        <p><strong>Fecha de creación:</strong> ${species.fecha_creacion}</p>
                    </div>
                </section>
            `;
    })
        .catch(err => {
        console.error(err);
        detailContainer.innerHTML = '<p>Error al cargar la especie. Revisa la consola para más detalles.</p>';
    });
}
else {
    detailContainer.innerHTML = '<p>ID de especie no proporcionado.</p>';
}
