# Proiectarea bazei de date - Pax

## 1. counties
Stocheaza judetele Romaniei.

Campuri:
- id
- code
- name

Rol:
- utilizat pentru filtrare
- utilizat pentru harta
- utilizat in tabela vehicle_records

## 2. vehicle_categories
Stocheaza categoriile nationale de vehicule.

Campuri:
- id
- name

Rol:
- utilizat in filtre si agregari

## 3. community_categories
Stocheaza categoriile comunitare ale vehiculelor.

Campuri:
- id
- name

Rol:
- ofera nivel suplimentar de detaliu

## 4. fuel_types
Stocheaza tipurile de combustibil.

Campuri:
- id
- name

Rol:
- utilizat in filtre, comparatii si grafice

## 5. brands
Stocheaza marcile de vehicule.

Campuri:
- id
- name

Rol:
- utilizat in analize mai detaliate si in modul admin

## 6. import_batches
Stocheaza informatii despre importurile efectuate.

Campuri:
- id
- source_year
- source_file
- imported_at
- rows_inserted
- rows_rejected
- notes

Rol:
- trasabilitate
- administrare
- debugging

## 7. vehicle_records
Tabela principala cu datele detaliate despre parc auto.

Campuri:
- id
- year
- county_id
- national_category_id
- community_category_id
- brand_id
- model_description
- fuel_type_id
- vehicle_count
- import_batch_id

Rol:
- baza pentru dashboard
- baza pentru comparatii
- baza pentru grafice
- baza pentru harta

## 8. admins
Stocheaza utilizatorii administratori.

Campuri:
- id
- username
- password_hash
- created_at

## Relatii

- counties 1 -> N vehicle_records
- vehicle_categories 1 -> N vehicle_records
- community_categories 1 -> N vehicle_records
- fuel_types 1 -> N vehicle_records
- brands 1 -> N vehicle_records
- import_batches 1 -> N vehicle_records