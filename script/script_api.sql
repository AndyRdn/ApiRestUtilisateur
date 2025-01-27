create extension if not exists pgcrypto;

create sequence double_authentification_id_seq;

alter sequence double_authentification_id_seq owner to postgres;

create sequence historique_utilisateur_id_seq;

alter sequence historique_utilisateur_id_seq owner to postgres;

create sequence inscription_pending_id_seq;

alter sequence inscription_pending_id_seq owner to postgres;

create sequence login_tentative_id_seq;

alter sequence login_tentative_id_seq owner to postgres;

create sequence utilisateur_id_seq;

alter sequence utilisateur_id_seq owner to postgres;

create sequence config_id_seq;

alter sequence config_id_seq owner to postgres;

create sequence token_utilisateur_id_seq;

alter sequence token_utilisateur_id_seq owner to postgres;

create table doctrine_migration_versions
(
    version        varchar(191) not null
        primary key,
    executed_at    timestamp(0) default NULL::timestamp without time zone,
    execution_time integer
);

alter table doctrine_migration_versions
    owner to postgres;

create table historique_utilisateur
(
    id             integer      not null
        primary key,
    prenom         varchar(255) not null,
    nom            varchar(255) default NULL::character varying,
    date_naissance timestamp(0) not null,
    genre          integer      not null,
    mot_de_passe   varchar(255) not null,
    updated_at     timestamp(0) not null
);

comment on column historique_utilisateur.date_naissance is '(DC2Type:datetime_immutable)';

comment on column historique_utilisateur.updated_at is '(DC2Type:datetime_immutable)';

alter table historique_utilisateur
    owner to postgres;

create table inscription_pending
(
    id             integer      not null
        primary key,
    prenom         varchar(255) not null,
    nom            varchar(255) default NULL::character varying,
    date_naissance timestamp(0) not null,
    genre          integer      not null,
    mail           varchar(255) not null,
    mot_de_passe   varchar(255) not null
);

comment on column inscription_pending.date_naissance is '(DC2Type:datetime_immutable)';

alter table inscription_pending
    owner to postgres;

create table utilisateur
(
    id             integer      not null
        primary key,
    prenom         varchar(255) not null,
    nom            varchar(255) default NULL::character varying,
    date_naissance timestamp(0) not null,
    genre          integer      not null,
    mail           varchar(255) not null,
    mot_de_passe   varchar(255) not null
);

comment on column utilisateur.date_naissance is '(DC2Type:datetime_immutable)';

alter table utilisateur
    owner to postgres;

create table double_authentification
(
    id             integer      not null
        primary key,
    utilisateur_id integer
        constraint fk_de0128cdfb88e14f
            references utilisateur,
    code           integer      not null,
    daty           timestamp(0) not null
);

comment on column double_authentification.daty is '(DC2Type:datetime_immutable)';

alter table double_authentification
    owner to postgres;

create index idx_de0128cdfb88e14f
    on double_authentification (utilisateur_id);

create table login_tentative
(
    id             integer not null
        primary key,
    utilisateur_id integer not null
        constraint fk_bb5da80cfb88e14f
            references utilisateur,
    tentative      integer not null
);

alter table login_tentative
    owner to postgres;

create unique index uniq_bb5da80cfb88e14f
    on login_tentative (utilisateur_id);

create table config
(
    id     integer      not null
        primary key,
    nom    varchar(255) not null,
    valeur varchar(255) not null
);

alter table config
    owner to postgres;

create table token_utilisateur
(
    id             integer      not null
        primary key,
    utilisateur_id integer
        constraint fk_312d3129fb88e14f
            references utilisateur,
    token          varchar(255) not null,
    updated_at     timestamp(0) not null
);

comment on column token_utilisateur.updated_at is '(DC2Type:datetime_immutable)';

alter table token_utilisateur
    owner to postgres;

create unique index uniq_312d3129fb88e14f
    on token_utilisateur (utilisateur_id);

INSERT INTO public.config (id, nom, valeur) VALUES (1, 'tentative', '4');
INSERT INTO public.config (id, nom, valeur) VALUES (2, 'delais', '90');
INSERT INTO public.config (id, nom, valeur) VALUES (3, 'token', '3600');


INSERT INTO public.utilisateur (id, prenom, nom, date_naissance, genre, mail, mot_de_passe) VALUES (2, 'Mia', 'Aina', '2005-04-27 00:00:00', 1, 'miarantsoasuper3000@gmail.com', '$2y$12$FtzibUJA32B7Pluso7n8ROlkeEK/2bwopPEvLJY1Qn1McOAEKS.5O');
INSERT INTO public.utilisateur (id, prenom, nom, date_naissance, genre, mail, mot_de_passe) VALUES (3, 'Andy', 'Aina', '2005-04-27 00:00:00', 1, 'irina.elina.r@gmail.com', '$2y$12$zn9AZBOyi7l4jrOFiJwXDOamp75WbttxlIH1tq4Hyd6MQpqtu7yvy');
INSERT INTO public.utilisateur (id, prenom, nom, date_naissance, genre, mail, mot_de_passe) VALUES (4, 'Andy', 'Roberto', '2005-04-27 00:00:00', 0, 'andyrdn4@gmail.com', '090b235e9eb8f197f2dd927937222c570396d971222d9009a9189e2b6cc0a2c1');
INSERT INTO public.utilisateur (id, prenom, nom, date_naissance, genre, mail, mot_de_passe) VALUES (5, 'Irina', 'El', '2005-04-27 00:00:00', 0, 'el.inar7305@gmail.com', '07123e1f482356c415f684407a3b8723e10b2cbbc0b8fcd6282c49d37c9c1abc');


INSERT INTO public.doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\Version20241219060350', '2024-12-19 22:22:11', 45);
INSERT INTO public.doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\Version20241219204840', '2024-12-19 22:22:12', 7);
INSERT INTO public.doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\Version20241220082114', '2024-12-20 09:21:37', 33);
INSERT INTO public.doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\Version20241220093626', '2024-12-20 10:36:33', 45);
INSERT INTO public.doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\Version20241220101812', '2024-12-20 11:18:17', 7);


INSERT INTO public.double_authentification (id, utilisateur_id, code, daty) VALUES (11, 2, 859663, '2024-12-20 09:30:37');
INSERT INTO public.double_authentification (id, utilisateur_id, code, daty) VALUES (12, null, 889204, '2024-12-20 09:31:43');
INSERT INTO public.double_authentification (id, utilisateur_id, code, daty) VALUES (13, null, 694153, '2024-12-20 09:39:14');
INSERT INTO public.double_authentification (id, utilisateur_id, code, daty) VALUES (14, null, 847331, '2024-12-20 09:51:34');
INSERT INTO public.double_authentification (id, utilisateur_id, code, daty) VALUES (15, 4, 761339, '2024-12-20 09:52:31');
INSERT INTO public.double_authentification (id, utilisateur_id, code, daty) VALUES (16, null, 260928, '2024-12-20 10:00:09');
INSERT INTO public.double_authentification (id, utilisateur_id, code, daty) VALUES (17, 5, 778796, '2024-12-20 10:23:36');
INSERT INTO public.double_authentification (id, utilisateur_id, code, daty) VALUES (18, 5, 748593, '2024-12-20 10:25:15');


INSERT INTO public.historique_utilisateur (id, prenom, nom, date_naissance, genre, mot_de_passe, updated_at) VALUES (1, 'Andy', 'Roberto', '2005-04-27 00:00:00', 0, '090b235e9eb8f197f2dd927937222c570396d971222d9009a9189e2b6cc0a2c1', '2024-12-20 09:56:29');
INSERT INTO public.historique_utilisateur (id, prenom, nom, date_naissance, genre, mot_de_passe, updated_at) VALUES (2, 'Andy', 'Aina', '2005-04-27 00:00:00', 0, '07123e1f482356c415f684407a3b8723e10b2cbbc0b8fcd6282c49d37c9c1abc', '2024-12-20 10:00:36');
INSERT INTO public.historique_utilisateur (id, prenom, nom, date_naissance, genre, mot_de_passe, updated_at) VALUES (3, 'Irina', 'El', '2005-04-27 00:00:00', 0, '07123e1f482356c415f684407a3b8723e10b2cbbc0b8fcd6282c49d37c9c1abc', '2024-12-20 10:02:28');


INSERT INTO public.inscription_pending (id, prenom, nom, date_naissance, genre, mail, mot_de_passe) VALUES (1, 'Mia', 'Aina', '2005-04-27 00:00:00', 1, 'miarantsoasuper3000@gmail.com', '$2y$12$FtzibUJA32B7Pluso7n8ROlkeEK/2bwopPEvLJY1Qn1McOAEKS.5O');
INSERT INTO public.inscription_pending (id, prenom, nom, date_naissance, genre, mail, mot_de_passe) VALUES (4, 'Andy', 'Aina', '2005-04-27 00:00:00', 1, 'irina.elina.r@gmail.com', '$2y$12$zn9AZBOyi7l4jrOFiJwXDOamp75WbttxlIH1tq4Hyd6MQpqtu7yvy');
INSERT INTO public.inscription_pending (id, prenom, nom, date_naissance, genre, mail, mot_de_passe) VALUES (5, 'Andy', 'Aina', '2005-04-27 00:00:00', 1, 'andyrdn4@gmail.com', '0ebe2eca800cf7bd9d9d9f9f4aafbc0c77ae155f43bbbeca69cb256a24c7f9bb');
INSERT INTO public.inscription_pending (id, prenom, nom, date_naissance, genre, mail, mot_de_passe) VALUES (6, 'Andy', 'Aina', '2005-04-27 00:00:00', 0, 'el.inar7305@gmail.com', '07123e1f482356c415f684407a3b8723e10b2cbbc0b8fcd6282c49d37c9c1abc');

INSERT INTO public.login_tentative (id, utilisateur_id, tentative) VALUES (1, 2, 4);
INSERT INTO public.login_tentative (id, utilisateur_id, tentative) VALUES (2, 3, 2);
INSERT INTO public.login_tentative (id, utilisateur_id, tentative) VALUES (3, 4, 4);
INSERT INTO public.login_tentative (id, utilisateur_id, tentative) VALUES (4, 5, 4);
