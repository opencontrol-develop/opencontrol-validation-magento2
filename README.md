# OpenControl-Validation-Magento2

Herramienta de análisis de fraude de OpenControl para Magento2

## Instalación

Ir a la carpeta del proyecto de Magento y seguir los siguientes pasos:
```bash    
composer require opencontrol/magento2-integration:1.0.*
php bin/magento module:enable OpenControl_Integration --clear-static-content
php bin/magento setup:upgrade
php bin/magento cache:clean
```

## Actualización
En caso de ya contar con el módulo instalado y sea necesario actualizar, seguir los siguientes pasos:

```bash
composer clear-cache
composer update opencontrol/magento2-integration
bin/magento setup:upgrade
php bin/magento cache:clean
```

## Administración
### 1. Configuración del módulo

Para configurar el módulo desde el panel de administración de la tienda diríjase a: Stores > Configuration > Sales > OpenControl

#### Características
- Análisis de transacciones mediante tarjetas crédito/débito.
- Seleccionar los métodos de pago a analizar.
- Selección de modo entre **Sandbox** y **Producción**.
	 
![Configuracio-n-Open-Control.png](https://i.postimg.cc/WpGJ0zyq/Configuracio-n-Open-Control.png)
- Estatus personalizados en las órdenes analizadas por OpenControl.
	- **Denegado por OpenControl**
	- **Aprobado por OpenControl**

![Estatus-Open-Control.png](https://i.postimg.cc/8k4z1634/Estatus-Open-Control.png)

- Comentarios personalizados en las órdenes procesadas.

![Analizado-Open-Control.png](https://i.postimg.cc/XYkb8Sb8/Analizado-Open-Control.png)

![Denegado-Open-Control.png](https://i.postimg.cc/QNKZzWDV/Denegado-Open-Control.png)

#### Notas
*Para hacer uso de este módulo ponerse en contacto con soporte@openpay.mx*