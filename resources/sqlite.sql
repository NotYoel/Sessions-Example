-- #!sqlite
-- #{ table
-- #{ users
CREATE TABLE IF NOT EXISTS players
(
    xuid VARCHAR
(
    32
) PRIMARY KEY NOT NULL,
    username TEXT DEFAULT "",
    money INTEGER
    );
-- #}
-- #}

-- #{ add
-- #{ player
-- # :xuid string
-- # :username string
-- # :money int
INSERT
OR IGNORE INTO players (xuid, username, money)
    VALUES (:xuid, :username, :money);
-- #&
UPDATE players
SET money = :money
WHERE xuid = :xuid;
-- #}
-- #}

-- #{ get
-- #{ player
-- # :xuid string
SELECT *
FROM players
WHERE xuid = :xuid;
-- #}
-- #}