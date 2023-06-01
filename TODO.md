
Crear una página de opciones para guardar la configuracion
* github_uri
* token 

Utilizar el filtro 'upgrader_post_install', para tareas post-instalacion del theme:
* Mensajes de información.
* etc.

Utilizar el filtro 'theme_api', para mostrar información de la actualizacion

En la función github_repository_info(), controlar la respuesta, y ver si no trae información
de un repositorio, ver cual es el mensaje:
* Token no válido
* Alcanzado limite de peticiones
* Url no válida
* etc.