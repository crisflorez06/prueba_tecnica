# Etapa 1: El Constructor (Builder)
# Usamos una imagen de Node.js para instalar dependencias y construir la aplicación de Angular.
FROM node:20 as builder

# Establecemos el directorio de trabajo.
WORKDIR /app

# Copiamos los archivos de definición de paquetes e instalamos las dependencias.
COPY package.json package-lock.json ./
RUN npm install

# Copiamos el resto del código fuente de la aplicación.
COPY . .

# Construimos la aplicación para producción. El resultado estará en /app/dist/frontend/
RUN npm run build

# --- 

# Etapa 2: La Imagen Final (Final Image)
# Empezamos desde una imagen súper ligera de Nginx.
FROM nginx:alpine

# Copiamos los archivos estáticos construidos desde la etapa 'builder' al directorio público de Nginx.
COPY --from=builder /app/dist/frontend/browser /usr/share/nginx/html

# Copiamos nuestro archivo de configuración de Nginx para que soporte las rutas de Angular (Single Page Application).
COPY nginx.conf /etc/nginx/conf.d/default.conf

