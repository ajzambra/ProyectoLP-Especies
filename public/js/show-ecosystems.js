const ecosystemList = document.getElementById('ecosystemList');
const filterForm = document.getElementById('filterForm');
const resetBtn = document.getElementById('resetBtn');

const BASE_URL = '/ProyectoLP-Especies/api/ecosystems.php?json=1';

async function fetchEcosystems(filters = {}) {
    try {
        let url = new URL(window.location.origin + BASE_URL);
        Object.keys(filters).forEach(key => {
            if (filters[key]) url.searchParams.append(key, filters[key]);
        });

        const res = await fetch(url);
        if (!res.ok) throw new Error('Error al cargar los ecosistemas');
        const data = await res.json();
        return data;
    } catch (err) {
        console.error(err);
        ecosystemList.innerHTML = `<div class="col-12"><div class="alert alert-danger">${err.message}</div></div>`;
        return [];
    }
}

function renderEcosystems(ecosystems) {
    if (!ecosystems.length) {
        ecosystemList.innerHTML = `<div class="col-12"><div class="alert alert-info">No hay ecosistemas registrados.</div></div>`;
        return;
    }

    ecosystemList.innerHTML = ecosystems.map(eco => `
        <div class="col-md-4">
            <div class="card">
                ${eco.imagen_url 
                    ? `<img src="${eco.imagen_url}" class="card-img-top" alt="${eco.nombre}">`
                    : `<div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center"><span>Sin imagen</span></div>`}
                <div class="card-body">
                    <h5 class="card-title">${eco.nombre}</h5>
                    <p class="card-text">
                        <strong>Clasificación:</strong> ${eco.clasificacion || 'No especificado'}<br>
                        <strong>Ubicación:</strong> ${eco.lugar || 'No especificado'}
                    </p>
                    <p class="card-text">
                        <strong>Descripción:</strong> ${eco.descripcion ? eco.descripcion.substr(0, 100)+'...' : 'Sin descripción'}
                    </p>
                    <div class="d-flex justify-content-between">
                        <a href="/ProyectoLP-Especies/app/Views/ecosystems/edit.php?id=${eco.id}" class="btn btn-warning btn-action">Editar</a>
                        <button class="btn btn-danger btn-action" onclick="deleteEcosystem(${eco.id})">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

async function deleteEcosystem(id) {
    if (!confirm('¿Estás seguro de eliminar este ecosistema?')) return;

    try {
        const res = await fetch(`/ProyectoLP-Especies/api/ecosystems.php?action=delete&id=${id}`, {
            method: 'POST'
        });

        if (!res.ok) {
            const text = await res.text(); 
            throw new Error('No se pudo eliminar el ecosistema: ' + text);
        }

        const data = await res.json();
        if (data.error) throw new Error(data.error);

        alert(data.success || 'Ecosistema eliminado');
        loadEcosystems(); // recarga lista
    } catch(err) {
        alert(err.message);
    }
}

async function loadEcosystems(filters = {}) {
    const data = await fetchEcosystems(filters);
    renderEcosystems(data);
}

filterForm.addEventListener('submit', e => {
    e.preventDefault();
    const filters = {
        nombre: filterForm.nombre.value,
        clasificacion: filterForm.clasificacion.value,
        lugar: filterForm.lugar.value
    };
    loadEcosystems(filters);
});

resetBtn.addEventListener('click', () => {
    filterForm.reset();
    loadEcosystems();
});

loadEcosystems();