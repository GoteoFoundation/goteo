--
-- Import form location, locatiom_item structure
--
INSERT
INTO user_location
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
JOIN user ON user.id=a.item
WHERE a.type='user';

-- DON'T DO THIS BEFORE IMPORT THE project_location TABLE!!!
DROP TABLE location;
DROP TABLE location_item;
