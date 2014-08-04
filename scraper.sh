DBNAME=maphpia_gtt

# mysqladmin drop -f $DBNAME
# mysqladmin create $DBNAME
# mysql $DBNAME  < db/db.sql
# mysql $DBNAME  < db/db.data.sql

# rm -f data/cache/zfcache-*/*-list-page-*
# sudo chown $USER.$USER data/cache/ -Rvf
# inicia el debug del CLI, hay que agregar un if para ver si viene un flag de debug como parámetro del debug -d
# export QUERY_STRING="start_debug=true"
php public/index.php scraper

# que suene un sonido al terminar para poder saber que ya terminó
play /usr/share/sounds/ubuntu/stereo/dialog-information.ogg 
