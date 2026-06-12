# PIV — PISA Results Visualizer

## Descriere

PIV — PISA Results Visualizer este o aplicatie Web dezvoltata in PHP si SQLite pentru vizualizarea, analiza si compararea rezultatelor OECD PISA si a indicatorilor educationali proveniti din World Bank Education Statistics.

Aplicatia ofera functionalitati de filtrare, comparare, reprezentare grafica si export al datelor prin intermediul unei interfete Web responsive.

---

## Functionalitati

* Vizualizarea rezultatelor OECD PISA
* Filtrare dupa tara, disciplina, categorie si an
* Compararea rezultatelor intre tari
* Vizualizarea indicatorilor educationali
* Generarea de grafice interactive
* Export in formatele CSV, JSON, SVG si WebP
* Modul de administrare
* Mecanism propriu de caching
* Comunicare asincrona prin AJAX si Fetch API

---

## Tehnologii Utilizate

### Backend

* PHP 
* SQLite 3
* PDO

### Frontend

* HTML5
* CSS3
* JavaScript 
* Fetch API
* SVG

---

## Surse de Date

### OECD PISA

Date privind performantele elevilor la:

* Matematica
* Citire
* Stiinte

### World Bank Education Statistics

Date privind indicatorii educationali:

* alfabetizare
* inscriere scolara
* cheltuieli pentru educatie
* alti indicatori statistici

---

## Structura Proiectului

```text
PIV - Pisa/
│
├── Admin/
├── Api/
├── Cache/
├── Config/
├── Data/
├── Database/
├── Docs/
├── Frontend/
├── Import/
│
├── README.md
```

---

## Baza de Date

Aplicatia utilizeaza SQLite si contine urmatoarele tabele:

### countries

Informatii despre tari.

### pisa_results

Rezultate OECD PISA.

### indicators

Definitii ale indicatorilor educationali.

### education_stats

Valori ale indicatorilor educationali pe ani si tari.

---

## Servicii Web

### Countries API

```text
GET /Api/countries.php
```

Returneaza lista tarilor disponibile.

### PISA API

```text
GET /Api/pisa.php
```

Parametri:

```text
country
discipline
subject
year
```

### Compare API

```text
GET /Api/compare.php
```

Permite compararea mai multor tari.

### Education API

```text
GET /Api/education.php
```

Returneaza indicatori educationali.

### Export API

```text
GET /Api/export.php
```

Permite exportul datelor.

---

## Vizualizari

Aplicatia ofera trei modalitati principale de reprezentare:

### Line Chart

Evolutia scorurilor in timp.

### Bar Chart

Comparatii intre tari.

### Radar Chart

Comparatii intre discipline.

---

## Export

Formate suportate:

* CSV
* JSON
* SVG
* WebP

---

## Modul de Administrare

Acces:

```text
/Admin/login.php
```

Functionalitati:

* autentificare administrator
* afisare statistici baza de date
* gestionare sesiune

---

## Securitate

Aplicatia implementeaza:

* PDO Prepared Statements
* Protectie impotriva SQL Injection
* htmlspecialchars()
* Protectie impotriva XSS
* Autentificare bazata pe sesiuni

---

## Cache

Pentru imbunatatirea performantei a fost implementat un mecanism propriu de caching bazat pe fisiere JSON.

Rezultatele API-urilor sunt salvate temporar si reutilizate pentru cereri identice.

---

## Instalare

### 1. Crearea bazei de date

```bash
sqlite3 Database/pisavis.sqlite
.read Database/schema.sql
```

### 2. Importul datelor

```bash
php Import/countries.php
php Import/pisa.php
php Import/edstats.php
```

### 3. Pornirea aplicatiei

```bash
php -S localhost:8000
```

### 4. Accesarea aplicatiei

```text
http://localhost:8000/Frontend/index.php
```

---

## Cerinte Acoperite

* Servicii Web
* AJAX
* SQLite
* Responsive Design
* Filtrare multi-criteriala
* Comparatii intre tari
* Export CSV
* Export JSON
* Export SVG
* Export WebP
* Modul de administrare
* Cache
* Vizualizari multiple

---

## Autor

Madalina Bitiusca

Facultatea de Informatica, Universitatea "Alexandru Ioan Cuza" din Iasi
