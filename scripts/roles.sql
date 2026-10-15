-- Roles de MySQL del proyecto (se aplican a cada una de las tres bases).
-- Los marcadores de nombre de base y de clave los reemplaza scripts/aplicar-roles.sh.
CREATE ROLE IF NOT EXISTS 'rol_admin', 'rol_escritura', 'rol_lectura';

GRANT ALL PRIVILEGES ON `__DB__`.* TO 'rol_admin';
GRANT SELECT, INSERT, UPDATE, DELETE ON `__DB__`.* TO 'rol_escritura';
GRANT SELECT ON `__DB__`.* TO 'rol_lectura';

CREATE USER IF NOT EXISTS 'usr_admin'@'%' IDENTIFIED BY '__CLAVE__';
CREATE USER IF NOT EXISTS 'usr_escritura'@'%' IDENTIFIED BY '__CLAVE__';
CREATE USER IF NOT EXISTS 'usr_lectura'@'%' IDENTIFIED BY '__CLAVE__';
ALTER USER 'usr_admin'@'%' IDENTIFIED BY '__CLAVE__';
ALTER USER 'usr_escritura'@'%' IDENTIFIED BY '__CLAVE__';
ALTER USER 'usr_lectura'@'%' IDENTIFIED BY '__CLAVE__';

GRANT 'rol_admin' TO 'usr_admin'@'%';
GRANT 'rol_escritura' TO 'usr_escritura'@'%';
GRANT 'rol_lectura' TO 'usr_lectura'@'%';
SET DEFAULT ROLE ALL TO 'usr_admin'@'%', 'usr_escritura'@'%', 'usr_lectura'@'%';
