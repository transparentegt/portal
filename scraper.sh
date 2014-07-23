DBNAME=maphpia_gtt

mysqladmin drop -f $DBNAME
mysqladmin create $DBNAME
mysql $DBNAME  < db/db.sql
mysql $DBNAME  < db/db.data.sql

# sudo chown $USER.$USER data/cache/ -Rvf
php public/index.php scraper
