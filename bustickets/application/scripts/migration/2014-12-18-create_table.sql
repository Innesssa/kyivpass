-----------------------------------------------------

CREATE TYPE binary_type AS ENUM ('0', '1');
CREATE TYPE role_entity AS ENUM ('role','permission');
CREATE TYPE error_level AS ENUM	('critical','major','minor','info','debug');

-----------------------------------------------------

create table userLoginData(
Id		serial primary key,
login		varchar(50) not null unique,
password	varchar(32) not null,
quantity_false	int
);
comment on table userLoginData is 'таблиця для авторизації користувача в системі АРМ';

comment on column userLoginData.id is 'id користувача';
comment on column userLoginData.login is 'логін користувача в системі';
comment on column userLoginData.password is 'пароль криптований md5';
comment on column userLoginData.quantity_false is 'кількість помилкових входів до системи';


create table userActionsData(
id 	serial primary key,
need_reload 	binary_type,
last_online 	TIMESTAMP,
last_login 	  TIMESTAMP,
last_change_password 	TIMESTAMP
);

comment on table userActionsData is 'таблиця дій користувача';

comment on column userActionsData.id is 'id користувача';
comment on column userActionsData.need_reload is 'ознака примусового перезавантаження данних користувача';
comment on column userActionsData.last_online is 'час останньої активності користувача';
comment on column userActionsData.last_login is	'час останнього входу у систем';
comment on column userActionsData.last_change_password is 'час останньої зміни паролю';


create table userPersonalData(
id 	serial primary key,
lastname 	varchar(50) not null,
middlename 	varchar(50) not null,
firstname 	varchar(50) not null
);

comment on table userPersonalData is 'таблиця з персональними даними користувача';
comment on column userPersonalData.id is 'id користувача';
comment on column userPersonalData.lastname is 'прізвище';
comment on column userPersonalData.middlename is 'по-батькові';
comment on column userPersonalData.firstname is	'ім''я';


create table userServiceData(
id 	serial primary key,
persnum 	varchar(50) not null,
company 	varchar(50) not null,
dept 		varchar(50) not null,
position 	varchar(50) not null,
workstart 	date,
workend 	date
);
comment on table userServiceData is 'таблиця зі службовими даними користувача';
comment on column userServiceData.id is	'id користувача';
comment on column userServiceData.persnum is 'табельний номер';
comment on column userServiceData.company is 'організація';
comment on column userServiceData.dept is 'відділ';
comment on column userServiceData.position is 'посада';
comment on column userServiceData.workstart is 'початок роботи';
comment on column userServiceData.workend is 'дата звільнення ';


create table ScheduleType(
id 	serial primary key,
sign 	int not null,
datestart 	date not null,
dateend 	date,
weekdate 	varchar(50) not null
);
comment on table ScheduleType is 'таблиця з типами проміжків часу';
comment on column ScheduleType.id is 'id відрізку';
comment on column ScheduleType.sign is 'тип відрізку часу(дозволено/заборонено)';
comment on column ScheduleType.datestart is 'початок відрізку часу';
comment on column ScheduleType.dateend is 'закінчення відрізку часу';
comment on column ScheduleType.weekdate is 'дні тижня';


create table userSchedule(
id 	serial primary key,
sign 	integer references ScheduleType(Id),
datestart 	date not null,
dateend 	date,
area 	varchar(50)
);
create index on userSchedule(datestart);
create index on userSchedule(dateend);
comment on table userSchedule is 'таблиця з розкладом роботи персоналу';
comment on column userSchedule.id is 'id користувача';
comment on column userSchedule.sign is 'тип часового відрізку (дозволено/заборонено)';
comment on column userSchedule.datestart is 'початок відрізку часу';
comment on column userSchedule.dateend is 'закінчення відрізку часу';
comment on column userSchedule.area is 'зона впливу';

create table permissionRoles(
  id 		serial primary key,
  title 		varchar(255) not null unique,
  description 	text
);
comment on table permissionRoles is 'таблиця з ролями доступу для користувачів системи';
comment on column permissionRoles.id is 'id ролі';
comment on column permissionRoles.title is 'назва';
comment on column permissionRoles.description is 'опис';



create table HandBooks(
  id   		serial primary key,
  par_id 		int,
  type	        varchar(50) not null,
  title	        varchar(50) not null,
  description     text
);
comment on table HandBooks is 'таблиця зі справочною інофрмацією';
comment on column HandBooks.id is 'id запису';
comment on column HandBooks.par_id is 'id запису';
comment on column HandBooks.type is 'тип запису';
comment on column HandBooks.title is 'назва елементу';
comment on column HandBooks.description is 'коментар';


create table lnkRole2Permission(
  id 		serial primary key,
  id_role 	int unique,
  id_permission 	int unique
);
comment on table lnkRole2Permission is 'таблиця зв''язку ролей(permissionRoles) з доступами(permissions)';
comment on column lnkRole2Permission.id is 'id запису';
comment on column lnkRole2Permission.id_role is 'id доступу';
comment on column lnkRole2Permission.id_permission is 'опис';



create table lnkUser2System(
  id 	serial primary key,
  idu 	int unique,
  entity 	role_entity,
  id_entity 	int unique
);
comment on table lnkUser2System is 'таблиця зв''язку користувача системы з ролями(permissionRoles) та з доступами(permissions)';
comment on column lnkUser2System.id is 'id запису';
comment on column lnkUser2System.idu is 'id користувача';
comment on column lnkUser2System.entity is 'ознака - запис ролі або запис доступу';
comment on column lnkUser2System.id_entity is 'id відповідного запису - ролі або доступу, в залежності від entity';



create table controllers(
id 	serial primary key,
title 	varchar(255) not null unique,
description 	text
);
comment on table controllers is 'таблиця блоків АРМу';
comment on column controllers.id is 'id ролі';
comment on column controllers.title is 'назва';
comment on column controllers.description is 'опис';



create table actions(
id 	serial primary key,
title 	varchar(255) not null unique,
description 	text
);
comment on table actions is 'таблиця дій в АРМі';
comment on column actions.id is 'id дії';
comment on column actions.title is 'назва';
comment on column actions.description is 'опис';



create table lnkAction2Controller(
id 	serial primary key,
id_controller 	int unique,
id_action 	int unique
);
comment on table lnkAction2Controller is 'таблиця дій(actions) у блоках(controllers) АРМу';
comment on column lnkAction2Controller.id is 'id запису';
comment on column lnkAction2Controller.id_controller is 'id блоку';
comment on column lnkAction2Controller.id_action is 'id дії';




create table userEventsSysLog(
id 		serial primary key,
idu 		int,
dt 		timestamp,
id_controller 	int,
id_action 	int,
permissions 	text,
request 	text,
answer 	text
);
comment on table userEventsSysLog is 'таблиця збирання дій користувачів АРМу';
comment on column userEventsSysLog.id is 'id дії';
comment on column userEventsSysLog.idu is 'id користувача';
comment on column userEventsSysLog.dt is 'час дії';
comment on column userEventsSysLog.id_controller is 'id блока';
comment on column userEventsSysLog.id_action is 'id дії';
comment on column userEventsSysLog.permissions is 'права користувача';
comment on column userEventsSysLog.request is 'вхідні данні';
comment on column userEventsSysLog.answer is 'вихідні данні';


create table errorSysLog(
id 		serial primary key,
error_level 	error_level,
idu 		int,
dt 		timestamp,
id_controller 	int,
id_action 	int,
error_code 	int,
error_str_number int,
error_file 	varchar(100),
error_message 	text,
error_trace 	text,
request 	text
);
comment on table errorSysLog is 'таблиця збирання виключних ситуацій системи АРМу';
comment on column errorSysLog.id is 'id запису';
comment on column errorSysLog.error_level is 'категорія помилки';
comment on column errorSysLog.idu is 'id користувача';
comment on column errorSysLog.dt is 'час дії';
comment on column errorSysLog.id_controller is 'id блока';
comment on column errorSysLog.id_action is 'id дії';
comment on column errorSysLog.error_code is 'код помилки';
comment on column errorSysLog.error_str_number is 'номер рядка у файлі де виникла помилка';
comment on column errorSysLog.error_file is 'ім''я файлу, де виникла помилка';
comment on column errorSysLog.error_message is 'опис помилки';
comment on column errorSysLog.error_trace is 'ланцюжок класів і методів що призвели до помилки';
comment on column errorSysLog.request is 'вхідні данні';



create table errorsCustomList(
id 	serial primary key,
message 	varchar(100) unique
);
comment on table errorsCustomList is 'таблиця довідник зі списком помилок АРМу';
comment on column errorsCustomList.id is 'id запису';
comment on column errorsCustomList.message is 'опис помилки';



---------------------------
create table lnkStation2Route(
id 		serial primary key,
routeid 	int not null,
stationid 	int not null,
pos 		int,
timeperiod 	int,
holdtime 	int,
distantion 	numeric(10,2),
price 		numeric(10,2),
priceinzone 	numeric(10,2),
description 	text
);

create unique index lnkStation2Route_uniq on lnkStation2Route(routeid,stationid,pos);

comment on table  lnkStation2Route is 'таблиця список зупинок за маршрутом';
comment on column lnkStation2Route.id is 'id запису';
comment on column lnkStation2Route.routeid is 'id маршруту';
comment on column lnkStation2Route.stationid is 'id зупики';
comment on column lnkStation2Route.pos is 'номер за маршрутом';
comment on column lnkStation2Route.timeperiod is 'час слідування від попередньої зупинки у хвилинах';
comment on column lnkStation2Route.holdtime is 'час зупинки на зупинці у хвилинах';
comment on column lnkStation2Route.distantion is 'відстань від';
comment on column lnkStation2Route.price is 'ціна від попередньої зупинки';
comment on column lnkStation2Route.priceinzone is 'ціна у зоні';
comment on column lnkStation2Route.description is 'коментарі';



create table Routes(
id 		serial primary key,
code 		varchar(50) not null,
title 		varchar(250) not null,
conveyorid 	int not null,
typePrice 	int not null,
insurerid 	int not null,
insurerrateid 	int not null,
vehicletypeid 	int not null,
countplaces 	int not null,
description 	text
);
comment on table  Routes is 'таблиця маршрутів';
comment on column Routes.id is 'id маршруту';
comment on column Routes.code is 'номер маршруту';
comment on column Routes.title is 'назва';
comment on column Routes.conveyorid is 'юридична особа-власник маршруту';
comment on column Routes.typePrice is 'тип очислення ціни квитка';
comment on column Routes.insurerid is 'страхова компанія';
comment on column Routes.insurerrateid is 'тип страхування';
comment on column Routes.vehicletypeid is 'тип траспортного засобу';
comment on column Routes.countplaces is 'кількість мість';
comment on column Routes.description is 'коментар';




create table ScheduleTable(
id 		serial primary key,
routeid 	int not null,
year 		varchar(10),
month 		varchar(10),
day 		varchar(10),
hour 		varchar(10),
minute 		varchar(10),
reserv 		text
);
comment on table  ScheduleTable is 'таблиця розкладу за маршрутом';
comment on column ScheduleTable.id is 'id запису';
comment on column ScheduleTable.routeid is 'id маршруту';
comment on column ScheduleTable.year is 'рік';
comment on column ScheduleTable.month is 'місяц';
comment on column ScheduleTable.day is 'день';
comment on column ScheduleTable.hour is 'година';
comment on column ScheduleTable.minute is 'хвилина';
comment on column ScheduleTable.reserv is 'массив заброньованих місць';




create table tickets(
id 		serial primary key,
routeid 	int not null,
sheduleid 	int not null,
code 		varchar(20) not null,
buytime 	timestamp not null,
buyuid 		int not null,
buystation 	int not null,
timestart 	timestamp not null,
startstation 	int not null,
finishstation 	int not null,
placenumber 	int not null,
fullamount 	numeric(10,2),
insurerate 	numeric(10,2),
tax 		numeric(10,2),
servicetax 	numeric(10,2),
stationtax 	numeric(10,2),
discounttype 	int,
discount 	numeric(10,2),
driverpart 	numeric(10,2),
refund 		numeric(10,2),
refundtime 	timestamp,
refunduid 	int,
refundstation 	int,
description 	text
);

create unique index tickets_route on tickets(routeid,timestart);

comment on table  tickets is 'таблиця придбаних квитків';
comment on column tickets.id is 'id квитка';
comment on column tickets.routeid is 'id маршруту';
comment on column tickets.sheduleid is 'id розкладу';
comment on column tickets.code is 'номер квитка';
comment on column tickets.buytime is 'дата та час продажу квитка';
comment on column tickets.buyuid is 'id користувача, який продав квиток';
comment on column tickets.buystation is 'id станції, де продано квиток';
comment on column tickets.timestart is 'дата та час відправлення рейсу';
comment on column tickets.startstation is 'станція відправдення';
comment on column tickets.finishstation is 'cтанція прибуття';
comment on column tickets.placenumber is 'номер місця';
comment on column tickets.fullamount is 'повна ціна';
comment on column tickets.insurerate is 'страховий внесок';
comment on column tickets.tax is 'податок';
comment on column tickets.servicetax is 'станційний збір';
comment on column tickets.stationtax is 'податок з станції';
comment on column tickets.discounttype is 'тип знижки на квиток (пільговий проїзд)';
comment on column tickets.discount is 'частка знижки';
comment on column tickets.driverpart is 'частка перевізника';
comment on column tickets.refund is 'сумма коштів при повернені квитка';
comment on column tickets.refundtime is 'дата та час повернення';
comment on column tickets.refunduid is 'id користувача що прийняв до повернення квиток';
comment on column tickets.refundstation is 'id станції , де повернуто квиток';
comment on column tickets.description is 'для коментарів';



create table race(
id 		serial primary key,
routeid 	int,
scheduleid 	int,
dtstart 	timestamp,
conveyorid 	int,
driverid 	int,
placescount 	int,
vechlienumber 	varchar(10),
disp_uid 	int,
dateaction 	timestamp
);

comment on table  race is 'таблиця відправлених рейсів';
comment on column race.id is 'id рейсу';
comment on column race.routeid is 'id маршруту';
comment on column race.scheduleid is 'id розкладу';
comment on column race.dtstart is 'дата та час відправлення';
comment on column race.conveyorid is 'id компанії-перевізника';
comment on column race.driverid is 'id перевізника';
comment on column race.placescount is 'кількість місць';
comment on column race.vechlienumber is 'номер транспортного засобу';
comment on column race.disp_uid is 'диспечера(користувача)';
comment on column race.dateaction is 'дата та час закриття рейсу за маршрутом';


------------------------------------


--- create table PhoneList(
--- id 		serial primary key,
--- phone 		varchar(20),
--- descr 		varchar(40)
---);
--- comment on table  PhoneList is 'телефони';
--- comment on column PhoneList.id is 'id контакту';
--- comment on column PhoneList.phone is 'номер телефону';
--- comment on column PhoneList.descr is 'опис телефону';



create table ContactList(
id 		serial primary key,
legalid 	int,
title 		varchar(80),
type 		int,
phone1 		varchar(20),
descr1 		varchar(40),
phone2 		varchar(20),
descr2 		varchar(40),
phone3 		varchar(20),
descr3 		varchar(40),
phone4 		varchar(20),
descr4 		varchar(40)
);
comment on table  ContactList is 'контакти та телефони';
comment on column ContactList.id is 'id контакту';
comment on column ContactList.legalid is 'id організації';
comment on column ContactList.title is 'ім''я/назва контакту';
comment on column ContactList.type is 'тип контакту';
comment on column ContactList.phone1 is 'номер телефону1';
comment on column ContactList.descr1 is 'опис телефону1';
comment on column ContactList.phone2 is 'номер телефону2';
comment on column ContactList.descr2 is 'опис телефону2';
comment on column ContactList.phone3 is 'номер телефону3';
comment on column ContactList.descr3 is 'опис телефону3';
comment on column ContactList.phone4 is 'номер телефону4';
comment on column ContactList.descr4 is 'опис телефону4';



create table LegalEntityList(
id 		serial primary key,
title 		varchar(120),
type 		int,
IPN 		varchar(12),
EDRPOU 		varchar(8),
MFO 		varchar(6),
accountnr 	varchar(20),
Bank 		varchar(120),
legaladdress 	varchar(120),
realaddress 	varchar(120),
email 		varchar(120)
);
comment on table  LegalEntityList is 'таблиця організацій';
comment on column LegalEntityList.id is 'id організації';
comment on column LegalEntityList.title is 'назва організації';
comment on column LegalEntityList.type is 'тип організації';
comment on column LegalEntityList.IPN is 'індивідуальний податковий номер';
comment on column LegalEntityList.EDRPOU is 'ЄДРПОУ';
comment on column LegalEntityList.accountnr is 'номер банківського рахунку';
comment on column LegalEntityList.MFO is 'МФО банку';
comment on column LegalEntityList.Bank is 'назва банку';
comment on column LegalEntityList.legaladdress is 'юридична адреса';
comment on column LegalEntityList.realaddress is 'діюча адреса';
comment on column LegalEntityList.email is 'електронна пошта';








-----------------------------------------
create view organizationlist_conveyor_view as
select a.id, a.title, a.type, a.ipn, a.edrpou, a.mfo, a.accountnr, a.bank, a.legaladdress, a.realaddress, a.email
from organizationlist a
where type=(select id from handbooks where title='перевізник');

create view organizationlist_driver_view as
select id, title, type, ipn, edrpou, mfo, accountnr, bank, legaladdress, realaddress, email
from organizationlist
where type=(select id from handbooks where title='водій');

create view organizationlist_insurance_view as
select id, title, type, ipn, edrpou, mfo, accountnr, bank, legaladdress, realaddress, email
from organizationlist
where type=(select id from handbooks where title='страхова компанія');

-----------------------
create view handbooks_price_view as
select id, par_id, type, title, description
from handbooks
where type='price'
order by title;

create view handbooks_insurrate_view as
select id, par_id, type, title, description
from handbooks
where type='insurrate'
order by title;

create view handbooks_vehicletype_view as
select id, par_id, type, title, description
from handbooks
where type='vehicletype'
order by title;

create view handbooks_sitnumber_view as
select id, par_id, type, title, description
from handbooks
where type='sitnumber'
order by title;

------------------------------
create view userlogindata_view as
select l.*,p.lastname,p.middlename,p.firstname,r.id_entity,d.title as perm_title,d.description as perm_description
from userlogindata l,userpersonaldata p,lnkuser2system r
Left Join permissionroles d ON r.id_entity = d.id
where l.id = p.id AND l.id = r.idu


---------------------------------------------------
