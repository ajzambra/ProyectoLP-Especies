# Guía rápida para prueba del Proyecto

1. Instalar XAMPP  
   Descarga e instala XAMPP. No cambies la ruta por defecto.  

2. Copiar el proyecto al servidor  
   Coloca tu carpeta del backend en:  
   `C:\xampp\htdocs\PROYECTOLP-ESPECIES\`  
   (Usa exactamente ese nombre en la carpeta y en las URLs).  

3. Iniciar servicios  
   Abre XAMPP y pulsa **Start** en Apache y MySQL.  

4. Crear la base de datos e importar el esquema  
   - Entra a [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/).  
   - Crea la BD **biodiversidad** con cotejamiento **utf8mb4_unicode_ci**.  
   - Selecciona la BD → pestaña **Importar** → sube `database/schema.sql` → **Continuar**.  
   - Se podrá visualizar las tablas **ecosistemas** y **especies**.  

5. Para poder ver la página principal probar en:  
   [http://localhost/ProyectoLP-Especies/index.html](http://localhost/ProyectoLP-Especies/index.html)  

6. Prueba de escritura (crear ecosistema)  
   Abre el formulario para crear ecosistemas:  
   [http://localhost/PROYECTOLP-ESPECIES/registrar-ecosistema.html](http://localhost/PROYECTOLP-ESPECIES/registrar-ecosistema.html)  

7. Prueba de lectura (listar ecosistemas)  
   [http://localhost/PROYECTOLP-ESPECIES/ver-ecosistemas.html](http://localhost/PROYECTOLP-ESPECIES/ver-ecosistemas.html)  
   Debe mostrar tarjetas con nombre, clasificación y lugar.  
