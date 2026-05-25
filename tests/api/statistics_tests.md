
---

# 2. `~/Projects/pax/tests/api/statistics_tests.md`

```md id="2vmviu"
# Statistics API tests

Endpoint:
`http://localhost:8001/api/statistics.php`

## Scop
Acest endpoint trebuie sa intoarca diferite tipuri de statistici pentru dashboard, in functie de parametrul `view`.

Valorile suportate pentru `view`:
- `overview`
- `yearly-totals`
- `top-brands`
- `fuel-distribution`
- `county-ranking`
- `category-distribution`

---

## 1. Overview pe 2024

### Request
`http://localhost:8001/api/statistics.php?view=overview&year=2024`

### Ce verific
- raspuns JSON
- `status = success`
- `view = overview`
- `year = 2024`
- in `result` exista:
  - `year`
  - `total_vehicles`
  - `counties_count`
  - `brands_count`
  - `fuel_types_count`
  - `national_categories_count`

---

## 2. Overview implicit, fara view

### Request
`http://localhost:8001/api/statistics.php?year=2024`

### Ce verific
- daca `view` lipseste, endpoint-ul foloseste implicit `overview`

---

## 3. Yearly totals

### Request
`http://localhost:8001/api/statistics.php?view=yearly-totals`

### Ce verific
- nu cere `year`
- intoarce lista cu totalurile pe ani
- fiecare element trebuie sa aiba:
  - `year`
  - `total_vehicles`

Exemplu:
```json
[
  {
    "year": 2020,
    "total_vehicles": 12345
  }
]

# Teste valide

## 1. Overview pe 2024

### Request

`http://localhost:8001/api/statistics.php?view=overview&year=2024`

### Ce verific

- `status = success`
- `data.view = overview`
- `data.year = 2024`
- in `data.result` exista:
    - `year`
    - `total_vehicles`
    - `counties_count`
    - `brands_count`
    - `fuel_types_count`
    - `national_categories_count`

---

## 2. Overview pe 2023

### Request

`http://localhost:8001/api/statistics.php?view=overview&year=2023`

### Ce verific

- endpoint-ul raspunde corect si pentru alt an
- valorile difera fata de 2024

---

## 3. Overview pe 2022

### Request

`http://localhost:8001/api/statistics.php?view=overview&year=2022`

### Ce verific

- endpoint-ul raspunde corect si pentru alt an
- structura raspunsului ramane identica

---

## 4. Overview implicit, fara `view`

### Request

`http://localhost:8001/api/statistics.php?year=2024`

### Ce verific

- daca `view` lipseste, endpoint-ul foloseste `overview`
- `data.view = overview`

---

## 5. Yearly totals

### Request

`http://localhost:8001/api/statistics.php?view=yearly-totals`

### Ce verific

- raspuns fara `year`
- `data.view = yearly-totals`
- `data.result` este lista
- fiecare element are:
    - `year`
    - `total_vehicles`

### Exemplu asteptat


[  {    "year": 2020,    "total_vehicles": 12345  },  {    "year": 2021,    "total_vehicles": 13000  }]


---

## 6. Top brands pe 2024, limit 5

### Request

`http://localhost:8001/api/statistics.php?view=top-brands&year=2024&limit=5`

### Ce verific

- `data.view = top-brands`
- `data.year = 2024`
- `data.limit = 5`
- `data.result` are maximum 5 elemente
- fiecare element are:
    - `id`
    - `name`
    - `total_vehicles`

---

## 7. Top brands pe 2024, limit 10

### Request

`http://localhost:8001/api/statistics.php?view=top-brands&year=2024&limit=10`

### Ce verific

- `result` are maximum 10 elemente
- ordonarea este descrescatoare dupa `total_vehicles`

---

## 8. Top brands fara limit

### Request

`http://localhost:8001/api/statistics.php?view=top-brands&year=2024`

### Ce verific

- se foloseste limita implicita din config

---

## 9. Top brands pe 2023, limit 3

### Request

`http://localhost:8001/api/statistics.php?view=top-brands&year=2023&limit=3`

### Ce verific

- limita 3 este respectata
- rezultatele pot diferi fata de 2024

---

## 10. Fuel distribution pe 2024

### Request

`http://localhost:8001/api/statistics.php?view=fuel-distribution&year=2024`

### Ce verific

- `data.view = fuel-distribution`
- `data.year = 2024`
- fiecare element are:
    - `id`
    - `name`
    - `total_vehicles`

---

## 11. Fuel distribution pe 2023

### Request

`http://localhost:8001/api/statistics.php?view=fuel-distribution&year=2023`

### Ce verific

- rezultatele difera fata de 2024

---

## 12. County ranking pe 2024

### Request

`http://localhost:8001/api/statistics.php?view=county-ranking&year=2024`

### Ce verific

- `data.view = county-ranking`
- fiecare element are:
    - `id`
    - `code`
    - `name`
    - `total_vehicles`

---

## 13. County ranking pe 2022

### Request

`http://localhost:8001/api/statistics.php?view=county-ranking&year=2022`

### Ce verific

- clasamentul se schimba in functie de an

---

## 14. Category distribution pe 2024

### Request

`http://localhost:8001/api/statistics.php?view=category-distribution&year=2024`

### Ce verific

- `data.view = category-distribution`
- fiecare element are:
    - `id`
    - `name`
    - `total_vehicles`

---

## 15. Category distribution pe 2021

### Request

`http://localhost:8001/api/statistics.php?view=category-distribution&year=2021`

### Ce verific

- endpoint-ul functioneaza si pe alt an

---

# Teste de validare / eroare

## 16. View invalid

### Request

`http://localhost:8001/api/statistics.php?view=gresit`

### Ce verific

- eroare 400
- mesaj despre `view`
- lista valorilor permise

---

## 17. Overview fara year

### Request

`http://localhost:8001/api/statistics.php?view=overview`

### Ce verific

- eroare 400
- `year` este obligatoriu

---

## 18. Top brands fara year

### Request

`http://localhost:8001/api/statistics.php?view=top-brands&limit=5`

### Ce verific

- eroare 400
- `year` este obligatoriu

---

## 19. Fuel distribution fara year

### Request

`http://localhost:8001/api/statistics.php?view=fuel-distribution`

### Ce verific

- eroare 400

---

## 20. County ranking fara year

### Request

`http://localhost:8001/api/statistics.php?view=county-ranking`

### Ce verific

- eroare 400

---

## 21. Category distribution fara year

### Request

`http://localhost:8001/api/statistics.php?view=category-distribution`

### Ce verific

- eroare 400

---

## 22. Year invalid ca tip

### Request

`http://localhost:8001/api/statistics.php?view=overview&year=abc`

### Ce verific

- eroare 400
- mesaj despre numar intreg valid

---

## 23. Year sub interval

### Request

`http://localhost:8001/api/statistics.php?view=overview&year=2018`

### Ce verific

- eroare 400
- anul este in afara intervalului permis

---

## 24. Year peste interval

### Request

`http://localhost:8001/api/statistics.php?view=overview&year=2030`

### Ce verific

- eroare 400

---

## 25. Limit = 0

### Request

`http://localhost:8001/api/statistics.php?view=top-brands&year=2024&limit=0`

### Ce verific

- eroare 400

---

## 26. Limit prea mare

### Request

`http://localhost:8001/api/statistics.php?view=top-brands&year=2024&limit=1000`

### Ce verific

- eroare 400

---

## 27. Limit invalid ca tip

### Request

`http://localhost:8001/api/statistics.php?view=top-brands&year=2024&limit=abc`

### Ce verific

- eroare 400

---

# Teste comparative utile

## 28. Overview pe toti anii

### Request-uri

- `http://localhost:8001/api/statistics.php?view=overview&year=2020`
- `http://localhost:8001/api/statistics.php?view=overview&year=2021`
- `http://localhost:8001/api/statistics.php?view=overview&year=2022`
- `http://localhost:8001/api/statistics.php?view=overview&year=2023`
- `http://localhost:8001/api/statistics.php?view=overview&year=2024`

### Ce verific

- datele variaza pe ani
- endpoint-ul raspunde consecvent pentru toate valorile valide

---

## 29. Top brands pe mai multi ani

### Request-uri

- `http://localhost:8001/api/statistics.php?view=top-brands&year=2020&limit=5`
- `http://localhost:8001/api/statistics.php?view=top-brands&year=2021&limit=5`
- `http://localhost:8001/api/statistics.php?view=top-brands&year=2022&limit=5`
- `http://localhost:8001/api/statistics.php?view=top-brands&year=2023&limit=5`
- `http://localhost:8001/api/statistics.php?view=top-brands&year=2024&limit=5`

### Ce verific

- topurile se schimba intre ani

---

## 30. Fuel distribution pe mai multi ani

### Request-uri

- `http://localhost:8001/api/statistics.php?view=fuel-distribution&year=2020`
- `http://localhost:8001/api/statistics.php?view=fuel-distribution&year=2021`
- `http://localhost:8001/api/statistics.php?view=fuel-distribution&year=2022`
- `http://localhost:8001/api/statistics.php?view=fuel-distribution&year=2023`
- `http://localhost:8001/api/statistics.php?view=fuel-distribution&year=2024`

### Ce verific

- distributiile se schimba intre ani

---

# Teste din terminal


curl "http://localhost:8001/api/statistics.php?view=overview&year=2024"curl "http://localhost:8001/api/statistics.php?view=yearly-totals"curl "http://localhost:8001/api/statistics.php?view=top-brands&year=2024&limit=5"curl "http://localhost:8001/api/statistics.php?view=fuel-distribution&year=2024"curl "http://localhost:8001/api/statistics.php?view=county-ranking&year=2024"curl "http://localhost:8001/api/statistics.php?view=category-distribution&year=2024"


---

# Set minim de teste recomandat

http://localhost:8001/api/statistics.php?view=overview&year=2024http://localhost:8001/api/statistics.php?view=yearly-totalshttp://localhost:8001/api/statistics.php?view=top-brands&year=2024&limit=5http://localhost:8001/api/statistics.php?view=fuel-distribution&year=2024http://localhost:8001/api/statistics.php?view=county-ranking&year=2024http://localhost:8001/api/statistics.php?view=category-distribution&year=2024http://localhost:8001/api/statistics.php?view=gresithttp://localhost:8001/api/statistics.php?view=overview```

---

## Ce confirma acest fisier de teste

Daca testele merg, atunci inseamna ca:

- validatorii functioneaza corect
- `StatisticsRepository.php` functioneaza
- endpoint-ul selecteaza corect metoda in functie de `view`
- raspunsurile JSON sunt standardizate
- cazurile de eroare sunt tratate corect
```

