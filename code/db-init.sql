CREATE TABLE MouseHistory
(
  uid INTEGER,
  x INTEGER,
  y INTEGER,
  t INTEGER
);
INSERT INTO MouseHistory (uid, x, y, t) VALUES (0,0,0,0);

CREATE TABLE KeyValue
(
  k VARCHAR(128),
  v VARCHAR(128)
);
INSERT INTO KeyValue (k, v) VALUES ("RecentUser", "0");