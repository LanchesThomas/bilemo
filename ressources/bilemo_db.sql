--
-- PostgreSQL database dump
--

-- Dumped from database version 17.2
-- Dumped by pg_dump version 17.1

-- Started on 2025-05-26 12:07:49

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 4826 (class 1262 OID 16544)
-- Name: bilemo; Type: DATABASE; Schema: -; Owner: postgres
--

CREATE DATABASE bilemo WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'French_France.1252';


ALTER DATABASE bilemo OWNER TO postgres;

\connect bilemo

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 223 (class 1259 OID 16570)
-- Name: customer; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.customer (
    id integer NOT NULL,
    username character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL
);


ALTER TABLE public.customer OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 16569)
-- Name: customer_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.customer_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.customer_id_seq OWNER TO postgres;

--
-- TOC entry 4827 (class 0 OID 0)
-- Dependencies: 222
-- Name: customer_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.customer_id_seq OWNED BY public.customer.id;


--
-- TOC entry 219 (class 1259 OID 16554)
-- Name: doctrine_migration_versions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.doctrine_migration_versions (
    version character varying(191) NOT NULL,
    executed_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    execution_time integer
);


ALTER TABLE public.doctrine_migration_versions OWNER TO postgres;

--
-- TOC entry 218 (class 1259 OID 16546)
-- Name: product; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.product (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    color character varying(255) NOT NULL,
    price double precision NOT NULL,
    description text,
    brand character varying(255) NOT NULL,
    stock integer NOT NULL,
    created_at date NOT NULL,
    updatedat date NOT NULL
);


ALTER TABLE public.product OWNER TO postgres;

--
-- TOC entry 4828 (class 0 OID 0)
-- Dependencies: 218
-- Name: COLUMN product.created_at; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.product.created_at IS '(DC2Type:date_immutable)';


--
-- TOC entry 4829 (class 0 OID 0)
-- Dependencies: 218
-- Name: COLUMN product.updatedat; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN public.product.updatedat IS '(DC2Type:date_immutable)';


--
-- TOC entry 217 (class 1259 OID 16545)
-- Name: product_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.product_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.product_id_seq OWNER TO postgres;

--
-- TOC entry 4830 (class 0 OID 0)
-- Dependencies: 217
-- Name: product_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.product_id_seq OWNED BY public.product.id;


--
-- TOC entry 221 (class 1259 OID 16561)
-- Name: user; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public."user" (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    firstname character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    customer_id integer NOT NULL
);


ALTER TABLE public."user" OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 16560)
-- Name: user_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.user_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.user_id_seq OWNER TO postgres;

--
-- TOC entry 4831 (class 0 OID 0)
-- Dependencies: 220
-- Name: user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.user_id_seq OWNED BY public."user".id;


--
-- TOC entry 4658 (class 2604 OID 16573)
-- Name: customer id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customer ALTER COLUMN id SET DEFAULT nextval('public.customer_id_seq'::regclass);


--
-- TOC entry 4655 (class 2604 OID 16549)
-- Name: product id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product ALTER COLUMN id SET DEFAULT nextval('public.product_id_seq'::regclass);


--
-- TOC entry 4657 (class 2604 OID 16564)
-- Name: user id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user" ALTER COLUMN id SET DEFAULT nextval('public.user_id_seq'::regclass);


--
-- TOC entry 4820 (class 0 OID 16570)
-- Dependencies: 223
-- Data for Name: customer; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.customer (id, username, email, password) FROM stdin;
11	Customer_0	customer_0@mail.com	$2y$13$CGFA9tPE3cQRMmfnfhNTIOlXYYmaOyn8X3kc.XIFv9LbmuCVvCqLO
12	Customer_1	customer_1@mail.com	$2y$13$pTJ3zFVthuSxbUeBvOvwfu7HKp9ZLNSKW8IPpV8QRQYaJZZPvFCQu
13	Customer_2	customer_2@mail.com	$2y$13$CSehwHe0JiehdBTpm.PX3O.1aHyxwzN8geeb0MqZVV7aectt6C4Qu
14	Customer_3	customer_3@mail.com	$2y$13$ImdEisVOCG9HRISpaC22F.9U8ZmWXyfh1nYZyOOYjaVkCuFIxiLJG
15	Customer_4	customer_4@mail.com	$2y$13$8VAFkGrhIQ9z76XnpLLX5eU9R50IcmzDtKTX2fOABbkeo2nmf2IMS
16	admin	admin@mail.com	$2y$13$uUTsEPsN.o6ACmTt/RbI0.Yyk/gqfQBYVIYg148PChcRXRvA5R2n6
\.


--
-- TOC entry 4816 (class 0 OID 16554)
-- Dependencies: 219
-- Data for Name: doctrine_migration_versions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.doctrine_migration_versions (version, executed_at, execution_time) FROM stdin;
DoctrineMigrations\\Version20250331105421	2025-03-31 10:54:33	95
DoctrineMigrations\\Version20250331110044	2025-03-31 11:01:00	26
\.


--
-- TOC entry 4815 (class 0 OID 16546)
-- Dependencies: 218
-- Data for Name: product; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.product (id, name, color, price, description, brand, stock, created_at, updatedat) FROM stdin;
81	Téléphone_0	red	120	Description_0	Brand _0	10	2025-05-12	2025-05-12
82	Téléphone_1	red	120	Description_1	Brand _1	10	2025-05-12	2025-05-12
83	Téléphone_2	red	120	Description_2	Brand _2	10	2025-05-12	2025-05-12
84	Téléphone_3	red	120	Description_3	Brand _3	10	2025-05-12	2025-05-12
85	Téléphone_4	red	120	Description_4	Brand _4	10	2025-05-12	2025-05-12
86	Téléphone_5	red	120	Description_5	Brand _5	10	2025-05-12	2025-05-12
87	Téléphone_6	red	120	Description_6	Brand _6	10	2025-05-12	2025-05-12
88	Téléphone_7	red	120	Description_7	Brand _7	10	2025-05-12	2025-05-12
89	Téléphone_8	red	120	Description_8	Brand _8	10	2025-05-12	2025-05-12
90	Téléphone_9	red	120	Description_9	Brand _9	10	2025-05-12	2025-05-12
91	Téléphone_10	red	120	Description_10	Brand _10	10	2025-05-12	2025-05-12
92	Téléphone_11	red	120	Description_11	Brand _11	10	2025-05-12	2025-05-12
93	Téléphone_12	red	120	Description_12	Brand _12	10	2025-05-12	2025-05-12
94	Téléphone_13	red	120	Description_13	Brand _13	10	2025-05-12	2025-05-12
95	Téléphone_14	red	120	Description_14	Brand _14	10	2025-05-12	2025-05-12
96	Téléphone_15	red	120	Description_15	Brand _15	10	2025-05-12	2025-05-12
97	Téléphone_16	red	120	Description_16	Brand _16	10	2025-05-12	2025-05-12
98	Téléphone_17	red	120	Description_17	Brand _17	10	2025-05-12	2025-05-12
99	Téléphone_18	red	120	Description_18	Brand _18	10	2025-05-12	2025-05-12
100	Téléphone_19	red	120	Description_19	Brand _19	10	2025-05-12	2025-05-12
\.


--
-- TOC entry 4818 (class 0 OID 16561)
-- Dependencies: 221
-- Data for Name: user; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public."user" (id, name, firstname, email, customer_id) FROM stdin;
31	User_0	User_Firstname_0	user_0@mail.com	13
32	User_1	User_Firstname_1	user_1@mail.com	14
33	User_2	User_Firstname_2	user_2@mail.com	13
34	User_3	User_Firstname_3	user_3@mail.com	13
35	User_4	User_Firstname_4	user_4@mail.com	11
36	User_5	User_Firstname_5	user_5@mail.com	11
37	User_6	User_Firstname_6	user_6@mail.com	12
40	User_9	User_Firstname_9	user_9@mail.com	11
55	adminName	adminFirstName	adminEmail@email.com	16
57	John Doe	John	johndoe@mail.com	16
60	John Doe	John	johndoe3@mail.com	16
\.


--
-- TOC entry 4832 (class 0 OID 0)
-- Dependencies: 222
-- Name: customer_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.customer_id_seq', 16, true);


--
-- TOC entry 4833 (class 0 OID 0)
-- Dependencies: 217
-- Name: product_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.product_id_seq', 100, true);


--
-- TOC entry 4834 (class 0 OID 0)
-- Dependencies: 220
-- Name: user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.user_id_seq', 60, true);


--
-- TOC entry 4667 (class 2606 OID 16577)
-- Name: customer customer_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.customer
    ADD CONSTRAINT customer_pkey PRIMARY KEY (id);


--
-- TOC entry 4662 (class 2606 OID 16559)
-- Name: doctrine_migration_versions doctrine_migration_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.doctrine_migration_versions
    ADD CONSTRAINT doctrine_migration_versions_pkey PRIMARY KEY (version);


--
-- TOC entry 4660 (class 2606 OID 16553)
-- Name: product product_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product
    ADD CONSTRAINT product_pkey PRIMARY KEY (id);


--
-- TOC entry 4665 (class 2606 OID 16568)
-- Name: user user_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (id);


--
-- TOC entry 4663 (class 1259 OID 16583)
-- Name: idx_8d93d6499395c3f3; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_8d93d6499395c3f3 ON public."user" USING btree (customer_id);


--
-- TOC entry 4668 (class 2606 OID 16578)
-- Name: user fk_8d93d6499395c3f3; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT fk_8d93d6499395c3f3 FOREIGN KEY (customer_id) REFERENCES public.customer(id);


-- Completed on 2025-05-26 12:07:49

--
-- PostgreSQL database dump complete
--

