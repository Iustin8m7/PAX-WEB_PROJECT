CREATE TABLE counties (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE vehicle_categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE community_categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE fuel_types (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE brands (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE import_batches (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    source_year INTEGER NOT NULL,
    source_file TEXT NOT NULL,
    imported_at TEXT NOT NULL,
    rows_inserted INTEGER DEFAULT 0,
    rows_rejected INTEGER DEFAULT 0,
    notes TEXT
);

CREATE TABLE admins (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    created_at TEXT NOT NULL
);

CREATE TABLE vehicle_records (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    year INTEGER NOT NULL,
    county_id INTEGER NOT NULL,
    national_category_id INTEGER NOT NULL,
    community_category_id INTEGER,
    brand_id INTEGER,
    model_description TEXT,
    fuel_type_id INTEGER NOT NULL,
    vehicle_count INTEGER NOT NULL,
    import_batch_id INTEGER,
    FOREIGN KEY (county_id) REFERENCES counties(id),
    FOREIGN KEY (national_category_id) REFERENCES vehicle_categories(id),
    FOREIGN KEY (community_category_id) REFERENCES community_categories(id),
    FOREIGN KEY (brand_id) REFERENCES brands(id),
    FOREIGN KEY (fuel_type_id) REFERENCES fuel_types(id),
    FOREIGN KEY (import_batch_id) REFERENCES import_batches(id)
);

CREATE INDEX idx_vehicle_records_year ON vehicle_records(year);
CREATE INDEX idx_vehicle_records_county ON vehicle_records(county_id);
CREATE INDEX idx_vehicle_records_fuel ON vehicle_records(fuel_type_id);
CREATE INDEX idx_vehicle_records_category ON vehicle_records(national_category_id);
CREATE INDEX idx_vehicle_records_brand ON vehicle_records(brand_id);
CREATE INDEX idx_vehicle_records_year_county ON vehicle_records(year, county_id);