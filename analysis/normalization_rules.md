# Reguli de normalizare pentru importul datelor

## 1. Reguli generale
- toate campurile text vor fi trecute prin trim()
- campurile goale vor fi tratate corespunzator tipului lor
- numele campurilor vor fi convertite in denumiri interne standard

## 2. JUDET
Camp sursa: JUDET
Camp intern: county_name

Reguli:
- trim()
- se pastreaza forma uppercase existenta in sursa pentru consistenta interna
- ulterior se va face maparea la county_code si la denumirea folosita in GeoJSON

## 3. CATEGORIE_NATIONALA
Camp intern: national_category

Reguli:
- trim()
- se pastreaza valoarea textuala
- va fi folosita in filtre si agregari

## 4. CATEGORIE_COMUNITARA
Camp intern: community_category

Reguli:
- trim()
- se pastreaza separat de categoria nationala
- va fi folosita pentru detalii suplimentare si eventuale filtre secundare

## 5. MARCA
Camp intern: brand_name

Reguli:
- trim()
- se pastreaza valoarea originala
- optional se poate introduce ulterior o coloana brand_normalized pentru standardizare minima

## 6. DESCRIERE_COMERCIALA
Camp intern: model_description

Reguli:
- trim()
- daca valoarea este goala, se salveaza NULL
- nu va fi folosita ca atribut obligatoriu in chei sau relatii

## 7. VALUE_NAME
Camp intern: fuel_type

Reguli:
- trim()
- redenumire interna obligatorie la fuel_type
- va fi utilizat in filtre, comparatii si vizualizari

## 8. TOTAL_VEHICULE
Camp intern: vehicle_count

Reguli:
- conversie la integer
- camp obligatoriu
- utilizat ca masura numerica principala

## 9. Campuri adaugate de sistem
- year: extras din fisierul/anul importat
- source_file: numele fisierului sursa
- import_batch_id: identificatorul importului