create or replace view %DB_PREFIX%v_preview_article as
select 	  a.*
		, concat(a.article_date_u, a.article_id) article_date
		, b.path
		, b.node_name category
		, b.disp_seq
		, b.color color
		, b.background_color background_color
		, b.icon_file icon_file
from %DB_PREFIX%article a
left join %DB_PREFIX%v_category b
on a.category_id = b.node_id
where a.del_flag='0' and publication in ('1', '2')
