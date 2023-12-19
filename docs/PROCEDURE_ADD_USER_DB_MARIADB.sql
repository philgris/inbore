CREATE TABLE user_db (
    id BIGINT NOT NULL AUTO_INCREMENT,
    user_name VARCHAR(255) NOT NULL,
    user_password VARCHAR(255) NOT NULL,
    user_email VARCHAR(255) DEFAULT NULL,
    user_role VARCHAR(255) NOT NULL,
    salt VARCHAR(255) DEFAULT NULL,
    user_full_name VARCHAR(255) NOT NULL,
    user_institution VARCHAR(255) DEFAULT NULL,
    date_of_creation TIMESTAMP DEFAULT current_timestamp() ,
    date_of_update TIMESTAMP DEFAULT current_timestamp() ,
    creation_user_name BIGINT,
    update_user_name BIGINT,
    user_is_active SMALLINT NOT NULL,
    user_comments TEXT,
    PRIMARY KEY (id)
);

ALTER TABLE user_db ADD UNIQUE (user_name);

INSERT INTO user_db(id, user_name, user_password, user_email, user_role, salt, user_full_name, user_institution, date_of_creation, date_of_update, creation_user_name, update_user_name, user_is_active, user_comments)
VALUES (1, 'admin', 'GR2D7A7ZZ+kxkB7E+QsmTDAFYwmFsU73VCpBQyPAEneTXFvIocbfWuc2y1Ie6fyAmSyXlg3i0tV2CtwWEpjl8w==', 'admin@institution.fr','ROLE_ADMIN', null, 'admin name', 'institution', null, null, 1, 1 , 1, 'Default admin account TO CHANGE : login = admin / password = adminInBORe');
