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

