
---

# 3. `~/Projects/pax/tests/api/map_tests.md`

```md id="mu20k5"
# Map API tests

Endpoint:
`http://localhost:8001/api/map.php`

## Scop
Acest endpoint trebuie sa intoarca date agregate pe judete pentru componenta cartografica.

Parametri suportati:
- `year` — obligatoriu
- `fuel_type` — optional
- `national_category` — optional

---

## 1. Test de baza, fara filtre optionale

### Request
`http://localhost:8001/api/map.php?year=2024`

### Ce verific
- raspuns JSON
- `status = success`
- `year = 2024`
- `filters.fuel_type = null`
- `filters.national_category = null`
- `result` este lista de judete

### Structura asteptata
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
        "county_code": "AB",
        "county_name": "ALBA",
        "total_vehicles": 123
      }
    ]
  }
}

# Teste valide de baza

## 1. Test de baza, fara filtre optionale

### Request

`http://localhost:8001/api/map.php?year=2024`

### Ce verific

- raspuns JSON
- `status = success`
- `data.year = 2024`
- `filters.fuel_type = null`
- `filters.national_category = null`
- `result` este lista de judete

### Ce verifica fiecare element din `result`

- exista `county_code`
- exista `county_name`
- exista `total_vehicles`

---

## 2. Test pe alt an

### Request

`http://localhost:8001/api/map.php?year=2023`

### Ce verific

- datele difera fata de 2024
- endpoint-ul nu este hardcodat

---

## 3. Filtru doar pe combustibil

### Request

`http://localhost:8001/api/map.php?year=2024&fuel_type=MOTORINA`

### Ce verific

- `filters.fuel_type = MOTORINA`
- `filters.national_category = null`
- rezultatele sunt filtrate pe combustibil

---

## 4. Filtru pe alt combustibil

### Request

`http://localhost:8001/api/map.php?year=2024&fuel_type=BENZINA`

### Ce verific

- rezultatele se schimba fata de MOTORINA
- filtrarea dupa combustibil functioneaza

---

## 5. Filtru doar pe categorie nationala

### Request

`http://localhost:8001/api/map.php?year=2024&national_category=AUTOBUZ`

### Ce verific

- `filters.national_category = AUTOBUZ`
- `filters.fuel_type = null`
- rezultatele sunt filtrate pe categoria nationala

---

## 6. Filtru pe alta categorie nationala

### Request

`http://localhost:8001/api/map.php?year=2024&national_category=AUTOTURISM`

### Ce verific

- daca acea categorie exista in DB, rezultatele sunt filtrate corect

---

## 7. Ambele filtre simultan

### Request

`http://localhost:8001/api/map.php?year=2024&fuel_type=MOTORINA&national_category=AUTOBUZ`

### Ce verific

- ambele filtre sunt aplicate
- structura `filters` reflecta exact request-ul

---

## 8. Alt an + ambele filtre

### Request

`http://localhost:8001/api/map.php?year=2023&fuel_type=MOTORINA&national_category=AUTOBUZ`

### Ce verific

- anul influenteaza rezultatul
- rezultatele difera fata de 2024

---

# Teste de validare / eroare

## 9. Lipsa year

### Request

`http://localhost:8001/api/map.php`

### Ce verific

- eroare 400
- `year` este obligatoriu

---

## 10. Year invalid ca tip

### Request

`http://localhost:8001/api/map.php?year=abc`

### Ce verific

- eroare 400
- mesaj despre numar intreg valid

---

## 11. Year sub interval

### Request

`http://localhost:8001/api/map.php?year=2018`

### Ce verific

- eroare 400
- anul este in afara intervalului permis

---

## 12. Year peste interval

### Request

`http://localhost:8001/api/map.php?year=2030`

### Ce verific

- eroare 400

---

## 13. fuel_type gol

### Request

`http://localhost:8001/api/map.php?year=2024&fuel_type=`

### Ce verific

- `fuel_type` devine `null`
- comportamentul este acelasi ca fara filtru pe combustibil

---

## 14. national_category goala

### Request

`http://localhost:8001/api/map.php?year=2024&national_category=`

### Ce verific

- `national_category` devine `null`

---

## 15. Ambele filtre goale

### Request

`http://localhost:8001/api/map.php?year=2024&fuel_type=&national_category=`

### Ce verific

- comportamentul este acelasi ca la:  
    `http://localhost:8001/api/map.php?year=2024`

---

# Teste comparative utile

## 16. General vs filtrat pe combustibil

### Request-uri

- `http://localhost:8001/api/map.php?year=2024`
- `http://localhost:8001/api/map.php?year=2024&fuel_type=MOTORINA`

### Ce verific

- totalurile filtrate sunt, in general, mai mici sau egale fata de totalurile generale

---

## 17. General vs filtrat pe categorie

### Request-uri

- `http://localhost:8001/api/map.php?year=2024`
- `http://localhost:8001/api/map.php?year=2024&national_category=AUTOBUZ`

### Ce verific

- distributia pe judete se schimba cand aplici categoria

---

## 18. Doua combinatii diferite

### Request-uri

- `http://localhost:8001/api/map.php?year=2024&fuel_type=MOTORINA&national_category=AUTOBUZ`
- `http://localhost:8001/api/map.php?year=2024&fuel_type=BENZINA&national_category=AUTOBUZ`

### Ce verific

- rezultatele difera in functie de combustibil

---

## 19. Filtru doar pe combustibil, alt an

### Request

`http://localhost:8001/api/map.php?year=2022&fuel_type=MOTORINA`

### Ce verific

- anul si combustibilul influenteaza simultan rezultatul

---

## 20. Filtru doar pe categorie, alt an

### Request

`http://localhost:8001/api/map.php?year=2022&national_category=AUTOBUZ`

### Ce verific

- anul si categoria influenteaza simultan rezultatul

---

# Teste din terminal


curl "http://localhost:8001/api/map.php?year=2024"
curl "http://localhost:8001/api/map.php?year=2024&fuel_type=MOTORINA"
curl "http://localhost:8001/api/map.php?year=2024&national_category=AUTOBUZ"
curl "http://localhost:8001/api/map.php?year=2024&fuel_type=MOTORINA&national_category=AUTOBUZ"
curl "http://localhost:8001/api/map.php?year=abc"
curl "http://localhost:8001/api/map.php"


---

# Set minim de teste recomandat


http://localhost:8001/api/map.php?year=2024http://localhost:8001/api/map.php?year=2024&fuel_type=MOTORINAhttp://localhost:8001/api/map.php?year=2024&national_category=AUTOBUZhttp://localhost:8001/api/map.php?year=2024&fuel_type=MOTORINA&national_category=AUTOBUZhttp://localhost:8001/api/map.phphttp://localhost:8001/api/map.php?year=abc


---

## Ce confirma acest fisier de teste

Daca testele merg, atunci inseamna ca:

- `MapRepository.php` functioneaza corect
- endpoint-ul citeste si valideaza parametrii bine
- filtrele optionale sunt aplicate corect
- raspunsul JSON este potrivit pentru componenta cartografica
- endpoint-ul este pregatit pentru integrarea cu front-end-ul
```