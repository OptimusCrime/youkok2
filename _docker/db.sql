CREATE TABLE element (
  id serial PRIMARY KEY,
  name TEXT NOT NULL,
  slug TEXT NULL DEFAULT NULL,
  uri TEXT NULL,
  parent INT NULL,
  empty BOOL NOT NULL,
  checksum TEXT NULL,
  size INT NULL,
  directory BOOL NOT NULL,
  pending BOOL NOT NULL,
  deleted BOOL NOT NULL,
  link TEXT NULL,
  added timestamptz NOT NULL DEFAULT now(),
  requested_deletion BOOL NOT NULL,
  downloads_today INT NOT NULL DEFAULT 0,
  downloads_week INT NOT NULL DEFAULT 0,
  downloads_month INT NOT NULL DEFAULT 0,
  downloads_year INT NOT NULL DEFAULT 0,
  downloads_all INT NOT NULL DEFAULT 0,
  last_visited timestamptz NULL DEFAULT NULL,
  last_downloaded timestamptz NULL DEFAULT NULL
);

CREATE TABLE download (
    element INT NOT NULL,
    date DATE NOT NULL,
    downloads INT NOT NULL
);

ALTER TABLE download ADD PRIMARY KEY (element, date);
