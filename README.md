# EventMaster-Back

Back end para el proyecto Event Master

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="600"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Tabla de Contenidos

- [Descripción](#descripción)
- [Tecnologías Utilizadas](#tecnologías-utilizadas)
- [Requisitos Previos](#requisitos-previos)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Estructura del Código](#estructura-del-código)
- [Ejecución del Servidor](#ejecución-del-servidor)
- [Contribuciones](#contribuciones)
- [Contacto](#contacto)

## Descripción

EventMaster-Back es el back-end de una plataforma de gestión de eventos desarrollada con Laravel. Este proyecto proporciona API endpoints para la gestión de usuarios, eventos y asistentes. Está desacoplado del front-end, el cual se encuentra en un repositorio separado.

## Tecnologías Utilizadas

- **Laravel**: Versión 8.83.27
- **PHP**: Versión 7.4.29 o superior
- **PostgreSQL**: Versión 12 o superior
- **Composer**
- **pgAdmin4**: Herramienta para la administración de bases de datos PostgreSQL

## Requisitos Previos

Antes de comenzar, asegúrate de tener instalado lo siguiente:

- **PHP**: [Descargar PHP](https://www.php.net/downloads)
- **Composer**: [Descargar Composer](https://getcomposer.org/download/)
- **PostgreSQL**: [Descargar PostgreSQL](https://www.postgresql.org/download/)
- **pgAdmin4**: [Descargar pgAdmin4](https://www.pgadmin.org/download/pgadmin-4-windows/)

## Instalación

1. Clona el repositorio de EventMaster-Back:
    ```bash
    git clone https://github.com/tu-usuario/eventmaster-back.git
    cd eventmaster-back
    ```

2. Instala las dependencias del proyecto:
    ```bash
    composer install
    ```

## Configuración

1. Copia el archivo de configuración `.env.example` a `.env`:
    ```bash
    cp .env.example .env
    ```

2. Genera la clave de la aplicación:
    ```bash
    php artisan key:generate
    ```

3. Configura tu base de datos en el archivo `.env`:
    ```env
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=nombre_de_tu_base_de_datos
    DB_USERNAME=tu_usuario
    DB_PASSWORD=tu_contraseña
    ```

4. Ejecuta las migraciones para crear las tablas necesarias en la base de datos:
    ```bash
    php artisan migrate
    ```

## Estructura del Código

Algunas carpetas y archivos importantes en el proyecto:

- `app`: Contiene los controladores, modelos y servicios de la aplicación.
- `routes`: Define las rutas de la API.
- `database/migrations`: Contiene las migraciones de la base de datos.
- `config`: Archivos de configuración de la aplicación.

## Ejecución del Servidor

Para iniciar el servidor de desarrollo, ejecuta el siguiente comando:

```bash
php artisan serve
```
Navega a [http://localhost:8000/](http://localhost:8000/) para ver la aplicación en funcionamiento.

## Contribuciones

Las contribuciones son bienvenidas. Por favor, sigue estos pasos:

1. Realiza un fork del repositorio.
2. Crea una rama para tu nueva función (`git checkout -b feature/nueva-funcion`).
3. Realiza tus cambios y haz commit (`git commit -am 'Añadir nueva función'`).
4. Haz push a la rama (`git push origin feature/nueva-funcion`).
5. Abre un Pull Request.

## Contacto

Para cualquier duda o consulta adicional, no dudes en contactarnos:

- **Email**: [correo@example.com](mailto:correo@example.com)
- **Repositorio del front-end**: [EventMaster-Front](https://github.com/nach0gomez/EventMaster-Front)
