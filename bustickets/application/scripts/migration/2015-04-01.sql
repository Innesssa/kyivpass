

drop function maxsumm_from_kassaoperation(dt_begin timestamp,operation text);
create or replace function maxsumm_from_kassaoperation(dt_begin timestamp,operation text)
  returns table (openamount numeric(8,2),closeamount numeric(8,2),kassauid int,dt timestamp)
as
$body$
  select ks.amount,round(to_number(ks.symmcass_n6,'99999999999')/100,2),ks.kassauid,ks.dt
   from kassaoperation ks
   where ks.operation=$2 AND ks.dt = ( select max(k.dt) from kassaoperation k where k.operation=$2 AND dt<$1 and ks.kassauid=k.kassauid)
$body$
language sql;


drop function summ_from_kassaoperation(dt_begin timestamp,dt_end timestamp,operation text);
create or replace function summ_from_kassaoperation(dt_begin timestamp,dt_end timestamp,operation text)
  returns table (amount numeric(8,2),kassauid int)
as
$body$
  select sum(ks.amount),ks.kassauid
   from kassaoperation ks
   where ks.operation=$3 AND ( ks.dt between  $1 AND $2 )
   Group By ks.kassauid;
$body$
language sql;
select * from summ_from_kassaoperation('2015-03-18 00:00:00','2015-03-31 23:59:59','Inkas')


drop function maxsumm_from_kassaopenday(dt_begin timestamp,operation text,uid integer);
create or replace function maxsumm_from_kassaopenday(dt_begin timestamp,operation text,uid integer)
  returns table (openamount numeric(8,2),kassauid int,dt timestamp)
as
$body$
select ks.amount,ks.kassauid,ks.dt
   from kassaoperation ks
   where ks.kassauid=$3 AND ks.operation=$2 AND ks.dt > (

select date_trunc('day',max(k.dt))
from kassaoperation k
where k.operation=$2
AND dt<=$1
and ks.kassauid=k.kassauid

)
Order By ks.dt ASC
Limit 1
$body$
language sql;





select op.openamount,
       SUM (t.conv_tariff_with_benefits*1.0 + t.stat_tariff_with_benefits*1.0 + t.conv_tariff_with_benefits_vat*1.0 + t.stat_tariff_with_benefits_vat*1.0) as col1,
       SUM(t.conv_tariff_with_benefits*1.0 + t.conv_tariff_with_benefits_vat*1.0) as col2,
	   SUM(t.stat_tariff_with_benefits*1.0 + t.stat_tariff_with_benefits_vat*1.0) as col3,
	   SUM(insurer_tariff_with_benefits) as col4,
	   SUM(station_tax_tariff_with_benefits*1.0+station_tax_tariff_with_benefits_vat*1.0) as col5,
	   SUM(t.prepaid_vat*1.0+t.prepaid*1.0) as col6,
	   SUM(0.0) as col7,
	   SUM(0.0) as col8,
	   SUM(t.conv_luggage_tariff_vat*1.0
	       +t.stat_luggage_tariff_vat*1.0
		   +t.paidfromother_vat*1.0
		   +t.conv_tariff_with_benefits_vat*1.0
		   +t.stat_tariff_with_benefits_vat*1.0
		   +t.station_tax_tariff_with_benefits_vat*1.0
		   +t.prepaid_vat*1.0) as col9,
       cl.closeamount,

	   i.amount as inkasamount,

		   count(t.place) as places ,
		   t.kassauid ,
		   u.id,
		   u.login,
		   u.lastname,
		   u.middlename,
		   u.firstname
from  tickets as t, userlogindata_view u  ON
Left Join maxsumm_from_kassaoperation('2015-03-18 00:00:00', 'OpenDay') as op ON op.kassauid=u.id
Left Join maxsumm_from_kassaoperation('2015-03-31 23:59:59','CloseDay') as cl ON cl.kassauid=u.id
Left Join summ_from_kassaoperation('2015-03-18 00:00:00','2015-03-31 23:59:59','Inkas')  as i ON i.kassauid=u.id
where ( t.lastchange between '1426629600' AND '1427835599') AND u.id=t.kassauid AND t.station_buy='1306' AND t."status" IN ('paid','bpaid','deduction') u.perm_title='kassa'
Group By t.kassauid ,u.login, u.lastname,u.middlename,u.firstname,cl.closeamount,op.openamount,i.amount,u.id
