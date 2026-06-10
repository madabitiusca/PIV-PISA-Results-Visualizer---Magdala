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

    FOREIGN KEY (country_code)
        REFERENCES countries(code)
);

CREATE TABLE indicators (
    code TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    topic TEXT
);

CREATE TABLE education_stats (
    id INTEGER PRIMARY KEY AUTOINCREMENT,

    country_code TEXT NOT NULL,
    indicator_code TEXT NOT NULL,
    year INTEGER NOT NULL,
    value REAL,

    FOREIGN KEY (country_code)
        REFERENCES countries(code),

    FOREIGN KEY (indicator_code)
        REFERENCES indicators(code)
);

CREATE INDEX idx_pisa_country
ON pisa_results(country_code);

CREATE INDEX idx_pisa_year
ON pisa_results(year);

CREATE INDEX idx_pisa_discipline
ON pisa_results(discipline);

CREATE INDEX idx_edstats_country
ON education_stats(country_code);

CREATE INDEX idx_edstats_indicator
ON education_stats(indicator_code);

CREATE INDEX idx_edstats_year
ON education_stats(year);