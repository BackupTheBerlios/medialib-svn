SELECT ins.creation_date AS date
,col_cont."VARCHAR" AS collection
,tit_cont."VARCHAR" AS title
,med.urn
FROM template AS tpl
LEFT JOIN content AS col_cont ON tpl.data_field_id = col_cont.data_field_id AND tpl.search = 'collection'
LEFT JOIN content AS tit_cont ON tpl.data_field_id = tit_cont.data_field_id AND tpl.search = 'title'
INNER JOIN instance AS ins ON tit_cont.entity_id = ins.id
INNER JOIN set ON ins.id = set.instance_id
LEFT JOIN media AS med ON set.id = med.set_id AND mime_type = 'cov'
WHERE ins.id = 59980