SELECT 'invest not located', COUNT(*) FROM invest WHERE id NOT IN (SELECT id FROM invest_location )
UNION
SELECT 'invest located', COUNT(*) FROM invest_location
UNION
SELECT 'invests total', COUNT(*) FROM invest
UNION
SELECT 'users total', COUNT(*) FROM `user`
UNION
SELECT 'users located', COUNT(*) FROM user_location
UNION
SELECT
'invests forced', COUNT(invest.id)
FROM invest, user_location
WHERE invest.user=user_location.id AND invest.id NOT IN (SELECT id FROM invest_location);


# Update locations for old invests based on users
INSERT INTO invest_location
(id, latitude, longitude, method, locable, city, region, country, country_code, info, modified)
SELECT
invest.id,
user_location.latitude, user_location.longitude, user_location.method, user_location.locable, user_location.city, user_location.region, user_location.country, user_location.country_code, user_location.info, user_location.modified
FROM invest, user_location
WHERE invest.user=user_location.id AND invest.id NOT IN (SELECT id FROM invest_location);
