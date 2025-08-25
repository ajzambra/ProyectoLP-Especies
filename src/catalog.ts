const catalogContainer = document.getElementById('catalog-container') as HTMLElement;

interface Species {
    id_especie: number;
    nombre_comun: string;
    tipo: string;
    descripcion: string;
    imagen_url: string;
    nombre_ecosistema: string;
}

function truncateText(text: string, maxLength: number) {
    return text.length > maxLength ? text.slice(0, maxLength) + '...' : text;
}

function cargarCatalogo() {
    catalogContainer.innerHTML = `<h1 class="catalog-title">Lista de Especies</h1>`;
    
    fetch('/ProyectoLP-Especies/api/species.php?action=list')
    .then(res => {
        if (!res.ok) throw new Error('No se pudieron cargar las especies');
        return res.json();
    })
    .then((data: Species[]) => {
        const cardsContainer = document.createElement('div');
        cardsContainer.className = 'cards-container';
        data.forEach(species => {
            const card = document.createElement('div');
            card.className = 'species-card';
            card.onclick = () => {
                window.location.href = `details.html?id=${species.id_especie}`;
            };

            card.innerHTML = `
                <img src="/ProyectoLP-Especies/public/${species.imagen_url || 'uploads/species/default.png'}" alt="${species.nombre_comun}">
                <div class="species-info">
                    <div class="species-name">${species.nombre_comun}</div>
                    <div class="species-type">Tipo: ${species.tipo}</div>
                    <div class="species-description">Descripción: ${species.descripcion}</div>
                </div>
            `;
            cardsContainer.appendChild(card);
        });

        catalogContainer.appendChild(cardsContainer);
    })
    .catch(err => {
        console.error('Error cargando catálogo:', err);
        catalogContainer.innerHTML += '<p>Error al cargar las especies.</p>';
    });
}

document.addEventListener('DOMContentLoaded', () => cargarCatalogo());
