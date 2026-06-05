CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE users
    ADD COLUMN IF NOT EXISTS username VARCHAR(120) NULL AFTER name,
    ADD COLUMN IF NOT EXISTS email VARCHAR(255) NULL AFTER username,
    ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER role,
    ADD COLUMN IF NOT EXISTS last_login DATETIME NULL AFTER is_active,
    ADD COLUMN IF NOT EXISTS created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER last_login,
    ADD COLUMN IF NOT EXISTS updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER created_at;

UPDATE users
SET username = LOWER(REPLACE(name, ' ', '.'))
WHERE (username IS NULL OR username = '');

CREATE TABLE IF NOT EXISTS documents (
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(120) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'draft',
    file_type VARCHAR(20) NOT NULL DEFAULT 'PDF',
    file_size VARCHAR(50) NOT NULL DEFAULT '',
    summary TEXT NOT NULL,
    published_at DATETIME NULL,
    is_visible TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS news_posts (
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(120) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'draft',
    excerpt TEXT NOT NULL,
    content TEXT NOT NULL,
    published_at DATETIME NULL,
    is_visible TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS gallery_albums (
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(120) NOT NULL,
    description TEXT NOT NULL,
    cover_image VARCHAR(255) NOT NULL DEFAULT 'img/uvod.JPG',
    item_count INT(11) NOT NULL DEFAULT 0,
    status VARCHAR(50) NOT NULL DEFAULT 'public',
    is_visible TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(120) NOT NULL,
    setting_value TEXT NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO users (name, username, email, password, role, is_active, created_at, updated_at)
SELECT 'Martin Kessner', 'admin', 'obec_radkovice@volny.cz', '$2y$10$28Q6GVQa4zTg3vWdrCcUy.YJg2WxVZsIh5LNB3lJOoUkkgYdx93vq', 'admin', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'admin');

INSERT INTO documents (title, category, status, file_type, file_size, summary, published_at, is_visible, created_at, updated_at)
SELECT * FROM (
    SELECT 'Zápis z 12. řádného zasedání zastupitelstva obce' AS title, 'Zastupitelstvo' AS category, 'published' AS status, 'PDF' AS file_type, '1.2 MB' AS file_size, 'Kompletní znění zápisu včetně schválených usnesení a výsledků hlasování.' AS summary, '2024-05-24 08:00:00' AS published_at, 1 AS is_visible, NOW() AS created_at, NOW() AS updated_at
    UNION ALL
    SELECT 'Návrh rozpočtového opatření č. 4/2024', 'Finance', 'review', 'PDF', '840 KB', 'Veřejná vyhláška o záměru úpravy rozpočtových kapitol pro investiční výstavbu.', '2024-05-20 10:00:00', 1, NOW(), NOW()
    UNION ALL
    SELECT 'Výběrové řízení: Modernizace veřejného osvětlení', 'Výběrové řízení', 'draft', 'ZIP', '4.5 MB', 'Zadávací dokumentace k podání nabídek na dodavatele úsporného osvětlení.', '2024-05-15 09:00:00', 1, NOW(), NOW()
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM documents);

INSERT INTO news_posts (title, category, status, excerpt, content, published_at, is_visible, created_at, updated_at)
SELECT * FROM (
    SELECT 'Pozvánka na setkání seniorů' AS title, 'Kultura' AS category, 'published' AS status, 'Srdečně zveme všechny naše spoluobčany na tradiční podzimní setkání.' AS excerpt, 'Srdečně zveme všechny naše spoluobčany na tradiční podzimní setkání v kulturním domě.' AS content, '2024-10-24 10:00:00' AS published_at, 1 AS is_visible, NOW() AS created_at, NOW() AS updated_at
    UNION ALL
    SELECT 'Nové termíny svozu odpadu', 'Obecní úřad', 'published', 'Od listopadu dochází ke změně v harmonogramu svozu komunálního odpadu.', 'Od listopadu dochází ke změně v harmonogramu svozu komunálního odpadu a tříděného sběru.', '2024-10-22 08:00:00', 1, NOW(), NOW()
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM news_posts);

INSERT INTO gallery_albums (title, category, description, cover_image, item_count, status, is_visible, created_at, updated_at)
SELECT * FROM (
    SELECT 'Obecní slavnosti 2024' AS title, 'Akce v obci' AS category, 'Fotografie z tradičního setkání občanů a kulturního programu.' AS description, 'img/uvod.JPG' AS cover_image, 42 AS item_count, 'public' AS status, 1 AS is_visible, NOW() AS created_at, NOW() AS updated_at
    UNION ALL
    SELECT 'Krásy okolí Budče', 'Příroda', 'Panoramatické snímky a přírodní zajímavosti z okolí obce.', 'img/uvod.JPG', 28, 'public', 1, NOW(), NOW()
    UNION ALL
    SELECT 'Rekonstrukce návsi', 'Architektura', 'Dokumentace postupných oprav veřejného prostranství.', 'img/uvod.JPG', 16, 'draft', 1, NOW(), NOW()
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM gallery_albums);

INSERT INTO site_settings (setting_key, setting_value, updated_at)
SELECT * FROM (
    SELECT 'site_name' AS setting_key, 'Radkovice u Budče' AS setting_value, NOW() AS updated_at
    UNION ALL
    SELECT 'contact_email', 'obec_radkovice@volny.cz', NOW()
    UNION ALL
    SELECT 'contact_phone', '770 132 011', NOW()
    UNION ALL
    SELECT 'databox_id', '9pfj2mc', NOW()
    UNION ALL
    SELECT 'office_address', 'Obecní úřad\nRadkovice u Budče 14\n380 01 Dačice', NOW()
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM site_settings);
