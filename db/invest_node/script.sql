INSERT INTO invest_node (project_id, project_node, user_id, user_node, invest_id)
SELECT project_id, project.node, user_id, user_node, invest_id
  FROM (
    SELECT  invest.id as invest_id,
        invest.user as user_id,
        invest.project as project_id,
        user.node as user_node
    FROM invest
    LEFT JOIN user ON invest.user = user.id
  ) AS T
JOIN project ON project.id = project_id;
