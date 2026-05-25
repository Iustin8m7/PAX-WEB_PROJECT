# Filters API tests

Endpoint:
`http://localhost:8001/api/filters.php`

## Scop
Acest endpoint trebuie sa intoarca toate valorile disponibile pentru filtrele din aplicatie:
- ani
- judete
- categorii nationale
- categorii comunitare
- combustibili
- marci

---

## Test principal

### Request
`http://localhost:8001/api/filters.php`

### Ce verific
- raspunsul este JSON
- `status` este `success`
- exista cheia `data`
- in `data` exista:
  - `years`
  - `counties`
  - `national_categories`
  - `community_categories`
  - `fuel_types`
  - `brands`

### Structura asteptata
```json
{
  "status": "success",
  "data": {
    "years": [...],
    "counties": [...],
    "national_categories": [...],
    "community_categories": [...],
    "fuel_types": [...],
    "brands": [...]
  }
}

# Teste principale

## 1. Test principal al endpoint-ului

### Request

`http://localhost:8001/api/filters.php`

### Ce verific

- raspunsul este JSON
- codul HTTP este 200
- `status = success`
- exista cheia `data`

### Ce verific in `data`

- exista `years`
- exista `counties`
- exista `national_categories`
- exista `community_categories`
- exista `fuel_types`
- exista `brands`

### Rezultat asteptat

Endpoint-ul trebuie sa raspunda fara parametri si sa intoarca toate filtrele.

---

## 2. Verificarea anilor

### Request

`http://localhost:8001/api/filters.php`

### Ce verific in `data.years`

- este un array simplu
- valorile sunt numerice
- nu sunt duplicate
- sunt ordonate crescator

### Exemplu asteptat


[2020, 2021, 2022, 2023, 2024]


### Ce confirma

- metoda `getAvailableYears()` functioneaza
- `SELECT DISTINCT year` functioneaza
- `ORDER BY year ASC` functioneaza

---

## 3. Verificarea judetelor

### Request

`http://localhost:8001/api/filters.php`

### Ce verific in `data.counties`

Fiecare element trebuie sa aiba:

- `code`
- `name`

### Exemplu asteptat


[  {    "code": "AB",    "name": "ALBA"  },  {    "code": "AR",    "name": "ARAD"  }]


### Ce verific suplimentar

- nu exista duplicate
- sunt ordonate alfabetic dupa `name`
- codurile par corecte (`AB`, `IS`, `CJ`, etc.)

### Ce confirma

- join-ul cu tabela `counties` functioneaza
- codul si numele judetului sunt aduse corect
- filtrele pentru harta si search pot folosi aceste valori

---

## 4. Verificarea categoriilor nationale

### Request

`http://localhost:8001/api/filters.php`

### Ce verific in `data.national_categories`

Fiecare element trebuie sa aiba:

- `id`
- `name`

### Exemplu asteptat```
[  {    "id": 1,    "name": "AUTOBUZ"  }]


### Ce verific suplimentar

- sunt ordonate dupa `name`
- nu exista duplicate logice
- numele sunt lizibile si curate

### Ce confirma

- join-ul cu `vehicle_categories` functioneaza
- aplicatia poate popula filtrul pentru categorie nationala

---

## 5. Verificarea categoriilor comunitare

### Request

`http://localhost:8001/api/filters.php`

### Ce verific in `data.community_categories`

Fiecare element trebuie sa aiba:

- `id`
- `name`

### Exemplu asteptat


[  {    "id": 1,    "name": "M2"  },  {    "id": 2,    "name": "M3"  }]


### Ce verific suplimentar

- nu exista duplicate
- valorile nu contin spatii inutile
- sunt ordonate

### Ce confirma

- join-ul cu `community_categories` functioneaza
- aplicatia poate popula filtrul pentru categorie comunitara

---

## 6. Verificarea combustibililor

### Request

`http://localhost:8001/api/filters.php`

### Ce verific in `data.fuel_types`

Fiecare element trebuie sa aiba:

- `id`
- `name`

### Exemplu asteptat


[  {    "id": 1,    "name": "BENZINA"  },  {    "id": 2,    "name": "MOTORINA"  }]


### Ce verific suplimentar

- valorile sunt reale
- sunt ordonate alfabetic dupa `name`
- nu exista duplicate

### Ce confirma

- join-ul cu `fuel_types` functioneaza
- filtrarea dupa combustibil se poate face corect



## 7. Verificarea marcilor

### Request

`http://localhost:8001/api/filters.php`

### Ce verific in `data.brands`

Fiecare element trebuie sa aiba:

- `id`
- `name`

### Exemplu asteptat


[  {    "id": 1,    "name": "IVECO"  },  {    "id": 2,    "name": "MAN"  }]


### Ce verific suplimentar

- sunt ordonate dupa `name`
- nu exista duplicate
- valorile sunt potrivite pentru dropdown-ul de cautare

### Ce confirma

- join-ul cu `brands` functioneaza
- endpoint-ul poate alimenta filtrul de marca

---

# Teste de consistenta

## 8. Verificare ca endpoint-ul raspunde fara parametri suplimentari

### Request

`http://localhost:8001/api/filters.php?x=1`

### Ce verific

- endpoint-ul continua sa functioneze
- eventual ignora parametrii nefolositi
- raspunsul ramane corect

---

## 9. Verificare a structurii complete

### Request

`http://localhost:8001/api/filters.php`

### Ce verific

- `status`
- `data.years`
- `data.counties`
- `data.national_categories`
- `data.community_categories`
- `data.fuel_types`
- `data.brands`

### Ce confirma

- endpoint-ul este complet
- `getAllFilters()` functioneaza corect

---

# Teste utile pentru verificare manuala in browser

## 10. Deschidere directa in browser

`http://localhost:8001/api/filters.php`

### Ce verific

- browserul afiseaza JSON, nu pagina HTML
- raspunsul nu este gol
- nu apare eroare 500

---

## 11. Verificare ca listele nu sunt goale

`http://localhost:8001/api/filters.php`

### Ce verific

- `years` nu este gol
- `counties` nu este gol
- `national_categories` nu este gol
- `community_categories` nu este gol
- `fuel_types` nu este gol
- `brands` nu este gol

### Observatie

Daca unele liste sunt goale, problema poate fi:

- lipsa datelor in DB
- lipsa join-urilor corecte
- import incomplet

---

# Teste din terminal

curl "http://localhost:8001/api/filters.php"curl -i "http://localhost:8001/api/filters.php"


### Ce verific

- codul HTTP este 200
- `Content-Type` este `application/json`
- body-ul contine `status` si `data`

---

# Set minim de teste recomandat


http://localhost:8001/api/filters.phphttp://localhost:8001/api/filters.php?x=1


---

## Ce confirma acest fisier de teste

Daca testele merg, atunci inseamna ca:

- `Response.php` functioneaza
- `Database.php` functioneaza
- `FilterRepository.php` functioneaza
- baza de date este accesibila
- endpoint-ul este disponibil si raspunde corect
```