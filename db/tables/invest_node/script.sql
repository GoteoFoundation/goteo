--
-- Si el nodo del usuario y del proyecto es el mismo asumimos el del proyecto, sino asumimos el de usuario.

INSERT INTO invest_node (project_id, project_node, user_id, user_node, invest_id, invest_node)
SELECT project_id, project.node, user_id, user_node, invest_id, IF(STRCMP(user_node, project.node), user_node, project.node)
  FROM (
    SELECT  invest.id as invest_id,
        invest.user as user_id,
        invest.project as project_id,
        user.node as user_node
    FROM invest
    LEFT JOIN user ON invest.user = user.id
  ) AS T
JOIN project ON project.id = project_id;
