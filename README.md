# Proyecto Full-Stack de Gestión de Usuarios

Este es un proyecto de prueba técnica que demuestra una aplicación web completa con un backend en PHP, un frontend en Angular y una base de datos MySQL, todo orquestado con Docker.

## Arquitectura

*   **Frontend:** Angular, servido por Nginx.
*   **Backend:** API REST en PHP, servida por Apache.
*   **Base de Datos:** MySQL.
*   **Gestión de BD:** PhpMyAdmin.
*   **Orquestación:** Docker Compose.

---

## Prerrequisitos

*   Docker
*   Docker Compose

---

## Instalación y Ejecución

1.  **Clonar el Repositorio**
    ```bash
    git clone https://github.com/crisflorez06/prueba_tecnica.git
    cd prueba
    ```

2.  **Configurar Variables de Entorno**
    *   Crea una copia del archivo de ejemplo `.env.example` y renómbrala a `.env`.
        ```bash
        cp .env.example .env
        ```
    *   Edita el archivo `.env` y rellena los valores para las contraseñas y la base de datos.

3.  **Construir y Levantar los Contenedores**
    *   Ejecuta el siguiente comando en la raíz del proyecto. La primera vez puede tardar varios minutos mientras se descargan y construyen las imágenes.
        ```bash
        docker compose up -d --build
        ```

---

## Acceso a los Servicios

Una vez que los contenedores estén en ejecución, podrás acceder a los servicios en las siguientes URLs:

*   **Aplicación Frontend:** [http://localhost:4200](http://localhost:4200)
*   **API Backend:** [http://localhost:8080](http://localhost:8080)
*   **PhpMyAdmin:** [http://localhost:8081](http://localhost:8081)

---
