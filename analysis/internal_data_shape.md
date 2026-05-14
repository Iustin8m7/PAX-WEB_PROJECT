# Forma interna a datelor

Aplicatia Pax va pastra datele la un nivel detaliat suficient pentru:
- filtrare multi-criteriala
- comparatii intre ani
- comparatii intre judete
- agregari pe combustibil
- agregari pe categorie
- topuri pe marci
- detalii suplimentare in modul admin

Forma interna finala a unui rand va contine:
- year
- county_name
- national_category
- community_category
- brand_name
- model_description
- fuel_type
- vehicle_count
- source_file
- import_batch_id

Aceasta structura permite o aplicatie bogata, fara a pierde flexibilitatea necesara pentru dashboard si reprezentarea cartografica.