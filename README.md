#  Prueba tema alojado en Github

## ¿Qué es esto?
Pruebas para crear un tema de Wordpress que se actualice desde un repositorio alojado en Github.

**IMPORANTE** Este es un tema básico con lo mínimo para para poder usarlo en una instalación Wordpress, creado solo para buscar actualizaciones en Github y para nada esta codificado para que sea funcional como para un sitio web, mostrar entradas, páginas chulas, etc.

Partiendo de esta charla para actualizar plugins desde un repositorio privado en Github, [Actualiza plugins propios desde un repositorio privado git](https://wordpress.tv/2022/09/25/actualiza-plugins-propios-desde-un-repositorio-privado-git/), he desarrollado este tema para que haga lo mismo pero con temas.

A esta primera versión le faltarían aún muchas cosas, puedes echar un vistazo a [tareas para mi yo del mañana](/TODO.md) para ver qué **no esta**

## Disclamer ##

Ni que decir tiene que este es un ejercicio de prueba y que deberías tener <span style="color:red"> **mucho cuidado** y **bajo tu responsabilidad**</span> el usar este código en producción.


## ¿Qué necesitamos? ##
Primero debemos tener el tema alojado en Github, y haber creado al menos una release. 

Si el repositorio es privado, deberás al menos crear un token de acceso al mismo, para ello mira el video de [Actualiza plugins propios desde un repositorio privado git](https://wordpress.tv/2022/09/25/actualiza-plugins-propios-desde-un-repositorio-privado-git/)

**NOTA** Mi recomendación es que los *tags* sean del estilo vn.n.n, donde cada n sea un número, ejemplo, v1.0.0

Segundo, vamos a mirar el código, donde básicamente tenemos una clase, WPTHEMENAME_Updater, que añade los hooks necesarios para consultar el repositorio en Github y comprobar si existe alguna versión superior a la existente.

Para crear el objeto, se le pasa un array de configuración con la url donde se encuentran las versiones y el token de acceso.

**SOLO** es necesario indicar el token de acceso si el repositorio es privado.

~~~
function themename_check_update() {
	if ( is_admin() ) {

		$theme_name = basename( dirname(__FILE__) ) ;
		$config  = array(
			'github_uri' => 'https://api.github.com/repos/PisanoWP/theme-wpupdater/releases',			                 
			'token'      => false,
		);
		$updater = new WPTHEMENAME_Updater( $theme_name, $config);
		$updater->check_update();		
    
	}
}
~~~

Si has llegado hasta aquí, espero que te sirva de ayuda y si tienes algún comentario en pisanowp@gmaill.com te leo.


