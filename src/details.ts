// detail.ts
interface Species {
    id_especie: number;
    nombre_comun: string;
    tipo: string;
    descripcion: string;
    imagen_url: string;
    fecha_creacion: string;
    nombre_ecosistema: string;
    imagen_ecosistema_url: string; // agregar si tienes imagen del ecosistema
    tipo_ecosistema: string;
    lugar_ecosistema: string;
}

const params = new URLSearchParams(window.location.search);
const id = params.get('id');
const detailContainer = document.getElementById('detail-container') as HTMLElement;

if (id) {
    fetch(`/ProyectoLP-Especies/api/species.php?action=get&id=${id}`)
        .then(async res => {
            if (!res.ok) throw new Error(`Error en la respuesta del servidor: ${res.status}`);
            const text = await res.text();
            try {
                return JSON.parse(text) as Species; 
            } catch (e) {
                console.error('Respuesta no válida JSON:', text);
                throw new Error('El servidor no devolvió un JSON válido.');
            }
        })
        .then((species: Species) => {
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
} else {
    detailContainer.innerHTML = '<p>ID de especie no proporcionado.</p>';
}
