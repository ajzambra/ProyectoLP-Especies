"use strict";


const BASE_API = "http://localhost/ProyectoLP-Especies";
const LIST_URL = `${BASE_API}/api/ecosystems.php`;

const $lista  = document.getElementById("lista");
const $q      = document.getElementById("q");
const $filtro = document.getElementById("filtro");
const $btn    = document.getElementById("btnFiltrar");

const esc = s => String(s ?? "").replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));

async function cargar(){
  const params = new URLSearchParams();
  if ($q.value.trim()) params.set("q", $q.value.trim());
  if ($filtro.value)   params.set("clasificacion", $filtro.value);

  $lista.innerHTML = `<div class="card"><div class="body">Cargando...</div></div>`;

  try{
    const res  = await fetch(`${LIST_URL}${params.toString() ? "?" + params.toString() : ""}`, { headers: { Accept: "application/json" }});
    const data = await res.json();
    const rows = Array.isArray(data) ? data : (data.items || []);
    if (!rows.length){
      $lista.innerHTML = `<div class="card"><div class="body muted">No hay resultados</div></div>`;
      return;
    }

    $lista.innerHTML = rows.map(it => {
      const img = it.imagen_url ? `${BASE_API}/${esc(it.imagen_url)}` : "";
      return `
        <div class="card">
          ${img ? `<img src="${img}" alt="${esc(it.nombre)}">` : `<img src="" alt="Sin imagen" style="display:none">`}
          <div class="body">
            <div class="badge">${esc(it.clasificacion || "—")}</div>
            <h3 style="margin:8px 0 4px">${esc(it.nombre)}</h3>
            <div class="muted">${esc(it.lugar || "—")}</div>
            ${it.descripcion ? `<p style="margin:8px 0 0">${esc(it.descripcion)}</p>` : ""}
            ${it.created_at ? `<div class="muted" style="margin-top:6px">Creado: ${esc(it.created_at)}</div>` : ""}
          </div>
        </div>
      `;
    }).join("");
  }catch(e){
    console.error(e);
    $lista.innerHTML = `<div class="card"><div class="body" style="color:crimson">Error cargando datos</div></div>`;
  }
}

$btn.addEventListener("click", cargar);
cargar();
