---
name: Recuperación de fotos vehiculares
description: Cómo identificar imágenes originales remotas cuando la copia local no contiene los archivos correctos.
---

Las fotos originales de vehículos pueden existir en el `invoice_files` remoto de Hostinger aunque no estén en la copia local del proyecto. No se debe asumir que el inventario local representa todo lo disponible en producción.

**Why:** Los nombres generados por PHP comienzan con un `uniqid` cuyo prefijo hexadecimal codifica la hora de carga. Esa hora, junto con modelo, año y color, permitió distinguir archivos originales de imágenes genéricas y documentos.

**How to apply:** Antes de reemplazar fotos, obtener del administrador remoto los nombres exactos, comprobar sus URLs públicas, correlacionar la hora del prefijo con `cars.created_at`, revisar visualmente cada archivo y actualizar solo coincidencias inequívocas. Respaldar primero `cars.invoice_file` y `galery.invoice_file`.