--
-- PostgreSQL database dump
--

-- Dumped from database version 13.7
-- Dumped by pg_dump version 13.7

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: notify_messenger_messages(); Type: FUNCTION; Schema: public; Owner: symfony
--

CREATE FUNCTION public.notify_messenger_messages() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    BEGIN
        PERFORM pg_notify('messenger_messages', NEW.queue_name::text);
        RETURN NEW;
    END;
$$;


ALTER FUNCTION public.notify_messenger_messages() OWNER TO symfony;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: addresses; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.addresses (
    id integer NOT NULL,
    instance_id integer,
    ip character varying(16) NOT NULL,
    mac character varying(18) NOT NULL
);


ALTER TABLE public.addresses OWNER TO symfony;

--
-- Name: addresses_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.addresses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.addresses_id_seq OWNER TO symfony;

--
-- Name: breeds; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.breeds (
    id integer NOT NULL,
    name character varying(255) NOT NULL
);


ALTER TABLE public.breeds OWNER TO symfony;

--
-- Name: breeds_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.breeds_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.breeds_id_seq OWNER TO symfony;

--
-- Name: doctrine_migration_versions; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.doctrine_migration_versions (
    version character varying(191) NOT NULL,
    executed_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    execution_time integer
);


ALTER TABLE public.doctrine_migration_versions OWNER TO symfony;

--
-- Name: domains; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.domains (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    description text
);


ALTER TABLE public.domains OWNER TO symfony;

--
-- Name: domains_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.domains_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.domains_id_seq OWNER TO symfony;

--
-- Name: environment_statuses; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.environment_statuses (
    id integer NOT NULL,
    status character varying(255) NOT NULL,
    description character varying(255)
);


ALTER TABLE public.environment_statuses OWNER TO symfony;

--
-- Name: environment_statuses_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.environment_statuses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.environment_statuses_id_seq OWNER TO symfony;

--
-- Name: environments; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.environments (
    id integer NOT NULL,
    task_id integer NOT NULL,
    session_id integer,
    instance_id integer,
    started_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    status_id integer DEFAULT 1 NOT NULL,
    hash character varying(8) NOT NULL,
    finished_at timestamp(0) without time zone,
    valid boolean,
    deployment integer,
    verification integer
);


ALTER TABLE public.environments OWNER TO symfony;

--
-- Name: COLUMN environments.started_at; Type: COMMENT; Schema: public; Owner: symfony
--

COMMENT ON COLUMN public.environments.started_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN environments.finished_at; Type: COMMENT; Schema: public; Owner: symfony
--

COMMENT ON COLUMN public.environments.finished_at IS '(DC2Type:datetime_immutable)';


--
-- Name: environments_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.environments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.environments_id_seq OWNER TO symfony;

--
-- Name: hardware_profiles; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.hardware_profiles (
    id integer NOT NULL,
    type boolean NOT NULL,
    description text,
    cost integer NOT NULL,
    name character varying(255) NOT NULL,
    supported boolean DEFAULT false NOT NULL
);


ALTER TABLE public.hardware_profiles OWNER TO symfony;

--
-- Name: hardware_profiles_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.hardware_profiles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.hardware_profiles_id_seq OWNER TO symfony;

--
-- Name: instance_statuses; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.instance_statuses (
    id integer NOT NULL,
    status character varying(255) NOT NULL,
    description character varying(255)
);


ALTER TABLE public.instance_statuses OWNER TO symfony;

--
-- Name: instance_statuses_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.instance_statuses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.instance_statuses_id_seq OWNER TO symfony;

--
-- Name: instance_types; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.instance_types (
    id integer NOT NULL,
    os_id integer NOT NULL,
    hw_profile_id integer NOT NULL
);


ALTER TABLE public.instance_types OWNER TO symfony;

--
-- Name: instance_types_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.instance_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.instance_types_id_seq OWNER TO symfony;

--
-- Name: instances; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.instances (
    id integer NOT NULL,
    instance_type_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    status_id integer DEFAULT 6 NOT NULL,
    name character varying(255) NOT NULL
);


ALTER TABLE public.instances OWNER TO symfony;

--
-- Name: COLUMN instances.created_at; Type: COMMENT; Schema: public; Owner: symfony
--

COMMENT ON COLUMN public.instances.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: instances_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.instances_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.instances_id_seq OWNER TO symfony;

--
-- Name: messenger_messages; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.messenger_messages (
    id bigint NOT NULL,
    body text NOT NULL,
    headers text NOT NULL,
    queue_name character varying(190) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    available_at timestamp(0) without time zone NOT NULL,
    delivered_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


ALTER TABLE public.messenger_messages OWNER TO symfony;

--
-- Name: messenger_messages_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.messenger_messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.messenger_messages_id_seq OWNER TO symfony;

--
-- Name: messenger_messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: symfony
--

ALTER SEQUENCE public.messenger_messages_id_seq OWNED BY public.messenger_messages.id;


--
-- Name: operating_systems; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.operating_systems (
    id integer NOT NULL,
    release character varying(255) NOT NULL,
    description text,
    supported boolean NOT NULL,
    breed_id integer NOT NULL,
    alias character varying(255) DEFAULT NULL::character varying
);


ALTER TABLE public.operating_systems OWNER TO symfony;

--
-- Name: operating_systems_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.operating_systems_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.operating_systems_id_seq OWNER TO symfony;

--
-- Name: ports; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.ports (
    id integer NOT NULL,
    number integer NOT NULL,
    address_id integer
);


ALTER TABLE public.ports OWNER TO symfony;

--
-- Name: ports_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.ports_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.ports_id_seq OWNER TO symfony;

--
-- Name: session_oses; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.session_oses (
    id integer NOT NULL,
    session_id integer NOT NULL,
    os_id integer NOT NULL
);


ALTER TABLE public.session_oses OWNER TO symfony;

--
-- Name: session_oses_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.session_oses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.session_oses_id_seq OWNER TO symfony;

--
-- Name: session_statuses; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.session_statuses (
    id integer NOT NULL,
    status character varying(255) NOT NULL
);


ALTER TABLE public.session_statuses OWNER TO symfony;

--
-- Name: session_statuses_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.session_statuses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.session_statuses_id_seq OWNER TO symfony;

--
-- Name: session_techs; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.session_techs (
    id integer NOT NULL,
    session_id integer NOT NULL,
    tech_id integer NOT NULL
);


ALTER TABLE public.session_techs OWNER TO symfony;

--
-- Name: session_techs_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.session_techs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.session_techs_id_seq OWNER TO symfony;

--
-- Name: sessions; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.sessions (
    id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    finished_at timestamp(0) without time zone,
    hash character varying(8) NOT NULL,
    testee_id integer NOT NULL,
    status_id integer DEFAULT 1 NOT NULL,
    started_at timestamp(0) without time zone
);


ALTER TABLE public.sessions OWNER TO symfony;

--
-- Name: COLUMN sessions.created_at; Type: COMMENT; Schema: public; Owner: symfony
--

COMMENT ON COLUMN public.sessions.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN sessions.finished_at; Type: COMMENT; Schema: public; Owner: symfony
--

COMMENT ON COLUMN public.sessions.finished_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN sessions.started_at; Type: COMMENT; Schema: public; Owner: symfony
--

COMMENT ON COLUMN public.sessions.started_at IS '(DC2Type:datetime_immutable)';


--
-- Name: sessions_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.sessions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sessions_id_seq OWNER TO symfony;

--
-- Name: task_instance_types; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.task_instance_types (
    id integer NOT NULL,
    task_id integer NOT NULL,
    instance_type_id integer NOT NULL
);


ALTER TABLE public.task_instance_types OWNER TO symfony;

--
-- Name: task_instance_types_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.task_instance_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.task_instance_types_id_seq OWNER TO symfony;

--
-- Name: task_oses; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.task_oses (
    id integer NOT NULL,
    task_id integer NOT NULL,
    os_id integer NOT NULL
);


ALTER TABLE public.task_oses OWNER TO symfony;

--
-- Name: task_oses_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.task_oses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.task_oses_id_seq OWNER TO symfony;

--
-- Name: task_techs; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.task_techs (
    id integer NOT NULL,
    task_id integer NOT NULL,
    tech_id integer NOT NULL
);


ALTER TABLE public.task_techs OWNER TO symfony;

--
-- Name: task_techs_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.task_techs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.task_techs_id_seq OWNER TO symfony;

--
-- Name: tasks; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.tasks (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    path character varying(255) NOT NULL,
    project integer,
    solve integer,
    deploy integer,
    verify integer
);


ALTER TABLE public.tasks OWNER TO symfony;

--
-- Name: tasks_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.tasks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.tasks_id_seq OWNER TO symfony;

--
-- Name: technologies; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.technologies (
    id integer NOT NULL,
    domain_id integer NOT NULL,
    name character varying(255) NOT NULL,
    description text
);


ALTER TABLE public.technologies OWNER TO symfony;

--
-- Name: technologies_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.technologies_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.technologies_id_seq OWNER TO symfony;

--
-- Name: testees; Type: TABLE; Schema: public; Owner: symfony
--

CREATE TABLE public.testees (
    id integer NOT NULL,
    email character varying(255) NOT NULL,
    oauth_token character varying(255) NOT NULL,
    registered_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.testees OWNER TO symfony;

--
-- Name: COLUMN testees.registered_at; Type: COMMENT; Schema: public; Owner: symfony
--

COMMENT ON COLUMN public.testees.registered_at IS '(DC2Type:datetime_immutable)';


--
-- Name: testees_id_seq; Type: SEQUENCE; Schema: public; Owner: symfony
--

CREATE SEQUENCE public.testees_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.testees_id_seq OWNER TO symfony;

--
-- Name: messenger_messages id; Type: DEFAULT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.messenger_messages ALTER COLUMN id SET DEFAULT nextval('public.messenger_messages_id_seq'::regclass);


--
-- Data for Name: addresses; Type: TABLE DATA; Schema: public; Owner: symfony
--

INSERT INTO public.addresses VALUES (3343, 162, '172.27.72.99', 'aa:bb:cc:dd:48:63');
INSERT INTO public.addresses VALUES (3285, 163, '172.27.72.41', 'aa:bb:cc:dd:48:29');
INSERT INTO public.addresses VALUES (3371, NULL, '172.27.72.127', 'aa:bb:cc:dd:48:7f');
INSERT INTO public.addresses VALUES (3372, NULL, '172.27.72.128', 'aa:bb:cc:dd:48:80');
INSERT INTO public.addresses VALUES (3373, NULL, '172.27.72.129', 'aa:bb:cc:dd:48:81');
INSERT INTO public.addresses VALUES (3374, NULL, '172.27.72.130', 'aa:bb:cc:dd:48:82');
INSERT INTO public.addresses VALUES (3375, NULL, '172.27.72.131', 'aa:bb:cc:dd:48:83');
INSERT INTO public.addresses VALUES (3376, NULL, '172.27.72.132', 'aa:bb:cc:dd:48:84');
INSERT INTO public.addresses VALUES (3377, NULL, '172.27.72.133', 'aa:bb:cc:dd:48:85');
INSERT INTO public.addresses VALUES (3378, NULL, '172.27.72.134', 'aa:bb:cc:dd:48:86');
INSERT INTO public.addresses VALUES (3281, 164, '172.27.72.37', 'aa:bb:cc:dd:48:25');
INSERT INTO public.addresses VALUES (3264, 165, '172.27.72.20', 'aa:bb:cc:dd:48:14');
INSERT INTO public.addresses VALUES (3273, 166, '172.27.72.29', 'aa:bb:cc:dd:48:1d');
INSERT INTO public.addresses VALUES (3260, 167, '172.27.72.16', 'aa:bb:cc:dd:48:10');
INSERT INTO public.addresses VALUES (3293, 168, '172.27.72.49', 'aa:bb:cc:dd:48:31');
INSERT INTO public.addresses VALUES (3286, 169, '172.27.72.42', 'aa:bb:cc:dd:48:2a');
INSERT INTO public.addresses VALUES (3263, 170, '172.27.72.19', 'aa:bb:cc:dd:48:13');
INSERT INTO public.addresses VALUES (3276, 171, '172.27.72.32', 'aa:bb:cc:dd:48:20');
INSERT INTO public.addresses VALUES (3288, 172, '172.27.72.44', 'aa:bb:cc:dd:48:2c');
INSERT INTO public.addresses VALUES (3270, 173, '172.27.72.26', 'aa:bb:cc:dd:48:1a');
INSERT INTO public.addresses VALUES (3275, 174, '172.27.72.31', 'aa:bb:cc:dd:48:1f');
INSERT INTO public.addresses VALUES (3259, 175, '172.27.72.15', 'aa:bb:cc:dd:48:0f');
INSERT INTO public.addresses VALUES (3284, 176, '172.27.72.40', 'aa:bb:cc:dd:48:28');
INSERT INTO public.addresses VALUES (3277, 177, '172.27.72.33', 'aa:bb:cc:dd:48:21');
INSERT INTO public.addresses VALUES (3289, 178, '172.27.72.45', 'aa:bb:cc:dd:48:2d');
INSERT INTO public.addresses VALUES (3287, 179, '172.27.72.43', 'aa:bb:cc:dd:48:2b');
INSERT INTO public.addresses VALUES (3267, 180, '172.27.72.23', 'aa:bb:cc:dd:48:17');
INSERT INTO public.addresses VALUES (3292, 181, '172.27.72.48', 'aa:bb:cc:dd:48:30');
INSERT INTO public.addresses VALUES (3282, 182, '172.27.72.38', 'aa:bb:cc:dd:48:26');
INSERT INTO public.addresses VALUES (3262, 183, '172.27.72.18', 'aa:bb:cc:dd:48:12');
INSERT INTO public.addresses VALUES (3261, 184, '172.27.72.17', 'aa:bb:cc:dd:48:11');
INSERT INTO public.addresses VALUES (3266, 185, '172.27.72.22', 'aa:bb:cc:dd:48:16');
INSERT INTO public.addresses VALUES (3278, 186, '172.27.72.34', 'aa:bb:cc:dd:48:22');
INSERT INTO public.addresses VALUES (3268, 187, '172.27.72.24', 'aa:bb:cc:dd:48:18');
INSERT INTO public.addresses VALUES (3283, 188, '172.27.72.39', 'aa:bb:cc:dd:48:27');
INSERT INTO public.addresses VALUES (3269, 189, '172.27.72.25', 'aa:bb:cc:dd:48:19');
INSERT INTO public.addresses VALUES (3271, 190, '172.27.72.27', 'aa:bb:cc:dd:48:1b');
INSERT INTO public.addresses VALUES (3296, 191, '172.27.72.52', 'aa:bb:cc:dd:48:34');
INSERT INTO public.addresses VALUES (3295, 192, '172.27.72.51', 'aa:bb:cc:dd:48:33');
INSERT INTO public.addresses VALUES (3294, 193, '172.27.72.50', 'aa:bb:cc:dd:48:32');
INSERT INTO public.addresses VALUES (3307, 194, '172.27.72.63', 'aa:bb:cc:dd:48:3f');
INSERT INTO public.addresses VALUES (3308, 195, '172.27.72.64', 'aa:bb:cc:dd:48:40');
INSERT INTO public.addresses VALUES (3309, 196, '172.27.72.65', 'aa:bb:cc:dd:48:41');
INSERT INTO public.addresses VALUES (3301, 197, '172.27.72.57', 'aa:bb:cc:dd:48:39');
INSERT INTO public.addresses VALUES (3297, 198, '172.27.72.53', 'aa:bb:cc:dd:48:35');
INSERT INTO public.addresses VALUES (3306, 199, '172.27.72.62', 'aa:bb:cc:dd:48:3e');
INSERT INTO public.addresses VALUES (3299, 200, '172.27.72.55', 'aa:bb:cc:dd:48:37');
INSERT INTO public.addresses VALUES (3304, 201, '172.27.72.60', 'aa:bb:cc:dd:48:3c');
INSERT INTO public.addresses VALUES (3302, 202, '172.27.72.58', 'aa:bb:cc:dd:48:3a');
INSERT INTO public.addresses VALUES (3303, 203, '172.27.72.59', 'aa:bb:cc:dd:48:3b');
INSERT INTO public.addresses VALUES (3305, 204, '172.27.72.61', 'aa:bb:cc:dd:48:3d');
INSERT INTO public.addresses VALUES (3298, 205, '172.27.72.54', 'aa:bb:cc:dd:48:36');
INSERT INTO public.addresses VALUES (3300, 206, '172.27.72.56', 'aa:bb:cc:dd:48:38');
INSERT INTO public.addresses VALUES (3310, 207, '172.27.72.66', 'aa:bb:cc:dd:48:42');
INSERT INTO public.addresses VALUES (3311, 208, '172.27.72.67', 'aa:bb:cc:dd:48:43');
INSERT INTO public.addresses VALUES (3314, 209, '172.27.72.70', 'aa:bb:cc:dd:48:46');
INSERT INTO public.addresses VALUES (3317, 210, '172.27.72.73', 'aa:bb:cc:dd:48:49');
INSERT INTO public.addresses VALUES (3312, 211, '172.27.72.68', 'aa:bb:cc:dd:48:44');
INSERT INTO public.addresses VALUES (3315, 212, '172.27.72.71', 'aa:bb:cc:dd:48:47');
INSERT INTO public.addresses VALUES (3316, 213, '172.27.72.72', 'aa:bb:cc:dd:48:48');
INSERT INTO public.addresses VALUES (3318, 214, '172.27.72.74', 'aa:bb:cc:dd:48:4a');
INSERT INTO public.addresses VALUES (3313, 215, '172.27.72.69', 'aa:bb:cc:dd:48:45');
INSERT INTO public.addresses VALUES (3319, 216, '172.27.72.75', 'aa:bb:cc:dd:48:4b');
INSERT INTO public.addresses VALUES (3320, 217, '172.27.72.76', 'aa:bb:cc:dd:48:4c');
INSERT INTO public.addresses VALUES (3324, 218, '172.27.72.80', 'aa:bb:cc:dd:48:50');
INSERT INTO public.addresses VALUES (3321, 219, '172.27.72.77', 'aa:bb:cc:dd:48:4d');
INSERT INTO public.addresses VALUES (3323, 220, '172.27.72.79', 'aa:bb:cc:dd:48:4f');
INSERT INTO public.addresses VALUES (3322, 221, '172.27.72.78', 'aa:bb:cc:dd:48:4e');
INSERT INTO public.addresses VALUES (3325, 222, '172.27.72.81', 'aa:bb:cc:dd:48:51');
INSERT INTO public.addresses VALUES (3326, 223, '172.27.72.82', 'aa:bb:cc:dd:48:52');
INSERT INTO public.addresses VALUES (3327, 224, '172.27.72.83', 'aa:bb:cc:dd:48:53');
INSERT INTO public.addresses VALUES (3329, 225, '172.27.72.85', 'aa:bb:cc:dd:48:55');
INSERT INTO public.addresses VALUES (3345, 226, '172.27.72.101', 'aa:bb:cc:dd:48:65');
INSERT INTO public.addresses VALUES (3351, 227, '172.27.72.107', 'aa:bb:cc:dd:48:6b');
INSERT INTO public.addresses VALUES (3357, 228, '172.27.72.113', 'aa:bb:cc:dd:48:71');
INSERT INTO public.addresses VALUES (3348, 229, '172.27.72.104', 'aa:bb:cc:dd:48:68');
INSERT INTO public.addresses VALUES (3359, 230, '172.27.72.115', 'aa:bb:cc:dd:48:73');
INSERT INTO public.addresses VALUES (3347, 231, '172.27.72.103', 'aa:bb:cc:dd:48:67');
INSERT INTO public.addresses VALUES (3349, 232, '172.27.72.105', 'aa:bb:cc:dd:48:69');
INSERT INTO public.addresses VALUES (3352, 233, '172.27.72.108', 'aa:bb:cc:dd:48:6c');
INSERT INTO public.addresses VALUES (3353, 234, '172.27.72.109', 'aa:bb:cc:dd:48:6d');
INSERT INTO public.addresses VALUES (3356, 235, '172.27.72.112', 'aa:bb:cc:dd:48:70');
INSERT INTO public.addresses VALUES (3355, 236, '172.27.72.111', 'aa:bb:cc:dd:48:6f');
INSERT INTO public.addresses VALUES (3354, 237, '172.27.72.110', 'aa:bb:cc:dd:48:6e');
INSERT INTO public.addresses VALUES (3358, 238, '172.27.72.114', 'aa:bb:cc:dd:48:72');
INSERT INTO public.addresses VALUES (3361, 239, '172.27.72.117', 'aa:bb:cc:dd:48:75');
INSERT INTO public.addresses VALUES (3344, 240, '172.27.72.100', 'aa:bb:cc:dd:48:64');
INSERT INTO public.addresses VALUES (3360, 241, '172.27.72.116', 'aa:bb:cc:dd:48:74');
INSERT INTO public.addresses VALUES (3330, 242, '172.27.72.86', 'aa:bb:cc:dd:48:56');
INSERT INTO public.addresses VALUES (3331, 243, '172.27.72.87', 'aa:bb:cc:dd:48:57');
INSERT INTO public.addresses VALUES (3332, 244, '172.27.72.88', 'aa:bb:cc:dd:48:58');
INSERT INTO public.addresses VALUES (3333, 245, '172.27.72.89', 'aa:bb:cc:dd:48:59');
INSERT INTO public.addresses VALUES (3334, 246, '172.27.72.90', 'aa:bb:cc:dd:48:5a');
INSERT INTO public.addresses VALUES (3335, 247, '172.27.72.91', 'aa:bb:cc:dd:48:5b');
INSERT INTO public.addresses VALUES (3336, 248, '172.27.72.92', 'aa:bb:cc:dd:48:5c');
INSERT INTO public.addresses VALUES (3337, 249, '172.27.72.93', 'aa:bb:cc:dd:48:5d');
INSERT INTO public.addresses VALUES (3338, 250, '172.27.72.94', 'aa:bb:cc:dd:48:5e');
INSERT INTO public.addresses VALUES (3339, 251, '172.27.72.95', 'aa:bb:cc:dd:48:5f');
INSERT INTO public.addresses VALUES (3340, 252, '172.27.72.96', 'aa:bb:cc:dd:48:60');
INSERT INTO public.addresses VALUES (3341, 253, '172.27.72.97', 'aa:bb:cc:dd:48:61');
INSERT INTO public.addresses VALUES (3342, 254, '172.27.72.98', 'aa:bb:cc:dd:48:62');
INSERT INTO public.addresses VALUES (3364, 255, '172.27.72.120', 'aa:bb:cc:dd:48:78');
INSERT INTO public.addresses VALUES (3365, 256, '172.27.72.121', 'aa:bb:cc:dd:48:79');
INSERT INTO public.addresses VALUES (3366, 257, '172.27.72.122', 'aa:bb:cc:dd:48:7a');
INSERT INTO public.addresses VALUES (3367, 258, '172.27.72.123', 'aa:bb:cc:dd:48:7b');
INSERT INTO public.addresses VALUES (3368, 259, '172.27.72.124', 'aa:bb:cc:dd:48:7c');
INSERT INTO public.addresses VALUES (3369, 260, '172.27.72.125', 'aa:bb:cc:dd:48:7d');
INSERT INTO public.addresses VALUES (3370, 261, '172.27.72.126', 'aa:bb:cc:dd:48:7e');
INSERT INTO public.addresses VALUES (3379, NULL, '172.27.72.135', 'aa:bb:cc:dd:48:87');
INSERT INTO public.addresses VALUES (3380, NULL, '172.27.72.136', 'aa:bb:cc:dd:48:88');
INSERT INTO public.addresses VALUES (3381, NULL, '172.27.72.137', 'aa:bb:cc:dd:48:89');
INSERT INTO public.addresses VALUES (3382, NULL, '172.27.72.138', 'aa:bb:cc:dd:48:8a');
INSERT INTO public.addresses VALUES (3383, NULL, '172.27.72.139', 'aa:bb:cc:dd:48:8b');
INSERT INTO public.addresses VALUES (3384, NULL, '172.27.72.140', 'aa:bb:cc:dd:48:8c');
INSERT INTO public.addresses VALUES (3385, NULL, '172.27.72.141', 'aa:bb:cc:dd:48:8d');
INSERT INTO public.addresses VALUES (3386, NULL, '172.27.72.142', 'aa:bb:cc:dd:48:8e');
INSERT INTO public.addresses VALUES (3387, NULL, '172.27.72.143', 'aa:bb:cc:dd:48:8f');
INSERT INTO public.addresses VALUES (3388, NULL, '172.27.72.144', 'aa:bb:cc:dd:48:90');
INSERT INTO public.addresses VALUES (3389, NULL, '172.27.72.145', 'aa:bb:cc:dd:48:91');
INSERT INTO public.addresses VALUES (3390, NULL, '172.27.72.146', 'aa:bb:cc:dd:48:92');
INSERT INTO public.addresses VALUES (3391, NULL, '172.27.72.147', 'aa:bb:cc:dd:48:93');
INSERT INTO public.addresses VALUES (3392, NULL, '172.27.72.148', 'aa:bb:cc:dd:48:94');
INSERT INTO public.addresses VALUES (3393, NULL, '172.27.72.149', 'aa:bb:cc:dd:48:95');
INSERT INTO public.addresses VALUES (3394, NULL, '172.27.72.150', 'aa:bb:cc:dd:48:96');
INSERT INTO public.addresses VALUES (3395, NULL, '172.27.72.151', 'aa:bb:cc:dd:48:97');
INSERT INTO public.addresses VALUES (3396, NULL, '172.27.72.152', 'aa:bb:cc:dd:48:98');
INSERT INTO public.addresses VALUES (3397, NULL, '172.27.72.153', 'aa:bb:cc:dd:48:99');
INSERT INTO public.addresses VALUES (3398, NULL, '172.27.72.154', 'aa:bb:cc:dd:48:9a');
INSERT INTO public.addresses VALUES (3399, NULL, '172.27.72.155', 'aa:bb:cc:dd:48:9b');
INSERT INTO public.addresses VALUES (3400, NULL, '172.27.72.156', 'aa:bb:cc:dd:48:9c');
INSERT INTO public.addresses VALUES (3401, NULL, '172.27.72.157', 'aa:bb:cc:dd:48:9d');
INSERT INTO public.addresses VALUES (3402, NULL, '172.27.72.158', 'aa:bb:cc:dd:48:9e');
INSERT INTO public.addresses VALUES (3403, NULL, '172.27.72.159', 'aa:bb:cc:dd:48:9f');
INSERT INTO public.addresses VALUES (3404, NULL, '172.27.72.160', 'aa:bb:cc:dd:48:a0');
INSERT INTO public.addresses VALUES (3405, NULL, '172.27.72.161', 'aa:bb:cc:dd:48:a1');
INSERT INTO public.addresses VALUES (3406, NULL, '172.27.72.162', 'aa:bb:cc:dd:48:a2');
INSERT INTO public.addresses VALUES (3407, NULL, '172.27.72.163', 'aa:bb:cc:dd:48:a3');
INSERT INTO public.addresses VALUES (3408, NULL, '172.27.72.164', 'aa:bb:cc:dd:48:a4');
INSERT INTO public.addresses VALUES (3409, NULL, '172.27.72.165', 'aa:bb:cc:dd:48:a5');
INSERT INTO public.addresses VALUES (3410, NULL, '172.27.72.166', 'aa:bb:cc:dd:48:a6');
INSERT INTO public.addresses VALUES (3411, NULL, '172.27.72.167', 'aa:bb:cc:dd:48:a7');
INSERT INTO public.addresses VALUES (3412, NULL, '172.27.72.168', 'aa:bb:cc:dd:48:a8');
INSERT INTO public.addresses VALUES (3413, NULL, '172.27.72.169', 'aa:bb:cc:dd:48:a9');
INSERT INTO public.addresses VALUES (3414, NULL, '172.27.72.170', 'aa:bb:cc:dd:48:aa');
INSERT INTO public.addresses VALUES (3415, NULL, '172.27.72.171', 'aa:bb:cc:dd:48:ab');
INSERT INTO public.addresses VALUES (3416, NULL, '172.27.72.172', 'aa:bb:cc:dd:48:ac');
INSERT INTO public.addresses VALUES (3417, NULL, '172.27.72.173', 'aa:bb:cc:dd:48:ad');
INSERT INTO public.addresses VALUES (3418, NULL, '172.27.72.174', 'aa:bb:cc:dd:48:ae');
INSERT INTO public.addresses VALUES (3419, NULL, '172.27.72.175', 'aa:bb:cc:dd:48:af');
INSERT INTO public.addresses VALUES (3420, NULL, '172.27.72.176', 'aa:bb:cc:dd:48:b0');
INSERT INTO public.addresses VALUES (3421, NULL, '172.27.72.177', 'aa:bb:cc:dd:48:b1');
INSERT INTO public.addresses VALUES (3422, NULL, '172.27.72.178', 'aa:bb:cc:dd:48:b2');
INSERT INTO public.addresses VALUES (3423, NULL, '172.27.72.179', 'aa:bb:cc:dd:48:b3');
INSERT INTO public.addresses VALUES (3424, NULL, '172.27.72.180', 'aa:bb:cc:dd:48:b4');
INSERT INTO public.addresses VALUES (3425, NULL, '172.27.72.181', 'aa:bb:cc:dd:48:b5');
INSERT INTO public.addresses VALUES (3426, NULL, '172.27.72.182', 'aa:bb:cc:dd:48:b6');
INSERT INTO public.addresses VALUES (3427, NULL, '172.27.72.183', 'aa:bb:cc:dd:48:b7');
INSERT INTO public.addresses VALUES (3428, NULL, '172.27.72.184', 'aa:bb:cc:dd:48:b8');
INSERT INTO public.addresses VALUES (3429, NULL, '172.27.72.185', 'aa:bb:cc:dd:48:b9');
INSERT INTO public.addresses VALUES (3430, NULL, '172.27.72.186', 'aa:bb:cc:dd:48:ba');
INSERT INTO public.addresses VALUES (3431, NULL, '172.27.72.187', 'aa:bb:cc:dd:48:bb');
INSERT INTO public.addresses VALUES (3432, NULL, '172.27.72.188', 'aa:bb:cc:dd:48:bc');
INSERT INTO public.addresses VALUES (3433, NULL, '172.27.72.189', 'aa:bb:cc:dd:48:bd');
INSERT INTO public.addresses VALUES (3434, NULL, '172.27.72.190', 'aa:bb:cc:dd:48:be');
INSERT INTO public.addresses VALUES (3435, NULL, '172.27.72.191', 'aa:bb:cc:dd:48:bf');
INSERT INTO public.addresses VALUES (3436, NULL, '172.27.72.192', 'aa:bb:cc:dd:48:c0');
INSERT INTO public.addresses VALUES (3437, NULL, '172.27.72.193', 'aa:bb:cc:dd:48:c1');
INSERT INTO public.addresses VALUES (3438, NULL, '172.27.72.194', 'aa:bb:cc:dd:48:c2');
INSERT INTO public.addresses VALUES (3439, NULL, '172.27.72.195', 'aa:bb:cc:dd:48:c3');
INSERT INTO public.addresses VALUES (3440, NULL, '172.27.72.196', 'aa:bb:cc:dd:48:c4');
INSERT INTO public.addresses VALUES (3441, NULL, '172.27.72.197', 'aa:bb:cc:dd:48:c5');
INSERT INTO public.addresses VALUES (3442, NULL, '172.27.72.198', 'aa:bb:cc:dd:48:c6');
INSERT INTO public.addresses VALUES (3443, NULL, '172.27.72.199', 'aa:bb:cc:dd:48:c7');
INSERT INTO public.addresses VALUES (3444, NULL, '172.27.72.200', 'aa:bb:cc:dd:48:c8');
INSERT INTO public.addresses VALUES (3445, NULL, '172.27.72.201', 'aa:bb:cc:dd:48:c9');
INSERT INTO public.addresses VALUES (3446, NULL, '172.27.72.202', 'aa:bb:cc:dd:48:ca');
INSERT INTO public.addresses VALUES (3447, NULL, '172.27.72.203', 'aa:bb:cc:dd:48:cb');
INSERT INTO public.addresses VALUES (3448, NULL, '172.27.72.204', 'aa:bb:cc:dd:48:cc');
INSERT INTO public.addresses VALUES (3449, NULL, '172.27.72.205', 'aa:bb:cc:dd:48:cd');
INSERT INTO public.addresses VALUES (3450, NULL, '172.27.72.206', 'aa:bb:cc:dd:48:ce');
INSERT INTO public.addresses VALUES (3451, NULL, '172.27.72.207', 'aa:bb:cc:dd:48:cf');
INSERT INTO public.addresses VALUES (3452, NULL, '172.27.72.208', 'aa:bb:cc:dd:48:d0');
INSERT INTO public.addresses VALUES (3453, NULL, '172.27.72.209', 'aa:bb:cc:dd:48:d1');
INSERT INTO public.addresses VALUES (3454, NULL, '172.27.72.210', 'aa:bb:cc:dd:48:d2');
INSERT INTO public.addresses VALUES (3455, NULL, '172.27.72.211', 'aa:bb:cc:dd:48:d3');
INSERT INTO public.addresses VALUES (3456, NULL, '172.27.72.212', 'aa:bb:cc:dd:48:d4');
INSERT INTO public.addresses VALUES (3457, NULL, '172.27.72.213', 'aa:bb:cc:dd:48:d5');
INSERT INTO public.addresses VALUES (3458, NULL, '172.27.72.214', 'aa:bb:cc:dd:48:d6');
INSERT INTO public.addresses VALUES (3459, NULL, '172.27.72.215', 'aa:bb:cc:dd:48:d7');
INSERT INTO public.addresses VALUES (3460, NULL, '172.27.72.216', 'aa:bb:cc:dd:48:d8');
INSERT INTO public.addresses VALUES (3461, NULL, '172.27.72.217', 'aa:bb:cc:dd:48:d9');
INSERT INTO public.addresses VALUES (3462, NULL, '172.27.72.218', 'aa:bb:cc:dd:48:da');
INSERT INTO public.addresses VALUES (3463, NULL, '172.27.72.219', 'aa:bb:cc:dd:48:db');
INSERT INTO public.addresses VALUES (3464, NULL, '172.27.72.220', 'aa:bb:cc:dd:48:dc');
INSERT INTO public.addresses VALUES (3465, NULL, '172.27.72.221', 'aa:bb:cc:dd:48:dd');
INSERT INTO public.addresses VALUES (3466, NULL, '172.27.72.222', 'aa:bb:cc:dd:48:de');
INSERT INTO public.addresses VALUES (3467, NULL, '172.27.72.223', 'aa:bb:cc:dd:48:df');
INSERT INTO public.addresses VALUES (3468, NULL, '172.27.72.224', 'aa:bb:cc:dd:48:e0');
INSERT INTO public.addresses VALUES (3469, NULL, '172.27.72.225', 'aa:bb:cc:dd:48:e1');
INSERT INTO public.addresses VALUES (3470, NULL, '172.27.72.226', 'aa:bb:cc:dd:48:e2');
INSERT INTO public.addresses VALUES (3471, NULL, '172.27.72.227', 'aa:bb:cc:dd:48:e3');
INSERT INTO public.addresses VALUES (3472, NULL, '172.27.72.228', 'aa:bb:cc:dd:48:e4');
INSERT INTO public.addresses VALUES (3473, NULL, '172.27.72.229', 'aa:bb:cc:dd:48:e5');
INSERT INTO public.addresses VALUES (3474, NULL, '172.27.72.230', 'aa:bb:cc:dd:48:e6');
INSERT INTO public.addresses VALUES (3475, NULL, '172.27.72.231', 'aa:bb:cc:dd:48:e7');
INSERT INTO public.addresses VALUES (3476, NULL, '172.27.72.232', 'aa:bb:cc:dd:48:e8');
INSERT INTO public.addresses VALUES (3477, NULL, '172.27.72.233', 'aa:bb:cc:dd:48:e9');
INSERT INTO public.addresses VALUES (3478, NULL, '172.27.72.234', 'aa:bb:cc:dd:48:ea');
INSERT INTO public.addresses VALUES (3479, NULL, '172.27.72.235', 'aa:bb:cc:dd:48:eb');
INSERT INTO public.addresses VALUES (3480, NULL, '172.27.72.236', 'aa:bb:cc:dd:48:ec');
INSERT INTO public.addresses VALUES (3481, NULL, '172.27.72.237', 'aa:bb:cc:dd:48:ed');
INSERT INTO public.addresses VALUES (3482, NULL, '172.27.72.238', 'aa:bb:cc:dd:48:ee');
INSERT INTO public.addresses VALUES (3483, NULL, '172.27.72.239', 'aa:bb:cc:dd:48:ef');
INSERT INTO public.addresses VALUES (3484, NULL, '172.27.72.240', 'aa:bb:cc:dd:48:f0');
INSERT INTO public.addresses VALUES (3485, NULL, '172.27.72.241', 'aa:bb:cc:dd:48:f1');
INSERT INTO public.addresses VALUES (3486, NULL, '172.27.72.242', 'aa:bb:cc:dd:48:f2');
INSERT INTO public.addresses VALUES (3487, NULL, '172.27.72.243', 'aa:bb:cc:dd:48:f3');
INSERT INTO public.addresses VALUES (3488, NULL, '172.27.72.244', 'aa:bb:cc:dd:48:f4');
INSERT INTO public.addresses VALUES (3489, NULL, '172.27.72.245', 'aa:bb:cc:dd:48:f5');
INSERT INTO public.addresses VALUES (3490, NULL, '172.27.72.246', 'aa:bb:cc:dd:48:f6');
INSERT INTO public.addresses VALUES (3491, NULL, '172.27.72.247', 'aa:bb:cc:dd:48:f7');
INSERT INTO public.addresses VALUES (3492, NULL, '172.27.72.248', 'aa:bb:cc:dd:48:f8');
INSERT INTO public.addresses VALUES (3493, NULL, '172.27.72.249', 'aa:bb:cc:dd:48:f9');
INSERT INTO public.addresses VALUES (3494, NULL, '172.27.72.250', 'aa:bb:cc:dd:48:fa');
INSERT INTO public.addresses VALUES (3495, NULL, '172.27.72.251', 'aa:bb:cc:dd:48:fb');
INSERT INTO public.addresses VALUES (3496, NULL, '172.27.72.252', 'aa:bb:cc:dd:48:fc');
INSERT INTO public.addresses VALUES (3497, NULL, '172.27.72.253', 'aa:bb:cc:dd:48:fd');
INSERT INTO public.addresses VALUES (3498, NULL, '172.27.72.254', 'aa:bb:cc:dd:48:fe');
INSERT INTO public.addresses VALUES (3499, NULL, '172.27.72.255', 'aa:bb:cc:dd:48:ff');
INSERT INTO public.addresses VALUES (3500, NULL, '172.27.160.0', 'aa:bb:cc:dd:a0:00');
INSERT INTO public.addresses VALUES (3501, NULL, '172.27.160.1', 'aa:bb:cc:dd:a0:01');
INSERT INTO public.addresses VALUES (3502, NULL, '172.27.160.2', 'aa:bb:cc:dd:a0:02');
INSERT INTO public.addresses VALUES (3503, NULL, '172.27.160.3', 'aa:bb:cc:dd:a0:03');
INSERT INTO public.addresses VALUES (3504, NULL, '172.27.160.4', 'aa:bb:cc:dd:a0:04');
INSERT INTO public.addresses VALUES (3505, NULL, '172.27.160.5', 'aa:bb:cc:dd:a0:05');
INSERT INTO public.addresses VALUES (3506, NULL, '172.27.160.6', 'aa:bb:cc:dd:a0:06');
INSERT INTO public.addresses VALUES (3507, NULL, '172.27.160.7', 'aa:bb:cc:dd:a0:07');
INSERT INTO public.addresses VALUES (3508, NULL, '172.27.160.8', 'aa:bb:cc:dd:a0:08');
INSERT INTO public.addresses VALUES (3509, NULL, '172.27.160.9', 'aa:bb:cc:dd:a0:09');
INSERT INTO public.addresses VALUES (3510, NULL, '172.27.160.10', 'aa:bb:cc:dd:a0:0a');
INSERT INTO public.addresses VALUES (3511, NULL, '172.27.160.11', 'aa:bb:cc:dd:a0:0b');
INSERT INTO public.addresses VALUES (3512, NULL, '172.27.160.12', 'aa:bb:cc:dd:a0:0c');
INSERT INTO public.addresses VALUES (3513, NULL, '172.27.160.13', 'aa:bb:cc:dd:a0:0d');
INSERT INTO public.addresses VALUES (3514, NULL, '172.27.160.14', 'aa:bb:cc:dd:a0:0e');
INSERT INTO public.addresses VALUES (3515, NULL, '172.27.160.15', 'aa:bb:cc:dd:a0:0f');
INSERT INTO public.addresses VALUES (3516, NULL, '172.27.160.16', 'aa:bb:cc:dd:a0:10');
INSERT INTO public.addresses VALUES (3517, NULL, '172.27.160.17', 'aa:bb:cc:dd:a0:11');
INSERT INTO public.addresses VALUES (3518, NULL, '172.27.160.18', 'aa:bb:cc:dd:a0:12');
INSERT INTO public.addresses VALUES (3519, NULL, '172.27.160.19', 'aa:bb:cc:dd:a0:13');
INSERT INTO public.addresses VALUES (3520, NULL, '172.27.160.20', 'aa:bb:cc:dd:a0:14');
INSERT INTO public.addresses VALUES (3521, NULL, '172.27.160.21', 'aa:bb:cc:dd:a0:15');
INSERT INTO public.addresses VALUES (3522, NULL, '172.27.160.22', 'aa:bb:cc:dd:a0:16');
INSERT INTO public.addresses VALUES (3523, NULL, '172.27.160.23', 'aa:bb:cc:dd:a0:17');
INSERT INTO public.addresses VALUES (3524, NULL, '172.27.160.24', 'aa:bb:cc:dd:a0:18');
INSERT INTO public.addresses VALUES (3525, NULL, '172.27.160.25', 'aa:bb:cc:dd:a0:19');
INSERT INTO public.addresses VALUES (3526, NULL, '172.27.160.26', 'aa:bb:cc:dd:a0:1a');
INSERT INTO public.addresses VALUES (3527, NULL, '172.27.160.27', 'aa:bb:cc:dd:a0:1b');
INSERT INTO public.addresses VALUES (3528, NULL, '172.27.160.28', 'aa:bb:cc:dd:a0:1c');
INSERT INTO public.addresses VALUES (3529, NULL, '172.27.160.29', 'aa:bb:cc:dd:a0:1d');
INSERT INTO public.addresses VALUES (3530, NULL, '172.27.160.30', 'aa:bb:cc:dd:a0:1e');
INSERT INTO public.addresses VALUES (3531, NULL, '172.27.160.31', 'aa:bb:cc:dd:a0:1f');
INSERT INTO public.addresses VALUES (3532, NULL, '172.27.160.32', 'aa:bb:cc:dd:a0:20');
INSERT INTO public.addresses VALUES (3533, NULL, '172.27.160.33', 'aa:bb:cc:dd:a0:21');
INSERT INTO public.addresses VALUES (3534, NULL, '172.27.160.34', 'aa:bb:cc:dd:a0:22');
INSERT INTO public.addresses VALUES (3535, NULL, '172.27.160.35', 'aa:bb:cc:dd:a0:23');
INSERT INTO public.addresses VALUES (3536, NULL, '172.27.160.36', 'aa:bb:cc:dd:a0:24');
INSERT INTO public.addresses VALUES (3537, NULL, '172.27.160.37', 'aa:bb:cc:dd:a0:25');
INSERT INTO public.addresses VALUES (3538, NULL, '172.27.160.38', 'aa:bb:cc:dd:a0:26');
INSERT INTO public.addresses VALUES (3539, NULL, '172.27.160.39', 'aa:bb:cc:dd:a0:27');
INSERT INTO public.addresses VALUES (3540, NULL, '172.27.160.40', 'aa:bb:cc:dd:a0:28');
INSERT INTO public.addresses VALUES (3541, NULL, '172.27.160.41', 'aa:bb:cc:dd:a0:29');
INSERT INTO public.addresses VALUES (3542, NULL, '172.27.160.42', 'aa:bb:cc:dd:a0:2a');
INSERT INTO public.addresses VALUES (3543, NULL, '172.27.160.43', 'aa:bb:cc:dd:a0:2b');
INSERT INTO public.addresses VALUES (3544, NULL, '172.27.160.44', 'aa:bb:cc:dd:a0:2c');
INSERT INTO public.addresses VALUES (3545, NULL, '172.27.160.45', 'aa:bb:cc:dd:a0:2d');
INSERT INTO public.addresses VALUES (3546, NULL, '172.27.160.46', 'aa:bb:cc:dd:a0:2e');
INSERT INTO public.addresses VALUES (3547, NULL, '172.27.160.47', 'aa:bb:cc:dd:a0:2f');
INSERT INTO public.addresses VALUES (3548, NULL, '172.27.160.48', 'aa:bb:cc:dd:a0:30');
INSERT INTO public.addresses VALUES (3549, NULL, '172.27.160.49', 'aa:bb:cc:dd:a0:31');
INSERT INTO public.addresses VALUES (3550, NULL, '172.27.160.50', 'aa:bb:cc:dd:a0:32');
INSERT INTO public.addresses VALUES (3551, NULL, '172.27.160.51', 'aa:bb:cc:dd:a0:33');
INSERT INTO public.addresses VALUES (3552, NULL, '172.27.160.52', 'aa:bb:cc:dd:a0:34');
INSERT INTO public.addresses VALUES (3553, NULL, '172.27.160.53', 'aa:bb:cc:dd:a0:35');
INSERT INTO public.addresses VALUES (3554, NULL, '172.27.160.54', 'aa:bb:cc:dd:a0:36');
INSERT INTO public.addresses VALUES (3555, NULL, '172.27.160.55', 'aa:bb:cc:dd:a0:37');
INSERT INTO public.addresses VALUES (3556, NULL, '172.27.160.56', 'aa:bb:cc:dd:a0:38');
INSERT INTO public.addresses VALUES (3557, NULL, '172.27.160.57', 'aa:bb:cc:dd:a0:39');
INSERT INTO public.addresses VALUES (3558, NULL, '172.27.160.58', 'aa:bb:cc:dd:a0:3a');
INSERT INTO public.addresses VALUES (3559, NULL, '172.27.160.59', 'aa:bb:cc:dd:a0:3b');
INSERT INTO public.addresses VALUES (3560, NULL, '172.27.160.60', 'aa:bb:cc:dd:a0:3c');
INSERT INTO public.addresses VALUES (3561, NULL, '172.27.160.61', 'aa:bb:cc:dd:a0:3d');
INSERT INTO public.addresses VALUES (3562, NULL, '172.27.160.62', 'aa:bb:cc:dd:a0:3e');
INSERT INTO public.addresses VALUES (3563, NULL, '172.27.160.63', 'aa:bb:cc:dd:a0:3f');
INSERT INTO public.addresses VALUES (3564, NULL, '172.27.160.64', 'aa:bb:cc:dd:a0:40');
INSERT INTO public.addresses VALUES (3565, NULL, '172.27.160.65', 'aa:bb:cc:dd:a0:41');
INSERT INTO public.addresses VALUES (3566, NULL, '172.27.160.66', 'aa:bb:cc:dd:a0:42');
INSERT INTO public.addresses VALUES (3567, NULL, '172.27.160.67', 'aa:bb:cc:dd:a0:43');
INSERT INTO public.addresses VALUES (3568, NULL, '172.27.160.68', 'aa:bb:cc:dd:a0:44');
INSERT INTO public.addresses VALUES (3569, NULL, '172.27.160.69', 'aa:bb:cc:dd:a0:45');
INSERT INTO public.addresses VALUES (3570, NULL, '172.27.160.70', 'aa:bb:cc:dd:a0:46');
INSERT INTO public.addresses VALUES (3571, NULL, '172.27.160.71', 'aa:bb:cc:dd:a0:47');
INSERT INTO public.addresses VALUES (3572, NULL, '172.27.160.72', 'aa:bb:cc:dd:a0:48');
INSERT INTO public.addresses VALUES (3573, NULL, '172.27.160.73', 'aa:bb:cc:dd:a0:49');
INSERT INTO public.addresses VALUES (3574, NULL, '172.27.160.74', 'aa:bb:cc:dd:a0:4a');
INSERT INTO public.addresses VALUES (3575, NULL, '172.27.160.75', 'aa:bb:cc:dd:a0:4b');
INSERT INTO public.addresses VALUES (3576, NULL, '172.27.160.76', 'aa:bb:cc:dd:a0:4c');
INSERT INTO public.addresses VALUES (3577, NULL, '172.27.160.77', 'aa:bb:cc:dd:a0:4d');
INSERT INTO public.addresses VALUES (3578, NULL, '172.27.160.78', 'aa:bb:cc:dd:a0:4e');
INSERT INTO public.addresses VALUES (3579, NULL, '172.27.160.79', 'aa:bb:cc:dd:a0:4f');
INSERT INTO public.addresses VALUES (3580, NULL, '172.27.160.80', 'aa:bb:cc:dd:a0:50');
INSERT INTO public.addresses VALUES (3581, NULL, '172.27.160.81', 'aa:bb:cc:dd:a0:51');
INSERT INTO public.addresses VALUES (3582, NULL, '172.27.160.82', 'aa:bb:cc:dd:a0:52');
INSERT INTO public.addresses VALUES (3583, NULL, '172.27.160.83', 'aa:bb:cc:dd:a0:53');
INSERT INTO public.addresses VALUES (3584, NULL, '172.27.160.84', 'aa:bb:cc:dd:a0:54');
INSERT INTO public.addresses VALUES (3585, NULL, '172.27.160.85', 'aa:bb:cc:dd:a0:55');
INSERT INTO public.addresses VALUES (3586, NULL, '172.27.160.86', 'aa:bb:cc:dd:a0:56');
INSERT INTO public.addresses VALUES (3587, NULL, '172.27.160.87', 'aa:bb:cc:dd:a0:57');
INSERT INTO public.addresses VALUES (3588, NULL, '172.27.160.88', 'aa:bb:cc:dd:a0:58');
INSERT INTO public.addresses VALUES (3589, NULL, '172.27.160.89', 'aa:bb:cc:dd:a0:59');
INSERT INTO public.addresses VALUES (3590, NULL, '172.27.160.90', 'aa:bb:cc:dd:a0:5a');
INSERT INTO public.addresses VALUES (3591, NULL, '172.27.160.91', 'aa:bb:cc:dd:a0:5b');
INSERT INTO public.addresses VALUES (3592, NULL, '172.27.160.92', 'aa:bb:cc:dd:a0:5c');
INSERT INTO public.addresses VALUES (3593, NULL, '172.27.160.93', 'aa:bb:cc:dd:a0:5d');
INSERT INTO public.addresses VALUES (3594, NULL, '172.27.160.94', 'aa:bb:cc:dd:a0:5e');
INSERT INTO public.addresses VALUES (3595, NULL, '172.27.160.95', 'aa:bb:cc:dd:a0:5f');
INSERT INTO public.addresses VALUES (3596, NULL, '172.27.160.96', 'aa:bb:cc:dd:a0:60');
INSERT INTO public.addresses VALUES (3597, NULL, '172.27.160.97', 'aa:bb:cc:dd:a0:61');
INSERT INTO public.addresses VALUES (3598, NULL, '172.27.160.98', 'aa:bb:cc:dd:a0:62');
INSERT INTO public.addresses VALUES (3599, NULL, '172.27.160.99', 'aa:bb:cc:dd:a0:63');
INSERT INTO public.addresses VALUES (3600, NULL, '172.27.160.100', 'aa:bb:cc:dd:a0:64');
INSERT INTO public.addresses VALUES (3601, NULL, '172.27.160.101', 'aa:bb:cc:dd:a0:65');
INSERT INTO public.addresses VALUES (3602, NULL, '172.27.160.102', 'aa:bb:cc:dd:a0:66');
INSERT INTO public.addresses VALUES (3603, NULL, '172.27.160.103', 'aa:bb:cc:dd:a0:67');
INSERT INTO public.addresses VALUES (3604, NULL, '172.27.160.104', 'aa:bb:cc:dd:a0:68');
INSERT INTO public.addresses VALUES (3605, NULL, '172.27.160.105', 'aa:bb:cc:dd:a0:69');
INSERT INTO public.addresses VALUES (3606, NULL, '172.27.160.106', 'aa:bb:cc:dd:a0:6a');
INSERT INTO public.addresses VALUES (3607, NULL, '172.27.160.107', 'aa:bb:cc:dd:a0:6b');
INSERT INTO public.addresses VALUES (3608, NULL, '172.27.160.108', 'aa:bb:cc:dd:a0:6c');
INSERT INTO public.addresses VALUES (3609, NULL, '172.27.160.109', 'aa:bb:cc:dd:a0:6d');
INSERT INTO public.addresses VALUES (3610, NULL, '172.27.160.110', 'aa:bb:cc:dd:a0:6e');
INSERT INTO public.addresses VALUES (3611, NULL, '172.27.160.111', 'aa:bb:cc:dd:a0:6f');
INSERT INTO public.addresses VALUES (3612, NULL, '172.27.160.112', 'aa:bb:cc:dd:a0:70');
INSERT INTO public.addresses VALUES (3613, NULL, '172.27.160.113', 'aa:bb:cc:dd:a0:71');
INSERT INTO public.addresses VALUES (3614, NULL, '172.27.160.114', 'aa:bb:cc:dd:a0:72');
INSERT INTO public.addresses VALUES (3615, NULL, '172.27.160.115', 'aa:bb:cc:dd:a0:73');
INSERT INTO public.addresses VALUES (3616, NULL, '172.27.160.116', 'aa:bb:cc:dd:a0:74');
INSERT INTO public.addresses VALUES (3617, NULL, '172.27.160.117', 'aa:bb:cc:dd:a0:75');
INSERT INTO public.addresses VALUES (3618, NULL, '172.27.160.118', 'aa:bb:cc:dd:a0:76');
INSERT INTO public.addresses VALUES (3619, NULL, '172.27.160.119', 'aa:bb:cc:dd:a0:77');
INSERT INTO public.addresses VALUES (3620, NULL, '172.27.160.120', 'aa:bb:cc:dd:a0:78');
INSERT INTO public.addresses VALUES (3621, NULL, '172.27.160.121', 'aa:bb:cc:dd:a0:79');
INSERT INTO public.addresses VALUES (3622, NULL, '172.27.160.122', 'aa:bb:cc:dd:a0:7a');
INSERT INTO public.addresses VALUES (3623, NULL, '172.27.160.123', 'aa:bb:cc:dd:a0:7b');
INSERT INTO public.addresses VALUES (3624, NULL, '172.27.160.124', 'aa:bb:cc:dd:a0:7c');
INSERT INTO public.addresses VALUES (3625, NULL, '172.27.160.125', 'aa:bb:cc:dd:a0:7d');
INSERT INTO public.addresses VALUES (3626, NULL, '172.27.160.126', 'aa:bb:cc:dd:a0:7e');
INSERT INTO public.addresses VALUES (3627, NULL, '172.27.160.127', 'aa:bb:cc:dd:a0:7f');
INSERT INTO public.addresses VALUES (3628, NULL, '172.27.160.128', 'aa:bb:cc:dd:a0:80');
INSERT INTO public.addresses VALUES (3629, NULL, '172.27.160.129', 'aa:bb:cc:dd:a0:81');
INSERT INTO public.addresses VALUES (3630, NULL, '172.27.160.130', 'aa:bb:cc:dd:a0:82');
INSERT INTO public.addresses VALUES (3631, NULL, '172.27.160.131', 'aa:bb:cc:dd:a0:83');
INSERT INTO public.addresses VALUES (3632, NULL, '172.27.160.132', 'aa:bb:cc:dd:a0:84');
INSERT INTO public.addresses VALUES (3633, NULL, '172.27.160.133', 'aa:bb:cc:dd:a0:85');
INSERT INTO public.addresses VALUES (3634, NULL, '172.27.160.134', 'aa:bb:cc:dd:a0:86');
INSERT INTO public.addresses VALUES (3635, NULL, '172.27.160.135', 'aa:bb:cc:dd:a0:87');
INSERT INTO public.addresses VALUES (3636, NULL, '172.27.160.136', 'aa:bb:cc:dd:a0:88');
INSERT INTO public.addresses VALUES (3637, NULL, '172.27.160.137', 'aa:bb:cc:dd:a0:89');
INSERT INTO public.addresses VALUES (3638, NULL, '172.27.160.138', 'aa:bb:cc:dd:a0:8a');
INSERT INTO public.addresses VALUES (3639, NULL, '172.27.160.139', 'aa:bb:cc:dd:a0:8b');
INSERT INTO public.addresses VALUES (3640, NULL, '172.27.160.140', 'aa:bb:cc:dd:a0:8c');
INSERT INTO public.addresses VALUES (3641, NULL, '172.27.160.141', 'aa:bb:cc:dd:a0:8d');
INSERT INTO public.addresses VALUES (3642, NULL, '172.27.160.142', 'aa:bb:cc:dd:a0:8e');
INSERT INTO public.addresses VALUES (3643, NULL, '172.27.160.143', 'aa:bb:cc:dd:a0:8f');
INSERT INTO public.addresses VALUES (3644, NULL, '172.27.160.144', 'aa:bb:cc:dd:a0:90');
INSERT INTO public.addresses VALUES (3645, NULL, '172.27.160.145', 'aa:bb:cc:dd:a0:91');
INSERT INTO public.addresses VALUES (3646, NULL, '172.27.160.146', 'aa:bb:cc:dd:a0:92');
INSERT INTO public.addresses VALUES (3647, NULL, '172.27.160.147', 'aa:bb:cc:dd:a0:93');
INSERT INTO public.addresses VALUES (3648, NULL, '172.27.160.148', 'aa:bb:cc:dd:a0:94');
INSERT INTO public.addresses VALUES (3649, NULL, '172.27.160.149', 'aa:bb:cc:dd:a0:95');
INSERT INTO public.addresses VALUES (3650, NULL, '172.27.160.150', 'aa:bb:cc:dd:a0:96');
INSERT INTO public.addresses VALUES (3651, NULL, '172.27.160.151', 'aa:bb:cc:dd:a0:97');
INSERT INTO public.addresses VALUES (3652, NULL, '172.27.160.152', 'aa:bb:cc:dd:a0:98');
INSERT INTO public.addresses VALUES (3653, NULL, '172.27.160.153', 'aa:bb:cc:dd:a0:99');
INSERT INTO public.addresses VALUES (3654, NULL, '172.27.160.154', 'aa:bb:cc:dd:a0:9a');
INSERT INTO public.addresses VALUES (3655, NULL, '172.27.160.155', 'aa:bb:cc:dd:a0:9b');
INSERT INTO public.addresses VALUES (3656, NULL, '172.27.160.156', 'aa:bb:cc:dd:a0:9c');
INSERT INTO public.addresses VALUES (3657, NULL, '172.27.160.157', 'aa:bb:cc:dd:a0:9d');
INSERT INTO public.addresses VALUES (3658, NULL, '172.27.160.158', 'aa:bb:cc:dd:a0:9e');
INSERT INTO public.addresses VALUES (3659, NULL, '172.27.160.159', 'aa:bb:cc:dd:a0:9f');
INSERT INTO public.addresses VALUES (3660, NULL, '172.27.160.160', 'aa:bb:cc:dd:a0:a0');
INSERT INTO public.addresses VALUES (3661, NULL, '172.27.160.161', 'aa:bb:cc:dd:a0:a1');
INSERT INTO public.addresses VALUES (3662, NULL, '172.27.160.162', 'aa:bb:cc:dd:a0:a2');
INSERT INTO public.addresses VALUES (3663, NULL, '172.27.160.163', 'aa:bb:cc:dd:a0:a3');
INSERT INTO public.addresses VALUES (3664, NULL, '172.27.160.164', 'aa:bb:cc:dd:a0:a4');
INSERT INTO public.addresses VALUES (3665, NULL, '172.27.160.165', 'aa:bb:cc:dd:a0:a5');
INSERT INTO public.addresses VALUES (3666, NULL, '172.27.160.166', 'aa:bb:cc:dd:a0:a6');
INSERT INTO public.addresses VALUES (3667, NULL, '172.27.160.167', 'aa:bb:cc:dd:a0:a7');
INSERT INTO public.addresses VALUES (3668, NULL, '172.27.160.168', 'aa:bb:cc:dd:a0:a8');
INSERT INTO public.addresses VALUES (3669, NULL, '172.27.160.169', 'aa:bb:cc:dd:a0:a9');
INSERT INTO public.addresses VALUES (3670, NULL, '172.27.160.170', 'aa:bb:cc:dd:a0:aa');
INSERT INTO public.addresses VALUES (3671, NULL, '172.27.160.171', 'aa:bb:cc:dd:a0:ab');
INSERT INTO public.addresses VALUES (3672, NULL, '172.27.160.172', 'aa:bb:cc:dd:a0:ac');
INSERT INTO public.addresses VALUES (3673, NULL, '172.27.160.173', 'aa:bb:cc:dd:a0:ad');
INSERT INTO public.addresses VALUES (3674, NULL, '172.27.160.174', 'aa:bb:cc:dd:a0:ae');
INSERT INTO public.addresses VALUES (3675, NULL, '172.27.160.175', 'aa:bb:cc:dd:a0:af');
INSERT INTO public.addresses VALUES (3676, NULL, '172.27.160.176', 'aa:bb:cc:dd:a0:b0');
INSERT INTO public.addresses VALUES (3677, NULL, '172.27.160.177', 'aa:bb:cc:dd:a0:b1');
INSERT INTO public.addresses VALUES (3678, NULL, '172.27.160.178', 'aa:bb:cc:dd:a0:b2');
INSERT INTO public.addresses VALUES (3679, NULL, '172.27.160.179', 'aa:bb:cc:dd:a0:b3');
INSERT INTO public.addresses VALUES (3680, NULL, '172.27.160.180', 'aa:bb:cc:dd:a0:b4');
INSERT INTO public.addresses VALUES (3681, NULL, '172.27.160.181', 'aa:bb:cc:dd:a0:b5');
INSERT INTO public.addresses VALUES (3682, NULL, '172.27.160.182', 'aa:bb:cc:dd:a0:b6');
INSERT INTO public.addresses VALUES (3683, NULL, '172.27.160.183', 'aa:bb:cc:dd:a0:b7');
INSERT INTO public.addresses VALUES (3684, NULL, '172.27.160.184', 'aa:bb:cc:dd:a0:b8');
INSERT INTO public.addresses VALUES (3685, NULL, '172.27.160.185', 'aa:bb:cc:dd:a0:b9');
INSERT INTO public.addresses VALUES (3686, NULL, '172.27.160.186', 'aa:bb:cc:dd:a0:ba');
INSERT INTO public.addresses VALUES (3687, NULL, '172.27.160.187', 'aa:bb:cc:dd:a0:bb');
INSERT INTO public.addresses VALUES (3688, NULL, '172.27.160.188', 'aa:bb:cc:dd:a0:bc');
INSERT INTO public.addresses VALUES (3689, NULL, '172.27.160.189', 'aa:bb:cc:dd:a0:bd');
INSERT INTO public.addresses VALUES (3690, NULL, '172.27.160.190', 'aa:bb:cc:dd:a0:be');
INSERT INTO public.addresses VALUES (3691, NULL, '172.27.160.191', 'aa:bb:cc:dd:a0:bf');
INSERT INTO public.addresses VALUES (3692, NULL, '172.27.160.192', 'aa:bb:cc:dd:a0:c0');
INSERT INTO public.addresses VALUES (3693, NULL, '172.27.160.193', 'aa:bb:cc:dd:a0:c1');
INSERT INTO public.addresses VALUES (3694, NULL, '172.27.160.194', 'aa:bb:cc:dd:a0:c2');
INSERT INTO public.addresses VALUES (3695, NULL, '172.27.160.195', 'aa:bb:cc:dd:a0:c3');
INSERT INTO public.addresses VALUES (3696, NULL, '172.27.160.196', 'aa:bb:cc:dd:a0:c4');
INSERT INTO public.addresses VALUES (3697, NULL, '172.27.160.197', 'aa:bb:cc:dd:a0:c5');
INSERT INTO public.addresses VALUES (3698, NULL, '172.27.160.198', 'aa:bb:cc:dd:a0:c6');
INSERT INTO public.addresses VALUES (3699, NULL, '172.27.160.199', 'aa:bb:cc:dd:a0:c7');
INSERT INTO public.addresses VALUES (3700, NULL, '172.27.160.200', 'aa:bb:cc:dd:a0:c8');
INSERT INTO public.addresses VALUES (3701, NULL, '172.27.160.201', 'aa:bb:cc:dd:a0:c9');
INSERT INTO public.addresses VALUES (3702, NULL, '172.27.160.202', 'aa:bb:cc:dd:a0:ca');
INSERT INTO public.addresses VALUES (3703, NULL, '172.27.160.203', 'aa:bb:cc:dd:a0:cb');
INSERT INTO public.addresses VALUES (3704, NULL, '172.27.160.204', 'aa:bb:cc:dd:a0:cc');
INSERT INTO public.addresses VALUES (3705, NULL, '172.27.160.205', 'aa:bb:cc:dd:a0:cd');
INSERT INTO public.addresses VALUES (3706, NULL, '172.27.160.206', 'aa:bb:cc:dd:a0:ce');
INSERT INTO public.addresses VALUES (3707, NULL, '172.27.160.207', 'aa:bb:cc:dd:a0:cf');
INSERT INTO public.addresses VALUES (3708, NULL, '172.27.160.208', 'aa:bb:cc:dd:a0:d0');
INSERT INTO public.addresses VALUES (3709, NULL, '172.27.160.209', 'aa:bb:cc:dd:a0:d1');
INSERT INTO public.addresses VALUES (3710, NULL, '172.27.160.210', 'aa:bb:cc:dd:a0:d2');
INSERT INTO public.addresses VALUES (3711, NULL, '172.27.160.211', 'aa:bb:cc:dd:a0:d3');
INSERT INTO public.addresses VALUES (3712, NULL, '172.27.160.212', 'aa:bb:cc:dd:a0:d4');
INSERT INTO public.addresses VALUES (3713, NULL, '172.27.160.213', 'aa:bb:cc:dd:a0:d5');
INSERT INTO public.addresses VALUES (3714, NULL, '172.27.160.214', 'aa:bb:cc:dd:a0:d6');
INSERT INTO public.addresses VALUES (3715, NULL, '172.27.160.215', 'aa:bb:cc:dd:a0:d7');
INSERT INTO public.addresses VALUES (3716, NULL, '172.27.160.216', 'aa:bb:cc:dd:a0:d8');
INSERT INTO public.addresses VALUES (3717, NULL, '172.27.160.217', 'aa:bb:cc:dd:a0:d9');
INSERT INTO public.addresses VALUES (3718, NULL, '172.27.160.218', 'aa:bb:cc:dd:a0:da');
INSERT INTO public.addresses VALUES (3719, NULL, '172.27.160.219', 'aa:bb:cc:dd:a0:db');
INSERT INTO public.addresses VALUES (3720, NULL, '172.27.160.220', 'aa:bb:cc:dd:a0:dc');
INSERT INTO public.addresses VALUES (3721, NULL, '172.27.160.221', 'aa:bb:cc:dd:a0:dd');
INSERT INTO public.addresses VALUES (3722, NULL, '172.27.160.222', 'aa:bb:cc:dd:a0:de');
INSERT INTO public.addresses VALUES (3723, NULL, '172.27.160.223', 'aa:bb:cc:dd:a0:df');
INSERT INTO public.addresses VALUES (3724, NULL, '172.27.160.224', 'aa:bb:cc:dd:a0:e0');
INSERT INTO public.addresses VALUES (3725, NULL, '172.27.160.225', 'aa:bb:cc:dd:a0:e1');
INSERT INTO public.addresses VALUES (3726, NULL, '172.27.160.226', 'aa:bb:cc:dd:a0:e2');
INSERT INTO public.addresses VALUES (3727, NULL, '172.27.160.227', 'aa:bb:cc:dd:a0:e3');
INSERT INTO public.addresses VALUES (3728, NULL, '172.27.160.228', 'aa:bb:cc:dd:a0:e4');
INSERT INTO public.addresses VALUES (3729, NULL, '172.27.160.229', 'aa:bb:cc:dd:a0:e5');
INSERT INTO public.addresses VALUES (3730, NULL, '172.27.160.230', 'aa:bb:cc:dd:a0:e6');
INSERT INTO public.addresses VALUES (3731, NULL, '172.27.160.231', 'aa:bb:cc:dd:a0:e7');
INSERT INTO public.addresses VALUES (3732, NULL, '172.27.160.232', 'aa:bb:cc:dd:a0:e8');
INSERT INTO public.addresses VALUES (3733, NULL, '172.27.160.233', 'aa:bb:cc:dd:a0:e9');
INSERT INTO public.addresses VALUES (3734, NULL, '172.27.160.234', 'aa:bb:cc:dd:a0:ea');
INSERT INTO public.addresses VALUES (3735, NULL, '172.27.160.235', 'aa:bb:cc:dd:a0:eb');
INSERT INTO public.addresses VALUES (3736, NULL, '172.27.160.236', 'aa:bb:cc:dd:a0:ec');
INSERT INTO public.addresses VALUES (3737, NULL, '172.27.160.237', 'aa:bb:cc:dd:a0:ed');
INSERT INTO public.addresses VALUES (3738, NULL, '172.27.160.238', 'aa:bb:cc:dd:a0:ee');
INSERT INTO public.addresses VALUES (3739, NULL, '172.27.160.239', 'aa:bb:cc:dd:a0:ef');
INSERT INTO public.addresses VALUES (3740, NULL, '172.27.160.240', 'aa:bb:cc:dd:a0:f0');
INSERT INTO public.addresses VALUES (3741, NULL, '172.27.160.241', 'aa:bb:cc:dd:a0:f1');
INSERT INTO public.addresses VALUES (3742, NULL, '172.27.160.242', 'aa:bb:cc:dd:a0:f2');
INSERT INTO public.addresses VALUES (3743, NULL, '172.27.160.243', 'aa:bb:cc:dd:a0:f3');
INSERT INTO public.addresses VALUES (3744, NULL, '172.27.160.244', 'aa:bb:cc:dd:a0:f4');
INSERT INTO public.addresses VALUES (3745, NULL, '172.27.160.245', 'aa:bb:cc:dd:a0:f5');
INSERT INTO public.addresses VALUES (3746, NULL, '172.27.160.246', 'aa:bb:cc:dd:a0:f6');
INSERT INTO public.addresses VALUES (3747, NULL, '172.27.160.247', 'aa:bb:cc:dd:a0:f7');
INSERT INTO public.addresses VALUES (3748, NULL, '172.27.160.248', 'aa:bb:cc:dd:a0:f8');
INSERT INTO public.addresses VALUES (3749, NULL, '172.27.160.249', 'aa:bb:cc:dd:a0:f9');
INSERT INTO public.addresses VALUES (3750, NULL, '172.27.160.250', 'aa:bb:cc:dd:a0:fa');
INSERT INTO public.addresses VALUES (3751, NULL, '172.27.160.251', 'aa:bb:cc:dd:a0:fb');
INSERT INTO public.addresses VALUES (3752, NULL, '172.27.160.252', 'aa:bb:cc:dd:a0:fc');
INSERT INTO public.addresses VALUES (3753, NULL, '172.27.160.253', 'aa:bb:cc:dd:a0:fd');
INSERT INTO public.addresses VALUES (3754, NULL, '172.27.160.254', 'aa:bb:cc:dd:a0:fe');
INSERT INTO public.addresses VALUES (3755, NULL, '172.27.160.255', 'aa:bb:cc:dd:a0:ff');
INSERT INTO public.addresses VALUES (3756, NULL, '172.27.161.0', 'aa:bb:cc:dd:a1:00');
INSERT INTO public.addresses VALUES (3757, NULL, '172.27.161.1', 'aa:bb:cc:dd:a1:01');
INSERT INTO public.addresses VALUES (3758, NULL, '172.27.161.2', 'aa:bb:cc:dd:a1:02');
INSERT INTO public.addresses VALUES (3759, NULL, '172.27.161.3', 'aa:bb:cc:dd:a1:03');
INSERT INTO public.addresses VALUES (3760, NULL, '172.27.161.4', 'aa:bb:cc:dd:a1:04');
INSERT INTO public.addresses VALUES (3761, NULL, '172.27.161.5', 'aa:bb:cc:dd:a1:05');
INSERT INTO public.addresses VALUES (3762, NULL, '172.27.161.6', 'aa:bb:cc:dd:a1:06');
INSERT INTO public.addresses VALUES (3763, NULL, '172.27.161.7', 'aa:bb:cc:dd:a1:07');
INSERT INTO public.addresses VALUES (3764, NULL, '172.27.161.8', 'aa:bb:cc:dd:a1:08');
INSERT INTO public.addresses VALUES (3765, NULL, '172.27.161.9', 'aa:bb:cc:dd:a1:09');
INSERT INTO public.addresses VALUES (3766, NULL, '172.27.161.10', 'aa:bb:cc:dd:a1:0a');
INSERT INTO public.addresses VALUES (3767, NULL, '172.27.161.11', 'aa:bb:cc:dd:a1:0b');
INSERT INTO public.addresses VALUES (3768, NULL, '172.27.161.12', 'aa:bb:cc:dd:a1:0c');
INSERT INTO public.addresses VALUES (3769, NULL, '172.27.161.13', 'aa:bb:cc:dd:a1:0d');
INSERT INTO public.addresses VALUES (3770, NULL, '172.27.161.14', 'aa:bb:cc:dd:a1:0e');
INSERT INTO public.addresses VALUES (3771, NULL, '172.27.161.15', 'aa:bb:cc:dd:a1:0f');
INSERT INTO public.addresses VALUES (3772, NULL, '172.27.161.16', 'aa:bb:cc:dd:a1:10');
INSERT INTO public.addresses VALUES (3773, NULL, '172.27.161.17', 'aa:bb:cc:dd:a1:11');
INSERT INTO public.addresses VALUES (3774, NULL, '172.27.161.18', 'aa:bb:cc:dd:a1:12');
INSERT INTO public.addresses VALUES (3775, NULL, '172.27.161.19', 'aa:bb:cc:dd:a1:13');
INSERT INTO public.addresses VALUES (3776, NULL, '172.27.161.20', 'aa:bb:cc:dd:a1:14');
INSERT INTO public.addresses VALUES (3777, NULL, '172.27.161.21', 'aa:bb:cc:dd:a1:15');
INSERT INTO public.addresses VALUES (3778, NULL, '172.27.161.22', 'aa:bb:cc:dd:a1:16');
INSERT INTO public.addresses VALUES (3779, NULL, '172.27.161.23', 'aa:bb:cc:dd:a1:17');
INSERT INTO public.addresses VALUES (3780, NULL, '172.27.161.24', 'aa:bb:cc:dd:a1:18');
INSERT INTO public.addresses VALUES (3781, NULL, '172.27.161.25', 'aa:bb:cc:dd:a1:19');
INSERT INTO public.addresses VALUES (3782, NULL, '172.27.161.26', 'aa:bb:cc:dd:a1:1a');
INSERT INTO public.addresses VALUES (3783, NULL, '172.27.161.27', 'aa:bb:cc:dd:a1:1b');
INSERT INTO public.addresses VALUES (3784, NULL, '172.27.161.28', 'aa:bb:cc:dd:a1:1c');
INSERT INTO public.addresses VALUES (3785, NULL, '172.27.161.29', 'aa:bb:cc:dd:a1:1d');
INSERT INTO public.addresses VALUES (3786, NULL, '172.27.161.30', 'aa:bb:cc:dd:a1:1e');
INSERT INTO public.addresses VALUES (3787, NULL, '172.27.161.31', 'aa:bb:cc:dd:a1:1f');
INSERT INTO public.addresses VALUES (3788, NULL, '172.27.161.32', 'aa:bb:cc:dd:a1:20');
INSERT INTO public.addresses VALUES (3789, NULL, '172.27.161.33', 'aa:bb:cc:dd:a1:21');
INSERT INTO public.addresses VALUES (3790, NULL, '172.27.161.34', 'aa:bb:cc:dd:a1:22');
INSERT INTO public.addresses VALUES (3791, NULL, '172.27.161.35', 'aa:bb:cc:dd:a1:23');
INSERT INTO public.addresses VALUES (3792, NULL, '172.27.161.36', 'aa:bb:cc:dd:a1:24');
INSERT INTO public.addresses VALUES (3793, NULL, '172.27.161.37', 'aa:bb:cc:dd:a1:25');
INSERT INTO public.addresses VALUES (3794, NULL, '172.27.161.38', 'aa:bb:cc:dd:a1:26');
INSERT INTO public.addresses VALUES (3795, NULL, '172.27.161.39', 'aa:bb:cc:dd:a1:27');
INSERT INTO public.addresses VALUES (3796, NULL, '172.27.161.40', 'aa:bb:cc:dd:a1:28');
INSERT INTO public.addresses VALUES (3797, NULL, '172.27.161.41', 'aa:bb:cc:dd:a1:29');
INSERT INTO public.addresses VALUES (3798, NULL, '172.27.161.42', 'aa:bb:cc:dd:a1:2a');
INSERT INTO public.addresses VALUES (3799, NULL, '172.27.161.43', 'aa:bb:cc:dd:a1:2b');
INSERT INTO public.addresses VALUES (3800, NULL, '172.27.161.44', 'aa:bb:cc:dd:a1:2c');
INSERT INTO public.addresses VALUES (3801, NULL, '172.27.161.45', 'aa:bb:cc:dd:a1:2d');
INSERT INTO public.addresses VALUES (3802, NULL, '172.27.161.46', 'aa:bb:cc:dd:a1:2e');
INSERT INTO public.addresses VALUES (3803, NULL, '172.27.161.47', 'aa:bb:cc:dd:a1:2f');
INSERT INTO public.addresses VALUES (3804, NULL, '172.27.161.48', 'aa:bb:cc:dd:a1:30');
INSERT INTO public.addresses VALUES (3805, NULL, '172.27.161.49', 'aa:bb:cc:dd:a1:31');
INSERT INTO public.addresses VALUES (3806, NULL, '172.27.161.50', 'aa:bb:cc:dd:a1:32');
INSERT INTO public.addresses VALUES (3807, NULL, '172.27.161.51', 'aa:bb:cc:dd:a1:33');
INSERT INTO public.addresses VALUES (3808, NULL, '172.27.161.52', 'aa:bb:cc:dd:a1:34');
INSERT INTO public.addresses VALUES (3809, NULL, '172.27.161.53', 'aa:bb:cc:dd:a1:35');
INSERT INTO public.addresses VALUES (3810, NULL, '172.27.161.54', 'aa:bb:cc:dd:a1:36');
INSERT INTO public.addresses VALUES (3811, NULL, '172.27.161.55', 'aa:bb:cc:dd:a1:37');
INSERT INTO public.addresses VALUES (3812, NULL, '172.27.161.56', 'aa:bb:cc:dd:a1:38');
INSERT INTO public.addresses VALUES (3813, NULL, '172.27.161.57', 'aa:bb:cc:dd:a1:39');
INSERT INTO public.addresses VALUES (3814, NULL, '172.27.161.58', 'aa:bb:cc:dd:a1:3a');
INSERT INTO public.addresses VALUES (3815, NULL, '172.27.161.59', 'aa:bb:cc:dd:a1:3b');
INSERT INTO public.addresses VALUES (3816, NULL, '172.27.161.60', 'aa:bb:cc:dd:a1:3c');
INSERT INTO public.addresses VALUES (3817, NULL, '172.27.161.61', 'aa:bb:cc:dd:a1:3d');
INSERT INTO public.addresses VALUES (3818, NULL, '172.27.161.62', 'aa:bb:cc:dd:a1:3e');
INSERT INTO public.addresses VALUES (3819, NULL, '172.27.161.63', 'aa:bb:cc:dd:a1:3f');
INSERT INTO public.addresses VALUES (3820, NULL, '172.27.161.64', 'aa:bb:cc:dd:a1:40');
INSERT INTO public.addresses VALUES (3821, NULL, '172.27.161.65', 'aa:bb:cc:dd:a1:41');
INSERT INTO public.addresses VALUES (3822, NULL, '172.27.161.66', 'aa:bb:cc:dd:a1:42');
INSERT INTO public.addresses VALUES (3823, NULL, '172.27.161.67', 'aa:bb:cc:dd:a1:43');
INSERT INTO public.addresses VALUES (3824, NULL, '172.27.161.68', 'aa:bb:cc:dd:a1:44');
INSERT INTO public.addresses VALUES (3825, NULL, '172.27.161.69', 'aa:bb:cc:dd:a1:45');
INSERT INTO public.addresses VALUES (3826, NULL, '172.27.161.70', 'aa:bb:cc:dd:a1:46');
INSERT INTO public.addresses VALUES (3827, NULL, '172.27.161.71', 'aa:bb:cc:dd:a1:47');
INSERT INTO public.addresses VALUES (3828, NULL, '172.27.161.72', 'aa:bb:cc:dd:a1:48');
INSERT INTO public.addresses VALUES (3829, NULL, '172.27.161.73', 'aa:bb:cc:dd:a1:49');
INSERT INTO public.addresses VALUES (3830, NULL, '172.27.161.74', 'aa:bb:cc:dd:a1:4a');
INSERT INTO public.addresses VALUES (3831, NULL, '172.27.161.75', 'aa:bb:cc:dd:a1:4b');
INSERT INTO public.addresses VALUES (3832, NULL, '172.27.161.76', 'aa:bb:cc:dd:a1:4c');
INSERT INTO public.addresses VALUES (3833, NULL, '172.27.161.77', 'aa:bb:cc:dd:a1:4d');
INSERT INTO public.addresses VALUES (3834, NULL, '172.27.161.78', 'aa:bb:cc:dd:a1:4e');
INSERT INTO public.addresses VALUES (3835, NULL, '172.27.161.79', 'aa:bb:cc:dd:a1:4f');
INSERT INTO public.addresses VALUES (3836, NULL, '172.27.161.80', 'aa:bb:cc:dd:a1:50');
INSERT INTO public.addresses VALUES (3837, NULL, '172.27.161.81', 'aa:bb:cc:dd:a1:51');
INSERT INTO public.addresses VALUES (3838, NULL, '172.27.161.82', 'aa:bb:cc:dd:a1:52');
INSERT INTO public.addresses VALUES (3839, NULL, '172.27.161.83', 'aa:bb:cc:dd:a1:53');
INSERT INTO public.addresses VALUES (3840, NULL, '172.27.161.84', 'aa:bb:cc:dd:a1:54');
INSERT INTO public.addresses VALUES (3841, NULL, '172.27.161.85', 'aa:bb:cc:dd:a1:55');
INSERT INTO public.addresses VALUES (3842, NULL, '172.27.161.86', 'aa:bb:cc:dd:a1:56');
INSERT INTO public.addresses VALUES (3843, NULL, '172.27.161.87', 'aa:bb:cc:dd:a1:57');
INSERT INTO public.addresses VALUES (3844, NULL, '172.27.161.88', 'aa:bb:cc:dd:a1:58');
INSERT INTO public.addresses VALUES (3845, NULL, '172.27.161.89', 'aa:bb:cc:dd:a1:59');
INSERT INTO public.addresses VALUES (3846, NULL, '172.27.161.90', 'aa:bb:cc:dd:a1:5a');
INSERT INTO public.addresses VALUES (3847, NULL, '172.27.161.91', 'aa:bb:cc:dd:a1:5b');
INSERT INTO public.addresses VALUES (3848, NULL, '172.27.161.92', 'aa:bb:cc:dd:a1:5c');
INSERT INTO public.addresses VALUES (3849, NULL, '172.27.161.93', 'aa:bb:cc:dd:a1:5d');
INSERT INTO public.addresses VALUES (3850, NULL, '172.27.161.94', 'aa:bb:cc:dd:a1:5e');
INSERT INTO public.addresses VALUES (3851, NULL, '172.27.161.95', 'aa:bb:cc:dd:a1:5f');
INSERT INTO public.addresses VALUES (3852, NULL, '172.27.161.96', 'aa:bb:cc:dd:a1:60');
INSERT INTO public.addresses VALUES (3853, NULL, '172.27.161.97', 'aa:bb:cc:dd:a1:61');
INSERT INTO public.addresses VALUES (3854, NULL, '172.27.161.98', 'aa:bb:cc:dd:a1:62');
INSERT INTO public.addresses VALUES (3855, NULL, '172.27.161.99', 'aa:bb:cc:dd:a1:63');
INSERT INTO public.addresses VALUES (3856, NULL, '172.27.161.100', 'aa:bb:cc:dd:a1:64');
INSERT INTO public.addresses VALUES (3857, NULL, '172.27.161.101', 'aa:bb:cc:dd:a1:65');
INSERT INTO public.addresses VALUES (3858, NULL, '172.27.161.102', 'aa:bb:cc:dd:a1:66');
INSERT INTO public.addresses VALUES (3859, NULL, '172.27.161.103', 'aa:bb:cc:dd:a1:67');
INSERT INTO public.addresses VALUES (3860, NULL, '172.27.161.104', 'aa:bb:cc:dd:a1:68');
INSERT INTO public.addresses VALUES (3861, NULL, '172.27.161.105', 'aa:bb:cc:dd:a1:69');
INSERT INTO public.addresses VALUES (3862, NULL, '172.27.161.106', 'aa:bb:cc:dd:a1:6a');
INSERT INTO public.addresses VALUES (3863, NULL, '172.27.161.107', 'aa:bb:cc:dd:a1:6b');
INSERT INTO public.addresses VALUES (3864, NULL, '172.27.161.108', 'aa:bb:cc:dd:a1:6c');
INSERT INTO public.addresses VALUES (3865, NULL, '172.27.161.109', 'aa:bb:cc:dd:a1:6d');
INSERT INTO public.addresses VALUES (3866, NULL, '172.27.161.110', 'aa:bb:cc:dd:a1:6e');
INSERT INTO public.addresses VALUES (3867, NULL, '172.27.161.111', 'aa:bb:cc:dd:a1:6f');
INSERT INTO public.addresses VALUES (3868, NULL, '172.27.161.112', 'aa:bb:cc:dd:a1:70');
INSERT INTO public.addresses VALUES (3869, NULL, '172.27.161.113', 'aa:bb:cc:dd:a1:71');
INSERT INTO public.addresses VALUES (3870, NULL, '172.27.161.114', 'aa:bb:cc:dd:a1:72');
INSERT INTO public.addresses VALUES (3871, NULL, '172.27.161.115', 'aa:bb:cc:dd:a1:73');
INSERT INTO public.addresses VALUES (3872, NULL, '172.27.161.116', 'aa:bb:cc:dd:a1:74');
INSERT INTO public.addresses VALUES (3873, NULL, '172.27.161.117', 'aa:bb:cc:dd:a1:75');
INSERT INTO public.addresses VALUES (3874, NULL, '172.27.161.118', 'aa:bb:cc:dd:a1:76');
INSERT INTO public.addresses VALUES (3875, NULL, '172.27.161.119', 'aa:bb:cc:dd:a1:77');
INSERT INTO public.addresses VALUES (3876, NULL, '172.27.161.120', 'aa:bb:cc:dd:a1:78');
INSERT INTO public.addresses VALUES (3877, NULL, '172.27.161.121', 'aa:bb:cc:dd:a1:79');
INSERT INTO public.addresses VALUES (3878, NULL, '172.27.161.122', 'aa:bb:cc:dd:a1:7a');
INSERT INTO public.addresses VALUES (3879, NULL, '172.27.161.123', 'aa:bb:cc:dd:a1:7b');
INSERT INTO public.addresses VALUES (3880, NULL, '172.27.161.124', 'aa:bb:cc:dd:a1:7c');
INSERT INTO public.addresses VALUES (3881, NULL, '172.27.161.125', 'aa:bb:cc:dd:a1:7d');
INSERT INTO public.addresses VALUES (3882, NULL, '172.27.161.126', 'aa:bb:cc:dd:a1:7e');
INSERT INTO public.addresses VALUES (3883, NULL, '172.27.161.127', 'aa:bb:cc:dd:a1:7f');
INSERT INTO public.addresses VALUES (3884, NULL, '172.27.161.128', 'aa:bb:cc:dd:a1:80');
INSERT INTO public.addresses VALUES (3885, NULL, '172.27.161.129', 'aa:bb:cc:dd:a1:81');
INSERT INTO public.addresses VALUES (3886, NULL, '172.27.161.130', 'aa:bb:cc:dd:a1:82');
INSERT INTO public.addresses VALUES (3887, NULL, '172.27.161.131', 'aa:bb:cc:dd:a1:83');
INSERT INTO public.addresses VALUES (3888, NULL, '172.27.161.132', 'aa:bb:cc:dd:a1:84');
INSERT INTO public.addresses VALUES (3889, NULL, '172.27.161.133', 'aa:bb:cc:dd:a1:85');
INSERT INTO public.addresses VALUES (3890, NULL, '172.27.161.134', 'aa:bb:cc:dd:a1:86');
INSERT INTO public.addresses VALUES (3891, NULL, '172.27.161.135', 'aa:bb:cc:dd:a1:87');
INSERT INTO public.addresses VALUES (3892, NULL, '172.27.161.136', 'aa:bb:cc:dd:a1:88');
INSERT INTO public.addresses VALUES (3893, NULL, '172.27.161.137', 'aa:bb:cc:dd:a1:89');
INSERT INTO public.addresses VALUES (3894, NULL, '172.27.161.138', 'aa:bb:cc:dd:a1:8a');
INSERT INTO public.addresses VALUES (3895, NULL, '172.27.161.139', 'aa:bb:cc:dd:a1:8b');
INSERT INTO public.addresses VALUES (3896, NULL, '172.27.161.140', 'aa:bb:cc:dd:a1:8c');
INSERT INTO public.addresses VALUES (3897, NULL, '172.27.161.141', 'aa:bb:cc:dd:a1:8d');
INSERT INTO public.addresses VALUES (3898, NULL, '172.27.161.142', 'aa:bb:cc:dd:a1:8e');
INSERT INTO public.addresses VALUES (3899, NULL, '172.27.161.143', 'aa:bb:cc:dd:a1:8f');
INSERT INTO public.addresses VALUES (3900, NULL, '172.27.161.144', 'aa:bb:cc:dd:a1:90');
INSERT INTO public.addresses VALUES (3901, NULL, '172.27.161.145', 'aa:bb:cc:dd:a1:91');
INSERT INTO public.addresses VALUES (3902, NULL, '172.27.161.146', 'aa:bb:cc:dd:a1:92');
INSERT INTO public.addresses VALUES (3903, NULL, '172.27.161.147', 'aa:bb:cc:dd:a1:93');
INSERT INTO public.addresses VALUES (3904, NULL, '172.27.161.148', 'aa:bb:cc:dd:a1:94');
INSERT INTO public.addresses VALUES (3905, NULL, '172.27.161.149', 'aa:bb:cc:dd:a1:95');
INSERT INTO public.addresses VALUES (3906, NULL, '172.27.161.150', 'aa:bb:cc:dd:a1:96');
INSERT INTO public.addresses VALUES (3907, NULL, '172.27.161.151', 'aa:bb:cc:dd:a1:97');
INSERT INTO public.addresses VALUES (3908, NULL, '172.27.161.152', 'aa:bb:cc:dd:a1:98');
INSERT INTO public.addresses VALUES (3909, NULL, '172.27.161.153', 'aa:bb:cc:dd:a1:99');
INSERT INTO public.addresses VALUES (3910, NULL, '172.27.161.154', 'aa:bb:cc:dd:a1:9a');
INSERT INTO public.addresses VALUES (3911, NULL, '172.27.161.155', 'aa:bb:cc:dd:a1:9b');
INSERT INTO public.addresses VALUES (3912, NULL, '172.27.161.156', 'aa:bb:cc:dd:a1:9c');
INSERT INTO public.addresses VALUES (3913, NULL, '172.27.161.157', 'aa:bb:cc:dd:a1:9d');
INSERT INTO public.addresses VALUES (3914, NULL, '172.27.161.158', 'aa:bb:cc:dd:a1:9e');
INSERT INTO public.addresses VALUES (3915, NULL, '172.27.161.159', 'aa:bb:cc:dd:a1:9f');
INSERT INTO public.addresses VALUES (3916, NULL, '172.27.161.160', 'aa:bb:cc:dd:a1:a0');
INSERT INTO public.addresses VALUES (3917, NULL, '172.27.161.161', 'aa:bb:cc:dd:a1:a1');
INSERT INTO public.addresses VALUES (3918, NULL, '172.27.161.162', 'aa:bb:cc:dd:a1:a2');
INSERT INTO public.addresses VALUES (3919, NULL, '172.27.161.163', 'aa:bb:cc:dd:a1:a3');
INSERT INTO public.addresses VALUES (3920, NULL, '172.27.161.164', 'aa:bb:cc:dd:a1:a4');
INSERT INTO public.addresses VALUES (3921, NULL, '172.27.161.165', 'aa:bb:cc:dd:a1:a5');
INSERT INTO public.addresses VALUES (3922, NULL, '172.27.161.166', 'aa:bb:cc:dd:a1:a6');
INSERT INTO public.addresses VALUES (3923, NULL, '172.27.161.167', 'aa:bb:cc:dd:a1:a7');
INSERT INTO public.addresses VALUES (3924, NULL, '172.27.161.168', 'aa:bb:cc:dd:a1:a8');
INSERT INTO public.addresses VALUES (3925, NULL, '172.27.161.169', 'aa:bb:cc:dd:a1:a9');
INSERT INTO public.addresses VALUES (3926, NULL, '172.27.161.170', 'aa:bb:cc:dd:a1:aa');
INSERT INTO public.addresses VALUES (3927, NULL, '172.27.161.171', 'aa:bb:cc:dd:a1:ab');
INSERT INTO public.addresses VALUES (3928, NULL, '172.27.161.172', 'aa:bb:cc:dd:a1:ac');
INSERT INTO public.addresses VALUES (3929, NULL, '172.27.161.173', 'aa:bb:cc:dd:a1:ad');
INSERT INTO public.addresses VALUES (3930, NULL, '172.27.161.174', 'aa:bb:cc:dd:a1:ae');
INSERT INTO public.addresses VALUES (3931, NULL, '172.27.161.175', 'aa:bb:cc:dd:a1:af');
INSERT INTO public.addresses VALUES (3932, NULL, '172.27.161.176', 'aa:bb:cc:dd:a1:b0');
INSERT INTO public.addresses VALUES (3933, NULL, '172.27.161.177', 'aa:bb:cc:dd:a1:b1');
INSERT INTO public.addresses VALUES (3934, NULL, '172.27.161.178', 'aa:bb:cc:dd:a1:b2');
INSERT INTO public.addresses VALUES (3935, NULL, '172.27.161.179', 'aa:bb:cc:dd:a1:b3');
INSERT INTO public.addresses VALUES (3936, NULL, '172.27.161.180', 'aa:bb:cc:dd:a1:b4');
INSERT INTO public.addresses VALUES (3937, NULL, '172.27.161.181', 'aa:bb:cc:dd:a1:b5');
INSERT INTO public.addresses VALUES (3938, NULL, '172.27.161.182', 'aa:bb:cc:dd:a1:b6');
INSERT INTO public.addresses VALUES (3939, NULL, '172.27.161.183', 'aa:bb:cc:dd:a1:b7');
INSERT INTO public.addresses VALUES (3940, NULL, '172.27.161.184', 'aa:bb:cc:dd:a1:b8');
INSERT INTO public.addresses VALUES (3941, NULL, '172.27.161.185', 'aa:bb:cc:dd:a1:b9');
INSERT INTO public.addresses VALUES (3942, NULL, '172.27.161.186', 'aa:bb:cc:dd:a1:ba');
INSERT INTO public.addresses VALUES (3943, NULL, '172.27.161.187', 'aa:bb:cc:dd:a1:bb');
INSERT INTO public.addresses VALUES (3944, NULL, '172.27.161.188', 'aa:bb:cc:dd:a1:bc');
INSERT INTO public.addresses VALUES (3945, NULL, '172.27.161.189', 'aa:bb:cc:dd:a1:bd');
INSERT INTO public.addresses VALUES (3946, NULL, '172.27.161.190', 'aa:bb:cc:dd:a1:be');
INSERT INTO public.addresses VALUES (3947, NULL, '172.27.161.191', 'aa:bb:cc:dd:a1:bf');
INSERT INTO public.addresses VALUES (3948, NULL, '172.27.161.192', 'aa:bb:cc:dd:a1:c0');
INSERT INTO public.addresses VALUES (3949, NULL, '172.27.161.193', 'aa:bb:cc:dd:a1:c1');
INSERT INTO public.addresses VALUES (3950, NULL, '172.27.161.194', 'aa:bb:cc:dd:a1:c2');
INSERT INTO public.addresses VALUES (3951, NULL, '172.27.161.195', 'aa:bb:cc:dd:a1:c3');
INSERT INTO public.addresses VALUES (3952, NULL, '172.27.161.196', 'aa:bb:cc:dd:a1:c4');
INSERT INTO public.addresses VALUES (3953, NULL, '172.27.161.197', 'aa:bb:cc:dd:a1:c5');
INSERT INTO public.addresses VALUES (3954, NULL, '172.27.161.198', 'aa:bb:cc:dd:a1:c6');
INSERT INTO public.addresses VALUES (3955, NULL, '172.27.161.199', 'aa:bb:cc:dd:a1:c7');
INSERT INTO public.addresses VALUES (3956, NULL, '172.27.161.200', 'aa:bb:cc:dd:a1:c8');
INSERT INTO public.addresses VALUES (3957, NULL, '172.27.161.201', 'aa:bb:cc:dd:a1:c9');
INSERT INTO public.addresses VALUES (3958, NULL, '172.27.161.202', 'aa:bb:cc:dd:a1:ca');
INSERT INTO public.addresses VALUES (3959, NULL, '172.27.161.203', 'aa:bb:cc:dd:a1:cb');
INSERT INTO public.addresses VALUES (3960, NULL, '172.27.161.204', 'aa:bb:cc:dd:a1:cc');
INSERT INTO public.addresses VALUES (3961, NULL, '172.27.161.205', 'aa:bb:cc:dd:a1:cd');
INSERT INTO public.addresses VALUES (3962, NULL, '172.27.161.206', 'aa:bb:cc:dd:a1:ce');
INSERT INTO public.addresses VALUES (3963, NULL, '172.27.161.207', 'aa:bb:cc:dd:a1:cf');
INSERT INTO public.addresses VALUES (3964, NULL, '172.27.161.208', 'aa:bb:cc:dd:a1:d0');
INSERT INTO public.addresses VALUES (3965, NULL, '172.27.161.209', 'aa:bb:cc:dd:a1:d1');
INSERT INTO public.addresses VALUES (3966, NULL, '172.27.161.210', 'aa:bb:cc:dd:a1:d2');
INSERT INTO public.addresses VALUES (3967, NULL, '172.27.161.211', 'aa:bb:cc:dd:a1:d3');
INSERT INTO public.addresses VALUES (3968, NULL, '172.27.161.212', 'aa:bb:cc:dd:a1:d4');
INSERT INTO public.addresses VALUES (3969, NULL, '172.27.161.213', 'aa:bb:cc:dd:a1:d5');
INSERT INTO public.addresses VALUES (3970, NULL, '172.27.161.214', 'aa:bb:cc:dd:a1:d6');
INSERT INTO public.addresses VALUES (3971, NULL, '172.27.161.215', 'aa:bb:cc:dd:a1:d7');
INSERT INTO public.addresses VALUES (3972, NULL, '172.27.161.216', 'aa:bb:cc:dd:a1:d8');
INSERT INTO public.addresses VALUES (3973, NULL, '172.27.161.217', 'aa:bb:cc:dd:a1:d9');
INSERT INTO public.addresses VALUES (3974, NULL, '172.27.161.218', 'aa:bb:cc:dd:a1:da');
INSERT INTO public.addresses VALUES (3975, NULL, '172.27.161.219', 'aa:bb:cc:dd:a1:db');
INSERT INTO public.addresses VALUES (3976, NULL, '172.27.161.220', 'aa:bb:cc:dd:a1:dc');
INSERT INTO public.addresses VALUES (3977, NULL, '172.27.161.221', 'aa:bb:cc:dd:a1:dd');
INSERT INTO public.addresses VALUES (3978, NULL, '172.27.161.222', 'aa:bb:cc:dd:a1:de');
INSERT INTO public.addresses VALUES (3979, NULL, '172.27.161.223', 'aa:bb:cc:dd:a1:df');
INSERT INTO public.addresses VALUES (3980, NULL, '172.27.161.224', 'aa:bb:cc:dd:a1:e0');
INSERT INTO public.addresses VALUES (3981, NULL, '172.27.161.225', 'aa:bb:cc:dd:a1:e1');
INSERT INTO public.addresses VALUES (3982, NULL, '172.27.161.226', 'aa:bb:cc:dd:a1:e2');
INSERT INTO public.addresses VALUES (3983, NULL, '172.27.161.227', 'aa:bb:cc:dd:a1:e3');
INSERT INTO public.addresses VALUES (3984, NULL, '172.27.161.228', 'aa:bb:cc:dd:a1:e4');
INSERT INTO public.addresses VALUES (3985, NULL, '172.27.161.229', 'aa:bb:cc:dd:a1:e5');
INSERT INTO public.addresses VALUES (3986, NULL, '172.27.161.230', 'aa:bb:cc:dd:a1:e6');
INSERT INTO public.addresses VALUES (3987, NULL, '172.27.161.231', 'aa:bb:cc:dd:a1:e7');
INSERT INTO public.addresses VALUES (3988, NULL, '172.27.161.232', 'aa:bb:cc:dd:a1:e8');
INSERT INTO public.addresses VALUES (3989, NULL, '172.27.161.233', 'aa:bb:cc:dd:a1:e9');
INSERT INTO public.addresses VALUES (3990, NULL, '172.27.161.234', 'aa:bb:cc:dd:a1:ea');
INSERT INTO public.addresses VALUES (3991, NULL, '172.27.161.235', 'aa:bb:cc:dd:a1:eb');
INSERT INTO public.addresses VALUES (3992, NULL, '172.27.161.236', 'aa:bb:cc:dd:a1:ec');
INSERT INTO public.addresses VALUES (3993, NULL, '172.27.161.237', 'aa:bb:cc:dd:a1:ed');
INSERT INTO public.addresses VALUES (3994, NULL, '172.27.161.238', 'aa:bb:cc:dd:a1:ee');
INSERT INTO public.addresses VALUES (3995, NULL, '172.27.161.239', 'aa:bb:cc:dd:a1:ef');
INSERT INTO public.addresses VALUES (3996, NULL, '172.27.161.240', 'aa:bb:cc:dd:a1:f0');
INSERT INTO public.addresses VALUES (3997, NULL, '172.27.161.241', 'aa:bb:cc:dd:a1:f1');
INSERT INTO public.addresses VALUES (3998, NULL, '172.27.161.242', 'aa:bb:cc:dd:a1:f2');
INSERT INTO public.addresses VALUES (3999, NULL, '172.27.161.243', 'aa:bb:cc:dd:a1:f3');
INSERT INTO public.addresses VALUES (4000, NULL, '172.27.161.244', 'aa:bb:cc:dd:a1:f4');
INSERT INTO public.addresses VALUES (4001, NULL, '172.27.161.245', 'aa:bb:cc:dd:a1:f5');
INSERT INTO public.addresses VALUES (4002, NULL, '172.27.161.246', 'aa:bb:cc:dd:a1:f6');
INSERT INTO public.addresses VALUES (4003, NULL, '172.27.161.247', 'aa:bb:cc:dd:a1:f7');
INSERT INTO public.addresses VALUES (4004, NULL, '172.27.161.248', 'aa:bb:cc:dd:a1:f8');
INSERT INTO public.addresses VALUES (4005, NULL, '172.27.161.249', 'aa:bb:cc:dd:a1:f9');
INSERT INTO public.addresses VALUES (4006, NULL, '172.27.161.250', 'aa:bb:cc:dd:a1:fa');
INSERT INTO public.addresses VALUES (4007, NULL, '172.27.161.251', 'aa:bb:cc:dd:a1:fb');
INSERT INTO public.addresses VALUES (4008, NULL, '172.27.161.252', 'aa:bb:cc:dd:a1:fc');
INSERT INTO public.addresses VALUES (4009, NULL, '172.27.161.253', 'aa:bb:cc:dd:a1:fd');
INSERT INTO public.addresses VALUES (4010, NULL, '172.27.161.254', 'aa:bb:cc:dd:a1:fe');
INSERT INTO public.addresses VALUES (4011, NULL, '172.27.161.255', 'aa:bb:cc:dd:a1:ff');
INSERT INTO public.addresses VALUES (4012, NULL, '172.27.162.0', 'aa:bb:cc:dd:a2:00');
INSERT INTO public.addresses VALUES (4013, NULL, '172.27.162.1', 'aa:bb:cc:dd:a2:01');
INSERT INTO public.addresses VALUES (4014, NULL, '172.27.162.2', 'aa:bb:cc:dd:a2:02');
INSERT INTO public.addresses VALUES (4015, NULL, '172.27.162.3', 'aa:bb:cc:dd:a2:03');
INSERT INTO public.addresses VALUES (4016, NULL, '172.27.162.4', 'aa:bb:cc:dd:a2:04');
INSERT INTO public.addresses VALUES (4017, NULL, '172.27.162.5', 'aa:bb:cc:dd:a2:05');
INSERT INTO public.addresses VALUES (4018, NULL, '172.27.162.6', 'aa:bb:cc:dd:a2:06');
INSERT INTO public.addresses VALUES (4019, NULL, '172.27.162.7', 'aa:bb:cc:dd:a2:07');
INSERT INTO public.addresses VALUES (4020, NULL, '172.27.162.8', 'aa:bb:cc:dd:a2:08');
INSERT INTO public.addresses VALUES (4021, NULL, '172.27.162.9', 'aa:bb:cc:dd:a2:09');
INSERT INTO public.addresses VALUES (4022, NULL, '172.27.162.10', 'aa:bb:cc:dd:a2:0a');
INSERT INTO public.addresses VALUES (4023, NULL, '172.27.162.11', 'aa:bb:cc:dd:a2:0b');
INSERT INTO public.addresses VALUES (4024, NULL, '172.27.162.12', 'aa:bb:cc:dd:a2:0c');
INSERT INTO public.addresses VALUES (4025, NULL, '172.27.162.13', 'aa:bb:cc:dd:a2:0d');
INSERT INTO public.addresses VALUES (4026, NULL, '172.27.162.14', 'aa:bb:cc:dd:a2:0e');
INSERT INTO public.addresses VALUES (4027, NULL, '172.27.162.15', 'aa:bb:cc:dd:a2:0f');
INSERT INTO public.addresses VALUES (4028, NULL, '172.27.162.16', 'aa:bb:cc:dd:a2:10');
INSERT INTO public.addresses VALUES (4029, NULL, '172.27.162.17', 'aa:bb:cc:dd:a2:11');
INSERT INTO public.addresses VALUES (4030, NULL, '172.27.162.18', 'aa:bb:cc:dd:a2:12');
INSERT INTO public.addresses VALUES (4031, NULL, '172.27.162.19', 'aa:bb:cc:dd:a2:13');
INSERT INTO public.addresses VALUES (4032, NULL, '172.27.162.20', 'aa:bb:cc:dd:a2:14');
INSERT INTO public.addresses VALUES (4033, NULL, '172.27.162.21', 'aa:bb:cc:dd:a2:15');
INSERT INTO public.addresses VALUES (4034, NULL, '172.27.162.22', 'aa:bb:cc:dd:a2:16');
INSERT INTO public.addresses VALUES (4035, NULL, '172.27.162.23', 'aa:bb:cc:dd:a2:17');
INSERT INTO public.addresses VALUES (4036, NULL, '172.27.162.24', 'aa:bb:cc:dd:a2:18');
INSERT INTO public.addresses VALUES (4037, NULL, '172.27.162.25', 'aa:bb:cc:dd:a2:19');
INSERT INTO public.addresses VALUES (4038, NULL, '172.27.162.26', 'aa:bb:cc:dd:a2:1a');
INSERT INTO public.addresses VALUES (4039, NULL, '172.27.162.27', 'aa:bb:cc:dd:a2:1b');
INSERT INTO public.addresses VALUES (4040, NULL, '172.27.162.28', 'aa:bb:cc:dd:a2:1c');
INSERT INTO public.addresses VALUES (4041, NULL, '172.27.162.29', 'aa:bb:cc:dd:a2:1d');
INSERT INTO public.addresses VALUES (4042, NULL, '172.27.162.30', 'aa:bb:cc:dd:a2:1e');
INSERT INTO public.addresses VALUES (4043, NULL, '172.27.162.31', 'aa:bb:cc:dd:a2:1f');
INSERT INTO public.addresses VALUES (4044, NULL, '172.27.162.32', 'aa:bb:cc:dd:a2:20');
INSERT INTO public.addresses VALUES (4045, NULL, '172.27.162.33', 'aa:bb:cc:dd:a2:21');
INSERT INTO public.addresses VALUES (4046, NULL, '172.27.162.34', 'aa:bb:cc:dd:a2:22');
INSERT INTO public.addresses VALUES (4047, NULL, '172.27.162.35', 'aa:bb:cc:dd:a2:23');
INSERT INTO public.addresses VALUES (4048, NULL, '172.27.162.36', 'aa:bb:cc:dd:a2:24');
INSERT INTO public.addresses VALUES (4049, NULL, '172.27.162.37', 'aa:bb:cc:dd:a2:25');
INSERT INTO public.addresses VALUES (4050, NULL, '172.27.162.38', 'aa:bb:cc:dd:a2:26');
INSERT INTO public.addresses VALUES (4051, NULL, '172.27.162.39', 'aa:bb:cc:dd:a2:27');
INSERT INTO public.addresses VALUES (4052, NULL, '172.27.162.40', 'aa:bb:cc:dd:a2:28');
INSERT INTO public.addresses VALUES (4053, NULL, '172.27.162.41', 'aa:bb:cc:dd:a2:29');
INSERT INTO public.addresses VALUES (4054, NULL, '172.27.162.42', 'aa:bb:cc:dd:a2:2a');
INSERT INTO public.addresses VALUES (4055, NULL, '172.27.162.43', 'aa:bb:cc:dd:a2:2b');
INSERT INTO public.addresses VALUES (4056, NULL, '172.27.162.44', 'aa:bb:cc:dd:a2:2c');
INSERT INTO public.addresses VALUES (4057, NULL, '172.27.162.45', 'aa:bb:cc:dd:a2:2d');
INSERT INTO public.addresses VALUES (4058, NULL, '172.27.162.46', 'aa:bb:cc:dd:a2:2e');
INSERT INTO public.addresses VALUES (4059, NULL, '172.27.162.47', 'aa:bb:cc:dd:a2:2f');
INSERT INTO public.addresses VALUES (4060, NULL, '172.27.162.48', 'aa:bb:cc:dd:a2:30');
INSERT INTO public.addresses VALUES (4061, NULL, '172.27.162.49', 'aa:bb:cc:dd:a2:31');
INSERT INTO public.addresses VALUES (4062, NULL, '172.27.162.50', 'aa:bb:cc:dd:a2:32');
INSERT INTO public.addresses VALUES (4063, NULL, '172.27.162.51', 'aa:bb:cc:dd:a2:33');
INSERT INTO public.addresses VALUES (4064, NULL, '172.27.162.52', 'aa:bb:cc:dd:a2:34');
INSERT INTO public.addresses VALUES (4065, NULL, '172.27.162.53', 'aa:bb:cc:dd:a2:35');
INSERT INTO public.addresses VALUES (4066, NULL, '172.27.162.54', 'aa:bb:cc:dd:a2:36');
INSERT INTO public.addresses VALUES (4067, NULL, '172.27.162.55', 'aa:bb:cc:dd:a2:37');
INSERT INTO public.addresses VALUES (4068, NULL, '172.27.162.56', 'aa:bb:cc:dd:a2:38');
INSERT INTO public.addresses VALUES (4069, NULL, '172.27.162.57', 'aa:bb:cc:dd:a2:39');
INSERT INTO public.addresses VALUES (4070, NULL, '172.27.162.58', 'aa:bb:cc:dd:a2:3a');
INSERT INTO public.addresses VALUES (4071, NULL, '172.27.162.59', 'aa:bb:cc:dd:a2:3b');
INSERT INTO public.addresses VALUES (4072, NULL, '172.27.162.60', 'aa:bb:cc:dd:a2:3c');
INSERT INTO public.addresses VALUES (4073, NULL, '172.27.162.61', 'aa:bb:cc:dd:a2:3d');
INSERT INTO public.addresses VALUES (4074, NULL, '172.27.162.62', 'aa:bb:cc:dd:a2:3e');
INSERT INTO public.addresses VALUES (4075, NULL, '172.27.162.63', 'aa:bb:cc:dd:a2:3f');
INSERT INTO public.addresses VALUES (4076, NULL, '172.27.162.64', 'aa:bb:cc:dd:a2:40');
INSERT INTO public.addresses VALUES (4077, NULL, '172.27.162.65', 'aa:bb:cc:dd:a2:41');
INSERT INTO public.addresses VALUES (4078, NULL, '172.27.162.66', 'aa:bb:cc:dd:a2:42');
INSERT INTO public.addresses VALUES (4079, NULL, '172.27.162.67', 'aa:bb:cc:dd:a2:43');
INSERT INTO public.addresses VALUES (4080, NULL, '172.27.162.68', 'aa:bb:cc:dd:a2:44');
INSERT INTO public.addresses VALUES (4081, NULL, '172.27.162.69', 'aa:bb:cc:dd:a2:45');
INSERT INTO public.addresses VALUES (4082, NULL, '172.27.162.70', 'aa:bb:cc:dd:a2:46');
INSERT INTO public.addresses VALUES (4083, NULL, '172.27.162.71', 'aa:bb:cc:dd:a2:47');
INSERT INTO public.addresses VALUES (4084, NULL, '172.27.162.72', 'aa:bb:cc:dd:a2:48');
INSERT INTO public.addresses VALUES (4085, NULL, '172.27.162.73', 'aa:bb:cc:dd:a2:49');
INSERT INTO public.addresses VALUES (4086, NULL, '172.27.162.74', 'aa:bb:cc:dd:a2:4a');
INSERT INTO public.addresses VALUES (4087, NULL, '172.27.162.75', 'aa:bb:cc:dd:a2:4b');
INSERT INTO public.addresses VALUES (4088, NULL, '172.27.162.76', 'aa:bb:cc:dd:a2:4c');
INSERT INTO public.addresses VALUES (4089, NULL, '172.27.162.77', 'aa:bb:cc:dd:a2:4d');
INSERT INTO public.addresses VALUES (4090, NULL, '172.27.162.78', 'aa:bb:cc:dd:a2:4e');
INSERT INTO public.addresses VALUES (4091, NULL, '172.27.162.79', 'aa:bb:cc:dd:a2:4f');
INSERT INTO public.addresses VALUES (4092, NULL, '172.27.162.80', 'aa:bb:cc:dd:a2:50');
INSERT INTO public.addresses VALUES (4093, NULL, '172.27.162.81', 'aa:bb:cc:dd:a2:51');
INSERT INTO public.addresses VALUES (4094, NULL, '172.27.162.82', 'aa:bb:cc:dd:a2:52');
INSERT INTO public.addresses VALUES (4095, NULL, '172.27.162.83', 'aa:bb:cc:dd:a2:53');
INSERT INTO public.addresses VALUES (4096, NULL, '172.27.162.84', 'aa:bb:cc:dd:a2:54');
INSERT INTO public.addresses VALUES (4097, NULL, '172.27.162.85', 'aa:bb:cc:dd:a2:55');
INSERT INTO public.addresses VALUES (4098, NULL, '172.27.162.86', 'aa:bb:cc:dd:a2:56');
INSERT INTO public.addresses VALUES (4099, NULL, '172.27.162.87', 'aa:bb:cc:dd:a2:57');
INSERT INTO public.addresses VALUES (4100, NULL, '172.27.162.88', 'aa:bb:cc:dd:a2:58');
INSERT INTO public.addresses VALUES (4101, NULL, '172.27.162.89', 'aa:bb:cc:dd:a2:59');
INSERT INTO public.addresses VALUES (4102, NULL, '172.27.162.90', 'aa:bb:cc:dd:a2:5a');
INSERT INTO public.addresses VALUES (4103, NULL, '172.27.162.91', 'aa:bb:cc:dd:a2:5b');
INSERT INTO public.addresses VALUES (4104, NULL, '172.27.162.92', 'aa:bb:cc:dd:a2:5c');
INSERT INTO public.addresses VALUES (4105, NULL, '172.27.162.93', 'aa:bb:cc:dd:a2:5d');
INSERT INTO public.addresses VALUES (4106, NULL, '172.27.162.94', 'aa:bb:cc:dd:a2:5e');
INSERT INTO public.addresses VALUES (4107, NULL, '172.27.162.95', 'aa:bb:cc:dd:a2:5f');
INSERT INTO public.addresses VALUES (4108, NULL, '172.27.162.96', 'aa:bb:cc:dd:a2:60');
INSERT INTO public.addresses VALUES (4109, NULL, '172.27.162.97', 'aa:bb:cc:dd:a2:61');
INSERT INTO public.addresses VALUES (4110, NULL, '172.27.162.98', 'aa:bb:cc:dd:a2:62');
INSERT INTO public.addresses VALUES (4111, NULL, '172.27.162.99', 'aa:bb:cc:dd:a2:63');
INSERT INTO public.addresses VALUES (4112, NULL, '172.27.162.100', 'aa:bb:cc:dd:a2:64');
INSERT INTO public.addresses VALUES (4113, NULL, '172.27.162.101', 'aa:bb:cc:dd:a2:65');
INSERT INTO public.addresses VALUES (4114, NULL, '172.27.162.102', 'aa:bb:cc:dd:a2:66');
INSERT INTO public.addresses VALUES (4115, NULL, '172.27.162.103', 'aa:bb:cc:dd:a2:67');
INSERT INTO public.addresses VALUES (4116, NULL, '172.27.162.104', 'aa:bb:cc:dd:a2:68');
INSERT INTO public.addresses VALUES (4117, NULL, '172.27.162.105', 'aa:bb:cc:dd:a2:69');
INSERT INTO public.addresses VALUES (4118, NULL, '172.27.162.106', 'aa:bb:cc:dd:a2:6a');
INSERT INTO public.addresses VALUES (4119, NULL, '172.27.162.107', 'aa:bb:cc:dd:a2:6b');
INSERT INTO public.addresses VALUES (4120, NULL, '172.27.162.108', 'aa:bb:cc:dd:a2:6c');
INSERT INTO public.addresses VALUES (4121, NULL, '172.27.162.109', 'aa:bb:cc:dd:a2:6d');
INSERT INTO public.addresses VALUES (4122, NULL, '172.27.162.110', 'aa:bb:cc:dd:a2:6e');
INSERT INTO public.addresses VALUES (4123, NULL, '172.27.162.111', 'aa:bb:cc:dd:a2:6f');
INSERT INTO public.addresses VALUES (4124, NULL, '172.27.162.112', 'aa:bb:cc:dd:a2:70');
INSERT INTO public.addresses VALUES (4125, NULL, '172.27.162.113', 'aa:bb:cc:dd:a2:71');
INSERT INTO public.addresses VALUES (4126, NULL, '172.27.162.114', 'aa:bb:cc:dd:a2:72');
INSERT INTO public.addresses VALUES (4127, NULL, '172.27.162.115', 'aa:bb:cc:dd:a2:73');
INSERT INTO public.addresses VALUES (4128, NULL, '172.27.162.116', 'aa:bb:cc:dd:a2:74');
INSERT INTO public.addresses VALUES (4129, NULL, '172.27.162.117', 'aa:bb:cc:dd:a2:75');
INSERT INTO public.addresses VALUES (4130, NULL, '172.27.162.118', 'aa:bb:cc:dd:a2:76');
INSERT INTO public.addresses VALUES (4131, NULL, '172.27.162.119', 'aa:bb:cc:dd:a2:77');
INSERT INTO public.addresses VALUES (4132, NULL, '172.27.162.120', 'aa:bb:cc:dd:a2:78');
INSERT INTO public.addresses VALUES (4133, NULL, '172.27.162.121', 'aa:bb:cc:dd:a2:79');
INSERT INTO public.addresses VALUES (4134, NULL, '172.27.162.122', 'aa:bb:cc:dd:a2:7a');
INSERT INTO public.addresses VALUES (4135, NULL, '172.27.162.123', 'aa:bb:cc:dd:a2:7b');
INSERT INTO public.addresses VALUES (4136, NULL, '172.27.162.124', 'aa:bb:cc:dd:a2:7c');
INSERT INTO public.addresses VALUES (4137, NULL, '172.27.162.125', 'aa:bb:cc:dd:a2:7d');
INSERT INTO public.addresses VALUES (4138, NULL, '172.27.162.126', 'aa:bb:cc:dd:a2:7e');
INSERT INTO public.addresses VALUES (4139, NULL, '172.27.162.127', 'aa:bb:cc:dd:a2:7f');
INSERT INTO public.addresses VALUES (4140, NULL, '172.27.162.128', 'aa:bb:cc:dd:a2:80');
INSERT INTO public.addresses VALUES (4141, NULL, '172.27.162.129', 'aa:bb:cc:dd:a2:81');
INSERT INTO public.addresses VALUES (4142, NULL, '172.27.162.130', 'aa:bb:cc:dd:a2:82');
INSERT INTO public.addresses VALUES (4143, NULL, '172.27.162.131', 'aa:bb:cc:dd:a2:83');
INSERT INTO public.addresses VALUES (4144, NULL, '172.27.162.132', 'aa:bb:cc:dd:a2:84');
INSERT INTO public.addresses VALUES (4145, NULL, '172.27.162.133', 'aa:bb:cc:dd:a2:85');
INSERT INTO public.addresses VALUES (4146, NULL, '172.27.162.134', 'aa:bb:cc:dd:a2:86');
INSERT INTO public.addresses VALUES (4147, NULL, '172.27.162.135', 'aa:bb:cc:dd:a2:87');
INSERT INTO public.addresses VALUES (4148, NULL, '172.27.162.136', 'aa:bb:cc:dd:a2:88');
INSERT INTO public.addresses VALUES (4149, NULL, '172.27.162.137', 'aa:bb:cc:dd:a2:89');
INSERT INTO public.addresses VALUES (4150, NULL, '172.27.162.138', 'aa:bb:cc:dd:a2:8a');
INSERT INTO public.addresses VALUES (4151, NULL, '172.27.162.139', 'aa:bb:cc:dd:a2:8b');
INSERT INTO public.addresses VALUES (4152, NULL, '172.27.162.140', 'aa:bb:cc:dd:a2:8c');
INSERT INTO public.addresses VALUES (4153, NULL, '172.27.162.141', 'aa:bb:cc:dd:a2:8d');
INSERT INTO public.addresses VALUES (4154, NULL, '172.27.162.142', 'aa:bb:cc:dd:a2:8e');
INSERT INTO public.addresses VALUES (4155, NULL, '172.27.162.143', 'aa:bb:cc:dd:a2:8f');
INSERT INTO public.addresses VALUES (4156, NULL, '172.27.162.144', 'aa:bb:cc:dd:a2:90');
INSERT INTO public.addresses VALUES (4157, NULL, '172.27.162.145', 'aa:bb:cc:dd:a2:91');
INSERT INTO public.addresses VALUES (4158, NULL, '172.27.162.146', 'aa:bb:cc:dd:a2:92');
INSERT INTO public.addresses VALUES (4159, NULL, '172.27.162.147', 'aa:bb:cc:dd:a2:93');
INSERT INTO public.addresses VALUES (4160, NULL, '172.27.162.148', 'aa:bb:cc:dd:a2:94');
INSERT INTO public.addresses VALUES (4161, NULL, '172.27.162.149', 'aa:bb:cc:dd:a2:95');
INSERT INTO public.addresses VALUES (4162, NULL, '172.27.162.150', 'aa:bb:cc:dd:a2:96');
INSERT INTO public.addresses VALUES (4163, NULL, '172.27.162.151', 'aa:bb:cc:dd:a2:97');
INSERT INTO public.addresses VALUES (4164, NULL, '172.27.162.152', 'aa:bb:cc:dd:a2:98');
INSERT INTO public.addresses VALUES (4165, NULL, '172.27.162.153', 'aa:bb:cc:dd:a2:99');
INSERT INTO public.addresses VALUES (4166, NULL, '172.27.162.154', 'aa:bb:cc:dd:a2:9a');
INSERT INTO public.addresses VALUES (4167, NULL, '172.27.162.155', 'aa:bb:cc:dd:a2:9b');
INSERT INTO public.addresses VALUES (4168, NULL, '172.27.162.156', 'aa:bb:cc:dd:a2:9c');
INSERT INTO public.addresses VALUES (4169, NULL, '172.27.162.157', 'aa:bb:cc:dd:a2:9d');
INSERT INTO public.addresses VALUES (4170, NULL, '172.27.162.158', 'aa:bb:cc:dd:a2:9e');
INSERT INTO public.addresses VALUES (4171, NULL, '172.27.162.159', 'aa:bb:cc:dd:a2:9f');
INSERT INTO public.addresses VALUES (4172, NULL, '172.27.162.160', 'aa:bb:cc:dd:a2:a0');
INSERT INTO public.addresses VALUES (4173, NULL, '172.27.162.161', 'aa:bb:cc:dd:a2:a1');
INSERT INTO public.addresses VALUES (4174, NULL, '172.27.162.162', 'aa:bb:cc:dd:a2:a2');
INSERT INTO public.addresses VALUES (4175, NULL, '172.27.162.163', 'aa:bb:cc:dd:a2:a3');
INSERT INTO public.addresses VALUES (4176, NULL, '172.27.162.164', 'aa:bb:cc:dd:a2:a4');
INSERT INTO public.addresses VALUES (4177, NULL, '172.27.162.165', 'aa:bb:cc:dd:a2:a5');
INSERT INTO public.addresses VALUES (4178, NULL, '172.27.162.166', 'aa:bb:cc:dd:a2:a6');
INSERT INTO public.addresses VALUES (4179, NULL, '172.27.162.167', 'aa:bb:cc:dd:a2:a7');
INSERT INTO public.addresses VALUES (4180, NULL, '172.27.162.168', 'aa:bb:cc:dd:a2:a8');
INSERT INTO public.addresses VALUES (4181, NULL, '172.27.162.169', 'aa:bb:cc:dd:a2:a9');
INSERT INTO public.addresses VALUES (4182, NULL, '172.27.162.170', 'aa:bb:cc:dd:a2:aa');
INSERT INTO public.addresses VALUES (4183, NULL, '172.27.162.171', 'aa:bb:cc:dd:a2:ab');
INSERT INTO public.addresses VALUES (4184, NULL, '172.27.162.172', 'aa:bb:cc:dd:a2:ac');
INSERT INTO public.addresses VALUES (4185, NULL, '172.27.162.173', 'aa:bb:cc:dd:a2:ad');
INSERT INTO public.addresses VALUES (4186, NULL, '172.27.162.174', 'aa:bb:cc:dd:a2:ae');
INSERT INTO public.addresses VALUES (4187, NULL, '172.27.162.175', 'aa:bb:cc:dd:a2:af');
INSERT INTO public.addresses VALUES (4188, NULL, '172.27.162.176', 'aa:bb:cc:dd:a2:b0');
INSERT INTO public.addresses VALUES (4189, NULL, '172.27.162.177', 'aa:bb:cc:dd:a2:b1');
INSERT INTO public.addresses VALUES (4190, NULL, '172.27.162.178', 'aa:bb:cc:dd:a2:b2');
INSERT INTO public.addresses VALUES (4191, NULL, '172.27.162.179', 'aa:bb:cc:dd:a2:b3');
INSERT INTO public.addresses VALUES (4192, NULL, '172.27.162.180', 'aa:bb:cc:dd:a2:b4');
INSERT INTO public.addresses VALUES (4193, NULL, '172.27.162.181', 'aa:bb:cc:dd:a2:b5');
INSERT INTO public.addresses VALUES (4194, NULL, '172.27.162.182', 'aa:bb:cc:dd:a2:b6');
INSERT INTO public.addresses VALUES (4195, NULL, '172.27.162.183', 'aa:bb:cc:dd:a2:b7');
INSERT INTO public.addresses VALUES (4196, NULL, '172.27.162.184', 'aa:bb:cc:dd:a2:b8');
INSERT INTO public.addresses VALUES (4197, NULL, '172.27.162.185', 'aa:bb:cc:dd:a2:b9');
INSERT INTO public.addresses VALUES (4198, NULL, '172.27.162.186', 'aa:bb:cc:dd:a2:ba');
INSERT INTO public.addresses VALUES (4199, NULL, '172.27.162.187', 'aa:bb:cc:dd:a2:bb');
INSERT INTO public.addresses VALUES (4200, NULL, '172.27.162.188', 'aa:bb:cc:dd:a2:bc');
INSERT INTO public.addresses VALUES (4201, NULL, '172.27.162.189', 'aa:bb:cc:dd:a2:bd');
INSERT INTO public.addresses VALUES (4202, NULL, '172.27.162.190', 'aa:bb:cc:dd:a2:be');
INSERT INTO public.addresses VALUES (4203, NULL, '172.27.162.191', 'aa:bb:cc:dd:a2:bf');
INSERT INTO public.addresses VALUES (4204, NULL, '172.27.162.192', 'aa:bb:cc:dd:a2:c0');
INSERT INTO public.addresses VALUES (4205, NULL, '172.27.162.193', 'aa:bb:cc:dd:a2:c1');
INSERT INTO public.addresses VALUES (4206, NULL, '172.27.162.194', 'aa:bb:cc:dd:a2:c2');
INSERT INTO public.addresses VALUES (4207, NULL, '172.27.162.195', 'aa:bb:cc:dd:a2:c3');
INSERT INTO public.addresses VALUES (4208, NULL, '172.27.162.196', 'aa:bb:cc:dd:a2:c4');
INSERT INTO public.addresses VALUES (4209, NULL, '172.27.162.197', 'aa:bb:cc:dd:a2:c5');
INSERT INTO public.addresses VALUES (4210, NULL, '172.27.162.198', 'aa:bb:cc:dd:a2:c6');
INSERT INTO public.addresses VALUES (4211, NULL, '172.27.162.199', 'aa:bb:cc:dd:a2:c7');
INSERT INTO public.addresses VALUES (4212, NULL, '172.27.162.200', 'aa:bb:cc:dd:a2:c8');
INSERT INTO public.addresses VALUES (4213, NULL, '172.27.162.201', 'aa:bb:cc:dd:a2:c9');
INSERT INTO public.addresses VALUES (4214, NULL, '172.27.162.202', 'aa:bb:cc:dd:a2:ca');
INSERT INTO public.addresses VALUES (4215, NULL, '172.27.162.203', 'aa:bb:cc:dd:a2:cb');
INSERT INTO public.addresses VALUES (4216, NULL, '172.27.162.204', 'aa:bb:cc:dd:a2:cc');
INSERT INTO public.addresses VALUES (4217, NULL, '172.27.162.205', 'aa:bb:cc:dd:a2:cd');
INSERT INTO public.addresses VALUES (4218, NULL, '172.27.162.206', 'aa:bb:cc:dd:a2:ce');
INSERT INTO public.addresses VALUES (4219, NULL, '172.27.162.207', 'aa:bb:cc:dd:a2:cf');
INSERT INTO public.addresses VALUES (4220, NULL, '172.27.162.208', 'aa:bb:cc:dd:a2:d0');
INSERT INTO public.addresses VALUES (4221, NULL, '172.27.162.209', 'aa:bb:cc:dd:a2:d1');
INSERT INTO public.addresses VALUES (4222, NULL, '172.27.162.210', 'aa:bb:cc:dd:a2:d2');
INSERT INTO public.addresses VALUES (4223, NULL, '172.27.162.211', 'aa:bb:cc:dd:a2:d3');
INSERT INTO public.addresses VALUES (4224, NULL, '172.27.162.212', 'aa:bb:cc:dd:a2:d4');
INSERT INTO public.addresses VALUES (4225, NULL, '172.27.162.213', 'aa:bb:cc:dd:a2:d5');
INSERT INTO public.addresses VALUES (4226, NULL, '172.27.162.214', 'aa:bb:cc:dd:a2:d6');
INSERT INTO public.addresses VALUES (4227, NULL, '172.27.162.215', 'aa:bb:cc:dd:a2:d7');
INSERT INTO public.addresses VALUES (4228, NULL, '172.27.162.216', 'aa:bb:cc:dd:a2:d8');
INSERT INTO public.addresses VALUES (4229, NULL, '172.27.162.217', 'aa:bb:cc:dd:a2:d9');
INSERT INTO public.addresses VALUES (4230, NULL, '172.27.162.218', 'aa:bb:cc:dd:a2:da');
INSERT INTO public.addresses VALUES (4231, NULL, '172.27.162.219', 'aa:bb:cc:dd:a2:db');
INSERT INTO public.addresses VALUES (4232, NULL, '172.27.162.220', 'aa:bb:cc:dd:a2:dc');
INSERT INTO public.addresses VALUES (4233, NULL, '172.27.162.221', 'aa:bb:cc:dd:a2:dd');
INSERT INTO public.addresses VALUES (4234, NULL, '172.27.162.222', 'aa:bb:cc:dd:a2:de');
INSERT INTO public.addresses VALUES (4235, NULL, '172.27.162.223', 'aa:bb:cc:dd:a2:df');
INSERT INTO public.addresses VALUES (4236, NULL, '172.27.162.224', 'aa:bb:cc:dd:a2:e0');
INSERT INTO public.addresses VALUES (4237, NULL, '172.27.162.225', 'aa:bb:cc:dd:a2:e1');
INSERT INTO public.addresses VALUES (4238, NULL, '172.27.162.226', 'aa:bb:cc:dd:a2:e2');
INSERT INTO public.addresses VALUES (4239, NULL, '172.27.162.227', 'aa:bb:cc:dd:a2:e3');
INSERT INTO public.addresses VALUES (4240, NULL, '172.27.162.228', 'aa:bb:cc:dd:a2:e4');
INSERT INTO public.addresses VALUES (4241, NULL, '172.27.162.229', 'aa:bb:cc:dd:a2:e5');
INSERT INTO public.addresses VALUES (4242, NULL, '172.27.162.230', 'aa:bb:cc:dd:a2:e6');
INSERT INTO public.addresses VALUES (4243, NULL, '172.27.162.231', 'aa:bb:cc:dd:a2:e7');
INSERT INTO public.addresses VALUES (4244, NULL, '172.27.162.232', 'aa:bb:cc:dd:a2:e8');
INSERT INTO public.addresses VALUES (4245, NULL, '172.27.162.233', 'aa:bb:cc:dd:a2:e9');
INSERT INTO public.addresses VALUES (4246, NULL, '172.27.162.234', 'aa:bb:cc:dd:a2:ea');
INSERT INTO public.addresses VALUES (4247, NULL, '172.27.162.235', 'aa:bb:cc:dd:a2:eb');
INSERT INTO public.addresses VALUES (4248, NULL, '172.27.162.236', 'aa:bb:cc:dd:a2:ec');
INSERT INTO public.addresses VALUES (4249, NULL, '172.27.162.237', 'aa:bb:cc:dd:a2:ed');
INSERT INTO public.addresses VALUES (4250, NULL, '172.27.162.238', 'aa:bb:cc:dd:a2:ee');
INSERT INTO public.addresses VALUES (4251, NULL, '172.27.162.239', 'aa:bb:cc:dd:a2:ef');
INSERT INTO public.addresses VALUES (4252, NULL, '172.27.162.240', 'aa:bb:cc:dd:a2:f0');
INSERT INTO public.addresses VALUES (4253, NULL, '172.27.162.241', 'aa:bb:cc:dd:a2:f1');
INSERT INTO public.addresses VALUES (4254, NULL, '172.27.162.242', 'aa:bb:cc:dd:a2:f2');
INSERT INTO public.addresses VALUES (4255, NULL, '172.27.162.243', 'aa:bb:cc:dd:a2:f3');
INSERT INTO public.addresses VALUES (4256, NULL, '172.27.162.244', 'aa:bb:cc:dd:a2:f4');
INSERT INTO public.addresses VALUES (4257, NULL, '172.27.162.245', 'aa:bb:cc:dd:a2:f5');
INSERT INTO public.addresses VALUES (4258, NULL, '172.27.162.246', 'aa:bb:cc:dd:a2:f6');
INSERT INTO public.addresses VALUES (4259, NULL, '172.27.162.247', 'aa:bb:cc:dd:a2:f7');
INSERT INTO public.addresses VALUES (4260, NULL, '172.27.162.248', 'aa:bb:cc:dd:a2:f8');
INSERT INTO public.addresses VALUES (4261, NULL, '172.27.162.249', 'aa:bb:cc:dd:a2:f9');
INSERT INTO public.addresses VALUES (4262, NULL, '172.27.162.250', 'aa:bb:cc:dd:a2:fa');
INSERT INTO public.addresses VALUES (4263, NULL, '172.27.162.251', 'aa:bb:cc:dd:a2:fb');
INSERT INTO public.addresses VALUES (4264, NULL, '172.27.162.252', 'aa:bb:cc:dd:a2:fc');
INSERT INTO public.addresses VALUES (4265, NULL, '172.27.162.253', 'aa:bb:cc:dd:a2:fd');
INSERT INTO public.addresses VALUES (4266, NULL, '172.27.162.254', 'aa:bb:cc:dd:a2:fe');
INSERT INTO public.addresses VALUES (4267, NULL, '172.27.162.255', 'aa:bb:cc:dd:a2:ff');
INSERT INTO public.addresses VALUES (4268, NULL, '172.27.163.0', 'aa:bb:cc:dd:a3:00');
INSERT INTO public.addresses VALUES (4269, NULL, '172.27.163.1', 'aa:bb:cc:dd:a3:01');
INSERT INTO public.addresses VALUES (4270, NULL, '172.27.163.2', 'aa:bb:cc:dd:a3:02');
INSERT INTO public.addresses VALUES (4271, NULL, '172.27.163.3', 'aa:bb:cc:dd:a3:03');
INSERT INTO public.addresses VALUES (4272, NULL, '172.27.163.4', 'aa:bb:cc:dd:a3:04');
INSERT INTO public.addresses VALUES (4273, NULL, '172.27.163.5', 'aa:bb:cc:dd:a3:05');
INSERT INTO public.addresses VALUES (4274, NULL, '172.27.163.6', 'aa:bb:cc:dd:a3:06');
INSERT INTO public.addresses VALUES (4275, NULL, '172.27.163.7', 'aa:bb:cc:dd:a3:07');
INSERT INTO public.addresses VALUES (4276, NULL, '172.27.163.8', 'aa:bb:cc:dd:a3:08');
INSERT INTO public.addresses VALUES (4277, NULL, '172.27.163.9', 'aa:bb:cc:dd:a3:09');
INSERT INTO public.addresses VALUES (4278, NULL, '172.27.163.10', 'aa:bb:cc:dd:a3:0a');
INSERT INTO public.addresses VALUES (4279, NULL, '172.27.163.11', 'aa:bb:cc:dd:a3:0b');
INSERT INTO public.addresses VALUES (4280, NULL, '172.27.163.12', 'aa:bb:cc:dd:a3:0c');
INSERT INTO public.addresses VALUES (4281, NULL, '172.27.163.13', 'aa:bb:cc:dd:a3:0d');
INSERT INTO public.addresses VALUES (4282, NULL, '172.27.163.14', 'aa:bb:cc:dd:a3:0e');
INSERT INTO public.addresses VALUES (4283, NULL, '172.27.163.15', 'aa:bb:cc:dd:a3:0f');
INSERT INTO public.addresses VALUES (4284, NULL, '172.27.163.16', 'aa:bb:cc:dd:a3:10');
INSERT INTO public.addresses VALUES (4285, NULL, '172.27.163.17', 'aa:bb:cc:dd:a3:11');
INSERT INTO public.addresses VALUES (4286, NULL, '172.27.163.18', 'aa:bb:cc:dd:a3:12');
INSERT INTO public.addresses VALUES (4287, NULL, '172.27.163.19', 'aa:bb:cc:dd:a3:13');
INSERT INTO public.addresses VALUES (4288, NULL, '172.27.163.20', 'aa:bb:cc:dd:a3:14');
INSERT INTO public.addresses VALUES (4289, NULL, '172.27.163.21', 'aa:bb:cc:dd:a3:15');
INSERT INTO public.addresses VALUES (4290, NULL, '172.27.163.22', 'aa:bb:cc:dd:a3:16');
INSERT INTO public.addresses VALUES (4291, NULL, '172.27.163.23', 'aa:bb:cc:dd:a3:17');
INSERT INTO public.addresses VALUES (4292, NULL, '172.27.163.24', 'aa:bb:cc:dd:a3:18');
INSERT INTO public.addresses VALUES (4293, NULL, '172.27.163.25', 'aa:bb:cc:dd:a3:19');
INSERT INTO public.addresses VALUES (4294, NULL, '172.27.163.26', 'aa:bb:cc:dd:a3:1a');
INSERT INTO public.addresses VALUES (4295, NULL, '172.27.163.27', 'aa:bb:cc:dd:a3:1b');
INSERT INTO public.addresses VALUES (4296, NULL, '172.27.163.28', 'aa:bb:cc:dd:a3:1c');
INSERT INTO public.addresses VALUES (4297, NULL, '172.27.163.29', 'aa:bb:cc:dd:a3:1d');
INSERT INTO public.addresses VALUES (4298, NULL, '172.27.163.30', 'aa:bb:cc:dd:a3:1e');
INSERT INTO public.addresses VALUES (4299, NULL, '172.27.163.31', 'aa:bb:cc:dd:a3:1f');
INSERT INTO public.addresses VALUES (4300, NULL, '172.27.163.32', 'aa:bb:cc:dd:a3:20');
INSERT INTO public.addresses VALUES (4301, NULL, '172.27.163.33', 'aa:bb:cc:dd:a3:21');
INSERT INTO public.addresses VALUES (4302, NULL, '172.27.163.34', 'aa:bb:cc:dd:a3:22');
INSERT INTO public.addresses VALUES (4303, NULL, '172.27.163.35', 'aa:bb:cc:dd:a3:23');
INSERT INTO public.addresses VALUES (4304, NULL, '172.27.163.36', 'aa:bb:cc:dd:a3:24');
INSERT INTO public.addresses VALUES (4305, NULL, '172.27.163.37', 'aa:bb:cc:dd:a3:25');
INSERT INTO public.addresses VALUES (4306, NULL, '172.27.163.38', 'aa:bb:cc:dd:a3:26');
INSERT INTO public.addresses VALUES (4307, NULL, '172.27.163.39', 'aa:bb:cc:dd:a3:27');
INSERT INTO public.addresses VALUES (4308, NULL, '172.27.163.40', 'aa:bb:cc:dd:a3:28');
INSERT INTO public.addresses VALUES (4309, NULL, '172.27.163.41', 'aa:bb:cc:dd:a3:29');
INSERT INTO public.addresses VALUES (4310, NULL, '172.27.163.42', 'aa:bb:cc:dd:a3:2a');
INSERT INTO public.addresses VALUES (4311, NULL, '172.27.163.43', 'aa:bb:cc:dd:a3:2b');
INSERT INTO public.addresses VALUES (4312, NULL, '172.27.163.44', 'aa:bb:cc:dd:a3:2c');
INSERT INTO public.addresses VALUES (4313, NULL, '172.27.163.45', 'aa:bb:cc:dd:a3:2d');
INSERT INTO public.addresses VALUES (4314, NULL, '172.27.163.46', 'aa:bb:cc:dd:a3:2e');
INSERT INTO public.addresses VALUES (4315, NULL, '172.27.163.47', 'aa:bb:cc:dd:a3:2f');
INSERT INTO public.addresses VALUES (4316, NULL, '172.27.163.48', 'aa:bb:cc:dd:a3:30');
INSERT INTO public.addresses VALUES (4317, NULL, '172.27.163.49', 'aa:bb:cc:dd:a3:31');
INSERT INTO public.addresses VALUES (4318, NULL, '172.27.163.50', 'aa:bb:cc:dd:a3:32');
INSERT INTO public.addresses VALUES (4319, NULL, '172.27.163.51', 'aa:bb:cc:dd:a3:33');
INSERT INTO public.addresses VALUES (4320, NULL, '172.27.163.52', 'aa:bb:cc:dd:a3:34');
INSERT INTO public.addresses VALUES (4321, NULL, '172.27.163.53', 'aa:bb:cc:dd:a3:35');
INSERT INTO public.addresses VALUES (4322, NULL, '172.27.163.54', 'aa:bb:cc:dd:a3:36');
INSERT INTO public.addresses VALUES (4323, NULL, '172.27.163.55', 'aa:bb:cc:dd:a3:37');
INSERT INTO public.addresses VALUES (4324, NULL, '172.27.163.56', 'aa:bb:cc:dd:a3:38');
INSERT INTO public.addresses VALUES (4325, NULL, '172.27.163.57', 'aa:bb:cc:dd:a3:39');
INSERT INTO public.addresses VALUES (4326, NULL, '172.27.163.58', 'aa:bb:cc:dd:a3:3a');
INSERT INTO public.addresses VALUES (4327, NULL, '172.27.163.59', 'aa:bb:cc:dd:a3:3b');
INSERT INTO public.addresses VALUES (4328, NULL, '172.27.163.60', 'aa:bb:cc:dd:a3:3c');
INSERT INTO public.addresses VALUES (4329, NULL, '172.27.163.61', 'aa:bb:cc:dd:a3:3d');
INSERT INTO public.addresses VALUES (4330, NULL, '172.27.163.62', 'aa:bb:cc:dd:a3:3e');
INSERT INTO public.addresses VALUES (4331, NULL, '172.27.163.63', 'aa:bb:cc:dd:a3:3f');
INSERT INTO public.addresses VALUES (4332, NULL, '172.27.163.64', 'aa:bb:cc:dd:a3:40');
INSERT INTO public.addresses VALUES (4333, NULL, '172.27.163.65', 'aa:bb:cc:dd:a3:41');
INSERT INTO public.addresses VALUES (4334, NULL, '172.27.163.66', 'aa:bb:cc:dd:a3:42');
INSERT INTO public.addresses VALUES (4335, NULL, '172.27.163.67', 'aa:bb:cc:dd:a3:43');
INSERT INTO public.addresses VALUES (4336, NULL, '172.27.163.68', 'aa:bb:cc:dd:a3:44');
INSERT INTO public.addresses VALUES (4337, NULL, '172.27.163.69', 'aa:bb:cc:dd:a3:45');
INSERT INTO public.addresses VALUES (4338, NULL, '172.27.163.70', 'aa:bb:cc:dd:a3:46');
INSERT INTO public.addresses VALUES (4339, NULL, '172.27.163.71', 'aa:bb:cc:dd:a3:47');
INSERT INTO public.addresses VALUES (4340, NULL, '172.27.163.72', 'aa:bb:cc:dd:a3:48');
INSERT INTO public.addresses VALUES (4341, NULL, '172.27.163.73', 'aa:bb:cc:dd:a3:49');
INSERT INTO public.addresses VALUES (4342, NULL, '172.27.163.74', 'aa:bb:cc:dd:a3:4a');
INSERT INTO public.addresses VALUES (4343, NULL, '172.27.163.75', 'aa:bb:cc:dd:a3:4b');
INSERT INTO public.addresses VALUES (4344, NULL, '172.27.163.76', 'aa:bb:cc:dd:a3:4c');
INSERT INTO public.addresses VALUES (4345, NULL, '172.27.163.77', 'aa:bb:cc:dd:a3:4d');
INSERT INTO public.addresses VALUES (4346, NULL, '172.27.163.78', 'aa:bb:cc:dd:a3:4e');
INSERT INTO public.addresses VALUES (4347, NULL, '172.27.163.79', 'aa:bb:cc:dd:a3:4f');
INSERT INTO public.addresses VALUES (4348, NULL, '172.27.163.80', 'aa:bb:cc:dd:a3:50');
INSERT INTO public.addresses VALUES (4349, NULL, '172.27.163.81', 'aa:bb:cc:dd:a3:51');
INSERT INTO public.addresses VALUES (4350, NULL, '172.27.163.82', 'aa:bb:cc:dd:a3:52');
INSERT INTO public.addresses VALUES (4351, NULL, '172.27.163.83', 'aa:bb:cc:dd:a3:53');
INSERT INTO public.addresses VALUES (4352, NULL, '172.27.163.84', 'aa:bb:cc:dd:a3:54');
INSERT INTO public.addresses VALUES (4353, NULL, '172.27.163.85', 'aa:bb:cc:dd:a3:55');
INSERT INTO public.addresses VALUES (4354, NULL, '172.27.163.86', 'aa:bb:cc:dd:a3:56');
INSERT INTO public.addresses VALUES (4355, NULL, '172.27.163.87', 'aa:bb:cc:dd:a3:57');
INSERT INTO public.addresses VALUES (4356, NULL, '172.27.163.88', 'aa:bb:cc:dd:a3:58');
INSERT INTO public.addresses VALUES (4357, NULL, '172.27.163.89', 'aa:bb:cc:dd:a3:59');
INSERT INTO public.addresses VALUES (4358, NULL, '172.27.163.90', 'aa:bb:cc:dd:a3:5a');
INSERT INTO public.addresses VALUES (4359, NULL, '172.27.163.91', 'aa:bb:cc:dd:a3:5b');
INSERT INTO public.addresses VALUES (4360, NULL, '172.27.163.92', 'aa:bb:cc:dd:a3:5c');
INSERT INTO public.addresses VALUES (4361, NULL, '172.27.163.93', 'aa:bb:cc:dd:a3:5d');
INSERT INTO public.addresses VALUES (4362, NULL, '172.27.163.94', 'aa:bb:cc:dd:a3:5e');
INSERT INTO public.addresses VALUES (4363, NULL, '172.27.163.95', 'aa:bb:cc:dd:a3:5f');
INSERT INTO public.addresses VALUES (4364, NULL, '172.27.163.96', 'aa:bb:cc:dd:a3:60');
INSERT INTO public.addresses VALUES (4365, NULL, '172.27.163.97', 'aa:bb:cc:dd:a3:61');
INSERT INTO public.addresses VALUES (4366, NULL, '172.27.163.98', 'aa:bb:cc:dd:a3:62');
INSERT INTO public.addresses VALUES (4367, NULL, '172.27.163.99', 'aa:bb:cc:dd:a3:63');
INSERT INTO public.addresses VALUES (4368, NULL, '172.27.163.100', 'aa:bb:cc:dd:a3:64');
INSERT INTO public.addresses VALUES (4369, NULL, '172.27.163.101', 'aa:bb:cc:dd:a3:65');
INSERT INTO public.addresses VALUES (4370, NULL, '172.27.163.102', 'aa:bb:cc:dd:a3:66');
INSERT INTO public.addresses VALUES (4371, NULL, '172.27.163.103', 'aa:bb:cc:dd:a3:67');
INSERT INTO public.addresses VALUES (4372, NULL, '172.27.163.104', 'aa:bb:cc:dd:a3:68');
INSERT INTO public.addresses VALUES (4373, NULL, '172.27.163.105', 'aa:bb:cc:dd:a3:69');
INSERT INTO public.addresses VALUES (4374, NULL, '172.27.163.106', 'aa:bb:cc:dd:a3:6a');
INSERT INTO public.addresses VALUES (4375, NULL, '172.27.163.107', 'aa:bb:cc:dd:a3:6b');
INSERT INTO public.addresses VALUES (4376, NULL, '172.27.163.108', 'aa:bb:cc:dd:a3:6c');
INSERT INTO public.addresses VALUES (4377, NULL, '172.27.163.109', 'aa:bb:cc:dd:a3:6d');
INSERT INTO public.addresses VALUES (4378, NULL, '172.27.163.110', 'aa:bb:cc:dd:a3:6e');
INSERT INTO public.addresses VALUES (4379, NULL, '172.27.163.111', 'aa:bb:cc:dd:a3:6f');
INSERT INTO public.addresses VALUES (4380, NULL, '172.27.163.112', 'aa:bb:cc:dd:a3:70');
INSERT INTO public.addresses VALUES (4381, NULL, '172.27.163.113', 'aa:bb:cc:dd:a3:71');
INSERT INTO public.addresses VALUES (4382, NULL, '172.27.163.114', 'aa:bb:cc:dd:a3:72');
INSERT INTO public.addresses VALUES (4383, NULL, '172.27.163.115', 'aa:bb:cc:dd:a3:73');
INSERT INTO public.addresses VALUES (4384, NULL, '172.27.163.116', 'aa:bb:cc:dd:a3:74');
INSERT INTO public.addresses VALUES (4385, NULL, '172.27.163.117', 'aa:bb:cc:dd:a3:75');
INSERT INTO public.addresses VALUES (4386, NULL, '172.27.163.118', 'aa:bb:cc:dd:a3:76');
INSERT INTO public.addresses VALUES (4387, NULL, '172.27.163.119', 'aa:bb:cc:dd:a3:77');
INSERT INTO public.addresses VALUES (4388, NULL, '172.27.163.120', 'aa:bb:cc:dd:a3:78');
INSERT INTO public.addresses VALUES (4389, NULL, '172.27.163.121', 'aa:bb:cc:dd:a3:79');
INSERT INTO public.addresses VALUES (4390, NULL, '172.27.163.122', 'aa:bb:cc:dd:a3:7a');
INSERT INTO public.addresses VALUES (4391, NULL, '172.27.163.123', 'aa:bb:cc:dd:a3:7b');
INSERT INTO public.addresses VALUES (4392, NULL, '172.27.163.124', 'aa:bb:cc:dd:a3:7c');
INSERT INTO public.addresses VALUES (4393, NULL, '172.27.163.125', 'aa:bb:cc:dd:a3:7d');
INSERT INTO public.addresses VALUES (4394, NULL, '172.27.163.126', 'aa:bb:cc:dd:a3:7e');
INSERT INTO public.addresses VALUES (4395, NULL, '172.27.163.127', 'aa:bb:cc:dd:a3:7f');
INSERT INTO public.addresses VALUES (4396, NULL, '172.27.163.128', 'aa:bb:cc:dd:a3:80');
INSERT INTO public.addresses VALUES (4397, NULL, '172.27.163.129', 'aa:bb:cc:dd:a3:81');
INSERT INTO public.addresses VALUES (4398, NULL, '172.27.163.130', 'aa:bb:cc:dd:a3:82');
INSERT INTO public.addresses VALUES (4399, NULL, '172.27.163.131', 'aa:bb:cc:dd:a3:83');
INSERT INTO public.addresses VALUES (4400, NULL, '172.27.163.132', 'aa:bb:cc:dd:a3:84');
INSERT INTO public.addresses VALUES (4401, NULL, '172.27.163.133', 'aa:bb:cc:dd:a3:85');
INSERT INTO public.addresses VALUES (4402, NULL, '172.27.163.134', 'aa:bb:cc:dd:a3:86');
INSERT INTO public.addresses VALUES (4403, NULL, '172.27.163.135', 'aa:bb:cc:dd:a3:87');
INSERT INTO public.addresses VALUES (4404, NULL, '172.27.163.136', 'aa:bb:cc:dd:a3:88');
INSERT INTO public.addresses VALUES (4405, NULL, '172.27.163.137', 'aa:bb:cc:dd:a3:89');
INSERT INTO public.addresses VALUES (4406, NULL, '172.27.163.138', 'aa:bb:cc:dd:a3:8a');
INSERT INTO public.addresses VALUES (4407, NULL, '172.27.163.139', 'aa:bb:cc:dd:a3:8b');
INSERT INTO public.addresses VALUES (4408, NULL, '172.27.163.140', 'aa:bb:cc:dd:a3:8c');
INSERT INTO public.addresses VALUES (4409, NULL, '172.27.163.141', 'aa:bb:cc:dd:a3:8d');
INSERT INTO public.addresses VALUES (4410, NULL, '172.27.163.142', 'aa:bb:cc:dd:a3:8e');
INSERT INTO public.addresses VALUES (4411, NULL, '172.27.163.143', 'aa:bb:cc:dd:a3:8f');
INSERT INTO public.addresses VALUES (4412, NULL, '172.27.163.144', 'aa:bb:cc:dd:a3:90');
INSERT INTO public.addresses VALUES (4413, NULL, '172.27.163.145', 'aa:bb:cc:dd:a3:91');
INSERT INTO public.addresses VALUES (4414, NULL, '172.27.163.146', 'aa:bb:cc:dd:a3:92');
INSERT INTO public.addresses VALUES (4415, NULL, '172.27.163.147', 'aa:bb:cc:dd:a3:93');
INSERT INTO public.addresses VALUES (4416, NULL, '172.27.163.148', 'aa:bb:cc:dd:a3:94');
INSERT INTO public.addresses VALUES (4417, NULL, '172.27.163.149', 'aa:bb:cc:dd:a3:95');
INSERT INTO public.addresses VALUES (4418, NULL, '172.27.163.150', 'aa:bb:cc:dd:a3:96');
INSERT INTO public.addresses VALUES (4419, NULL, '172.27.163.151', 'aa:bb:cc:dd:a3:97');
INSERT INTO public.addresses VALUES (4420, NULL, '172.27.163.152', 'aa:bb:cc:dd:a3:98');
INSERT INTO public.addresses VALUES (4421, NULL, '172.27.163.153', 'aa:bb:cc:dd:a3:99');
INSERT INTO public.addresses VALUES (4422, NULL, '172.27.163.154', 'aa:bb:cc:dd:a3:9a');
INSERT INTO public.addresses VALUES (4423, NULL, '172.27.163.155', 'aa:bb:cc:dd:a3:9b');
INSERT INTO public.addresses VALUES (4424, NULL, '172.27.163.156', 'aa:bb:cc:dd:a3:9c');
INSERT INTO public.addresses VALUES (4425, NULL, '172.27.163.157', 'aa:bb:cc:dd:a3:9d');
INSERT INTO public.addresses VALUES (4426, NULL, '172.27.163.158', 'aa:bb:cc:dd:a3:9e');
INSERT INTO public.addresses VALUES (4427, NULL, '172.27.163.159', 'aa:bb:cc:dd:a3:9f');
INSERT INTO public.addresses VALUES (4428, NULL, '172.27.163.160', 'aa:bb:cc:dd:a3:a0');
INSERT INTO public.addresses VALUES (4429, NULL, '172.27.163.161', 'aa:bb:cc:dd:a3:a1');
INSERT INTO public.addresses VALUES (4430, NULL, '172.27.163.162', 'aa:bb:cc:dd:a3:a2');
INSERT INTO public.addresses VALUES (4431, NULL, '172.27.163.163', 'aa:bb:cc:dd:a3:a3');
INSERT INTO public.addresses VALUES (4432, NULL, '172.27.163.164', 'aa:bb:cc:dd:a3:a4');
INSERT INTO public.addresses VALUES (4433, NULL, '172.27.163.165', 'aa:bb:cc:dd:a3:a5');
INSERT INTO public.addresses VALUES (4434, NULL, '172.27.163.166', 'aa:bb:cc:dd:a3:a6');
INSERT INTO public.addresses VALUES (4435, NULL, '172.27.163.167', 'aa:bb:cc:dd:a3:a7');
INSERT INTO public.addresses VALUES (4436, NULL, '172.27.163.168', 'aa:bb:cc:dd:a3:a8');
INSERT INTO public.addresses VALUES (4437, NULL, '172.27.163.169', 'aa:bb:cc:dd:a3:a9');
INSERT INTO public.addresses VALUES (4438, NULL, '172.27.163.170', 'aa:bb:cc:dd:a3:aa');
INSERT INTO public.addresses VALUES (4439, NULL, '172.27.163.171', 'aa:bb:cc:dd:a3:ab');
INSERT INTO public.addresses VALUES (4440, NULL, '172.27.163.172', 'aa:bb:cc:dd:a3:ac');
INSERT INTO public.addresses VALUES (4441, NULL, '172.27.163.173', 'aa:bb:cc:dd:a3:ad');
INSERT INTO public.addresses VALUES (4442, NULL, '172.27.163.174', 'aa:bb:cc:dd:a3:ae');
INSERT INTO public.addresses VALUES (4443, NULL, '172.27.163.175', 'aa:bb:cc:dd:a3:af');
INSERT INTO public.addresses VALUES (4444, NULL, '172.27.163.176', 'aa:bb:cc:dd:a3:b0');
INSERT INTO public.addresses VALUES (4445, NULL, '172.27.163.177', 'aa:bb:cc:dd:a3:b1');
INSERT INTO public.addresses VALUES (4446, NULL, '172.27.163.178', 'aa:bb:cc:dd:a3:b2');
INSERT INTO public.addresses VALUES (4447, NULL, '172.27.163.179', 'aa:bb:cc:dd:a3:b3');
INSERT INTO public.addresses VALUES (4448, NULL, '172.27.163.180', 'aa:bb:cc:dd:a3:b4');
INSERT INTO public.addresses VALUES (4449, NULL, '172.27.163.181', 'aa:bb:cc:dd:a3:b5');
INSERT INTO public.addresses VALUES (4450, NULL, '172.27.163.182', 'aa:bb:cc:dd:a3:b6');
INSERT INTO public.addresses VALUES (4451, NULL, '172.27.163.183', 'aa:bb:cc:dd:a3:b7');
INSERT INTO public.addresses VALUES (4452, NULL, '172.27.163.184', 'aa:bb:cc:dd:a3:b8');
INSERT INTO public.addresses VALUES (4453, NULL, '172.27.163.185', 'aa:bb:cc:dd:a3:b9');
INSERT INTO public.addresses VALUES (4454, NULL, '172.27.163.186', 'aa:bb:cc:dd:a3:ba');
INSERT INTO public.addresses VALUES (4455, NULL, '172.27.163.187', 'aa:bb:cc:dd:a3:bb');
INSERT INTO public.addresses VALUES (4456, NULL, '172.27.163.188', 'aa:bb:cc:dd:a3:bc');
INSERT INTO public.addresses VALUES (4457, NULL, '172.27.163.189', 'aa:bb:cc:dd:a3:bd');
INSERT INTO public.addresses VALUES (4458, NULL, '172.27.163.190', 'aa:bb:cc:dd:a3:be');
INSERT INTO public.addresses VALUES (4459, NULL, '172.27.163.191', 'aa:bb:cc:dd:a3:bf');
INSERT INTO public.addresses VALUES (4460, NULL, '172.27.163.192', 'aa:bb:cc:dd:a3:c0');
INSERT INTO public.addresses VALUES (4461, NULL, '172.27.163.193', 'aa:bb:cc:dd:a3:c1');
INSERT INTO public.addresses VALUES (4462, NULL, '172.27.163.194', 'aa:bb:cc:dd:a3:c2');
INSERT INTO public.addresses VALUES (4463, NULL, '172.27.163.195', 'aa:bb:cc:dd:a3:c3');
INSERT INTO public.addresses VALUES (4464, NULL, '172.27.163.196', 'aa:bb:cc:dd:a3:c4');
INSERT INTO public.addresses VALUES (4465, NULL, '172.27.163.197', 'aa:bb:cc:dd:a3:c5');
INSERT INTO public.addresses VALUES (4466, NULL, '172.27.163.198', 'aa:bb:cc:dd:a3:c6');
INSERT INTO public.addresses VALUES (4467, NULL, '172.27.163.199', 'aa:bb:cc:dd:a3:c7');
INSERT INTO public.addresses VALUES (4468, NULL, '172.27.163.200', 'aa:bb:cc:dd:a3:c8');
INSERT INTO public.addresses VALUES (4469, NULL, '172.27.163.201', 'aa:bb:cc:dd:a3:c9');
INSERT INTO public.addresses VALUES (4470, NULL, '172.27.163.202', 'aa:bb:cc:dd:a3:ca');
INSERT INTO public.addresses VALUES (4471, NULL, '172.27.163.203', 'aa:bb:cc:dd:a3:cb');
INSERT INTO public.addresses VALUES (4472, NULL, '172.27.163.204', 'aa:bb:cc:dd:a3:cc');
INSERT INTO public.addresses VALUES (4473, NULL, '172.27.163.205', 'aa:bb:cc:dd:a3:cd');
INSERT INTO public.addresses VALUES (4474, NULL, '172.27.163.206', 'aa:bb:cc:dd:a3:ce');
INSERT INTO public.addresses VALUES (4475, NULL, '172.27.163.207', 'aa:bb:cc:dd:a3:cf');
INSERT INTO public.addresses VALUES (4476, NULL, '172.27.163.208', 'aa:bb:cc:dd:a3:d0');
INSERT INTO public.addresses VALUES (4477, NULL, '172.27.163.209', 'aa:bb:cc:dd:a3:d1');
INSERT INTO public.addresses VALUES (4478, NULL, '172.27.163.210', 'aa:bb:cc:dd:a3:d2');
INSERT INTO public.addresses VALUES (4479, NULL, '172.27.163.211', 'aa:bb:cc:dd:a3:d3');
INSERT INTO public.addresses VALUES (4480, NULL, '172.27.163.212', 'aa:bb:cc:dd:a3:d4');
INSERT INTO public.addresses VALUES (4481, NULL, '172.27.163.213', 'aa:bb:cc:dd:a3:d5');
INSERT INTO public.addresses VALUES (4482, NULL, '172.27.163.214', 'aa:bb:cc:dd:a3:d6');
INSERT INTO public.addresses VALUES (4483, NULL, '172.27.163.215', 'aa:bb:cc:dd:a3:d7');
INSERT INTO public.addresses VALUES (4484, NULL, '172.27.163.216', 'aa:bb:cc:dd:a3:d8');
INSERT INTO public.addresses VALUES (4485, NULL, '172.27.163.217', 'aa:bb:cc:dd:a3:d9');
INSERT INTO public.addresses VALUES (4486, NULL, '172.27.163.218', 'aa:bb:cc:dd:a3:da');
INSERT INTO public.addresses VALUES (4487, NULL, '172.27.163.219', 'aa:bb:cc:dd:a3:db');
INSERT INTO public.addresses VALUES (4488, NULL, '172.27.163.220', 'aa:bb:cc:dd:a3:dc');
INSERT INTO public.addresses VALUES (4489, NULL, '172.27.163.221', 'aa:bb:cc:dd:a3:dd');
INSERT INTO public.addresses VALUES (4490, NULL, '172.27.163.222', 'aa:bb:cc:dd:a3:de');
INSERT INTO public.addresses VALUES (4491, NULL, '172.27.163.223', 'aa:bb:cc:dd:a3:df');
INSERT INTO public.addresses VALUES (4492, NULL, '172.27.163.224', 'aa:bb:cc:dd:a3:e0');
INSERT INTO public.addresses VALUES (4493, NULL, '172.27.163.225', 'aa:bb:cc:dd:a3:e1');
INSERT INTO public.addresses VALUES (4494, NULL, '172.27.163.226', 'aa:bb:cc:dd:a3:e2');
INSERT INTO public.addresses VALUES (4495, NULL, '172.27.163.227', 'aa:bb:cc:dd:a3:e3');
INSERT INTO public.addresses VALUES (4496, NULL, '172.27.163.228', 'aa:bb:cc:dd:a3:e4');
INSERT INTO public.addresses VALUES (4497, NULL, '172.27.163.229', 'aa:bb:cc:dd:a3:e5');
INSERT INTO public.addresses VALUES (4498, NULL, '172.27.163.230', 'aa:bb:cc:dd:a3:e6');
INSERT INTO public.addresses VALUES (4499, NULL, '172.27.163.231', 'aa:bb:cc:dd:a3:e7');
INSERT INTO public.addresses VALUES (4500, NULL, '172.27.163.232', 'aa:bb:cc:dd:a3:e8');
INSERT INTO public.addresses VALUES (4501, NULL, '172.27.163.233', 'aa:bb:cc:dd:a3:e9');
INSERT INTO public.addresses VALUES (4502, NULL, '172.27.163.234', 'aa:bb:cc:dd:a3:ea');
INSERT INTO public.addresses VALUES (4503, NULL, '172.27.163.235', 'aa:bb:cc:dd:a3:eb');
INSERT INTO public.addresses VALUES (4504, NULL, '172.27.163.236', 'aa:bb:cc:dd:a3:ec');
INSERT INTO public.addresses VALUES (4505, NULL, '172.27.163.237', 'aa:bb:cc:dd:a3:ed');
INSERT INTO public.addresses VALUES (4506, NULL, '172.27.163.238', 'aa:bb:cc:dd:a3:ee');
INSERT INTO public.addresses VALUES (4507, NULL, '172.27.163.239', 'aa:bb:cc:dd:a3:ef');
INSERT INTO public.addresses VALUES (4508, NULL, '172.27.163.240', 'aa:bb:cc:dd:a3:f0');
INSERT INTO public.addresses VALUES (4509, NULL, '172.27.163.241', 'aa:bb:cc:dd:a3:f1');
INSERT INTO public.addresses VALUES (4510, NULL, '172.27.163.242', 'aa:bb:cc:dd:a3:f2');
INSERT INTO public.addresses VALUES (4511, NULL, '172.27.163.243', 'aa:bb:cc:dd:a3:f3');
INSERT INTO public.addresses VALUES (4512, NULL, '172.27.163.244', 'aa:bb:cc:dd:a3:f4');
INSERT INTO public.addresses VALUES (4513, NULL, '172.27.163.245', 'aa:bb:cc:dd:a3:f5');
INSERT INTO public.addresses VALUES (4514, NULL, '172.27.163.246', 'aa:bb:cc:dd:a3:f6');
INSERT INTO public.addresses VALUES (4515, NULL, '172.27.163.247', 'aa:bb:cc:dd:a3:f7');
INSERT INTO public.addresses VALUES (4516, NULL, '172.27.163.248', 'aa:bb:cc:dd:a3:f8');
INSERT INTO public.addresses VALUES (4517, NULL, '172.27.163.249', 'aa:bb:cc:dd:a3:f9');
INSERT INTO public.addresses VALUES (4518, NULL, '172.27.163.250', 'aa:bb:cc:dd:a3:fa');
INSERT INTO public.addresses VALUES (4519, NULL, '172.27.163.251', 'aa:bb:cc:dd:a3:fb');
INSERT INTO public.addresses VALUES (4520, NULL, '172.27.163.252', 'aa:bb:cc:dd:a3:fc');
INSERT INTO public.addresses VALUES (4521, NULL, '172.27.163.253', 'aa:bb:cc:dd:a3:fd');
INSERT INTO public.addresses VALUES (4522, NULL, '172.27.163.254', 'aa:bb:cc:dd:a3:fe');
INSERT INTO public.addresses VALUES (4523, NULL, '172.27.163.255', 'aa:bb:cc:dd:a3:ff');
INSERT INTO public.addresses VALUES (4524, NULL, '172.27.164.0', 'aa:bb:cc:dd:a4:00');
INSERT INTO public.addresses VALUES (4525, NULL, '172.27.164.1', 'aa:bb:cc:dd:a4:01');
INSERT INTO public.addresses VALUES (4526, NULL, '172.27.164.2', 'aa:bb:cc:dd:a4:02');
INSERT INTO public.addresses VALUES (4527, NULL, '172.27.164.3', 'aa:bb:cc:dd:a4:03');
INSERT INTO public.addresses VALUES (4528, NULL, '172.27.164.4', 'aa:bb:cc:dd:a4:04');
INSERT INTO public.addresses VALUES (4529, NULL, '172.27.164.5', 'aa:bb:cc:dd:a4:05');
INSERT INTO public.addresses VALUES (4530, NULL, '172.27.164.6', 'aa:bb:cc:dd:a4:06');
INSERT INTO public.addresses VALUES (4531, NULL, '172.27.164.7', 'aa:bb:cc:dd:a4:07');
INSERT INTO public.addresses VALUES (4532, NULL, '172.27.164.8', 'aa:bb:cc:dd:a4:08');
INSERT INTO public.addresses VALUES (4533, NULL, '172.27.164.9', 'aa:bb:cc:dd:a4:09');
INSERT INTO public.addresses VALUES (4534, NULL, '172.27.164.10', 'aa:bb:cc:dd:a4:0a');
INSERT INTO public.addresses VALUES (4535, NULL, '172.27.164.11', 'aa:bb:cc:dd:a4:0b');
INSERT INTO public.addresses VALUES (4536, NULL, '172.27.164.12', 'aa:bb:cc:dd:a4:0c');
INSERT INTO public.addresses VALUES (4537, NULL, '172.27.164.13', 'aa:bb:cc:dd:a4:0d');
INSERT INTO public.addresses VALUES (4538, NULL, '172.27.164.14', 'aa:bb:cc:dd:a4:0e');
INSERT INTO public.addresses VALUES (4539, NULL, '172.27.164.15', 'aa:bb:cc:dd:a4:0f');
INSERT INTO public.addresses VALUES (4540, NULL, '172.27.164.16', 'aa:bb:cc:dd:a4:10');
INSERT INTO public.addresses VALUES (4541, NULL, '172.27.164.17', 'aa:bb:cc:dd:a4:11');
INSERT INTO public.addresses VALUES (4542, NULL, '172.27.164.18', 'aa:bb:cc:dd:a4:12');
INSERT INTO public.addresses VALUES (4543, NULL, '172.27.164.19', 'aa:bb:cc:dd:a4:13');
INSERT INTO public.addresses VALUES (4544, NULL, '172.27.164.20', 'aa:bb:cc:dd:a4:14');
INSERT INTO public.addresses VALUES (4545, NULL, '172.27.164.21', 'aa:bb:cc:dd:a4:15');
INSERT INTO public.addresses VALUES (4546, NULL, '172.27.164.22', 'aa:bb:cc:dd:a4:16');
INSERT INTO public.addresses VALUES (4547, NULL, '172.27.164.23', 'aa:bb:cc:dd:a4:17');
INSERT INTO public.addresses VALUES (4548, NULL, '172.27.164.24', 'aa:bb:cc:dd:a4:18');
INSERT INTO public.addresses VALUES (4549, NULL, '172.27.164.25', 'aa:bb:cc:dd:a4:19');
INSERT INTO public.addresses VALUES (4550, NULL, '172.27.164.26', 'aa:bb:cc:dd:a4:1a');
INSERT INTO public.addresses VALUES (4551, NULL, '172.27.164.27', 'aa:bb:cc:dd:a4:1b');
INSERT INTO public.addresses VALUES (4552, NULL, '172.27.164.28', 'aa:bb:cc:dd:a4:1c');
INSERT INTO public.addresses VALUES (4553, NULL, '172.27.164.29', 'aa:bb:cc:dd:a4:1d');
INSERT INTO public.addresses VALUES (4554, NULL, '172.27.164.30', 'aa:bb:cc:dd:a4:1e');
INSERT INTO public.addresses VALUES (4555, NULL, '172.27.164.31', 'aa:bb:cc:dd:a4:1f');
INSERT INTO public.addresses VALUES (4556, NULL, '172.27.164.32', 'aa:bb:cc:dd:a4:20');
INSERT INTO public.addresses VALUES (4557, NULL, '172.27.164.33', 'aa:bb:cc:dd:a4:21');
INSERT INTO public.addresses VALUES (4558, NULL, '172.27.164.34', 'aa:bb:cc:dd:a4:22');
INSERT INTO public.addresses VALUES (4559, NULL, '172.27.164.35', 'aa:bb:cc:dd:a4:23');
INSERT INTO public.addresses VALUES (4560, NULL, '172.27.164.36', 'aa:bb:cc:dd:a4:24');
INSERT INTO public.addresses VALUES (4561, NULL, '172.27.164.37', 'aa:bb:cc:dd:a4:25');
INSERT INTO public.addresses VALUES (4562, NULL, '172.27.164.38', 'aa:bb:cc:dd:a4:26');
INSERT INTO public.addresses VALUES (4563, NULL, '172.27.164.39', 'aa:bb:cc:dd:a4:27');
INSERT INTO public.addresses VALUES (4564, NULL, '172.27.164.40', 'aa:bb:cc:dd:a4:28');
INSERT INTO public.addresses VALUES (4565, NULL, '172.27.164.41', 'aa:bb:cc:dd:a4:29');
INSERT INTO public.addresses VALUES (4566, NULL, '172.27.164.42', 'aa:bb:cc:dd:a4:2a');
INSERT INTO public.addresses VALUES (4567, NULL, '172.27.164.43', 'aa:bb:cc:dd:a4:2b');
INSERT INTO public.addresses VALUES (4568, NULL, '172.27.164.44', 'aa:bb:cc:dd:a4:2c');
INSERT INTO public.addresses VALUES (4569, NULL, '172.27.164.45', 'aa:bb:cc:dd:a4:2d');
INSERT INTO public.addresses VALUES (4570, NULL, '172.27.164.46', 'aa:bb:cc:dd:a4:2e');
INSERT INTO public.addresses VALUES (4571, NULL, '172.27.164.47', 'aa:bb:cc:dd:a4:2f');
INSERT INTO public.addresses VALUES (4572, NULL, '172.27.164.48', 'aa:bb:cc:dd:a4:30');
INSERT INTO public.addresses VALUES (4573, NULL, '172.27.164.49', 'aa:bb:cc:dd:a4:31');
INSERT INTO public.addresses VALUES (4574, NULL, '172.27.164.50', 'aa:bb:cc:dd:a4:32');
INSERT INTO public.addresses VALUES (4575, NULL, '172.27.164.51', 'aa:bb:cc:dd:a4:33');
INSERT INTO public.addresses VALUES (4576, NULL, '172.27.164.52', 'aa:bb:cc:dd:a4:34');
INSERT INTO public.addresses VALUES (4577, NULL, '172.27.164.53', 'aa:bb:cc:dd:a4:35');
INSERT INTO public.addresses VALUES (4578, NULL, '172.27.164.54', 'aa:bb:cc:dd:a4:36');
INSERT INTO public.addresses VALUES (4579, NULL, '172.27.164.55', 'aa:bb:cc:dd:a4:37');
INSERT INTO public.addresses VALUES (4580, NULL, '172.27.164.56', 'aa:bb:cc:dd:a4:38');
INSERT INTO public.addresses VALUES (4581, NULL, '172.27.164.57', 'aa:bb:cc:dd:a4:39');
INSERT INTO public.addresses VALUES (4582, NULL, '172.27.164.58', 'aa:bb:cc:dd:a4:3a');
INSERT INTO public.addresses VALUES (4583, NULL, '172.27.164.59', 'aa:bb:cc:dd:a4:3b');
INSERT INTO public.addresses VALUES (4584, NULL, '172.27.164.60', 'aa:bb:cc:dd:a4:3c');
INSERT INTO public.addresses VALUES (4585, NULL, '172.27.164.61', 'aa:bb:cc:dd:a4:3d');
INSERT INTO public.addresses VALUES (4586, NULL, '172.27.164.62', 'aa:bb:cc:dd:a4:3e');
INSERT INTO public.addresses VALUES (4587, NULL, '172.27.164.63', 'aa:bb:cc:dd:a4:3f');
INSERT INTO public.addresses VALUES (4588, NULL, '172.27.164.64', 'aa:bb:cc:dd:a4:40');
INSERT INTO public.addresses VALUES (4589, NULL, '172.27.164.65', 'aa:bb:cc:dd:a4:41');
INSERT INTO public.addresses VALUES (4590, NULL, '172.27.164.66', 'aa:bb:cc:dd:a4:42');
INSERT INTO public.addresses VALUES (4591, NULL, '172.27.164.67', 'aa:bb:cc:dd:a4:43');
INSERT INTO public.addresses VALUES (4592, NULL, '172.27.164.68', 'aa:bb:cc:dd:a4:44');
INSERT INTO public.addresses VALUES (4593, NULL, '172.27.164.69', 'aa:bb:cc:dd:a4:45');
INSERT INTO public.addresses VALUES (4594, NULL, '172.27.164.70', 'aa:bb:cc:dd:a4:46');
INSERT INTO public.addresses VALUES (4595, NULL, '172.27.164.71', 'aa:bb:cc:dd:a4:47');
INSERT INTO public.addresses VALUES (4596, NULL, '172.27.164.72', 'aa:bb:cc:dd:a4:48');
INSERT INTO public.addresses VALUES (4597, NULL, '172.27.164.73', 'aa:bb:cc:dd:a4:49');
INSERT INTO public.addresses VALUES (4598, NULL, '172.27.164.74', 'aa:bb:cc:dd:a4:4a');
INSERT INTO public.addresses VALUES (4599, NULL, '172.27.164.75', 'aa:bb:cc:dd:a4:4b');
INSERT INTO public.addresses VALUES (4600, NULL, '172.27.164.76', 'aa:bb:cc:dd:a4:4c');
INSERT INTO public.addresses VALUES (4601, NULL, '172.27.164.77', 'aa:bb:cc:dd:a4:4d');
INSERT INTO public.addresses VALUES (4602, NULL, '172.27.164.78', 'aa:bb:cc:dd:a4:4e');
INSERT INTO public.addresses VALUES (4603, NULL, '172.27.164.79', 'aa:bb:cc:dd:a4:4f');
INSERT INTO public.addresses VALUES (4604, NULL, '172.27.164.80', 'aa:bb:cc:dd:a4:50');
INSERT INTO public.addresses VALUES (4605, NULL, '172.27.164.81', 'aa:bb:cc:dd:a4:51');
INSERT INTO public.addresses VALUES (4606, NULL, '172.27.164.82', 'aa:bb:cc:dd:a4:52');
INSERT INTO public.addresses VALUES (4607, NULL, '172.27.164.83', 'aa:bb:cc:dd:a4:53');
INSERT INTO public.addresses VALUES (4608, NULL, '172.27.164.84', 'aa:bb:cc:dd:a4:54');
INSERT INTO public.addresses VALUES (4609, NULL, '172.27.164.85', 'aa:bb:cc:dd:a4:55');
INSERT INTO public.addresses VALUES (4610, NULL, '172.27.164.86', 'aa:bb:cc:dd:a4:56');
INSERT INTO public.addresses VALUES (4611, NULL, '172.27.164.87', 'aa:bb:cc:dd:a4:57');
INSERT INTO public.addresses VALUES (4612, NULL, '172.27.164.88', 'aa:bb:cc:dd:a4:58');
INSERT INTO public.addresses VALUES (4613, NULL, '172.27.164.89', 'aa:bb:cc:dd:a4:59');
INSERT INTO public.addresses VALUES (4614, NULL, '172.27.164.90', 'aa:bb:cc:dd:a4:5a');
INSERT INTO public.addresses VALUES (4615, NULL, '172.27.164.91', 'aa:bb:cc:dd:a4:5b');
INSERT INTO public.addresses VALUES (4616, NULL, '172.27.164.92', 'aa:bb:cc:dd:a4:5c');
INSERT INTO public.addresses VALUES (4617, NULL, '172.27.164.93', 'aa:bb:cc:dd:a4:5d');
INSERT INTO public.addresses VALUES (4618, NULL, '172.27.164.94', 'aa:bb:cc:dd:a4:5e');
INSERT INTO public.addresses VALUES (4619, NULL, '172.27.164.95', 'aa:bb:cc:dd:a4:5f');
INSERT INTO public.addresses VALUES (4620, NULL, '172.27.164.96', 'aa:bb:cc:dd:a4:60');
INSERT INTO public.addresses VALUES (4621, NULL, '172.27.164.97', 'aa:bb:cc:dd:a4:61');
INSERT INTO public.addresses VALUES (4622, NULL, '172.27.164.98', 'aa:bb:cc:dd:a4:62');
INSERT INTO public.addresses VALUES (4623, NULL, '172.27.164.99', 'aa:bb:cc:dd:a4:63');
INSERT INTO public.addresses VALUES (4624, NULL, '172.27.164.100', 'aa:bb:cc:dd:a4:64');
INSERT INTO public.addresses VALUES (4625, NULL, '172.27.164.101', 'aa:bb:cc:dd:a4:65');
INSERT INTO public.addresses VALUES (4626, NULL, '172.27.164.102', 'aa:bb:cc:dd:a4:66');
INSERT INTO public.addresses VALUES (4627, NULL, '172.27.164.103', 'aa:bb:cc:dd:a4:67');
INSERT INTO public.addresses VALUES (4628, NULL, '172.27.164.104', 'aa:bb:cc:dd:a4:68');
INSERT INTO public.addresses VALUES (4629, NULL, '172.27.164.105', 'aa:bb:cc:dd:a4:69');
INSERT INTO public.addresses VALUES (4630, NULL, '172.27.164.106', 'aa:bb:cc:dd:a4:6a');
INSERT INTO public.addresses VALUES (4631, NULL, '172.27.164.107', 'aa:bb:cc:dd:a4:6b');
INSERT INTO public.addresses VALUES (4632, NULL, '172.27.164.108', 'aa:bb:cc:dd:a4:6c');
INSERT INTO public.addresses VALUES (4633, NULL, '172.27.164.109', 'aa:bb:cc:dd:a4:6d');
INSERT INTO public.addresses VALUES (4634, NULL, '172.27.164.110', 'aa:bb:cc:dd:a4:6e');
INSERT INTO public.addresses VALUES (4635, NULL, '172.27.164.111', 'aa:bb:cc:dd:a4:6f');
INSERT INTO public.addresses VALUES (4636, NULL, '172.27.164.112', 'aa:bb:cc:dd:a4:70');
INSERT INTO public.addresses VALUES (4637, NULL, '172.27.164.113', 'aa:bb:cc:dd:a4:71');
INSERT INTO public.addresses VALUES (4638, NULL, '172.27.164.114', 'aa:bb:cc:dd:a4:72');
INSERT INTO public.addresses VALUES (4639, NULL, '172.27.164.115', 'aa:bb:cc:dd:a4:73');
INSERT INTO public.addresses VALUES (4640, NULL, '172.27.164.116', 'aa:bb:cc:dd:a4:74');
INSERT INTO public.addresses VALUES (4641, NULL, '172.27.164.117', 'aa:bb:cc:dd:a4:75');
INSERT INTO public.addresses VALUES (4642, NULL, '172.27.164.118', 'aa:bb:cc:dd:a4:76');
INSERT INTO public.addresses VALUES (4643, NULL, '172.27.164.119', 'aa:bb:cc:dd:a4:77');
INSERT INTO public.addresses VALUES (4644, NULL, '172.27.164.120', 'aa:bb:cc:dd:a4:78');
INSERT INTO public.addresses VALUES (4645, NULL, '172.27.164.121', 'aa:bb:cc:dd:a4:79');
INSERT INTO public.addresses VALUES (4646, NULL, '172.27.164.122', 'aa:bb:cc:dd:a4:7a');
INSERT INTO public.addresses VALUES (4647, NULL, '172.27.164.123', 'aa:bb:cc:dd:a4:7b');
INSERT INTO public.addresses VALUES (4648, NULL, '172.27.164.124', 'aa:bb:cc:dd:a4:7c');
INSERT INTO public.addresses VALUES (4649, NULL, '172.27.164.125', 'aa:bb:cc:dd:a4:7d');
INSERT INTO public.addresses VALUES (4650, NULL, '172.27.164.126', 'aa:bb:cc:dd:a4:7e');
INSERT INTO public.addresses VALUES (4651, NULL, '172.27.164.127', 'aa:bb:cc:dd:a4:7f');
INSERT INTO public.addresses VALUES (4652, NULL, '172.27.164.128', 'aa:bb:cc:dd:a4:80');
INSERT INTO public.addresses VALUES (4653, NULL, '172.27.164.129', 'aa:bb:cc:dd:a4:81');
INSERT INTO public.addresses VALUES (4654, NULL, '172.27.164.130', 'aa:bb:cc:dd:a4:82');
INSERT INTO public.addresses VALUES (4655, NULL, '172.27.164.131', 'aa:bb:cc:dd:a4:83');
INSERT INTO public.addresses VALUES (4656, NULL, '172.27.164.132', 'aa:bb:cc:dd:a4:84');
INSERT INTO public.addresses VALUES (4657, NULL, '172.27.164.133', 'aa:bb:cc:dd:a4:85');
INSERT INTO public.addresses VALUES (4658, NULL, '172.27.164.134', 'aa:bb:cc:dd:a4:86');
INSERT INTO public.addresses VALUES (4659, NULL, '172.27.164.135', 'aa:bb:cc:dd:a4:87');
INSERT INTO public.addresses VALUES (4660, NULL, '172.27.164.136', 'aa:bb:cc:dd:a4:88');
INSERT INTO public.addresses VALUES (4661, NULL, '172.27.164.137', 'aa:bb:cc:dd:a4:89');
INSERT INTO public.addresses VALUES (4662, NULL, '172.27.164.138', 'aa:bb:cc:dd:a4:8a');
INSERT INTO public.addresses VALUES (4663, NULL, '172.27.164.139', 'aa:bb:cc:dd:a4:8b');
INSERT INTO public.addresses VALUES (4664, NULL, '172.27.164.140', 'aa:bb:cc:dd:a4:8c');
INSERT INTO public.addresses VALUES (4665, NULL, '172.27.164.141', 'aa:bb:cc:dd:a4:8d');
INSERT INTO public.addresses VALUES (4666, NULL, '172.27.164.142', 'aa:bb:cc:dd:a4:8e');
INSERT INTO public.addresses VALUES (4667, NULL, '172.27.164.143', 'aa:bb:cc:dd:a4:8f');
INSERT INTO public.addresses VALUES (4668, NULL, '172.27.164.144', 'aa:bb:cc:dd:a4:90');
INSERT INTO public.addresses VALUES (4669, NULL, '172.27.164.145', 'aa:bb:cc:dd:a4:91');
INSERT INTO public.addresses VALUES (4670, NULL, '172.27.164.146', 'aa:bb:cc:dd:a4:92');
INSERT INTO public.addresses VALUES (4671, NULL, '172.27.164.147', 'aa:bb:cc:dd:a4:93');
INSERT INTO public.addresses VALUES (4672, NULL, '172.27.164.148', 'aa:bb:cc:dd:a4:94');
INSERT INTO public.addresses VALUES (4673, NULL, '172.27.164.149', 'aa:bb:cc:dd:a4:95');
INSERT INTO public.addresses VALUES (4674, NULL, '172.27.164.150', 'aa:bb:cc:dd:a4:96');
INSERT INTO public.addresses VALUES (4675, NULL, '172.27.164.151', 'aa:bb:cc:dd:a4:97');
INSERT INTO public.addresses VALUES (4676, NULL, '172.27.164.152', 'aa:bb:cc:dd:a4:98');
INSERT INTO public.addresses VALUES (4677, NULL, '172.27.164.153', 'aa:bb:cc:dd:a4:99');
INSERT INTO public.addresses VALUES (4678, NULL, '172.27.164.154', 'aa:bb:cc:dd:a4:9a');
INSERT INTO public.addresses VALUES (4679, NULL, '172.27.164.155', 'aa:bb:cc:dd:a4:9b');
INSERT INTO public.addresses VALUES (4680, NULL, '172.27.164.156', 'aa:bb:cc:dd:a4:9c');
INSERT INTO public.addresses VALUES (4681, NULL, '172.27.164.157', 'aa:bb:cc:dd:a4:9d');
INSERT INTO public.addresses VALUES (4682, NULL, '172.27.164.158', 'aa:bb:cc:dd:a4:9e');
INSERT INTO public.addresses VALUES (4683, NULL, '172.27.164.159', 'aa:bb:cc:dd:a4:9f');
INSERT INTO public.addresses VALUES (4684, NULL, '172.27.164.160', 'aa:bb:cc:dd:a4:a0');
INSERT INTO public.addresses VALUES (4685, NULL, '172.27.164.161', 'aa:bb:cc:dd:a4:a1');
INSERT INTO public.addresses VALUES (4686, NULL, '172.27.164.162', 'aa:bb:cc:dd:a4:a2');
INSERT INTO public.addresses VALUES (4687, NULL, '172.27.164.163', 'aa:bb:cc:dd:a4:a3');
INSERT INTO public.addresses VALUES (4688, NULL, '172.27.164.164', 'aa:bb:cc:dd:a4:a4');
INSERT INTO public.addresses VALUES (4689, NULL, '172.27.164.165', 'aa:bb:cc:dd:a4:a5');
INSERT INTO public.addresses VALUES (4690, NULL, '172.27.164.166', 'aa:bb:cc:dd:a4:a6');
INSERT INTO public.addresses VALUES (4691, NULL, '172.27.164.167', 'aa:bb:cc:dd:a4:a7');
INSERT INTO public.addresses VALUES (4692, NULL, '172.27.164.168', 'aa:bb:cc:dd:a4:a8');
INSERT INTO public.addresses VALUES (4693, NULL, '172.27.164.169', 'aa:bb:cc:dd:a4:a9');
INSERT INTO public.addresses VALUES (4694, NULL, '172.27.164.170', 'aa:bb:cc:dd:a4:aa');
INSERT INTO public.addresses VALUES (4695, NULL, '172.27.164.171', 'aa:bb:cc:dd:a4:ab');
INSERT INTO public.addresses VALUES (4696, NULL, '172.27.164.172', 'aa:bb:cc:dd:a4:ac');
INSERT INTO public.addresses VALUES (4697, NULL, '172.27.164.173', 'aa:bb:cc:dd:a4:ad');
INSERT INTO public.addresses VALUES (4698, NULL, '172.27.164.174', 'aa:bb:cc:dd:a4:ae');
INSERT INTO public.addresses VALUES (4699, NULL, '172.27.164.175', 'aa:bb:cc:dd:a4:af');
INSERT INTO public.addresses VALUES (4700, NULL, '172.27.164.176', 'aa:bb:cc:dd:a4:b0');
INSERT INTO public.addresses VALUES (4701, NULL, '172.27.164.177', 'aa:bb:cc:dd:a4:b1');
INSERT INTO public.addresses VALUES (4702, NULL, '172.27.164.178', 'aa:bb:cc:dd:a4:b2');
INSERT INTO public.addresses VALUES (4703, NULL, '172.27.164.179', 'aa:bb:cc:dd:a4:b3');
INSERT INTO public.addresses VALUES (4704, NULL, '172.27.164.180', 'aa:bb:cc:dd:a4:b4');
INSERT INTO public.addresses VALUES (4705, NULL, '172.27.164.181', 'aa:bb:cc:dd:a4:b5');
INSERT INTO public.addresses VALUES (4706, NULL, '172.27.164.182', 'aa:bb:cc:dd:a4:b6');
INSERT INTO public.addresses VALUES (4707, NULL, '172.27.164.183', 'aa:bb:cc:dd:a4:b7');
INSERT INTO public.addresses VALUES (4708, NULL, '172.27.164.184', 'aa:bb:cc:dd:a4:b8');
INSERT INTO public.addresses VALUES (4709, NULL, '172.27.164.185', 'aa:bb:cc:dd:a4:b9');
INSERT INTO public.addresses VALUES (4710, NULL, '172.27.164.186', 'aa:bb:cc:dd:a4:ba');
INSERT INTO public.addresses VALUES (4711, NULL, '172.27.164.187', 'aa:bb:cc:dd:a4:bb');
INSERT INTO public.addresses VALUES (4712, NULL, '172.27.164.188', 'aa:bb:cc:dd:a4:bc');
INSERT INTO public.addresses VALUES (4713, NULL, '172.27.164.189', 'aa:bb:cc:dd:a4:bd');
INSERT INTO public.addresses VALUES (4714, NULL, '172.27.164.190', 'aa:bb:cc:dd:a4:be');
INSERT INTO public.addresses VALUES (4715, NULL, '172.27.164.191', 'aa:bb:cc:dd:a4:bf');
INSERT INTO public.addresses VALUES (4716, NULL, '172.27.164.192', 'aa:bb:cc:dd:a4:c0');
INSERT INTO public.addresses VALUES (4717, NULL, '172.27.164.193', 'aa:bb:cc:dd:a4:c1');
INSERT INTO public.addresses VALUES (4718, NULL, '172.27.164.194', 'aa:bb:cc:dd:a4:c2');
INSERT INTO public.addresses VALUES (4719, NULL, '172.27.164.195', 'aa:bb:cc:dd:a4:c3');
INSERT INTO public.addresses VALUES (4720, NULL, '172.27.164.196', 'aa:bb:cc:dd:a4:c4');
INSERT INTO public.addresses VALUES (4721, NULL, '172.27.164.197', 'aa:bb:cc:dd:a4:c5');
INSERT INTO public.addresses VALUES (4722, NULL, '172.27.164.198', 'aa:bb:cc:dd:a4:c6');
INSERT INTO public.addresses VALUES (4723, NULL, '172.27.164.199', 'aa:bb:cc:dd:a4:c7');
INSERT INTO public.addresses VALUES (4724, NULL, '172.27.164.200', 'aa:bb:cc:dd:a4:c8');
INSERT INTO public.addresses VALUES (4725, NULL, '172.27.164.201', 'aa:bb:cc:dd:a4:c9');
INSERT INTO public.addresses VALUES (4726, NULL, '172.27.164.202', 'aa:bb:cc:dd:a4:ca');
INSERT INTO public.addresses VALUES (4727, NULL, '172.27.164.203', 'aa:bb:cc:dd:a4:cb');
INSERT INTO public.addresses VALUES (4728, NULL, '172.27.164.204', 'aa:bb:cc:dd:a4:cc');
INSERT INTO public.addresses VALUES (4729, NULL, '172.27.164.205', 'aa:bb:cc:dd:a4:cd');
INSERT INTO public.addresses VALUES (4730, NULL, '172.27.164.206', 'aa:bb:cc:dd:a4:ce');
INSERT INTO public.addresses VALUES (4731, NULL, '172.27.164.207', 'aa:bb:cc:dd:a4:cf');
INSERT INTO public.addresses VALUES (4732, NULL, '172.27.164.208', 'aa:bb:cc:dd:a4:d0');
INSERT INTO public.addresses VALUES (4733, NULL, '172.27.164.209', 'aa:bb:cc:dd:a4:d1');
INSERT INTO public.addresses VALUES (4734, NULL, '172.27.164.210', 'aa:bb:cc:dd:a4:d2');
INSERT INTO public.addresses VALUES (4735, NULL, '172.27.164.211', 'aa:bb:cc:dd:a4:d3');
INSERT INTO public.addresses VALUES (4736, NULL, '172.27.164.212', 'aa:bb:cc:dd:a4:d4');
INSERT INTO public.addresses VALUES (4737, NULL, '172.27.164.213', 'aa:bb:cc:dd:a4:d5');
INSERT INTO public.addresses VALUES (4738, NULL, '172.27.164.214', 'aa:bb:cc:dd:a4:d6');
INSERT INTO public.addresses VALUES (4739, NULL, '172.27.164.215', 'aa:bb:cc:dd:a4:d7');
INSERT INTO public.addresses VALUES (4740, NULL, '172.27.164.216', 'aa:bb:cc:dd:a4:d8');
INSERT INTO public.addresses VALUES (4741, NULL, '172.27.164.217', 'aa:bb:cc:dd:a4:d9');
INSERT INTO public.addresses VALUES (4742, NULL, '172.27.164.218', 'aa:bb:cc:dd:a4:da');
INSERT INTO public.addresses VALUES (4743, NULL, '172.27.164.219', 'aa:bb:cc:dd:a4:db');
INSERT INTO public.addresses VALUES (4744, NULL, '172.27.164.220', 'aa:bb:cc:dd:a4:dc');
INSERT INTO public.addresses VALUES (4745, NULL, '172.27.164.221', 'aa:bb:cc:dd:a4:dd');
INSERT INTO public.addresses VALUES (4746, NULL, '172.27.164.222', 'aa:bb:cc:dd:a4:de');
INSERT INTO public.addresses VALUES (4747, NULL, '172.27.164.223', 'aa:bb:cc:dd:a4:df');
INSERT INTO public.addresses VALUES (4748, NULL, '172.27.164.224', 'aa:bb:cc:dd:a4:e0');
INSERT INTO public.addresses VALUES (4749, NULL, '172.27.164.225', 'aa:bb:cc:dd:a4:e1');
INSERT INTO public.addresses VALUES (4750, NULL, '172.27.164.226', 'aa:bb:cc:dd:a4:e2');
INSERT INTO public.addresses VALUES (4751, NULL, '172.27.164.227', 'aa:bb:cc:dd:a4:e3');
INSERT INTO public.addresses VALUES (4752, NULL, '172.27.164.228', 'aa:bb:cc:dd:a4:e4');
INSERT INTO public.addresses VALUES (4753, NULL, '172.27.164.229', 'aa:bb:cc:dd:a4:e5');
INSERT INTO public.addresses VALUES (4754, NULL, '172.27.164.230', 'aa:bb:cc:dd:a4:e6');
INSERT INTO public.addresses VALUES (4755, NULL, '172.27.164.231', 'aa:bb:cc:dd:a4:e7');
INSERT INTO public.addresses VALUES (4756, NULL, '172.27.164.232', 'aa:bb:cc:dd:a4:e8');
INSERT INTO public.addresses VALUES (4757, NULL, '172.27.164.233', 'aa:bb:cc:dd:a4:e9');
INSERT INTO public.addresses VALUES (4758, NULL, '172.27.164.234', 'aa:bb:cc:dd:a4:ea');
INSERT INTO public.addresses VALUES (4759, NULL, '172.27.164.235', 'aa:bb:cc:dd:a4:eb');
INSERT INTO public.addresses VALUES (4760, NULL, '172.27.164.236', 'aa:bb:cc:dd:a4:ec');
INSERT INTO public.addresses VALUES (4761, NULL, '172.27.164.237', 'aa:bb:cc:dd:a4:ed');
INSERT INTO public.addresses VALUES (4762, NULL, '172.27.164.238', 'aa:bb:cc:dd:a4:ee');
INSERT INTO public.addresses VALUES (4763, NULL, '172.27.164.239', 'aa:bb:cc:dd:a4:ef');
INSERT INTO public.addresses VALUES (4764, NULL, '172.27.164.240', 'aa:bb:cc:dd:a4:f0');
INSERT INTO public.addresses VALUES (4765, NULL, '172.27.164.241', 'aa:bb:cc:dd:a4:f1');
INSERT INTO public.addresses VALUES (4766, NULL, '172.27.164.242', 'aa:bb:cc:dd:a4:f2');
INSERT INTO public.addresses VALUES (4767, NULL, '172.27.164.243', 'aa:bb:cc:dd:a4:f3');
INSERT INTO public.addresses VALUES (4768, NULL, '172.27.164.244', 'aa:bb:cc:dd:a4:f4');
INSERT INTO public.addresses VALUES (4769, NULL, '172.27.164.245', 'aa:bb:cc:dd:a4:f5');
INSERT INTO public.addresses VALUES (4770, NULL, '172.27.164.246', 'aa:bb:cc:dd:a4:f6');
INSERT INTO public.addresses VALUES (4771, NULL, '172.27.164.247', 'aa:bb:cc:dd:a4:f7');
INSERT INTO public.addresses VALUES (4772, NULL, '172.27.164.248', 'aa:bb:cc:dd:a4:f8');
INSERT INTO public.addresses VALUES (4773, NULL, '172.27.164.249', 'aa:bb:cc:dd:a4:f9');
INSERT INTO public.addresses VALUES (4774, NULL, '172.27.164.250', 'aa:bb:cc:dd:a4:fa');
INSERT INTO public.addresses VALUES (4775, NULL, '172.27.164.251', 'aa:bb:cc:dd:a4:fb');
INSERT INTO public.addresses VALUES (4776, NULL, '172.27.164.252', 'aa:bb:cc:dd:a4:fc');
INSERT INTO public.addresses VALUES (4777, NULL, '172.27.164.253', 'aa:bb:cc:dd:a4:fd');
INSERT INTO public.addresses VALUES (4778, NULL, '172.27.164.254', 'aa:bb:cc:dd:a4:fe');
INSERT INTO public.addresses VALUES (4779, NULL, '172.27.164.255', 'aa:bb:cc:dd:a4:ff');
INSERT INTO public.addresses VALUES (4780, NULL, '172.27.165.0', 'aa:bb:cc:dd:a5:00');
INSERT INTO public.addresses VALUES (4781, NULL, '172.27.165.1', 'aa:bb:cc:dd:a5:01');
INSERT INTO public.addresses VALUES (4782, NULL, '172.27.165.2', 'aa:bb:cc:dd:a5:02');
INSERT INTO public.addresses VALUES (4783, NULL, '172.27.165.3', 'aa:bb:cc:dd:a5:03');
INSERT INTO public.addresses VALUES (4784, NULL, '172.27.165.4', 'aa:bb:cc:dd:a5:04');
INSERT INTO public.addresses VALUES (4785, NULL, '172.27.165.5', 'aa:bb:cc:dd:a5:05');
INSERT INTO public.addresses VALUES (4786, NULL, '172.27.165.6', 'aa:bb:cc:dd:a5:06');
INSERT INTO public.addresses VALUES (4787, NULL, '172.27.165.7', 'aa:bb:cc:dd:a5:07');
INSERT INTO public.addresses VALUES (4788, NULL, '172.27.165.8', 'aa:bb:cc:dd:a5:08');
INSERT INTO public.addresses VALUES (4789, NULL, '172.27.165.9', 'aa:bb:cc:dd:a5:09');
INSERT INTO public.addresses VALUES (4790, NULL, '172.27.165.10', 'aa:bb:cc:dd:a5:0a');
INSERT INTO public.addresses VALUES (4791, NULL, '172.27.165.11', 'aa:bb:cc:dd:a5:0b');
INSERT INTO public.addresses VALUES (4792, NULL, '172.27.165.12', 'aa:bb:cc:dd:a5:0c');
INSERT INTO public.addresses VALUES (4793, NULL, '172.27.165.13', 'aa:bb:cc:dd:a5:0d');
INSERT INTO public.addresses VALUES (4794, NULL, '172.27.165.14', 'aa:bb:cc:dd:a5:0e');
INSERT INTO public.addresses VALUES (4795, NULL, '172.27.165.15', 'aa:bb:cc:dd:a5:0f');
INSERT INTO public.addresses VALUES (4796, NULL, '172.27.165.16', 'aa:bb:cc:dd:a5:10');
INSERT INTO public.addresses VALUES (4797, NULL, '172.27.165.17', 'aa:bb:cc:dd:a5:11');
INSERT INTO public.addresses VALUES (4798, NULL, '172.27.165.18', 'aa:bb:cc:dd:a5:12');
INSERT INTO public.addresses VALUES (4799, NULL, '172.27.165.19', 'aa:bb:cc:dd:a5:13');
INSERT INTO public.addresses VALUES (4800, NULL, '172.27.165.20', 'aa:bb:cc:dd:a5:14');
INSERT INTO public.addresses VALUES (4801, NULL, '172.27.165.21', 'aa:bb:cc:dd:a5:15');
INSERT INTO public.addresses VALUES (4802, NULL, '172.27.165.22', 'aa:bb:cc:dd:a5:16');
INSERT INTO public.addresses VALUES (4803, NULL, '172.27.165.23', 'aa:bb:cc:dd:a5:17');
INSERT INTO public.addresses VALUES (4804, NULL, '172.27.165.24', 'aa:bb:cc:dd:a5:18');
INSERT INTO public.addresses VALUES (4805, NULL, '172.27.165.25', 'aa:bb:cc:dd:a5:19');
INSERT INTO public.addresses VALUES (4806, NULL, '172.27.165.26', 'aa:bb:cc:dd:a5:1a');
INSERT INTO public.addresses VALUES (4807, NULL, '172.27.165.27', 'aa:bb:cc:dd:a5:1b');
INSERT INTO public.addresses VALUES (4808, NULL, '172.27.165.28', 'aa:bb:cc:dd:a5:1c');
INSERT INTO public.addresses VALUES (4809, NULL, '172.27.165.29', 'aa:bb:cc:dd:a5:1d');
INSERT INTO public.addresses VALUES (4810, NULL, '172.27.165.30', 'aa:bb:cc:dd:a5:1e');
INSERT INTO public.addresses VALUES (4811, NULL, '172.27.165.31', 'aa:bb:cc:dd:a5:1f');
INSERT INTO public.addresses VALUES (4812, NULL, '172.27.165.32', 'aa:bb:cc:dd:a5:20');
INSERT INTO public.addresses VALUES (4813, NULL, '172.27.165.33', 'aa:bb:cc:dd:a5:21');
INSERT INTO public.addresses VALUES (4814, NULL, '172.27.165.34', 'aa:bb:cc:dd:a5:22');
INSERT INTO public.addresses VALUES (4815, NULL, '172.27.165.35', 'aa:bb:cc:dd:a5:23');
INSERT INTO public.addresses VALUES (4816, NULL, '172.27.165.36', 'aa:bb:cc:dd:a5:24');
INSERT INTO public.addresses VALUES (4817, NULL, '172.27.165.37', 'aa:bb:cc:dd:a5:25');
INSERT INTO public.addresses VALUES (4818, NULL, '172.27.165.38', 'aa:bb:cc:dd:a5:26');
INSERT INTO public.addresses VALUES (4819, NULL, '172.27.165.39', 'aa:bb:cc:dd:a5:27');
INSERT INTO public.addresses VALUES (4820, NULL, '172.27.165.40', 'aa:bb:cc:dd:a5:28');
INSERT INTO public.addresses VALUES (4821, NULL, '172.27.165.41', 'aa:bb:cc:dd:a5:29');
INSERT INTO public.addresses VALUES (4822, NULL, '172.27.165.42', 'aa:bb:cc:dd:a5:2a');
INSERT INTO public.addresses VALUES (4823, NULL, '172.27.165.43', 'aa:bb:cc:dd:a5:2b');
INSERT INTO public.addresses VALUES (4824, NULL, '172.27.165.44', 'aa:bb:cc:dd:a5:2c');
INSERT INTO public.addresses VALUES (4825, NULL, '172.27.165.45', 'aa:bb:cc:dd:a5:2d');
INSERT INTO public.addresses VALUES (4826, NULL, '172.27.165.46', 'aa:bb:cc:dd:a5:2e');
INSERT INTO public.addresses VALUES (4827, NULL, '172.27.165.47', 'aa:bb:cc:dd:a5:2f');
INSERT INTO public.addresses VALUES (4828, NULL, '172.27.165.48', 'aa:bb:cc:dd:a5:30');
INSERT INTO public.addresses VALUES (4829, NULL, '172.27.165.49', 'aa:bb:cc:dd:a5:31');
INSERT INTO public.addresses VALUES (4830, NULL, '172.27.165.50', 'aa:bb:cc:dd:a5:32');
INSERT INTO public.addresses VALUES (4831, NULL, '172.27.165.51', 'aa:bb:cc:dd:a5:33');
INSERT INTO public.addresses VALUES (4832, NULL, '172.27.165.52', 'aa:bb:cc:dd:a5:34');
INSERT INTO public.addresses VALUES (4833, NULL, '172.27.165.53', 'aa:bb:cc:dd:a5:35');
INSERT INTO public.addresses VALUES (4834, NULL, '172.27.165.54', 'aa:bb:cc:dd:a5:36');
INSERT INTO public.addresses VALUES (4835, NULL, '172.27.165.55', 'aa:bb:cc:dd:a5:37');
INSERT INTO public.addresses VALUES (4836, NULL, '172.27.165.56', 'aa:bb:cc:dd:a5:38');
INSERT INTO public.addresses VALUES (4837, NULL, '172.27.165.57', 'aa:bb:cc:dd:a5:39');
INSERT INTO public.addresses VALUES (4838, NULL, '172.27.165.58', 'aa:bb:cc:dd:a5:3a');
INSERT INTO public.addresses VALUES (4839, NULL, '172.27.165.59', 'aa:bb:cc:dd:a5:3b');
INSERT INTO public.addresses VALUES (4840, NULL, '172.27.165.60', 'aa:bb:cc:dd:a5:3c');
INSERT INTO public.addresses VALUES (4841, NULL, '172.27.165.61', 'aa:bb:cc:dd:a5:3d');
INSERT INTO public.addresses VALUES (4842, NULL, '172.27.165.62', 'aa:bb:cc:dd:a5:3e');
INSERT INTO public.addresses VALUES (4843, NULL, '172.27.165.63', 'aa:bb:cc:dd:a5:3f');
INSERT INTO public.addresses VALUES (4844, NULL, '172.27.165.64', 'aa:bb:cc:dd:a5:40');
INSERT INTO public.addresses VALUES (4845, NULL, '172.27.165.65', 'aa:bb:cc:dd:a5:41');
INSERT INTO public.addresses VALUES (4846, NULL, '172.27.165.66', 'aa:bb:cc:dd:a5:42');
INSERT INTO public.addresses VALUES (4847, NULL, '172.27.165.67', 'aa:bb:cc:dd:a5:43');
INSERT INTO public.addresses VALUES (4848, NULL, '172.27.165.68', 'aa:bb:cc:dd:a5:44');
INSERT INTO public.addresses VALUES (4849, NULL, '172.27.165.69', 'aa:bb:cc:dd:a5:45');
INSERT INTO public.addresses VALUES (4850, NULL, '172.27.165.70', 'aa:bb:cc:dd:a5:46');
INSERT INTO public.addresses VALUES (4851, NULL, '172.27.165.71', 'aa:bb:cc:dd:a5:47');
INSERT INTO public.addresses VALUES (4852, NULL, '172.27.165.72', 'aa:bb:cc:dd:a5:48');
INSERT INTO public.addresses VALUES (4853, NULL, '172.27.165.73', 'aa:bb:cc:dd:a5:49');
INSERT INTO public.addresses VALUES (4854, NULL, '172.27.165.74', 'aa:bb:cc:dd:a5:4a');
INSERT INTO public.addresses VALUES (4855, NULL, '172.27.165.75', 'aa:bb:cc:dd:a5:4b');
INSERT INTO public.addresses VALUES (4856, NULL, '172.27.165.76', 'aa:bb:cc:dd:a5:4c');
INSERT INTO public.addresses VALUES (4857, NULL, '172.27.165.77', 'aa:bb:cc:dd:a5:4d');
INSERT INTO public.addresses VALUES (4858, NULL, '172.27.165.78', 'aa:bb:cc:dd:a5:4e');
INSERT INTO public.addresses VALUES (4859, NULL, '172.27.165.79', 'aa:bb:cc:dd:a5:4f');
INSERT INTO public.addresses VALUES (4860, NULL, '172.27.165.80', 'aa:bb:cc:dd:a5:50');
INSERT INTO public.addresses VALUES (4861, NULL, '172.27.165.81', 'aa:bb:cc:dd:a5:51');
INSERT INTO public.addresses VALUES (4862, NULL, '172.27.165.82', 'aa:bb:cc:dd:a5:52');
INSERT INTO public.addresses VALUES (4863, NULL, '172.27.165.83', 'aa:bb:cc:dd:a5:53');
INSERT INTO public.addresses VALUES (4864, NULL, '172.27.165.84', 'aa:bb:cc:dd:a5:54');
INSERT INTO public.addresses VALUES (4865, NULL, '172.27.165.85', 'aa:bb:cc:dd:a5:55');
INSERT INTO public.addresses VALUES (4866, NULL, '172.27.165.86', 'aa:bb:cc:dd:a5:56');
INSERT INTO public.addresses VALUES (4867, NULL, '172.27.165.87', 'aa:bb:cc:dd:a5:57');
INSERT INTO public.addresses VALUES (4868, NULL, '172.27.165.88', 'aa:bb:cc:dd:a5:58');
INSERT INTO public.addresses VALUES (4869, NULL, '172.27.165.89', 'aa:bb:cc:dd:a5:59');
INSERT INTO public.addresses VALUES (4870, NULL, '172.27.165.90', 'aa:bb:cc:dd:a5:5a');
INSERT INTO public.addresses VALUES (4871, NULL, '172.27.165.91', 'aa:bb:cc:dd:a5:5b');
INSERT INTO public.addresses VALUES (4872, NULL, '172.27.165.92', 'aa:bb:cc:dd:a5:5c');
INSERT INTO public.addresses VALUES (4873, NULL, '172.27.165.93', 'aa:bb:cc:dd:a5:5d');
INSERT INTO public.addresses VALUES (4874, NULL, '172.27.165.94', 'aa:bb:cc:dd:a5:5e');
INSERT INTO public.addresses VALUES (4875, NULL, '172.27.165.95', 'aa:bb:cc:dd:a5:5f');
INSERT INTO public.addresses VALUES (4876, NULL, '172.27.165.96', 'aa:bb:cc:dd:a5:60');
INSERT INTO public.addresses VALUES (4877, NULL, '172.27.165.97', 'aa:bb:cc:dd:a5:61');
INSERT INTO public.addresses VALUES (4878, NULL, '172.27.165.98', 'aa:bb:cc:dd:a5:62');
INSERT INTO public.addresses VALUES (4879, NULL, '172.27.165.99', 'aa:bb:cc:dd:a5:63');
INSERT INTO public.addresses VALUES (4880, NULL, '172.27.165.100', 'aa:bb:cc:dd:a5:64');
INSERT INTO public.addresses VALUES (4881, NULL, '172.27.165.101', 'aa:bb:cc:dd:a5:65');
INSERT INTO public.addresses VALUES (4882, NULL, '172.27.165.102', 'aa:bb:cc:dd:a5:66');
INSERT INTO public.addresses VALUES (4883, NULL, '172.27.165.103', 'aa:bb:cc:dd:a5:67');
INSERT INTO public.addresses VALUES (4884, NULL, '172.27.165.104', 'aa:bb:cc:dd:a5:68');
INSERT INTO public.addresses VALUES (4885, NULL, '172.27.165.105', 'aa:bb:cc:dd:a5:69');
INSERT INTO public.addresses VALUES (4886, NULL, '172.27.165.106', 'aa:bb:cc:dd:a5:6a');
INSERT INTO public.addresses VALUES (4887, NULL, '172.27.165.107', 'aa:bb:cc:dd:a5:6b');
INSERT INTO public.addresses VALUES (4888, NULL, '172.27.165.108', 'aa:bb:cc:dd:a5:6c');
INSERT INTO public.addresses VALUES (4889, NULL, '172.27.165.109', 'aa:bb:cc:dd:a5:6d');
INSERT INTO public.addresses VALUES (4890, NULL, '172.27.165.110', 'aa:bb:cc:dd:a5:6e');
INSERT INTO public.addresses VALUES (4891, NULL, '172.27.165.111', 'aa:bb:cc:dd:a5:6f');
INSERT INTO public.addresses VALUES (4892, NULL, '172.27.165.112', 'aa:bb:cc:dd:a5:70');
INSERT INTO public.addresses VALUES (4893, NULL, '172.27.165.113', 'aa:bb:cc:dd:a5:71');
INSERT INTO public.addresses VALUES (4894, NULL, '172.27.165.114', 'aa:bb:cc:dd:a5:72');
INSERT INTO public.addresses VALUES (4895, NULL, '172.27.165.115', 'aa:bb:cc:dd:a5:73');
INSERT INTO public.addresses VALUES (4896, NULL, '172.27.165.116', 'aa:bb:cc:dd:a5:74');
INSERT INTO public.addresses VALUES (4897, NULL, '172.27.165.117', 'aa:bb:cc:dd:a5:75');
INSERT INTO public.addresses VALUES (4898, NULL, '172.27.165.118', 'aa:bb:cc:dd:a5:76');
INSERT INTO public.addresses VALUES (4899, NULL, '172.27.165.119', 'aa:bb:cc:dd:a5:77');
INSERT INTO public.addresses VALUES (4900, NULL, '172.27.165.120', 'aa:bb:cc:dd:a5:78');
INSERT INTO public.addresses VALUES (4901, NULL, '172.27.165.121', 'aa:bb:cc:dd:a5:79');
INSERT INTO public.addresses VALUES (4902, NULL, '172.27.165.122', 'aa:bb:cc:dd:a5:7a');
INSERT INTO public.addresses VALUES (4903, NULL, '172.27.165.123', 'aa:bb:cc:dd:a5:7b');
INSERT INTO public.addresses VALUES (4904, NULL, '172.27.165.124', 'aa:bb:cc:dd:a5:7c');
INSERT INTO public.addresses VALUES (4905, NULL, '172.27.165.125', 'aa:bb:cc:dd:a5:7d');
INSERT INTO public.addresses VALUES (4906, NULL, '172.27.165.126', 'aa:bb:cc:dd:a5:7e');
INSERT INTO public.addresses VALUES (4907, NULL, '172.27.165.127', 'aa:bb:cc:dd:a5:7f');
INSERT INTO public.addresses VALUES (4908, NULL, '172.27.165.128', 'aa:bb:cc:dd:a5:80');
INSERT INTO public.addresses VALUES (4909, NULL, '172.27.165.129', 'aa:bb:cc:dd:a5:81');
INSERT INTO public.addresses VALUES (4910, NULL, '172.27.165.130', 'aa:bb:cc:dd:a5:82');
INSERT INTO public.addresses VALUES (4911, NULL, '172.27.165.131', 'aa:bb:cc:dd:a5:83');
INSERT INTO public.addresses VALUES (4912, NULL, '172.27.165.132', 'aa:bb:cc:dd:a5:84');
INSERT INTO public.addresses VALUES (4913, NULL, '172.27.165.133', 'aa:bb:cc:dd:a5:85');
INSERT INTO public.addresses VALUES (4914, NULL, '172.27.165.134', 'aa:bb:cc:dd:a5:86');
INSERT INTO public.addresses VALUES (4915, NULL, '172.27.165.135', 'aa:bb:cc:dd:a5:87');
INSERT INTO public.addresses VALUES (4916, NULL, '172.27.165.136', 'aa:bb:cc:dd:a5:88');
INSERT INTO public.addresses VALUES (4917, NULL, '172.27.165.137', 'aa:bb:cc:dd:a5:89');
INSERT INTO public.addresses VALUES (4918, NULL, '172.27.165.138', 'aa:bb:cc:dd:a5:8a');
INSERT INTO public.addresses VALUES (4919, NULL, '172.27.165.139', 'aa:bb:cc:dd:a5:8b');
INSERT INTO public.addresses VALUES (4920, NULL, '172.27.165.140', 'aa:bb:cc:dd:a5:8c');
INSERT INTO public.addresses VALUES (4921, NULL, '172.27.165.141', 'aa:bb:cc:dd:a5:8d');
INSERT INTO public.addresses VALUES (4922, NULL, '172.27.165.142', 'aa:bb:cc:dd:a5:8e');
INSERT INTO public.addresses VALUES (4923, NULL, '172.27.165.143', 'aa:bb:cc:dd:a5:8f');
INSERT INTO public.addresses VALUES (4924, NULL, '172.27.165.144', 'aa:bb:cc:dd:a5:90');
INSERT INTO public.addresses VALUES (4925, NULL, '172.27.165.145', 'aa:bb:cc:dd:a5:91');
INSERT INTO public.addresses VALUES (4926, NULL, '172.27.165.146', 'aa:bb:cc:dd:a5:92');
INSERT INTO public.addresses VALUES (4927, NULL, '172.27.165.147', 'aa:bb:cc:dd:a5:93');
INSERT INTO public.addresses VALUES (4928, NULL, '172.27.165.148', 'aa:bb:cc:dd:a5:94');
INSERT INTO public.addresses VALUES (4929, NULL, '172.27.165.149', 'aa:bb:cc:dd:a5:95');
INSERT INTO public.addresses VALUES (4930, NULL, '172.27.165.150', 'aa:bb:cc:dd:a5:96');
INSERT INTO public.addresses VALUES (4931, NULL, '172.27.165.151', 'aa:bb:cc:dd:a5:97');
INSERT INTO public.addresses VALUES (4932, NULL, '172.27.165.152', 'aa:bb:cc:dd:a5:98');
INSERT INTO public.addresses VALUES (4933, NULL, '172.27.165.153', 'aa:bb:cc:dd:a5:99');
INSERT INTO public.addresses VALUES (4934, NULL, '172.27.165.154', 'aa:bb:cc:dd:a5:9a');
INSERT INTO public.addresses VALUES (4935, NULL, '172.27.165.155', 'aa:bb:cc:dd:a5:9b');
INSERT INTO public.addresses VALUES (4936, NULL, '172.27.165.156', 'aa:bb:cc:dd:a5:9c');
INSERT INTO public.addresses VALUES (4937, NULL, '172.27.165.157', 'aa:bb:cc:dd:a5:9d');
INSERT INTO public.addresses VALUES (4938, NULL, '172.27.165.158', 'aa:bb:cc:dd:a5:9e');
INSERT INTO public.addresses VALUES (4939, NULL, '172.27.165.159', 'aa:bb:cc:dd:a5:9f');
INSERT INTO public.addresses VALUES (4940, NULL, '172.27.165.160', 'aa:bb:cc:dd:a5:a0');
INSERT INTO public.addresses VALUES (4941, NULL, '172.27.165.161', 'aa:bb:cc:dd:a5:a1');
INSERT INTO public.addresses VALUES (4942, NULL, '172.27.165.162', 'aa:bb:cc:dd:a5:a2');
INSERT INTO public.addresses VALUES (4943, NULL, '172.27.165.163', 'aa:bb:cc:dd:a5:a3');
INSERT INTO public.addresses VALUES (4944, NULL, '172.27.165.164', 'aa:bb:cc:dd:a5:a4');
INSERT INTO public.addresses VALUES (4945, NULL, '172.27.165.165', 'aa:bb:cc:dd:a5:a5');
INSERT INTO public.addresses VALUES (4946, NULL, '172.27.165.166', 'aa:bb:cc:dd:a5:a6');
INSERT INTO public.addresses VALUES (4947, NULL, '172.27.165.167', 'aa:bb:cc:dd:a5:a7');
INSERT INTO public.addresses VALUES (4948, NULL, '172.27.165.168', 'aa:bb:cc:dd:a5:a8');
INSERT INTO public.addresses VALUES (4949, NULL, '172.27.165.169', 'aa:bb:cc:dd:a5:a9');
INSERT INTO public.addresses VALUES (4950, NULL, '172.27.165.170', 'aa:bb:cc:dd:a5:aa');
INSERT INTO public.addresses VALUES (4951, NULL, '172.27.165.171', 'aa:bb:cc:dd:a5:ab');
INSERT INTO public.addresses VALUES (4952, NULL, '172.27.165.172', 'aa:bb:cc:dd:a5:ac');
INSERT INTO public.addresses VALUES (4953, NULL, '172.27.165.173', 'aa:bb:cc:dd:a5:ad');
INSERT INTO public.addresses VALUES (4954, NULL, '172.27.165.174', 'aa:bb:cc:dd:a5:ae');
INSERT INTO public.addresses VALUES (4955, NULL, '172.27.165.175', 'aa:bb:cc:dd:a5:af');
INSERT INTO public.addresses VALUES (4956, NULL, '172.27.165.176', 'aa:bb:cc:dd:a5:b0');
INSERT INTO public.addresses VALUES (4957, NULL, '172.27.165.177', 'aa:bb:cc:dd:a5:b1');
INSERT INTO public.addresses VALUES (4958, NULL, '172.27.165.178', 'aa:bb:cc:dd:a5:b2');
INSERT INTO public.addresses VALUES (4959, NULL, '172.27.165.179', 'aa:bb:cc:dd:a5:b3');
INSERT INTO public.addresses VALUES (4960, NULL, '172.27.165.180', 'aa:bb:cc:dd:a5:b4');
INSERT INTO public.addresses VALUES (4961, NULL, '172.27.165.181', 'aa:bb:cc:dd:a5:b5');
INSERT INTO public.addresses VALUES (4962, NULL, '172.27.165.182', 'aa:bb:cc:dd:a5:b6');
INSERT INTO public.addresses VALUES (4963, NULL, '172.27.165.183', 'aa:bb:cc:dd:a5:b7');
INSERT INTO public.addresses VALUES (4964, NULL, '172.27.165.184', 'aa:bb:cc:dd:a5:b8');
INSERT INTO public.addresses VALUES (4965, NULL, '172.27.165.185', 'aa:bb:cc:dd:a5:b9');
INSERT INTO public.addresses VALUES (4966, NULL, '172.27.165.186', 'aa:bb:cc:dd:a5:ba');
INSERT INTO public.addresses VALUES (4967, NULL, '172.27.165.187', 'aa:bb:cc:dd:a5:bb');
INSERT INTO public.addresses VALUES (4968, NULL, '172.27.165.188', 'aa:bb:cc:dd:a5:bc');
INSERT INTO public.addresses VALUES (4969, NULL, '172.27.165.189', 'aa:bb:cc:dd:a5:bd');
INSERT INTO public.addresses VALUES (4970, NULL, '172.27.165.190', 'aa:bb:cc:dd:a5:be');
INSERT INTO public.addresses VALUES (4971, NULL, '172.27.165.191', 'aa:bb:cc:dd:a5:bf');
INSERT INTO public.addresses VALUES (4972, NULL, '172.27.165.192', 'aa:bb:cc:dd:a5:c0');
INSERT INTO public.addresses VALUES (4973, NULL, '172.27.165.193', 'aa:bb:cc:dd:a5:c1');
INSERT INTO public.addresses VALUES (4974, NULL, '172.27.165.194', 'aa:bb:cc:dd:a5:c2');
INSERT INTO public.addresses VALUES (4975, NULL, '172.27.165.195', 'aa:bb:cc:dd:a5:c3');
INSERT INTO public.addresses VALUES (4976, NULL, '172.27.165.196', 'aa:bb:cc:dd:a5:c4');
INSERT INTO public.addresses VALUES (4977, NULL, '172.27.165.197', 'aa:bb:cc:dd:a5:c5');
INSERT INTO public.addresses VALUES (4978, NULL, '172.27.165.198', 'aa:bb:cc:dd:a5:c6');
INSERT INTO public.addresses VALUES (4979, NULL, '172.27.165.199', 'aa:bb:cc:dd:a5:c7');
INSERT INTO public.addresses VALUES (4980, NULL, '172.27.165.200', 'aa:bb:cc:dd:a5:c8');
INSERT INTO public.addresses VALUES (4981, NULL, '172.27.165.201', 'aa:bb:cc:dd:a5:c9');
INSERT INTO public.addresses VALUES (4982, NULL, '172.27.165.202', 'aa:bb:cc:dd:a5:ca');
INSERT INTO public.addresses VALUES (4983, NULL, '172.27.165.203', 'aa:bb:cc:dd:a5:cb');
INSERT INTO public.addresses VALUES (4984, NULL, '172.27.165.204', 'aa:bb:cc:dd:a5:cc');
INSERT INTO public.addresses VALUES (4985, NULL, '172.27.165.205', 'aa:bb:cc:dd:a5:cd');
INSERT INTO public.addresses VALUES (4986, NULL, '172.27.165.206', 'aa:bb:cc:dd:a5:ce');
INSERT INTO public.addresses VALUES (4987, NULL, '172.27.165.207', 'aa:bb:cc:dd:a5:cf');
INSERT INTO public.addresses VALUES (4988, NULL, '172.27.165.208', 'aa:bb:cc:dd:a5:d0');
INSERT INTO public.addresses VALUES (4989, NULL, '172.27.165.209', 'aa:bb:cc:dd:a5:d1');
INSERT INTO public.addresses VALUES (4990, NULL, '172.27.165.210', 'aa:bb:cc:dd:a5:d2');
INSERT INTO public.addresses VALUES (4991, NULL, '172.27.165.211', 'aa:bb:cc:dd:a5:d3');
INSERT INTO public.addresses VALUES (4992, NULL, '172.27.165.212', 'aa:bb:cc:dd:a5:d4');
INSERT INTO public.addresses VALUES (4993, NULL, '172.27.165.213', 'aa:bb:cc:dd:a5:d5');
INSERT INTO public.addresses VALUES (4994, NULL, '172.27.165.214', 'aa:bb:cc:dd:a5:d6');
INSERT INTO public.addresses VALUES (4995, NULL, '172.27.165.215', 'aa:bb:cc:dd:a5:d7');
INSERT INTO public.addresses VALUES (4996, NULL, '172.27.165.216', 'aa:bb:cc:dd:a5:d8');
INSERT INTO public.addresses VALUES (4997, NULL, '172.27.165.217', 'aa:bb:cc:dd:a5:d9');
INSERT INTO public.addresses VALUES (4998, NULL, '172.27.165.218', 'aa:bb:cc:dd:a5:da');
INSERT INTO public.addresses VALUES (4999, NULL, '172.27.165.219', 'aa:bb:cc:dd:a5:db');
INSERT INTO public.addresses VALUES (5000, NULL, '172.27.165.220', 'aa:bb:cc:dd:a5:dc');
INSERT INTO public.addresses VALUES (5001, NULL, '172.27.165.221', 'aa:bb:cc:dd:a5:dd');
INSERT INTO public.addresses VALUES (5002, NULL, '172.27.165.222', 'aa:bb:cc:dd:a5:de');
INSERT INTO public.addresses VALUES (5003, NULL, '172.27.165.223', 'aa:bb:cc:dd:a5:df');
INSERT INTO public.addresses VALUES (5004, NULL, '172.27.165.224', 'aa:bb:cc:dd:a5:e0');
INSERT INTO public.addresses VALUES (5005, NULL, '172.27.165.225', 'aa:bb:cc:dd:a5:e1');
INSERT INTO public.addresses VALUES (5006, NULL, '172.27.165.226', 'aa:bb:cc:dd:a5:e2');
INSERT INTO public.addresses VALUES (5007, NULL, '172.27.165.227', 'aa:bb:cc:dd:a5:e3');
INSERT INTO public.addresses VALUES (5008, NULL, '172.27.165.228', 'aa:bb:cc:dd:a5:e4');
INSERT INTO public.addresses VALUES (5009, NULL, '172.27.165.229', 'aa:bb:cc:dd:a5:e5');
INSERT INTO public.addresses VALUES (5010, NULL, '172.27.165.230', 'aa:bb:cc:dd:a5:e6');
INSERT INTO public.addresses VALUES (5011, NULL, '172.27.165.231', 'aa:bb:cc:dd:a5:e7');
INSERT INTO public.addresses VALUES (5012, NULL, '172.27.165.232', 'aa:bb:cc:dd:a5:e8');
INSERT INTO public.addresses VALUES (5013, NULL, '172.27.165.233', 'aa:bb:cc:dd:a5:e9');
INSERT INTO public.addresses VALUES (5014, NULL, '172.27.165.234', 'aa:bb:cc:dd:a5:ea');
INSERT INTO public.addresses VALUES (5015, NULL, '172.27.165.235', 'aa:bb:cc:dd:a5:eb');
INSERT INTO public.addresses VALUES (5016, NULL, '172.27.165.236', 'aa:bb:cc:dd:a5:ec');
INSERT INTO public.addresses VALUES (5017, NULL, '172.27.165.237', 'aa:bb:cc:dd:a5:ed');
INSERT INTO public.addresses VALUES (5018, NULL, '172.27.165.238', 'aa:bb:cc:dd:a5:ee');
INSERT INTO public.addresses VALUES (5019, NULL, '172.27.165.239', 'aa:bb:cc:dd:a5:ef');
INSERT INTO public.addresses VALUES (5020, NULL, '172.27.165.240', 'aa:bb:cc:dd:a5:f0');
INSERT INTO public.addresses VALUES (5021, NULL, '172.27.165.241', 'aa:bb:cc:dd:a5:f1');
INSERT INTO public.addresses VALUES (5022, NULL, '172.27.165.242', 'aa:bb:cc:dd:a5:f2');
INSERT INTO public.addresses VALUES (5023, NULL, '172.27.165.243', 'aa:bb:cc:dd:a5:f3');
INSERT INTO public.addresses VALUES (5024, NULL, '172.27.165.244', 'aa:bb:cc:dd:a5:f4');
INSERT INTO public.addresses VALUES (5025, NULL, '172.27.165.245', 'aa:bb:cc:dd:a5:f5');
INSERT INTO public.addresses VALUES (5026, NULL, '172.27.165.246', 'aa:bb:cc:dd:a5:f6');
INSERT INTO public.addresses VALUES (5027, NULL, '172.27.165.247', 'aa:bb:cc:dd:a5:f7');
INSERT INTO public.addresses VALUES (5028, NULL, '172.27.165.248', 'aa:bb:cc:dd:a5:f8');
INSERT INTO public.addresses VALUES (5029, NULL, '172.27.165.249', 'aa:bb:cc:dd:a5:f9');
INSERT INTO public.addresses VALUES (5030, NULL, '172.27.165.250', 'aa:bb:cc:dd:a5:fa');
INSERT INTO public.addresses VALUES (5031, NULL, '172.27.165.251', 'aa:bb:cc:dd:a5:fb');
INSERT INTO public.addresses VALUES (5032, NULL, '172.27.165.252', 'aa:bb:cc:dd:a5:fc');
INSERT INTO public.addresses VALUES (5033, NULL, '172.27.165.253', 'aa:bb:cc:dd:a5:fd');
INSERT INTO public.addresses VALUES (5034, NULL, '172.27.165.254', 'aa:bb:cc:dd:a5:fe');
INSERT INTO public.addresses VALUES (5035, NULL, '172.27.165.255', 'aa:bb:cc:dd:a5:ff');
INSERT INTO public.addresses VALUES (5036, NULL, '172.27.166.0', 'aa:bb:cc:dd:a6:00');
INSERT INTO public.addresses VALUES (5037, NULL, '172.27.166.1', 'aa:bb:cc:dd:a6:01');
INSERT INTO public.addresses VALUES (5038, NULL, '172.27.166.2', 'aa:bb:cc:dd:a6:02');
INSERT INTO public.addresses VALUES (5039, NULL, '172.27.166.3', 'aa:bb:cc:dd:a6:03');
INSERT INTO public.addresses VALUES (5040, NULL, '172.27.166.4', 'aa:bb:cc:dd:a6:04');
INSERT INTO public.addresses VALUES (5041, NULL, '172.27.166.5', 'aa:bb:cc:dd:a6:05');
INSERT INTO public.addresses VALUES (5042, NULL, '172.27.166.6', 'aa:bb:cc:dd:a6:06');
INSERT INTO public.addresses VALUES (5043, NULL, '172.27.166.7', 'aa:bb:cc:dd:a6:07');
INSERT INTO public.addresses VALUES (5044, NULL, '172.27.166.8', 'aa:bb:cc:dd:a6:08');
INSERT INTO public.addresses VALUES (5045, NULL, '172.27.166.9', 'aa:bb:cc:dd:a6:09');
INSERT INTO public.addresses VALUES (5046, NULL, '172.27.166.10', 'aa:bb:cc:dd:a6:0a');
INSERT INTO public.addresses VALUES (5047, NULL, '172.27.166.11', 'aa:bb:cc:dd:a6:0b');
INSERT INTO public.addresses VALUES (5048, NULL, '172.27.166.12', 'aa:bb:cc:dd:a6:0c');
INSERT INTO public.addresses VALUES (5049, NULL, '172.27.166.13', 'aa:bb:cc:dd:a6:0d');
INSERT INTO public.addresses VALUES (5050, NULL, '172.27.166.14', 'aa:bb:cc:dd:a6:0e');
INSERT INTO public.addresses VALUES (5051, NULL, '172.27.166.15', 'aa:bb:cc:dd:a6:0f');
INSERT INTO public.addresses VALUES (5052, NULL, '172.27.166.16', 'aa:bb:cc:dd:a6:10');
INSERT INTO public.addresses VALUES (5053, NULL, '172.27.166.17', 'aa:bb:cc:dd:a6:11');
INSERT INTO public.addresses VALUES (5054, NULL, '172.27.166.18', 'aa:bb:cc:dd:a6:12');
INSERT INTO public.addresses VALUES (5055, NULL, '172.27.166.19', 'aa:bb:cc:dd:a6:13');
INSERT INTO public.addresses VALUES (5056, NULL, '172.27.166.20', 'aa:bb:cc:dd:a6:14');
INSERT INTO public.addresses VALUES (5057, NULL, '172.27.166.21', 'aa:bb:cc:dd:a6:15');
INSERT INTO public.addresses VALUES (5058, NULL, '172.27.166.22', 'aa:bb:cc:dd:a6:16');
INSERT INTO public.addresses VALUES (5059, NULL, '172.27.166.23', 'aa:bb:cc:dd:a6:17');
INSERT INTO public.addresses VALUES (5060, NULL, '172.27.166.24', 'aa:bb:cc:dd:a6:18');
INSERT INTO public.addresses VALUES (5061, NULL, '172.27.166.25', 'aa:bb:cc:dd:a6:19');
INSERT INTO public.addresses VALUES (5062, NULL, '172.27.166.26', 'aa:bb:cc:dd:a6:1a');
INSERT INTO public.addresses VALUES (5063, NULL, '172.27.166.27', 'aa:bb:cc:dd:a6:1b');
INSERT INTO public.addresses VALUES (5064, NULL, '172.27.166.28', 'aa:bb:cc:dd:a6:1c');
INSERT INTO public.addresses VALUES (5065, NULL, '172.27.166.29', 'aa:bb:cc:dd:a6:1d');
INSERT INTO public.addresses VALUES (5066, NULL, '172.27.166.30', 'aa:bb:cc:dd:a6:1e');
INSERT INTO public.addresses VALUES (5067, NULL, '172.27.166.31', 'aa:bb:cc:dd:a6:1f');
INSERT INTO public.addresses VALUES (5068, NULL, '172.27.166.32', 'aa:bb:cc:dd:a6:20');
INSERT INTO public.addresses VALUES (5069, NULL, '172.27.166.33', 'aa:bb:cc:dd:a6:21');
INSERT INTO public.addresses VALUES (5070, NULL, '172.27.166.34', 'aa:bb:cc:dd:a6:22');
INSERT INTO public.addresses VALUES (5071, NULL, '172.27.166.35', 'aa:bb:cc:dd:a6:23');
INSERT INTO public.addresses VALUES (5072, NULL, '172.27.166.36', 'aa:bb:cc:dd:a6:24');
INSERT INTO public.addresses VALUES (5073, NULL, '172.27.166.37', 'aa:bb:cc:dd:a6:25');
INSERT INTO public.addresses VALUES (5074, NULL, '172.27.166.38', 'aa:bb:cc:dd:a6:26');
INSERT INTO public.addresses VALUES (5075, NULL, '172.27.166.39', 'aa:bb:cc:dd:a6:27');
INSERT INTO public.addresses VALUES (5076, NULL, '172.27.166.40', 'aa:bb:cc:dd:a6:28');
INSERT INTO public.addresses VALUES (5077, NULL, '172.27.166.41', 'aa:bb:cc:dd:a6:29');
INSERT INTO public.addresses VALUES (5078, NULL, '172.27.166.42', 'aa:bb:cc:dd:a6:2a');
INSERT INTO public.addresses VALUES (5079, NULL, '172.27.166.43', 'aa:bb:cc:dd:a6:2b');
INSERT INTO public.addresses VALUES (5080, NULL, '172.27.166.44', 'aa:bb:cc:dd:a6:2c');
INSERT INTO public.addresses VALUES (5081, NULL, '172.27.166.45', 'aa:bb:cc:dd:a6:2d');
INSERT INTO public.addresses VALUES (5082, NULL, '172.27.166.46', 'aa:bb:cc:dd:a6:2e');
INSERT INTO public.addresses VALUES (5083, NULL, '172.27.166.47', 'aa:bb:cc:dd:a6:2f');
INSERT INTO public.addresses VALUES (5084, NULL, '172.27.166.48', 'aa:bb:cc:dd:a6:30');
INSERT INTO public.addresses VALUES (5085, NULL, '172.27.166.49', 'aa:bb:cc:dd:a6:31');
INSERT INTO public.addresses VALUES (5086, NULL, '172.27.166.50', 'aa:bb:cc:dd:a6:32');
INSERT INTO public.addresses VALUES (5087, NULL, '172.27.166.51', 'aa:bb:cc:dd:a6:33');
INSERT INTO public.addresses VALUES (5088, NULL, '172.27.166.52', 'aa:bb:cc:dd:a6:34');
INSERT INTO public.addresses VALUES (5089, NULL, '172.27.166.53', 'aa:bb:cc:dd:a6:35');
INSERT INTO public.addresses VALUES (5090, NULL, '172.27.166.54', 'aa:bb:cc:dd:a6:36');
INSERT INTO public.addresses VALUES (5091, NULL, '172.27.166.55', 'aa:bb:cc:dd:a6:37');
INSERT INTO public.addresses VALUES (5092, NULL, '172.27.166.56', 'aa:bb:cc:dd:a6:38');
INSERT INTO public.addresses VALUES (5093, NULL, '172.27.166.57', 'aa:bb:cc:dd:a6:39');
INSERT INTO public.addresses VALUES (5094, NULL, '172.27.166.58', 'aa:bb:cc:dd:a6:3a');
INSERT INTO public.addresses VALUES (5095, NULL, '172.27.166.59', 'aa:bb:cc:dd:a6:3b');
INSERT INTO public.addresses VALUES (5096, NULL, '172.27.166.60', 'aa:bb:cc:dd:a6:3c');
INSERT INTO public.addresses VALUES (5097, NULL, '172.27.166.61', 'aa:bb:cc:dd:a6:3d');
INSERT INTO public.addresses VALUES (5098, NULL, '172.27.166.62', 'aa:bb:cc:dd:a6:3e');
INSERT INTO public.addresses VALUES (5099, NULL, '172.27.166.63', 'aa:bb:cc:dd:a6:3f');
INSERT INTO public.addresses VALUES (5100, NULL, '172.27.166.64', 'aa:bb:cc:dd:a6:40');
INSERT INTO public.addresses VALUES (5101, NULL, '172.27.166.65', 'aa:bb:cc:dd:a6:41');
INSERT INTO public.addresses VALUES (5102, NULL, '172.27.166.66', 'aa:bb:cc:dd:a6:42');
INSERT INTO public.addresses VALUES (5103, NULL, '172.27.166.67', 'aa:bb:cc:dd:a6:43');
INSERT INTO public.addresses VALUES (5104, NULL, '172.27.166.68', 'aa:bb:cc:dd:a6:44');
INSERT INTO public.addresses VALUES (5105, NULL, '172.27.166.69', 'aa:bb:cc:dd:a6:45');
INSERT INTO public.addresses VALUES (5106, NULL, '172.27.166.70', 'aa:bb:cc:dd:a6:46');
INSERT INTO public.addresses VALUES (5107, NULL, '172.27.166.71', 'aa:bb:cc:dd:a6:47');
INSERT INTO public.addresses VALUES (5108, NULL, '172.27.166.72', 'aa:bb:cc:dd:a6:48');
INSERT INTO public.addresses VALUES (5109, NULL, '172.27.166.73', 'aa:bb:cc:dd:a6:49');
INSERT INTO public.addresses VALUES (5110, NULL, '172.27.166.74', 'aa:bb:cc:dd:a6:4a');
INSERT INTO public.addresses VALUES (5111, NULL, '172.27.166.75', 'aa:bb:cc:dd:a6:4b');
INSERT INTO public.addresses VALUES (5112, NULL, '172.27.166.76', 'aa:bb:cc:dd:a6:4c');
INSERT INTO public.addresses VALUES (5113, NULL, '172.27.166.77', 'aa:bb:cc:dd:a6:4d');
INSERT INTO public.addresses VALUES (5114, NULL, '172.27.166.78', 'aa:bb:cc:dd:a6:4e');
INSERT INTO public.addresses VALUES (5115, NULL, '172.27.166.79', 'aa:bb:cc:dd:a6:4f');
INSERT INTO public.addresses VALUES (5116, NULL, '172.27.166.80', 'aa:bb:cc:dd:a6:50');
INSERT INTO public.addresses VALUES (5117, NULL, '172.27.166.81', 'aa:bb:cc:dd:a6:51');
INSERT INTO public.addresses VALUES (5118, NULL, '172.27.166.82', 'aa:bb:cc:dd:a6:52');
INSERT INTO public.addresses VALUES (5119, NULL, '172.27.166.83', 'aa:bb:cc:dd:a6:53');
INSERT INTO public.addresses VALUES (5120, NULL, '172.27.166.84', 'aa:bb:cc:dd:a6:54');
INSERT INTO public.addresses VALUES (5121, NULL, '172.27.166.85', 'aa:bb:cc:dd:a6:55');
INSERT INTO public.addresses VALUES (5122, NULL, '172.27.166.86', 'aa:bb:cc:dd:a6:56');
INSERT INTO public.addresses VALUES (5123, NULL, '172.27.166.87', 'aa:bb:cc:dd:a6:57');
INSERT INTO public.addresses VALUES (5124, NULL, '172.27.166.88', 'aa:bb:cc:dd:a6:58');
INSERT INTO public.addresses VALUES (5125, NULL, '172.27.166.89', 'aa:bb:cc:dd:a6:59');
INSERT INTO public.addresses VALUES (5126, NULL, '172.27.166.90', 'aa:bb:cc:dd:a6:5a');
INSERT INTO public.addresses VALUES (5127, NULL, '172.27.166.91', 'aa:bb:cc:dd:a6:5b');
INSERT INTO public.addresses VALUES (5128, NULL, '172.27.166.92', 'aa:bb:cc:dd:a6:5c');
INSERT INTO public.addresses VALUES (5129, NULL, '172.27.166.93', 'aa:bb:cc:dd:a6:5d');
INSERT INTO public.addresses VALUES (5130, NULL, '172.27.166.94', 'aa:bb:cc:dd:a6:5e');
INSERT INTO public.addresses VALUES (5131, NULL, '172.27.166.95', 'aa:bb:cc:dd:a6:5f');
INSERT INTO public.addresses VALUES (5132, NULL, '172.27.166.96', 'aa:bb:cc:dd:a6:60');
INSERT INTO public.addresses VALUES (5133, NULL, '172.27.166.97', 'aa:bb:cc:dd:a6:61');
INSERT INTO public.addresses VALUES (5134, NULL, '172.27.166.98', 'aa:bb:cc:dd:a6:62');
INSERT INTO public.addresses VALUES (5135, NULL, '172.27.166.99', 'aa:bb:cc:dd:a6:63');
INSERT INTO public.addresses VALUES (5136, NULL, '172.27.166.100', 'aa:bb:cc:dd:a6:64');
INSERT INTO public.addresses VALUES (5137, NULL, '172.27.166.101', 'aa:bb:cc:dd:a6:65');
INSERT INTO public.addresses VALUES (5138, NULL, '172.27.166.102', 'aa:bb:cc:dd:a6:66');
INSERT INTO public.addresses VALUES (5139, NULL, '172.27.166.103', 'aa:bb:cc:dd:a6:67');
INSERT INTO public.addresses VALUES (5140, NULL, '172.27.166.104', 'aa:bb:cc:dd:a6:68');
INSERT INTO public.addresses VALUES (5141, NULL, '172.27.166.105', 'aa:bb:cc:dd:a6:69');
INSERT INTO public.addresses VALUES (5142, NULL, '172.27.166.106', 'aa:bb:cc:dd:a6:6a');
INSERT INTO public.addresses VALUES (5143, NULL, '172.27.166.107', 'aa:bb:cc:dd:a6:6b');
INSERT INTO public.addresses VALUES (5144, NULL, '172.27.166.108', 'aa:bb:cc:dd:a6:6c');
INSERT INTO public.addresses VALUES (5145, NULL, '172.27.166.109', 'aa:bb:cc:dd:a6:6d');
INSERT INTO public.addresses VALUES (5146, NULL, '172.27.166.110', 'aa:bb:cc:dd:a6:6e');
INSERT INTO public.addresses VALUES (5147, NULL, '172.27.166.111', 'aa:bb:cc:dd:a6:6f');
INSERT INTO public.addresses VALUES (5148, NULL, '172.27.166.112', 'aa:bb:cc:dd:a6:70');
INSERT INTO public.addresses VALUES (5149, NULL, '172.27.166.113', 'aa:bb:cc:dd:a6:71');
INSERT INTO public.addresses VALUES (5150, NULL, '172.27.166.114', 'aa:bb:cc:dd:a6:72');
INSERT INTO public.addresses VALUES (5151, NULL, '172.27.166.115', 'aa:bb:cc:dd:a6:73');
INSERT INTO public.addresses VALUES (5152, NULL, '172.27.166.116', 'aa:bb:cc:dd:a6:74');
INSERT INTO public.addresses VALUES (5153, NULL, '172.27.166.117', 'aa:bb:cc:dd:a6:75');
INSERT INTO public.addresses VALUES (5154, NULL, '172.27.166.118', 'aa:bb:cc:dd:a6:76');
INSERT INTO public.addresses VALUES (5155, NULL, '172.27.166.119', 'aa:bb:cc:dd:a6:77');
INSERT INTO public.addresses VALUES (5156, NULL, '172.27.166.120', 'aa:bb:cc:dd:a6:78');
INSERT INTO public.addresses VALUES (5157, NULL, '172.27.166.121', 'aa:bb:cc:dd:a6:79');
INSERT INTO public.addresses VALUES (5158, NULL, '172.27.166.122', 'aa:bb:cc:dd:a6:7a');
INSERT INTO public.addresses VALUES (5159, NULL, '172.27.166.123', 'aa:bb:cc:dd:a6:7b');
INSERT INTO public.addresses VALUES (5160, NULL, '172.27.166.124', 'aa:bb:cc:dd:a6:7c');
INSERT INTO public.addresses VALUES (5161, NULL, '172.27.166.125', 'aa:bb:cc:dd:a6:7d');
INSERT INTO public.addresses VALUES (5162, NULL, '172.27.166.126', 'aa:bb:cc:dd:a6:7e');
INSERT INTO public.addresses VALUES (5163, NULL, '172.27.166.127', 'aa:bb:cc:dd:a6:7f');
INSERT INTO public.addresses VALUES (5164, NULL, '172.27.166.128', 'aa:bb:cc:dd:a6:80');
INSERT INTO public.addresses VALUES (5165, NULL, '172.27.166.129', 'aa:bb:cc:dd:a6:81');
INSERT INTO public.addresses VALUES (5166, NULL, '172.27.166.130', 'aa:bb:cc:dd:a6:82');
INSERT INTO public.addresses VALUES (5167, NULL, '172.27.166.131', 'aa:bb:cc:dd:a6:83');
INSERT INTO public.addresses VALUES (5168, NULL, '172.27.166.132', 'aa:bb:cc:dd:a6:84');
INSERT INTO public.addresses VALUES (5169, NULL, '172.27.166.133', 'aa:bb:cc:dd:a6:85');
INSERT INTO public.addresses VALUES (5170, NULL, '172.27.166.134', 'aa:bb:cc:dd:a6:86');
INSERT INTO public.addresses VALUES (5171, NULL, '172.27.166.135', 'aa:bb:cc:dd:a6:87');
INSERT INTO public.addresses VALUES (5172, NULL, '172.27.166.136', 'aa:bb:cc:dd:a6:88');
INSERT INTO public.addresses VALUES (5173, NULL, '172.27.166.137', 'aa:bb:cc:dd:a6:89');
INSERT INTO public.addresses VALUES (5174, NULL, '172.27.166.138', 'aa:bb:cc:dd:a6:8a');
INSERT INTO public.addresses VALUES (5175, NULL, '172.27.166.139', 'aa:bb:cc:dd:a6:8b');
INSERT INTO public.addresses VALUES (5176, NULL, '172.27.166.140', 'aa:bb:cc:dd:a6:8c');
INSERT INTO public.addresses VALUES (5177, NULL, '172.27.166.141', 'aa:bb:cc:dd:a6:8d');
INSERT INTO public.addresses VALUES (5178, NULL, '172.27.166.142', 'aa:bb:cc:dd:a6:8e');
INSERT INTO public.addresses VALUES (5179, NULL, '172.27.166.143', 'aa:bb:cc:dd:a6:8f');
INSERT INTO public.addresses VALUES (5180, NULL, '172.27.166.144', 'aa:bb:cc:dd:a6:90');
INSERT INTO public.addresses VALUES (5181, NULL, '172.27.166.145', 'aa:bb:cc:dd:a6:91');
INSERT INTO public.addresses VALUES (5182, NULL, '172.27.166.146', 'aa:bb:cc:dd:a6:92');
INSERT INTO public.addresses VALUES (5183, NULL, '172.27.166.147', 'aa:bb:cc:dd:a6:93');
INSERT INTO public.addresses VALUES (5184, NULL, '172.27.166.148', 'aa:bb:cc:dd:a6:94');
INSERT INTO public.addresses VALUES (5185, NULL, '172.27.166.149', 'aa:bb:cc:dd:a6:95');
INSERT INTO public.addresses VALUES (5186, NULL, '172.27.166.150', 'aa:bb:cc:dd:a6:96');
INSERT INTO public.addresses VALUES (5187, NULL, '172.27.166.151', 'aa:bb:cc:dd:a6:97');
INSERT INTO public.addresses VALUES (5188, NULL, '172.27.166.152', 'aa:bb:cc:dd:a6:98');
INSERT INTO public.addresses VALUES (5189, NULL, '172.27.166.153', 'aa:bb:cc:dd:a6:99');
INSERT INTO public.addresses VALUES (5190, NULL, '172.27.166.154', 'aa:bb:cc:dd:a6:9a');
INSERT INTO public.addresses VALUES (5191, NULL, '172.27.166.155', 'aa:bb:cc:dd:a6:9b');
INSERT INTO public.addresses VALUES (5192, NULL, '172.27.166.156', 'aa:bb:cc:dd:a6:9c');
INSERT INTO public.addresses VALUES (5193, NULL, '172.27.166.157', 'aa:bb:cc:dd:a6:9d');
INSERT INTO public.addresses VALUES (5194, NULL, '172.27.166.158', 'aa:bb:cc:dd:a6:9e');
INSERT INTO public.addresses VALUES (5195, NULL, '172.27.166.159', 'aa:bb:cc:dd:a6:9f');
INSERT INTO public.addresses VALUES (5196, NULL, '172.27.166.160', 'aa:bb:cc:dd:a6:a0');
INSERT INTO public.addresses VALUES (5197, NULL, '172.27.166.161', 'aa:bb:cc:dd:a6:a1');
INSERT INTO public.addresses VALUES (5198, NULL, '172.27.166.162', 'aa:bb:cc:dd:a6:a2');
INSERT INTO public.addresses VALUES (5199, NULL, '172.27.166.163', 'aa:bb:cc:dd:a6:a3');
INSERT INTO public.addresses VALUES (5200, NULL, '172.27.166.164', 'aa:bb:cc:dd:a6:a4');
INSERT INTO public.addresses VALUES (5201, NULL, '172.27.166.165', 'aa:bb:cc:dd:a6:a5');
INSERT INTO public.addresses VALUES (5202, NULL, '172.27.166.166', 'aa:bb:cc:dd:a6:a6');
INSERT INTO public.addresses VALUES (5203, NULL, '172.27.166.167', 'aa:bb:cc:dd:a6:a7');
INSERT INTO public.addresses VALUES (5204, NULL, '172.27.166.168', 'aa:bb:cc:dd:a6:a8');
INSERT INTO public.addresses VALUES (5205, NULL, '172.27.166.169', 'aa:bb:cc:dd:a6:a9');
INSERT INTO public.addresses VALUES (5206, NULL, '172.27.166.170', 'aa:bb:cc:dd:a6:aa');
INSERT INTO public.addresses VALUES (5207, NULL, '172.27.166.171', 'aa:bb:cc:dd:a6:ab');
INSERT INTO public.addresses VALUES (5208, NULL, '172.27.166.172', 'aa:bb:cc:dd:a6:ac');
INSERT INTO public.addresses VALUES (5209, NULL, '172.27.166.173', 'aa:bb:cc:dd:a6:ad');
INSERT INTO public.addresses VALUES (5210, NULL, '172.27.166.174', 'aa:bb:cc:dd:a6:ae');
INSERT INTO public.addresses VALUES (5211, NULL, '172.27.166.175', 'aa:bb:cc:dd:a6:af');
INSERT INTO public.addresses VALUES (5212, NULL, '172.27.166.176', 'aa:bb:cc:dd:a6:b0');
INSERT INTO public.addresses VALUES (5213, NULL, '172.27.166.177', 'aa:bb:cc:dd:a6:b1');
INSERT INTO public.addresses VALUES (5214, NULL, '172.27.166.178', 'aa:bb:cc:dd:a6:b2');
INSERT INTO public.addresses VALUES (5215, NULL, '172.27.166.179', 'aa:bb:cc:dd:a6:b3');
INSERT INTO public.addresses VALUES (5216, NULL, '172.27.166.180', 'aa:bb:cc:dd:a6:b4');
INSERT INTO public.addresses VALUES (5217, NULL, '172.27.166.181', 'aa:bb:cc:dd:a6:b5');
INSERT INTO public.addresses VALUES (5218, NULL, '172.27.166.182', 'aa:bb:cc:dd:a6:b6');
INSERT INTO public.addresses VALUES (5219, NULL, '172.27.166.183', 'aa:bb:cc:dd:a6:b7');
INSERT INTO public.addresses VALUES (5220, NULL, '172.27.166.184', 'aa:bb:cc:dd:a6:b8');
INSERT INTO public.addresses VALUES (5221, NULL, '172.27.166.185', 'aa:bb:cc:dd:a6:b9');
INSERT INTO public.addresses VALUES (5222, NULL, '172.27.166.186', 'aa:bb:cc:dd:a6:ba');
INSERT INTO public.addresses VALUES (5223, NULL, '172.27.166.187', 'aa:bb:cc:dd:a6:bb');
INSERT INTO public.addresses VALUES (5224, NULL, '172.27.166.188', 'aa:bb:cc:dd:a6:bc');
INSERT INTO public.addresses VALUES (5225, NULL, '172.27.166.189', 'aa:bb:cc:dd:a6:bd');
INSERT INTO public.addresses VALUES (5226, NULL, '172.27.166.190', 'aa:bb:cc:dd:a6:be');
INSERT INTO public.addresses VALUES (5227, NULL, '172.27.166.191', 'aa:bb:cc:dd:a6:bf');
INSERT INTO public.addresses VALUES (5228, NULL, '172.27.166.192', 'aa:bb:cc:dd:a6:c0');
INSERT INTO public.addresses VALUES (5229, NULL, '172.27.166.193', 'aa:bb:cc:dd:a6:c1');
INSERT INTO public.addresses VALUES (5230, NULL, '172.27.166.194', 'aa:bb:cc:dd:a6:c2');
INSERT INTO public.addresses VALUES (5231, NULL, '172.27.166.195', 'aa:bb:cc:dd:a6:c3');
INSERT INTO public.addresses VALUES (5232, NULL, '172.27.166.196', 'aa:bb:cc:dd:a6:c4');
INSERT INTO public.addresses VALUES (5233, NULL, '172.27.166.197', 'aa:bb:cc:dd:a6:c5');
INSERT INTO public.addresses VALUES (5234, NULL, '172.27.166.198', 'aa:bb:cc:dd:a6:c6');
INSERT INTO public.addresses VALUES (5235, NULL, '172.27.166.199', 'aa:bb:cc:dd:a6:c7');
INSERT INTO public.addresses VALUES (5236, NULL, '172.27.166.200', 'aa:bb:cc:dd:a6:c8');
INSERT INTO public.addresses VALUES (5237, NULL, '172.27.166.201', 'aa:bb:cc:dd:a6:c9');
INSERT INTO public.addresses VALUES (5238, NULL, '172.27.166.202', 'aa:bb:cc:dd:a6:ca');
INSERT INTO public.addresses VALUES (5239, NULL, '172.27.166.203', 'aa:bb:cc:dd:a6:cb');
INSERT INTO public.addresses VALUES (5240, NULL, '172.27.166.204', 'aa:bb:cc:dd:a6:cc');
INSERT INTO public.addresses VALUES (5241, NULL, '172.27.166.205', 'aa:bb:cc:dd:a6:cd');
INSERT INTO public.addresses VALUES (5242, NULL, '172.27.166.206', 'aa:bb:cc:dd:a6:ce');
INSERT INTO public.addresses VALUES (5243, NULL, '172.27.166.207', 'aa:bb:cc:dd:a6:cf');
INSERT INTO public.addresses VALUES (5244, NULL, '172.27.166.208', 'aa:bb:cc:dd:a6:d0');
INSERT INTO public.addresses VALUES (5245, NULL, '172.27.166.209', 'aa:bb:cc:dd:a6:d1');
INSERT INTO public.addresses VALUES (5246, NULL, '172.27.166.210', 'aa:bb:cc:dd:a6:d2');
INSERT INTO public.addresses VALUES (5247, NULL, '172.27.166.211', 'aa:bb:cc:dd:a6:d3');
INSERT INTO public.addresses VALUES (5248, NULL, '172.27.166.212', 'aa:bb:cc:dd:a6:d4');
INSERT INTO public.addresses VALUES (5249, NULL, '172.27.166.213', 'aa:bb:cc:dd:a6:d5');
INSERT INTO public.addresses VALUES (5250, NULL, '172.27.166.214', 'aa:bb:cc:dd:a6:d6');
INSERT INTO public.addresses VALUES (5251, NULL, '172.27.166.215', 'aa:bb:cc:dd:a6:d7');
INSERT INTO public.addresses VALUES (5252, NULL, '172.27.166.216', 'aa:bb:cc:dd:a6:d8');
INSERT INTO public.addresses VALUES (5253, NULL, '172.27.166.217', 'aa:bb:cc:dd:a6:d9');
INSERT INTO public.addresses VALUES (5254, NULL, '172.27.166.218', 'aa:bb:cc:dd:a6:da');
INSERT INTO public.addresses VALUES (5255, NULL, '172.27.166.219', 'aa:bb:cc:dd:a6:db');
INSERT INTO public.addresses VALUES (5256, NULL, '172.27.166.220', 'aa:bb:cc:dd:a6:dc');
INSERT INTO public.addresses VALUES (5257, NULL, '172.27.166.221', 'aa:bb:cc:dd:a6:dd');
INSERT INTO public.addresses VALUES (5258, NULL, '172.27.166.222', 'aa:bb:cc:dd:a6:de');
INSERT INTO public.addresses VALUES (5259, NULL, '172.27.166.223', 'aa:bb:cc:dd:a6:df');
INSERT INTO public.addresses VALUES (5260, NULL, '172.27.166.224', 'aa:bb:cc:dd:a6:e0');
INSERT INTO public.addresses VALUES (5261, NULL, '172.27.166.225', 'aa:bb:cc:dd:a6:e1');
INSERT INTO public.addresses VALUES (5262, NULL, '172.27.166.226', 'aa:bb:cc:dd:a6:e2');
INSERT INTO public.addresses VALUES (5263, NULL, '172.27.166.227', 'aa:bb:cc:dd:a6:e3');
INSERT INTO public.addresses VALUES (5264, NULL, '172.27.166.228', 'aa:bb:cc:dd:a6:e4');
INSERT INTO public.addresses VALUES (5265, NULL, '172.27.166.229', 'aa:bb:cc:dd:a6:e5');
INSERT INTO public.addresses VALUES (5266, NULL, '172.27.166.230', 'aa:bb:cc:dd:a6:e6');
INSERT INTO public.addresses VALUES (5267, NULL, '172.27.166.231', 'aa:bb:cc:dd:a6:e7');
INSERT INTO public.addresses VALUES (5268, NULL, '172.27.166.232', 'aa:bb:cc:dd:a6:e8');
INSERT INTO public.addresses VALUES (5269, NULL, '172.27.166.233', 'aa:bb:cc:dd:a6:e9');
INSERT INTO public.addresses VALUES (5270, NULL, '172.27.166.234', 'aa:bb:cc:dd:a6:ea');
INSERT INTO public.addresses VALUES (5271, NULL, '172.27.166.235', 'aa:bb:cc:dd:a6:eb');
INSERT INTO public.addresses VALUES (5272, NULL, '172.27.166.236', 'aa:bb:cc:dd:a6:ec');
INSERT INTO public.addresses VALUES (5273, NULL, '172.27.166.237', 'aa:bb:cc:dd:a6:ed');
INSERT INTO public.addresses VALUES (5274, NULL, '172.27.166.238', 'aa:bb:cc:dd:a6:ee');
INSERT INTO public.addresses VALUES (5275, NULL, '172.27.166.239', 'aa:bb:cc:dd:a6:ef');
INSERT INTO public.addresses VALUES (5276, NULL, '172.27.166.240', 'aa:bb:cc:dd:a6:f0');
INSERT INTO public.addresses VALUES (5277, NULL, '172.27.166.241', 'aa:bb:cc:dd:a6:f1');
INSERT INTO public.addresses VALUES (5278, NULL, '172.27.166.242', 'aa:bb:cc:dd:a6:f2');
INSERT INTO public.addresses VALUES (5279, NULL, '172.27.166.243', 'aa:bb:cc:dd:a6:f3');
INSERT INTO public.addresses VALUES (5280, NULL, '172.27.166.244', 'aa:bb:cc:dd:a6:f4');
INSERT INTO public.addresses VALUES (5281, NULL, '172.27.166.245', 'aa:bb:cc:dd:a6:f5');
INSERT INTO public.addresses VALUES (5282, NULL, '172.27.166.246', 'aa:bb:cc:dd:a6:f6');
INSERT INTO public.addresses VALUES (5283, NULL, '172.27.166.247', 'aa:bb:cc:dd:a6:f7');
INSERT INTO public.addresses VALUES (5284, NULL, '172.27.166.248', 'aa:bb:cc:dd:a6:f8');
INSERT INTO public.addresses VALUES (5285, NULL, '172.27.166.249', 'aa:bb:cc:dd:a6:f9');
INSERT INTO public.addresses VALUES (5286, NULL, '172.27.166.250', 'aa:bb:cc:dd:a6:fa');
INSERT INTO public.addresses VALUES (5287, NULL, '172.27.166.251', 'aa:bb:cc:dd:a6:fb');
INSERT INTO public.addresses VALUES (5288, NULL, '172.27.166.252', 'aa:bb:cc:dd:a6:fc');
INSERT INTO public.addresses VALUES (5289, NULL, '172.27.166.253', 'aa:bb:cc:dd:a6:fd');
INSERT INTO public.addresses VALUES (5290, NULL, '172.27.166.254', 'aa:bb:cc:dd:a6:fe');
INSERT INTO public.addresses VALUES (5291, NULL, '172.27.166.255', 'aa:bb:cc:dd:a6:ff');
INSERT INTO public.addresses VALUES (5292, NULL, '172.27.167.0', 'aa:bb:cc:dd:a7:00');
INSERT INTO public.addresses VALUES (5293, NULL, '172.27.167.1', 'aa:bb:cc:dd:a7:01');
INSERT INTO public.addresses VALUES (5294, NULL, '172.27.167.2', 'aa:bb:cc:dd:a7:02');
INSERT INTO public.addresses VALUES (5295, NULL, '172.27.167.3', 'aa:bb:cc:dd:a7:03');
INSERT INTO public.addresses VALUES (5296, NULL, '172.27.167.4', 'aa:bb:cc:dd:a7:04');
INSERT INTO public.addresses VALUES (5297, NULL, '172.27.167.5', 'aa:bb:cc:dd:a7:05');
INSERT INTO public.addresses VALUES (5298, NULL, '172.27.167.6', 'aa:bb:cc:dd:a7:06');
INSERT INTO public.addresses VALUES (5299, NULL, '172.27.167.7', 'aa:bb:cc:dd:a7:07');
INSERT INTO public.addresses VALUES (5300, NULL, '172.27.167.8', 'aa:bb:cc:dd:a7:08');
INSERT INTO public.addresses VALUES (5301, NULL, '172.27.167.9', 'aa:bb:cc:dd:a7:09');
INSERT INTO public.addresses VALUES (5302, NULL, '172.27.167.10', 'aa:bb:cc:dd:a7:0a');
INSERT INTO public.addresses VALUES (5303, NULL, '172.27.167.11', 'aa:bb:cc:dd:a7:0b');
INSERT INTO public.addresses VALUES (5304, NULL, '172.27.167.12', 'aa:bb:cc:dd:a7:0c');
INSERT INTO public.addresses VALUES (5305, NULL, '172.27.167.13', 'aa:bb:cc:dd:a7:0d');
INSERT INTO public.addresses VALUES (5306, NULL, '172.27.167.14', 'aa:bb:cc:dd:a7:0e');
INSERT INTO public.addresses VALUES (5307, NULL, '172.27.167.15', 'aa:bb:cc:dd:a7:0f');
INSERT INTO public.addresses VALUES (5308, NULL, '172.27.167.16', 'aa:bb:cc:dd:a7:10');
INSERT INTO public.addresses VALUES (5309, NULL, '172.27.167.17', 'aa:bb:cc:dd:a7:11');
INSERT INTO public.addresses VALUES (5310, NULL, '172.27.167.18', 'aa:bb:cc:dd:a7:12');
INSERT INTO public.addresses VALUES (5311, NULL, '172.27.167.19', 'aa:bb:cc:dd:a7:13');
INSERT INTO public.addresses VALUES (5312, NULL, '172.27.167.20', 'aa:bb:cc:dd:a7:14');
INSERT INTO public.addresses VALUES (5313, NULL, '172.27.167.21', 'aa:bb:cc:dd:a7:15');
INSERT INTO public.addresses VALUES (5314, NULL, '172.27.167.22', 'aa:bb:cc:dd:a7:16');
INSERT INTO public.addresses VALUES (5315, NULL, '172.27.167.23', 'aa:bb:cc:dd:a7:17');
INSERT INTO public.addresses VALUES (5316, NULL, '172.27.167.24', 'aa:bb:cc:dd:a7:18');
INSERT INTO public.addresses VALUES (5317, NULL, '172.27.167.25', 'aa:bb:cc:dd:a7:19');
INSERT INTO public.addresses VALUES (5318, NULL, '172.27.167.26', 'aa:bb:cc:dd:a7:1a');
INSERT INTO public.addresses VALUES (5319, NULL, '172.27.167.27', 'aa:bb:cc:dd:a7:1b');
INSERT INTO public.addresses VALUES (5320, NULL, '172.27.167.28', 'aa:bb:cc:dd:a7:1c');
INSERT INTO public.addresses VALUES (5321, NULL, '172.27.167.29', 'aa:bb:cc:dd:a7:1d');
INSERT INTO public.addresses VALUES (5322, NULL, '172.27.167.30', 'aa:bb:cc:dd:a7:1e');
INSERT INTO public.addresses VALUES (5323, NULL, '172.27.167.31', 'aa:bb:cc:dd:a7:1f');
INSERT INTO public.addresses VALUES (5324, NULL, '172.27.167.32', 'aa:bb:cc:dd:a7:20');
INSERT INTO public.addresses VALUES (5325, NULL, '172.27.167.33', 'aa:bb:cc:dd:a7:21');
INSERT INTO public.addresses VALUES (5326, NULL, '172.27.167.34', 'aa:bb:cc:dd:a7:22');
INSERT INTO public.addresses VALUES (5327, NULL, '172.27.167.35', 'aa:bb:cc:dd:a7:23');
INSERT INTO public.addresses VALUES (5328, NULL, '172.27.167.36', 'aa:bb:cc:dd:a7:24');
INSERT INTO public.addresses VALUES (5329, NULL, '172.27.167.37', 'aa:bb:cc:dd:a7:25');
INSERT INTO public.addresses VALUES (5330, NULL, '172.27.167.38', 'aa:bb:cc:dd:a7:26');
INSERT INTO public.addresses VALUES (5331, NULL, '172.27.167.39', 'aa:bb:cc:dd:a7:27');
INSERT INTO public.addresses VALUES (5332, NULL, '172.27.167.40', 'aa:bb:cc:dd:a7:28');
INSERT INTO public.addresses VALUES (5333, NULL, '172.27.167.41', 'aa:bb:cc:dd:a7:29');
INSERT INTO public.addresses VALUES (5334, NULL, '172.27.167.42', 'aa:bb:cc:dd:a7:2a');
INSERT INTO public.addresses VALUES (5335, NULL, '172.27.167.43', 'aa:bb:cc:dd:a7:2b');
INSERT INTO public.addresses VALUES (5336, NULL, '172.27.167.44', 'aa:bb:cc:dd:a7:2c');
INSERT INTO public.addresses VALUES (5337, NULL, '172.27.167.45', 'aa:bb:cc:dd:a7:2d');
INSERT INTO public.addresses VALUES (5338, NULL, '172.27.167.46', 'aa:bb:cc:dd:a7:2e');
INSERT INTO public.addresses VALUES (5339, NULL, '172.27.167.47', 'aa:bb:cc:dd:a7:2f');
INSERT INTO public.addresses VALUES (5340, NULL, '172.27.167.48', 'aa:bb:cc:dd:a7:30');
INSERT INTO public.addresses VALUES (5341, NULL, '172.27.167.49', 'aa:bb:cc:dd:a7:31');
INSERT INTO public.addresses VALUES (5342, NULL, '172.27.167.50', 'aa:bb:cc:dd:a7:32');
INSERT INTO public.addresses VALUES (5343, NULL, '172.27.167.51', 'aa:bb:cc:dd:a7:33');
INSERT INTO public.addresses VALUES (5344, NULL, '172.27.167.52', 'aa:bb:cc:dd:a7:34');
INSERT INTO public.addresses VALUES (5345, NULL, '172.27.167.53', 'aa:bb:cc:dd:a7:35');
INSERT INTO public.addresses VALUES (5346, NULL, '172.27.167.54', 'aa:bb:cc:dd:a7:36');
INSERT INTO public.addresses VALUES (5347, NULL, '172.27.167.55', 'aa:bb:cc:dd:a7:37');
INSERT INTO public.addresses VALUES (5348, NULL, '172.27.167.56', 'aa:bb:cc:dd:a7:38');
INSERT INTO public.addresses VALUES (5349, NULL, '172.27.167.57', 'aa:bb:cc:dd:a7:39');
INSERT INTO public.addresses VALUES (5350, NULL, '172.27.167.58', 'aa:bb:cc:dd:a7:3a');
INSERT INTO public.addresses VALUES (5351, NULL, '172.27.167.59', 'aa:bb:cc:dd:a7:3b');
INSERT INTO public.addresses VALUES (5352, NULL, '172.27.167.60', 'aa:bb:cc:dd:a7:3c');
INSERT INTO public.addresses VALUES (5353, NULL, '172.27.167.61', 'aa:bb:cc:dd:a7:3d');
INSERT INTO public.addresses VALUES (5354, NULL, '172.27.167.62', 'aa:bb:cc:dd:a7:3e');
INSERT INTO public.addresses VALUES (5355, NULL, '172.27.167.63', 'aa:bb:cc:dd:a7:3f');
INSERT INTO public.addresses VALUES (5356, NULL, '172.27.167.64', 'aa:bb:cc:dd:a7:40');
INSERT INTO public.addresses VALUES (5357, NULL, '172.27.167.65', 'aa:bb:cc:dd:a7:41');
INSERT INTO public.addresses VALUES (5358, NULL, '172.27.167.66', 'aa:bb:cc:dd:a7:42');
INSERT INTO public.addresses VALUES (5359, NULL, '172.27.167.67', 'aa:bb:cc:dd:a7:43');
INSERT INTO public.addresses VALUES (5360, NULL, '172.27.167.68', 'aa:bb:cc:dd:a7:44');
INSERT INTO public.addresses VALUES (5361, NULL, '172.27.167.69', 'aa:bb:cc:dd:a7:45');
INSERT INTO public.addresses VALUES (5362, NULL, '172.27.167.70', 'aa:bb:cc:dd:a7:46');
INSERT INTO public.addresses VALUES (5363, NULL, '172.27.167.71', 'aa:bb:cc:dd:a7:47');
INSERT INTO public.addresses VALUES (5364, NULL, '172.27.167.72', 'aa:bb:cc:dd:a7:48');
INSERT INTO public.addresses VALUES (5365, NULL, '172.27.167.73', 'aa:bb:cc:dd:a7:49');
INSERT INTO public.addresses VALUES (5366, NULL, '172.27.167.74', 'aa:bb:cc:dd:a7:4a');
INSERT INTO public.addresses VALUES (5367, NULL, '172.27.167.75', 'aa:bb:cc:dd:a7:4b');
INSERT INTO public.addresses VALUES (5368, NULL, '172.27.167.76', 'aa:bb:cc:dd:a7:4c');
INSERT INTO public.addresses VALUES (5369, NULL, '172.27.167.77', 'aa:bb:cc:dd:a7:4d');
INSERT INTO public.addresses VALUES (5370, NULL, '172.27.167.78', 'aa:bb:cc:dd:a7:4e');
INSERT INTO public.addresses VALUES (5371, NULL, '172.27.167.79', 'aa:bb:cc:dd:a7:4f');
INSERT INTO public.addresses VALUES (5372, NULL, '172.27.167.80', 'aa:bb:cc:dd:a7:50');
INSERT INTO public.addresses VALUES (5373, NULL, '172.27.167.81', 'aa:bb:cc:dd:a7:51');
INSERT INTO public.addresses VALUES (5374, NULL, '172.27.167.82', 'aa:bb:cc:dd:a7:52');
INSERT INTO public.addresses VALUES (5375, NULL, '172.27.167.83', 'aa:bb:cc:dd:a7:53');
INSERT INTO public.addresses VALUES (5376, NULL, '172.27.167.84', 'aa:bb:cc:dd:a7:54');
INSERT INTO public.addresses VALUES (5377, NULL, '172.27.167.85', 'aa:bb:cc:dd:a7:55');
INSERT INTO public.addresses VALUES (5378, NULL, '172.27.167.86', 'aa:bb:cc:dd:a7:56');
INSERT INTO public.addresses VALUES (5379, NULL, '172.27.167.87', 'aa:bb:cc:dd:a7:57');
INSERT INTO public.addresses VALUES (5380, NULL, '172.27.167.88', 'aa:bb:cc:dd:a7:58');
INSERT INTO public.addresses VALUES (5381, NULL, '172.27.167.89', 'aa:bb:cc:dd:a7:59');
INSERT INTO public.addresses VALUES (5382, NULL, '172.27.167.90', 'aa:bb:cc:dd:a7:5a');
INSERT INTO public.addresses VALUES (5383, NULL, '172.27.167.91', 'aa:bb:cc:dd:a7:5b');
INSERT INTO public.addresses VALUES (5384, NULL, '172.27.167.92', 'aa:bb:cc:dd:a7:5c');
INSERT INTO public.addresses VALUES (5385, NULL, '172.27.167.93', 'aa:bb:cc:dd:a7:5d');
INSERT INTO public.addresses VALUES (5386, NULL, '172.27.167.94', 'aa:bb:cc:dd:a7:5e');
INSERT INTO public.addresses VALUES (5387, NULL, '172.27.167.95', 'aa:bb:cc:dd:a7:5f');
INSERT INTO public.addresses VALUES (5388, NULL, '172.27.167.96', 'aa:bb:cc:dd:a7:60');
INSERT INTO public.addresses VALUES (5389, NULL, '172.27.167.97', 'aa:bb:cc:dd:a7:61');
INSERT INTO public.addresses VALUES (5390, NULL, '172.27.167.98', 'aa:bb:cc:dd:a7:62');
INSERT INTO public.addresses VALUES (5391, NULL, '172.27.167.99', 'aa:bb:cc:dd:a7:63');
INSERT INTO public.addresses VALUES (5392, NULL, '172.27.167.100', 'aa:bb:cc:dd:a7:64');
INSERT INTO public.addresses VALUES (5393, NULL, '172.27.167.101', 'aa:bb:cc:dd:a7:65');
INSERT INTO public.addresses VALUES (5394, NULL, '172.27.167.102', 'aa:bb:cc:dd:a7:66');
INSERT INTO public.addresses VALUES (5395, NULL, '172.27.167.103', 'aa:bb:cc:dd:a7:67');
INSERT INTO public.addresses VALUES (5396, NULL, '172.27.167.104', 'aa:bb:cc:dd:a7:68');
INSERT INTO public.addresses VALUES (5397, NULL, '172.27.167.105', 'aa:bb:cc:dd:a7:69');
INSERT INTO public.addresses VALUES (5398, NULL, '172.27.167.106', 'aa:bb:cc:dd:a7:6a');
INSERT INTO public.addresses VALUES (5399, NULL, '172.27.167.107', 'aa:bb:cc:dd:a7:6b');
INSERT INTO public.addresses VALUES (5400, NULL, '172.27.167.108', 'aa:bb:cc:dd:a7:6c');
INSERT INTO public.addresses VALUES (5401, NULL, '172.27.167.109', 'aa:bb:cc:dd:a7:6d');
INSERT INTO public.addresses VALUES (5402, NULL, '172.27.167.110', 'aa:bb:cc:dd:a7:6e');
INSERT INTO public.addresses VALUES (5403, NULL, '172.27.167.111', 'aa:bb:cc:dd:a7:6f');
INSERT INTO public.addresses VALUES (5404, NULL, '172.27.167.112', 'aa:bb:cc:dd:a7:70');
INSERT INTO public.addresses VALUES (5405, NULL, '172.27.167.113', 'aa:bb:cc:dd:a7:71');
INSERT INTO public.addresses VALUES (5406, NULL, '172.27.167.114', 'aa:bb:cc:dd:a7:72');
INSERT INTO public.addresses VALUES (5407, NULL, '172.27.167.115', 'aa:bb:cc:dd:a7:73');
INSERT INTO public.addresses VALUES (5408, NULL, '172.27.167.116', 'aa:bb:cc:dd:a7:74');
INSERT INTO public.addresses VALUES (5409, NULL, '172.27.167.117', 'aa:bb:cc:dd:a7:75');
INSERT INTO public.addresses VALUES (5410, NULL, '172.27.167.118', 'aa:bb:cc:dd:a7:76');
INSERT INTO public.addresses VALUES (5411, NULL, '172.27.167.119', 'aa:bb:cc:dd:a7:77');
INSERT INTO public.addresses VALUES (5412, NULL, '172.27.167.120', 'aa:bb:cc:dd:a7:78');
INSERT INTO public.addresses VALUES (5413, NULL, '172.27.167.121', 'aa:bb:cc:dd:a7:79');
INSERT INTO public.addresses VALUES (5414, NULL, '172.27.167.122', 'aa:bb:cc:dd:a7:7a');
INSERT INTO public.addresses VALUES (5415, NULL, '172.27.167.123', 'aa:bb:cc:dd:a7:7b');
INSERT INTO public.addresses VALUES (5416, NULL, '172.27.167.124', 'aa:bb:cc:dd:a7:7c');
INSERT INTO public.addresses VALUES (5417, NULL, '172.27.167.125', 'aa:bb:cc:dd:a7:7d');
INSERT INTO public.addresses VALUES (5418, NULL, '172.27.167.126', 'aa:bb:cc:dd:a7:7e');
INSERT INTO public.addresses VALUES (5419, NULL, '172.27.167.127', 'aa:bb:cc:dd:a7:7f');
INSERT INTO public.addresses VALUES (5420, NULL, '172.27.167.128', 'aa:bb:cc:dd:a7:80');
INSERT INTO public.addresses VALUES (5421, NULL, '172.27.167.129', 'aa:bb:cc:dd:a7:81');
INSERT INTO public.addresses VALUES (5422, NULL, '172.27.167.130', 'aa:bb:cc:dd:a7:82');
INSERT INTO public.addresses VALUES (5423, NULL, '172.27.167.131', 'aa:bb:cc:dd:a7:83');
INSERT INTO public.addresses VALUES (5424, NULL, '172.27.167.132', 'aa:bb:cc:dd:a7:84');
INSERT INTO public.addresses VALUES (5425, NULL, '172.27.167.133', 'aa:bb:cc:dd:a7:85');
INSERT INTO public.addresses VALUES (5426, NULL, '172.27.167.134', 'aa:bb:cc:dd:a7:86');
INSERT INTO public.addresses VALUES (5427, NULL, '172.27.167.135', 'aa:bb:cc:dd:a7:87');
INSERT INTO public.addresses VALUES (5428, NULL, '172.27.167.136', 'aa:bb:cc:dd:a7:88');
INSERT INTO public.addresses VALUES (5429, NULL, '172.27.167.137', 'aa:bb:cc:dd:a7:89');
INSERT INTO public.addresses VALUES (5430, NULL, '172.27.167.138', 'aa:bb:cc:dd:a7:8a');
INSERT INTO public.addresses VALUES (5431, NULL, '172.27.167.139', 'aa:bb:cc:dd:a7:8b');
INSERT INTO public.addresses VALUES (5432, NULL, '172.27.167.140', 'aa:bb:cc:dd:a7:8c');
INSERT INTO public.addresses VALUES (5433, NULL, '172.27.167.141', 'aa:bb:cc:dd:a7:8d');
INSERT INTO public.addresses VALUES (5434, NULL, '172.27.167.142', 'aa:bb:cc:dd:a7:8e');
INSERT INTO public.addresses VALUES (5435, NULL, '172.27.167.143', 'aa:bb:cc:dd:a7:8f');
INSERT INTO public.addresses VALUES (5436, NULL, '172.27.167.144', 'aa:bb:cc:dd:a7:90');
INSERT INTO public.addresses VALUES (5437, NULL, '172.27.167.145', 'aa:bb:cc:dd:a7:91');
INSERT INTO public.addresses VALUES (5438, NULL, '172.27.167.146', 'aa:bb:cc:dd:a7:92');
INSERT INTO public.addresses VALUES (5439, NULL, '172.27.167.147', 'aa:bb:cc:dd:a7:93');
INSERT INTO public.addresses VALUES (5440, NULL, '172.27.167.148', 'aa:bb:cc:dd:a7:94');
INSERT INTO public.addresses VALUES (5441, NULL, '172.27.167.149', 'aa:bb:cc:dd:a7:95');
INSERT INTO public.addresses VALUES (5442, NULL, '172.27.167.150', 'aa:bb:cc:dd:a7:96');
INSERT INTO public.addresses VALUES (5443, NULL, '172.27.167.151', 'aa:bb:cc:dd:a7:97');
INSERT INTO public.addresses VALUES (5444, NULL, '172.27.167.152', 'aa:bb:cc:dd:a7:98');
INSERT INTO public.addresses VALUES (5445, NULL, '172.27.167.153', 'aa:bb:cc:dd:a7:99');
INSERT INTO public.addresses VALUES (5446, NULL, '172.27.167.154', 'aa:bb:cc:dd:a7:9a');
INSERT INTO public.addresses VALUES (5447, NULL, '172.27.167.155', 'aa:bb:cc:dd:a7:9b');
INSERT INTO public.addresses VALUES (5448, NULL, '172.27.167.156', 'aa:bb:cc:dd:a7:9c');
INSERT INTO public.addresses VALUES (5449, NULL, '172.27.167.157', 'aa:bb:cc:dd:a7:9d');
INSERT INTO public.addresses VALUES (5450, NULL, '172.27.167.158', 'aa:bb:cc:dd:a7:9e');
INSERT INTO public.addresses VALUES (5451, NULL, '172.27.167.159', 'aa:bb:cc:dd:a7:9f');
INSERT INTO public.addresses VALUES (5452, NULL, '172.27.167.160', 'aa:bb:cc:dd:a7:a0');
INSERT INTO public.addresses VALUES (5453, NULL, '172.27.167.161', 'aa:bb:cc:dd:a7:a1');
INSERT INTO public.addresses VALUES (5454, NULL, '172.27.167.162', 'aa:bb:cc:dd:a7:a2');
INSERT INTO public.addresses VALUES (5455, NULL, '172.27.167.163', 'aa:bb:cc:dd:a7:a3');
INSERT INTO public.addresses VALUES (5456, NULL, '172.27.167.164', 'aa:bb:cc:dd:a7:a4');
INSERT INTO public.addresses VALUES (5457, NULL, '172.27.167.165', 'aa:bb:cc:dd:a7:a5');
INSERT INTO public.addresses VALUES (5458, NULL, '172.27.167.166', 'aa:bb:cc:dd:a7:a6');
INSERT INTO public.addresses VALUES (5459, NULL, '172.27.167.167', 'aa:bb:cc:dd:a7:a7');
INSERT INTO public.addresses VALUES (5460, NULL, '172.27.167.168', 'aa:bb:cc:dd:a7:a8');
INSERT INTO public.addresses VALUES (5461, NULL, '172.27.167.169', 'aa:bb:cc:dd:a7:a9');
INSERT INTO public.addresses VALUES (5462, NULL, '172.27.167.170', 'aa:bb:cc:dd:a7:aa');
INSERT INTO public.addresses VALUES (5463, NULL, '172.27.167.171', 'aa:bb:cc:dd:a7:ab');
INSERT INTO public.addresses VALUES (5464, NULL, '172.27.167.172', 'aa:bb:cc:dd:a7:ac');
INSERT INTO public.addresses VALUES (5465, NULL, '172.27.167.173', 'aa:bb:cc:dd:a7:ad');
INSERT INTO public.addresses VALUES (5466, NULL, '172.27.167.174', 'aa:bb:cc:dd:a7:ae');
INSERT INTO public.addresses VALUES (5467, NULL, '172.27.167.175', 'aa:bb:cc:dd:a7:af');
INSERT INTO public.addresses VALUES (5468, NULL, '172.27.167.176', 'aa:bb:cc:dd:a7:b0');
INSERT INTO public.addresses VALUES (5469, NULL, '172.27.167.177', 'aa:bb:cc:dd:a7:b1');
INSERT INTO public.addresses VALUES (5470, NULL, '172.27.167.178', 'aa:bb:cc:dd:a7:b2');
INSERT INTO public.addresses VALUES (5471, NULL, '172.27.167.179', 'aa:bb:cc:dd:a7:b3');
INSERT INTO public.addresses VALUES (5472, NULL, '172.27.167.180', 'aa:bb:cc:dd:a7:b4');
INSERT INTO public.addresses VALUES (5473, NULL, '172.27.167.181', 'aa:bb:cc:dd:a7:b5');
INSERT INTO public.addresses VALUES (5474, NULL, '172.27.167.182', 'aa:bb:cc:dd:a7:b6');
INSERT INTO public.addresses VALUES (5475, NULL, '172.27.167.183', 'aa:bb:cc:dd:a7:b7');
INSERT INTO public.addresses VALUES (5476, NULL, '172.27.167.184', 'aa:bb:cc:dd:a7:b8');
INSERT INTO public.addresses VALUES (5477, NULL, '172.27.167.185', 'aa:bb:cc:dd:a7:b9');
INSERT INTO public.addresses VALUES (5478, NULL, '172.27.167.186', 'aa:bb:cc:dd:a7:ba');
INSERT INTO public.addresses VALUES (5479, NULL, '172.27.167.187', 'aa:bb:cc:dd:a7:bb');
INSERT INTO public.addresses VALUES (5480, NULL, '172.27.167.188', 'aa:bb:cc:dd:a7:bc');
INSERT INTO public.addresses VALUES (5481, NULL, '172.27.167.189', 'aa:bb:cc:dd:a7:bd');
INSERT INTO public.addresses VALUES (5482, NULL, '172.27.167.190', 'aa:bb:cc:dd:a7:be');
INSERT INTO public.addresses VALUES (5483, NULL, '172.27.167.191', 'aa:bb:cc:dd:a7:bf');
INSERT INTO public.addresses VALUES (5484, NULL, '172.27.167.192', 'aa:bb:cc:dd:a7:c0');
INSERT INTO public.addresses VALUES (5485, NULL, '172.27.167.193', 'aa:bb:cc:dd:a7:c1');
INSERT INTO public.addresses VALUES (5486, NULL, '172.27.167.194', 'aa:bb:cc:dd:a7:c2');
INSERT INTO public.addresses VALUES (5487, NULL, '172.27.167.195', 'aa:bb:cc:dd:a7:c3');
INSERT INTO public.addresses VALUES (5488, NULL, '172.27.167.196', 'aa:bb:cc:dd:a7:c4');
INSERT INTO public.addresses VALUES (5489, NULL, '172.27.167.197', 'aa:bb:cc:dd:a7:c5');
INSERT INTO public.addresses VALUES (5490, NULL, '172.27.167.198', 'aa:bb:cc:dd:a7:c6');
INSERT INTO public.addresses VALUES (5491, NULL, '172.27.167.199', 'aa:bb:cc:dd:a7:c7');
INSERT INTO public.addresses VALUES (5492, NULL, '172.27.167.200', 'aa:bb:cc:dd:a7:c8');
INSERT INTO public.addresses VALUES (5493, NULL, '172.27.167.201', 'aa:bb:cc:dd:a7:c9');
INSERT INTO public.addresses VALUES (5494, NULL, '172.27.167.202', 'aa:bb:cc:dd:a7:ca');
INSERT INTO public.addresses VALUES (5495, NULL, '172.27.167.203', 'aa:bb:cc:dd:a7:cb');
INSERT INTO public.addresses VALUES (5496, NULL, '172.27.167.204', 'aa:bb:cc:dd:a7:cc');
INSERT INTO public.addresses VALUES (5497, NULL, '172.27.167.205', 'aa:bb:cc:dd:a7:cd');
INSERT INTO public.addresses VALUES (5498, NULL, '172.27.167.206', 'aa:bb:cc:dd:a7:ce');
INSERT INTO public.addresses VALUES (5499, NULL, '172.27.167.207', 'aa:bb:cc:dd:a7:cf');
INSERT INTO public.addresses VALUES (5500, NULL, '172.27.167.208', 'aa:bb:cc:dd:a7:d0');
INSERT INTO public.addresses VALUES (5501, NULL, '172.27.167.209', 'aa:bb:cc:dd:a7:d1');
INSERT INTO public.addresses VALUES (5502, NULL, '172.27.167.210', 'aa:bb:cc:dd:a7:d2');
INSERT INTO public.addresses VALUES (5503, NULL, '172.27.167.211', 'aa:bb:cc:dd:a7:d3');
INSERT INTO public.addresses VALUES (5504, NULL, '172.27.167.212', 'aa:bb:cc:dd:a7:d4');
INSERT INTO public.addresses VALUES (5505, NULL, '172.27.167.213', 'aa:bb:cc:dd:a7:d5');
INSERT INTO public.addresses VALUES (5506, NULL, '172.27.167.214', 'aa:bb:cc:dd:a7:d6');
INSERT INTO public.addresses VALUES (5507, NULL, '172.27.167.215', 'aa:bb:cc:dd:a7:d7');
INSERT INTO public.addresses VALUES (5508, NULL, '172.27.167.216', 'aa:bb:cc:dd:a7:d8');
INSERT INTO public.addresses VALUES (5509, NULL, '172.27.167.217', 'aa:bb:cc:dd:a7:d9');
INSERT INTO public.addresses VALUES (5510, NULL, '172.27.167.218', 'aa:bb:cc:dd:a7:da');
INSERT INTO public.addresses VALUES (3346, NULL, '172.27.72.102', 'aa:bb:cc:dd:48:66');
INSERT INTO public.addresses VALUES (3350, NULL, '172.27.72.106', 'aa:bb:cc:dd:48:6a');
INSERT INTO public.addresses VALUES (3362, NULL, '172.27.72.118', 'aa:bb:cc:dd:48:76');
INSERT INTO public.addresses VALUES (3363, NULL, '172.27.72.119', 'aa:bb:cc:dd:48:77');
INSERT INTO public.addresses VALUES (3254, NULL, '172.27.72.10', 'aa:bb:cc:dd:48:0a');
INSERT INTO public.addresses VALUES (3255, NULL, '172.27.72.11', 'aa:bb:cc:dd:48:0b');
INSERT INTO public.addresses VALUES (3257, NULL, '172.27.72.13', 'aa:bb:cc:dd:48:0d');
INSERT INTO public.addresses VALUES (3258, NULL, '172.27.72.14', 'aa:bb:cc:dd:48:0e');
INSERT INTO public.addresses VALUES (3256, NULL, '172.27.72.12', 'aa:bb:cc:dd:48:0c');
INSERT INTO public.addresses VALUES (3290, NULL, '172.27.72.46', 'aa:bb:cc:dd:48:2e');
INSERT INTO public.addresses VALUES (3274, NULL, '172.27.72.30', 'aa:bb:cc:dd:48:1e');
INSERT INTO public.addresses VALUES (3265, NULL, '172.27.72.21', 'aa:bb:cc:dd:48:15');
INSERT INTO public.addresses VALUES (3272, NULL, '172.27.72.28', 'aa:bb:cc:dd:48:1c');
INSERT INTO public.addresses VALUES (3279, NULL, '172.27.72.35', 'aa:bb:cc:dd:48:23');
INSERT INTO public.addresses VALUES (3280, NULL, '172.27.72.36', 'aa:bb:cc:dd:48:24');
INSERT INTO public.addresses VALUES (3291, NULL, '172.27.72.47', 'aa:bb:cc:dd:48:2f');
INSERT INTO public.addresses VALUES (5511, NULL, '172.27.167.219', 'aa:bb:cc:dd:a7:db');
INSERT INTO public.addresses VALUES (5512, NULL, '172.27.167.220', 'aa:bb:cc:dd:a7:dc');
INSERT INTO public.addresses VALUES (5513, NULL, '172.27.167.221', 'aa:bb:cc:dd:a7:dd');
INSERT INTO public.addresses VALUES (5514, NULL, '172.27.167.222', 'aa:bb:cc:dd:a7:de');
INSERT INTO public.addresses VALUES (5515, NULL, '172.27.167.223', 'aa:bb:cc:dd:a7:df');
INSERT INTO public.addresses VALUES (5516, NULL, '172.27.167.224', 'aa:bb:cc:dd:a7:e0');
INSERT INTO public.addresses VALUES (5517, NULL, '172.27.167.225', 'aa:bb:cc:dd:a7:e1');
INSERT INTO public.addresses VALUES (5518, NULL, '172.27.167.226', 'aa:bb:cc:dd:a7:e2');
INSERT INTO public.addresses VALUES (5519, NULL, '172.27.167.227', 'aa:bb:cc:dd:a7:e3');
INSERT INTO public.addresses VALUES (5520, NULL, '172.27.167.228', 'aa:bb:cc:dd:a7:e4');
INSERT INTO public.addresses VALUES (5521, NULL, '172.27.167.229', 'aa:bb:cc:dd:a7:e5');
INSERT INTO public.addresses VALUES (5522, NULL, '172.27.167.230', 'aa:bb:cc:dd:a7:e6');
INSERT INTO public.addresses VALUES (5523, NULL, '172.27.167.231', 'aa:bb:cc:dd:a7:e7');
INSERT INTO public.addresses VALUES (5524, NULL, '172.27.167.232', 'aa:bb:cc:dd:a7:e8');
INSERT INTO public.addresses VALUES (5525, NULL, '172.27.167.233', 'aa:bb:cc:dd:a7:e9');
INSERT INTO public.addresses VALUES (5526, NULL, '172.27.167.234', 'aa:bb:cc:dd:a7:ea');
INSERT INTO public.addresses VALUES (5527, NULL, '172.27.167.235', 'aa:bb:cc:dd:a7:eb');
INSERT INTO public.addresses VALUES (5528, NULL, '172.27.167.236', 'aa:bb:cc:dd:a7:ec');
INSERT INTO public.addresses VALUES (5529, NULL, '172.27.167.237', 'aa:bb:cc:dd:a7:ed');
INSERT INTO public.addresses VALUES (5530, NULL, '172.27.167.238', 'aa:bb:cc:dd:a7:ee');
INSERT INTO public.addresses VALUES (5531, NULL, '172.27.167.239', 'aa:bb:cc:dd:a7:ef');
INSERT INTO public.addresses VALUES (5532, NULL, '172.27.167.240', 'aa:bb:cc:dd:a7:f0');
INSERT INTO public.addresses VALUES (5533, NULL, '172.27.167.241', 'aa:bb:cc:dd:a7:f1');
INSERT INTO public.addresses VALUES (5534, NULL, '172.27.167.242', 'aa:bb:cc:dd:a7:f2');
INSERT INTO public.addresses VALUES (5535, NULL, '172.27.167.243', 'aa:bb:cc:dd:a7:f3');
INSERT INTO public.addresses VALUES (5536, NULL, '172.27.167.244', 'aa:bb:cc:dd:a7:f4');
INSERT INTO public.addresses VALUES (5537, NULL, '172.27.167.245', 'aa:bb:cc:dd:a7:f5');
INSERT INTO public.addresses VALUES (5538, NULL, '172.27.167.246', 'aa:bb:cc:dd:a7:f6');
INSERT INTO public.addresses VALUES (5539, NULL, '172.27.167.247', 'aa:bb:cc:dd:a7:f7');
INSERT INTO public.addresses VALUES (5540, NULL, '172.27.167.248', 'aa:bb:cc:dd:a7:f8');
INSERT INTO public.addresses VALUES (5541, NULL, '172.27.167.249', 'aa:bb:cc:dd:a7:f9');
INSERT INTO public.addresses VALUES (5542, NULL, '172.27.167.250', 'aa:bb:cc:dd:a7:fa');
INSERT INTO public.addresses VALUES (5543, NULL, '172.27.167.251', 'aa:bb:cc:dd:a7:fb');
INSERT INTO public.addresses VALUES (5544, NULL, '172.27.167.252', 'aa:bb:cc:dd:a7:fc');
INSERT INTO public.addresses VALUES (5545, NULL, '172.27.167.253', 'aa:bb:cc:dd:a7:fd');
INSERT INTO public.addresses VALUES (5546, NULL, '172.27.167.254', 'aa:bb:cc:dd:a7:fe');
INSERT INTO public.addresses VALUES (5547, NULL, '172.27.167.255', 'aa:bb:cc:dd:a7:ff');
INSERT INTO public.addresses VALUES (3328, NULL, '172.27.72.84', 'aa:bb:cc:dd:48:54');


--
-- Data for Name: breeds; Type: TABLE DATA; Schema: public; Owner: symfony
--

INSERT INTO public.breeds VALUES (1, 'Ubuntu');
INSERT INTO public.breeds VALUES (2, 'Fedora');
INSERT INTO public.breeds VALUES (3, 'RHEL');


--
-- Data for Name: doctrine_migration_versions; Type: TABLE DATA; Schema: public; Owner: symfony
--



--
-- Data for Name: domains; Type: TABLE DATA; Schema: public; Owner: symfony
--

INSERT INTO public.domains VALUES (0, 'Storage', 'Everything related to storage subsystem, including volume managers, working with block devices, mounting, formatting, etc.');
INSERT INTO public.domains VALUES (1, 'Security', 'SELinux, passwords, kerberos, openssl, file permissions, ACLs, etc.');
INSERT INTO public.domains VALUES (2, 'Network', 'Tasks related to network configuration, monitoring and troubleshooting.');
INSERT INTO public.domains VALUES (5, 'Automation', 'Collection of tasks related to automating repeating work.');
INSERT INTO public.domains VALUES (6, 'Performance', 'Tuning system parameters');
INSERT INTO public.domains VALUES (9, 'System management', 'systemd, processes');
INSERT INTO public.domains VALUES (10, 'Virtualization', 'docker, LXC');
INSERT INTO public.domains VALUES (8, 'Hardware', 'lspci, dmidecode');
INSERT INTO public.domains VALUES (7, 'Software', 'yum, dnf, rpm');
INSERT INTO public.domains VALUES (11, 'Monitoring', 'get system running status and metrics');


--
-- Data for Name: environment_statuses; Type: TABLE DATA; Schema: public; Owner: symfony
--

INSERT INTO public.environment_statuses VALUES (2, 'Created', NULL);
INSERT INTO public.environment_statuses VALUES (5, 'Complete', NULL);
INSERT INTO public.environment_statuses VALUES (6, 'Verified', NULL);
INSERT INTO public.environment_statuses VALUES (7, 'Solved', NULL);
INSERT INTO public.environment_statuses VALUES (1, 'New', 'New Environment entity not linked to Session or Instances');
INSERT INTO public.environment_statuses VALUES (4, 'Skipped', 'User clicked Skipped button during test Session');
INSERT INTO public.environment_statuses VALUES (3, 'Deployed', 'Deployment playbook has been run for the Environment');


--
-- Data for Name: environments; Type: TABLE DATA; Schema: public; Owner: symfony
--

INSERT INTO public.environments VALUES (164, 11, NULL, 200, NULL, 3, 'cb5844a3', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (133, 6, 3, NULL, '2022-10-11 06:11:49', 4, '4726b614', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (184, 10, 3, NULL, '2022-10-11 06:26:09', 4, 'b4b7176c', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (181, 8, 3, NULL, '2022-10-11 06:31:29', 4, '1787cc24', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (132, 6, 1, NULL, '2022-10-06 15:05:34', 6, 'bcd270cd', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (219, 10, NULL, 162, NULL, 3, '37769709', NULL, NULL, 1323, NULL);
INSERT INTO public.environments VALUES (138, 8, 1, NULL, '2022-10-05 13:05:58', 6, 'a338a3c1', '2022-10-05 13:15:25', NULL, NULL, NULL);
INSERT INTO public.environments VALUES (165, 11, NULL, 201, NULL, 3, '6696b08c', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (240, 14, NULL, 231, NULL, 3, '259f3bae', NULL, NULL, 1495, NULL);
INSERT INTO public.environments VALUES (197, 15, NULL, 222, NULL, 3, '40bf112e', NULL, NULL, 1232, NULL);
INSERT INTO public.environments VALUES (134, 7, 1, NULL, '2022-10-06 15:49:03', 4, 'd79ead43', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (220, 12, NULL, 223, NULL, 3, 'cc50bc59', NULL, NULL, 1331, NULL);
INSERT INTO public.environments VALUES (198, 15, NULL, 221, NULL, 3, '2f9646b6', NULL, NULL, 1240, NULL);
INSERT INTO public.environments VALUES (199, 15, NULL, 202, NULL, 3, '1ade31b4', NULL, NULL, 1248, NULL);
INSERT INTO public.environments VALUES (221, 12, NULL, 178, NULL, 3, 'e0345f11', NULL, NULL, 1339, NULL);
INSERT INTO public.environments VALUES (123, 1, 1, NULL, '2022-10-05 13:18:58', 6, 'ffc63a30', '2022-10-05 13:19:53', NULL, NULL, NULL);
INSERT INTO public.environments VALUES (242, 7, NULL, 185, NULL, 3, '31bf7a8b', NULL, NULL, 1511, NULL);
INSERT INTO public.environments VALUES (200, 15, NULL, 179, NULL, 3, '51386493', NULL, NULL, 1256, NULL);
INSERT INTO public.environments VALUES (222, 12, NULL, 181, NULL, 3, '61b1a67c', NULL, NULL, 1347, NULL);
INSERT INTO public.environments VALUES (149, 1, NULL, 170, NULL, 3, 'ead717cc', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (201, 15, NULL, 198, NULL, 3, '1d6599a2', NULL, NULL, 1264, NULL);
INSERT INTO public.environments VALUES (243, 8, NULL, 184, NULL, 3, '44966d27', NULL, NULL, 1519, NULL);
INSERT INTO public.environments VALUES (223, 8, NULL, 224, NULL, 3, 'c3ce4620', NULL, NULL, 1355, NULL);
INSERT INTO public.environments VALUES (168, 13, NULL, 204, NULL, 3, '1bdc3e2f', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (124, 1, NULL, 168, NULL, 3, '6e3cad84', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (143, 10, 1, NULL, '2022-10-05 13:31:41', 6, '272eabfe', '2022-10-05 13:32:12', NULL, NULL, NULL);
INSERT INTO public.environments VALUES (224, 8, NULL, 226, NULL, 3, 'aec9faa4', NULL, NULL, 1363, NULL);
INSERT INTO public.environments VALUES (167, 12, 3, NULL, '2022-10-10 15:45:37', 4, '506d903c', '2022-10-10 15:47:44', NULL, NULL, NULL);
INSERT INTO public.environments VALUES (169, 13, NULL, 205, NULL, 3, '294783c6', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (150, 2, NULL, 164, NULL, 3, 'c5733395', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (127, 2, NULL, 172, NULL, 3, '60221f8a', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (170, 1, NULL, 206, NULL, 3, 'aa21297f', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (144, 9, 1, NULL, '2022-10-05 13:33:12', 6, '73d088c9', '2022-10-05 13:37:06', NULL, NULL, NULL);
INSERT INTO public.environments VALUES (113, 2, NULL, 165, NULL, 3, '3585ed93', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (225, 17, NULL, 232, NULL, 3, '82565af4', NULL, NULL, 1371, NULL);
INSERT INTO public.environments VALUES (171, 1, NULL, 207, NULL, 3, '04bc0d1e', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (245, 1, NULL, 186, NULL, 3, '04165098', NULL, NULL, 1535, NULL);
INSERT INTO public.environments VALUES (151, 2, NULL, 183, NULL, 3, '44c8d268', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (226, 10, NULL, 233, NULL, 3, 'ca603eaa', NULL, NULL, 1379, NULL);
INSERT INTO public.environments VALUES (130, 5, NULL, 175, NULL, 3, '12a8120d', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (148, 1, 1, NULL, '2022-10-18 06:27:25', 6, '84e52529', '2022-10-24 14:02:14', true, NULL, 1541);
INSERT INTO public.environments VALUES (114, 5, 1, NULL, '2022-10-05 13:40:43', 4, '7efa0602', '2022-10-05 13:40:47', NULL, NULL, NULL);
INSERT INTO public.environments VALUES (172, 2, NULL, 208, NULL, 3, '2dfbbadc', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (173, 2, NULL, 209, NULL, 3, '49185777', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (246, 13, NULL, 187, NULL, 3, 'fe976589', NULL, NULL, 1549, NULL);
INSERT INTO public.environments VALUES (152, 5, NULL, 188, NULL, 3, '8a4d74b3', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (228, 12, NULL, 235, NULL, 3, '1576bfff', NULL, NULL, 1395, NULL);
INSERT INTO public.environments VALUES (241, 13, 1, NULL, '2022-10-24 14:41:44', 6, '9efd299b', '2022-10-24 14:43:09', true, 1503, 1553);
INSERT INTO public.environments VALUES (145, 10, 1, NULL, '2022-10-05 13:40:53', 6, '5540e5c6', '2022-10-05 13:42:18', NULL, NULL, NULL);
INSERT INTO public.environments VALUES (174, 5, NULL, 210, NULL, 3, 'a760c4b8', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (229, 14, NULL, 236, NULL, 3, '92713f26', NULL, NULL, 1403, NULL);
INSERT INTO public.environments VALUES (175, 5, NULL, 211, NULL, 3, '4c095b89', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (153, 5, NULL, 189, NULL, 3, '6eec5d16', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (230, 7, NULL, 237, NULL, 3, '967f92a9', NULL, NULL, 1411, NULL);
INSERT INTO public.environments VALUES (146, 11, 1, NULL, '2022-10-05 13:49:02', 6, '4748c4b6', '2022-10-05 13:50:59', true, NULL, NULL);
INSERT INTO public.environments VALUES (176, 6, NULL, 163, NULL, 3, '2e9b0b1f', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (189, 12, 3, NULL, '2022-10-10 15:48:12', 4, '3bf9760b', '2022-10-10 15:48:27', NULL, NULL, NULL);
INSERT INTO public.environments VALUES (105, 5, 1, NULL, '2022-10-05 12:37:18', 4, '653d2a4e', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (177, 6, NULL, 173, NULL, 3, 'd63de90a', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (154, 6, NULL, 190, NULL, 3, '60d70866', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (162, 10, 3, NULL, '2022-10-10 15:44:37', 4, '96ca3e15', '2022-10-10 15:45:07', NULL, NULL, NULL);
INSERT INTO public.environments VALUES (247, 6, NULL, 169, NULL, 3, '0629d438', NULL, NULL, 1561, NULL);
INSERT INTO public.environments VALUES (244, 6, 1, 176, '2022-10-24 19:38:59', 3, '03fbb5a2', NULL, NULL, 1527, NULL);
INSERT INTO public.environments VALUES (231, 8, NULL, 238, NULL, 3, '2fac3003', NULL, NULL, 1419, NULL);
INSERT INTO public.environments VALUES (147, 9, 1, NULL, '2022-10-05 13:52:26', 6, 'b4f040d6', '2022-10-05 14:03:49', false, NULL, NULL);
INSERT INTO public.environments VALUES (122, 1, 1, NULL, '2022-10-05 12:24:00', 6, 'f07a8cb0', '2022-10-05 13:01:45', NULL, NULL, NULL);
INSERT INTO public.environments VALUES (213, 16, NULL, 203, NULL, 3, '30653328', NULL, NULL, 1275, NULL);
INSERT INTO public.environments VALUES (232, 16, NULL, 239, NULL, 3, '57c1caa0', NULL, NULL, 1427, NULL);
INSERT INTO public.environments VALUES (214, 16, NULL, 215, NULL, 3, 'bce12c79', NULL, NULL, 1283, NULL);
INSERT INTO public.environments VALUES (155, 6, NULL, 191, NULL, 3, '9e331682', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (179, 7, NULL, 167, NULL, 3, '6808c335', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (156, 7, NULL, 192, NULL, 3, 'fd422c75', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (158, 8, 3, NULL, '2022-10-10 15:48:34', 4, '6cb28d0f', '2022-10-10 15:50:32', NULL, NULL, NULL);
INSERT INTO public.environments VALUES (157, 7, NULL, 193, NULL, 3, 'a6f22012', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (215, 16, NULL, 194, NULL, 3, 'bc370f79', NULL, NULL, 1291, NULL);
INSERT INTO public.environments VALUES (182, 9, 1, NULL, '2022-10-06 14:53:13', 6, 'c4f63982', '2022-10-18 06:19:47', true, NULL, 1431);
INSERT INTO public.environments VALUES (129, 5, 3, NULL, '2022-10-10 15:43:12', 4, '8caa03a6', '2022-10-10 15:50:52', NULL, NULL, NULL);
INSERT INTO public.environments VALUES (159, 8, NULL, 195, NULL, 3, '25cf13a4', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (216, 17, NULL, 174, NULL, 3, 'dff44c9b', NULL, NULL, 1299, NULL);
INSERT INTO public.environments VALUES (160, 9, NULL, 196, NULL, 3, '253c377c', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (161, 9, NULL, 197, NULL, 3, '0e9d1199', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (183, 9, NULL, 171, NULL, 3, 'efad9cdb', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (188, 12, 3, NULL, '2022-10-10 15:50:57', 4, 'e769cd8b', '2022-10-10 15:51:01', NULL, NULL, NULL);
INSERT INTO public.environments VALUES (233, 9, NULL, 240, NULL, 3, '03d73c1a', NULL, NULL, 1439, NULL);
INSERT INTO public.environments VALUES (217, 17, NULL, 214, NULL, 3, '1f966672', NULL, NULL, 1307, NULL);
INSERT INTO public.environments VALUES (234, 16, NULL, 241, NULL, 3, 'ea48ca4d', NULL, NULL, 1447, NULL);
INSERT INTO public.environments VALUES (163, 10, NULL, 199, NULL, 3, 'd9292de0', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (227, 9, 1, NULL, '2022-10-18 06:24:29', 4, '884561c6', '2022-10-18 06:27:21', NULL, 1387, NULL);
INSERT INTO public.environments VALUES (185, 10, NULL, 180, NULL, 3, 'bd2a9709', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (218, 17, NULL, 225, NULL, 3, '2e77f004', NULL, NULL, 1315, NULL);
INSERT INTO public.environments VALUES (186, 11, NULL, 212, NULL, 3, '4dea6152', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (187, 11, NULL, 213, NULL, 3, '0da71ee7', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (190, 13, NULL, 216, NULL, 3, '8e7277e7', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (235, 17, NULL, 242, NULL, 3, '73715a0f', NULL, NULL, 1455, NULL);
INSERT INTO public.environments VALUES (191, 13, NULL, 217, NULL, 3, 'd3550e94', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (236, 11, NULL, 227, NULL, 3, 'ffe24e99', NULL, NULL, 1463, NULL);
INSERT INTO public.environments VALUES (139, 8, 1, NULL, '2022-10-05 14:05:00', 4, '7ec4fc26', '2022-10-06 14:49:57', NULL, NULL, NULL);
INSERT INTO public.environments VALUES (180, 8, 1, NULL, '2022-10-06 14:50:01', 6, '2b69cf82', '2022-10-06 14:50:35', true, NULL, NULL);
INSERT INTO public.environments VALUES (192, 14, NULL, 218, NULL, 3, 'e87030a8', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (237, 10, NULL, 228, NULL, 3, '6121d6d1', NULL, NULL, 1471, NULL);
INSERT INTO public.environments VALUES (193, 14, NULL, 219, NULL, 3, '23c48fcb', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (194, 14, NULL, 220, NULL, 3, 'ba74c18e', NULL, NULL, NULL, NULL);
INSERT INTO public.environments VALUES (238, 9, NULL, 229, NULL, 3, '0fea6db4', NULL, NULL, 1479, NULL);
INSERT INTO public.environments VALUES (239, 12, NULL, 230, NULL, 3, '28265da0', NULL, NULL, 1487, NULL);


--
-- Data for Name: hardware_profiles; Type: TABLE DATA; Schema: public; Owner: symfony
--

INSERT INTO public.hardware_profiles VALUES (3, true, 'VM with 1 CPU and 1G memory', 1500, 'baseball', false);
INSERT INTO public.hardware_profiles VALUES (0, false, 'Container, 10% CPU allowance, 256MB memory, no swap, 1GB root', 10, 'cricket', true);
INSERT INTO public.hardware_profiles VALUES (2, true, 'VM with 1 CPU and 512M memory', 1000, 'tennis', false);
INSERT INTO public.hardware_profiles VALUES (1, false, 'Container, 10% CPU allowance, 256MB memory, 128MB swap, 1GB root', 20, 'soccer', true);


--
-- Data for Name: instance_statuses; Type: TABLE DATA; Schema: public; Owner: symfony
--

INSERT INTO public.instance_statuses VALUES (2, 'Bound', NULL);
INSERT INTO public.instance_statuses VALUES (8, 'Sleeping', 'Stopped LXC instance bound to an active environment');
INSERT INTO public.instance_statuses VALUES (5, 'Started', 'Unbound started LXC instance, ready for allocation');
INSERT INTO public.instance_statuses VALUES (6, 'New', 'New Instance entity not linked to an actual LXC instance');
INSERT INTO public.instance_statuses VALUES (7, 'Running', 'Started LXC instance bound to an active Environment');
INSERT INTO public.instance_statuses VALUES (4, 'Stopped', 'Unbound stopped LXC instance, ready for allocation');


--
-- Data for Name: instance_types; Type: TABLE DATA; Schema: public; Owner: symfony
--

INSERT INTO public.instance_types VALUES (110, 2, 0);
INSERT INTO public.instance_types VALUES (111, 7, 0);
INSERT INTO public.instance_types VALUES (112, 2, 1);
INSERT INTO public.instance_types VALUES (113, 7, 1);


--
-- Data for Name: instances; Type: TABLE DATA; Schema: public; Owner: symfony
--

INSERT INTO public.instances VALUES (185, 110, '2022-09-28 12:55:53', 5, 'premium-magpie');
INSERT INTO public.instances VALUES (184, 110, '2022-09-28 12:55:49', 5, 'pleasing-seahorse');
INSERT INTO public.instances VALUES (176, 110, '2022-09-28 12:55:14', 5, 'concise-anteater');
INSERT INTO public.instances VALUES (186, 110, '2022-09-28 12:55:58', 5, 'crisp-meerkat');
INSERT INTO public.instances VALUES (187, 110, '2022-09-28 12:56:02', 5, 'amusing-arachnid');
INSERT INTO public.instances VALUES (169, 110, '2022-09-28 08:53:44', 5, 'alive-adder');
INSERT INTO public.instances VALUES (177, 110, '2022-09-28 12:55:18', 4, 'witty-bream');
INSERT INTO public.instances VALUES (166, 110, '2022-09-28 08:53:28', 5, 'major-fowl');
INSERT INTO public.instances VALUES (165, 110, '2022-09-28 08:53:24', 5, 'chief-rat');
INSERT INTO public.instances VALUES (222, 110, '2022-09-30 19:58:28', 5, 'included-scorpion');
INSERT INTO public.instances VALUES (170, 110, '2022-09-28 08:53:48', 5, 'sharp-cricket');
INSERT INTO public.instances VALUES (164, 110, '2022-09-28 08:53:20', 5, 'upward-seahorse');
INSERT INTO public.instances VALUES (183, 110, '2022-09-28 12:55:44', 5, 'verified-tadpole');
INSERT INTO public.instances VALUES (188, 110, '2022-09-28 12:56:07', 5, 'driving-leech');
INSERT INTO public.instances VALUES (189, 110, '2022-09-28 12:56:12', 5, 'valid-kingfish');
INSERT INTO public.instances VALUES (190, 110, '2022-09-28 12:56:16', 5, 'excited-shrew');
INSERT INTO public.instances VALUES (191, 110, '2022-09-28 12:56:21', 5, 'electric-jackass');
INSERT INTO public.instances VALUES (192, 110, '2022-09-28 13:00:36', 5, 'accurate-kid');
INSERT INTO public.instances VALUES (193, 110, '2022-09-28 13:00:39', 5, 'game-parakeet');
INSERT INTO public.instances VALUES (195, 110, '2022-09-28 13:00:48', 5, 'complete-lobster');
INSERT INTO public.instances VALUES (196, 110, '2022-09-28 13:00:52', 5, 'enabled-lab');
INSERT INTO public.instances VALUES (243, 110, '2022-10-18 05:56:45', 5, 'first-akita');
INSERT INTO public.instances VALUES (244, 110, '2022-10-18 05:56:49', 5, 'in-airedale');
INSERT INTO public.instances VALUES (245, 110, '2022-10-18 05:56:54', 5, 'rich-vervet');
INSERT INTO public.instances VALUES (246, 110, '2022-10-18 05:56:59', 5, 'humorous-goshawk');
INSERT INTO public.instances VALUES (247, 110, '2022-10-18 05:57:04', 5, 'composed-cockatoo');
INSERT INTO public.instances VALUES (248, 110, '2022-10-18 05:57:09', 5, 'boss-elk');
INSERT INTO public.instances VALUES (249, 110, '2022-10-18 05:57:14', 5, 'super-eel');
INSERT INTO public.instances VALUES (250, 110, '2022-10-18 05:57:21', 5, 'destined-joey');
INSERT INTO public.instances VALUES (251, 110, '2022-10-18 05:57:30', 5, 'adequate-elk');
INSERT INTO public.instances VALUES (252, 110, '2022-10-18 05:57:37', 5, 'welcomed-doberman');
INSERT INTO public.instances VALUES (253, 110, '2022-10-18 05:57:42', 5, 'viable-lamprey');
INSERT INTO public.instances VALUES (254, 110, '2022-10-18 05:57:47', 5, 'communal-beagle');
INSERT INTO public.instances VALUES (255, 110, '2022-10-18 05:57:53', 5, 'vocal-anteater');
INSERT INTO public.instances VALUES (256, 110, '2022-10-18 05:57:59', 5, 'actual-buffalo');
INSERT INTO public.instances VALUES (257, 110, '2022-10-18 05:58:06', 5, 'sound-jay');
INSERT INTO public.instances VALUES (258, 110, '2022-10-18 05:58:11', 5, 'hardy-hippo');
INSERT INTO public.instances VALUES (259, 110, '2022-10-18 05:58:21', 5, 'fair-bullfrog');
INSERT INTO public.instances VALUES (260, 110, '2022-10-18 05:58:26', 5, 'legal-flamingo');
INSERT INTO public.instances VALUES (261, 110, '2022-10-18 05:58:31', 5, 'champion-jawfish');
INSERT INTO public.instances VALUES (182, 110, '2022-09-28 12:55:40', 5, 'trusting-escargot');
INSERT INTO public.instances VALUES (234, 110, '2022-10-18 05:55:12', 5, 'improved-tapir');
INSERT INTO public.instances VALUES (197, 110, '2022-09-28 13:00:57', 5, 'adapting-killdeer');
INSERT INTO public.instances VALUES (199, 110, '2022-09-28 13:01:06', 5, 'prompt-goblin');
INSERT INTO public.instances VALUES (200, 110, '2022-09-28 13:01:11', 5, 'guiding-badger');
INSERT INTO public.instances VALUES (201, 110, '2022-09-28 13:01:15', 5, 'sharing-imp');
INSERT INTO public.instances VALUES (204, 110, '2022-09-28 13:01:29', 5, 'noble-crawdad');
INSERT INTO public.instances VALUES (205, 110, '2022-09-28 13:01:34', 5, 'obliging-amoeba');
INSERT INTO public.instances VALUES (206, 110, '2022-09-28 13:01:38', 5, 'fun-bunny');
INSERT INTO public.instances VALUES (207, 110, '2022-09-28 13:01:43', 5, 'pro-sole');
INSERT INTO public.instances VALUES (208, 110, '2022-09-28 13:01:47', 5, 'organic-raptor');
INSERT INTO public.instances VALUES (209, 110, '2022-09-28 13:01:52', 5, 'trusting-lemming');
INSERT INTO public.instances VALUES (210, 110, '2022-09-28 13:01:57', 5, 'merry-longhorn');
INSERT INTO public.instances VALUES (211, 110, '2022-09-28 13:02:02', 5, 'nice-viper');
INSERT INTO public.instances VALUES (163, 110, '2022-09-28 08:53:17', 5, 'steady-gannet');
INSERT INTO public.instances VALUES (173, 110, '2022-09-28 12:55:01', 5, 'measured-osprey');
INSERT INTO public.instances VALUES (167, 110, '2022-09-28 08:53:32', 5, 'thankful-molly');
INSERT INTO public.instances VALUES (221, 110, '2022-09-30 19:58:23', 5, 'amusing-roughy');
INSERT INTO public.instances VALUES (202, 110, '2022-09-28 13:01:20', 5, 'profound-rattler');
INSERT INTO public.instances VALUES (179, 110, '2022-09-28 12:55:27', 5, 'chief-moth');
INSERT INTO public.instances VALUES (198, 110, '2022-09-28 13:01:01', 5, 'prompt-koi');
INSERT INTO public.instances VALUES (168, 110, '2022-09-28 08:53:40', 5, 'inspired-bass');
INSERT INTO public.instances VALUES (203, 110, '2022-09-28 13:01:24', 5, 'related-dinosaur');
INSERT INTO public.instances VALUES (171, 110, '2022-09-28 08:53:52', 5, 'sunny-hedgehog');
INSERT INTO public.instances VALUES (172, 110, '2022-09-28 12:54:57', 5, 'finer-piglet');
INSERT INTO public.instances VALUES (180, 110, '2022-09-28 12:55:31', 5, 'vocal-ghoul');
INSERT INTO public.instances VALUES (175, 110, '2022-09-28 12:55:09', 5, 'crucial-guppy');
INSERT INTO public.instances VALUES (212, 110, '2022-09-30 19:57:45', 5, 'uncommon-hen');
INSERT INTO public.instances VALUES (213, 110, '2022-09-30 19:57:49', 5, 'stirred-minnow');
INSERT INTO public.instances VALUES (216, 110, '2022-09-30 19:58:01', 5, 'helping-wallaby');
INSERT INTO public.instances VALUES (217, 110, '2022-09-30 19:58:06', 5, 'discrete-werewolf');
INSERT INTO public.instances VALUES (215, 110, '2022-09-30 19:57:57', 5, 'magical-bulldog');
INSERT INTO public.instances VALUES (194, 110, '2022-09-28 13:00:44', 5, 'related-hare');
INSERT INTO public.instances VALUES (174, 110, '2022-09-28 12:55:05', 5, 'smart-emu');
INSERT INTO public.instances VALUES (214, 110, '2022-09-30 19:57:53', 5, 'climbing-marten');
INSERT INTO public.instances VALUES (225, 110, '2022-09-30 19:58:42', 5, 'valid-rabbit');
INSERT INTO public.instances VALUES (162, 110, '2022-09-28 08:52:52', 5, 'well-lobster');
INSERT INTO public.instances VALUES (223, 110, '2022-09-30 19:58:32', 5, 'amazing-bluebird');
INSERT INTO public.instances VALUES (178, 110, '2022-09-28 12:55:23', 5, 'darling-reindeer');
INSERT INTO public.instances VALUES (181, 110, '2022-09-28 12:55:35', 5, 'modern-swine');
INSERT INTO public.instances VALUES (224, 110, '2022-09-30 19:58:37', 5, 'lucky-glider');
INSERT INTO public.instances VALUES (226, 110, '2022-09-30 19:58:47', 5, 'feasible-anteater');
INSERT INTO public.instances VALUES (218, 110, '2022-09-30 19:58:10', 5, 'free-werewolf');
INSERT INTO public.instances VALUES (219, 110, '2022-09-30 19:58:15', 5, 'pure-giraffe');
INSERT INTO public.instances VALUES (220, 110, '2022-09-30 19:58:19', 5, 'skilled-heron');
INSERT INTO public.instances VALUES (232, 110, '2022-10-18 05:55:05', 5, 'evolving-bluejay');
INSERT INTO public.instances VALUES (233, 110, '2022-10-18 05:55:08', 5, 'stunning-elephant');
INSERT INTO public.instances VALUES (235, 110, '2022-10-18 05:55:16', 5, 'relaxed-piglet');
INSERT INTO public.instances VALUES (236, 110, '2022-10-18 05:55:20', 5, 'saved-gator');
INSERT INTO public.instances VALUES (237, 110, '2022-10-18 05:55:24', 5, 'tough-raven');
INSERT INTO public.instances VALUES (238, 110, '2022-10-18 05:55:29', 5, 'popular-tadpole');
INSERT INTO public.instances VALUES (239, 110, '2022-10-18 05:55:35', 5, 'main-cub');
INSERT INTO public.instances VALUES (240, 110, '2022-10-18 05:55:40', 5, 'guided-yeti');
INSERT INTO public.instances VALUES (241, 110, '2022-10-18 05:55:45', 5, 'heroic-falcon');
INSERT INTO public.instances VALUES (242, 110, '2022-10-18 05:56:41', 5, 'profound-glowworm');
INSERT INTO public.instances VALUES (227, 110, '2022-09-30 19:58:51', 5, 'upright-basilisk');
INSERT INTO public.instances VALUES (228, 110, '2022-09-30 19:58:56', 5, 'intent-bluebird');
INSERT INTO public.instances VALUES (229, 110, '2022-09-30 19:59:01', 5, 'creative-hog');
INSERT INTO public.instances VALUES (230, 110, '2022-09-30 19:59:06', 5, 'balanced-zebra');
INSERT INTO public.instances VALUES (231, 110, '2022-09-30 19:59:10', 5, 'wondrous-flamingo');


--
-- Data for Name: messenger_messages; Type: TABLE DATA; Schema: public; Owner: symfony
--



--
-- Data for Name: operating_systems; Type: TABLE DATA; Schema: public; Owner: symfony
--

INSERT INTO public.operating_systems VALUES (2, '18.04 LTS', 'Major version', true, 1, 'bionic');
INSERT INTO public.operating_systems VALUES (5, '33', 'Older version', false, 2, 'f33');
INSERT INTO public.operating_systems VALUES (4, '35', 'New version', false, 2, 'f35');
INSERT INTO public.operating_systems VALUES (3, '22.04 LTS', 'Modern version', false, 1, 'jammy');
INSERT INTO public.operating_systems VALUES (7, '20.04 LTS', 'Current release', true, 1, 'focal');


--
-- Data for Name: ports; Type: TABLE DATA; Schema: public; Owner: symfony
--

INSERT INTO public.ports VALUES (2921, 8120, 3759);
INSERT INTO public.ports VALUES (2927, 8126, 3764);
INSERT INTO public.ports VALUES (2934, 8133, 3769);
INSERT INTO public.ports VALUES (2940, 8139, 3774);
INSERT INTO public.ports VALUES (2946, 8145, 3779);
INSERT INTO public.ports VALUES (2952, 8151, 3784);
INSERT INTO public.ports VALUES (2959, 8158, 3789);
INSERT INTO public.ports VALUES (2965, 8164, 3794);
INSERT INTO public.ports VALUES (2971, 8170, 3799);
INSERT INTO public.ports VALUES (2977, 8176, 3804);
INSERT INTO public.ports VALUES (2984, 8183, 3809);
INSERT INTO public.ports VALUES (2990, 8189, 3814);
INSERT INTO public.ports VALUES (2996, 8195, 3819);
INSERT INTO public.ports VALUES (3002, 8201, 3824);
INSERT INTO public.ports VALUES (3009, 8208, 3829);
INSERT INTO public.ports VALUES (3016, 8215, 3834);
INSERT INTO public.ports VALUES (5909, 7940, 3839);
INSERT INTO public.ports VALUES (5914, 7945, 3844);
INSERT INTO public.ports VALUES (5919, 7950, 3849);
INSERT INTO public.ports VALUES (5924, 7955, 3854);
INSERT INTO public.ports VALUES (5929, 7960, 3859);
INSERT INTO public.ports VALUES (5934, 7965, 3864);
INSERT INTO public.ports VALUES (5937, 7968, 3867);
INSERT INTO public.ports VALUES (5938, 7969, 3868);
INSERT INTO public.ports VALUES (5939, 7970, 3869);
INSERT INTO public.ports VALUES (5940, 7971, 3870);
INSERT INTO public.ports VALUES (5941, 7972, 3871);
INSERT INTO public.ports VALUES (5942, 7973, 3872);
INSERT INTO public.ports VALUES (5943, 7974, 3873);
INSERT INTO public.ports VALUES (5944, 7975, 3874);
INSERT INTO public.ports VALUES (5945, 7976, 3875);
INSERT INTO public.ports VALUES (5946, 7977, 3876);
INSERT INTO public.ports VALUES (5947, 7978, 3877);
INSERT INTO public.ports VALUES (5948, 7979, 3878);
INSERT INTO public.ports VALUES (5949, 7980, 3879);
INSERT INTO public.ports VALUES (5950, 7981, 3880);
INSERT INTO public.ports VALUES (5951, 7982, 3881);
INSERT INTO public.ports VALUES (5952, 7983, 3882);
INSERT INTO public.ports VALUES (5953, 7984, 3883);
INSERT INTO public.ports VALUES (5954, 7985, 3884);
INSERT INTO public.ports VALUES (5955, 7986, 3885);
INSERT INTO public.ports VALUES (5956, 7987, 3886);
INSERT INTO public.ports VALUES (5957, 7988, 3887);
INSERT INTO public.ports VALUES (5958, 7989, 3888);
INSERT INTO public.ports VALUES (5959, 7990, 3889);
INSERT INTO public.ports VALUES (5960, 7991, 3890);
INSERT INTO public.ports VALUES (5961, 7992, 3891);
INSERT INTO public.ports VALUES (5962, 7993, 3892);
INSERT INTO public.ports VALUES (5963, 7994, 3893);
INSERT INTO public.ports VALUES (5964, 7995, 3894);
INSERT INTO public.ports VALUES (5965, 7996, 3895);
INSERT INTO public.ports VALUES (5966, 7997, 3896);
INSERT INTO public.ports VALUES (5967, 7998, 3897);
INSERT INTO public.ports VALUES (3067, 8266, 3898);
INSERT INTO public.ports VALUES (3072, 8271, 3902);
INSERT INTO public.ports VALUES (3079, 8278, 3907);
INSERT INTO public.ports VALUES (3086, 8285, 3912);
INSERT INTO public.ports VALUES (3093, 8292, 3917);
INSERT INTO public.ports VALUES (3100, 8299, 3922);
INSERT INTO public.ports VALUES (5969, 8000, 3927);
INSERT INTO public.ports VALUES (5974, 8005, 3932);
INSERT INTO public.ports VALUES (5979, 8010, 3937);
INSERT INTO public.ports VALUES (5984, 8015, 3942);
INSERT INTO public.ports VALUES (5989, 8020, 3947);
INSERT INTO public.ports VALUES (5994, 8025, 3952);
INSERT INTO public.ports VALUES (5999, 8030, 3957);
INSERT INTO public.ports VALUES (6004, 8035, 3962);
INSERT INTO public.ports VALUES (6009, 8040, 3967);
INSERT INTO public.ports VALUES (4710, 9073, 3972);
INSERT INTO public.ports VALUES (4728, 9091, 3977);
INSERT INTO public.ports VALUES (4740, 9103, 3982);
INSERT INTO public.ports VALUES (4745, 9108, 3987);
INSERT INTO public.ports VALUES (4748, 9111, 3990);
INSERT INTO public.ports VALUES (4749, 9112, 3991);
INSERT INTO public.ports VALUES (4750, 9113, 3992);
INSERT INTO public.ports VALUES (4751, 9114, 3993);
INSERT INTO public.ports VALUES (4752, 9115, 3994);
INSERT INTO public.ports VALUES (4753, 9116, 3995);
INSERT INTO public.ports VALUES (4754, 9117, 3996);
INSERT INTO public.ports VALUES (4755, 9118, 3997);
INSERT INTO public.ports VALUES (4756, 9119, 3998);
INSERT INTO public.ports VALUES (4757, 9120, 3999);
INSERT INTO public.ports VALUES (4758, 9121, 4000);
INSERT INTO public.ports VALUES (4759, 9122, 4001);
INSERT INTO public.ports VALUES (4760, 9123, 4002);
INSERT INTO public.ports VALUES (4761, 9124, 4003);
INSERT INTO public.ports VALUES (4762, 9125, 4004);
INSERT INTO public.ports VALUES (4763, 9126, 4005);
INSERT INTO public.ports VALUES (4764, 9127, 4006);
INSERT INTO public.ports VALUES (4765, 9128, 4007);
INSERT INTO public.ports VALUES (4766, 9129, 4008);
INSERT INTO public.ports VALUES (6011, 8042, 4009);
INSERT INTO public.ports VALUES (6012, 8043, 4010);
INSERT INTO public.ports VALUES (6013, 8044, 4011);
INSERT INTO public.ports VALUES (6492, 9991, 4856);
INSERT INTO public.ports VALUES (6497, 9996, 4861);
INSERT INTO public.ports VALUES (5030, 9393, 4865);
INSERT INTO public.ports VALUES (5031, 9394, 4866);
INSERT INTO public.ports VALUES (5038, 9401, 4871);
INSERT INTO public.ports VALUES (5045, 9408, 4876);
INSERT INTO public.ports VALUES (4962, 9325, 4881);
INSERT INTO public.ports VALUES (4969, 9332, 4886);
INSERT INTO public.ports VALUES (5238, 9601, 3254);
INSERT INTO public.ports VALUES (5239, 9602, 3255);
INSERT INTO public.ports VALUES (5240, 9603, 3256);
INSERT INTO public.ports VALUES (5242, 9605, 3257);
INSERT INTO public.ports VALUES (5245, 9608, 3259);
INSERT INTO public.ports VALUES (5246, 9609, 3260);
INSERT INTO public.ports VALUES (5247, 9610, 3261);
INSERT INTO public.ports VALUES (5249, 9612, 3262);
INSERT INTO public.ports VALUES (5251, 9614, 3264);
INSERT INTO public.ports VALUES (5253, 9616, 3265);
INSERT INTO public.ports VALUES (5254, 9617, 3266);
INSERT INTO public.ports VALUES (5256, 9619, 3267);
INSERT INTO public.ports VALUES (5258, 9621, 3269);
INSERT INTO public.ports VALUES (5260, 9623, 3270);
INSERT INTO public.ports VALUES (5261, 9624, 3271);
INSERT INTO public.ports VALUES (5262, 9625, 3272);
INSERT INTO public.ports VALUES (5265, 9628, 3274);
INSERT INTO public.ports VALUES (5267, 9630, 3275);
INSERT INTO public.ports VALUES (5268, 9631, 3276);
INSERT INTO public.ports VALUES (5269, 9632, 3277);
INSERT INTO public.ports VALUES (5272, 9635, 3279);
INSERT INTO public.ports VALUES (5273, 9636, 3280);
INSERT INTO public.ports VALUES (5275, 9638, 3281);
INSERT INTO public.ports VALUES (5637, 7600, 3282);
INSERT INTO public.ports VALUES (5639, 7602, 3284);
INSERT INTO public.ports VALUES (5640, 7603, 3285);
INSERT INTO public.ports VALUES (5641, 7604, 3286);
INSERT INTO public.ports VALUES (5642, 7605, 3287);
INSERT INTO public.ports VALUES (5644, 7607, 3289);
INSERT INTO public.ports VALUES (5645, 7608, 3290);
INSERT INTO public.ports VALUES (5646, 7609, 3291);
INSERT INTO public.ports VALUES (5647, 7610, 3292);
INSERT INTO public.ports VALUES (5649, 7612, 3294);
INSERT INTO public.ports VALUES (5650, 7613, 3295);
INSERT INTO public.ports VALUES (5651, 7614, 3296);
INSERT INTO public.ports VALUES (5652, 7615, 3297);
INSERT INTO public.ports VALUES (5654, 7617, 3299);
INSERT INTO public.ports VALUES (5655, 7618, 3300);
INSERT INTO public.ports VALUES (5656, 7619, 3301);
INSERT INTO public.ports VALUES (5657, 7620, 3302);
INSERT INTO public.ports VALUES (5659, 7622, 3304);
INSERT INTO public.ports VALUES (5660, 7623, 3305);
INSERT INTO public.ports VALUES (5661, 7624, 3306);
INSERT INTO public.ports VALUES (5662, 7625, 3307);
INSERT INTO public.ports VALUES (5664, 7627, 3309);
INSERT INTO public.ports VALUES (5665, 7628, 3310);
INSERT INTO public.ports VALUES (5666, 7629, 3311);
INSERT INTO public.ports VALUES (5667, 7630, 3312);
INSERT INTO public.ports VALUES (5669, 7632, 3314);
INSERT INTO public.ports VALUES (5670, 7633, 3315);
INSERT INTO public.ports VALUES (5671, 7634, 3316);
INSERT INTO public.ports VALUES (5672, 7635, 3317);
INSERT INTO public.ports VALUES (5674, 7637, 3319);
INSERT INTO public.ports VALUES (5675, 7638, 3320);
INSERT INTO public.ports VALUES (5676, 7639, 3321);
INSERT INTO public.ports VALUES (5677, 7640, 3322);
INSERT INTO public.ports VALUES (5679, 7642, 3324);
INSERT INTO public.ports VALUES (5680, 7643, 3325);
INSERT INTO public.ports VALUES (5681, 7644, 3326);
INSERT INTO public.ports VALUES (5682, 7645, 3327);
INSERT INTO public.ports VALUES (5684, 7647, 3329);
INSERT INTO public.ports VALUES (5685, 7648, 3330);
INSERT INTO public.ports VALUES (5686, 7649, 3331);
INSERT INTO public.ports VALUES (5687, 7650, 3332);
INSERT INTO public.ports VALUES (5689, 7652, 3334);
INSERT INTO public.ports VALUES (5690, 7653, 3335);
INSERT INTO public.ports VALUES (6014, 8045, 4012);
INSERT INTO public.ports VALUES (6015, 8046, 4013);
INSERT INTO public.ports VALUES (6016, 8047, 4014);
INSERT INTO public.ports VALUES (6017, 8048, 4015);
INSERT INTO public.ports VALUES (6018, 8049, 4016);
INSERT INTO public.ports VALUES (6019, 8050, 4017);
INSERT INTO public.ports VALUES (6020, 8051, 4018);
INSERT INTO public.ports VALUES (6021, 8052, 4019);
INSERT INTO public.ports VALUES (6022, 8053, 4020);
INSERT INTO public.ports VALUES (6023, 8054, 4021);
INSERT INTO public.ports VALUES (6024, 8055, 4022);
INSERT INTO public.ports VALUES (6025, 8056, 4023);
INSERT INTO public.ports VALUES (6026, 8057, 4024);
INSERT INTO public.ports VALUES (6027, 8058, 4025);
INSERT INTO public.ports VALUES (6028, 8059, 4026);
INSERT INTO public.ports VALUES (6029, 8060, 4027);
INSERT INTO public.ports VALUES (6030, 8061, 4028);
INSERT INTO public.ports VALUES (6031, 8062, 4029);
INSERT INTO public.ports VALUES (6032, 8063, 4030);
INSERT INTO public.ports VALUES (6033, 8064, 4031);
INSERT INTO public.ports VALUES (6034, 8065, 4032);
INSERT INTO public.ports VALUES (6035, 8066, 4033);
INSERT INTO public.ports VALUES (6036, 8067, 4034);
INSERT INTO public.ports VALUES (6037, 8068, 4035);
INSERT INTO public.ports VALUES (6038, 8069, 4036);
INSERT INTO public.ports VALUES (6039, 8070, 4037);
INSERT INTO public.ports VALUES (6040, 8071, 4038);
INSERT INTO public.ports VALUES (6041, 8072, 4039);
INSERT INTO public.ports VALUES (6042, 8073, 4040);
INSERT INTO public.ports VALUES (6043, 8074, 4041);
INSERT INTO public.ports VALUES (6044, 8075, 4042);
INSERT INTO public.ports VALUES (6045, 8076, 4043);
INSERT INTO public.ports VALUES (6046, 8077, 4044);
INSERT INTO public.ports VALUES (6047, 8078, 4045);
INSERT INTO public.ports VALUES (6048, 8079, 4046);
INSERT INTO public.ports VALUES (6049, 8080, 4047);
INSERT INTO public.ports VALUES (6050, 8081, 4048);
INSERT INTO public.ports VALUES (6051, 8082, 4049);
INSERT INTO public.ports VALUES (6052, 8083, 4050);
INSERT INTO public.ports VALUES (6053, 8084, 4051);
INSERT INTO public.ports VALUES (6054, 8085, 4052);
INSERT INTO public.ports VALUES (6055, 8086, 4053);
INSERT INTO public.ports VALUES (6056, 8087, 4054);
INSERT INTO public.ports VALUES (6057, 8088, 4055);
INSERT INTO public.ports VALUES (6058, 8089, 4056);
INSERT INTO public.ports VALUES (6059, 8090, 4057);
INSERT INTO public.ports VALUES (6060, 8091, 4058);
INSERT INTO public.ports VALUES (6061, 8092, 4059);
INSERT INTO public.ports VALUES (6062, 8093, 4060);
INSERT INTO public.ports VALUES (6063, 8094, 4061);
INSERT INTO public.ports VALUES (6064, 8095, 4062);
INSERT INTO public.ports VALUES (6065, 8096, 4063);
INSERT INTO public.ports VALUES (6066, 8097, 4064);
INSERT INTO public.ports VALUES (6067, 8098, 4065);
INSERT INTO public.ports VALUES (6068, 8099, 4066);
INSERT INTO public.ports VALUES (6069, 8100, 4067);
INSERT INTO public.ports VALUES (6070, 8101, 4068);
INSERT INTO public.ports VALUES (6071, 8102, 4069);
INSERT INTO public.ports VALUES (6072, 8103, 4070);
INSERT INTO public.ports VALUES (6073, 8104, 4071);
INSERT INTO public.ports VALUES (6074, 8105, 4072);
INSERT INTO public.ports VALUES (6075, 8106, 4073);
INSERT INTO public.ports VALUES (6076, 8107, 4074);
INSERT INTO public.ports VALUES (6077, 8108, 4075);
INSERT INTO public.ports VALUES (6078, 8109, 4076);
INSERT INTO public.ports VALUES (6079, 8110, 4077);
INSERT INTO public.ports VALUES (6080, 8111, 4078);
INSERT INTO public.ports VALUES (6081, 8300, 4079);
INSERT INTO public.ports VALUES (6082, 8301, 4080);
INSERT INTO public.ports VALUES (6083, 8302, 4081);
INSERT INTO public.ports VALUES (6084, 8303, 4082);
INSERT INTO public.ports VALUES (6085, 8304, 4083);
INSERT INTO public.ports VALUES (6086, 8305, 4084);
INSERT INTO public.ports VALUES (6087, 8306, 4085);
INSERT INTO public.ports VALUES (6088, 8307, 4086);
INSERT INTO public.ports VALUES (6089, 8308, 4087);
INSERT INTO public.ports VALUES (6090, 8309, 4088);
INSERT INTO public.ports VALUES (6091, 8310, 4089);
INSERT INTO public.ports VALUES (6092, 8311, 4090);
INSERT INTO public.ports VALUES (6093, 8312, 4091);
INSERT INTO public.ports VALUES (6094, 8313, 4092);
INSERT INTO public.ports VALUES (6095, 8314, 4093);
INSERT INTO public.ports VALUES (6096, 8315, 4094);
INSERT INTO public.ports VALUES (6097, 8316, 4095);
INSERT INTO public.ports VALUES (6098, 8317, 4096);
INSERT INTO public.ports VALUES (6099, 8318, 4097);
INSERT INTO public.ports VALUES (6100, 8319, 4098);
INSERT INTO public.ports VALUES (6101, 8320, 4099);
INSERT INTO public.ports VALUES (6102, 8321, 4100);
INSERT INTO public.ports VALUES (6103, 8322, 4101);
INSERT INTO public.ports VALUES (6104, 8323, 4102);
INSERT INTO public.ports VALUES (6105, 8324, 4103);
INSERT INTO public.ports VALUES (6106, 8325, 4104);
INSERT INTO public.ports VALUES (6107, 8326, 4105);
INSERT INTO public.ports VALUES (6108, 8327, 4106);
INSERT INTO public.ports VALUES (6109, 8328, 4107);
INSERT INTO public.ports VALUES (6110, 8329, 4108);
INSERT INTO public.ports VALUES (6111, 8330, 4109);
INSERT INTO public.ports VALUES (6112, 8331, 4110);
INSERT INTO public.ports VALUES (6113, 8332, 4111);
INSERT INTO public.ports VALUES (6114, 8333, 4112);
INSERT INTO public.ports VALUES (6115, 8334, 4113);
INSERT INTO public.ports VALUES (2918, 8117, 3463);
INSERT INTO public.ports VALUES (2923, 8122, 3464);
INSERT INTO public.ports VALUES (2928, 8127, 3465);
INSERT INTO public.ports VALUES (2938, 8137, 3467);
INSERT INTO public.ports VALUES (2943, 8142, 3468);
INSERT INTO public.ports VALUES (2948, 8147, 3469);
INSERT INTO public.ports VALUES (2953, 8152, 3470);
INSERT INTO public.ports VALUES (2963, 8162, 3472);
INSERT INTO public.ports VALUES (2968, 8167, 3473);
INSERT INTO public.ports VALUES (2973, 8172, 3474);
INSERT INTO public.ports VALUES (2978, 8177, 3475);
INSERT INTO public.ports VALUES (2988, 8187, 3477);
INSERT INTO public.ports VALUES (2993, 8192, 3478);
INSERT INTO public.ports VALUES (2998, 8197, 3479);
INSERT INTO public.ports VALUES (3003, 8202, 3480);
INSERT INTO public.ports VALUES (3011, 8210, 3482);
INSERT INTO public.ports VALUES (3014, 8213, 3483);
INSERT INTO public.ports VALUES (3018, 8217, 3484);
INSERT INTO public.ports VALUES (3021, 8220, 3485);
INSERT INTO public.ports VALUES (3023, 8222, 3487);
INSERT INTO public.ports VALUES (3024, 8223, 3488);
INSERT INTO public.ports VALUES (3025, 8224, 3489);
INSERT INTO public.ports VALUES (3026, 8225, 3490);
INSERT INTO public.ports VALUES (3028, 8227, 3492);
INSERT INTO public.ports VALUES (3029, 8228, 3493);
INSERT INTO public.ports VALUES (3030, 8229, 3494);
INSERT INTO public.ports VALUES (3031, 8230, 3495);
INSERT INTO public.ports VALUES (3033, 8232, 3497);
INSERT INTO public.ports VALUES (3034, 8233, 3498);
INSERT INTO public.ports VALUES (3035, 8234, 3499);
INSERT INTO public.ports VALUES (3036, 8235, 3500);
INSERT INTO public.ports VALUES (3038, 8237, 3502);
INSERT INTO public.ports VALUES (3039, 8238, 3503);
INSERT INTO public.ports VALUES (3040, 8239, 3504);
INSERT INTO public.ports VALUES (4767, 9130, 3505);
INSERT INTO public.ports VALUES (4853, 9216, 3507);
INSERT INTO public.ports VALUES (4854, 9217, 3508);
INSERT INTO public.ports VALUES (4855, 9218, 3509);
INSERT INTO public.ports VALUES (4856, 9219, 3510);
INSERT INTO public.ports VALUES (4858, 9221, 3512);
INSERT INTO public.ports VALUES (4859, 9222, 3513);
INSERT INTO public.ports VALUES (4860, 9223, 3514);
INSERT INTO public.ports VALUES (4861, 9224, 3515);
INSERT INTO public.ports VALUES (4863, 9226, 3517);
INSERT INTO public.ports VALUES (4864, 9227, 3518);
INSERT INTO public.ports VALUES (4865, 9228, 3519);
INSERT INTO public.ports VALUES (4866, 9229, 3520);
INSERT INTO public.ports VALUES (4868, 9231, 3522);
INSERT INTO public.ports VALUES (4869, 9232, 3523);
INSERT INTO public.ports VALUES (4870, 9233, 3524);
INSERT INTO public.ports VALUES (4871, 9234, 3525);
INSERT INTO public.ports VALUES (4873, 9236, 3527);
INSERT INTO public.ports VALUES (4874, 9237, 3528);
INSERT INTO public.ports VALUES (4875, 9238, 3529);
INSERT INTO public.ports VALUES (4876, 9239, 3530);
INSERT INTO public.ports VALUES (4878, 9241, 3532);
INSERT INTO public.ports VALUES (4879, 9242, 3533);
INSERT INTO public.ports VALUES (4880, 9243, 3534);
INSERT INTO public.ports VALUES (4881, 9244, 3535);
INSERT INTO public.ports VALUES (4883, 9246, 3537);
INSERT INTO public.ports VALUES (4884, 9247, 3538);
INSERT INTO public.ports VALUES (4885, 9248, 3539);
INSERT INTO public.ports VALUES (4886, 9249, 3540);
INSERT INTO public.ports VALUES (4888, 9251, 3542);
INSERT INTO public.ports VALUES (4889, 9252, 3543);
INSERT INTO public.ports VALUES (4890, 9253, 3544);
INSERT INTO public.ports VALUES (4891, 9254, 3545);
INSERT INTO public.ports VALUES (4893, 9256, 3547);
INSERT INTO public.ports VALUES (4894, 9257, 3548);
INSERT INTO public.ports VALUES (4895, 9258, 3549);
INSERT INTO public.ports VALUES (4896, 9259, 3550);
INSERT INTO public.ports VALUES (4898, 9261, 3552);
INSERT INTO public.ports VALUES (4899, 9262, 3553);
INSERT INTO public.ports VALUES (2917, 8116, 3756);
INSERT INTO public.ports VALUES (2919, 8118, 3757);
INSERT INTO public.ports VALUES (2920, 8119, 3758);
INSERT INTO public.ports VALUES (2922, 8121, 3760);
INSERT INTO public.ports VALUES (2924, 8123, 3761);
INSERT INTO public.ports VALUES (2925, 8124, 3762);
INSERT INTO public.ports VALUES (2926, 8125, 3763);
INSERT INTO public.ports VALUES (2929, 8128, 3765);
INSERT INTO public.ports VALUES (2930, 8129, 3766);
INSERT INTO public.ports VALUES (2931, 8130, 3767);
INSERT INTO public.ports VALUES (2932, 8131, 3768);
INSERT INTO public.ports VALUES (2935, 8134, 3770);
INSERT INTO public.ports VALUES (2936, 8135, 3771);
INSERT INTO public.ports VALUES (2937, 8136, 3772);
INSERT INTO public.ports VALUES (2939, 8138, 3773);
INSERT INTO public.ports VALUES (2941, 8140, 3775);
INSERT INTO public.ports VALUES (2942, 8141, 3776);
INSERT INTO public.ports VALUES (2944, 8143, 3777);
INSERT INTO public.ports VALUES (2945, 8144, 3778);
INSERT INTO public.ports VALUES (2947, 8146, 3780);
INSERT INTO public.ports VALUES (2949, 8148, 3781);
INSERT INTO public.ports VALUES (2950, 8149, 3782);
INSERT INTO public.ports VALUES (2951, 8150, 3783);
INSERT INTO public.ports VALUES (2954, 8153, 3785);
INSERT INTO public.ports VALUES (2955, 8154, 3786);
INSERT INTO public.ports VALUES (2956, 8155, 3787);
INSERT INTO public.ports VALUES (2957, 8156, 3788);
INSERT INTO public.ports VALUES (2960, 8159, 3790);
INSERT INTO public.ports VALUES (2961, 8160, 3791);
INSERT INTO public.ports VALUES (2962, 8161, 3792);
INSERT INTO public.ports VALUES (2964, 8163, 3793);
INSERT INTO public.ports VALUES (2966, 8165, 3795);
INSERT INTO public.ports VALUES (2967, 8166, 3796);
INSERT INTO public.ports VALUES (2969, 8168, 3797);
INSERT INTO public.ports VALUES (2970, 8169, 3798);
INSERT INTO public.ports VALUES (2972, 8171, 3800);
INSERT INTO public.ports VALUES (2974, 8173, 3801);
INSERT INTO public.ports VALUES (2975, 8174, 3802);
INSERT INTO public.ports VALUES (2976, 8175, 3803);
INSERT INTO public.ports VALUES (2979, 8178, 3805);
INSERT INTO public.ports VALUES (2980, 8179, 3806);
INSERT INTO public.ports VALUES (2981, 8180, 3807);
INSERT INTO public.ports VALUES (2982, 8181, 3808);
INSERT INTO public.ports VALUES (2985, 8184, 3810);
INSERT INTO public.ports VALUES (2986, 8185, 3811);
INSERT INTO public.ports VALUES (2987, 8186, 3812);
INSERT INTO public.ports VALUES (2989, 8188, 3813);
INSERT INTO public.ports VALUES (2991, 8190, 3815);
INSERT INTO public.ports VALUES (2992, 8191, 3816);
INSERT INTO public.ports VALUES (2994, 8193, 3817);
INSERT INTO public.ports VALUES (2995, 8194, 3818);
INSERT INTO public.ports VALUES (2997, 8196, 3820);
INSERT INTO public.ports VALUES (2999, 8198, 3821);
INSERT INTO public.ports VALUES (3000, 8199, 3822);
INSERT INTO public.ports VALUES (3001, 8200, 3823);
INSERT INTO public.ports VALUES (3004, 8203, 3825);
INSERT INTO public.ports VALUES (3005, 8204, 3826);
INSERT INTO public.ports VALUES (3006, 8205, 3827);
INSERT INTO public.ports VALUES (3008, 8207, 3828);
INSERT INTO public.ports VALUES (3010, 8209, 3830);
INSERT INTO public.ports VALUES (3012, 8211, 3831);
INSERT INTO public.ports VALUES (3013, 8212, 3832);
INSERT INTO public.ports VALUES (3015, 8214, 3833);
INSERT INTO public.ports VALUES (3017, 8216, 3835);
INSERT INTO public.ports VALUES (3019, 8218, 3836);
INSERT INTO public.ports VALUES (3020, 8219, 3837);
INSERT INTO public.ports VALUES (5908, 7939, 3838);
INSERT INTO public.ports VALUES (5910, 7941, 3840);
INSERT INTO public.ports VALUES (5911, 7942, 3841);
INSERT INTO public.ports VALUES (5912, 7943, 3842);
INSERT INTO public.ports VALUES (5913, 7944, 3843);
INSERT INTO public.ports VALUES (5915, 7946, 3845);
INSERT INTO public.ports VALUES (5916, 7947, 3846);
INSERT INTO public.ports VALUES (5917, 7948, 3847);
INSERT INTO public.ports VALUES (5918, 7949, 3848);
INSERT INTO public.ports VALUES (5920, 7951, 3850);
INSERT INTO public.ports VALUES (4976, 9339, 4891);
INSERT INTO public.ports VALUES (4983, 9346, 4896);
INSERT INTO public.ports VALUES (4990, 9353, 4901);
INSERT INTO public.ports VALUES (4997, 9360, 4906);
INSERT INTO public.ports VALUES (5003, 9366, 4911);
INSERT INTO public.ports VALUES (5010, 9373, 4916);
INSERT INTO public.ports VALUES (5017, 9380, 4921);
INSERT INTO public.ports VALUES (5024, 9387, 4926);
INSERT INTO public.ports VALUES (5285, 9648, 4930);
INSERT INTO public.ports VALUES (5288, 9651, 4933);
INSERT INTO public.ports VALUES (5293, 9656, 4938);
INSERT INTO public.ports VALUES (5298, 9661, 4943);
INSERT INTO public.ports VALUES (5303, 9666, 4948);
INSERT INTO public.ports VALUES (5308, 9671, 4953);
INSERT INTO public.ports VALUES (5313, 9676, 4958);
INSERT INTO public.ports VALUES (5318, 9681, 4963);
INSERT INTO public.ports VALUES (5324, 9687, 4968);
INSERT INTO public.ports VALUES (5330, 9693, 4973);
INSERT INTO public.ports VALUES (5336, 9699, 4978);
INSERT INTO public.ports VALUES (5342, 9705, 4983);
INSERT INTO public.ports VALUES (5349, 9712, 4988);
INSERT INTO public.ports VALUES (5355, 9718, 4993);
INSERT INTO public.ports VALUES (5361, 9724, 4998);
INSERT INTO public.ports VALUES (5367, 9730, 5003);
INSERT INTO public.ports VALUES (5374, 9737, 5008);
INSERT INTO public.ports VALUES (5380, 9743, 5013);
INSERT INTO public.ports VALUES (5836, 7799, 3681);
INSERT INTO public.ports VALUES (5837, 7800, 3682);
INSERT INTO public.ports VALUES (5838, 7801, 3683);
INSERT INTO public.ports VALUES (5840, 7803, 3685);
INSERT INTO public.ports VALUES (5841, 7804, 3686);
INSERT INTO public.ports VALUES (5842, 7805, 3687);
INSERT INTO public.ports VALUES (5843, 7806, 3688);
INSERT INTO public.ports VALUES (5845, 7808, 3690);
INSERT INTO public.ports VALUES (5846, 7809, 3691);
INSERT INTO public.ports VALUES (5847, 7810, 3692);
INSERT INTO public.ports VALUES (5848, 7811, 3693);
INSERT INTO public.ports VALUES (5850, 7813, 3695);
INSERT INTO public.ports VALUES (5851, 7814, 3696);
INSERT INTO public.ports VALUES (5852, 7815, 3697);
INSERT INTO public.ports VALUES (5853, 7816, 3698);
INSERT INTO public.ports VALUES (5855, 7818, 3700);
INSERT INTO public.ports VALUES (5856, 7819, 3701);
INSERT INTO public.ports VALUES (5857, 7820, 3702);
INSERT INTO public.ports VALUES (5858, 7821, 3703);
INSERT INTO public.ports VALUES (5860, 7823, 3705);
INSERT INTO public.ports VALUES (5861, 7824, 3706);
INSERT INTO public.ports VALUES (5862, 7825, 3707);
INSERT INTO public.ports VALUES (5863, 7826, 3708);
INSERT INTO public.ports VALUES (5865, 7828, 3710);
INSERT INTO public.ports VALUES (5866, 7829, 3711);
INSERT INTO public.ports VALUES (5867, 7830, 3712);
INSERT INTO public.ports VALUES (5868, 7831, 3713);
INSERT INTO public.ports VALUES (5870, 7833, 3715);
INSERT INTO public.ports VALUES (5871, 7834, 3716);
INSERT INTO public.ports VALUES (5872, 7835, 3717);
INSERT INTO public.ports VALUES (5873, 7836, 3718);
INSERT INTO public.ports VALUES (5875, 7838, 3720);
INSERT INTO public.ports VALUES (5876, 7839, 3721);
INSERT INTO public.ports VALUES (5877, 7840, 3722);
INSERT INTO public.ports VALUES (5878, 7841, 3723);
INSERT INTO public.ports VALUES (5880, 7843, 3725);
INSERT INTO public.ports VALUES (5881, 7844, 3726);
INSERT INTO public.ports VALUES (5882, 7845, 3727);
INSERT INTO public.ports VALUES (5883, 7846, 3728);
INSERT INTO public.ports VALUES (5885, 7848, 3730);
INSERT INTO public.ports VALUES (5886, 7849, 3731);
INSERT INTO public.ports VALUES (5887, 7850, 3732);
INSERT INTO public.ports VALUES (5888, 7851, 3733);
INSERT INTO public.ports VALUES (5890, 7853, 3735);
INSERT INTO public.ports VALUES (5891, 7922, 3736);
INSERT INTO public.ports VALUES (5892, 7923, 3737);
INSERT INTO public.ports VALUES (5893, 7924, 3738);
INSERT INTO public.ports VALUES (5895, 7926, 3740);
INSERT INTO public.ports VALUES (5896, 7927, 3741);
INSERT INTO public.ports VALUES (5897, 7928, 3742);
INSERT INTO public.ports VALUES (5898, 7929, 3743);
INSERT INTO public.ports VALUES (5900, 7931, 3745);
INSERT INTO public.ports VALUES (5901, 7932, 3746);
INSERT INTO public.ports VALUES (5902, 7933, 3747);
INSERT INTO public.ports VALUES (5903, 7934, 3748);
INSERT INTO public.ports VALUES (5905, 7936, 3750);
INSERT INTO public.ports VALUES (5906, 7937, 3751);
INSERT INTO public.ports VALUES (5907, 7938, 3752);
INSERT INTO public.ports VALUES (2914, 8113, 3753);
INSERT INTO public.ports VALUES (2916, 8115, 3755);
INSERT INTO public.ports VALUES (5921, 7952, 3851);
INSERT INTO public.ports VALUES (5922, 7953, 3852);
INSERT INTO public.ports VALUES (5923, 7954, 3853);
INSERT INTO public.ports VALUES (5925, 7956, 3855);
INSERT INTO public.ports VALUES (5926, 7957, 3856);
INSERT INTO public.ports VALUES (5927, 7958, 3857);
INSERT INTO public.ports VALUES (5928, 7959, 3858);
INSERT INTO public.ports VALUES (5930, 7961, 3860);
INSERT INTO public.ports VALUES (5931, 7962, 3861);
INSERT INTO public.ports VALUES (5932, 7963, 3862);
INSERT INTO public.ports VALUES (5933, 7964, 3863);
INSERT INTO public.ports VALUES (5935, 7966, 3865);
INSERT INTO public.ports VALUES (5936, 7967, 3866);
INSERT INTO public.ports VALUES (5386, 9749, 5018);
INSERT INTO public.ports VALUES (5392, 9755, 5023);
INSERT INTO public.ports VALUES (5399, 9762, 5028);
INSERT INTO public.ports VALUES (5192, 9555, 5033);
INSERT INTO public.ports VALUES (3068, 8267, 3899);
INSERT INTO public.ports VALUES (3070, 8269, 3900);
INSERT INTO public.ports VALUES (3071, 8270, 3901);
INSERT INTO public.ports VALUES (3074, 8273, 3903);
INSERT INTO public.ports VALUES (3075, 8274, 3904);
INSERT INTO public.ports VALUES (3076, 8275, 3905);
INSERT INTO public.ports VALUES (3078, 8277, 3906);
INSERT INTO public.ports VALUES (3081, 8280, 3908);
INSERT INTO public.ports VALUES (3082, 8281, 3909);
INSERT INTO public.ports VALUES (3083, 8282, 3910);
INSERT INTO public.ports VALUES (3085, 8284, 3911);
INSERT INTO public.ports VALUES (3087, 8286, 3913);
INSERT INTO public.ports VALUES (3089, 8288, 3914);
INSERT INTO public.ports VALUES (3090, 8289, 3915);
INSERT INTO public.ports VALUES (3092, 8291, 3916);
INSERT INTO public.ports VALUES (3094, 8293, 3918);
INSERT INTO public.ports VALUES (3096, 8295, 3919);
INSERT INTO public.ports VALUES (3097, 8296, 3920);
INSERT INTO public.ports VALUES (3098, 8297, 3921);
INSERT INTO public.ports VALUES (4688, 9051, 3923);
INSERT INTO public.ports VALUES (4692, 9055, 3924);
INSERT INTO public.ports VALUES (4695, 9058, 3925);
INSERT INTO public.ports VALUES (5968, 7999, 3926);
INSERT INTO public.ports VALUES (5970, 8001, 3928);
INSERT INTO public.ports VALUES (5971, 8002, 3929);
INSERT INTO public.ports VALUES (5972, 8003, 3930);
INSERT INTO public.ports VALUES (5973, 8004, 3931);
INSERT INTO public.ports VALUES (5975, 8006, 3933);
INSERT INTO public.ports VALUES (5976, 8007, 3934);
INSERT INTO public.ports VALUES (5977, 8008, 3935);
INSERT INTO public.ports VALUES (5978, 8009, 3936);
INSERT INTO public.ports VALUES (5980, 8011, 3938);
INSERT INTO public.ports VALUES (5981, 8012, 3939);
INSERT INTO public.ports VALUES (5982, 8013, 3940);
INSERT INTO public.ports VALUES (5983, 8014, 3941);
INSERT INTO public.ports VALUES (5985, 8016, 3943);
INSERT INTO public.ports VALUES (5986, 8017, 3944);
INSERT INTO public.ports VALUES (5987, 8018, 3945);
INSERT INTO public.ports VALUES (5988, 8019, 3946);
INSERT INTO public.ports VALUES (5990, 8021, 3948);
INSERT INTO public.ports VALUES (5991, 8022, 3949);
INSERT INTO public.ports VALUES (5992, 8023, 3950);
INSERT INTO public.ports VALUES (5993, 8024, 3951);
INSERT INTO public.ports VALUES (5995, 8026, 3953);
INSERT INTO public.ports VALUES (5996, 8027, 3954);
INSERT INTO public.ports VALUES (5997, 8028, 3955);
INSERT INTO public.ports VALUES (5998, 8029, 3956);
INSERT INTO public.ports VALUES (6000, 8031, 3958);
INSERT INTO public.ports VALUES (6001, 8032, 3959);
INSERT INTO public.ports VALUES (6002, 8033, 3960);
INSERT INTO public.ports VALUES (6003, 8034, 3961);
INSERT INTO public.ports VALUES (6005, 8036, 3963);
INSERT INTO public.ports VALUES (6006, 8037, 3964);
INSERT INTO public.ports VALUES (6007, 8038, 3965);
INSERT INTO public.ports VALUES (6008, 8039, 3966);
INSERT INTO public.ports VALUES (6010, 8041, 3968);
INSERT INTO public.ports VALUES (4699, 9062, 3969);
INSERT INTO public.ports VALUES (4703, 9066, 3970);
INSERT INTO public.ports VALUES (4706, 9069, 3971);
INSERT INTO public.ports VALUES (4714, 9077, 3973);
INSERT INTO public.ports VALUES (4717, 9080, 3974);
INSERT INTO public.ports VALUES (4721, 9084, 3975);
INSERT INTO public.ports VALUES (4725, 9088, 3976);
INSERT INTO public.ports VALUES (4732, 9095, 3978);
INSERT INTO public.ports VALUES (4736, 9099, 3979);
INSERT INTO public.ports VALUES (4738, 9101, 3980);
INSERT INTO public.ports VALUES (4739, 9102, 3981);
INSERT INTO public.ports VALUES (4741, 9104, 3983);
INSERT INTO public.ports VALUES (4742, 9105, 3984);
INSERT INTO public.ports VALUES (4743, 9106, 3985);
INSERT INTO public.ports VALUES (4744, 9107, 3986);
INSERT INTO public.ports VALUES (4746, 9109, 3988);
INSERT INTO public.ports VALUES (4747, 9110, 3989);
INSERT INTO public.ports VALUES (5199, 9562, 5038);
INSERT INTO public.ports VALUES (5206, 9569, 5043);
INSERT INTO public.ports VALUES (5209, 9572, 5045);
INSERT INTO public.ports VALUES (5210, 9573, 5046);
INSERT INTO public.ports VALUES (5212, 9575, 5047);
INSERT INTO public.ports VALUES (5213, 9576, 5048);
INSERT INTO public.ports VALUES (5214, 9577, 5049);
INSERT INTO public.ports VALUES (5216, 9579, 5050);
INSERT INTO public.ports VALUES (5217, 9580, 5051);
INSERT INTO public.ports VALUES (5218, 9581, 5052);
INSERT INTO public.ports VALUES (5220, 9583, 5053);
INSERT INTO public.ports VALUES (5221, 9584, 5054);
INSERT INTO public.ports VALUES (5223, 9586, 5055);
INSERT INTO public.ports VALUES (5224, 9587, 5056);
INSERT INTO public.ports VALUES (5413, 9776, 5057);
INSERT INTO public.ports VALUES (5414, 9777, 5058);
INSERT INTO public.ports VALUES (5421, 9784, 5063);
INSERT INTO public.ports VALUES (5428, 9791, 5068);
INSERT INTO public.ports VALUES (5435, 9798, 5073);
INSERT INTO public.ports VALUES (5442, 9805, 5078);
INSERT INTO public.ports VALUES (5449, 9812, 5083);
INSERT INTO public.ports VALUES (5456, 9819, 5088);
INSERT INTO public.ports VALUES (5463, 9826, 5093);
INSERT INTO public.ports VALUES (5469, 9832, 5098);
INSERT INTO public.ports VALUES (5476, 9839, 5103);
INSERT INTO public.ports VALUES (5483, 9846, 5108);
INSERT INTO public.ports VALUES (5490, 9853, 5113);
INSERT INTO public.ports VALUES (5497, 9860, 5118);
INSERT INTO public.ports VALUES (3681, 7854, 5122);
INSERT INTO public.ports VALUES (3706, 7879, 5127);
INSERT INTO public.ports VALUES (3731, 7904, 5132);
INSERT INTO public.ports VALUES (4151, 8512, 5259);
INSERT INTO public.ports VALUES (4153, 8514, 5261);
INSERT INTO public.ports VALUES (4158, 8519, 5266);
INSERT INTO public.ports VALUES (4161, 8522, 5271);
INSERT INTO public.ports VALUES (4166, 8527, 5276);
INSERT INTO public.ports VALUES (4171, 8532, 5281);
INSERT INTO public.ports VALUES (4176, 8537, 5286);
INSERT INTO public.ports VALUES (4181, 8542, 5291);
INSERT INTO public.ports VALUES (6116, 8335, 4114);
INSERT INTO public.ports VALUES (6117, 8336, 4115);
INSERT INTO public.ports VALUES (4433, 8796, 4116);
INSERT INTO public.ports VALUES (4436, 8799, 4117);
INSERT INTO public.ports VALUES (4440, 8803, 4118);
INSERT INTO public.ports VALUES (4444, 8807, 4119);
INSERT INTO public.ports VALUES (4447, 8810, 4120);
INSERT INTO public.ports VALUES (4451, 8814, 4121);
INSERT INTO public.ports VALUES (4455, 8818, 4122);
INSERT INTO public.ports VALUES (4458, 8821, 4123);
INSERT INTO public.ports VALUES (4462, 8825, 4124);
INSERT INTO public.ports VALUES (4466, 8829, 4125);
INSERT INTO public.ports VALUES (4469, 8832, 4126);
INSERT INTO public.ports VALUES (4473, 8836, 4127);
INSERT INTO public.ports VALUES (4477, 8840, 4128);
INSERT INTO public.ports VALUES (4480, 8843, 4129);
INSERT INTO public.ports VALUES (4484, 8847, 4130);
INSERT INTO public.ports VALUES (4488, 8851, 4131);
INSERT INTO public.ports VALUES (4491, 8854, 4132);
INSERT INTO public.ports VALUES (4495, 8858, 4133);
INSERT INTO public.ports VALUES (4499, 8862, 4134);
INSERT INTO public.ports VALUES (4502, 8865, 4135);
INSERT INTO public.ports VALUES (4506, 8869, 4136);
INSERT INTO public.ports VALUES (4510, 8873, 4137);
INSERT INTO public.ports VALUES (4512, 8875, 4138);
INSERT INTO public.ports VALUES (4513, 8876, 4139);
INSERT INTO public.ports VALUES (4514, 8877, 4140);
INSERT INTO public.ports VALUES (4515, 8878, 4141);
INSERT INTO public.ports VALUES (4516, 8879, 4142);
INSERT INTO public.ports VALUES (4517, 8880, 4143);
INSERT INTO public.ports VALUES (4518, 8881, 4144);
INSERT INTO public.ports VALUES (4519, 8882, 4145);
INSERT INTO public.ports VALUES (4520, 8883, 4146);
INSERT INTO public.ports VALUES (4521, 8884, 4147);
INSERT INTO public.ports VALUES (4522, 8885, 4148);
INSERT INTO public.ports VALUES (4523, 8886, 4149);
INSERT INTO public.ports VALUES (4524, 8887, 4150);
INSERT INTO public.ports VALUES (4525, 8888, 4151);
INSERT INTO public.ports VALUES (4102, 8463, 4152);
INSERT INTO public.ports VALUES (4103, 8464, 4153);
INSERT INTO public.ports VALUES (4104, 8465, 4154);
INSERT INTO public.ports VALUES (4105, 8466, 4155);
INSERT INTO public.ports VALUES (4107, 8468, 4156);
INSERT INTO public.ports VALUES (4108, 8469, 4157);
INSERT INTO public.ports VALUES (4109, 8470, 4158);
INSERT INTO public.ports VALUES (4110, 8471, 4159);
INSERT INTO public.ports VALUES (4112, 8473, 4160);
INSERT INTO public.ports VALUES (4113, 8474, 4161);
INSERT INTO public.ports VALUES (4114, 8475, 4162);
INSERT INTO public.ports VALUES (4115, 8476, 4163);
INSERT INTO public.ports VALUES (4117, 8478, 4164);
INSERT INTO public.ports VALUES (4118, 8479, 4165);
INSERT INTO public.ports VALUES (4119, 8480, 4166);
INSERT INTO public.ports VALUES (4120, 8481, 4167);
INSERT INTO public.ports VALUES (6118, 8337, 4168);
INSERT INTO public.ports VALUES (6119, 8338, 4169);
INSERT INTO public.ports VALUES (6120, 8339, 4170);
INSERT INTO public.ports VALUES (6121, 8340, 4171);
INSERT INTO public.ports VALUES (6122, 8341, 4172);
INSERT INTO public.ports VALUES (6123, 8342, 4173);
INSERT INTO public.ports VALUES (6124, 8343, 4174);
INSERT INTO public.ports VALUES (6125, 8344, 4175);
INSERT INTO public.ports VALUES (6126, 8345, 4176);
INSERT INTO public.ports VALUES (6127, 8346, 4177);
INSERT INTO public.ports VALUES (6128, 8347, 4178);
INSERT INTO public.ports VALUES (6129, 8348, 4179);
INSERT INTO public.ports VALUES (6130, 8349, 4180);
INSERT INTO public.ports VALUES (6131, 8350, 4181);
INSERT INTO public.ports VALUES (6132, 8351, 4182);
INSERT INTO public.ports VALUES (6133, 8352, 4183);
INSERT INTO public.ports VALUES (6134, 8353, 4184);
INSERT INTO public.ports VALUES (6136, 8355, 4186);
INSERT INTO public.ports VALUES (6137, 8356, 4187);
INSERT INTO public.ports VALUES (6138, 8357, 4188);
INSERT INTO public.ports VALUES (6139, 8358, 4189);
INSERT INTO public.ports VALUES (6141, 8360, 4191);
INSERT INTO public.ports VALUES (6142, 8361, 4192);
INSERT INTO public.ports VALUES (6143, 8362, 4193);
INSERT INTO public.ports VALUES (6144, 8363, 4194);
INSERT INTO public.ports VALUES (6146, 8365, 4196);
INSERT INTO public.ports VALUES (6147, 8570, 4197);
INSERT INTO public.ports VALUES (6148, 8571, 4198);
INSERT INTO public.ports VALUES (6149, 8572, 4199);
INSERT INTO public.ports VALUES (6151, 8574, 4201);
INSERT INTO public.ports VALUES (6152, 8575, 4202);
INSERT INTO public.ports VALUES (6153, 8576, 4203);
INSERT INTO public.ports VALUES (6154, 8577, 4204);
INSERT INTO public.ports VALUES (6156, 8579, 4206);
INSERT INTO public.ports VALUES (6157, 8580, 4207);
INSERT INTO public.ports VALUES (6158, 8581, 4208);
INSERT INTO public.ports VALUES (6159, 8582, 4209);
INSERT INTO public.ports VALUES (6161, 8584, 4211);
INSERT INTO public.ports VALUES (6162, 8585, 4212);
INSERT INTO public.ports VALUES (4186, 8547, 5296);
INSERT INTO public.ports VALUES (4191, 8552, 5301);
INSERT INTO public.ports VALUES (4196, 8557, 5306);
INSERT INTO public.ports VALUES (4201, 8562, 5311);
INSERT INTO public.ports VALUES (4206, 8567, 5316);
INSERT INTO public.ports VALUES (4558, 8921, 5321);
INSERT INTO public.ports VALUES (4563, 8926, 5326);
INSERT INTO public.ports VALUES (4568, 8931, 5331);
INSERT INTO public.ports VALUES (4573, 8936, 5336);
INSERT INTO public.ports VALUES (4768, 9131, 5341);
INSERT INTO public.ports VALUES (4810, 9173, 5346);
INSERT INTO public.ports VALUES (4815, 9178, 5351);
INSERT INTO public.ports VALUES (4819, 9182, 5355);
INSERT INTO public.ports VALUES (4823, 9186, 5359);
INSERT INTO public.ports VALUES (4824, 9187, 5360);
INSERT INTO public.ports VALUES (4825, 9188, 5361);
INSERT INTO public.ports VALUES (4578, 8941, 5362);
INSERT INTO public.ports VALUES (4579, 8942, 5363);
INSERT INTO public.ports VALUES (4580, 8943, 5364);
INSERT INTO public.ports VALUES (4581, 8944, 5365);
INSERT INTO public.ports VALUES (4582, 8945, 5366);
INSERT INTO public.ports VALUES (4583, 8946, 5367);
INSERT INTO public.ports VALUES (4584, 8947, 5368);
INSERT INTO public.ports VALUES (4585, 8948, 5369);
INSERT INTO public.ports VALUES (4586, 8949, 5370);
INSERT INTO public.ports VALUES (4587, 8950, 5371);
INSERT INTO public.ports VALUES (4588, 8951, 5372);
INSERT INTO public.ports VALUES (4589, 8952, 5373);
INSERT INTO public.ports VALUES (4590, 8953, 5374);
INSERT INTO public.ports VALUES (4591, 8954, 5375);
INSERT INTO public.ports VALUES (4592, 8955, 5376);
INSERT INTO public.ports VALUES (4593, 8956, 5377);
INSERT INTO public.ports VALUES (4594, 8957, 5378);
INSERT INTO public.ports VALUES (4595, 8958, 5379);
INSERT INTO public.ports VALUES (4596, 8959, 5380);
INSERT INTO public.ports VALUES (4597, 8960, 5381);
INSERT INTO public.ports VALUES (4598, 8961, 5382);
INSERT INTO public.ports VALUES (4599, 8962, 5383);
INSERT INTO public.ports VALUES (4600, 8963, 5384);
INSERT INTO public.ports VALUES (4601, 8964, 5385);
INSERT INTO public.ports VALUES (4602, 8965, 5386);
INSERT INTO public.ports VALUES (4603, 8966, 5387);
INSERT INTO public.ports VALUES (4604, 8967, 5388);
INSERT INTO public.ports VALUES (4605, 8968, 5389);
INSERT INTO public.ports VALUES (4606, 8969, 5390);
INSERT INTO public.ports VALUES (4607, 8970, 5391);
INSERT INTO public.ports VALUES (4608, 8971, 5392);
INSERT INTO public.ports VALUES (4609, 8972, 5393);
INSERT INTO public.ports VALUES (4610, 8973, 5394);
INSERT INTO public.ports VALUES (4611, 8974, 5395);
INSERT INTO public.ports VALUES (4612, 8975, 5396);
INSERT INTO public.ports VALUES (4613, 8976, 5397);
INSERT INTO public.ports VALUES (4614, 8977, 5398);
INSERT INTO public.ports VALUES (4615, 8978, 5399);
INSERT INTO public.ports VALUES (4616, 8979, 5400);
INSERT INTO public.ports VALUES (4617, 8980, 5401);
INSERT INTO public.ports VALUES (4618, 8981, 5402);
INSERT INTO public.ports VALUES (4619, 8982, 5403);
INSERT INTO public.ports VALUES (4620, 8983, 5404);
INSERT INTO public.ports VALUES (4621, 8984, 5405);
INSERT INTO public.ports VALUES (4622, 8985, 5406);
INSERT INTO public.ports VALUES (4623, 8986, 5407);
INSERT INTO public.ports VALUES (4624, 8987, 5408);
INSERT INTO public.ports VALUES (4625, 8988, 5409);
INSERT INTO public.ports VALUES (4626, 8989, 5410);
INSERT INTO public.ports VALUES (4627, 8990, 5411);
INSERT INTO public.ports VALUES (4628, 8991, 5412);
INSERT INTO public.ports VALUES (4629, 8992, 5413);
INSERT INTO public.ports VALUES (4630, 8993, 5414);
INSERT INTO public.ports VALUES (4631, 8994, 5415);
INSERT INTO public.ports VALUES (4632, 8995, 5416);
INSERT INTO public.ports VALUES (4633, 8996, 5417);
INSERT INTO public.ports VALUES (4634, 8997, 5418);
INSERT INTO public.ports VALUES (4635, 8998, 5419);
INSERT INTO public.ports VALUES (4636, 8999, 5420);
INSERT INTO public.ports VALUES (4637, 9000, 5421);
INSERT INTO public.ports VALUES (4638, 9001, 5422);
INSERT INTO public.ports VALUES (4639, 9002, 5423);
INSERT INTO public.ports VALUES (4640, 9003, 5424);
INSERT INTO public.ports VALUES (4641, 9004, 5425);
INSERT INTO public.ports VALUES (4642, 9005, 5426);
INSERT INTO public.ports VALUES (4643, 9006, 5427);
INSERT INTO public.ports VALUES (4644, 9007, 5428);
INSERT INTO public.ports VALUES (4645, 9008, 5429);
INSERT INTO public.ports VALUES (4646, 9009, 5430);
INSERT INTO public.ports VALUES (4647, 9010, 5431);
INSERT INTO public.ports VALUES (4648, 9011, 5432);
INSERT INTO public.ports VALUES (4651, 9014, 5433);
INSERT INTO public.ports VALUES (4655, 9018, 5434);
INSERT INTO public.ports VALUES (4659, 9022, 5435);
INSERT INTO public.ports VALUES (4662, 9025, 5436);
INSERT INTO public.ports VALUES (4666, 9029, 5437);
INSERT INTO public.ports VALUES (4670, 9033, 5438);
INSERT INTO public.ports VALUES (4673, 9036, 5439);
INSERT INTO public.ports VALUES (4677, 9040, 5440);
INSERT INTO public.ports VALUES (4681, 9044, 5441);
INSERT INTO public.ports VALUES (4684, 9047, 5442);
INSERT INTO public.ports VALUES (5033, 9396, 5443);
INSERT INTO public.ports VALUES (5040, 9403, 5445);
INSERT INTO public.ports VALUES (5052, 9415, 5450);
INSERT INTO public.ports VALUES (5057, 9420, 5455);
INSERT INTO public.ports VALUES (5062, 9425, 5460);
INSERT INTO public.ports VALUES (6178, 8601, 4315);
INSERT INTO public.ports VALUES (6179, 8602, 4316);
INSERT INTO public.ports VALUES (6180, 8603, 4317);
INSERT INTO public.ports VALUES (6182, 8605, 4319);
INSERT INTO public.ports VALUES (6183, 8606, 4320);
INSERT INTO public.ports VALUES (6184, 8607, 4321);
INSERT INTO public.ports VALUES (6185, 8608, 4322);
INSERT INTO public.ports VALUES (6187, 8610, 4324);
INSERT INTO public.ports VALUES (6188, 8611, 4325);
INSERT INTO public.ports VALUES (6189, 8612, 4326);
INSERT INTO public.ports VALUES (6190, 8613, 4327);
INSERT INTO public.ports VALUES (6192, 8615, 4329);
INSERT INTO public.ports VALUES (6193, 8616, 4330);
INSERT INTO public.ports VALUES (6194, 8617, 4331);
INSERT INTO public.ports VALUES (6195, 8618, 4332);
INSERT INTO public.ports VALUES (6197, 8620, 4334);
INSERT INTO public.ports VALUES (6198, 8621, 4335);
INSERT INTO public.ports VALUES (6199, 8622, 4336);
INSERT INTO public.ports VALUES (6200, 8623, 4337);
INSERT INTO public.ports VALUES (6202, 8625, 4339);
INSERT INTO public.ports VALUES (6203, 8626, 4340);
INSERT INTO public.ports VALUES (6204, 8627, 4341);
INSERT INTO public.ports VALUES (6205, 8628, 4342);
INSERT INTO public.ports VALUES (6207, 8630, 4344);
INSERT INTO public.ports VALUES (6208, 8631, 4345);
INSERT INTO public.ports VALUES (6209, 8632, 4346);
INSERT INTO public.ports VALUES (6210, 8633, 4347);
INSERT INTO public.ports VALUES (6212, 8635, 4349);
INSERT INTO public.ports VALUES (6213, 8636, 4350);
INSERT INTO public.ports VALUES (6214, 8637, 4351);
INSERT INTO public.ports VALUES (6215, 8638, 4352);
INSERT INTO public.ports VALUES (6217, 8640, 4354);
INSERT INTO public.ports VALUES (6218, 8641, 4355);
INSERT INTO public.ports VALUES (6219, 8642, 4356);
INSERT INTO public.ports VALUES (6220, 8643, 4357);
INSERT INTO public.ports VALUES (6222, 8645, 4359);
INSERT INTO public.ports VALUES (6223, 8646, 4360);
INSERT INTO public.ports VALUES (6224, 8647, 4361);
INSERT INTO public.ports VALUES (6225, 8648, 4362);
INSERT INTO public.ports VALUES (6227, 8650, 4364);
INSERT INTO public.ports VALUES (6228, 8651, 4365);
INSERT INTO public.ports VALUES (6229, 8652, 4366);
INSERT INTO public.ports VALUES (6230, 8653, 4367);
INSERT INTO public.ports VALUES (6232, 8655, 4369);
INSERT INTO public.ports VALUES (6233, 8656, 4370);
INSERT INTO public.ports VALUES (6234, 8657, 4371);
INSERT INTO public.ports VALUES (6235, 8658, 4372);
INSERT INTO public.ports VALUES (6237, 8660, 4374);
INSERT INTO public.ports VALUES (6238, 8661, 4375);
INSERT INTO public.ports VALUES (6239, 8662, 4376);
INSERT INTO public.ports VALUES (6240, 8663, 4377);
INSERT INTO public.ports VALUES (6242, 8665, 4379);
INSERT INTO public.ports VALUES (6243, 8666, 4380);
INSERT INTO public.ports VALUES (6244, 8667, 4381);
INSERT INTO public.ports VALUES (6245, 8668, 4382);
INSERT INTO public.ports VALUES (6247, 8670, 4384);
INSERT INTO public.ports VALUES (6248, 8671, 4385);
INSERT INTO public.ports VALUES (6249, 8672, 4386);
INSERT INTO public.ports VALUES (6250, 8673, 4387);
INSERT INTO public.ports VALUES (6252, 8675, 4389);
INSERT INTO public.ports VALUES (6253, 8676, 4390);
INSERT INTO public.ports VALUES (6254, 8677, 4391);
INSERT INTO public.ports VALUES (6255, 8678, 4392);
INSERT INTO public.ports VALUES (6257, 8680, 4394);
INSERT INTO public.ports VALUES (6258, 8681, 4395);
INSERT INTO public.ports VALUES (6259, 8682, 4396);
INSERT INTO public.ports VALUES (6260, 8683, 4397);
INSERT INTO public.ports VALUES (6262, 8685, 4399);
INSERT INTO public.ports VALUES (6263, 8686, 4400);
INSERT INTO public.ports VALUES (6264, 8687, 4401);
INSERT INTO public.ports VALUES (6265, 8688, 4402);
INSERT INTO public.ports VALUES (6267, 8690, 4404);
INSERT INTO public.ports VALUES (6268, 8691, 4405);
INSERT INTO public.ports VALUES (5067, 9430, 5465);
INSERT INTO public.ports VALUES (5072, 9435, 5470);
INSERT INTO public.ports VALUES (5077, 9440, 5475);
INSERT INTO public.ports VALUES (5082, 9445, 5480);
INSERT INTO public.ports VALUES (5087, 9450, 5485);
INSERT INTO public.ports VALUES (5092, 9455, 5490);
INSERT INTO public.ports VALUES (5097, 9460, 5495);
INSERT INTO public.ports VALUES (5102, 9465, 5500);
INSERT INTO public.ports VALUES (5107, 9470, 5505);
INSERT INTO public.ports VALUES (5112, 9475, 5510);
INSERT INTO public.ports VALUES (5117, 9480, 5515);
INSERT INTO public.ports VALUES (5122, 9485, 5520);
INSERT INTO public.ports VALUES (4828, 9191, 5525);
INSERT INTO public.ports VALUES (4833, 9196, 5530);
INSERT INTO public.ports VALUES (4838, 9201, 5535);
INSERT INTO public.ports VALUES (4843, 9206, 5540);
INSERT INTO public.ports VALUES (4848, 9211, 5545);
INSERT INTO public.ports VALUES (4432, 8795, 4533);
INSERT INTO public.ports VALUES (4434, 8797, 4534);
INSERT INTO public.ports VALUES (4435, 8798, 4535);
INSERT INTO public.ports VALUES (4438, 8801, 4537);
INSERT INTO public.ports VALUES (4439, 8802, 4538);
INSERT INTO public.ports VALUES (4441, 8804, 4539);
INSERT INTO public.ports VALUES (4442, 8805, 4540);
INSERT INTO public.ports VALUES (4445, 8808, 4542);
INSERT INTO public.ports VALUES (4446, 8809, 4543);
INSERT INTO public.ports VALUES (4448, 8811, 4544);
INSERT INTO public.ports VALUES (4449, 8812, 4545);
INSERT INTO public.ports VALUES (4452, 8815, 4547);
INSERT INTO public.ports VALUES (4453, 8816, 4548);
INSERT INTO public.ports VALUES (4454, 8817, 4549);
INSERT INTO public.ports VALUES (4456, 8819, 4550);
INSERT INTO public.ports VALUES (4459, 8822, 4552);
INSERT INTO public.ports VALUES (4460, 8823, 4553);
INSERT INTO public.ports VALUES (4461, 8824, 4554);
INSERT INTO public.ports VALUES (4463, 8826, 4555);
INSERT INTO public.ports VALUES (4465, 8828, 4557);
INSERT INTO public.ports VALUES (4467, 8830, 4558);
INSERT INTO public.ports VALUES (4468, 8831, 4559);
INSERT INTO public.ports VALUES (4470, 8833, 4560);
INSERT INTO public.ports VALUES (4472, 8835, 4562);
INSERT INTO public.ports VALUES (4474, 8837, 4563);
INSERT INTO public.ports VALUES (4475, 8838, 4564);
INSERT INTO public.ports VALUES (4476, 8839, 4565);
INSERT INTO public.ports VALUES (4479, 8842, 4567);
INSERT INTO public.ports VALUES (4481, 8844, 4568);
INSERT INTO public.ports VALUES (4482, 8845, 4569);
INSERT INTO public.ports VALUES (4483, 8846, 4570);
INSERT INTO public.ports VALUES (4486, 8849, 4572);
INSERT INTO public.ports VALUES (4487, 8850, 4573);
INSERT INTO public.ports VALUES (4489, 8852, 4574);
INSERT INTO public.ports VALUES (4490, 8853, 4575);
INSERT INTO public.ports VALUES (4493, 8856, 4577);
INSERT INTO public.ports VALUES (4494, 8857, 4578);
INSERT INTO public.ports VALUES (4496, 8859, 4579);
INSERT INTO public.ports VALUES (4497, 8860, 4580);
INSERT INTO public.ports VALUES (4500, 8863, 4582);
INSERT INTO public.ports VALUES (4501, 8864, 4583);
INSERT INTO public.ports VALUES (4503, 8866, 4584);
INSERT INTO public.ports VALUES (4504, 8867, 4585);
INSERT INTO public.ports VALUES (4507, 8870, 4587);
INSERT INTO public.ports VALUES (4508, 8871, 4588);
INSERT INTO public.ports VALUES (4509, 8872, 4589);
INSERT INTO public.ports VALUES (4511, 8874, 4590);
INSERT INTO public.ports VALUES (6293, 8716, 4592);
INSERT INTO public.ports VALUES (6294, 8717, 4593);
INSERT INTO public.ports VALUES (6295, 8718, 4594);
INSERT INTO public.ports VALUES (6296, 8719, 4595);
INSERT INTO public.ports VALUES (6298, 8721, 4597);
INSERT INTO public.ports VALUES (6299, 8722, 4598);
INSERT INTO public.ports VALUES (6300, 8723, 4599);
INSERT INTO public.ports VALUES (6301, 8724, 4600);
INSERT INTO public.ports VALUES (6303, 8726, 4602);
INSERT INTO public.ports VALUES (6304, 8727, 4603);
INSERT INTO public.ports VALUES (6305, 8728, 4604);
INSERT INTO public.ports VALUES (6306, 8729, 4605);
INSERT INTO public.ports VALUES (6308, 8731, 4607);
INSERT INTO public.ports VALUES (6309, 8732, 4608);
INSERT INTO public.ports VALUES (6310, 8733, 4609);
INSERT INTO public.ports VALUES (6311, 8734, 4610);
INSERT INTO public.ports VALUES (6313, 8736, 4612);
INSERT INTO public.ports VALUES (6314, 8737, 4613);
INSERT INTO public.ports VALUES (6315, 8738, 4614);
INSERT INTO public.ports VALUES (6316, 8739, 4615);
INSERT INTO public.ports VALUES (6318, 8741, 4617);
INSERT INTO public.ports VALUES (6319, 8742, 4618);
INSERT INTO public.ports VALUES (6320, 8743, 4619);
INSERT INTO public.ports VALUES (6321, 8744, 4620);
INSERT INTO public.ports VALUES (6323, 8746, 4622);
INSERT INTO public.ports VALUES (6324, 8747, 4623);
INSERT INTO public.ports VALUES (4718, 9081, 4772);
INSERT INTO public.ports VALUES (4719, 9082, 4773);
INSERT INTO public.ports VALUES (4720, 9083, 4774);
INSERT INTO public.ports VALUES (4722, 9085, 4775);
INSERT INTO public.ports VALUES (4723, 9086, 4776);
INSERT INTO public.ports VALUES (4724, 9087, 4777);
INSERT INTO public.ports VALUES (4726, 9089, 4778);
INSERT INTO public.ports VALUES (4727, 9090, 4779);
INSERT INTO public.ports VALUES (4729, 9092, 4780);
INSERT INTO public.ports VALUES (4730, 9093, 4781);
INSERT INTO public.ports VALUES (4731, 9094, 4782);
INSERT INTO public.ports VALUES (4733, 9096, 4783);
INSERT INTO public.ports VALUES (4734, 9097, 4784);
INSERT INTO public.ports VALUES (4735, 9098, 4785);
INSERT INTO public.ports VALUES (4737, 9100, 4786);
INSERT INTO public.ports VALUES (4649, 9012, 4787);
INSERT INTO public.ports VALUES (4650, 9013, 4788);
INSERT INTO public.ports VALUES (4652, 9015, 4789);
INSERT INTO public.ports VALUES (4653, 9016, 4790);
INSERT INTO public.ports VALUES (4654, 9017, 4791);
INSERT INTO public.ports VALUES (4656, 9019, 4792);
INSERT INTO public.ports VALUES (4657, 9020, 4793);
INSERT INTO public.ports VALUES (6135, 8354, 4185);
INSERT INTO public.ports VALUES (6140, 8359, 4190);
INSERT INTO public.ports VALUES (6145, 8364, 4195);
INSERT INTO public.ports VALUES (6150, 8573, 4200);
INSERT INTO public.ports VALUES (6155, 8578, 4205);
INSERT INTO public.ports VALUES (6160, 8583, 4210);
INSERT INTO public.ports VALUES (6163, 8586, 4213);
INSERT INTO public.ports VALUES (6164, 8587, 4214);
INSERT INTO public.ports VALUES (6165, 8588, 4215);
INSERT INTO public.ports VALUES (6166, 8589, 4216);
INSERT INTO public.ports VALUES (6167, 8590, 4217);
INSERT INTO public.ports VALUES (4526, 8889, 4218);
INSERT INTO public.ports VALUES (4527, 8890, 4219);
INSERT INTO public.ports VALUES (4528, 8891, 4220);
INSERT INTO public.ports VALUES (4529, 8892, 4221);
INSERT INTO public.ports VALUES (4530, 8893, 4222);
INSERT INTO public.ports VALUES (4531, 8894, 4223);
INSERT INTO public.ports VALUES (4532, 8895, 4224);
INSERT INTO public.ports VALUES (4533, 8896, 4225);
INSERT INTO public.ports VALUES (4534, 8897, 4226);
INSERT INTO public.ports VALUES (4535, 8898, 4227);
INSERT INTO public.ports VALUES (4536, 8899, 4228);
INSERT INTO public.ports VALUES (4537, 8900, 4229);
INSERT INTO public.ports VALUES (4538, 8901, 4230);
INSERT INTO public.ports VALUES (4539, 8902, 4231);
INSERT INTO public.ports VALUES (4540, 8903, 4232);
INSERT INTO public.ports VALUES (4541, 8904, 4233);
INSERT INTO public.ports VALUES (4542, 8905, 4234);
INSERT INTO public.ports VALUES (4543, 8906, 4235);
INSERT INTO public.ports VALUES (4544, 8907, 4236);
INSERT INTO public.ports VALUES (4545, 8908, 4237);
INSERT INTO public.ports VALUES (4546, 8909, 4238);
INSERT INTO public.ports VALUES (4547, 8910, 4239);
INSERT INTO public.ports VALUES (4548, 8911, 4240);
INSERT INTO public.ports VALUES (4549, 8912, 4241);
INSERT INTO public.ports VALUES (4550, 8913, 4242);
INSERT INTO public.ports VALUES (4551, 8914, 4243);
INSERT INTO public.ports VALUES (4552, 8915, 4244);
INSERT INTO public.ports VALUES (4553, 8916, 4245);
INSERT INTO public.ports VALUES (4554, 8917, 4246);
INSERT INTO public.ports VALUES (4555, 8918, 4247);
INSERT INTO public.ports VALUES (4030, 8391, 4248);
INSERT INTO public.ports VALUES (4032, 8393, 4249);
INSERT INTO public.ports VALUES (4033, 8394, 4250);
INSERT INTO public.ports VALUES (4034, 8395, 4251);
INSERT INTO public.ports VALUES (4035, 8396, 4252);
INSERT INTO public.ports VALUES (4037, 8398, 4253);
INSERT INTO public.ports VALUES (4038, 8399, 4254);
INSERT INTO public.ports VALUES (4039, 8400, 4255);
INSERT INTO public.ports VALUES (4040, 8401, 4256);
INSERT INTO public.ports VALUES (4042, 8403, 4257);
INSERT INTO public.ports VALUES (4043, 8404, 4258);
INSERT INTO public.ports VALUES (4044, 8405, 4259);
INSERT INTO public.ports VALUES (4045, 8406, 4260);
INSERT INTO public.ports VALUES (4047, 8408, 4261);
INSERT INTO public.ports VALUES (4048, 8409, 4262);
INSERT INTO public.ports VALUES (4049, 8410, 4263);
INSERT INTO public.ports VALUES (4050, 8411, 4264);
INSERT INTO public.ports VALUES (4052, 8413, 4265);
INSERT INTO public.ports VALUES (4053, 8414, 4266);
INSERT INTO public.ports VALUES (4054, 8415, 4267);
INSERT INTO public.ports VALUES (4687, 9050, 4750);
INSERT INTO public.ports VALUES (4689, 9052, 4751);
INSERT INTO public.ports VALUES (4690, 9053, 4752);
INSERT INTO public.ports VALUES (4691, 9054, 4753);
INSERT INTO public.ports VALUES (4693, 9056, 4754);
INSERT INTO public.ports VALUES (4694, 9057, 4755);
INSERT INTO public.ports VALUES (4696, 9059, 4756);
INSERT INTO public.ports VALUES (4697, 9060, 4757);
INSERT INTO public.ports VALUES (4698, 9061, 4758);
INSERT INTO public.ports VALUES (4700, 9063, 4759);
INSERT INTO public.ports VALUES (4701, 9064, 4760);
INSERT INTO public.ports VALUES (4702, 9065, 4761);
INSERT INTO public.ports VALUES (4704, 9067, 4762);
INSERT INTO public.ports VALUES (4705, 9068, 4763);
INSERT INTO public.ports VALUES (4707, 9070, 4764);
INSERT INTO public.ports VALUES (4708, 9071, 4765);
INSERT INTO public.ports VALUES (4709, 9072, 4766);
INSERT INTO public.ports VALUES (4711, 9074, 4767);
INSERT INTO public.ports VALUES (4712, 9075, 4768);
INSERT INTO public.ports VALUES (4713, 9076, 4769);
INSERT INTO public.ports VALUES (4715, 9078, 4770);
INSERT INTO public.ports VALUES (4716, 9079, 4771);
INSERT INTO public.ports VALUES (4658, 9021, 4794);
INSERT INTO public.ports VALUES (4660, 9023, 4795);
INSERT INTO public.ports VALUES (4661, 9024, 4796);
INSERT INTO public.ports VALUES (4663, 9026, 4797);
INSERT INTO public.ports VALUES (4664, 9027, 4798);
INSERT INTO public.ports VALUES (4665, 9028, 4799);
INSERT INTO public.ports VALUES (4667, 9030, 4800);
INSERT INTO public.ports VALUES (4668, 9031, 4801);
INSERT INTO public.ports VALUES (4669, 9032, 4802);
INSERT INTO public.ports VALUES (4671, 9034, 4803);
INSERT INTO public.ports VALUES (4672, 9035, 4804);
INSERT INTO public.ports VALUES (4674, 9037, 4805);
INSERT INTO public.ports VALUES (4675, 9038, 4806);
INSERT INTO public.ports VALUES (4676, 9039, 4807);
INSERT INTO public.ports VALUES (4678, 9041, 4808);
INSERT INTO public.ports VALUES (4679, 9042, 4809);
INSERT INTO public.ports VALUES (4680, 9043, 4810);
INSERT INTO public.ports VALUES (4682, 9045, 4811);
INSERT INTO public.ports VALUES (4683, 9046, 4812);
INSERT INTO public.ports VALUES (4685, 9048, 4813);
INSERT INTO public.ports VALUES (4686, 9049, 4814);
INSERT INTO public.ports VALUES (6451, 9950, 4815);
INSERT INTO public.ports VALUES (6452, 9951, 4816);
INSERT INTO public.ports VALUES (6453, 9952, 4817);
INSERT INTO public.ports VALUES (6454, 9953, 4818);
INSERT INTO public.ports VALUES (6455, 9954, 4819);
INSERT INTO public.ports VALUES (6456, 9955, 4820);
INSERT INTO public.ports VALUES (6457, 9956, 4821);
INSERT INTO public.ports VALUES (6458, 9957, 4822);
INSERT INTO public.ports VALUES (6459, 9958, 4823);
INSERT INTO public.ports VALUES (6460, 9959, 4824);
INSERT INTO public.ports VALUES (6461, 9960, 4825);
INSERT INTO public.ports VALUES (6462, 9961, 4826);
INSERT INTO public.ports VALUES (6463, 9962, 4827);
INSERT INTO public.ports VALUES (6464, 9963, 4828);
INSERT INTO public.ports VALUES (6465, 9964, 4829);
INSERT INTO public.ports VALUES (6466, 9965, 4830);
INSERT INTO public.ports VALUES (6467, 9966, 4831);
INSERT INTO public.ports VALUES (6468, 9967, 4832);
INSERT INTO public.ports VALUES (6469, 9968, 4833);
INSERT INTO public.ports VALUES (6470, 9969, 4834);
INSERT INTO public.ports VALUES (6471, 9970, 4835);
INSERT INTO public.ports VALUES (6472, 9971, 4836);
INSERT INTO public.ports VALUES (6473, 9972, 4837);
INSERT INTO public.ports VALUES (6474, 9973, 4838);
INSERT INTO public.ports VALUES (6475, 9974, 4839);
INSERT INTO public.ports VALUES (6476, 9975, 4840);
INSERT INTO public.ports VALUES (6477, 9976, 4841);
INSERT INTO public.ports VALUES (6478, 9977, 4842);
INSERT INTO public.ports VALUES (6479, 9978, 4843);
INSERT INTO public.ports VALUES (6480, 9979, 4844);
INSERT INTO public.ports VALUES (6481, 9980, 4845);
INSERT INTO public.ports VALUES (6482, 9981, 4846);
INSERT INTO public.ports VALUES (6483, 9982, 4847);
INSERT INTO public.ports VALUES (6484, 9983, 4848);
INSERT INTO public.ports VALUES (6485, 9984, 4849);
INSERT INTO public.ports VALUES (6486, 9985, 4850);
INSERT INTO public.ports VALUES (6487, 9986, 4851);
INSERT INTO public.ports VALUES (6488, 9987, 4852);
INSERT INTO public.ports VALUES (6489, 9988, 4853);
INSERT INTO public.ports VALUES (6490, 9989, 4854);
INSERT INTO public.ports VALUES (6491, 9990, 4855);
INSERT INTO public.ports VALUES (6493, 9992, 4857);
INSERT INTO public.ports VALUES (6494, 9993, 4858);
INSERT INTO public.ports VALUES (6495, 9994, 4859);
INSERT INTO public.ports VALUES (6496, 9995, 4860);
INSERT INTO public.ports VALUES (6498, 9997, 4862);
INSERT INTO public.ports VALUES (6499, 9998, 4863);
INSERT INTO public.ports VALUES (6500, 9999, 4864);
INSERT INTO public.ports VALUES (5243, 9606, 3258);
INSERT INTO public.ports VALUES (5250, 9613, 3263);
INSERT INTO public.ports VALUES (5257, 9620, 3268);
INSERT INTO public.ports VALUES (5264, 9627, 3273);
INSERT INTO public.ports VALUES (5271, 9634, 3278);
INSERT INTO public.ports VALUES (5638, 7601, 3283);
INSERT INTO public.ports VALUES (5643, 7606, 3288);
INSERT INTO public.ports VALUES (5648, 7611, 3293);
INSERT INTO public.ports VALUES (5653, 7616, 3298);
INSERT INTO public.ports VALUES (5658, 7621, 3303);
INSERT INTO public.ports VALUES (5663, 7626, 3308);
INSERT INTO public.ports VALUES (5668, 7631, 3313);
INSERT INTO public.ports VALUES (5673, 7636, 3318);
INSERT INTO public.ports VALUES (5678, 7641, 3323);
INSERT INTO public.ports VALUES (5683, 7646, 3328);
INSERT INTO public.ports VALUES (5688, 7651, 3333);
INSERT INTO public.ports VALUES (5691, 7654, 3336);
INSERT INTO public.ports VALUES (5692, 7655, 3337);
INSERT INTO public.ports VALUES (5693, 7656, 3338);
INSERT INTO public.ports VALUES (5694, 7657, 3339);
INSERT INTO public.ports VALUES (5695, 7658, 3340);
INSERT INTO public.ports VALUES (5696, 7659, 3341);
INSERT INTO public.ports VALUES (5697, 7660, 3342);
INSERT INTO public.ports VALUES (5698, 7661, 3343);
INSERT INTO public.ports VALUES (5699, 7662, 3344);
INSERT INTO public.ports VALUES (5700, 7663, 3345);
INSERT INTO public.ports VALUES (5701, 7664, 3346);
INSERT INTO public.ports VALUES (5702, 7665, 3347);
INSERT INTO public.ports VALUES (5703, 7666, 3348);
INSERT INTO public.ports VALUES (5704, 7667, 3349);
INSERT INTO public.ports VALUES (5705, 7668, 3350);
INSERT INTO public.ports VALUES (5706, 7669, 3351);
INSERT INTO public.ports VALUES (5707, 7670, 3352);
INSERT INTO public.ports VALUES (5708, 7671, 3353);
INSERT INTO public.ports VALUES (5709, 7672, 3354);
INSERT INTO public.ports VALUES (5710, 7673, 3355);
INSERT INTO public.ports VALUES (5711, 7674, 3356);
INSERT INTO public.ports VALUES (5712, 7675, 3357);
INSERT INTO public.ports VALUES (5713, 7676, 3358);
INSERT INTO public.ports VALUES (5714, 7677, 3359);
INSERT INTO public.ports VALUES (5715, 7678, 3360);
INSERT INTO public.ports VALUES (5716, 7679, 3361);
INSERT INTO public.ports VALUES (5717, 7680, 3362);
INSERT INTO public.ports VALUES (5718, 7681, 3363);
INSERT INTO public.ports VALUES (5719, 7682, 3364);
INSERT INTO public.ports VALUES (5720, 7683, 3365);
INSERT INTO public.ports VALUES (5721, 7684, 3366);
INSERT INTO public.ports VALUES (5722, 7685, 3367);
INSERT INTO public.ports VALUES (5723, 7686, 3368);
INSERT INTO public.ports VALUES (5724, 7687, 3369);
INSERT INTO public.ports VALUES (5725, 7688, 3370);
INSERT INTO public.ports VALUES (5726, 7689, 3371);
INSERT INTO public.ports VALUES (5727, 7690, 3372);
INSERT INTO public.ports VALUES (5728, 7691, 3373);
INSERT INTO public.ports VALUES (5729, 7692, 3374);
INSERT INTO public.ports VALUES (5730, 7693, 3375);
INSERT INTO public.ports VALUES (5731, 7694, 3376);
INSERT INTO public.ports VALUES (5732, 7695, 3377);
INSERT INTO public.ports VALUES (5733, 7696, 3378);
INSERT INTO public.ports VALUES (5734, 7697, 3379);
INSERT INTO public.ports VALUES (5735, 7698, 3380);
INSERT INTO public.ports VALUES (5736, 7699, 3381);
INSERT INTO public.ports VALUES (5737, 7700, 3382);
INSERT INTO public.ports VALUES (5738, 7701, 3383);
INSERT INTO public.ports VALUES (5739, 7702, 3384);
INSERT INTO public.ports VALUES (5740, 7703, 3385);
INSERT INTO public.ports VALUES (5741, 7704, 3386);
INSERT INTO public.ports VALUES (5742, 7705, 3387);
INSERT INTO public.ports VALUES (5743, 7706, 3388);
INSERT INTO public.ports VALUES (5744, 7707, 3389);
INSERT INTO public.ports VALUES (5745, 7708, 3390);
INSERT INTO public.ports VALUES (5746, 7709, 3391);
INSERT INTO public.ports VALUES (5747, 7710, 3392);
INSERT INTO public.ports VALUES (5748, 7711, 3393);
INSERT INTO public.ports VALUES (5749, 7712, 3394);
INSERT INTO public.ports VALUES (5750, 7713, 3395);
INSERT INTO public.ports VALUES (5751, 7714, 3396);
INSERT INTO public.ports VALUES (5752, 7715, 3397);
INSERT INTO public.ports VALUES (5753, 7716, 3398);
INSERT INTO public.ports VALUES (5754, 7717, 3399);
INSERT INTO public.ports VALUES (5755, 7718, 3400);
INSERT INTO public.ports VALUES (5756, 7719, 3401);
INSERT INTO public.ports VALUES (5757, 7720, 3402);
INSERT INTO public.ports VALUES (5758, 7721, 3403);
INSERT INTO public.ports VALUES (5759, 7722, 3404);
INSERT INTO public.ports VALUES (5760, 7723, 3405);
INSERT INTO public.ports VALUES (5761, 7724, 3406);
INSERT INTO public.ports VALUES (5762, 7725, 3407);
INSERT INTO public.ports VALUES (5763, 7726, 3408);
INSERT INTO public.ports VALUES (5764, 7727, 3409);
INSERT INTO public.ports VALUES (5765, 7728, 3410);
INSERT INTO public.ports VALUES (5766, 7729, 3411);
INSERT INTO public.ports VALUES (5767, 7730, 3412);
INSERT INTO public.ports VALUES (5768, 7731, 3413);
INSERT INTO public.ports VALUES (5769, 7732, 3414);
INSERT INTO public.ports VALUES (5770, 7733, 3415);
INSERT INTO public.ports VALUES (5771, 7734, 3416);
INSERT INTO public.ports VALUES (5772, 7735, 3417);
INSERT INTO public.ports VALUES (5773, 7736, 3418);
INSERT INTO public.ports VALUES (5774, 7737, 3419);
INSERT INTO public.ports VALUES (5775, 7738, 3420);
INSERT INTO public.ports VALUES (5776, 7739, 3421);
INSERT INTO public.ports VALUES (5777, 7740, 3422);
INSERT INTO public.ports VALUES (5778, 7741, 3423);
INSERT INTO public.ports VALUES (5779, 7742, 3424);
INSERT INTO public.ports VALUES (5780, 7743, 3425);
INSERT INTO public.ports VALUES (5781, 7744, 3426);
INSERT INTO public.ports VALUES (5782, 7745, 3427);
INSERT INTO public.ports VALUES (5783, 7746, 3428);
INSERT INTO public.ports VALUES (5784, 7747, 3429);
INSERT INTO public.ports VALUES (5785, 7748, 3430);
INSERT INTO public.ports VALUES (5786, 7749, 3431);
INSERT INTO public.ports VALUES (5787, 7750, 3432);
INSERT INTO public.ports VALUES (5788, 7751, 3433);
INSERT INTO public.ports VALUES (5789, 7752, 3434);
INSERT INTO public.ports VALUES (5790, 7753, 3435);
INSERT INTO public.ports VALUES (5791, 7754, 3436);
INSERT INTO public.ports VALUES (5792, 7755, 3437);
INSERT INTO public.ports VALUES (5793, 7756, 3438);
INSERT INTO public.ports VALUES (5794, 7757, 3439);
INSERT INTO public.ports VALUES (5032, 9395, 4867);
INSERT INTO public.ports VALUES (5034, 9397, 4868);
INSERT INTO public.ports VALUES (5035, 9398, 4869);
INSERT INTO public.ports VALUES (5036, 9399, 4870);
INSERT INTO public.ports VALUES (5039, 9402, 4872);
INSERT INTO public.ports VALUES (5041, 9404, 4873);
INSERT INTO public.ports VALUES (5042, 9405, 4874);
INSERT INTO public.ports VALUES (5043, 9406, 4875);
INSERT INTO public.ports VALUES (5046, 9409, 4877);
INSERT INTO public.ports VALUES (5047, 9410, 4878);
INSERT INTO public.ports VALUES (5049, 9412, 4879);
INSERT INTO public.ports VALUES (4961, 9324, 4880);
INSERT INTO public.ports VALUES (4964, 9327, 4882);
INSERT INTO public.ports VALUES (4965, 9328, 4883);
INSERT INTO public.ports VALUES (4966, 9329, 4884);
INSERT INTO public.ports VALUES (4968, 9331, 4885);
INSERT INTO public.ports VALUES (4970, 9333, 4887);
INSERT INTO public.ports VALUES (4972, 9335, 4888);
INSERT INTO public.ports VALUES (4973, 9336, 4889);
INSERT INTO public.ports VALUES (4975, 9338, 4890);
INSERT INTO public.ports VALUES (4977, 9340, 4892);
INSERT INTO public.ports VALUES (4979, 9342, 4893);
INSERT INTO public.ports VALUES (4980, 9343, 4894);
INSERT INTO public.ports VALUES (4981, 9344, 4895);
INSERT INTO public.ports VALUES (4984, 9347, 4897);
INSERT INTO public.ports VALUES (4986, 9349, 4898);
INSERT INTO public.ports VALUES (4987, 9350, 4899);
INSERT INTO public.ports VALUES (4988, 9351, 4900);
INSERT INTO public.ports VALUES (4991, 9354, 4902);
INSERT INTO public.ports VALUES (4992, 9355, 4903);
INSERT INTO public.ports VALUES (4994, 9357, 4904);
INSERT INTO public.ports VALUES (4995, 9358, 4905);
INSERT INTO public.ports VALUES (4998, 9361, 4907);
INSERT INTO public.ports VALUES (4999, 9362, 4908);
INSERT INTO public.ports VALUES (5001, 9364, 4909);
INSERT INTO public.ports VALUES (5002, 9365, 4910);
INSERT INTO public.ports VALUES (5005, 9368, 4912);
INSERT INTO public.ports VALUES (5006, 9369, 4913);
INSERT INTO public.ports VALUES (5008, 9371, 4914);
INSERT INTO public.ports VALUES (5009, 9372, 4915);
INSERT INTO public.ports VALUES (5012, 9375, 4917);
INSERT INTO public.ports VALUES (5013, 9376, 4918);
INSERT INTO public.ports VALUES (5014, 9377, 4919);
INSERT INTO public.ports VALUES (5016, 9379, 4920);
INSERT INTO public.ports VALUES (5019, 9382, 4922);
INSERT INTO public.ports VALUES (5020, 9383, 4923);
INSERT INTO public.ports VALUES (5021, 9384, 4924);
INSERT INTO public.ports VALUES (5023, 9386, 4925);
INSERT INTO public.ports VALUES (5025, 9388, 4927);
INSERT INTO public.ports VALUES (5027, 9390, 4928);
INSERT INTO public.ports VALUES (5028, 9391, 4929);
INSERT INTO public.ports VALUES (5795, 7758, 3440);
INSERT INTO public.ports VALUES (5796, 7759, 3441);
INSERT INTO public.ports VALUES (5797, 7760, 3442);
INSERT INTO public.ports VALUES (5798, 7761, 3443);
INSERT INTO public.ports VALUES (5799, 7762, 3444);
INSERT INTO public.ports VALUES (5800, 7763, 3445);
INSERT INTO public.ports VALUES (5801, 7764, 3446);
INSERT INTO public.ports VALUES (5802, 7765, 3447);
INSERT INTO public.ports VALUES (5803, 7766, 3448);
INSERT INTO public.ports VALUES (5804, 7767, 3449);
INSERT INTO public.ports VALUES (5805, 7768, 3450);
INSERT INTO public.ports VALUES (5806, 7769, 3451);
INSERT INTO public.ports VALUES (5807, 7770, 3452);
INSERT INTO public.ports VALUES (5808, 7771, 3453);
INSERT INTO public.ports VALUES (5809, 7772, 3454);
INSERT INTO public.ports VALUES (5810, 7773, 3455);
INSERT INTO public.ports VALUES (5811, 7774, 3456);
INSERT INTO public.ports VALUES (5812, 7775, 3457);
INSERT INTO public.ports VALUES (5813, 7776, 3458);
INSERT INTO public.ports VALUES (5814, 7777, 3459);
INSERT INTO public.ports VALUES (5815, 7778, 3460);
INSERT INTO public.ports VALUES (5816, 7779, 3461);
INSERT INTO public.ports VALUES (2913, 8112, 3462);
INSERT INTO public.ports VALUES (2933, 8132, 3466);
INSERT INTO public.ports VALUES (2958, 8157, 3471);
INSERT INTO public.ports VALUES (2983, 8182, 3476);
INSERT INTO public.ports VALUES (3007, 8206, 3481);
INSERT INTO public.ports VALUES (3022, 8221, 3486);
INSERT INTO public.ports VALUES (3027, 8226, 3491);
INSERT INTO public.ports VALUES (3032, 8231, 3496);
INSERT INTO public.ports VALUES (4055, 8416, 4268);
INSERT INTO public.ports VALUES (4057, 8418, 4269);
INSERT INTO public.ports VALUES (4058, 8419, 4270);
INSERT INTO public.ports VALUES (4059, 8420, 4271);
INSERT INTO public.ports VALUES (4060, 8421, 4272);
INSERT INTO public.ports VALUES (4062, 8423, 4273);
INSERT INTO public.ports VALUES (4063, 8424, 4274);
INSERT INTO public.ports VALUES (4064, 8425, 4275);
INSERT INTO public.ports VALUES (4065, 8426, 4276);
INSERT INTO public.ports VALUES (4067, 8428, 4277);
INSERT INTO public.ports VALUES (4068, 8429, 4278);
INSERT INTO public.ports VALUES (4069, 8430, 4279);
INSERT INTO public.ports VALUES (4070, 8431, 4280);
INSERT INTO public.ports VALUES (4072, 8433, 4281);
INSERT INTO public.ports VALUES (4073, 8434, 4282);
INSERT INTO public.ports VALUES (4074, 8435, 4283);
INSERT INTO public.ports VALUES (4075, 8436, 4284);
INSERT INTO public.ports VALUES (4077, 8438, 4285);
INSERT INTO public.ports VALUES (4078, 8439, 4286);
INSERT INTO public.ports VALUES (4079, 8440, 4287);
INSERT INTO public.ports VALUES (4080, 8441, 4288);
INSERT INTO public.ports VALUES (4082, 8443, 4289);
INSERT INTO public.ports VALUES (4083, 8444, 4290);
INSERT INTO public.ports VALUES (4084, 8445, 4291);
INSERT INTO public.ports VALUES (4085, 8446, 4292);
INSERT INTO public.ports VALUES (4087, 8448, 4293);
INSERT INTO public.ports VALUES (4088, 8449, 4294);
INSERT INTO public.ports VALUES (4089, 8450, 4295);
INSERT INTO public.ports VALUES (4090, 8451, 4296);
INSERT INTO public.ports VALUES (4092, 8453, 4297);
INSERT INTO public.ports VALUES (4093, 8454, 4298);
INSERT INTO public.ports VALUES (4094, 8455, 4299);
INSERT INTO public.ports VALUES (4095, 8456, 4300);
INSERT INTO public.ports VALUES (4097, 8458, 4301);
INSERT INTO public.ports VALUES (4098, 8459, 4302);
INSERT INTO public.ports VALUES (4099, 8460, 4303);
INSERT INTO public.ports VALUES (4100, 8461, 4304);
INSERT INTO public.ports VALUES (6168, 8591, 4305);
INSERT INTO public.ports VALUES (6169, 8592, 4306);
INSERT INTO public.ports VALUES (6170, 8593, 4307);
INSERT INTO public.ports VALUES (6171, 8594, 4308);
INSERT INTO public.ports VALUES (6172, 8595, 4309);
INSERT INTO public.ports VALUES (6173, 8596, 4310);
INSERT INTO public.ports VALUES (5286, 9649, 4931);
INSERT INTO public.ports VALUES (5287, 9650, 4932);
INSERT INTO public.ports VALUES (5289, 9652, 4934);
INSERT INTO public.ports VALUES (5290, 9653, 4935);
INSERT INTO public.ports VALUES (5291, 9654, 4936);
INSERT INTO public.ports VALUES (5292, 9655, 4937);
INSERT INTO public.ports VALUES (5294, 9657, 4939);
INSERT INTO public.ports VALUES (5295, 9658, 4940);
INSERT INTO public.ports VALUES (5296, 9659, 4941);
INSERT INTO public.ports VALUES (5297, 9660, 4942);
INSERT INTO public.ports VALUES (5299, 9662, 4944);
INSERT INTO public.ports VALUES (5300, 9663, 4945);
INSERT INTO public.ports VALUES (5301, 9664, 4946);
INSERT INTO public.ports VALUES (5302, 9665, 4947);
INSERT INTO public.ports VALUES (5304, 9667, 4949);
INSERT INTO public.ports VALUES (5305, 9668, 4950);
INSERT INTO public.ports VALUES (5306, 9669, 4951);
INSERT INTO public.ports VALUES (5307, 9670, 4952);
INSERT INTO public.ports VALUES (5309, 9672, 4954);
INSERT INTO public.ports VALUES (5310, 9673, 4955);
INSERT INTO public.ports VALUES (5311, 9674, 4956);
INSERT INTO public.ports VALUES (5312, 9675, 4957);
INSERT INTO public.ports VALUES (5314, 9677, 4959);
INSERT INTO public.ports VALUES (5315, 9678, 4960);
INSERT INTO public.ports VALUES (5316, 9679, 4961);
INSERT INTO public.ports VALUES (5317, 9680, 4962);
INSERT INTO public.ports VALUES (5319, 9682, 4964);
INSERT INTO public.ports VALUES (5320, 9683, 4965);
INSERT INTO public.ports VALUES (5321, 9684, 4966);
INSERT INTO public.ports VALUES (5322, 9685, 4967);
INSERT INTO public.ports VALUES (5325, 9688, 4969);
INSERT INTO public.ports VALUES (5326, 9689, 4970);
INSERT INTO public.ports VALUES (5327, 9690, 4971);
INSERT INTO public.ports VALUES (5329, 9692, 4972);
INSERT INTO public.ports VALUES (5331, 9694, 4974);
INSERT INTO public.ports VALUES (5332, 9695, 4975);
INSERT INTO public.ports VALUES (5334, 9697, 4976);
INSERT INTO public.ports VALUES (5335, 9698, 4977);
INSERT INTO public.ports VALUES (5337, 9700, 4979);
INSERT INTO public.ports VALUES (5339, 9702, 4980);
INSERT INTO public.ports VALUES (5340, 9703, 4981);
INSERT INTO public.ports VALUES (5341, 9704, 4982);
INSERT INTO public.ports VALUES (5344, 9707, 4984);
INSERT INTO public.ports VALUES (5345, 9708, 4985);
INSERT INTO public.ports VALUES (5346, 9709, 4986);
INSERT INTO public.ports VALUES (5347, 9710, 4987);
INSERT INTO public.ports VALUES (5350, 9713, 4989);
INSERT INTO public.ports VALUES (5351, 9714, 4990);
INSERT INTO public.ports VALUES (5352, 9715, 4991);
INSERT INTO public.ports VALUES (5354, 9717, 4992);
INSERT INTO public.ports VALUES (5356, 9719, 4994);
INSERT INTO public.ports VALUES (5357, 9720, 4995);
INSERT INTO public.ports VALUES (5359, 9722, 4996);
INSERT INTO public.ports VALUES (5360, 9723, 4997);
INSERT INTO public.ports VALUES (5362, 9725, 4999);
INSERT INTO public.ports VALUES (5364, 9727, 5000);
INSERT INTO public.ports VALUES (5365, 9728, 5001);
INSERT INTO public.ports VALUES (5366, 9729, 5002);
INSERT INTO public.ports VALUES (5369, 9732, 5004);
INSERT INTO public.ports VALUES (5370, 9733, 5005);
INSERT INTO public.ports VALUES (5371, 9734, 5006);
INSERT INTO public.ports VALUES (5372, 9735, 5007);
INSERT INTO public.ports VALUES (5375, 9738, 5009);
INSERT INTO public.ports VALUES (5376, 9739, 5010);
INSERT INTO public.ports VALUES (5377, 9740, 5011);
INSERT INTO public.ports VALUES (5379, 9742, 5012);
INSERT INTO public.ports VALUES (5381, 9744, 5014);
INSERT INTO public.ports VALUES (5382, 9745, 5015);
INSERT INTO public.ports VALUES (5384, 9747, 5016);
INSERT INTO public.ports VALUES (5385, 9748, 5017);
INSERT INTO public.ports VALUES (5387, 9750, 5019);
INSERT INTO public.ports VALUES (5389, 9752, 5020);
INSERT INTO public.ports VALUES (5390, 9753, 5021);
INSERT INTO public.ports VALUES (5391, 9754, 5022);
INSERT INTO public.ports VALUES (5394, 9757, 5024);
INSERT INTO public.ports VALUES (5395, 9758, 5025);
INSERT INTO public.ports VALUES (5396, 9759, 5026);
INSERT INTO public.ports VALUES (5397, 9760, 5027);
INSERT INTO public.ports VALUES (5187, 9550, 5029);
INSERT INTO public.ports VALUES (5188, 9551, 5030);
INSERT INTO public.ports VALUES (5190, 9553, 5031);
INSERT INTO public.ports VALUES (5191, 9554, 5032);
INSERT INTO public.ports VALUES (5194, 9557, 5034);
INSERT INTO public.ports VALUES (5195, 9558, 5035);
INSERT INTO public.ports VALUES (5196, 9559, 5036);
INSERT INTO public.ports VALUES (5198, 9561, 5037);
INSERT INTO public.ports VALUES (5201, 9564, 5039);
INSERT INTO public.ports VALUES (5202, 9565, 5040);
INSERT INTO public.ports VALUES (5203, 9566, 5041);
INSERT INTO public.ports VALUES (5205, 9568, 5042);
INSERT INTO public.ports VALUES (5207, 9570, 5044);
INSERT INTO public.ports VALUES (3037, 8236, 3501);
INSERT INTO public.ports VALUES (4852, 9215, 3506);
INSERT INTO public.ports VALUES (4857, 9220, 3511);
INSERT INTO public.ports VALUES (4862, 9225, 3516);
INSERT INTO public.ports VALUES (4867, 9230, 3521);
INSERT INTO public.ports VALUES (4872, 9235, 3526);
INSERT INTO public.ports VALUES (4877, 9240, 3531);
INSERT INTO public.ports VALUES (4882, 9245, 3536);
INSERT INTO public.ports VALUES (4887, 9250, 3541);
INSERT INTO public.ports VALUES (4892, 9255, 3546);
INSERT INTO public.ports VALUES (4897, 9260, 3551);
INSERT INTO public.ports VALUES (4900, 9263, 3554);
INSERT INTO public.ports VALUES (4901, 9264, 3555);
INSERT INTO public.ports VALUES (4902, 9265, 3556);
INSERT INTO public.ports VALUES (4903, 9266, 3557);
INSERT INTO public.ports VALUES (4904, 9267, 3558);
INSERT INTO public.ports VALUES (4905, 9268, 3559);
INSERT INTO public.ports VALUES (4906, 9269, 3560);
INSERT INTO public.ports VALUES (4907, 9270, 3561);
INSERT INTO public.ports VALUES (4908, 9271, 3562);
INSERT INTO public.ports VALUES (4909, 9272, 3563);
INSERT INTO public.ports VALUES (4910, 9273, 3564);
INSERT INTO public.ports VALUES (4960, 9323, 3565);
INSERT INTO public.ports VALUES (4963, 9326, 3566);
INSERT INTO public.ports VALUES (4967, 9330, 3567);
INSERT INTO public.ports VALUES (4971, 9334, 3568);
INSERT INTO public.ports VALUES (4974, 9337, 3569);
INSERT INTO public.ports VALUES (4978, 9341, 3570);
INSERT INTO public.ports VALUES (4982, 9345, 3571);
INSERT INTO public.ports VALUES (3041, 8240, 3572);
INSERT INTO public.ports VALUES (3042, 8241, 3573);
INSERT INTO public.ports VALUES (3043, 8242, 3574);
INSERT INTO public.ports VALUES (3044, 8243, 3575);
INSERT INTO public.ports VALUES (3045, 8244, 3576);
INSERT INTO public.ports VALUES (3046, 8245, 3577);
INSERT INTO public.ports VALUES (3047, 8246, 3578);
INSERT INTO public.ports VALUES (3048, 8247, 3579);
INSERT INTO public.ports VALUES (3049, 8248, 3580);
INSERT INTO public.ports VALUES (3050, 8249, 3581);
INSERT INTO public.ports VALUES (3051, 8250, 3582);
INSERT INTO public.ports VALUES (3052, 8251, 3583);
INSERT INTO public.ports VALUES (3053, 8252, 3584);
INSERT INTO public.ports VALUES (3054, 8253, 3585);
INSERT INTO public.ports VALUES (3055, 8254, 3586);
INSERT INTO public.ports VALUES (3056, 8255, 3587);
INSERT INTO public.ports VALUES (3057, 8256, 3588);
INSERT INTO public.ports VALUES (3058, 8257, 3589);
INSERT INTO public.ports VALUES (3059, 8258, 3590);
INSERT INTO public.ports VALUES (3060, 8259, 3591);
INSERT INTO public.ports VALUES (3061, 8260, 3592);
INSERT INTO public.ports VALUES (3062, 8261, 3593);
INSERT INTO public.ports VALUES (3063, 8262, 3594);
INSERT INTO public.ports VALUES (3064, 8263, 3595);
INSERT INTO public.ports VALUES (3682, 7855, 3596);
INSERT INTO public.ports VALUES (3683, 7856, 3597);
INSERT INTO public.ports VALUES (3684, 7857, 3598);
INSERT INTO public.ports VALUES (3685, 7858, 3599);
INSERT INTO public.ports VALUES (3687, 7860, 3600);
INSERT INTO public.ports VALUES (3688, 7861, 3601);
INSERT INTO public.ports VALUES (3689, 7862, 3602);
INSERT INTO public.ports VALUES (3690, 7863, 3603);
INSERT INTO public.ports VALUES (3692, 7865, 3604);
INSERT INTO public.ports VALUES (3693, 7866, 3605);
INSERT INTO public.ports VALUES (3694, 7867, 3606);
INSERT INTO public.ports VALUES (3695, 7868, 3607);
INSERT INTO public.ports VALUES (3697, 7870, 3608);
INSERT INTO public.ports VALUES (3698, 7871, 3609);
INSERT INTO public.ports VALUES (3699, 7872, 3610);
INSERT INTO public.ports VALUES (3700, 7873, 3611);
INSERT INTO public.ports VALUES (3702, 7875, 3612);
INSERT INTO public.ports VALUES (3703, 7876, 3613);
INSERT INTO public.ports VALUES (3704, 7877, 3614);
INSERT INTO public.ports VALUES (3705, 7878, 3615);
INSERT INTO public.ports VALUES (3707, 7880, 3616);
INSERT INTO public.ports VALUES (3708, 7881, 3617);
INSERT INTO public.ports VALUES (3709, 7882, 3618);
INSERT INTO public.ports VALUES (3710, 7883, 3619);
INSERT INTO public.ports VALUES (3712, 7885, 3620);
INSERT INTO public.ports VALUES (3713, 7886, 3621);
INSERT INTO public.ports VALUES (3714, 7887, 3622);
INSERT INTO public.ports VALUES (3715, 7888, 3623);
INSERT INTO public.ports VALUES (3717, 7890, 3624);
INSERT INTO public.ports VALUES (3718, 7891, 3625);
INSERT INTO public.ports VALUES (3719, 7892, 3626);
INSERT INTO public.ports VALUES (3720, 7893, 3627);
INSERT INTO public.ports VALUES (3722, 7895, 3628);
INSERT INTO public.ports VALUES (3723, 7896, 3629);
INSERT INTO public.ports VALUES (3724, 7897, 3630);
INSERT INTO public.ports VALUES (3725, 7898, 3631);
INSERT INTO public.ports VALUES (3727, 7900, 3632);
INSERT INTO public.ports VALUES (3728, 7901, 3633);
INSERT INTO public.ports VALUES (3729, 7902, 3634);
INSERT INTO public.ports VALUES (3730, 7903, 3635);
INSERT INTO public.ports VALUES (3732, 7905, 3636);
INSERT INTO public.ports VALUES (3733, 7906, 3637);
INSERT INTO public.ports VALUES (3734, 7907, 3638);
INSERT INTO public.ports VALUES (3735, 7908, 3639);
INSERT INTO public.ports VALUES (3737, 7910, 3640);
INSERT INTO public.ports VALUES (3738, 7911, 3641);
INSERT INTO public.ports VALUES (3739, 7912, 3642);
INSERT INTO public.ports VALUES (3740, 7913, 3643);
INSERT INTO public.ports VALUES (3742, 7915, 3644);
INSERT INTO public.ports VALUES (3743, 7916, 3645);
INSERT INTO public.ports VALUES (3744, 7917, 3646);
INSERT INTO public.ports VALUES (3745, 7918, 3647);
INSERT INTO public.ports VALUES (3747, 7920, 3648);
INSERT INTO public.ports VALUES (3748, 7921, 3649);
INSERT INTO public.ports VALUES (4985, 9348, 3650);
INSERT INTO public.ports VALUES (4989, 9352, 3651);
INSERT INTO public.ports VALUES (4993, 9356, 3652);
INSERT INTO public.ports VALUES (4996, 9359, 3653);
INSERT INTO public.ports VALUES (5000, 9363, 3654);
INSERT INTO public.ports VALUES (5004, 9367, 3655);
INSERT INTO public.ports VALUES (5007, 9370, 3656);
INSERT INTO public.ports VALUES (5011, 9374, 3657);
INSERT INTO public.ports VALUES (5015, 9378, 3658);
INSERT INTO public.ports VALUES (5018, 9381, 3659);
INSERT INTO public.ports VALUES (5022, 9385, 3660);
INSERT INTO public.ports VALUES (5026, 9389, 3661);
INSERT INTO public.ports VALUES (5817, 7780, 3662);
INSERT INTO public.ports VALUES (5818, 7781, 3663);
INSERT INTO public.ports VALUES (5819, 7782, 3664);
INSERT INTO public.ports VALUES (5820, 7783, 3665);
INSERT INTO public.ports VALUES (5416, 9779, 5059);
INSERT INTO public.ports VALUES (5417, 9780, 5060);
INSERT INTO public.ports VALUES (5419, 9782, 5061);
INSERT INTO public.ports VALUES (5420, 9783, 5062);
INSERT INTO public.ports VALUES (5423, 9786, 5064);
INSERT INTO public.ports VALUES (5424, 9787, 5065);
INSERT INTO public.ports VALUES (5425, 9788, 5066);
INSERT INTO public.ports VALUES (5427, 9790, 5067);
INSERT INTO public.ports VALUES (5430, 9793, 5069);
INSERT INTO public.ports VALUES (5431, 9794, 5070);
INSERT INTO public.ports VALUES (5432, 9795, 5071);
INSERT INTO public.ports VALUES (5434, 9797, 5072);
INSERT INTO public.ports VALUES (5436, 9799, 5074);
INSERT INTO public.ports VALUES (5438, 9801, 5075);
INSERT INTO public.ports VALUES (5439, 9802, 5076);
INSERT INTO public.ports VALUES (5441, 9804, 5077);
INSERT INTO public.ports VALUES (5443, 9806, 5079);
INSERT INTO public.ports VALUES (5445, 9808, 5080);
INSERT INTO public.ports VALUES (5446, 9809, 5081);
INSERT INTO public.ports VALUES (5447, 9810, 5082);
INSERT INTO public.ports VALUES (5450, 9813, 5084);
INSERT INTO public.ports VALUES (5452, 9815, 5085);
INSERT INTO public.ports VALUES (5453, 9816, 5086);
INSERT INTO public.ports VALUES (5454, 9817, 5087);
INSERT INTO public.ports VALUES (5457, 9820, 5089);
INSERT INTO public.ports VALUES (5458, 9821, 5090);
INSERT INTO public.ports VALUES (5460, 9823, 5091);
INSERT INTO public.ports VALUES (5461, 9824, 5092);
INSERT INTO public.ports VALUES (5464, 9827, 5094);
INSERT INTO public.ports VALUES (5465, 9828, 5095);
INSERT INTO public.ports VALUES (5467, 9830, 5096);
INSERT INTO public.ports VALUES (5468, 9831, 5097);
INSERT INTO public.ports VALUES (5471, 9834, 5099);
INSERT INTO public.ports VALUES (5472, 9835, 5100);
INSERT INTO public.ports VALUES (5474, 9837, 5101);
INSERT INTO public.ports VALUES (5475, 9838, 5102);
INSERT INTO public.ports VALUES (5478, 9841, 5104);
INSERT INTO public.ports VALUES (5479, 9842, 5105);
INSERT INTO public.ports VALUES (5480, 9843, 5106);
INSERT INTO public.ports VALUES (5482, 9845, 5107);
INSERT INTO public.ports VALUES (5485, 9848, 5109);
INSERT INTO public.ports VALUES (5486, 9849, 5110);
INSERT INTO public.ports VALUES (5487, 9850, 5111);
INSERT INTO public.ports VALUES (5489, 9852, 5112);
INSERT INTO public.ports VALUES (5491, 9854, 5114);
INSERT INTO public.ports VALUES (5493, 9856, 5115);
INSERT INTO public.ports VALUES (5494, 9857, 5116);
INSERT INTO public.ports VALUES (5496, 9859, 5117);
INSERT INTO public.ports VALUES (5498, 9861, 5119);
INSERT INTO public.ports VALUES (5500, 9863, 5120);
INSERT INTO public.ports VALUES (5501, 9864, 5121);
INSERT INTO public.ports VALUES (5821, 7784, 3666);
INSERT INTO public.ports VALUES (5822, 7785, 3667);
INSERT INTO public.ports VALUES (5823, 7786, 3668);
INSERT INTO public.ports VALUES (5824, 7787, 3669);
INSERT INTO public.ports VALUES (5825, 7788, 3670);
INSERT INTO public.ports VALUES (5826, 7789, 3671);
INSERT INTO public.ports VALUES (5827, 7790, 3672);
INSERT INTO public.ports VALUES (5828, 7791, 3673);
INSERT INTO public.ports VALUES (5829, 7792, 3674);
INSERT INTO public.ports VALUES (5830, 7793, 3675);
INSERT INTO public.ports VALUES (5831, 7794, 3676);
INSERT INTO public.ports VALUES (5832, 7795, 3677);
INSERT INTO public.ports VALUES (5833, 7796, 3678);
INSERT INTO public.ports VALUES (5834, 7797, 3679);
INSERT INTO public.ports VALUES (5835, 7798, 3680);
INSERT INTO public.ports VALUES (5839, 7802, 3684);
INSERT INTO public.ports VALUES (5844, 7807, 3689);
INSERT INTO public.ports VALUES (5849, 7812, 3694);
INSERT INTO public.ports VALUES (5854, 7817, 3699);
INSERT INTO public.ports VALUES (5859, 7822, 3704);
INSERT INTO public.ports VALUES (5864, 7827, 3709);
INSERT INTO public.ports VALUES (5869, 7832, 3714);
INSERT INTO public.ports VALUES (5874, 7837, 3719);
INSERT INTO public.ports VALUES (5879, 7842, 3724);
INSERT INTO public.ports VALUES (5884, 7847, 3729);
INSERT INTO public.ports VALUES (5889, 7852, 3734);
INSERT INTO public.ports VALUES (5894, 7925, 3739);
INSERT INTO public.ports VALUES (5899, 7930, 3744);
INSERT INTO public.ports VALUES (5904, 7935, 3749);
INSERT INTO public.ports VALUES (2915, 8114, 3754);
INSERT INTO public.ports VALUES (6174, 8597, 4311);
INSERT INTO public.ports VALUES (6175, 8598, 4312);
INSERT INTO public.ports VALUES (6176, 8599, 4313);
INSERT INTO public.ports VALUES (6177, 8600, 4314);
INSERT INTO public.ports VALUES (6181, 8604, 4318);
INSERT INTO public.ports VALUES (6186, 8609, 4323);
INSERT INTO public.ports VALUES (6191, 8614, 4328);
INSERT INTO public.ports VALUES (6196, 8619, 4333);
INSERT INTO public.ports VALUES (6201, 8624, 4338);
INSERT INTO public.ports VALUES (6206, 8629, 4343);
INSERT INTO public.ports VALUES (6211, 8634, 4348);
INSERT INTO public.ports VALUES (6216, 8639, 4353);
INSERT INTO public.ports VALUES (6221, 8644, 4358);
INSERT INTO public.ports VALUES (6226, 8649, 4363);
INSERT INTO public.ports VALUES (6231, 8654, 4368);
INSERT INTO public.ports VALUES (6236, 8659, 4373);
INSERT INTO public.ports VALUES (6241, 8664, 4378);
INSERT INTO public.ports VALUES (6246, 8669, 4383);
INSERT INTO public.ports VALUES (6251, 8674, 4388);
INSERT INTO public.ports VALUES (6256, 8679, 4393);
INSERT INTO public.ports VALUES (6261, 8684, 4398);
INSERT INTO public.ports VALUES (6266, 8689, 4403);
INSERT INTO public.ports VALUES (3065, 8264, 4406);
INSERT INTO public.ports VALUES (3066, 8265, 4407);
INSERT INTO public.ports VALUES (3069, 8268, 4408);
INSERT INTO public.ports VALUES (3073, 8272, 4409);
INSERT INTO public.ports VALUES (3077, 8276, 4410);
INSERT INTO public.ports VALUES (3080, 8279, 4411);
INSERT INTO public.ports VALUES (3084, 8283, 4412);
INSERT INTO public.ports VALUES (3088, 8287, 4413);
INSERT INTO public.ports VALUES (3091, 8290, 4414);
INSERT INTO public.ports VALUES (5323, 9686, 4415);
INSERT INTO public.ports VALUES (5328, 9691, 4416);
INSERT INTO public.ports VALUES (5333, 9696, 4417);
INSERT INTO public.ports VALUES (5338, 9701, 4418);
INSERT INTO public.ports VALUES (5343, 9706, 4419);
INSERT INTO public.ports VALUES (5348, 9711, 4420);
INSERT INTO public.ports VALUES (5353, 9716, 4421);
INSERT INTO public.ports VALUES (5358, 9721, 4422);
INSERT INTO public.ports VALUES (5363, 9726, 4423);
INSERT INTO public.ports VALUES (5368, 9731, 4424);
INSERT INTO public.ports VALUES (5373, 9736, 4425);
INSERT INTO public.ports VALUES (3095, 8294, 4426);
INSERT INTO public.ports VALUES (3099, 8298, 4427);
INSERT INTO public.ports VALUES (5378, 9741, 4428);
INSERT INTO public.ports VALUES (5383, 9746, 4429);
INSERT INTO public.ports VALUES (5388, 9751, 4430);
INSERT INTO public.ports VALUES (5393, 9756, 4431);
INSERT INTO public.ports VALUES (5398, 9761, 4432);
INSERT INTO public.ports VALUES (5400, 9763, 4433);
INSERT INTO public.ports VALUES (5401, 9764, 4434);
INSERT INTO public.ports VALUES (5402, 9765, 4435);
INSERT INTO public.ports VALUES (5403, 9766, 4436);
INSERT INTO public.ports VALUES (5404, 9767, 4437);
INSERT INTO public.ports VALUES (5405, 9768, 4438);
INSERT INTO public.ports VALUES (5406, 9769, 4439);
INSERT INTO public.ports VALUES (5407, 9770, 4440);
INSERT INTO public.ports VALUES (5408, 9771, 4441);
INSERT INTO public.ports VALUES (5409, 9772, 4442);
INSERT INTO public.ports VALUES (5410, 9773, 4443);
INSERT INTO public.ports VALUES (5411, 9774, 4444);
INSERT INTO public.ports VALUES (5412, 9775, 4445);
INSERT INTO public.ports VALUES (5415, 9778, 4446);
INSERT INTO public.ports VALUES (5418, 9781, 4447);
INSERT INTO public.ports VALUES (5422, 9785, 4448);
INSERT INTO public.ports VALUES (5426, 9789, 4449);
INSERT INTO public.ports VALUES (5429, 9792, 4450);
INSERT INTO public.ports VALUES (5433, 9796, 4451);
INSERT INTO public.ports VALUES (5437, 9800, 4452);
INSERT INTO public.ports VALUES (5440, 9803, 4453);
INSERT INTO public.ports VALUES (5444, 9807, 4454);
INSERT INTO public.ports VALUES (5448, 9811, 4455);
INSERT INTO public.ports VALUES (5451, 9814, 4456);
INSERT INTO public.ports VALUES (5455, 9818, 4457);
INSERT INTO public.ports VALUES (5459, 9822, 4458);
INSERT INTO public.ports VALUES (5462, 9825, 4459);
INSERT INTO public.ports VALUES (5466, 9829, 4460);
INSERT INTO public.ports VALUES (5470, 9833, 4461);
INSERT INTO public.ports VALUES (5473, 9836, 4462);
INSERT INTO public.ports VALUES (5477, 9840, 4463);
INSERT INTO public.ports VALUES (5481, 9844, 4464);
INSERT INTO public.ports VALUES (5484, 9847, 4465);
INSERT INTO public.ports VALUES (5488, 9851, 4466);
INSERT INTO public.ports VALUES (5492, 9855, 4467);
INSERT INTO public.ports VALUES (5495, 9858, 4468);
INSERT INTO public.ports VALUES (5499, 9862, 4469);
INSERT INTO public.ports VALUES (5502, 9865, 4470);
INSERT INTO public.ports VALUES (5503, 9866, 4471);
INSERT INTO public.ports VALUES (5504, 9867, 4472);
INSERT INTO public.ports VALUES (5505, 9868, 4473);
INSERT INTO public.ports VALUES (5506, 9869, 4474);
INSERT INTO public.ports VALUES (5507, 9870, 4475);
INSERT INTO public.ports VALUES (5508, 9871, 4476);
INSERT INTO public.ports VALUES (5509, 9872, 4477);
INSERT INTO public.ports VALUES (5510, 9873, 4478);
INSERT INTO public.ports VALUES (5511, 9874, 4479);
INSERT INTO public.ports VALUES (5512, 9875, 4480);
INSERT INTO public.ports VALUES (5513, 9876, 4481);
INSERT INTO public.ports VALUES (5514, 9877, 4482);
INSERT INTO public.ports VALUES (5515, 9878, 4483);
INSERT INTO public.ports VALUES (5516, 9879, 4484);
INSERT INTO public.ports VALUES (5517, 9880, 4485);
INSERT INTO public.ports VALUES (5518, 9881, 4486);
INSERT INTO public.ports VALUES (5519, 9882, 4487);
INSERT INTO public.ports VALUES (5520, 9883, 4488);
INSERT INTO public.ports VALUES (5521, 9884, 4489);
INSERT INTO public.ports VALUES (5522, 9885, 4490);
INSERT INTO public.ports VALUES (5523, 9886, 4491);
INSERT INTO public.ports VALUES (5524, 9887, 4492);
INSERT INTO public.ports VALUES (5525, 9888, 4493);
INSERT INTO public.ports VALUES (5526, 9889, 4494);
INSERT INTO public.ports VALUES (5527, 9890, 4495);
INSERT INTO public.ports VALUES (5528, 9891, 4496);
INSERT INTO public.ports VALUES (5529, 9892, 4497);
INSERT INTO public.ports VALUES (5530, 9893, 4498);
INSERT INTO public.ports VALUES (5531, 9894, 4499);
INSERT INTO public.ports VALUES (5532, 9895, 4500);
INSERT INTO public.ports VALUES (5533, 9896, 4501);
INSERT INTO public.ports VALUES (5534, 9897, 4502);
INSERT INTO public.ports VALUES (5535, 9898, 4503);
INSERT INTO public.ports VALUES (5536, 9899, 4504);
INSERT INTO public.ports VALUES (5537, 9900, 4505);
INSERT INTO public.ports VALUES (5538, 9901, 4506);
INSERT INTO public.ports VALUES (5539, 9902, 4507);
INSERT INTO public.ports VALUES (5540, 9903, 4508);
INSERT INTO public.ports VALUES (6269, 8692, 4509);
INSERT INTO public.ports VALUES (6270, 8693, 4510);
INSERT INTO public.ports VALUES (6271, 8694, 4511);
INSERT INTO public.ports VALUES (6272, 8695, 4512);
INSERT INTO public.ports VALUES (6273, 8696, 4513);
INSERT INTO public.ports VALUES (6274, 8697, 4514);
INSERT INTO public.ports VALUES (6275, 8698, 4515);
INSERT INTO public.ports VALUES (6276, 8699, 4516);
INSERT INTO public.ports VALUES (3686, 7859, 5123);
INSERT INTO public.ports VALUES (3691, 7864, 5124);
INSERT INTO public.ports VALUES (3696, 7869, 5125);
INSERT INTO public.ports VALUES (3701, 7874, 5126);
INSERT INTO public.ports VALUES (3711, 7884, 5128);
INSERT INTO public.ports VALUES (3716, 7889, 5129);
INSERT INTO public.ports VALUES (3721, 7894, 5130);
INSERT INTO public.ports VALUES (3726, 7899, 5131);
INSERT INTO public.ports VALUES (3736, 7909, 5133);
INSERT INTO public.ports VALUES (3741, 7914, 5134);
INSERT INTO public.ports VALUES (3746, 7919, 5135);
INSERT INTO public.ports VALUES (6277, 8700, 4517);
INSERT INTO public.ports VALUES (6278, 8701, 4518);
INSERT INTO public.ports VALUES (6279, 8702, 4519);
INSERT INTO public.ports VALUES (6280, 8703, 4520);
INSERT INTO public.ports VALUES (6281, 8704, 4521);
INSERT INTO public.ports VALUES (6282, 8705, 4522);
INSERT INTO public.ports VALUES (6283, 8706, 4523);
INSERT INTO public.ports VALUES (4005, 8366, 5136);
INSERT INTO public.ports VALUES (4006, 8367, 5137);
INSERT INTO public.ports VALUES (4007, 8368, 5138);
INSERT INTO public.ports VALUES (4008, 8369, 5139);
INSERT INTO public.ports VALUES (4009, 8370, 5140);
INSERT INTO public.ports VALUES (4010, 8371, 5141);
INSERT INTO public.ports VALUES (4011, 8372, 5142);
INSERT INTO public.ports VALUES (4012, 8373, 5143);
INSERT INTO public.ports VALUES (4013, 8374, 5144);
INSERT INTO public.ports VALUES (4014, 8375, 5145);
INSERT INTO public.ports VALUES (4015, 8376, 5146);
INSERT INTO public.ports VALUES (4016, 8377, 5147);
INSERT INTO public.ports VALUES (4017, 8378, 5148);
INSERT INTO public.ports VALUES (4018, 8379, 5149);
INSERT INTO public.ports VALUES (4019, 8380, 5150);
INSERT INTO public.ports VALUES (4020, 8381, 5151);
INSERT INTO public.ports VALUES (4021, 8382, 5152);
INSERT INTO public.ports VALUES (4022, 8383, 5153);
INSERT INTO public.ports VALUES (4023, 8384, 5154);
INSERT INTO public.ports VALUES (4024, 8385, 5155);
INSERT INTO public.ports VALUES (4025, 8386, 5156);
INSERT INTO public.ports VALUES (4026, 8387, 5157);
INSERT INTO public.ports VALUES (4027, 8388, 5158);
INSERT INTO public.ports VALUES (4028, 8389, 5159);
INSERT INTO public.ports VALUES (4769, 9132, 5160);
INSERT INTO public.ports VALUES (4911, 9274, 5161);
INSERT INTO public.ports VALUES (4912, 9275, 5162);
INSERT INTO public.ports VALUES (4913, 9276, 5163);
INSERT INTO public.ports VALUES (4914, 9277, 5164);
INSERT INTO public.ports VALUES (4915, 9278, 5165);
INSERT INTO public.ports VALUES (4916, 9279, 5166);
INSERT INTO public.ports VALUES (4917, 9280, 5167);
INSERT INTO public.ports VALUES (4918, 9281, 5168);
INSERT INTO public.ports VALUES (4919, 9282, 5169);
INSERT INTO public.ports VALUES (4920, 9283, 5170);
INSERT INTO public.ports VALUES (4921, 9284, 5171);
INSERT INTO public.ports VALUES (4922, 9285, 5172);
INSERT INTO public.ports VALUES (4923, 9286, 5173);
INSERT INTO public.ports VALUES (4924, 9287, 5174);
INSERT INTO public.ports VALUES (4925, 9288, 5175);
INSERT INTO public.ports VALUES (4926, 9289, 5176);
INSERT INTO public.ports VALUES (4927, 9290, 5177);
INSERT INTO public.ports VALUES (4928, 9291, 5178);
INSERT INTO public.ports VALUES (4929, 9292, 5179);
INSERT INTO public.ports VALUES (4930, 9293, 5180);
INSERT INTO public.ports VALUES (4931, 9294, 5181);
INSERT INTO public.ports VALUES (4932, 9295, 5182);
INSERT INTO public.ports VALUES (4933, 9296, 5183);
INSERT INTO public.ports VALUES (4934, 9297, 5184);
INSERT INTO public.ports VALUES (4935, 9298, 5185);
INSERT INTO public.ports VALUES (4936, 9299, 5186);
INSERT INTO public.ports VALUES (4937, 9300, 5187);
INSERT INTO public.ports VALUES (4938, 9301, 5188);
INSERT INTO public.ports VALUES (4939, 9302, 5189);
INSERT INTO public.ports VALUES (4940, 9303, 5190);
INSERT INTO public.ports VALUES (4941, 9304, 5191);
INSERT INTO public.ports VALUES (4942, 9305, 5192);
INSERT INTO public.ports VALUES (4943, 9306, 5193);
INSERT INTO public.ports VALUES (4944, 9307, 5194);
INSERT INTO public.ports VALUES (4945, 9308, 5195);
INSERT INTO public.ports VALUES (4946, 9309, 5196);
INSERT INTO public.ports VALUES (4947, 9310, 5197);
INSERT INTO public.ports VALUES (4106, 8467, 5198);
INSERT INTO public.ports VALUES (4111, 8472, 5199);
INSERT INTO public.ports VALUES (4116, 8477, 5200);
INSERT INTO public.ports VALUES (4121, 8482, 5201);
INSERT INTO public.ports VALUES (4122, 8483, 5202);
INSERT INTO public.ports VALUES (4123, 8484, 5203);
INSERT INTO public.ports VALUES (4124, 8485, 5204);
INSERT INTO public.ports VALUES (4125, 8486, 5205);
INSERT INTO public.ports VALUES (4126, 8487, 5206);
INSERT INTO public.ports VALUES (4127, 8488, 5207);
INSERT INTO public.ports VALUES (4128, 8489, 5208);
INSERT INTO public.ports VALUES (4129, 8490, 5209);
INSERT INTO public.ports VALUES (4130, 8491, 5210);
INSERT INTO public.ports VALUES (4131, 8492, 5211);
INSERT INTO public.ports VALUES (4132, 8493, 5212);
INSERT INTO public.ports VALUES (4133, 8494, 5213);
INSERT INTO public.ports VALUES (4134, 8495, 5214);
INSERT INTO public.ports VALUES (4135, 8496, 5215);
INSERT INTO public.ports VALUES (4136, 8497, 5216);
INSERT INTO public.ports VALUES (4137, 8498, 5217);
INSERT INTO public.ports VALUES (4138, 8499, 5218);
INSERT INTO public.ports VALUES (4139, 8500, 5219);
INSERT INTO public.ports VALUES (4140, 8501, 5220);
INSERT INTO public.ports VALUES (4141, 8502, 5221);
INSERT INTO public.ports VALUES (4142, 8503, 5222);
INSERT INTO public.ports VALUES (4143, 8504, 5223);
INSERT INTO public.ports VALUES (4144, 8505, 5224);
INSERT INTO public.ports VALUES (4145, 8506, 5225);
INSERT INTO public.ports VALUES (4146, 8507, 5226);
INSERT INTO public.ports VALUES (4147, 8508, 5227);
INSERT INTO public.ports VALUES (4148, 8509, 5228);
INSERT INTO public.ports VALUES (4149, 8510, 5229);
INSERT INTO public.ports VALUES (4150, 8511, 5230);
INSERT INTO public.ports VALUES (4948, 9311, 5231);
INSERT INTO public.ports VALUES (4949, 9312, 5232);
INSERT INTO public.ports VALUES (4950, 9313, 5233);
INSERT INTO public.ports VALUES (4951, 9314, 5234);
INSERT INTO public.ports VALUES (4952, 9315, 5235);
INSERT INTO public.ports VALUES (4953, 9316, 5236);
INSERT INTO public.ports VALUES (4954, 9317, 5237);
INSERT INTO public.ports VALUES (4955, 9318, 5238);
INSERT INTO public.ports VALUES (4956, 9319, 5239);
INSERT INTO public.ports VALUES (4957, 9320, 5240);
INSERT INTO public.ports VALUES (4958, 9321, 5241);
INSERT INTO public.ports VALUES (4959, 9322, 5242);
INSERT INTO public.ports VALUES (4029, 8390, 5243);
INSERT INTO public.ports VALUES (4031, 8392, 5244);
INSERT INTO public.ports VALUES (4036, 8397, 5245);
INSERT INTO public.ports VALUES (4041, 8402, 5246);
INSERT INTO public.ports VALUES (4046, 8407, 5247);
INSERT INTO public.ports VALUES (4051, 8412, 5248);
INSERT INTO public.ports VALUES (4056, 8417, 5249);
INSERT INTO public.ports VALUES (4061, 8422, 5250);
INSERT INTO public.ports VALUES (4066, 8427, 5251);
INSERT INTO public.ports VALUES (4071, 8432, 5252);
INSERT INTO public.ports VALUES (4076, 8437, 5253);
INSERT INTO public.ports VALUES (4081, 8442, 5254);
INSERT INTO public.ports VALUES (4086, 8447, 5255);
INSERT INTO public.ports VALUES (4091, 8452, 5256);
INSERT INTO public.ports VALUES (4096, 8457, 5257);
INSERT INTO public.ports VALUES (4101, 8462, 5258);
INSERT INTO public.ports VALUES (6284, 8707, 4524);
INSERT INTO public.ports VALUES (6285, 8708, 4525);
INSERT INTO public.ports VALUES (6286, 8709, 4526);
INSERT INTO public.ports VALUES (6287, 8710, 4527);
INSERT INTO public.ports VALUES (6288, 8711, 4528);
INSERT INTO public.ports VALUES (6289, 8712, 4529);
INSERT INTO public.ports VALUES (6290, 8713, 4530);
INSERT INTO public.ports VALUES (6291, 8714, 4531);
INSERT INTO public.ports VALUES (4431, 8794, 4532);
INSERT INTO public.ports VALUES (4437, 8800, 4536);
INSERT INTO public.ports VALUES (4443, 8806, 4541);
INSERT INTO public.ports VALUES (4450, 8813, 4546);
INSERT INTO public.ports VALUES (4457, 8820, 4551);
INSERT INTO public.ports VALUES (4464, 8827, 4556);
INSERT INTO public.ports VALUES (4471, 8834, 4561);
INSERT INTO public.ports VALUES (4478, 8841, 4566);
INSERT INTO public.ports VALUES (4485, 8848, 4571);
INSERT INTO public.ports VALUES (4492, 8855, 4576);
INSERT INTO public.ports VALUES (4498, 8861, 4581);
INSERT INTO public.ports VALUES (4505, 8868, 4586);
INSERT INTO public.ports VALUES (6292, 8715, 4591);
INSERT INTO public.ports VALUES (6297, 8720, 4596);
INSERT INTO public.ports VALUES (6302, 8725, 4601);
INSERT INTO public.ports VALUES (6307, 8730, 4606);
INSERT INTO public.ports VALUES (6312, 8735, 4611);
INSERT INTO public.ports VALUES (6317, 8740, 4616);
INSERT INTO public.ports VALUES (6322, 8745, 4621);
INSERT INTO public.ports VALUES (6325, 8748, 4624);
INSERT INTO public.ports VALUES (4152, 8513, 5260);
INSERT INTO public.ports VALUES (4154, 8515, 5262);
INSERT INTO public.ports VALUES (4155, 8516, 5263);
INSERT INTO public.ports VALUES (4156, 8517, 5264);
INSERT INTO public.ports VALUES (4157, 8518, 5265);
INSERT INTO public.ports VALUES (4159, 8520, 5267);
INSERT INTO public.ports VALUES (4770, 9133, 5268);
INSERT INTO public.ports VALUES (4771, 9134, 5269);
INSERT INTO public.ports VALUES (4160, 8521, 5270);
INSERT INTO public.ports VALUES (4162, 8523, 5272);
INSERT INTO public.ports VALUES (4163, 8524, 5273);
INSERT INTO public.ports VALUES (4164, 8525, 5274);
INSERT INTO public.ports VALUES (4165, 8526, 5275);
INSERT INTO public.ports VALUES (4167, 8528, 5277);
INSERT INTO public.ports VALUES (4168, 8529, 5278);
INSERT INTO public.ports VALUES (4169, 8530, 5279);
INSERT INTO public.ports VALUES (4170, 8531, 5280);
INSERT INTO public.ports VALUES (4172, 8533, 5282);
INSERT INTO public.ports VALUES (4173, 8534, 5283);
INSERT INTO public.ports VALUES (4174, 8535, 5284);
INSERT INTO public.ports VALUES (4175, 8536, 5285);
INSERT INTO public.ports VALUES (4177, 8538, 5287);
INSERT INTO public.ports VALUES (4178, 8539, 5288);
INSERT INTO public.ports VALUES (4179, 8540, 5289);
INSERT INTO public.ports VALUES (4180, 8541, 5290);
INSERT INTO public.ports VALUES (4182, 8543, 5292);
INSERT INTO public.ports VALUES (4183, 8544, 5293);
INSERT INTO public.ports VALUES (4184, 8545, 5294);
INSERT INTO public.ports VALUES (4185, 8546, 5295);
INSERT INTO public.ports VALUES (4187, 8548, 5297);
INSERT INTO public.ports VALUES (4188, 8549, 5298);
INSERT INTO public.ports VALUES (4189, 8550, 5299);
INSERT INTO public.ports VALUES (4190, 8551, 5300);
INSERT INTO public.ports VALUES (4192, 8553, 5302);
INSERT INTO public.ports VALUES (4193, 8554, 5303);
INSERT INTO public.ports VALUES (4194, 8555, 5304);
INSERT INTO public.ports VALUES (4195, 8556, 5305);
INSERT INTO public.ports VALUES (4197, 8558, 5307);
INSERT INTO public.ports VALUES (4198, 8559, 5308);
INSERT INTO public.ports VALUES (4199, 8560, 5309);
INSERT INTO public.ports VALUES (4200, 8561, 5310);
INSERT INTO public.ports VALUES (4202, 8563, 5312);
INSERT INTO public.ports VALUES (4203, 8564, 5313);
INSERT INTO public.ports VALUES (4204, 8565, 5314);
INSERT INTO public.ports VALUES (4205, 8566, 5315);
INSERT INTO public.ports VALUES (4207, 8568, 5317);
INSERT INTO public.ports VALUES (4208, 8569, 5318);
INSERT INTO public.ports VALUES (4556, 8919, 5319);
INSERT INTO public.ports VALUES (4557, 8920, 5320);
INSERT INTO public.ports VALUES (4559, 8922, 5322);
INSERT INTO public.ports VALUES (4560, 8923, 5323);
INSERT INTO public.ports VALUES (4561, 8924, 5324);
INSERT INTO public.ports VALUES (4562, 8925, 5325);
INSERT INTO public.ports VALUES (4564, 8927, 5327);
INSERT INTO public.ports VALUES (4565, 8928, 5328);
INSERT INTO public.ports VALUES (4566, 8929, 5329);
INSERT INTO public.ports VALUES (4567, 8930, 5330);
INSERT INTO public.ports VALUES (4569, 8932, 5332);
INSERT INTO public.ports VALUES (4570, 8933, 5333);
INSERT INTO public.ports VALUES (4571, 8934, 5334);
INSERT INTO public.ports VALUES (4572, 8935, 5335);
INSERT INTO public.ports VALUES (4574, 8937, 5337);
INSERT INTO public.ports VALUES (4575, 8938, 5338);
INSERT INTO public.ports VALUES (4576, 8939, 5339);
INSERT INTO public.ports VALUES (4577, 8940, 5340);
INSERT INTO public.ports VALUES (4806, 9169, 5342);
INSERT INTO public.ports VALUES (4807, 9170, 5343);
INSERT INTO public.ports VALUES (4808, 9171, 5344);
INSERT INTO public.ports VALUES (4809, 9172, 5345);
INSERT INTO public.ports VALUES (4811, 9174, 5347);
INSERT INTO public.ports VALUES (4812, 9175, 5348);
INSERT INTO public.ports VALUES (4813, 9176, 5349);
INSERT INTO public.ports VALUES (4814, 9177, 5350);
INSERT INTO public.ports VALUES (4816, 9179, 5352);
INSERT INTO public.ports VALUES (4817, 9180, 5353);
INSERT INTO public.ports VALUES (4818, 9181, 5354);
INSERT INTO public.ports VALUES (4820, 9183, 5356);
INSERT INTO public.ports VALUES (4821, 9184, 5357);
INSERT INTO public.ports VALUES (4822, 9185, 5358);
INSERT INTO public.ports VALUES (6326, 8749, 4625);
INSERT INTO public.ports VALUES (6327, 8750, 4626);
INSERT INTO public.ports VALUES (6328, 8751, 4627);
INSERT INTO public.ports VALUES (6329, 8752, 4628);
INSERT INTO public.ports VALUES (6330, 8753, 4629);
INSERT INTO public.ports VALUES (6331, 8754, 4630);
INSERT INTO public.ports VALUES (6332, 8755, 4631);
INSERT INTO public.ports VALUES (6333, 8756, 4632);
INSERT INTO public.ports VALUES (6334, 8757, 4633);
INSERT INTO public.ports VALUES (6335, 8758, 4634);
INSERT INTO public.ports VALUES (6336, 8759, 4635);
INSERT INTO public.ports VALUES (6337, 8760, 4636);
INSERT INTO public.ports VALUES (6338, 8761, 4637);
INSERT INTO public.ports VALUES (6339, 8762, 4638);
INSERT INTO public.ports VALUES (6340, 8763, 4639);
INSERT INTO public.ports VALUES (6341, 8764, 4640);
INSERT INTO public.ports VALUES (6342, 8765, 4641);
INSERT INTO public.ports VALUES (6343, 8766, 4642);
INSERT INTO public.ports VALUES (6344, 8767, 4643);
INSERT INTO public.ports VALUES (6345, 8768, 4644);
INSERT INTO public.ports VALUES (6346, 8769, 4645);
INSERT INTO public.ports VALUES (6347, 8770, 4646);
INSERT INTO public.ports VALUES (6348, 8771, 4647);
INSERT INTO public.ports VALUES (6349, 8772, 4648);
INSERT INTO public.ports VALUES (6350, 8773, 4649);
INSERT INTO public.ports VALUES (6351, 8774, 4650);
INSERT INTO public.ports VALUES (6352, 8775, 4651);
INSERT INTO public.ports VALUES (6353, 8776, 4652);
INSERT INTO public.ports VALUES (6354, 8777, 4653);
INSERT INTO public.ports VALUES (6355, 8778, 4654);
INSERT INTO public.ports VALUES (6356, 8779, 4655);
INSERT INTO public.ports VALUES (6357, 8780, 4656);
INSERT INTO public.ports VALUES (6358, 8781, 4657);
INSERT INTO public.ports VALUES (6359, 8782, 4658);
INSERT INTO public.ports VALUES (6360, 8783, 4659);
INSERT INTO public.ports VALUES (6361, 8784, 4660);
INSERT INTO public.ports VALUES (6362, 8785, 4661);
INSERT INTO public.ports VALUES (6363, 8786, 4662);
INSERT INTO public.ports VALUES (6364, 8787, 4663);
INSERT INTO public.ports VALUES (6365, 8788, 4664);
INSERT INTO public.ports VALUES (6366, 8789, 4665);
INSERT INTO public.ports VALUES (6367, 8790, 4666);
INSERT INTO public.ports VALUES (6368, 8791, 4667);
INSERT INTO public.ports VALUES (6369, 8792, 4668);
INSERT INTO public.ports VALUES (6370, 8793, 4669);
INSERT INTO public.ports VALUES (6371, 9135, 4670);
INSERT INTO public.ports VALUES (6372, 9136, 4671);
INSERT INTO public.ports VALUES (6373, 9137, 4672);
INSERT INTO public.ports VALUES (6374, 9138, 4673);
INSERT INTO public.ports VALUES (6375, 9139, 4674);
INSERT INTO public.ports VALUES (6376, 9140, 4675);
INSERT INTO public.ports VALUES (6377, 9141, 4676);
INSERT INTO public.ports VALUES (6378, 9142, 4677);
INSERT INTO public.ports VALUES (6379, 9143, 4678);
INSERT INTO public.ports VALUES (6380, 9144, 4679);
INSERT INTO public.ports VALUES (6381, 9145, 4680);
INSERT INTO public.ports VALUES (6382, 9146, 4681);
INSERT INTO public.ports VALUES (6383, 9147, 4682);
INSERT INTO public.ports VALUES (6384, 9148, 4683);
INSERT INTO public.ports VALUES (6385, 9149, 4684);
INSERT INTO public.ports VALUES (6386, 9150, 4685);
INSERT INTO public.ports VALUES (6387, 9151, 4686);
INSERT INTO public.ports VALUES (6388, 9152, 4687);
INSERT INTO public.ports VALUES (6389, 9153, 4688);
INSERT INTO public.ports VALUES (6390, 9154, 4689);
INSERT INTO public.ports VALUES (6391, 9155, 4690);
INSERT INTO public.ports VALUES (6392, 9156, 4691);
INSERT INTO public.ports VALUES (6393, 9157, 4692);
INSERT INTO public.ports VALUES (6394, 9158, 4693);
INSERT INTO public.ports VALUES (6395, 9159, 4694);
INSERT INTO public.ports VALUES (6396, 9160, 4695);
INSERT INTO public.ports VALUES (6397, 9161, 4696);
INSERT INTO public.ports VALUES (6398, 9162, 4697);
INSERT INTO public.ports VALUES (6399, 9163, 4698);
INSERT INTO public.ports VALUES (6400, 9164, 4699);
INSERT INTO public.ports VALUES (6401, 9165, 4700);
INSERT INTO public.ports VALUES (6402, 9166, 4701);
INSERT INTO public.ports VALUES (6403, 9167, 4702);
INSERT INTO public.ports VALUES (6404, 9168, 4703);
INSERT INTO public.ports VALUES (6405, 9904, 4704);
INSERT INTO public.ports VALUES (6406, 9905, 4705);
INSERT INTO public.ports VALUES (6407, 9906, 4706);
INSERT INTO public.ports VALUES (6408, 9907, 4707);
INSERT INTO public.ports VALUES (6409, 9908, 4708);
INSERT INTO public.ports VALUES (6410, 9909, 4709);
INSERT INTO public.ports VALUES (6411, 9910, 4710);
INSERT INTO public.ports VALUES (6412, 9911, 4711);
INSERT INTO public.ports VALUES (6413, 9912, 4712);
INSERT INTO public.ports VALUES (6414, 9913, 4713);
INSERT INTO public.ports VALUES (6415, 9914, 4714);
INSERT INTO public.ports VALUES (5037, 9400, 5444);
INSERT INTO public.ports VALUES (5044, 9407, 5446);
INSERT INTO public.ports VALUES (5048, 9411, 5447);
INSERT INTO public.ports VALUES (5050, 9413, 5448);
INSERT INTO public.ports VALUES (5051, 9414, 5449);
INSERT INTO public.ports VALUES (5053, 9416, 5451);
INSERT INTO public.ports VALUES (5054, 9417, 5452);
INSERT INTO public.ports VALUES (5055, 9418, 5453);
INSERT INTO public.ports VALUES (5056, 9419, 5454);
INSERT INTO public.ports VALUES (5058, 9421, 5456);
INSERT INTO public.ports VALUES (5059, 9422, 5457);
INSERT INTO public.ports VALUES (5060, 9423, 5458);
INSERT INTO public.ports VALUES (5061, 9424, 5459);
INSERT INTO public.ports VALUES (5063, 9426, 5461);
INSERT INTO public.ports VALUES (5064, 9427, 5462);
INSERT INTO public.ports VALUES (5065, 9428, 5463);
INSERT INTO public.ports VALUES (5066, 9429, 5464);
INSERT INTO public.ports VALUES (5068, 9431, 5466);
INSERT INTO public.ports VALUES (5069, 9432, 5467);
INSERT INTO public.ports VALUES (5070, 9433, 5468);
INSERT INTO public.ports VALUES (5071, 9434, 5469);
INSERT INTO public.ports VALUES (5073, 9436, 5471);
INSERT INTO public.ports VALUES (5074, 9437, 5472);
INSERT INTO public.ports VALUES (5075, 9438, 5473);
INSERT INTO public.ports VALUES (5076, 9439, 5474);
INSERT INTO public.ports VALUES (5078, 9441, 5476);
INSERT INTO public.ports VALUES (5079, 9442, 5477);
INSERT INTO public.ports VALUES (5080, 9443, 5478);
INSERT INTO public.ports VALUES (5081, 9444, 5479);
INSERT INTO public.ports VALUES (5083, 9446, 5481);
INSERT INTO public.ports VALUES (5084, 9447, 5482);
INSERT INTO public.ports VALUES (5085, 9448, 5483);
INSERT INTO public.ports VALUES (5086, 9449, 5484);
INSERT INTO public.ports VALUES (5088, 9451, 5486);
INSERT INTO public.ports VALUES (5089, 9452, 5487);
INSERT INTO public.ports VALUES (5090, 9453, 5488);
INSERT INTO public.ports VALUES (5091, 9454, 5489);
INSERT INTO public.ports VALUES (5093, 9456, 5491);
INSERT INTO public.ports VALUES (5094, 9457, 5492);
INSERT INTO public.ports VALUES (5095, 9458, 5493);
INSERT INTO public.ports VALUES (5096, 9459, 5494);
INSERT INTO public.ports VALUES (5098, 9461, 5496);
INSERT INTO public.ports VALUES (5099, 9462, 5497);
INSERT INTO public.ports VALUES (5100, 9463, 5498);
INSERT INTO public.ports VALUES (5101, 9464, 5499);
INSERT INTO public.ports VALUES (5103, 9466, 5501);
INSERT INTO public.ports VALUES (5104, 9467, 5502);
INSERT INTO public.ports VALUES (5105, 9468, 5503);
INSERT INTO public.ports VALUES (5106, 9469, 5504);
INSERT INTO public.ports VALUES (5108, 9471, 5506);
INSERT INTO public.ports VALUES (5109, 9472, 5507);
INSERT INTO public.ports VALUES (5110, 9473, 5508);
INSERT INTO public.ports VALUES (5111, 9474, 5509);
INSERT INTO public.ports VALUES (5113, 9476, 5511);
INSERT INTO public.ports VALUES (5114, 9477, 5512);
INSERT INTO public.ports VALUES (5115, 9478, 5513);
INSERT INTO public.ports VALUES (5116, 9479, 5514);
INSERT INTO public.ports VALUES (5118, 9481, 5516);
INSERT INTO public.ports VALUES (5119, 9482, 5517);
INSERT INTO public.ports VALUES (5120, 9483, 5518);
INSERT INTO public.ports VALUES (5121, 9484, 5519);
INSERT INTO public.ports VALUES (5123, 9486, 5521);
INSERT INTO public.ports VALUES (5124, 9487, 5522);
INSERT INTO public.ports VALUES (4826, 9189, 5523);
INSERT INTO public.ports VALUES (4827, 9190, 5524);
INSERT INTO public.ports VALUES (4829, 9192, 5526);
INSERT INTO public.ports VALUES (4830, 9193, 5527);
INSERT INTO public.ports VALUES (4831, 9194, 5528);
INSERT INTO public.ports VALUES (4832, 9195, 5529);
INSERT INTO public.ports VALUES (4834, 9197, 5531);
INSERT INTO public.ports VALUES (4835, 9198, 5532);
INSERT INTO public.ports VALUES (4836, 9199, 5533);
INSERT INTO public.ports VALUES (4837, 9200, 5534);
INSERT INTO public.ports VALUES (4839, 9202, 5536);
INSERT INTO public.ports VALUES (4840, 9203, 5537);
INSERT INTO public.ports VALUES (4841, 9204, 5538);
INSERT INTO public.ports VALUES (4842, 9205, 5539);
INSERT INTO public.ports VALUES (4844, 9207, 5541);
INSERT INTO public.ports VALUES (4845, 9208, 5542);
INSERT INTO public.ports VALUES (4846, 9209, 5543);
INSERT INTO public.ports VALUES (4847, 9210, 5544);
INSERT INTO public.ports VALUES (4849, 9212, 5546);
INSERT INTO public.ports VALUES (4850, 9213, 5547);
INSERT INTO public.ports VALUES (4851, 9214, NULL);
INSERT INTO public.ports VALUES (6416, 9915, 4715);
INSERT INTO public.ports VALUES (6417, 9916, 4716);
INSERT INTO public.ports VALUES (6418, 9917, 4717);
INSERT INTO public.ports VALUES (6419, 9918, 4718);
INSERT INTO public.ports VALUES (6420, 9919, 4719);
INSERT INTO public.ports VALUES (6421, 9920, 4720);
INSERT INTO public.ports VALUES (6422, 9921, 4721);
INSERT INTO public.ports VALUES (6423, 9922, 4722);
INSERT INTO public.ports VALUES (6424, 9923, 4723);
INSERT INTO public.ports VALUES (6425, 9924, 4724);
INSERT INTO public.ports VALUES (6426, 9925, 4725);
INSERT INTO public.ports VALUES (6427, 9926, 4726);
INSERT INTO public.ports VALUES (6428, 9927, 4727);
INSERT INTO public.ports VALUES (6429, 9928, 4728);
INSERT INTO public.ports VALUES (6430, 9929, 4729);
INSERT INTO public.ports VALUES (6431, 9930, 4730);
INSERT INTO public.ports VALUES (6432, 9931, 4731);
INSERT INTO public.ports VALUES (6433, 9932, 4732);
INSERT INTO public.ports VALUES (6434, 9933, 4733);
INSERT INTO public.ports VALUES (6435, 9934, 4734);
INSERT INTO public.ports VALUES (6436, 9935, 4735);
INSERT INTO public.ports VALUES (6437, 9936, 4736);
INSERT INTO public.ports VALUES (6438, 9937, 4737);
INSERT INTO public.ports VALUES (6439, 9938, 4738);
INSERT INTO public.ports VALUES (6440, 9939, 4739);
INSERT INTO public.ports VALUES (6441, 9940, 4740);
INSERT INTO public.ports VALUES (6442, 9941, 4741);
INSERT INTO public.ports VALUES (6443, 9942, 4742);
INSERT INTO public.ports VALUES (6444, 9943, 4743);
INSERT INTO public.ports VALUES (6445, 9944, 4744);
INSERT INTO public.ports VALUES (6446, 9945, 4745);
INSERT INTO public.ports VALUES (6447, 9946, 4746);
INSERT INTO public.ports VALUES (6448, 9947, 4747);
INSERT INTO public.ports VALUES (6449, 9948, 4748);
INSERT INTO public.ports VALUES (6450, 9949, 4749);
INSERT INTO public.ports VALUES (5125, 9488, NULL);
INSERT INTO public.ports VALUES (5126, 9489, NULL);
INSERT INTO public.ports VALUES (5127, 9490, NULL);
INSERT INTO public.ports VALUES (5128, 9491, NULL);
INSERT INTO public.ports VALUES (5129, 9492, NULL);
INSERT INTO public.ports VALUES (5130, 9493, NULL);
INSERT INTO public.ports VALUES (5131, 9494, NULL);
INSERT INTO public.ports VALUES (5132, 9495, NULL);
INSERT INTO public.ports VALUES (5133, 9496, NULL);
INSERT INTO public.ports VALUES (5134, 9497, NULL);
INSERT INTO public.ports VALUES (5135, 9498, NULL);
INSERT INTO public.ports VALUES (5136, 9499, NULL);
INSERT INTO public.ports VALUES (5137, 9500, NULL);
INSERT INTO public.ports VALUES (5138, 9501, NULL);
INSERT INTO public.ports VALUES (5139, 9502, NULL);
INSERT INTO public.ports VALUES (5140, 9503, NULL);
INSERT INTO public.ports VALUES (5141, 9504, NULL);
INSERT INTO public.ports VALUES (5142, 9505, NULL);
INSERT INTO public.ports VALUES (5143, 9506, NULL);
INSERT INTO public.ports VALUES (5144, 9507, NULL);
INSERT INTO public.ports VALUES (5145, 9508, NULL);
INSERT INTO public.ports VALUES (5146, 9509, NULL);
INSERT INTO public.ports VALUES (5147, 9510, NULL);
INSERT INTO public.ports VALUES (5148, 9511, NULL);
INSERT INTO public.ports VALUES (5149, 9512, NULL);
INSERT INTO public.ports VALUES (5150, 9513, NULL);
INSERT INTO public.ports VALUES (5151, 9514, NULL);
INSERT INTO public.ports VALUES (5152, 9515, NULL);
INSERT INTO public.ports VALUES (5153, 9516, NULL);
INSERT INTO public.ports VALUES (5154, 9517, NULL);
INSERT INTO public.ports VALUES (5155, 9518, NULL);
INSERT INTO public.ports VALUES (5156, 9519, NULL);
INSERT INTO public.ports VALUES (5157, 9520, NULL);
INSERT INTO public.ports VALUES (5158, 9521, NULL);
INSERT INTO public.ports VALUES (5159, 9522, NULL);
INSERT INTO public.ports VALUES (5160, 9523, NULL);
INSERT INTO public.ports VALUES (5161, 9524, NULL);
INSERT INTO public.ports VALUES (5162, 9525, NULL);
INSERT INTO public.ports VALUES (5163, 9526, NULL);
INSERT INTO public.ports VALUES (5164, 9527, NULL);
INSERT INTO public.ports VALUES (5165, 9528, NULL);
INSERT INTO public.ports VALUES (5166, 9529, NULL);
INSERT INTO public.ports VALUES (5167, 9530, NULL);
INSERT INTO public.ports VALUES (5168, 9531, NULL);
INSERT INTO public.ports VALUES (5169, 9532, NULL);
INSERT INTO public.ports VALUES (5170, 9533, NULL);
INSERT INTO public.ports VALUES (5171, 9534, NULL);
INSERT INTO public.ports VALUES (5172, 9535, NULL);
INSERT INTO public.ports VALUES (5173, 9536, NULL);
INSERT INTO public.ports VALUES (5174, 9537, NULL);
INSERT INTO public.ports VALUES (5175, 9538, NULL);
INSERT INTO public.ports VALUES (5176, 9539, NULL);
INSERT INTO public.ports VALUES (5177, 9540, NULL);
INSERT INTO public.ports VALUES (5178, 9541, NULL);
INSERT INTO public.ports VALUES (5179, 9542, NULL);
INSERT INTO public.ports VALUES (5180, 9543, NULL);
INSERT INTO public.ports VALUES (5181, 9544, NULL);
INSERT INTO public.ports VALUES (5182, 9545, NULL);
INSERT INTO public.ports VALUES (5183, 9546, NULL);
INSERT INTO public.ports VALUES (5184, 9547, NULL);
INSERT INTO public.ports VALUES (5185, 9548, NULL);
INSERT INTO public.ports VALUES (5186, 9549, NULL);
INSERT INTO public.ports VALUES (5189, 9552, NULL);
INSERT INTO public.ports VALUES (5193, 9556, NULL);
INSERT INTO public.ports VALUES (5197, 9560, NULL);
INSERT INTO public.ports VALUES (5200, 9563, NULL);
INSERT INTO public.ports VALUES (5204, 9567, NULL);
INSERT INTO public.ports VALUES (5208, 9571, NULL);
INSERT INTO public.ports VALUES (5211, 9574, NULL);
INSERT INTO public.ports VALUES (5215, 9578, NULL);
INSERT INTO public.ports VALUES (5219, 9582, NULL);
INSERT INTO public.ports VALUES (5222, 9585, NULL);
INSERT INTO public.ports VALUES (5226, 9589, NULL);
INSERT INTO public.ports VALUES (5230, 9593, NULL);
INSERT INTO public.ports VALUES (5233, 9596, NULL);
INSERT INTO public.ports VALUES (5237, 9600, NULL);
INSERT INTO public.ports VALUES (5241, 9604, NULL);
INSERT INTO public.ports VALUES (5244, 9607, NULL);
INSERT INTO public.ports VALUES (5248, 9611, NULL);
INSERT INTO public.ports VALUES (5252, 9615, NULL);
INSERT INTO public.ports VALUES (5255, 9618, NULL);
INSERT INTO public.ports VALUES (5259, 9622, NULL);
INSERT INTO public.ports VALUES (5263, 9626, NULL);
INSERT INTO public.ports VALUES (5266, 9629, NULL);
INSERT INTO public.ports VALUES (5270, 9633, NULL);
INSERT INTO public.ports VALUES (5274, 9637, NULL);
INSERT INTO public.ports VALUES (5276, 9639, NULL);
INSERT INTO public.ports VALUES (5277, 9640, NULL);
INSERT INTO public.ports VALUES (5278, 9641, NULL);
INSERT INTO public.ports VALUES (5279, 9642, NULL);
INSERT INTO public.ports VALUES (5280, 9643, NULL);
INSERT INTO public.ports VALUES (5281, 9644, NULL);
INSERT INTO public.ports VALUES (5282, 9645, NULL);
INSERT INTO public.ports VALUES (5283, 9646, NULL);
INSERT INTO public.ports VALUES (5284, 9647, NULL);


--
-- Data for Name: session_oses; Type: TABLE DATA; Schema: public; Owner: symfony
--

INSERT INTO public.session_oses VALUES (1, 1, 2);
INSERT INTO public.session_oses VALUES (2, 1, 3);
INSERT INTO public.session_oses VALUES (3, 2, 2);
INSERT INTO public.session_oses VALUES (4, 3, 2);


--
-- Data for Name: session_statuses; Type: TABLE DATA; Schema: public; Owner: symfony
--

INSERT INTO public.session_statuses VALUES (1, 'New');
INSERT INTO public.session_statuses VALUES (2, 'Ready');
INSERT INTO public.session_statuses VALUES (3, 'Allocated');
INSERT INTO public.session_statuses VALUES (4, 'Started');
INSERT INTO public.session_statuses VALUES (5, 'Finished');
INSERT INTO public.session_statuses VALUES (6, 'TimedOut');


--
-- Data for Name: session_techs; Type: TABLE DATA; Schema: public; Owner: symfony
--

INSERT INTO public.session_techs VALUES (1, 1, 16);
INSERT INTO public.session_techs VALUES (2, 1, 13);
INSERT INTO public.session_techs VALUES (3, 1, 19);
INSERT INTO public.session_techs VALUES (4, 1, 9);
INSERT INTO public.session_techs VALUES (5, 1, 14);
INSERT INTO public.session_techs VALUES (6, 2, 14);
INSERT INTO public.session_techs VALUES (7, 3, 14);
INSERT INTO public.session_techs VALUES (8, 3, 1);


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: symfony
--

INSERT INTO public.sessions VALUES (1, '2022-07-01 00:00:00', NULL, 'b026324c', 1, 4, NULL);
INSERT INTO public.sessions VALUES (2, '2022-10-10 12:00:00', NULL, '7d9bf2e9', 2, 2, NULL);
INSERT INTO public.sessions VALUES (3, '2022-10-10 00:00:00', NULL, '48b3e45e', 3, 5, NULL);


--
-- Data for Name: task_instance_types; Type: TABLE DATA; Schema: public; Owner: symfony
--

INSERT INTO public.task_instance_types VALUES (5, 1, 110);
INSERT INTO public.task_instance_types VALUES (6, 1, 111);
INSERT INTO public.task_instance_types VALUES (7, 1, 112);
INSERT INTO public.task_instance_types VALUES (8, 1, 113);
INSERT INTO public.task_instance_types VALUES (9, 2, 110);
INSERT INTO public.task_instance_types VALUES (10, 2, 111);
INSERT INTO public.task_instance_types VALUES (11, 2, 112);
INSERT INTO public.task_instance_types VALUES (12, 2, 113);
INSERT INTO public.task_instance_types VALUES (13, 7, 110);
INSERT INTO public.task_instance_types VALUES (14, 7, 111);
INSERT INTO public.task_instance_types VALUES (15, 7, 112);
INSERT INTO public.task_instance_types VALUES (16, 7, 113);
INSERT INTO public.task_instance_types VALUES (21, 5, 110);
INSERT INTO public.task_instance_types VALUES (22, 5, 111);
INSERT INTO public.task_instance_types VALUES (23, 5, 112);
INSERT INTO public.task_instance_types VALUES (24, 5, 113);
INSERT INTO public.task_instance_types VALUES (25, 6, 110);
INSERT INTO public.task_instance_types VALUES (26, 6, 111);
INSERT INTO public.task_instance_types VALUES (27, 6, 112);
INSERT INTO public.task_instance_types VALUES (28, 6, 113);
INSERT INTO public.task_instance_types VALUES (29, 8, 110);
INSERT INTO public.task_instance_types VALUES (30, 8, 111);
INSERT INTO public.task_instance_types VALUES (31, 8, 112);
INSERT INTO public.task_instance_types VALUES (32, 8, 113);
INSERT INTO public.task_instance_types VALUES (33, 9, 110);
INSERT INTO public.task_instance_types VALUES (34, 9, 111);
INSERT INTO public.task_instance_types VALUES (35, 9, 112);
INSERT INTO public.task_instance_types VALUES (36, 9, 113);
INSERT INTO public.task_instance_types VALUES (37, 10, 110);
INSERT INTO public.task_instance_types VALUES (38, 10, 111);
INSERT INTO public.task_instance_types VALUES (39, 10, 112);
INSERT INTO public.task_instance_types VALUES (40, 10, 113);
INSERT INTO public.task_instance_types VALUES (41, 11, 110);
INSERT INTO public.task_instance_types VALUES (42, 11, 111);
INSERT INTO public.task_instance_types VALUES (43, 11, 112);
INSERT INTO public.task_instance_types VALUES (44, 11, 113);
INSERT INTO public.task_instance_types VALUES (45, 12, 110);
INSERT INTO public.task_instance_types VALUES (46, 12, 111);
INSERT INTO public.task_instance_types VALUES (47, 12, 112);
INSERT INTO public.task_instance_types VALUES (48, 12, 113);
INSERT INTO public.task_instance_types VALUES (49, 13, 110);
INSERT INTO public.task_instance_types VALUES (50, 13, 111);
INSERT INTO public.task_instance_types VALUES (51, 13, 112);
INSERT INTO public.task_instance_types VALUES (52, 13, 113);
INSERT INTO public.task_instance_types VALUES (53, 14, 110);
INSERT INTO public.task_instance_types VALUES (54, 14, 111);
INSERT INTO public.task_instance_types VALUES (55, 14, 112);
INSERT INTO public.task_instance_types VALUES (56, 14, 113);
INSERT INTO public.task_instance_types VALUES (57, 16, 110);
INSERT INTO public.task_instance_types VALUES (58, 16, 111);
INSERT INTO public.task_instance_types VALUES (59, 16, 112);
INSERT INTO public.task_instance_types VALUES (60, 16, 113);
INSERT INTO public.task_instance_types VALUES (61, 15, 110);
INSERT INTO public.task_instance_types VALUES (62, 15, 111);
INSERT INTO public.task_instance_types VALUES (63, 15, 112);
INSERT INTO public.task_instance_types VALUES (64, 15, 113);
INSERT INTO public.task_instance_types VALUES (65, 17, 110);
INSERT INTO public.task_instance_types VALUES (66, 17, 111);
INSERT INTO public.task_instance_types VALUES (67, 17, 112);
INSERT INTO public.task_instance_types VALUES (68, 17, 113);


--
-- Data for Name: task_oses; Type: TABLE DATA; Schema: public; Owner: symfony
--

INSERT INTO public.task_oses VALUES (17, 1, 2);
INSERT INTO public.task_oses VALUES (18, 1, 7);
INSERT INTO public.task_oses VALUES (19, 2, 2);
INSERT INTO public.task_oses VALUES (20, 2, 7);
INSERT INTO public.task_oses VALUES (21, 7, 2);
INSERT INTO public.task_oses VALUES (22, 7, 7);
INSERT INTO public.task_oses VALUES (25, 5, 2);
INSERT INTO public.task_oses VALUES (26, 5, 7);
INSERT INTO public.task_oses VALUES (27, 6, 2);
INSERT INTO public.task_oses VALUES (28, 6, 7);
INSERT INTO public.task_oses VALUES (29, 8, 2);
INSERT INTO public.task_oses VALUES (30, 8, 7);
INSERT INTO public.task_oses VALUES (31, 9, 2);
INSERT INTO public.task_oses VALUES (32, 9, 7);
INSERT INTO public.task_oses VALUES (33, 10, 2);
INSERT INTO public.task_oses VALUES (34, 10, 7);
INSERT INTO public.task_oses VALUES (35, 11, 2);
INSERT INTO public.task_oses VALUES (36, 11, 7);
INSERT INTO public.task_oses VALUES (37, 12, 2);
INSERT INTO public.task_oses VALUES (38, 12, 7);
INSERT INTO public.task_oses VALUES (39, 13, 2);
INSERT INTO public.task_oses VALUES (40, 13, 7);
INSERT INTO public.task_oses VALUES (41, 14, 2);
INSERT INTO public.task_oses VALUES (42, 14, 7);
INSERT INTO public.task_oses VALUES (43, 16, 2);
INSERT INTO public.task_oses VALUES (44, 16, 7);
INSERT INTO public.task_oses VALUES (45, 15, 2);
INSERT INTO public.task_oses VALUES (46, 15, 7);
INSERT INTO public.task_oses VALUES (47, 17, 2);
INSERT INTO public.task_oses VALUES (48, 17, 7);


--
-- Data for Name: task_techs; Type: TABLE DATA; Schema: public; Owner: symfony
--

INSERT INTO public.task_techs VALUES (1, 1, 14);
INSERT INTO public.task_techs VALUES (2, 2, 14);
INSERT INTO public.task_techs VALUES (5, 5, 6);
INSERT INTO public.task_techs VALUES (6, 6, 22);
INSERT INTO public.task_techs VALUES (7, 5, 5);
INSERT INTO public.task_techs VALUES (8, 7, 23);
INSERT INTO public.task_techs VALUES (9, 8, 22);
INSERT INTO public.task_techs VALUES (10, 9, 14);
INSERT INTO public.task_techs VALUES (11, 10, 14);
INSERT INTO public.task_techs VALUES (12, 11, 14);
INSERT INTO public.task_techs VALUES (13, 12, 14);
INSERT INTO public.task_techs VALUES (14, 13, 24);
INSERT INTO public.task_techs VALUES (15, 14, 25);
INSERT INTO public.task_techs VALUES (16, 15, 14);
INSERT INTO public.task_techs VALUES (17, 15, 6);
INSERT INTO public.task_techs VALUES (18, 16, 14);
INSERT INTO public.task_techs VALUES (19, 17, 14);
INSERT INTO public.task_techs VALUES (20, 16, 6);
INSERT INTO public.task_techs VALUES (21, 17, 6);


--
-- Data for Name: tasks; Type: TABLE DATA; Schema: public; Owner: symfony
--

INSERT INTO public.tasks VALUES (13, 'Get default gateway', 'Get system default gateway and store the value into the file: /var/tmp/default_gateway', 'default_gateway', 72, 74, 73, 75);
INSERT INTO public.tasks VALUES (14, 'Extract TGZ archive', 'Extract tar.1.gz file from archive /var/tmp/archive.tgz and put it into /var/tmp', 'extract_tgz_file', 76, 78, 77, 79);
INSERT INTO public.tasks VALUES (15, 'Create a file and make it readable by user only', 'Create a file ''test_file'' in /var/tmp and remove read permission for group and others', 'unset_file_read_perm', 80, 82, 81, 83);
INSERT INTO public.tasks VALUES (17, 'Create a file and make it writable', 'Create a file ''test_file'' in /var/tmp and set write permission for owner, group and others', 'set_file_write_perm', 88, 90, 89, 91);
INSERT INTO public.tasks VALUES (16, 'Create a file and make it executable', 'Create a file ''test_file'' in /var/tmp and set execute permission', 'set_file_execute_perm', 84, 86, 85, 87);
INSERT INTO public.tasks VALUES (8, 'Install deb package', 'Install ''nano'' package into the OS', 'deb_install', 40, 42, 41, 43);
INSERT INTO public.tasks VALUES (6, 'Uninstall deb package', 'uninstall ''nano'' package from the OS', 'deb_uninstall', 36, 38, 37, 39);
INSERT INTO public.tasks VALUES (1, 'Create a directory', 'Create a directory: /var/tmp/test_dir', 'create_directory', 48, 50, 49, 51);
INSERT INTO public.tasks VALUES (2, 'Create a file', 'Create a file: /var/tmp/test_file', 'create_file', 44, 46, 45, 47);
INSERT INTO public.tasks VALUES (7, 'Get system UUID', 'Fetch system universally unique identifier and store it in the file: /var/tmp/uuid.txt', 'system_uuid', 28, 30, 29, 31);
INSERT INTO public.tasks VALUES (5, 'Set immune flag', 'Set immune flag to a file in the following location /var/tmp/test_file', 'set_immune', 32, 34, 33, 35);
INSERT INTO public.tasks VALUES (11, 'Create a hard link', 'Create a file: /var/tmp/test_file and a hard link to it /var/tmp/hard_link Then store their inode number in the file', 'create_hardlink', 64, 66, 65, 67);
INSERT INTO public.tasks VALUES (10, 'Create a named pipe', 'Create a named pipe in /var/tmp called test_fifo', 'create_fifo', 60, 62, 61, 63);
INSERT INTO public.tasks VALUES (9, 'Create a symbolic link', 'Create a symbolic link to /etc/hosts in /var/tmp', 'create_symlink', 56, 58, 57, 59);
INSERT INTO public.tasks VALUES (12, 'Create parent directories', 'Please create a following directory structure: /var/tmp/3/1/4/1/5/9/2/6/5/3/5/8/9/7/9/3/2/3/8/4/6/2/6/4/3/3/8/3/2/7/9/5/0/2/8/8/4/1/9/7/1/6/9/3/9/9/3/7/5/1/0/5/8/2/0/9/7/4/9/4/4/5/9/2', 'create_parent_directory', 68, 70, 69, 71);


--
-- Data for Name: technologies; Type: TABLE DATA; Schema: public; Owner: symfony
--

INSERT INTO public.technologies VALUES (1, 0, 'LVM', 'Logical Volume Manager configuration, managing and troubleshooting.');
INSERT INTO public.technologies VALUES (0, 0, 'Ceph', 'Configuring and troubleshooting Ceph storage.');
INSERT INTO public.technologies VALUES (2, 2, 'Netplan', 'Configuring the network with netplan.');
INSERT INTO public.technologies VALUES (3, 2, 'NetworkManager', 'Configuring the network with NetworkManager.');
INSERT INTO public.technologies VALUES (4, 2, 'Bonding', 'Configure interface bonding.');
INSERT INTO public.technologies VALUES (5, 1, 'ACL', 'Setting and removing file ACLs');
INSERT INTO public.technologies VALUES (6, 1, 'Permissions', 'Changing file permissions on filesystems.');
INSERT INTO public.technologies VALUES (7, 5, 'Ansible', 'Configuration management.');
INSERT INTO public.technologies VALUES (8, 6, 'Tuned', 'Special profiles for system parameters');
INSERT INTO public.technologies VALUES (9, 0, 'Filesystems', 'creating, resizing, repairing');
INSERT INTO public.technologies VALUES (10, 7, 'RPM', NULL);
INSERT INTO public.technologies VALUES (11, 7, 'DNF', NULL);
INSERT INTO public.technologies VALUES (12, 10, 'docker', NULL);
INSERT INTO public.technologies VALUES (13, 7, 'APT', NULL);
INSERT INTO public.technologies VALUES (14, 9, 'Files and directories', NULL);
INSERT INTO public.technologies VALUES (15, 0, 'NFS', NULL);
INSERT INTO public.technologies VALUES (16, 9, 'Systemd', NULL);
INSERT INTO public.technologies VALUES (17, 2, 'Firewall', NULL);
INSERT INTO public.technologies VALUES (18, 9, 'Remote access', 'Access via SSH');
INSERT INTO public.technologies VALUES (20, 11, 'Network traffic', 'capture, analyze network traffic.');
INSERT INTO public.technologies VALUES (19, 11, 'View processes', 'top, nmon, etc.');
INSERT INTO public.technologies VALUES (21, 11, 'I/O activity', 'view I/O activity, iostat');
INSERT INTO public.technologies VALUES (22, 7, 'Dpkg', 'use debian package manager');
INSERT INTO public.technologies VALUES (23, 8, 'dmidecode', 'dmidecode  is a tool for dumping a computer''s DMI (some say SMBIOS) table contents in a human-readable format.');
INSERT INTO public.technologies VALUES (24, 2, 'Network Settings', 'Network subsystem settings');
INSERT INTO public.technologies VALUES (25, 9, 'Archiving', 'ZIP, GZip, TAR, etc.');
INSERT INTO public.technologies VALUES (26, 9, 'Users and groups', 'Adding, removing, managing users and groups');


--
-- Data for Name: testees; Type: TABLE DATA; Schema: public; Owner: symfony
--

INSERT INTO public.testees VALUES (1, 'tutolmin@gmail.com', 'slakdjfsaofasldjfowijfasldkfjo45', '2021-06-20 09:25:00');
INSERT INTO public.testees VALUES (2, 'fercerpav@gmail.com', 'gsdfgsdghhwsehegsdfgsdfg', '2022-10-10 12:40:00');
INSERT INTO public.testees VALUES (3, 'strakhov.oleg@gmail.com', 'ghghghhjrtytsdsdfgsdfgs', '2022-10-10 15:00:00');


--
-- Name: addresses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.addresses_id_seq', 5547, true);


--
-- Name: breeds_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.breeds_id_seq', 3, true);


--
-- Name: domains_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.domains_id_seq', 11, true);


--
-- Name: environment_statuses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.environment_statuses_id_seq', 6, true);


--
-- Name: environments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.environments_id_seq', 247, true);


--
-- Name: hardware_profiles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.hardware_profiles_id_seq', 1, false);


--
-- Name: instance_statuses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.instance_statuses_id_seq', 6, true);


--
-- Name: instance_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.instance_types_id_seq', 113, true);


--
-- Name: instances_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.instances_id_seq', 261, true);


--
-- Name: messenger_messages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.messenger_messages_id_seq', 490, true);


--
-- Name: operating_systems_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.operating_systems_id_seq', 7, true);


--
-- Name: ports_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.ports_id_seq', 6500, true);


--
-- Name: session_oses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.session_oses_id_seq', 4, true);


--
-- Name: session_statuses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.session_statuses_id_seq', 6, true);


--
-- Name: session_techs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.session_techs_id_seq', 8, true);


--
-- Name: sessions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.sessions_id_seq', 3, true);


--
-- Name: task_instance_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.task_instance_types_id_seq', 68, true);


--
-- Name: task_oses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.task_oses_id_seq', 48, true);


--
-- Name: task_techs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.task_techs_id_seq', 21, true);


--
-- Name: tasks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.tasks_id_seq', 17, true);


--
-- Name: technologies_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.technologies_id_seq', 26, true);


--
-- Name: testees_id_seq; Type: SEQUENCE SET; Schema: public; Owner: symfony
--

SELECT pg_catalog.setval('public.testees_id_seq', 3, true);


--
-- Name: addresses addresses_mac; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.addresses
    ADD CONSTRAINT addresses_mac UNIQUE (mac);


--
-- Name: addresses addresses_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.addresses
    ADD CONSTRAINT addresses_pkey PRIMARY KEY (id);


--
-- Name: breeds breeds_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.breeds
    ADD CONSTRAINT breeds_pkey PRIMARY KEY (id);


--
-- Name: doctrine_migration_versions doctrine_migration_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.doctrine_migration_versions
    ADD CONSTRAINT doctrine_migration_versions_pkey PRIMARY KEY (version);


--
-- Name: domains domains_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.domains
    ADD CONSTRAINT domains_pkey PRIMARY KEY (id);


--
-- Name: environment_statuses environment_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.environment_statuses
    ADD CONSTRAINT environment_statuses_pkey PRIMARY KEY (id);


--
-- Name: environments environments_hash; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.environments
    ADD CONSTRAINT environments_hash UNIQUE (hash);


--
-- Name: environments environments_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.environments
    ADD CONSTRAINT environments_pkey PRIMARY KEY (id);


--
-- Name: hardware_profiles hardware_profiles_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.hardware_profiles
    ADD CONSTRAINT hardware_profiles_pkey PRIMARY KEY (id);


--
-- Name: instance_statuses instance_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.instance_statuses
    ADD CONSTRAINT instance_statuses_pkey PRIMARY KEY (id);


--
-- Name: instance_types instance_types_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.instance_types
    ADD CONSTRAINT instance_types_pkey PRIMARY KEY (id);


--
-- Name: instances instances_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.instances
    ADD CONSTRAINT instances_pkey PRIMARY KEY (id);


--
-- Name: messenger_messages messenger_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.messenger_messages
    ADD CONSTRAINT messenger_messages_pkey PRIMARY KEY (id);


--
-- Name: operating_systems operating_systems_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.operating_systems
    ADD CONSTRAINT operating_systems_pkey PRIMARY KEY (id);


--
-- Name: ports ports_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.ports
    ADD CONSTRAINT ports_pkey PRIMARY KEY (id);


--
-- Name: session_oses session_oses_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.session_oses
    ADD CONSTRAINT session_oses_pkey PRIMARY KEY (id);


--
-- Name: session_statuses session_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.session_statuses
    ADD CONSTRAINT session_statuses_pkey PRIMARY KEY (id);


--
-- Name: session_techs session_techs_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.session_techs
    ADD CONSTRAINT session_techs_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: task_instance_types task_instance_types_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.task_instance_types
    ADD CONSTRAINT task_instance_types_pkey PRIMARY KEY (id);


--
-- Name: task_oses task_oses_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.task_oses
    ADD CONSTRAINT task_oses_pkey PRIMARY KEY (id);


--
-- Name: task_techs task_techs_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.task_techs
    ADD CONSTRAINT task_techs_pkey PRIMARY KEY (id);


--
-- Name: tasks tasks_deploy; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.tasks
    ADD CONSTRAINT tasks_deploy UNIQUE (deploy);


--
-- Name: tasks tasks_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.tasks
    ADD CONSTRAINT tasks_pkey PRIMARY KEY (id);


--
-- Name: tasks tasks_project; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.tasks
    ADD CONSTRAINT tasks_project UNIQUE (project);


--
-- Name: tasks tasks_solve; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.tasks
    ADD CONSTRAINT tasks_solve UNIQUE (solve);


--
-- Name: tasks tasks_verify; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.tasks
    ADD CONSTRAINT tasks_verify UNIQUE (verify);


--
-- Name: technologies technologies_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.technologies
    ADD CONSTRAINT technologies_pkey PRIMARY KEY (id);


--
-- Name: testees testee_pkey; Type: CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.testees
    ADD CONSTRAINT testee_pkey PRIMARY KEY (id);


--
-- Name: addresses_ip; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX addresses_ip ON public.addresses USING btree (ip);


--
-- Name: breeds_name; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX breeds_name ON public.breeds USING btree (name);


--
-- Name: domains_name; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX domains_name ON public.domains USING btree (name);


--
-- Name: environments_deployment; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX environments_deployment ON public.environments USING btree (deployment);


--
-- Name: environments_statuses_status; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX environments_statuses_status ON public.environment_statuses USING btree (status);


--
-- Name: environments_verification; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX environments_verification ON public.environments USING btree (verification);


--
-- Name: hardware_profiles_name; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX hardware_profiles_name ON public.hardware_profiles USING btree (name);


--
-- Name: idx_2f95195364727bfc; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_2f95195364727bfc ON public.task_techs USING btree (tech_id);


--
-- Name: idx_2f9519538db60186; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_2f9519538db60186 ON public.task_techs USING btree (task_id);


--
-- Name: idx_334cbaa5613fecdf; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_334cbaa5613fecdf ON public.session_techs USING btree (session_id);


--
-- Name: idx_334cbaa564727bfc; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_334cbaa564727bfc ON public.session_techs USING btree (tech_id);


--
-- Name: idx_341510e3dca04d1; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_341510e3dca04d1 ON public.session_oses USING btree (os_id);


--
-- Name: idx_341510e613fecdf; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_341510e613fecdf ON public.session_oses USING btree (session_id);


--
-- Name: idx_4987eef72d84150; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_4987eef72d84150 ON public.task_instance_types USING btree (instance_type_id);


--
-- Name: idx_4987eef78db60186; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_4987eef78db60186 ON public.task_instance_types USING btree (task_id);


--
-- Name: idx_4ccbfb18115f0ee5; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_4ccbfb18115f0ee5 ON public.technologies USING btree (domain_id);


--
-- Name: idx_55ae98583dca04d1; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_55ae98583dca04d1 ON public.instance_types USING btree (os_id);


--
-- Name: idx_55ae9858cf6074e6; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_55ae9858cf6074e6 ON public.instance_types USING btree (hw_profile_id);


--
-- Name: idx_6fca75163a51721d; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_6fca75163a51721d ON public.addresses USING btree (instance_id);


--
-- Name: idx_75ea56e016ba31db; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_75ea56e016ba31db ON public.messenger_messages USING btree (delivered_at);


--
-- Name: idx_75ea56e0e3bd61ce; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_75ea56e0e3bd61ce ON public.messenger_messages USING btree (available_at);


--
-- Name: idx_75ea56e0fb7336f0; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_75ea56e0fb7336f0 ON public.messenger_messages USING btree (queue_name);


--
-- Name: idx_7a2700692d84150; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_7a2700692d84150 ON public.instances USING btree (instance_type_id);


--
-- Name: idx_7a2700696bf700bd; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_7a2700696bf700bd ON public.instances USING btree (status_id);


--
-- Name: idx_810d851aa8b4a30f; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_810d851aa8b4a30f ON public.operating_systems USING btree (breed_id);


--
-- Name: idx_9a609d135a544ee7; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_9a609d135a544ee7 ON public.sessions USING btree (testee_id);


--
-- Name: idx_9a609d136bf700bd; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_9a609d136bf700bd ON public.sessions USING btree (status_id);


--
-- Name: idx_b683c55f3dca04d1; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_b683c55f3dca04d1 ON public.task_oses USING btree (os_id);


--
-- Name: idx_b683c55f8db60186; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_b683c55f8db60186 ON public.task_oses USING btree (task_id);


--
-- Name: idx_ce28a831613fecdf; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_ce28a831613fecdf ON public.environments USING btree (session_id);


--
-- Name: idx_ce28a8316bf700bd; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_ce28a8316bf700bd ON public.environments USING btree (status_id);


--
-- Name: idx_ce28a8318db60186; Type: INDEX; Schema: public; Owner: symfony
--

CREATE INDEX idx_ce28a8318db60186 ON public.environments USING btree (task_id);


--
-- Name: instance_statuses_status; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX instance_statuses_status ON public.instance_statuses USING btree (status);


--
-- Name: instance_types_combo; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX instance_types_combo ON public.instance_types USING btree (hw_profile_id, os_id);


--
-- Name: instances_name; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX instances_name ON public.instances USING btree (name);


--
-- Name: operating_systems_combo; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX operating_systems_combo ON public.operating_systems USING btree (breed_id, release);


--
-- Name: ports_number; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX ports_number ON public.ports USING btree (number);


--
-- Name: session_oses_combo; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX session_oses_combo ON public.session_oses USING btree (session_id, os_id);


--
-- Name: session_statuses_status; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX session_statuses_status ON public.session_statuses USING btree (status);


--
-- Name: session_techs_combo; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX session_techs_combo ON public.session_techs USING btree (session_id, tech_id);


--
-- Name: sessions_hash; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX sessions_hash ON public.sessions USING btree (hash);


--
-- Name: task_instance_types_combo; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX task_instance_types_combo ON public.task_instance_types USING btree (task_id, instance_type_id);


--
-- Name: task_oses_combo; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX task_oses_combo ON public.task_oses USING btree (task_id, os_id);


--
-- Name: task_techs_combo; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX task_techs_combo ON public.task_techs USING btree (task_id, tech_id);


--
-- Name: tasks_name; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX tasks_name ON public.tasks USING btree (name);


--
-- Name: technologies_name; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX technologies_name ON public.technologies USING btree (name);


--
-- Name: testees_oauth_token; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX testees_oauth_token ON public.testees USING btree (oauth_token);


--
-- Name: uniq_899fd0cdf5b7af75; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX uniq_899fd0cdf5b7af75 ON public.ports USING btree (address_id);


--
-- Name: uniq_ce28a8313a51721d; Type: INDEX; Schema: public; Owner: symfony
--

CREATE UNIQUE INDEX uniq_ce28a8313a51721d ON public.environments USING btree (instance_id);


--
-- Name: messenger_messages notify_trigger; Type: TRIGGER; Schema: public; Owner: symfony
--

CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON public.messenger_messages FOR EACH ROW EXECUTE FUNCTION public.notify_messenger_messages();


--
-- Name: task_techs fk_2f95195364727bfc; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.task_techs
    ADD CONSTRAINT fk_2f95195364727bfc FOREIGN KEY (tech_id) REFERENCES public.technologies(id);


--
-- Name: task_techs fk_2f9519538db60186; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.task_techs
    ADD CONSTRAINT fk_2f9519538db60186 FOREIGN KEY (task_id) REFERENCES public.tasks(id);


--
-- Name: session_techs fk_334cbaa5613fecdf; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.session_techs
    ADD CONSTRAINT fk_334cbaa5613fecdf FOREIGN KEY (session_id) REFERENCES public.sessions(id);


--
-- Name: session_techs fk_334cbaa564727bfc; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.session_techs
    ADD CONSTRAINT fk_334cbaa564727bfc FOREIGN KEY (tech_id) REFERENCES public.technologies(id);


--
-- Name: session_oses fk_341510e3dca04d1; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.session_oses
    ADD CONSTRAINT fk_341510e3dca04d1 FOREIGN KEY (os_id) REFERENCES public.operating_systems(id);


--
-- Name: session_oses fk_341510e613fecdf; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.session_oses
    ADD CONSTRAINT fk_341510e613fecdf FOREIGN KEY (session_id) REFERENCES public.sessions(id);


--
-- Name: task_instance_types fk_4987eef72d84150; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.task_instance_types
    ADD CONSTRAINT fk_4987eef72d84150 FOREIGN KEY (instance_type_id) REFERENCES public.instance_types(id);


--
-- Name: task_instance_types fk_4987eef78db60186; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.task_instance_types
    ADD CONSTRAINT fk_4987eef78db60186 FOREIGN KEY (task_id) REFERENCES public.tasks(id);


--
-- Name: technologies fk_4ccbfb18115f0ee5; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.technologies
    ADD CONSTRAINT fk_4ccbfb18115f0ee5 FOREIGN KEY (domain_id) REFERENCES public.domains(id);


--
-- Name: instance_types fk_55ae98583dca04d1; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.instance_types
    ADD CONSTRAINT fk_55ae98583dca04d1 FOREIGN KEY (os_id) REFERENCES public.operating_systems(id);


--
-- Name: instance_types fk_55ae9858cf6074e6; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.instance_types
    ADD CONSTRAINT fk_55ae9858cf6074e6 FOREIGN KEY (hw_profile_id) REFERENCES public.hardware_profiles(id);


--
-- Name: addresses fk_6fca75163a51721d; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.addresses
    ADD CONSTRAINT fk_6fca75163a51721d FOREIGN KEY (instance_id) REFERENCES public.instances(id);


--
-- Name: instances fk_7a2700692d84150; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.instances
    ADD CONSTRAINT fk_7a2700692d84150 FOREIGN KEY (instance_type_id) REFERENCES public.instance_types(id);


--
-- Name: instances fk_7a2700696bf700bd; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.instances
    ADD CONSTRAINT fk_7a2700696bf700bd FOREIGN KEY (status_id) REFERENCES public.instance_statuses(id);


--
-- Name: operating_systems fk_810d851aa8b4a30f; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.operating_systems
    ADD CONSTRAINT fk_810d851aa8b4a30f FOREIGN KEY (breed_id) REFERENCES public.breeds(id);


--
-- Name: ports fk_899fd0cdf5b7af75; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.ports
    ADD CONSTRAINT fk_899fd0cdf5b7af75 FOREIGN KEY (address_id) REFERENCES public.addresses(id);


--
-- Name: sessions fk_9a609d135a544ee7; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT fk_9a609d135a544ee7 FOREIGN KEY (testee_id) REFERENCES public.testees(id);


--
-- Name: sessions fk_9a609d136bf700bd; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT fk_9a609d136bf700bd FOREIGN KEY (status_id) REFERENCES public.session_statuses(id);


--
-- Name: task_oses fk_b683c55f3dca04d1; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.task_oses
    ADD CONSTRAINT fk_b683c55f3dca04d1 FOREIGN KEY (os_id) REFERENCES public.operating_systems(id);


--
-- Name: task_oses fk_b683c55f8db60186; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.task_oses
    ADD CONSTRAINT fk_b683c55f8db60186 FOREIGN KEY (task_id) REFERENCES public.tasks(id);


--
-- Name: environments fk_ce28a8313a51721d; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.environments
    ADD CONSTRAINT fk_ce28a8313a51721d FOREIGN KEY (instance_id) REFERENCES public.instances(id);


--
-- Name: environments fk_ce28a831613fecdf; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.environments
    ADD CONSTRAINT fk_ce28a831613fecdf FOREIGN KEY (session_id) REFERENCES public.sessions(id);


--
-- Name: environments fk_ce28a8316bf700bd; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.environments
    ADD CONSTRAINT fk_ce28a8316bf700bd FOREIGN KEY (status_id) REFERENCES public.environment_statuses(id);


--
-- Name: environments fk_ce28a8318db60186; Type: FK CONSTRAINT; Schema: public; Owner: symfony
--

ALTER TABLE ONLY public.environments
    ADD CONSTRAINT fk_ce28a8318db60186 FOREIGN KEY (task_id) REFERENCES public.tasks(id);


--
-- PostgreSQL database dump complete
--

