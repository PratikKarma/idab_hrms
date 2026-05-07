start_time=$(date +%s)

while true
do
    /usr/local/bin/php /home/kcellinf/hrms.idabtech.com/artisan queue:work database --once --sleep=3 --tries=3 >> /home/kcellinf/hrms.idabtech.com/storage/logs/queue.log 2>&1

    current_time=$(date +%s)
    elapsed=$((current_time - start_time))

    if [ $elapsed -ge 880 ]; then
        break
    fi
done