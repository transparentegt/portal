DBNAME=maphpia_gtt

mysqladmin drop -f $DBNAME
mysqladmin create $DBNAME
mysql $DBNAME  < db/db.sql
mysql $DBNAME  < db/db.data.sql

# rm -f data/cache/zfcache-*/*-list-page-*
# sudo chown $USER.$USER data/cache/ -Rvf
# inicia el debug del CLI, hay que agregar un if para ver si viene un flag de debug como parámetro del debug -d
# export QUERY_STRING="start_debug=1&debug_host=127.0.0.1&debug_port=10137&debug_stop=0"
php public/index.php scraper
