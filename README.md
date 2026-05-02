# NOVA UNIÓ

Aplicació web de gestió per al **Club Esportiu Nova Unió**, un club de **Lloret de Mar** centrat principalment en **MMA** i **Sambo**.

Aquest projecte neix amb la idea de centralitzar en una sola aplicació tota la gestió del club: alumnes, grups, classes, assistències, quotes, pagaments, assegurances esportives i preinscripcions. A més, també inclou una part pública informativa i un panell intern per a la gestió del dia a dia.

---

# Accés ràpid per a la prova

Quan estigui tot instal·lat, es podrà obrir directament al navegador:

- **Panell intern:** `http://127.0.0.1:8000/panel/login`

Usuari demo d’administrador:

- **Email:** `admin@novaunio.local`
- **Contrasenya:** `NovaUnio1234!`

---

# Què inclou el projecte

## Part pública

La part pública és la web informativa del club i inclou pàgines com:

- Inici
- El club
- Professors
- Horaris
- Plans
- FAQ
- Contacte
- Preinscripció

## Panell privat

La part privada és el panell intern, accessible des de `/panel`, on es pot gestionar:

- Dashboard
- Alumnes
- Grups
- Programació de grups
- Calendari
- Assistències
- Quotes i pagaments
- Assegurances esportives
- Preinscripcions
- Informes
- Usuaris
- Perfil

---

# Què es necessita per provar-ho en local

Per poder muntar el projecte en local, és recomanable tenir instal·lat:

- **PHP**
- **Composer**
- **Node.js i npm**
- **MySQL o MariaDB**
- Un entorn local tipus **XAMPP**, **Laragon**, **MAMP** o similar

> El més recomanable és muntar-ho amb **MySQL/MariaDB**, perquè és el mateix tipus de base de dades que s’ha utilitzat durant el desenvolupament.

---

# Com provar l’aplicació en local

## Passos exactes per poder obrir el panell al navegador

### 1. Descomprimir o clonar el projecte

Posar el projecte dins d’una carpeta local de l’ordinador.

---

### 2. Obrir una terminal dins de la carpeta del projecte

Totes les comandes següents s’han d’executar dins de la carpeta arrel del projecte.

---

### 3. Instal·lar les dependències de PHP

```bash
composer install
```

---

### 4. Instal·lar les dependències del frontend

```bash
npm install
```

---

### 5. Crear el fitxer `.env`

S’ha de copiar el fitxer `.env.example` i canviar-li el nom a `.env`.

Si es vol fer per terminal:

```bash
cp .env.example .env
```

En Windows, si aquesta comanda no funciona, es pot fer manualment duplicant el fitxer `.env.example` i canviant-li el nom a `.env`.

---

### 6. Crear una base de dades local

S’ha de crear una base de dades buida a **MySQL** o **MariaDB**.

Per exemple:

- `novaunio_local`

---

### 7. Generar la clau de Laravel

```bash
php artisan key:generate
```

---

### 8. Crear les taules i carregar les dades demo

```bash
php artisan migrate:fresh --seed
```

Aquest comandament crearà tota l’estructura de la base de dades i carregarà dades de prova per poder entrar a l’aplicació i revisar-la sense trobar-ho tot buit.

---

### 9. Compilar els assets del frontend

```bash
npm run build
```

Si es prefereix treballar amb Vite en desenvolupament, també es pot fer servir:

```bash
npm run dev
```

---

### 10. Arrencar el servidor local

```bash
php artisan serve
```

---

## Obrir l’aplicació al navegador

Quan s’hagi executat l’última comanda, es podrà obrir al navegador:

- **Web pública:** `http://127.0.0.1:8000`
- **Panell intern:** `http://127.0.0.1:8000/panel/login`

---

# Usuari demo per provar l’aplicació

## Administrador

- **Email:** `admin@novaunio.local`
- **Contrasenya:** `NovaUnio1234!`

## Entrenador admin

- **Email:** `entrenadoradmin@novaunio.local`
- **Contrasenya:** `NovaUnio1234!`

## Entrenador

- **Email:** `entrenador@novaunio.local`
- **Contrasenya:** `NovaUnio1234!`

> Si es vol revisar tota l’aplicació amb accés complet, el millor és entrar amb l’usuari **admin**.

---

# Dades demo incloses

Quan es fa la instal·lació local, l’aplicació carrega dades de prova perquè es pugui revisar millor el funcionament del sistema. Entre aquestes dades hi ha:

- grups de prova
- alumnes actius
- quotes pagades i pendents
- pagaments registrats
- assegurances esportives
- preinscripcions
- classes passades i futures
- assistències registrades i classes sense llista

Això permet provar millor apartats com:

- la fitxa de l’alumne
- l’historial de pagaments
- el calendari
- les assistències
- el dashboard
- la gestió d’usuaris

---

# Si es vol començar de zero

Si es vol reiniciar tot l’entorn demo, només cal executar una altra vegada:

```bash
php artisan migrate:fresh --seed
```

Això tornarà a crear tota la base de dades amb les dades de prova.

---

# Resum ràpid de comandes

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

Després d’això, només cal obrir:

- `http://127.0.0.1:8000/panel/login`

I entrar amb:

- `admin@novaunio.local`
- `NovaUnio1234!`

---

# Notes importants

- Els usuaris i les dades demo estan preparats **només per provar el projecte en local**.
- No s’han pensat per utilitzar-se en producció.
- El projecte està preparat perquè el professor el pugui muntar en local i provar-lo directament amb un usuari administrador demo.

---

# Context del projecte

NOVA UNIÓ no és només una pràctica acadèmica. És una eina pensada per donar resposta a una necessitat real del club i ajudar a organitzar millor el seu dia a dia. La idea és que l’aplicació pugui ser útil de veritat per gestionar alumnes, classes, assistències, pagaments, preinscripcions i tot el que hi ha darrere del funcionament del club.

Per això, més enllà de la part tècnica, el projecte s’ha intentat desenvolupar d’una manera pràctica, clara i pensada perquè es pugui continuar millorant i fent créixer amb el temps.