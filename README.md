# Laravel 11 Dockerizada

## Tecnologías Utilizadas

### - Apache
### - PHP 8
### - Mysql

# Comandos

Se dispone de un fichero Makefile con las siguientes instrucciones

## make start

Comando principal que va a permitir generar todo el contenedor desde cero de la plantilla. Esto hará que se elimine los contenedores creados anteriormente para crearlo todo desde cero.

```
    make start
```

## make stop

Comando que sirve para parar la ejecución de los contenedores

```
    make stop
```

## make composer-install

Comando que sirve para instalar las dependencias del proyecto. 

Se ejecuta en caso de que sea necesario actualizar las dependencias.

```
    make composer-install
```

## make bash

Comando que permite acceder al bash dentro del contenedor para poder ejecutar las distintas instrucciones que puedan a afectar al funcionamiento del contenendor.

Por ejemplo: Los comandos que se deben de ejecutar de Laravel para migraciones.

```
    make bash
```

## make up

Comando que sirve para levantar los contenedores una vez que están creado con todas sus dependencias instaladas y con todas las imagenes creadas también.

Sirve para levantar el contenedor nada mas y hacerlo funcionar.

```
    make up
```
# Iniciación del proyecto 

## make start

Ejecutar make start en la terminal

```
    make start
```

## make ssh-be

Una vez los contenedores corriendo ejecutar en otra terminal

```
    make ssh-be
```

## php migration:generate

Una vez dentro del contenedor correr las migraciones

```
    php migration:generate
```

## Permisos

dar permiso si hace falta a la carpeta

```
    sudo chmod -R 777 <Nombre de la carpeta del proyecto>
```

## Probar funcionalidad

En el navegador ingresar a:

```
    http://localhost:8080/api/documentation#/

```

## Observaciones

Clonar el .env.example en el .env y verificar credenciales de la DB
