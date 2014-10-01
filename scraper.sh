DBNAME=maphpia_gtt
mysqladmin drop -f $DBNAME
mysqladmin create $DBNAME
vendor/bin/doctrine-module orm:validate-schema
vendor/bin/doctrine-module orm:schema-tool:create
mysql $DBNAME  < db/db.data.sql

# inicia el debug del CLI, hay que agregar un if para ver si viene un flag de debug como parámetro del debug -d

while getopts ":dx" opt; do
  case $opt in
    d)
      echo "DEBUG was activated!"
      export QUERY_STRING="start_debug=true"
      ;;
    x)
      echo "Clear cache activated!"
      rm -rf data/cache/zfcache-*
      ;;
    \?)
      echo "Invalid option: -$OPTARG" >&2
      ;;
  esac
done


# export QUERY_STRING="start_debug=true"
php public/index.php scraper

# que suene un sonido al terminar para poder saber que ya terminó
play /usr/share/sounds/ubuntu/stereo/dialog-question.ogg 
