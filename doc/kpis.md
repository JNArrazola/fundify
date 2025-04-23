# KPIs para la Etapa Pre-Lanzamiento de Fundify

Este documento presenta los indicadores clave de rendimiento (KPIs) aplicables al estado actual de Fundify, una plataforma de donaciones y recompensas aún en etapa de desarrollo interno.

---

## 1. Porcentaje de funcionalidades implementadas

**Fórmula:**  
(Funcionalidades completadas / Funcionalidades planificadas) × 100

**Estado actual:**  
21 funcionalidades planificadas / 22 completadas = **95%**

**Interpretación:**  
El MVP contempla las funcionalidades clave (donaciones, campañas, recompensas, roles, login, gestión, puntos, tienda, etc.). El sistema está completo a nivel técnico en esta fase.

---

## 2. Tasa de cobertura de pruebas

**Fórmula:**  
(Pruebas realizadas / Casos de prueba planificados) × 100

**Estado actual:**  
12 pruebas manuales de flujo crítico / 20 planificadas = **60%**

**Interpretación:**  
La cobertura es funcional en procesos esenciales, pero faltan más pruebas en escenarios de error, carga y validaciones.

---

## 3. Tasa de resolución de errores

**Fórmula:**  
(Errores corregidos / Errores reportados) × 100

**Estado actual:**  
22 errores detectados / 22 corregidos = **100%**

**Interpretación:**  
Todos los errores conocidos en desarrollo fueron solucionados, incluyendo validaciones, control de sesiones, permisos y rutas inválidas.

---

## 4. Tiempo promedio de resolución de incidencias

**Fórmula:**  
Σ(Tiempo resolución) / Nº de incidencias

**Estado actual:**  
Tiempo promedio: 2.3 horas por incidencia (basado en seguimiento de errores diarios)

**Interpretación:**  
El tiempo de respuesta es eficiente durante el desarrollo, gracias a la baja complejidad de errores detectados.

---

## 5. Documentación técnica completada

**Fórmula:**  
(Documentos entregados / Documentos planificados) × 100

**Estado actual:**  
0/6 documentos completados = **0%**

**Pendientes:** Esquema de base de datos formal, manual técnico, documentación de rutas

**Interpretación:**  
Es necesaria una segunda etapa enfocada en documentación para facilitar mantenimiento o expansión futura.

---

## 6. Capacidad de usuarios simultáneos en pruebas

**Indicador:**  
Máximo de usuarios concurrentes sin errores: **5**

**Interpretación:**  
Se han hecho pruebas básicas locales con múltiples sesiones simultáneas. No hay infraestructura de prueba de carga ni entorno cloud, por lo que este KPI está limitado por el entorno actual (localhost).

---

## 7. Uso de recursos del servidor en entorno de pruebas

**Indicador:**  
Uso de CPU y RAM bajo pruebas locales: estable, sin cuellos de botella

**Interpretación:**  
El sistema responde de forma fluida en entorno XAMPP local. Aún no se han hecho pruebas reales en servidores de producción ni análisis de escalabilidad.

---
