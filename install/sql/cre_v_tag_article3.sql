create or replace view %DB_PREFIX%v_tag_article3 as
select max(tag_id) tag_id, b.node_name tag_name, b.slug slug, b.disp_seq
from %DB_PREFIX%article_tag3 a
left join %DB_PREFIX%tag3 b
on a.tag_id = b.node_id
group by tag_id