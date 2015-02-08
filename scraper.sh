DBNAME=maphpia_gtt
while getopts ":adpx" opt; do
  case $opt in
    a) # limpiar la base de datos y empezar de cero 
      mysqladmin drop -f $DBNAME
      mysqladmin create $DBNAME
      vendor/bin/doctrine-module orm:validate-schema
      vendor/bin/doctrine-module orm:schema-tool:update --force
      mysql $DBNAME  < db/db.data.sql
      ;;
    d) # debug paso a paso en el IDE
      echo "DEBUG was activated!"
      export QUERY_STRING="start_debug=true"
      ;;
    p) # profiler para detectar cuellos de botella, no lo he logrado hacer funcionar
      echo "PROFILE was activated!"
      export QUERY_STRING="debug_host=127.0.0.1&debug_fastfile=1&start_debug=1&debug_port=10137&start_profile=1&use_remote=1&ZRayDisable=1&send_sess_end=1&debug_start_session=1"
      ;;
    x) #limpiar cache, páginas descargadas
      echo "Clear cache activated!"
      rm -rf data/cache/zfcache-*
      ;;
    \?)
      echo "Invalid option: -$OPTARG" >&2
      ;;
  esac
done

php public/index.php scraper

# que suene un sonido al terminar para poder saber que ya terminó
play /usr/share/sounds/ubuntu/stereo/dialog-question.ogg 
