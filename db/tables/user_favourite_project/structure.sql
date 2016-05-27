CREATE TABLE IF NOT EXISTS user_favourite_project (
  user varchar(50) NOT NULL,
  project varchar(50) NOT NULL,
  date_send date,
  date_marked date,
  UNIQUE KEY user_favourite_project (user, project)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User favourites projects';