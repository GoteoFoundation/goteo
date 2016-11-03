
DELETE FROM page_lang WHERE id NOT IN (SELECT id FROM page);
DELETE FROM page_node WHERE page NOT IN (SELECT id FROM page);

UPDATE page
INNER JOIN page_node ON page_node.page=page.id
SET page.name = page_node.name,
page.description = page_node.description,
page.content= page_node.content
WHERE page_node.lang='es' AND page_node.node='goteo';

TRUNCATE TABLE `page_lang`;

INSERT INTO page_lang
SELECT page, lang, NAME, description, content, pending FROM page_node WHERE page_node.lang!='es' AND page_node.node='goteo';

DROP TABLE page_node;
