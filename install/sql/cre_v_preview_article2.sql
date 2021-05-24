create or replace view %DB_PREFIX%v_preview_article2 as
select 	  a.*
		, concat(a.article_date_u, a.article_id) article_date
		, b.path
		, b.node_name category
		, b.disp_seq
		, b.slug category_slug
		, b.color color
		, b.background_color background_color
		, b.icon_file icon_file
		, c.tag_id
		, c.tags
		, c.slug tags_slug
from %DB_PREFIX%article2 a
left join %DB_PREFIX%v_category2 b
on a.category_id = b.node_id
left join %DB_PREFIX%v_article_tag2 c
on a.article_id = c.article_id
where a.del_flag='0' and publication in ('1', '2')
and a.article_id != '0000000000'
