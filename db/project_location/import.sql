--
-- Import form location, locatiom_item structure
--
INSERT IGNORE
INTO project_location
SELECT
a.item AS id,
b.latitude,
b.longitude,
a.method,
a.locable,
b.city,
b.region,
b.country,
b.country_code,
a.info,
a.modified
FROM location_item AS a
JOIN location AS b ON b.id=a.location
JOIN project ON project.id=a.item
WHERE a.type='project';

-- DON'T DO THIS BEFORE IMPORT THE user_location TABLE!!!
DROP TABLE location;
DROP TABLE location_item;
