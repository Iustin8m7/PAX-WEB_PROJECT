<a id="readme-top"></a>

<div align="center">
  <h1>PAX</h1>
  <h3>Platformă web pentru analiza parcului auto din România</h3>

  <p>
    Aplicație web PHP + SQLite pentru explorarea, filtrarea, compararea și vizualizarea datelor despre parcul auto din România, în perioada 2020-2024.
  </p>

  <p>
    <a href="#despre-proiect">Despre proiect</a>
    ·
    <a href="#functionalitati-principale">Funcționalități</a>
    ·
    <a href="#pornire-rapida">Pornire rapidă</a>
    ·
    <a href="#documentatie-api">API</a>
    ·
    <a href="#testare">Testare</a>
  </p>
</div>

<div align="center">

![PHP](https://img.shields.io/badge/PHP-plain%20PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite-database-003B57?style=for-the-badge&logo=sqlite&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-async%20frontend-F7DF1E?style=for-the-badge&logo=javascript&logoColor=111)
![HTML5](https://img.shields.io/badge/HTML5-pages-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-responsive-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![Chart.js](https://img.shields.io/badge/Chart.js-charts-FF6384?style=for-the-badge&logo=chartdotjs&logoColor=white)
![Leaflet](https://img.shields.io/badge/Leaflet-map-199900?style=for-the-badge&logo=leaflet&logoColor=white)
![Project Status](https://img.shields.io/badge/status-academic%20project-0F172A?style=for-the-badge)

</div>

---

## Cuprins

1. [Despre proiect](#despre-proiect)
2. [Funcționalități principale](#functionalitati-principale)
3. [Capturi de ecran](#capturi-de-ecran)
4. [Tehnologii folosite](#tehnologii-folosite)
5. [Structura proiectului](#structura-proiectului)
6. [Arhitectura aplicației](#arhitectura-aplicatiei)
7. [Baza de date](#baza-de-date)
8. [Normalizarea datelor](#normalizarea-datelor)
9. [Configurare](#configurare)
10. [Pornire rapidă](#pornire-rapida)
11. [Pagini principale](#pagini-principale)
12. [Documentație API](#documentatie-api)
13. [Testare](#testare)
14. [Checklist de testare manuală](#checklist-de-testare-manuala)
15. [Modul admin](#modul-admin)
16. [Import date](#import-date)
17. [Roadmap](#roadmap)
18. [Limitări cunoscute](#limitari-cunoscute)
19. [Troubleshooting](#troubleshooting)
20. [Observații de securitate](#observatii-de-securitate)
21. [Contributing](#contributing)
22. [License](#license)
23. [Contact](#contact)
24. [Acknowledgments](#acknowledgments)
25. [Note finale](#note-finale)

---

## Despre proiect

PAX este o aplicație web pentru analiza datelor despre parcul auto din România. Proiectul transformă fișiere CSV brute, împărțite pe ani, într-o bază de date SQLite normalizată și într-o interfață web care poate fi folosită pentru explorare vizuală, filtrare, căutare și comparații.

Prin „parc auto” se înțelege totalitatea vehiculelor înregistrate într-un anumit context administrativ și tehnic: an, județ, categorie națională, categorie comunitară, tip de combustibil, marcă și model comercial. Analiza acestor dimensiuni este utilă pentru observarea tendințelor: evoluția în timp, distribuția teritorială, preferințele pe combustibili, ponderea mărcilor, diferențele între județe și modificările între categorii de vehicule.

Aplicația rezolvă o problemă practică: datele CSV sunt greu de explorat direct, mai ales când trebuie comparate mai multe criterii. PAX oferă un strat de interogare și vizualizare peste aceste date:

- baza de date SQLite păstrează datele importate și normalizate;
- repository-urile PHP construiesc interogări SQL pentru filtre, statistici, căutare, hartă și admin;
- endpointurile API din `public/api/` returnează JSON standardizat;
- JavaScript-ul din `public/assets/js/` consumă API-urile asincron și actualizează interfața;
- paginile PHP din `public/` livrează structura HTML, CSS-ul și variabilele de configurare necesare frontend-ului.

Datele inspectate în proiect acoperă perioada 2020-2024, prin fișierele CSV din `raw_data/csv/`:

| An | Fișier CSV |
| --- | --- |
| 2020 | `parc_auto_2020_combustibil.csv` |
| 2021 | `parc_auto_2021_combustibil.csv` |
| 2022 | `parc_auto_2022_combustibil.csv` |
| 2023 | `parc_auto_2023_combustibil.csv` |
| 2024 | `parc_auto_2024_combustibil.csv` |

<p align="right">(<a href="#readme-top">Back to top</a>)</p>

---

## Funcționalități principale

### Pagina principală

Pagina `public/index.php` este punctul de intrare public al aplicației. Ea afișează prezentarea proiectului, intervalul analizat, sursa generală a datelor și linkuri către modulele principale.

Fișiere relevante:

- `public/index.php`
- `public/assets/css/main.css`
- `app/config/config.php`

Ce poate testa utilizatorul:

- deschiderea URL-ului `http://localhost:8001/`;
- navigarea către Dashboard, Hartă, Căutare, Comparații, Despre și Admin;
- verificarea că intervalul 2020-2024 este citit din configurare.

Rezultat așteptat: o pagină de prezentare cu navigație principală, zonă hero, descrierea modulelor și linkuri funcționale către restul aplicației.

### Dashboard statistic

Pagina `public/dashboard.php` oferă panoul analitic principal. Dashboard-ul folosește filtre globale și afișează carduri de overview, evoluție anuală, top mărci, distribuție pe combustibil, distribuție pe categorii și clasament pe județe.

Fișiere relevante:

- `public/dashboard.php`
- `public/assets/js/dashboard.js`
- `public/assets/js/charts.js`
- `public/assets/js/export-image.js`
- `public/assets/js/filters.js`
- `public/assets/js/api.js`
- `public/assets/css/dashboard.css`
- `app/repositories/StatisticsRepository.php`
- `public/api/statistics.php`
- `public/api/filters.php`
- `public/api/export.php`

Endpointuri folosite:

- `GET /api/filters.php`
- `GET /api/statistics.php?view=overview&year=2024`
- `GET /api/statistics.php?view=yearly-totals`
- `GET /api/statistics.php?view=top-brands&year=2024&limit=10`
- `GET /api/statistics.php?view=fuel-distribution&year=2024`
- `GET /api/statistics.php?view=county-ranking&year=2024`
- `GET /api/statistics.php?view=category-distribution&year=2024`
- `GET /api/export.php?resource=statistics&format=csv&view=...`

Ce poate testa utilizatorul:

- aplicarea filtrelor după an, județ, categorie, combustibil și marcă;
- resetarea filtrelor;
- încărcarea graficelor Chart.js;
- descărcarea exporturilor CSV pentru statistici;
- exportul unor grafice ca WebP, prin `export-image.js`.

Rezultat așteptat: carduri numerice și grafice actualizate asincron, fără reîncărcarea paginii.

### Hartă interactivă

Pagina `public/map-view.php` afișează o hartă Leaflet cu județele României, folosind contururi GeoJSON și date agregate pe județ. În codul actual, API-ul de hartă returnează pentru fiecare județ marca predominantă și totalul de vehicule, eventual filtrate după an, combustibil și categorie națională.

Fișiere relevante:

- `public/map-view.php`
- `public/assets/js/map.js`
- `public/assets/css/map.css`
- `public/assets/data/romania-counties.geojson`
- `raw_data/geojson/romania_counties.geojson`
- `app/repositories/MapRepository.php`
- `public/api/map.php`
- `public/api/brand-map-data.php`

Endpointuri folosite:

- `GET /api/map.php?year=2024`
- `GET /api/brand-map-data.php?year=2024`
- `GET /api/filters.php`

Ce poate testa utilizatorul:

- încărcarea hărții Leaflet;
- încărcarea tile-urilor CartoDB Voyager;
- încărcarea fișierului GeoJSON din `public/assets/data/romania-counties.geojson`;
- filtrarea după an, combustibil și categorie națională;
- click pe județ pentru afișarea detaliilor.

Rezultat așteptat: hartă cu județe colorate, tooltip/popup contextual și panou cu informații despre județul selectat.

### Căutare și filtrare

Pagina `public/search-view.php` oferă căutare multi-criterială în înregistrările importate. Utilizatorul poate filtra după an, județ, categorie națională, categorie comunitară, marcă, combustibil și model, cu sortare și paginare.

Fișiere relevante:

- `public/search-view.php`
- `public/assets/js/search.js`
- `public/assets/js/filters.js`
- `public/assets/js/api.js`
- `public/assets/css/search.css`
- `app/repositories/VehicleRepository.php`
- `public/api/search.php`
- `public/api/export.php`

Endpointuri folosite:

- `GET /api/search.php`
- `GET /api/filters.php`
- `GET /api/export.php?resource=search&format=csv&...`

Ce poate testa utilizatorul:

- filtrarea pentru `year=2024`;
- filtrarea după județ, de exemplu `county_code=IS`;
- filtrarea după marcă, de exemplu `brand=DACIA`;
- căutarea după model, de exemplu `model=LOGAN`;
- sortarea după `vehicle_count`, `brand_name`, `county_name` sau alte câmpuri permise;
- paginarea rezultatelor.

Rezultat așteptat: tabel cu rânduri din `vehicle_records`, total de rezultate, număr de pagini și controale de sortare/paginare.

### Comparații

Pagina `public/compare.php` compară două selecții A/B. Fiecare selecție are propriile filtre: an, județ, categorie națională, combustibil, marcă și model.

Fișiere relevante:

- `public/compare.php`
- `public/assets/js/compare.js`
- `public/assets/js/charts.js`
- `public/assets/js/filters.js`
- `public/assets/js/api.js`
- `public/assets/css/compare.css`
- `public/api/search.php`

Endpointuri folosite:

- `GET /api/filters.php`
- `GET /api/search.php` pentru selecția A
- `GET /api/search.php` pentru selecția B

Important: în implementarea curentă, `compare.js` construiește comparația pe baza endpointului de căutare cu `limit=100`. Asta înseamnă că rezultatele comparative pot fi incomplete pentru selecții care au mai mult de 100 de rânduri. Nu există încă un endpoint agregat dedicat pentru comparații.

Ce poate testa utilizatorul:

- selecții A/B cu ani diferiți;
- selecții A/B cu județe diferite;
- comparații pe marcă sau model;
- grafice comparative și tabele cu top județe/top modele.

Rezultat așteptat: totaluri pentru selecția A și B, diferență absolută, diferență procentuală unde se poate calcula, grafic comparativ și clasamente paralele.

### Pagina Despre

Pagina `public/about.php` descrie scopul proiectului, modulele publice, tehnologiile și arhitectura generală.

Fișiere relevante:

- `public/about.php`
- `public/assets/css/main.css`
- `public/assets/css/responsive.css`

Ce poate testa utilizatorul:

- încărcarea paginii;
- navigarea către modulele publice;
- aplicarea corectă a stilurilor.

Rezultat așteptat: pagină informativă în limba română, integrată vizual cu restul aplicației.

### Modul admin

Modulul admin există în `public/admin/` și este protejat prin sesiune PHP. Autentificarea este verificată în `app/helpers/auth.php` și `app/services/AuthService.php`.

Fișiere relevante:

- `public/admin/login.php`
- `public/admin/index.php`
- `public/admin/import.php`
- `public/admin/settings.php`
- `public/admin/logs.php`
- `app/helpers/auth.php`
- `app/services/AuthService.php`
- `app/services/AdminService.php`
- `app/repositories/AdminRepository.php`
- `app/config/config.php`

Funcționalități observate:

- login admin;
- logout prin `admin/index.php?action=logout`;
- dashboard admin cu număr de înregistrări, număr de importuri, ani disponibili, ultimul import și calea bazei de date;
- pagină de importuri cu batch-uri recente și sumar pe ani;
- pagină de setări cu valori din configurare;
- pagină de loguri care citește `logs/import_errors.log`.

Modulul admin este simplificat și orientat spre dezvoltare/local. Nu am observat formulare de upload/import direct din interfața admin; pagina de import afișează informații despre importuri existente, nu rulează scriptul de import.

### API JSON

Backend-ul expune endpointuri PHP în `public/api/`. Răspunsurile JSON sunt standardizate prin `app/core/Response.php`:

```json
{
  "status": "success",
  "data": {}
}
```

Pentru erori:

```json
{
  "status": "error",
  "message": "Mesaj de eroare"
}
```

### Import CSV

Importul CSV există în `scripts/import/import_csv_to_sqlite.php`. Scriptul citește fișierele din `raw_data/csv/`, inițializează schema dacă este nevoie, populează județele din `db/seed_counties.sql`, creează lookup-uri pentru categorii, combustibili și mărci, inserează în `vehicle_records` și înregistrează runde de import în `import_batches`.

### Validare parametri

Validarea parametrilor este centralizată în `app/helpers/validators.php`. Sunt validate:

- `year`, pe intervalul din configurare;
- `limit`, între 1 și `max_page_size`;
- `page`, minimum 1;
- câmpurile de sortare permise;
- ordinea de sortare `asc`/`desc`;
- valori permise pentru `view`.

### Normalizare UTF-8

Normalizarea răspunsurilor este realizată prin `app/helpers/normalizers.php`, care încearcă să păstreze sau să convertească șirurile la UTF-8. Endpointurile folosesc `normalizeUtf8Value()` înainte de a trimite JSON.

### Structură pe repository-uri

Accesul la date este separat în repository-uri:

| Repository | Rol |
| --- | --- |
| `FilterRepository.php` | liste de ani, județe, categorii, combustibili și mărci disponibile |
| `StatisticsRepository.php` | agregări statistice pentru dashboard |
| `VehicleRepository.php` | căutare, sortare, paginare și numărare rezultate |
| `MapRepository.php` | agregări pe județe și marca predominantă pe județ |
| `AdminRepository.php` | statistici administrative și informații despre importuri |

### Export

Exportul există prin `public/api/export.php` și `app/services/ExportService.php`. Formatul suportat este CSV. Resursele acceptate sunt:

- `search`;
- `statistics`;
- `map`.

Limitare importantă: exportul pentru `search` folosește intern `VehicleRepository::search()` cu pagina 1 și limită 100. Prin urmare, exportul căutării nu descarcă neapărat toate rândurile dacă filtrul produce peste 100 de rezultate.

Endpointul `public/api/brand-map.php` există ca fișier, dar este gol în versiunea inspectată. Pentru datele hărții pe mărci, endpointul funcțional este `public/api/brand-map-data.php`.

<p align="right">(<a href="#readme-top">Back to top</a>)</p>

---

## Capturi de ecran

Nu există capturi de ecran sau imagini de documentație în proiectul inspectat. Directorul `docs/` există, dar nu conține capturi.

> Capturile de ecran pot fi adăugate ulterior în `docs/screenshots/`.

Sugestii de capturi utile:

- pagina principală;
- dashboard cu grafice încărcate;
- hartă Leaflet cu județe colorate;
- căutare cu tabel și paginare;
- comparație A/B;
- panoul admin.

<p align="right">(<a href="#readme-top">Back to top</a>)</p>

---

## Tehnologii folosite

| Tehnologie | Rol în proiect |
| --- | --- |
| PHP | randare pagini publice, endpointuri API, sesiuni admin și scripturi CLI |
| SQLite | stocare locală a datelor importate și normalizate |
| PDO | conectare la SQLite, prepared statements și tratarea erorilor SQL |
| HTML5 | structurarea paginilor publice și admin |
| CSS3 | design responsive, layouturi, carduri, tabele și stiluri pentru hartă |
| JavaScript | consum API asincron, filtrare, randare tabele, paginare, hărți și grafice |
| Chart.js | grafice pentru dashboard și comparații |
| Leaflet | hartă interactivă a României |
| GeoJSON | contururi pentru județele României |
| PHP built-in server | rulare locală cu `php -S localhost:8001 -t public` |

Chart.js și Leaflet sunt încărcate din CDN în paginile care le folosesc. Fontul Plus Jakarta Sans este încărcat din Google Fonts.

<p align="right">(<a href="#readme-top">Back to top</a>)</p>

---

## Structura proiectului

```text
pax/
├── app/
│   ├── config/
│   │   └── config.php
│   ├── core/
│   │   ├── Database.php
│   │   ├── Response.php
│   │   └── Router.php
│   ├── helpers/
│   │   ├── auth.php
│   │   ├── normalizers.php
│   │   └── validators.php
│   ├── repositories/
│   │   ├── AdminRepository.php
│   │   ├── FilterRepository.php
│   │   ├── MapRepository.php
│   │   ├── StatisticsRepository.php
│   │   └── VehicleRepository.php
│   └── services/
│       ├── AdminService.php
│       ├── AuthService.php
│       ├── ExportService.php
│       ├── StatisticsService.php
│       └── VehicleService.php
├── db/
│   ├── schema.sql
│   └── seed_counties.sql
├── public/
│   ├── admin/
│   │   ├── index.php
│   │   ├── import.php
│   │   ├── login.php
│   │   ├── logs.php
│   │   └── settings.php
│   ├── api/
│   │   ├── brand-map.php
│   │   ├── brand-map-data.php
│   │   ├── export.php
│   │   ├── filters.php
│   │   ├── map.php
│   │   ├── search.php
│   │   └── statistics.php
│   ├── assets/
│   │   ├── css/
│   │   ├── data/
│   │   │   └── romania-counties.geojson
│   │   └── js/
│   ├── about.php
│   ├── compare.php
│   ├── dashboard.php
│   ├── index.php
│   ├── map-view.php
│   └── search-view.php
├── raw_data/
│   ├── csv/
│   └── geojson/
├── scripts/
│   ├── create_db.php
│   └── import/
│       └── import_csv_to_sqlite.php
├── logs/
│   └── import_errors.log
├── tests/
│   └── api/
└── README.md
```

Roluri principale:

- `app/config/` păstrează configurarea globală;
- `app/core/` conține conexiunea DB și răspunsurile JSON standardizate;
- `app/helpers/` conține funcții pentru autentificare, validare și normalizare;
- `app/repositories/` conține interogările SQL folosite de API-uri și servicii;
- `app/services/` conține servicii pentru admin, autentificare și export;
- `public/` este document root-ul aplicației web;
- `public/api/` conține endpointurile JSON/CSV;
- `public/assets/` conține CSS, JavaScript și GeoJSON;
- `db/` conține schema și seed-ul pentru județe;
- `raw_data/` conține datele brute CSV/GeoJSON;
- `scripts/` conține scripturi CLI pentru creare DB și import;
- `logs/` păstrează erori de import;
- `tests/api/` conține documente de testare manuală pentru API-uri.

Fișiere goale observate:

- `app/core/Router.php`;
- `app/services/VehicleService.php`;
- `app/services/StatisticsService.php`;
- `public/api/brand-map.php`.

<p align="right">(<a href="#readme-top">Back to top</a>)</p>

---

## Arhitectura aplicației

PAX folosește o arhitectură simplă, fără framework PHP. Paginile PHP livrează HTML-ul inițial, iar JavaScript-ul cere date prin API-uri și actualizează DOM-ul.

```text
Browser
  ↓
public/*.php
  ↓
public/assets/js/*.js
  ↓
public/api/*.php
  ↓
app/repositories/*.php
  ↓
app/core/Database.php
  ↓
db/pax.db
  ↓
JSON / CSV
  ↓
Frontend asincron
```

Rolul componentelor:

- paginile PHP (`public/index.php`, `dashboard.php`, `map-view.php`, `search-view.php`, `compare.php`, `about.php`) definesc structura paginilor și includ CSS/JS;
- JavaScript-ul (`api.js`, `filters.js`, `dashboard.js`, `map.js`, `search.js`, `compare.js`) trimite cereri asincrone și redă rezultatele;
- endpointurile API validează parametrii, apelează repository-uri și întorc JSON;
- repository-urile izolează interogările SQL;
- `Database.php` creează o conexiune PDO SQLite reutilizabilă și activează foreign keys;
- `Response.php` standardizează răspunsurile JSON;
- `validators.php` validează parametrii GET;
- `normalizers.php` normalizează valorile text pentru JSON UTF-8;
- `config.php` definește căi, interval de ani, paginare și credențiale admin locale.

Nu există un router activ în versiunea curentă: `app/core/Router.php` este gol. Rutarea se face prin fișiere PHP accesate direct.

<p align="right">(<a href="#readme-top">Back to top</a>)</p>

---

## Baza de date

Schema este definită în `db/schema.sql`. Baza de date folosită de aplicație este configurată ca `db/pax.db`.

### `counties`

Reprezintă județele României și București.

| Câmp | Rol |
| --- | --- |
| `id` | cheie primară |
| `code` | cod județ, unic, de exemplu `IS`, `CJ`, `B` |
| `name` | numele județului, unic, normalizat uppercase |

Relații: este referit de `vehicle_records.county_id`.

De ce există: evită repetarea numelui județului în fiecare înregistrare și permite agregări rapide pe județ.

### `vehicle_categories`

Reprezintă categoriile naționale de vehicule.

| Câmp | Rol |
| --- | --- |
| `id` | cheie primară |
| `name` | numele categoriei, unic |

Relații: este referit de `vehicle_records.national_category_id`.

De ce există: normalizează categoria națională și permite filtrare/statistici pe categorii.

### `community_categories`

Reprezintă categoriile comunitare de vehicule.

| Câmp | Rol |
| --- | --- |
| `id` | cheie primară |
| `name` | numele categoriei comunitare, unic |

Relații: este referit de `vehicle_records.community_category_id`, câmp opțional.

De ce există: separă clasificarea comunitară de cea națională și reduce duplicarea.

### `fuel_types`

Reprezintă tipurile de combustibil.

| Câmp | Rol |
| --- | --- |
| `id` | cheie primară |
| `name` | numele combustibilului, unic |

Relații: este referit de `vehicle_records.fuel_type_id`.

De ce există: permite distribuții pe combustibil și filtre coerente.

### `brands`

Reprezintă mărcile vehiculelor.

| Câmp | Rol |
| --- | --- |
| `id` | cheie primară |
| `name` | numele mărcii, unic |

Relații: este referit de `vehicle_records.brand_id`, câmp opțional.

De ce există: permite topuri de mărci, filtrare pe brand și agregări pe județe.

### `import_batches`

Urmărește rundele de import CSV.

| Câmp | Rol |
| --- | --- |
| `id` | cheie primară |
| `source_year` | anul extras din numele fișierului |
| `source_file` | fișierul CSV importat |
| `imported_at` | data și ora importului |
| `rows_inserted` | număr de rânduri inserate |
| `rows_rejected` | număr de rânduri respinse |
| `notes` | observații opționale |

Relații: este referit de `vehicle_records.import_batch_id`.

De ce există: oferă trasabilitate pentru importuri și alimentează modulul admin.

### `admins`

Definește o tabelă pentru administratori.

| Câmp | Rol |
| --- | --- |
| `id` | cheie primară |
| `username` | utilizator unic |
| `password_hash` | hash parolă |
| `created_at` | data creării |

Important: în codul curent, autentificarea admin folosește valorile din `app/config/config.php`, nu această tabelă. Tabela există în schema SQL, dar nu este folosită de `AuthService.php`.

### `vehicle_records`

Tabela principală cu datele despre vehicule.

| Câmp | Rol |
| --- | --- |
| `id` | cheie primară |
| `year` | anul datelor |
| `county_id` | referință la județ |
| `national_category_id` | referință la categoria națională |
| `community_category_id` | referință opțională la categoria comunitară |
| `brand_id` | referință opțională la marcă |
| `model_description` | descriere/model comercial |
| `fuel_type_id` | referință la combustibil |
| `vehicle_count` | total vehicule pentru combinația de dimensiuni |
| `import_batch_id` | referință opțională la importul care a inserat rândul |

Relații: conectează toate tabelele lookup și stă la baza căutărilor, statisticilor și hărții.

### Indexuri

Schema definește următoarele indexuri:

| Index | Câmpuri | Utilitate |
| --- | --- | --- |
| `idx_vehicle_records_year` | `year` | filtrări și agregări pe an |
| `idx_vehicle_records_county` | `county_id` | filtrări și grupări pe județ |
| `idx_vehicle_records_fuel` | `fuel_type_id` | distribuții pe combustibil |
| `idx_vehicle_records_category` | `national_category_id` | distribuții pe categorie |
| `idx_vehicle_records_brand` | `brand_id` | topuri și filtre pe marcă |
| `idx_vehicle_records_year_county` | `year`, `county_id` | hartă și agregări pe județ într-un an |

<p align="right">(<a href="#readme-top">Back to top</a>)</p>

---

## Normalizarea datelor

Datele nu sunt păstrate într-un singur tabel mare pentru că valorile precum județ, marcă, combustibil sau categorie se repetă de foarte multe ori. Proiectul folosește tabele lookup și chei străine pentru a păstra modelul relațional clar.

Avantaje:

- reducerea duplicării: numele unei mărci sau al unui combustibil este stocat o singură dată;
- consistență: filtrele folosesc valori unice din tabele lookup;
- performanță: indexurile pe ID-uri sunt mai eficiente decât repetarea textelor lungi;
- integritate: cheile străine împiedică referințe invalide;
- relații clare: `vehicle_records` descrie fapte, iar tabelele lookup descriu dimensiuni;
- mentenanță mai ușoară: corecturile de denumiri se fac într-un loc.

Scriptul de import creează sau reutilizează automat ID-urile pentru:

- `vehicle_categories`;
- `community_categories`;
- `fuel_types`;
- `brands`.

Pentru județe, scriptul folosește `db/seed_counties.sql` și normalizează câteva denumiri istorice sau alternative:

- `CARAS SEVERIN` -> `CARAS-SEVERIN`;
- `DIMBOVITA` -> `DAMBOVITA`;
- `VILCEA` -> `VALCEA`.

<p align="right">(<a href="#readme-top">Back to top</a>)</p>

---

## Configurare

Configurarea principală se află în `app/config/config.php`.

| Setare | Valoare/rol |
| --- | --- |
| `app_name` | numele aplicației, în cod este `Pax` |
| `debug` | `false` în configurarea inspectată |
| `paths.project_root` | rădăcina proiectului |
| `paths.db` | calea către `db/pax.db` |
| `paths.logs` | calea către directorul `logs` |
| `paths.geojson` | calea către GeoJSON-ul brut din `raw_data/geojson/` |
| `database.driver` | `sqlite` |
| `database.path` | calea fișierului SQLite |
| `app.default_year` | `2024` |
| `app.min_year` | `2020` |
| `app.max_year` | `2024` |
| `app.default_page_size` | `25` |
| `app.max_page_size` | `100` |
| `admin.username` | utilizator admin local |
| `admin.password` | parolă admin locală |

Configurația încearcă să aleagă automat între proiectul curent și o cale de tip `HOME/Projects/pax`, dacă acolo există deja `db/pax.db`.

Pentru testare locală, credențialele modulului admin se găsesc în `app/config/config.php`. În codul inspectat există valori hardcodate pentru mediu local/dezvoltare. Acest mod de lucru nu este potrivit pentru producție.

<p align="right">(<a href="#readme-top">Back to top</a>)</p>

---

## Pornire rapidă

### Prerequisites

Pentru rulare locală sunt necesare:

- PHP instalat;
- extensia PDO SQLite activă;
- SQLite sau `sqlite3` pentru verificări manuale;
- browser modern;
- opțional DB Browser for SQLite;
- conexiune internet pentru CDN-urile folosite de Chart.js, Leaflet, Google Fonts și tile-urile hărții.

Verificări utile:

```bash
php -v
php -m | grep -i sqlite
sqlite3 --version
```

### Installation

Clonează sau deschide proiectul local, apoi intră în directorul proiectului:

```bash
cd /cale/catre/proiect
```

Dacă baza `db/pax.db` nu există, poate fi creată din schema SQL:

```bash
php scripts/create_db.php
```

Pentru popularea bazei cu datele CSV existente:

```bash
php scripts/import/import_csv_to_sqlite.php
```

Pentru reimport forțat al fișierelor deja importate:

```bash
php scripts/import/import_csv_to_sqlite.php --force
```

### Running locally

Pornește serverul PHP built-in cu document root în `public`:

```bash
php -S localhost:8001 -t public
```

Opțiunea `-t public` este importantă deoarece doar fișierele din `public/` ar trebui expuse direct browserului. Codul aplicației din `app/`, fișierele SQL din `db/`, datele brute din `raw_data/` și scripturile din `scripts/` rămân în afara document root-ului.

Accesează:

```text
http://localhost:8001/
```

<p align="right">(<a href="#readme-top">Back to top</a>)</p>

---

## Pagini principale

| URL | Pagină | Ce ar trebui să apară |
| --- | --- | --- |
| `http://localhost:8001/` | Home | prezentarea PAX, linkuri către module, intervalul analizat |
| `http://localhost:8001/index.php` | Home | aceeași pagină principală |
| `http://localhost:8001/dashboard.php` | Dashboard | filtre, carduri numerice, grafice Chart.js și export CSV |
| `http://localhost:8001/map-view.php` | Hartă | hartă Leaflet, filtre și date pe județe |
| `http://localhost:8001/search-view.php` | Căutare | formular de filtrare, tabel, sortare, paginare și export CSV |
| `http://localhost:8001/compare.php` | Comparații | selecții A/B, diferențe, grafice și tabele comparative |
| `http://localhost:8001/about.php` | Despre | descrierea proiectului, module și tehnologii |
| `http://localhost:8001/admin/login.php` | Admin login | formular de autentificare admin |

<p align="right">(<a href="#readme-top">Back to top</a>)</p>

---

## Documentație API

Toate endpointurile se află sub:

```text
http://localhost:8001/api/
```

În mod normal folosesc metoda `GET`. Răspunsurile API valide au forma:

```json
{
  "status": "success",
  "data": {
    "result": []
  }
}
```

Erorile au forma:

```json
{
  "status": "error",
  "message": "Parametrul year trebuie să fie între 2020 și 2024."
}
```

### `GET /api/filters.php`

Returnează listele disponibile pentru filtre.

Exemplu:

```text
http://localhost:8001/api/filters.php
```

Răspuns simplificat:

```json
{
  "status": "success",
  "data": {
    "years": [2020, 2021, 2022, 2023, 2024],
    "counties": [{ "code": "IS", "name": "IASI" }],
    "nationalCategories": [{ "id": 1, "name": "AUTOTURISM" }],
    "communityCategories": [],
    "fuelTypes": [{ "id": 1, "name": "BENZINA" }],
    "brands": [{ "id": 1, "name": "DACIA" }]
  }
}
```

Posibile erori: `500` dacă baza de date lipsește sau interogarea eșuează.

### `GET /api/statistics.php`

Returnează statistici agregate pentru dashboard.

Parametri:

| Parametru | Descriere |
| --- | --- |
| `view` | `overview`, `yearly-totals`, `top-brands`, `fuel-distribution`, `county-ranking`, `category-distribution` |
| `year` | an între 2020 și 2024; obligatoriu pentru toate view-urile cu excepția `yearly-totals` |
| `county_code` | cod județ |
| `national_category` | categorie națională |
| `community_category` | categorie comunitară |
| `fuel_type` | tip combustibil |
| `brand` | marcă |
| `limit` | folosit pentru `top-brands`, maximum 100 |

Exemple:

```text
http://localhost:8001/api/statistics.php?view=overview&year=2024
http://localhost:8001/api/statistics.php?view=yearly-totals
http://localhost:8001/api/statistics.php?view=top-brands&year=2024&limit=10
http://localhost:8001/api/statistics.php?view=fuel-distribution&year=2024
http://localhost:8001/api/statistics.php?view=county-ranking&year=2024
http://localhost:8001/api/statistics.php?view=category-distribution&year=2024
```

Răspuns simplificat pentru `overview`:

```json
{
  "status": "success",
  "data": {
    "view": "overview",
    "filters": { "year": 2024 },
    "result": {
      "year": 2024,
      "total_vehicles": 123456,
      "counties_count": 42,
      "brands_count": 100,
      "fuel_types_count": 8,
      "national_categories_count": 10
    }
  }
}
```

Posibile erori:

- `400` pentru `view` invalid;
- `400` dacă `year` lipsește pentru view-uri care îl cer;
- `400` pentru `year` în afara intervalului;
- `500` pentru erori de DB.

### `GET /api/search.php`

Returnează rânduri filtrate din `vehicle_records`, cu paginare și sortare.

Parametri:

| Parametru | Descriere |
| --- | --- |
| `year` | an opțional între 2020 și 2024 |
| `county_code` | cod județ |
| `national_category` | categorie națională |
| `community_category` | categorie comunitară |
| `brand` | marcă |
| `fuel_type` | combustibil |
| `model` | căutare parțială în `model_description` |
| `page` | pagina curentă, minimum 1 |
| `limit` | rezultate pe pagină, 1-100 |
| `sort_by` | câmp de sortare permis |
| `sort_order` | `asc` sau `desc` |

Câmpuri de sortare permise:

- `year`;
- `county_code`;
- `county_name`;
- `national_category`;
- `community_category`;
- `brand_name`;
- `model_description`;
- `fuel_type`;
- `vehicle_count`.

Exemple:

```text
http://localhost:8001/api/search.php?year=2024&page=1&limit=25&sort_by=vehicle_count&sort_order=desc
http://localhost:8001/api/search.php?year=2024&county_code=IS&page=1&limit=25&sort_by=vehicle_count&sort_order=desc
http://localhost:8001/api/search.php?year=2024&brand=DACIA&page=1&limit=25&sort_by=vehicle_count&sort_order=desc
http://localhost:8001/api/search.php?year=2024&model=LOGAN&page=1&limit=25&sort_by=vehicle_count&sort_order=desc
```

Răspuns simplificat:

```json
{
  "status": "success",
  "data": {
    "rows": [
      {
        "year": 2024,
        "county_code": "IS",
        "county_name": "IASI",
        "national_category": "AUTOTURISM",
        "community_category": "M1",
        "brand_name": "DACIA",
        "model_description": "LOGAN",
        "fuel_type": "BENZINA",
        "vehicle_count": 1000
      }
    ],
    "total": 1,
    "page": 1,
    "pages": 1,
    "limit": 25,
    "sort_by": "vehicle_count",
    "sort_order": "desc"
  }
}
```

### `GET /api/map.php`

Returnează date agregate pe județ pentru hartă.

Parametri:

| Parametru | Descriere |
| --- | --- |
| `year` | obligatoriu, între 2020 și 2024 |
| `fuel_type` | opțional |
| `national_category` | opțional |

Exemplu:

```text
http://localhost:8001/api/map.php?year=2024
```

Răspuns simplificat:

```json
{
  "status": "success",
  "data": {
    "year": 2024,
    "filters": {
      "fuel_type": null,
      "national_category": null
    },
    "result": [
      {
        "county_code": "IS",
        "county_name": "IASI",
        "top_brand": "DACIA",
        "total_vehicles": 12345
      }
    ]
  }
}
```

### `GET /api/brand-map-data.php`

Returnează aceeași structură de date ca `map.php`, fiind folosit ca endpoint pentru componenta cartografică pe mărci.

Exemplu:

```text
http://localhost:8001/api/brand-map-data.php?year=2024
```

Parametri:

- `year`, obligatoriu;
- `fuel_type`, opțional;
- `national_category`, opțional.

### `GET /api/export.php`

Generează fișiere CSV descărcabile.

Parametri:

| Parametru | Descriere |
| --- | --- |
| `resource` | obligatoriu: `search`, `statistics`, `map` |
| `format` | opțional; în prezent doar `csv` |
| `view` | pentru exportul de statistici |
| `year` | folosit de statistici, hartă și căutare |
| `county_code` | filtru |
| `national_category` | filtru |
| `community_category` | filtru |
| `fuel_type` | filtru |
| `brand` | filtru |
| `model` | filtru pentru căutare |
| `sort_by` | sortare pentru căutare |
| `sort_order` | ordine sortare |
| `limit` | folosit de unele exporturi statistice |

Exemple:

```text
http://localhost:8001/api/export.php?resource=statistics&format=csv&view=overview&year=2024
http://localhost:8001/api/export.php?resource=search&format=csv&year=2024&brand=DACIA
http://localhost:8001/api/export.php?resource=map&format=csv&year=2024
```

Rezultat: răspuns cu `Content-Type: text/csv; charset=utf-8` și `Content-Disposition: attachment`.

Limitare: exportul `resource=search` este limitat la primele 100 de rezultate în implementarea actuală.

### `GET /api/brand-map.php`

Fișierul `public/api/brand-map.php` există, dar este gol în versiunea inspectată. Nu trebuie considerat endpoint funcțional. Folosește `brand-map-data.php` pentru datele hărții.

<p align="right">(<a href="#readme-top">Back to top</a>)</p>

---

## Testare

### Testarea paginilor

Pornește serverul:

```bash
php -S localhost:8001 -t public
```

Verifică paginile principale:

```bash
curl -I http://localhost:8001/
curl -I http://localhost:8001/index.php
curl -I http://localhost:8001/dashboard.php
curl -I http://localhost:8001/map-view.php
curl -I http://localhost:8001/search-view.php
curl -I http://localhost:8001/compare.php
curl -I http://localhost:8001/about.php
curl -I http://localhost:8001/admin/login.php
```

Rezultat așteptat: `HTTP/1.1 200 OK` pentru paginile existente.

### Testarea resurselor statice

```bash
curl -I http://localhost:8001/assets/css/main.css
curl -I http://localhost:8001/assets/css/dashboard.css
curl -I http://localhost:8001/assets/css/map.css
curl -I http://localhost:8001/assets/css/search.css
curl -I http://localhost:8001/assets/css/compare.css
curl -I http://localhost:8001/assets/js/api.js
curl -I http://localhost:8001/assets/js/filters.js
curl -I http://localhost:8001/assets/js/dashboard.js
curl -I http://localhost:8001/assets/js/map.js
curl -I http://localhost:8001/assets/data/romania-counties.geojson
```

Rezultat așteptat: `200 OK` pentru CSS, JS și GeoJSON.

### Testarea API-urilor

```bash
curl "http://localhost:8001/api/filters.php"
curl "http://localhost:8001/api/statistics.php?view=overview&year=2024"
curl "http://localhost:8001/api/statistics.php?view=yearly-totals"
curl "http://localhost:8001/api/search.php?year=2024&page=1&limit=25&sort_by=vehicle_count&sort_order=desc"
curl "http://localhost:8001/api/map.php?year=2024"
curl "http://localhost:8001/api/brand-map-data.php?year=2024"
```

Rezultat așteptat: JSON cu `status: "success"` pentru cereri valide.

Teste pentru erori:

```bash
curl "http://localhost:8001/api/statistics.php?view=overview"
curl "http://localhost:8001/api/statistics.php?view=invalid&year=2024"
curl "http://localhost:8001/api/search.php?year=2030"
curl "http://localhost:8001/api/search.php?page=0"
```

Rezultat așteptat: JSON cu `status: "error"` și mesaj de validare.

### Verificare PHP

```bash
find app public -name "*.php" -print0 | xargs -0 -n1 php -l
```

Rezultat așteptat: fiecare fișier PHP ar trebui să raporteze `No syntax errors detected`.

### Verificare SQLite

```bash
sqlite3 db/pax.db ".tables"
sqlite3 db/pax.db "select min(year), max(year), count(*) from vehicle_records;"
sqlite3 db/pax.db ".schema vehicle_records"
```

Rezultat așteptat:

- tabelele din `schema.sql` sunt prezente;
- intervalul minim/maxim al anilor este 2020-2024 după import complet;
- `vehicle_records` are cheile străine și câmpurile documentate.

### Verificare Git

```bash
git status
git diff
git diff --stat
```

Rezultat așteptat: se văd doar modificările intenționate. Pentru această actualizare, fișierul vizat este `README.md`.

### Testare vizuală în browser

Ce trebuie verificat:

- dashboard cu valori numerice și grafice;
- hartă cu județe colorate;
- search cu tabel și paginare;
- compare cu selecții A/B;
- about cu design aplicat;
- admin login și paginile admin după autentificare;
- lipsa erorilor roșii în Console;
- lipsa erorilor `404`/`500` în Network pentru resurse importante.

<p align="right">(<a href="#readme-top">Back to top</a>)</p>

---

## Checklist de testare manuală

- [ ] Serverul pornește cu `php -S localhost:8001 -t public`
- [ ] Pagina principală se încarcă
- [ ] Dashboard-ul afișează carduri și grafice
- [ ] Filtrele se populează
- [ ] Harta Leaflet se încarcă
- [ ] GeoJSON-ul se încarcă
- [ ] Click pe județ actualizează panoul lateral
- [ ] Search afișează rezultate
- [ ] Sortarea funcționează
- [ ] Paginarea funcționează
- [ ] Compare afișează diferențe
- [ ] About se încarcă fără CSS lipsă
- [ ] Admin login funcționează local
- [ ] Admin dashboard afișează statistici
- [ ] Admin import afișează batch-uri, dacă există importuri
- [ ] Admin logs afișează `logs/import_errors.log`, dacă fișierul există
- [ ] API-urile întorc JSON
- [ ] Exportul CSV descarcă fișiere
- [ ] Nu există erori roșii în Console
- [ ] Nu există 404/500 în Network pentru resurse importante

<p align="right">(<a href="#readme-top">Back to top</a>)</p>

---

## Modul admin

Modulul admin se accesează la:

```text
http://localhost:8001/admin/login.php
```

Autentificarea este implementată prin:

- `public/admin/login.php`;
- `app/helpers/auth.php`;
- `app/services/AuthService.php`;
- `app/config/config.php`.

Pentru testare locală, credențialele modulului admin se găsesc în `app/config/config.php`. Codul folosește `hash_equals()` pentru comparația valorilor introduse cu valorile din configurare și păstrează starea de autentificare în sesiunea PHP.

Pagini admin:

| Pagină | Rol |
| --- | --- |
| `/admin/login.php` | formular de autentificare |
| `/admin/index.php` | dashboard admin cu rezumat DB/importuri |
| `/admin/import.php` | listă importuri recente și sumar pe ani |
| `/admin/settings.php` | afișare configurare, căi, DB, ani și paginare |
| `/admin/logs.php` | afișare ultimele linii din `logs/import_errors.log` |

Ce trebuie testat:

- login cu credențialele locale;
- redirect către `/admin/index.php`;
- accesarea unei pagini admin fără login redirecționează la login;
- logout prin `admin/index.php?action=logout`;
- afișarea numărului de înregistrări și a importurilor;
- afișarea logului, dacă există.

Limitări:

- modulul admin este local/simplificat;
- tabela `admins` există în schema SQL, dar autentificarea curentă nu o folosește;
- nu există administrare completă de utilizatori;
- nu există upload CSV sau rulare import direct din interfața admin;
- parolele hardcodate în configurare sunt acceptabile doar pentru dezvoltare locală, nu pentru producție.

<p align="right">(<a href="#readme-top">Back to top</a>)</p>

---

## Import date

Importul este implementat în:

```text
scripts/import/import_csv_to_sqlite.php
```

Scriptul importă fișierele CSV din:

```text
raw_data/csv/
```

Fișiere așteptate:

- `parc_auto_2020_combustibil.csv`;
- `parc_auto_2021_combustibil.csv`;
- `parc_auto_2022_combustibil.csv`;
- `parc_auto_2023_combustibil.csv`;
- `parc_auto_2024_combustibil.csv`.

### Diferențe între ani

Scriptul tratează diferit structura CSV:

| Ani | Delimitator | Header așteptat |
| --- | --- | --- |
| 2020-2023 | `;` | `JUDET`, `CATEGORIE_NATIONALA`, `CATEGORIE_COMUNITARA`, `MARCA`, `DESCRIERE_COMERCIALA`, `VALUE_NAME`, `TOTAL_VEHICULE` |
| 2024 | `,` | `ID`, `JUDET`, `CATEGORIE_NATIONALA`, `CATEGORIE_COMUNITARA`, `MARCA`, `DESCRIERE_COMERCIALA`, `VALUE_NAME`, `TOTAL_VEHICULE` |

### Ce face importul

1. Determină rădăcina proiectului.
2. Deschide sau creează `db/pax.db`.
3. Activează `PRAGMA foreign_keys = ON`, `journal_mode = WAL` și `busy_timeout`.
4. Inițializează schema din `db/schema.sql`, dacă tabela `counties` nu există.
5. Populează județele din `db/seed_counties.sql`, dacă tabela este goală.
6. Creează un rând în `import_batches`.
7. Citește fiecare CSV rând cu rând.
8. Curăță valorile text.
9. Normalizează anumite nume de județe.
10. Creează sau reutilizează lookup-uri pentru categorii, combustibili și mărci.
11. Inserează rândurile valide în `vehicle_records`.
12. Loghează rândurile respinse în `logs/import_errors.log`.
13. Actualizează `rows_inserted` și `rows_rejected` în `import_batches`.

### Comenzi

Import normal:

```bash
php scripts/import/import_csv_to_sqlite.php
```

Reimport forțat:

```bash
php scripts/import/import_csv_to_sqlite.php --force
```

Variantă scurtă:

```bash
php scripts/import/import_csv_to_sqlite.php -f
```

### Rezultat așteptat

Scriptul afișează mesaje de forma:

```text
[OK] parc_auto_2024_combustibil.csv: inserate=..., respinse=...
Import finalizat.
```

Dacă un fișier lipsește:

```text
[LIPSA] Fisier inexistent: ...
```

Dacă rânduri sunt invalide, detaliile se scriu în:

```text
logs/import_errors.log
```

<p align="right">(<a href="#readme-top">Back to top</a>)</p>

---

## Roadmap

- [ ] Export CSV complet pentru toate rezultatele căutării, nu doar primele 100.
- [ ] Endpoint agregat dedicat pentru comparații A/B.
- [ ] Folosirea tabelei `admins` pentru autentificare cu parole hash-uite.
- [ ] Îmbunătățirea modulului admin cu upload CSV și declanșare import controlată.
- [ ] Teste automate pentru endpointurile API.
- [ ] Teste de integrare pentru importul CSV.
- [ ] Documentație extinsă pentru sursa oficială a datelor.
- [ ] Capturi de ecran în `docs/screenshots/`.
- [ ] Pregătire pentru deployment într-un mediu controlat.
- [ ] Tratare mai explicită a erorilor fără expunerea detaliilor interne.

Aceste elemente sunt îmbunătățiri viitoare, nu funcționalități complet implementate în versiunea curentă.

<p align="right">(<a href="#readme-top">Back to top</a>)</p>

---

## Limitări cunoscute

- `public/api/brand-map.php` este gol și nu trebuie considerat endpoint funcțional.
- `app/core/Router.php` este gol; aplicația nu folosește un router central.
- `app/services/VehicleService.php` și `app/services/StatisticsService.php` sunt goale.
- Comparațiile folosesc `/api/search.php` cu `limit=100`, deci pot fi incomplete pentru selecții mari.
- Exportul `search` descarcă primele 100 de rezultate, nu întregul set filtrat.
- Chart.js și Leaflet depind de CDN în paginile unde sunt folosite.
- Harta depinde și de tile-urile externe CartoDB Voyager.
- Modulul admin este local/simplificat și nu este proiectat ca sistem de producție.
- Tabela `admins` există, dar autentificarea folosește credențiale din config.
- Aplicația este un proiect academic, nu un sistem hardened pentru producție.

<p align="right">(<a href="#readme-top">Back to top</a>)</p>

---

## Troubleshooting

| Problemă | Cauză posibilă | Soluție |
| --- | --- | --- |
| Pagină albă | eroare PHP sau DB lipsă | rulează `php -l`, verifică logul serverului și existența `db/pax.db` |
| CSS nu se încarcă | server pornit din folder greșit | pornește cu `php -S localhost:8001 -t public` |
| JS nu se încarcă | cale greșită sau 404 | verifică `curl -I http://localhost:8001/assets/js/api.js` |
| API întoarce 500 | DB lipsă sau interogare eșuată | verifică `db/pax.db`, schema și importul |
| Baza `db/pax.db` lipsește | DB necreată | rulează `php scripts/create_db.php` și apoi importul |
| Harta nu apare | Leaflet CDN indisponibil sau JS error | verifică Network/Console și conexiunea internet |
| GeoJSON 404 | fișier lipsă în public assets | verifică `public/assets/data/romania-counties.geojson` |
| Chart.js nu este încărcat | CDN indisponibil | verifică Network pentru `cdn.jsdelivr.net/npm/chart.js` |
| Leaflet nu este disponibil | CDN indisponibil | verifică `cdn.jsdelivr.net/npm/leaflet@1.9.4` |
| Dropdown-urile sunt goale | `/api/filters.php` eșuează sau DB goală | rulează `curl "http://localhost:8001/api/filters.php"` și verifică importul |
| Portul 8001 este ocupat | alt proces folosește portul | folosește alt port, de exemplu `php -S localhost:8002 -t public` |
| Server pornit din folder greșit | căile relative nu se potrivesc | intră în rădăcina proiectului înainte de comandă |
| Admin redirectează la login | sesiune lipsă sau login invalid | autentifică-te prin `/admin/login.php` cu datele locale din config |
| Exportul CSV nu conține tot | limitare curentă a exportului search | filtrează mai strict sau implementează export paginat/complet |

<p align="right">(<a href="#readme-top">Back to top</a>)</p>

---

## Observații de securitate

- Conexiunea la DB folosește PDO.
- Interogările cu input de la utilizator folosesc prepared statements.
- Parametrii GET sunt validați în `validators.php`.
- Răspunsurile JSON sunt standardizate prin `Response.php`.
- `debug` este `false` în configurarea inspectată.
- Endpointurile prind excepțiile și returnează mesaje generale, fără detalii interne.
- Fișierele din `app/`, `db/`, `raw_data/`, `scripts/` și `logs/` nu ar trebui expuse direct; de aceea serverul se pornește cu `-t public`.
- Credențialele admin hardcodate în `config.php` sunt potrivite doar pentru testare locală/dezvoltare.
- Pentru producție, parolele trebuie mutate în variabile de mediu sau într-un mecanism securizat, iar tabela `admins` ar trebui folosită cu hash-uri robuste.
- Fișierele de test, scripturile CLI și datele brute nu trebuie mutate în `public/`.

<p align="right">(<a href="#readme-top">Back to top</a>)</p>

---

## Acknowledgments

- [PHP](https://www.php.net/) pentru runtime-ul aplicației și serverul local built-in.
- [SQLite](https://www.sqlite.org/) pentru baza de date locală.
- [Chart.js](https://www.chartjs.org/) pentru graficele din dashboard și comparații.
- [Leaflet](https://leafletjs.com/) pentru harta interactivă.
- [Best-README-Template](https://github.com/othneildrew/Best-README-Template) ca inspirație pentru structura documentației.
- GeoJSON-ul județelor inclus în proiect, folosit pentru contururile hărții.
- Fișierele CSV din `raw_data/csv/`, folosite ca sursă de date pentru import.

Sursa oficială exactă a datelor CSV nu este documentată explicit în fișierele inspectate; README-ul evită să inventeze o sursă nominală.

<p align="right">(<a href="#readme-top">Back to top</a>)</p>

---

## Note finale

PAX demonstrează un flux complet de lucru pentru o aplicație de analiză de date: preluarea datelor brute din CSV, normalizarea lor într-o bază relațională SQLite, expunerea prin API-uri JSON, consumul asincron în frontend și vizualizarea prin grafice, hartă interactivă, căutare și comparații.

Proiectul este potrivit ca aplicație academică de tip data exploration și arată cum pot fi combinate PHP simplu, PDO, SQLite, JavaScript, Chart.js și Leaflet într-o platformă coerentă pentru analiza parcului auto din România.

<p align="right">(<a href="#readme-top">Back to top</a>)</p>
