# Internal Fields for Pax

## Scopul documentului

Acest document definește câmpurile interne care vor fi păstrate și utilizate în proiectul Pax, pornind de la fișierele CSV brute analizate anterior.

Scopul acestui pas este transformarea structurii brute a surselor într-un model intern clar, stabil și ușor de folosit în baza de date, în scripturile de import și în aplicație.

Nu toate numele coloanelor din sursa brută sunt potrivite pentru utilizare internă. Din acest motiv, câmpurile sunt redenumite într-o formă mai clară și mai consecventă.

---

## Principiul de lucru

În proiect vor fi păstrate toate câmpurile considerate necesare și utile pentru analiză, agregare, filtrare, trasabilitate și integrarea cu harta județelor.

Modelul intern va conține:

- câmpuri principale de business, provenite direct sau indirect din datele brute;
- câmpuri tehnice, necesare pentru import și trasabilitate;
- un câmp suplimentar util pentru legarea cu date geografice.

---

## Câmpurile interne care vor fi păstrate

### 1. Câmpuri principale de business

- `year`
- `county_name`
- `national_category`
- `community_category`
- `brand_name`
- `model_description`
- `fuel_type`
- `vehicle_count`

### 2. Câmpuri tehnice

- `source_file`
- `import_batch_id`
- `imported_at`

### 3. Câmp suplimentar util

- `county_code`

---

## Descrierea fiecărui câmp intern

### `year`
Reprezintă anul la care se referă înregistrarea.

Acest câmp nu există explicit în conținutul rândurilor din CSV, ci este extras din numele fișierului sursă, de exemplu:
- `parc_auto_2020_combustibil.csv` -> `year = 2020`
- `parc_auto_2021_combustibil.csv` -> `year = 2021`

Acest câmp este esențial pentru comparații între ani și pentru analiza evoluției în timp.

### `county_name`
Reprezintă numele județului.

Provine din coloana brută `JUDET`.

Va fi folosit pentru:
- filtrare pe județ;
- agregare pe județ;
- legarea ulterioară cu harta județelor.

### `county_code`
Reprezintă un cod intern sau geografic asociat județului.

Acest câmp nu este prezent direct în fișierele CSV brute, dar este util pentru:
- chei stabile;
- legarea cu fișierul GeoJSON;
- evitarea dependenței exclusive de numele județului.

Acest câmp poate fi completat ulterior printr-o mapare separată.

### `national_category`
Reprezintă categoria națională a vehiculului.

Provine din coloana brută `CATEGORIE_NATIONALA`.

Exemplu: `AUTOBUZ`

### `community_category`
Reprezintă categoria comunitară a vehiculului.

Provine din coloana brută `CATEGORIE_COMUNITARA`.

Exemple: `M2`, `M3`

Acest câmp este important pentru clasificări standardizate și filtre suplimentare.

### `brand_name`
Reprezintă marca vehiculului.

Provine din coloana brută `MARCA`.

Exemple:
- `IVECO`
- `MERCEDES-BENZ`
- `MAN`

Acest câmp este util pentru analize pe producători sau mărci.

### `model_description`
Reprezintă descrierea comercială sau modelul vehiculului.

Provine din coloana brută `DESCRIERE_COMERCIALA`.

Exemple:
- `DAILY`
- `CITARO`
- `SPRINTER`

Acest câmp poate conține și valori lipsă, deci trebuie tratat atent la import.

### `fuel_type`
Reprezintă tipul de combustibil.

Provine din coloana brută `VALUE_NAME`.

Exemplu observat: `MOTORINA`

Acest câmp este foarte important pentru obiectivul principal al proiectului, deoarece permite analiza distribuției vehiculelor în funcție de combustibil.

### `vehicle_count`
Reprezintă numărul de vehicule pentru combinația de atribute din rândul respectiv.

Provine din coloana brută `TOTAL_VEHICULE`.

Acest câmp este numeric și reprezintă măsura principală folosită în agregări și statistici.

### `source_file`
Reprezintă numele fișierului din care a fost importat rândul.

Exemple:
- `parc_auto_2020_combustibil.csv`
- `parc_auto_2021_combustibil.csv`

Acest câmp este important pentru trasabilitate și debugging.

### `import_batch_id`
Reprezintă identificatorul unei runde de import.

Acest câmp este stabilit intern la rularea importului și ajută la:
- identificarea datelor încărcate într-o anumită sesiune;
- ștergerea sau refacerea unui import;
- urmărirea istoricului importurilor.

### `imported_at`
Reprezintă momentul la care rândul a fost importat în baza de date.

Acest câmp va fi generat automat la import și este util pentru audit și debugging.

---

## Maparea dintre coloanele brute și câmpurile interne

| Coloana brută CSV | Câmp intern |
|---|---|
| `JUDET` | `county_name` |
| `CATEGORIE_NATIONALA` | `national_category` |
| `CATEGORIE_COMUNITARA` | `community_category` |
| `MARCA` | `brand_name` |
| `DESCRIERE_COMERCIALA` | `model_description` |
| `VALUE_NAME` | `fuel_type` |
| `TOTAL_VEHICULE` | `vehicle_count` |

Câmpurile următoare nu vin direct din coloanele CSV, ci se adaugă în timpul importului sau prin prelucrare ulterioară:
- `year`
- `county_code`
- `source_file`
- `import_batch_id`
- `imported_at`

---

## Observații importante pentru implementare

- `year` se extrage din numele fișierului.
- `county_name` trebuie comparat ulterior cu valorile din fișierul GeoJSON.
- `community_category` poate conține spații inutile și va necesita curățare.
- `brand_name` poate conține variații de scriere și va necesita standardizare ulterioară.
- `model_description` poate avea valori lipsă.
- `fuel_type` trebuie validat pe toate fișierele pentru a identifica toate valorile distincte.
- `vehicle_count` trebuie importat ca valoare numerică.

---

## Concluzie

Modelul intern al proiectului Pax va păstra toate câmpurile relevante din sursa brută, într-o formă redenumită clar și consecvent, completată cu câmpuri tehnice pentru trasabilitate și controlul importului.

Lista finală a câmpurilor interne este:

- `year`
- `county_name`
- `county_code`
- `national_category`
- `community_category`
- `brand_name`
- `model_description`
- `fuel_type`
- `vehicle_count`
- `source_file`
- `import_batch_id`
- `imported_at`

Aceste câmpuri vor constitui baza pentru modelarea bazei de date, importul datelor și dezvoltarea aplicației.