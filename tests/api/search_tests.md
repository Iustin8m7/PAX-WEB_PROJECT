# Search API tests

Endpoint:
`http://localhost:8001/api/search.php`

## Scop
Acest endpoint trebuie sa permita:
- cautare multi-criteriala
- filtrare combinata
- sortare
- paginare
- obtinerea numarului total de rezultate

El este baza pentru:
- tabelul de rezultate
- navigarea prin rezultate
- cautare dupa mai multe criterii
- eventual export CSV pe date filtrate

---

## Parametri suportati

### Filtre
- `year`
- `county_code`
- `national_category`
- `community_category`
- `brand`
- `fuel_type`
- `model`

### Paginare
- `page`
- `limit`

### Sortare
- `sort`
- `order`

---

# Teste valide de baza

## 1. Cautare doar dupa an

### Request
`http://localhost:8001/api/search.php?year=2024`

### Ce verific
- raspunsul este JSON
- `status = success`
- `filters.year = 2024`
- exista cheia `pagination`
- exista cheia `sorting`
- exista cheia `result`

---

## 2. Cautare dupa an si judet

### Request
`http://localhost:8001/api/search.php?year=2024&county_code=IS`

### Ce verific
- `filters.year = 2024`
- `filters.county_code = IS`
- rezultatele sunt filtrate pe judetul respectiv

---

## 3. Cautare dupa an si combustibil

### Request
`http://localhost:8001/api/search.php?year=2024&fuel_type=MOTORINA`

### Ce verific
- `filters.fuel_type = MOTORINA`
- rezultatele sunt filtrate pe combustibil

---

## 4. Cautare dupa an, judet si combustibil

### Request
`http://localhost:8001/api/search.php?year=2024&county_code=IS&fuel_type=MOTORINA`

### Ce verific
- filtrele se combina corect
- rezultatele sunt mai restranse

---

## 5. Cautare dupa brand

### Request
`http://localhost:8001/api/search.php?brand=IVECO`

### Ce verific
- `filters.brand = IVECO`
- rezultatele contin doar acel brand

---

## 6. Cautare dupa model

### Request
`http://localhost:8001/api/search.php?model=SPRINTER`

### Ce verific
- filtrarea pe `model_description` functioneaza prin `LIKE`
- rezultatele contin textul cautat in model

---

## 7. Cautare dupa an si model

### Request
`http://localhost:8001/api/search.php?year=2024&model=SPRINTER`

### Ce verific
- filtrul pe an si filtrul pe model functioneaza impreuna

---

## 8. Cautare dupa categorie nationala

### Request
`http://localhost:8001/api/search.php?national_category=AUTOBUZ`

### Ce verific
- `filters.national_category = AUTOBUZ`
- rezultatele sunt filtrate pe categoria nationala

---

## 9. Cautare dupa categorie comunitara

### Request
`http://localhost:8001/api/search.php?community_category=M2`

### Ce verific
- `filters.community_category = M2`
- rezultatele sunt filtrate pe categoria comunitara

---

# Teste pentru sortare

## 10. Sortare dupa vehicle_count desc

### Request
`http://localhost:8001/api/search.php?year=2024&sort=vehicle_count&order=desc`

### Ce verific
- `sorting.sort = vehicle_count`
- `sorting.order = desc`
- rezultatele sunt ordonate descrescator dupa `vehicle_count`

---

## 11. Sortare dupa vehicle_count asc

### Request
`http://localhost:8001/api/search.php?year=2024&sort=vehicle_count&order=asc`

### Ce verific
- rezultatele sunt ordonate crescator dupa `vehicle_count`

---

## 12. Sortare dupa county

### Request
`http://localhost:8001/api/search.php?year=2024&sort=county&order=asc`

### Ce verific
- ordonarea se face dupa numele judetului

---

## 13. Sortare dupa brand

### Request
`http://localhost:8001/api/search.php?year=2024&sort=brand&order=asc`

### Ce verific
- ordonarea se face dupa numele marcii

---

## 14. Sortare dupa fuel_type

### Request
`http://localhost:8001/api/search.php?year=2024&sort=fuel_type&order=asc`

### Ce verific
- ordonarea se face dupa combustibil

---

# Teste pentru paginare

## 15. Prima pagina, limita 10

### Request
`http://localhost:8001/api/search.php?year=2024&page=1&limit=10`

### Ce verific
- `pagination.page = 1`
- `pagination.limit = 10`
- `result` are maximum 10 elemente

---

## 16. Pagina a doua, limita 10

### Request
`http://localhost:8001/api/search.php?year=2024&page=2&limit=10`

### Ce verific
- `pagination.page = 2`
- rezultatele difera fata de pagina 1

---

## 17. Limita 5

### Request
`http://localhost:8001/api/search.php?year=2024&limit=5`

### Ce verific
- `result` are maximum 5 elemente

---

## 18. Fara page si fara limit

### Request
`http://localhost:8001/api/search.php?year=2024`

### Ce verific
- se folosesc valorile implicite:
  - `page = 1`
  - `limit = default_page_size` din config

---

# Teste combinate

## 19. Brand + sortare + paginare

### Request
`http://localhost:8001/api/search.php?brand=IVECO&sort=vehicle_count&order=desc&page=1&limit=20`

### Ce verific
- filtrarea pe brand functioneaza
- sortarea functioneaza
- paginarea functioneaza

---

## 20. An + judet + combustibil + sortare

### Request
`http://localhost:8001/api/search.php?year=2024&county_code=IS&fuel_type=MOTORINA&sort=vehicle_count&order=desc`

### Ce verific
- cautarea multi-criteriala functioneaza corect

---

## 21. Model + paginare

### Request
`http://localhost:8001/api/search.php?model=SPRINTER&page=1&limit=20`

### Ce verific
- cautarea textuala si paginarea functioneaza impreuna

---

# Teste de validare / eroare

## 22. Year invalid ca tip

### Request
`http://localhost:8001/api/search.php?year=abc`

### Ce verific
- eroare 400
- mesaj despre numar intreg valid

---

## 23. Year sub interval

### Request
`http://localhost:8001/api/search.php?year=2018`

### Ce verific
- eroare 400
- mesaj ca anul este in afara intervalului permis

---

## 24. Year peste interval

### Request
`http://localhost:8001/api/search.php?year=2030`

### Ce verific
- eroare 400

---

## 25. Page = 0

### Request
`http://localhost:8001/api/search.php?year=2024&page=0`

### Ce verific
- eroare 400
- `page` trebuie sa fie >= 1

---

## 26. Page negativ

### Request
`http://localhost:8001/api/search.php?year=2024&page=-1`

### Ce verific
- eroare 400

---

## 27. Page invalid ca tip

### Request
`http://localhost:8001/api/search.php?year=2024&page=abc`

### Ce verific
- eroare 400

---

## 28. Limit = 0

### Request
`http://localhost:8001/api/search.php?year=2024&limit=0`

### Ce verific
- eroare 400
- `limit` trebuie sa fie intre 1 si valoarea maxima din config

---

## 29. Limit prea mare

### Request
`http://localhost:8001/api/search.php?year=2024&limit=1000`

### Ce verific
- eroare 400

---

## 30. Limit invalid ca tip

### Request
`http://localhost:8001/api/search.php?year=2024&limit=abc`

### Ce verific
- eroare 400

---

## 31. Sort invalid

### Request
`http://localhost:8001/api/search.php?year=2024&sort=gresit`

### Ce verific
- eroare 400
- mesaj despre valoare nepermisa pentru `sort`
- lista valorilor permise

---

## 32. Order invalid

### Request
`http://localhost:8001/api/search.php?year=2024&order=gresit`

### Ce verific
- eroare 400
- mesaj ca `order` trebuie sa fie `asc` sau `desc`

---

# Structura de raspuns asteptata

Pentru cazurile valide, raspunsul trebuie sa aiba forma:

```json id="cmgflr"
{
  "status": "success",
  "data": {
    "filters": {
      "year": 2024,
      "county_code": "IS",
      "national_category": null,
      "community_category": null,
      "brand": null,
      "fuel_type": "MOTORINA",
      "model": null
    },
    "pagination": {
      "page": 1,
      "limit": 25,
      "total_results": 123,
      "total_pages": 5
    },
    "sorting": {
      "sort": "vehicle_count",
      "order": "desc"
    },
    "result": [
      {
        "id": 1,
        "year": 2024,
        "county_code": "IS",
        "county_name": "IASI",
        "national_category": "AUTOBUZ",
        "community_category": "M2",
        "brand_name": "IVECO",
        "model_description": "DAILY",
        "fuel_type": "MOTORINA",
        "vehicle_count": 18
      }
    ]
  }
}