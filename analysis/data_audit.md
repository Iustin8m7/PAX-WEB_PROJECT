# Audit date sursa - Pax

## 1. Seturi de date analizate

Au fost analizate fisierele CSV aferente anilor 2020, 2021, 2022, 2023 si 2024, provenite din datasetul public privind parcul auto din Romania.

## 2. Structura generala observata

Fisierele par sa aiba aceeasi structura intre ani.
Prima linie este de tip header.

Coloanele identificate sunt:
- JUDET
- CATEGORIE_NATIONALA
- CATEGORIE_COMUNITARA
- MARCA
- DESCRIERE_COMERCIALA
- VALUE_NAME
- TOTAL_VEHICULE

## 3. Tipuri de date observate

- JUDET: text
- CATEGORIE_NATIONALA: text
- CATEGORIE_COMUNITARA: text
- MARCA: text
- DESCRIERE_COMERCIALA: text, cu valori lipsa in unele randuri
- VALUE_NAME: text; reprezinta tipul de combustibil
- TOTAL_VEHICULE: numeric

## 4. Observatii privind calitatea datelor

- Judetele apar scrise cu majuscule si fara diacritice.
- Exista celule goale in DESCRIERE_COMERCIALA.
- Exista variatii de scriere pentru unele marci, de exemplu:
  - MERCEDES-BENZ
  - MERCEDES BENZ
- Exista probabil spatii inutile in unele coloane, de exemplu:
  - M2
  - M3
- Valorile numerice din TOTAL_VEHICULE par consistente.
- Datele sunt la un nivel de granularitate mare:
  judet + categorie nationala + categorie comunitara + marca + descriere comerciala + combustibil.

## 5. Implicatii pentru proiect

- Va fi necesara curatarea textelor la import.
- Va fi necesara adaugarea explicita a campului year.
- VALUE_NAME trebuie reinterpretat intern ca fuel_type.
- DESCRIERE_COMERCIALA nu poate fi tratat ca un camp obligatoriu.
- Judetele trebuie comparate ulterior cu denumirile din fisierul GeoJSON pentru reprezentarea pe harta.

## 6. Exemplu real observat in date

Randuri extrase din fisierul 2021 confirma structura:
JUDET | CATEGORIE_NATIONALA | CATEGORIE_COMUNITARA | MARCA | DESCRIERE_COMERCIALA | VALUE_NAME | TOTAL_VEHICULE

Exemple:
- ALBA | AUTOBUZ | M2   | IVECO | DAILY | MOTORINA | 17
- ALBA | AUTOBUZ | M2   | IVECO |        | MOTORINA | 2
- ALBA | AUTOBUZ | M2   | MERCEDES-BENZ | SPRINTER | MOTORINA | 4
- ALBA | AUTOBUZ | M3   | BMC | TBX | MOTORINA | 3
- ALBA | AUTOBUZ | M3   | BOVA | MAGIQ | MOTORINA | 2

## 7. Concluzie

Seturile de date sunt suficient de bogate si suficient de consistente pentru a sustine:
- agregari pe judet
- agregari pe combustibil
- agregari pe categorie
- comparatii intre ani
- reprezentare pe harta
- topuri pe marci
- pastrarea unui nivel intern bogat al datelor