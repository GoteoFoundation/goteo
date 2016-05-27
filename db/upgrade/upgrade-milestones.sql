INSERT INTO project_milestone (`project`, `milestone`, `date`, `post`)
SELECT blog.owner AS `project`,
		NULL AS `milestone`,
	   post.date AS `date`,
	   post.id AS `post`
FROM post 
LEFT JOIN blog 
	ON blog.id=post.blog
WHERE blog.type='project' AND post.publish=1;