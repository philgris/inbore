-- prodedure qui ajoute la table user_db pour la gestion des utilisateurs et des roles
CREATE TABLE public.user_db (
    id bigint NOT NULL,
    user_name character varying(255) NOT NULL,
    user_password character varying(255) NOT NULL,
    user_email character varying(255) DEFAULT NULL::character varying,
    user_role character varying(255) NOT NULL,
    salt character varying(255) DEFAULT NULL::character varying,
    user_full_name character varying(255) NOT NULL,
    user_institution character varying(255) DEFAULT NULL::character varying,
    date_of_creation timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    date_of_update timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    creation_user_name bigint,
    update_user_name bigint,
    user_is_active smallint NOT NULL,
    user_comments text
);
ALTER TABLE public.user_db OWNER TO postgres;
CREATE SEQUENCE public.user_db_id_seq
START WITH 2
 INCREMENT BY 1
 NO MINVALUE
 NO MAXVALUE
 CACHE 1;
ALTER SEQUENCE public.user_db_id_seq OWNED BY public.user_db.id;
ALTER TABLE user_db ALTER COLUMN id SET DEFAULT nextval('public.user_db_id_seq'::regclass);
ALTER TABLE ONLY public.user_db ADD CONSTRAINT pk_user_db PRIMARY KEY (id);
ALTER TABLE ONLY public.user_db ADD CONSTRAINT uk_user_db__username UNIQUE (user_name);
-- db_user : Default admin account TO CHANGE : login = admin / password = adminInBORe
INSERT INTO public.user_db(id, user_name, user_password, user_email, user_role, salt, user_full_name, user_institution, date_of_creation, date_of_update, creation_user_name, update_user_name, user_is_active, user_comments)
VALUES (1, 'admin', 'GR2D7A7ZZ+kxkB7E+QsmTDAFYwmFsU73VCpBQyPAEneTXFvIocbfWuc2y1Ie6fyAmSyXlg3i0tV2CtwWEpjl8w==', 'admin@institution.fr','ROLE_ADMIN', null, 'admin name', 'institution', null, null, 1, 1 , 1, 'Default admin account TO CHANGE : login = admin / password = adminInBORe') ;

-- Add function user_full_name for postgres
CREATE OR REPLACE FUNCTION user_full_name(userid integer) RETURNS character varying
    LANGUAGE plpgsql
    AS $$
	DECLARE user_fullname VARCHAR;			
	BEGIN	
		SELECT user_db.user_full_name INTO user_fullname
		FROM user_db
		WHERE user_db.id = userid ;

		RETURN user_fullname ;
	END;
$$;