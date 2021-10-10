```
# 48367 Records
# 'Needs' GROUPED BY sub-county level
SELECT tb3.admin3_grid_id as grid_id, loc.name, loc.country_code, SUM(tb3.population) as population, SUM(tb3.needed) as needed, (0) as reported, (0) as percent
FROM (
         # 44395 Records
         SELECT
             lg1.admin0_grid_id,
             lg1.admin1_grid_id,
             lg1.admin2_grid_id,
             lg1.admin3_grid_id,
             lg1.population,
             IF(ROUND(lg1.population / IF(lg1.country_code = 'US', 5000, 50000)) < 1, 1,
                ROUND(lg1.population / IF(lg1.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid lg1
         WHERE lg1.level = 0
           AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                   FROM wp_3_dt_location_grid lg11
                                   WHERE lg11.level = 1
                                     AND lg11.admin0_grid_id = lg1.grid_id)
           AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg1.admin0_grid_id NOT IN
               (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                100054605, 100253456, 100342975, 100074571)
         UNION ALL
         SELECT
             lg2.admin0_grid_id,
             lg2.admin1_grid_id,
             lg2.admin2_grid_id,
             lg2.admin3_grid_id,
             lg2.population,
             IF(ROUND(lg2.population / IF(lg2.country_code = 'US', 5000, 50000)) < 1, 1,
                ROUND(lg2.population / IF(lg2.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid lg2
         WHERE lg2.level = 1
           AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                   FROM wp_3_dt_location_grid lg22
                                   WHERE lg22.level = 2
                                     AND lg22.admin1_grid_id = lg2.grid_id)
           AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg2.admin0_grid_id NOT IN
               (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                100054605, 100253456, 100342975, 100074571)
         UNION ALL
         SELECT
             lg3.admin0_grid_id,
             lg3.admin1_grid_id,
             lg3.admin2_grid_id,
             lg3.admin3_grid_id,
             lg3.population,
             IF(ROUND(lg3.population / IF(lg3.country_code = 'US', 5000, 50000)) < 1, 1,
                ROUND(lg3.population / IF(lg3.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid lg3
         WHERE lg3.level = 2
           AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg3.admin0_grid_id NOT IN
               (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                100054605, 100253456, 100342975, 100074571)
         UNION ALL
         SELECT
             lg4.admin0_grid_id,
             lg4.admin1_grid_id,
             lg4.admin2_grid_id,
             lg4.admin3_grid_id,
             lg4.population,
             IF(ROUND(lg4.population / IF(lg4.country_code = 'US', 5000, 50000)) < 1, 1,
                ROUND(lg4.population / IF(lg4.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid lg4
         WHERE lg4.level = 1
           AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg4.admin0_grid_id IN
               (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                100054605, 100253456, 100342975, 100074571)
         UNION ALL
         SELECT
             lg5.admin0_grid_id,
             lg5.admin1_grid_id,
             lg5.admin2_grid_id,
             lg5.admin3_grid_id,
             lg5.population,
             IF(ROUND(lg5.population / IF(lg5.country_code = 'US', 5000, 50000)) < 1, 1,
                ROUND(lg5.population / IF(lg5.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid as lg5
         WHERE lg5.level = 3
           AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg5.admin0_grid_id NOT IN
               (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                100054605, 100253456, 100342975, 100074571)
) as tb3
LEFT JOIN wp_3_dt_location_grid loc ON tb3.admin3_grid_id=loc.grid_id
WHERE tb3.admin3_grid_id IS NOT NULL
GROUP BY tb3.admin3_grid_id

UNION ALL

# 'Needs' GROUPED BY county level
SELECT tb2.admin2_grid_id as grid_id, loc.name, loc.country_code, SUM(tb2.population) as population, SUM(tb2.needed) as needed, (0) as reported, (0) as percent
FROM (
         SELECT
             lg1.admin0_grid_id,
             lg1.admin1_grid_id,
             lg1.admin2_grid_id,
             lg1.admin3_grid_id,
             lg1.population,
             IF(ROUND(lg1.population / IF(lg1.country_code = 'US', 5000, 50000)) < 1, 1,
                ROUND(lg1.population / IF(lg1.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid lg1
         WHERE lg1.level = 0
           AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
             FROM wp_3_dt_location_grid lg11
             WHERE lg11.level = 1
           AND lg11.admin0_grid_id = lg1.grid_id)
           AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg1.admin0_grid_id NOT IN
             (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
             100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
             100054605, 100253456, 100342975, 100074571)
         UNION ALL
         SELECT
             lg2.admin0_grid_id,
             lg2.admin1_grid_id,
             lg2.admin2_grid_id,
             lg2.admin3_grid_id,
             lg2.population,
             IF(ROUND(lg2.population / IF(lg2.country_code = 'US', 5000, 50000)) < 1, 1,
             ROUND(lg2.population / IF(lg2.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid lg2
         WHERE lg2.level = 1
           AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
             FROM wp_3_dt_location_grid lg22
             WHERE lg22.level = 2
           AND lg22.admin1_grid_id = lg2.grid_id)
           AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg2.admin0_grid_id NOT IN
             (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
             100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
             100054605, 100253456, 100342975, 100074571)
         UNION ALL
         SELECT
             lg3.admin0_grid_id,
             lg3.admin1_grid_id,
             lg3.admin2_grid_id,
             lg3.admin3_grid_id,
             lg3.population,
             IF(ROUND(lg3.population / IF(lg3.country_code = 'US', 5000, 50000)) < 1, 1,
             ROUND(lg3.population / IF(lg3.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid lg3
         WHERE lg3.level = 2
           AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg3.admin0_grid_id NOT IN
             (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
             100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
             100054605, 100253456, 100342975, 100074571)
         UNION ALL
         SELECT
             lg4.admin0_grid_id,
             lg4.admin1_grid_id,
             lg4.admin2_grid_id,
             lg4.admin3_grid_id,
             lg4.population,
             IF(ROUND(lg4.population / IF(lg4.country_code = 'US', 5000, 50000)) < 1, 1,
             ROUND(lg4.population / IF(lg4.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid lg4
         WHERE lg4.level = 1
           AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg4.admin0_grid_id IN
             (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
             100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
             100054605, 100253456, 100342975, 100074571)
         UNION ALL
         SELECT
             lg5.admin0_grid_id,
             lg5.admin1_grid_id,
             lg5.admin2_grid_id,
             lg5.admin3_grid_id,
             lg5.population,
             IF(ROUND(lg5.population / IF(lg5.country_code = 'US', 5000, 50000)) < 1, 1,
             ROUND(lg5.population / IF(lg5.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid as lg5
         WHERE lg5.level = 3
           AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg5.admin0_grid_id NOT IN
             (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
             100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
             100054605, 100253456, 100342975, 100074571)
) as tb2
LEFT JOIN wp_3_dt_location_grid loc ON tb2.admin2_grid_id=loc.grid_id
GROUP BY tb2.admin2_grid_id

UNION ALL

# 'Needs' GROUPED BY state level
SELECT tb1.admin1_grid_id as grid_id, loc.name, loc.country_code, SUM(tb1.population) as population, SUM(tb1.needed) as needed, (0) as reported, (0) as percent
FROM (
         SELECT
             lg1.admin0_grid_id,
             lg1.admin1_grid_id,
             lg1.admin2_grid_id,
             lg1.admin3_grid_id,
             lg1.population,
             IF(ROUND(lg1.population / IF(lg1.country_code = 'US', 5000, 50000)) < 1, 1,
                ROUND(lg1.population / IF(lg1.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid lg1
         WHERE lg1.level = 0
           AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                   FROM wp_3_dt_location_grid lg11
                                   WHERE lg11.level = 1
                                     AND lg11.admin0_grid_id = lg1.grid_id)
           AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg1.admin0_grid_id NOT IN
               (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                100054605, 100253456, 100342975, 100074571)
         UNION ALL
         SELECT
             lg2.admin0_grid_id,
             lg2.admin1_grid_id,
             lg2.admin2_grid_id,
             lg2.admin3_grid_id,
             lg2.population,
             IF(ROUND(lg2.population / IF(lg2.country_code = 'US', 5000, 50000)) < 1, 1,
                ROUND(lg2.population / IF(lg2.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid lg2
         WHERE lg2.level = 1
           AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                   FROM wp_3_dt_location_grid lg22
                                   WHERE lg22.level = 2
                                     AND lg22.admin1_grid_id = lg2.grid_id)
           AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg2.admin0_grid_id NOT IN
               (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                100054605, 100253456, 100342975, 100074571)
         UNION ALL
         SELECT
             lg3.admin0_grid_id,
             lg3.admin1_grid_id,
             lg3.admin2_grid_id,
             lg3.admin3_grid_id,
             lg3.population,
             IF(ROUND(lg3.population / IF(lg3.country_code = 'US', 5000, 50000)) < 1, 1,
                ROUND(lg3.population / IF(lg3.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid lg3
         WHERE lg3.level = 2
           AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg3.admin0_grid_id NOT IN
               (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                100054605, 100253456, 100342975, 100074571)
         UNION ALL
         SELECT
             lg4.admin0_grid_id,
             lg4.admin1_grid_id,
             lg4.admin2_grid_id,
             lg4.admin3_grid_id,
             lg4.population,
             IF(ROUND(lg4.population / IF(lg4.country_code = 'US', 5000, 50000)) < 1, 1,
                ROUND(lg4.population / IF(lg4.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid lg4
         WHERE lg4.level = 1
           AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg4.admin0_grid_id IN
               (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                100054605, 100253456, 100342975, 100074571)
         UNION ALL
         SELECT
             lg5.admin0_grid_id,
             lg5.admin1_grid_id,
             lg5.admin2_grid_id,
             lg5.admin3_grid_id,
             lg5.population,
             IF(ROUND(lg5.population / IF(lg5.country_code = 'US', 5000, 50000)) < 1, 1,
                ROUND(lg5.population / IF(lg5.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid as lg5
         WHERE lg5.level = 3
           AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg5.admin0_grid_id NOT IN
               (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                100054605, 100253456, 100342975, 100074571)
) as tb1
LEFT JOIN wp_3_dt_location_grid loc ON tb1.admin1_grid_id=loc.grid_id
GROUP BY tb1.admin1_grid_id

UNION ALL

# 'Needs' GROUPED BY country
SELECT tb0.admin0_grid_id as grid_id, loc.name,loc.country_code, SUM(tb0.population) as population, SUM(tb0.needed) as needed, (0) as reported, (0) as percent
FROM (
         # 44395 Records
         SELECT
             lg1.admin0_grid_id,
             lg1.admin1_grid_id,
             lg1.admin2_grid_id,
             lg1.admin3_grid_id,
             lg1.population,
             IF(ROUND(lg1.population / IF(lg1.country_code = 'US', 5000, 50000)) < 1, 1,
                ROUND(lg1.population / IF(lg1.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid lg1
         WHERE lg1.level = 0
           AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                   FROM wp_3_dt_location_grid lg11
                                   WHERE lg11.level = 1
                                     AND lg11.admin0_grid_id = lg1.grid_id)
           AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg1.admin0_grid_id NOT IN
               (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                100054605, 100253456, 100342975, 100074571)
         UNION ALL
         SELECT
             lg2.admin0_grid_id,
             lg2.admin1_grid_id,
             lg2.admin2_grid_id,
             lg2.admin3_grid_id,
             lg2.population,
             IF(ROUND(lg2.population / IF(lg2.country_code = 'US', 5000, 50000)) < 1, 1,
                ROUND(lg2.population / IF(lg2.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid lg2
         WHERE lg2.level = 1
           AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                   FROM wp_3_dt_location_grid lg22
                                   WHERE lg22.level = 2
                                     AND lg22.admin1_grid_id = lg2.grid_id)
           AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg2.admin0_grid_id NOT IN
               (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                100054605, 100253456, 100342975, 100074571)
         UNION ALL
         SELECT
             lg3.admin0_grid_id,
             lg3.admin1_grid_id,
             lg3.admin2_grid_id,
             lg3.admin3_grid_id,
             lg3.population,
             IF(ROUND(lg3.population / IF(lg3.country_code = 'US', 5000, 50000)) < 1, 1,
                ROUND(lg3.population / IF(lg3.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid lg3
         WHERE lg3.level = 2
           AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg3.admin0_grid_id NOT IN
               (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                100054605, 100253456, 100342975, 100074571)
         UNION ALL
         SELECT
             lg4.admin0_grid_id,
             lg4.admin1_grid_id,
             lg4.admin2_grid_id,
             lg4.admin3_grid_id,
             lg4.population,
             IF(ROUND(lg4.population / IF(lg4.country_code = 'US', 5000, 50000)) < 1, 1,
                ROUND(lg4.population / IF(lg4.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid lg4
         WHERE lg4.level = 1
           AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg4.admin0_grid_id IN
               (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                100054605, 100253456, 100342975, 100074571)
         UNION ALL
         SELECT
             lg5.admin0_grid_id,
             lg5.admin1_grid_id,
             lg5.admin2_grid_id,
             lg5.admin3_grid_id,
             lg5.population,
             IF(ROUND(lg5.population / IF(lg5.country_code = 'US', 5000, 50000)) < 1, 1,
                ROUND(lg5.population / IF(lg5.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid as lg5
         WHERE lg5.level = 3
           AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg5.admin0_grid_id NOT IN
               (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                100054605, 100253456, 100342975, 100074571)
) as tb0
LEFT JOIN wp_3_dt_location_grid loc ON tb0.admin0_grid_id=loc.grid_id
GROUP BY tb0.admin0_grid_id

UNION ALL

# World
SELECT 1 as grid_id, 'world','' as country_code, SUM(tbw.population) as population, SUM(tbw.needed) as needed, (0) as reported, (0) as percent
FROM (
         # 44395 Records
         SELECT
                'world',
             lg1.admin0_grid_id,
             lg1.admin1_grid_id,
             lg1.admin2_grid_id,
             lg1.admin3_grid_id,
             lg1.population,
             IF(ROUND(lg1.population / IF(lg1.country_code = 'US', 5000, 50000)) < 1, 1,
                ROUND(lg1.population / IF(lg1.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid lg1
         WHERE lg1.level = 0
           AND lg1.grid_id NOT IN (SELECT lg11.admin0_grid_id
                                   FROM wp_3_dt_location_grid lg11
                                   WHERE lg11.level = 1
                                     AND lg11.admin0_grid_id = lg1.grid_id)
           AND lg1.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg1.admin0_grid_id NOT IN
               (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                100054605, 100253456, 100342975, 100074571)
         UNION ALL
         SELECT
             'world',
             lg2.admin0_grid_id,
             lg2.admin1_grid_id,
             lg2.admin2_grid_id,
             lg2.admin3_grid_id,
             lg2.population,
             IF(ROUND(lg2.population / IF(lg2.country_code = 'US', 5000, 50000)) < 1, 1,
                ROUND(lg2.population / IF(lg2.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid lg2
         WHERE lg2.level = 1
           AND lg2.grid_id NOT IN (SELECT lg22.admin1_grid_id
                                   FROM wp_3_dt_location_grid lg22
                                   WHERE lg22.level = 2
                                     AND lg22.admin1_grid_id = lg2.grid_id)
           AND lg2.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg2.admin0_grid_id NOT IN
               (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                100054605, 100253456, 100342975, 100074571)
         UNION ALL
         SELECT
             'world',
             lg3.admin0_grid_id,
             lg3.admin1_grid_id,
             lg3.admin2_grid_id,
             lg3.admin3_grid_id,
             lg3.population,
             IF(ROUND(lg3.population / IF(lg3.country_code = 'US', 5000, 50000)) < 1, 1,
                ROUND(lg3.population / IF(lg3.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid lg3
         WHERE lg3.level = 2
           AND lg3.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg3.admin0_grid_id NOT IN
               (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                100054605, 100253456, 100342975, 100074571)
         UNION ALL
         SELECT
             'world',
             lg4.admin0_grid_id,
             lg4.admin1_grid_id,
             lg4.admin2_grid_id,
             lg4.admin3_grid_id,
             lg4.population,
             IF(ROUND(lg4.population / IF(lg4.country_code = 'US', 5000, 50000)) < 1, 1,
                ROUND(lg4.population / IF(lg4.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid lg4
         WHERE lg4.level = 1
           AND lg4.admin0_grid_id NOT IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg4.admin0_grid_id IN
               (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                100054605, 100253456, 100342975, 100074571)
         UNION ALL
         SELECT
             'world',
             lg5.admin0_grid_id,
             lg5.admin1_grid_id,
             lg5.admin2_grid_id,
             lg5.admin3_grid_id,
             lg5.population,
             IF(ROUND(lg5.population / IF(lg5.country_code = 'US', 5000, 50000)) < 1, 1,
                ROUND(lg5.population / IF(lg5.country_code = 'US', 5000, 50000))) as needed
         FROM wp_3_dt_location_grid as lg5
         WHERE lg5.level = 3
           AND lg5.admin0_grid_id IN (100050711, 100219347, 100089589, 100074576, 100259978, 100018514)
           AND lg5.admin0_grid_id NOT IN
               (100314737, 100083318, 100041128, 100133112, 100341242, 100132648, 100222839, 100379914, 100055707,
                100379993, 100130389, 100255271, 100363975, 100248845, 100001527, 100342458, 100024289, 100132795,
                100054605, 100253456, 100342975, 100074571)
) as tbw
LEFT JOIN wp_3_dt_location_grid loc ON 1=loc.grid_id
GROUP BY 'world';
```
