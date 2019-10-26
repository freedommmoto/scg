create table users
(
    id          integer primary key auto_increment,
    name        varchar(50) not null,
    password    text,
    enable      boolean  default true,
    last_update datetime default null
);

create table line_log
(
    id            integer primary key auto_increment,
    output_json   text,
    input_json    text,
    output_status int      default null,
    added_date    datetime default null,
    last_update   datetime default null
);

create table restaurants
(
    id          integer primary key auto_increment,
    google_id   varchar(90) not null,
    name        varchar(120) default null,
    address     text,
    img         text,
    added_date  datetime default null,
    last_update datetime default null
);

create table restaurant_order
(
    id            integer primary key auto_increment,
    restaurant_id integer,
    order_text    text,
    user_id       int      default null,
    added_date    datetime default null,
    last_update   datetime default null
);

create table line_users
(
    id            integer primary key auto_increment,
    id_line_users varchar(90) not null,
    restaurant_id integer  default 0,
    enable        boolean  default true,
    added_date    datetime default null,
    last_update   datetime default null
);