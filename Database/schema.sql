DROP TABLE IF EXISTS education_stats;
DROP TABLE IF EXISTS pisa_results;
DROP TABLE IF EXISTS indicators;
DROP TABLE IF EXISTS countries;

CREATE TABLE countries (
    code TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    region TEXT,
    income_group TEXT
);

CREATE TABLE pisa_results (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    country_code TEXT NOT NULL,
    year INTEGER NOT NULL,
    discipline TEXT NOT NULL,
    subject TEXT NOT NULL,
    score REAL NOT NULL,
    FOREIGN KEY (country_code) REFERENCES countries(code)
);

CREATE TABLE indicators (
    code TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    topic TEXT,
    source TEXT DEFAULT 'World Bank EdStats'
);

CREATE TABLE education_stats (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    country_code TEXT NOT NULL,
    indicator_code TEXT NOT NULL,
    year INTEGER NOT NULL,
    value REAL,
    FOREIGN KEY (country_code) REFERENCES countries(code),
    FOREIGN KEY (indicator_code) REFERENCES indicators(code)
);

CREATE INDEX idx_pisa_filter
ON pisa_results(country_code, year, discipline, subject);

CREATE INDEX idx_edstats_filter
ON education_stats(country_code, indicator_code, year);